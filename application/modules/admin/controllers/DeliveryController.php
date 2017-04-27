<?php

class Admin_DeliveryController extends Zend_Controller_Action {

    private $_deliveryMapper;
    private $_paymentMapper;
    private $_deliveryPaymentMapper;
    private $_flashMessenger;

    public function init() {
        $this->_flashMessenger = $this->_helper->FlashMessenger;

        $flashMessenger = $this->_flashMessenger->getMessages();
        if (!empty($flashMessenger)) {
            $currentMessage = current($flashMessenger);
            if (!empty($currentMessage['info'])) {
                $this->view->infoFlashMessage = $currentMessage['info'];
            } else if (!empty($currentMessage['error'])) {
                $this->view->errorFlashMessage = $currentMessage['error'];
            }
        }

        $this->_deliveryMapper = new Admin_Model_DeliveryMapper();
        $this->_paymentMapper = new Admin_Model_PaymentMapper();
        $this->_deliveryPaymentMapper = new Admin_Model_DeliveryPaymentMapper();
    }

    public function indexAction() {
        $deliveries = $this->_deliveryMapper->getDeliveries();
        $this->view->deliveries = $deliveries;
    }

    public function addAction() {

        $payments = $this->_paymentMapper->getPayments();
        $paymentsToForm = array();
        foreach ($payments as $payment) {
            $paymentsToForm[$payment->getPayment_id()] = $payment->getPayment_name();
        }

        $form = new Admin_Form_EditDeliveryForm();
        $form->setPayments($paymentsToForm);
        $form->startForm();
        $this->view->form = $form;

        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
            $delivery = new Admin_Model_Delivery();
            $delivery->setOptions($form->getValues());


            $db = $this->_deliveryMapper->getDbTable()->getDefaultAdapter();
            $db->beginTransaction();

            try {

                $this->_deliveryMapper->save($delivery);

                //ziskam posledni vlozene id delivery
                $delivery_id = $this->_deliveryMapper->getDbTable()->getDefaultAdapter()->lastInsertId();

                $deliveryPayments = array();
                $deliveryPayments = $this->getRequest()->getPost('delivery_payments');

                if (!empty($deliveryPayments)) {
                    $this->_deliveryPaymentMapper->deleteByDeliveryId($delivery_id);
                    foreach ($deliveryPayments as $deliveryPayment) {
                        $deliveryPaymentObj = new Admin_Model_DeliveryPayment();
                        $deliveryPaymentObj->setDelivery_payment_payment_id($deliveryPayment);
                        $deliveryPaymentObj->setDelivery_payment_delivery_id($delivery_id);
                        $this->_deliveryPaymentMapper->save($deliveryPaymentObj);
                    }
                }

                $this->_flashMessenger->addMessage(array('info' => 'Doprava byla úspěšně vložen.'));
                $db->commit();
            } catch (Exception $e) {
                $this->_flashMessenger->addMessage(array('error' => 'Při ukládání dopravy nastala chyba!<br />' . $e->getMessage()));
                $db->rollback();
            }

            $module = $this->getRequest()->getModuleName();
            $controller = $this->getRequest()->getControllerName();
            $this->_redirect($module . '/' . $controller);
        } else {
            $form->populate($form->getValues());
        }
    }

    public function editAction() {

        $delivery_id = (int) $this->getRequest()->getParam('id');
        $deliveryRow = $this->_deliveryMapper->getDeliveryById($delivery_id);

        if (!empty($deliveryRow)) {

            //platby pro zobrazeni ve formulari
            $payments = $this->_paymentMapper->getPayments();
            $paymentsToForm = array();
            foreach ($payments as $payment) {
                $paymentsToForm[$payment->getPayment_id()] = $payment->getPayment_name();
            }

            $form = new Admin_Form_EditDeliveryForm();
            $form->setPayments($paymentsToForm);
            $form->startForm();
            $this->view->form = $form;

            $form->populate($deliveryRow->toArray());

            //platby pro populate formulare
            $paymentsToPopulate = array();
            $deliveryPayments = $this->_deliveryPaymentMapper->getDeliveryPaymentByDeliveryId($delivery_id);

            if (!empty($deliveryPayments)) {
                foreach ($deliveryPayments as $dp) {
                    $paymentsToPopulate[] = $dp->getDelivery_payment_payment_id();
                }
            }

            if (!empty($paymentsToPopulate)) {
                $values = array(
                    'delivery_payments' => $paymentsToPopulate
                );
                $form->populate($values);
            }


            if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
                $delivery = new Admin_Model_Delivery();
                $delivery->setOptions($form->getValues());
                $delivery->setDelivery_id($delivery_id);

                $db = $this->_deliveryMapper->getDbTable()->getDefaultAdapter();
                $db->beginTransaction();

                try {
                    $this->_deliveryMapper->save($delivery);

                    //ziskam posledni vlozene id delivery
                    $deliveryPayments = array();
                    $deliveryPayments = $this->getRequest()->getPost('delivery_payments');

                    if (!empty($deliveryPayments)) {
                        $this->_deliveryPaymentMapper->deleteByDeliveryId($delivery_id);
                        foreach ($deliveryPayments as $deliveryPayment) {
                            $deliveryPaymentObj = new Admin_Model_DeliveryPayment();
                            $deliveryPaymentObj->setDelivery_payment_payment_id($deliveryPayment);
                            $deliveryPaymentObj->setDelivery_payment_delivery_id($delivery_id);
                            $this->_deliveryPaymentMapper->save($deliveryPaymentObj);
                        }
                    }

                    $this->_flashMessenger->addMessage(array('info' => 'Doprava byla úspěšně vložena.'));
                    $db->commit();
                } catch (Exception $e) {
                    $this->_flashMessenger->addMessage(array('error' => 'Při ukládání dopravy nastala chyba!<br />' . $e->getMessage()));
                    $db->rollback();
                }

                $module = $this->getRequest()->getModuleName();
                $controller = $this->getRequest()->getControllerName();
                $this->_redirect($module . '/' . $controller);
            } else {
                $form->populate($form->getValues());
            }
        } else {
            $module = $this->getRequest()->getModuleName();
            $controller = $this->getRequest()->getControllerName();
            $this->_redirect($module . '/' . $controller);
        }
    }

    public function deleteAction() {

        $id = (int) $this->getRequest()->getParam('id');

        try {
            $this->_deliveryMapper->delete($id);
            $this->_flashMessenger->addMessage(array('info' => 'Doprava byla úspěšně smazána.'));
        } catch (Exception $e) {
            $this->_flashMessenger->addMessage(array('error' => 'Při pokusu o smazání došlo k chybě!<br />' . $e->getMessage()));
        }
        $module = $this->getRequest()->getModuleName();
        $controller = $this->getRequest()->getControllerName();
        $this->_redirect($module . '/' . $controller);
    }

}

