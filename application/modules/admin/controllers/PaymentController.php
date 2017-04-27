<?php

class Admin_PaymentController extends Zend_Controller_Action {

    private $_paymentMapper;
    private $_deliveryMapper;
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

        $this->_paymentMapper = new Admin_Model_PaymentMapper();
        $this->_deliveryMapper = new Admin_Model_DeliveryMapper();
        $this->_deliveryPaymentMapper = new Admin_Model_DeliveryPaymentMapper();
    }

    public function indexAction() {
        $payments = $this->_paymentMapper->getPayments();
        $this->view->payments = $payments;
    }

    public function addAction() {

        $deliveries = $this->_deliveryMapper->getDeliveries();
        $deliveriesToForm = array();
        foreach ($deliveries as $delivery) {
            $deliveriesToForm[$delivery->getDelivery_id()] = $delivery->getDelivery_name();
        }
        
        $form = new Admin_Form_EditPaymentForm();
        $form->setDeliveries($deliveriesToForm);
        $form->startForm();
        $this->view->form = $form;

        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
            $payment = new Admin_Model_Payment();
            $payment->setOptions($form->getValues());

            $db = $this->_paymentMapper->getDbTable()->getDefaultAdapter();
            $db->beginTransaction();

            try {
                $this->_paymentMapper->save($payment);
                
                //ziskam posledni vlozene id payment
                $payment_id = $this->_paymentMapper->getDbTable()->getDefaultAdapter()->lastInsertId();

                $deliveryPayments = array();
                $deliveryPayments = $this->getRequest()->getPost('payment_deliveries');

                if (!empty($deliveryPayments)) {
                    $this->_deliveryPaymentMapper->deleteByPaymentId($payment_id);
                    foreach ($deliveryPayments as $deliveryPayment) {
                        $deliveryPaymentObj = new Admin_Model_DeliveryPayment();
                        $deliveryPaymentObj->setDelivery_payment_payment_id($payment_id);
                        $deliveryPaymentObj->setDelivery_payment_delivery_id($deliveryPayment);
                        $this->_deliveryPaymentMapper->save($deliveryPaymentObj);
                    }
                }
                

                $this->_flashMessenger->addMessage(array('info' => 'Platba byla úspěšně vložena.'));
                $db->commit();
            } catch (Exception $e) {
                $this->_flashMessenger->addMessage(array('error' => 'Při ukládání platby nastala chyba!<br />' . $e->getMessage()));
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

        $payment_id = (int) $this->getRequest()->getParam('id');
        $paymentRow = $this->_paymentMapper->getPaymentById($payment_id);

//        print_r($paymentRow);die;
        
        if (!empty($paymentRow)) {

            //doprava pro zobrazeni ve formulari
            $deliveries = $this->_deliveryMapper->getDeliveries();
            $deliveriesToForm = array();
            foreach ($deliveries as $delivery) {
                $deliveriesToForm[$delivery->getDelivery_id()] = $delivery->getDelivery_name();
            }
            
            $form = new Admin_Form_EditPaymentForm();
            $form->setDeliveries($deliveriesToForm);
            $form->startForm();
            $this->view->form = $form;

            $form->populate($paymentRow->toArray());
            
            
             //doprava pro populate formulare
            $deliveriesToPopulate = array();
            $deliveryPayments = $this->_deliveryPaymentMapper->getDeliveryPaymentByPaymentId($payment_id);

            if (!empty($deliveryPayments)) {
                foreach ($deliveryPayments as $dp) {
                    $deliveriesToPopulate[] = $dp->getDelivery_payment_delivery_id();
                }
            }

            if (!empty($deliveriesToPopulate)) {
                $values = array(
                    'payment_deliveries' => $deliveriesToPopulate
                );
                $form->populate($values);
            }
            
            
            if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
                $payment = new Admin_Model_Payment();
                $payment->setOptions($form->getValues());
                $payment->setPayment_id($payment_id);
                
                $db = $this->_paymentMapper->getDbTable()->getDefaultAdapter();
                $db->beginTransaction();

                try {
                    $this->_paymentMapper->save($payment);

                    //ziskam posledni vlozene id delivery
                    $deliveryPayments = array();
                    $deliveryPayments = $this->getRequest()->getPost('payment_deliveries');

                    if (!empty($deliveryPayments)) {
                        $this->_deliveryPaymentMapper->deleteByPaymentId($payment_id);
                        foreach ($deliveryPayments as $deliveryPayment) {
                            $deliveryPaymentObj = new Admin_Model_DeliveryPayment();
                            $deliveryPaymentObj->setDelivery_payment_payment_id($payment_id);
                            $deliveryPaymentObj->setDelivery_payment_delivery_id($deliveryPayment);
                            $this->_deliveryPaymentMapper->save($deliveryPaymentObj);
                        }
                    }
                    
                    
                    $this->_flashMessenger->addMessage(array('info' => 'Platba byla úspěšně vložena.'));
                    $db->commit();
                } catch (Exception $e) {
                    $this->_flashMessenger->addMessage(array('error' => 'Při ukládání platby nastala chyba!<br />' . $e->getMessage()));
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
            $this->_paymentMapper->delete($id);
            $this->_flashMessenger->addMessage(array('info' => 'Platba byla úspěšně smazána.'));
        } catch (Exception $e) {
            $this->_flashMessenger->addMessage(array('error' => 'Při pokusu o smazání došlo k chybě!<br />' . $e->getMessage()));
        }
        $module = $this->getRequest()->getModuleName();
        $controller = $this->getRequest()->getControllerName();
        $this->_redirect($module . '/' . $controller);
    }

}

