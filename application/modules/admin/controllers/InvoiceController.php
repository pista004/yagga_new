<?php

class Admin_InvoiceController extends Zend_Controller_Action {

    private $_flashMessenger;
    private $_orderMapper;
    private $_orderItemMapper;
    private $_orderStateMapper;
    private $_productMapper;
    private $_variantMapper;
    private $_deliveryMapper;
    private $_paymentMapper;
    private $_invoiceMapper;

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

        $this->_orderMapper = new Admin_Model_OrderMapper();
        $this->_orderItemMapper = new Admin_Model_OrderItemMapper();
        $this->_orderStateMapper = new Admin_Model_OrderStateMapper();
        $this->_productMapper = new Admin_Model_ProductMapper();
        $this->_variantMapper = new Admin_Model_VariantMapper();
        $this->_deliveryMapper = new Admin_Model_DeliveryMapper();
        $this->_paymentMapper = new Admin_Model_PaymentMapper();
        $this->_invoiceMapper = new Admin_Model_InvoiceMapper();
    }

    /*
     * Order
     */

    public function indexAction() {

        $orders = $this->_orderMapper->getOrders();
        $this->view->orders = $orders;
    }

    public function editAction() {

        $invoice_id = (int) $this->getRequest()->getParam('id');
        if ($invoice_id) {

            $invoiceMap = $this->_invoiceMapper->getInvoiceById($invoice_id);

            $order_id = $invoiceMap->getInvoice_order_id();
            
//            print_r($invoiceMap->toArray());die;
            $form = new Admin_Form_EditInvoiceForm();
            $form->startForm();


            $form->populate($invoiceMap->toArray());

            if ($invoiceMap->getInvoice_creating_date()) {
                $form->getElement('invoice_creating_date')->setValue(date('d. m. Y', $invoiceMap->getInvoice_creating_date()));
            }

            if ($invoiceMap->getInvoice_due_date()) {
                $form->getElement('invoice_due_date')->setValue(date('d. m. Y', $invoiceMap->getInvoice_due_date()));
            }

            $this->view->form = $form;

            if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {

                $invoice = new Admin_Model_Invoice();
                $invoice->setOptions($form->getValues());
                $invoice->setInvoice_id($invoice_id);

                if ($this->getRequest()->getParam('invoice_creating_date')) {
                    //prevod data ve formatu 22. 10. 2014 na timestamp
                    list($day, $month, $year) = explode('. ', $form->getValue('invoice_creating_date'));
                    $invoice->setInvoice_creating_date(mktime(0, 0, 0, $month, $day, $year));
                }


                if ($this->getRequest()->getParam('invoice_due_date')) {
                    //prevod data ve formatu 22. 10. 2014 na timestamp
                    list($day, $month, $year) = explode('. ', $form->getValue('invoice_due_date'));
                    $invoice->setInvoice_due_date(mktime(0, 0, 0, $month, $day, $year));
                }


                $db = $this->_invoiceMapper->getDbTable()->getDefaultAdapter();
                $db->beginTransaction();

                try {
//print_r($invoice);die;
                    $this->_invoiceMapper->save($invoice);

                    
                    $order = $this->_orderMapper->getOrderById($order_id);
                    $orderItems = $this->_orderItemMapper->getOrderItemsByOrderId($order_id);

                    $invoiceId = $invoice_id;
                    if ($invoiceId) {
                        $invoiceCreatingDate = $invoice->getInvoice_creating_date();
                        $invoiceDueDate = $invoice->getInvoice_due_date();

                        //generuju fakturu
                        $invoicePdfGenerator = new My_InvoicePdfGenerator();
                        $invoicePdfGenerator->generateInvoice($invoiceMap->getInvoice_number(), $invoiceCreatingDate, $invoiceDueDate, $order, $orderItems);
                    }

                    $db->commit();
                    $this->_flashMessenger->addMessage(array('info' => 'Faktura byla úspěšně upravena.'));
                } catch (Exception $e) {
                    $this->_flashMessenger->addMessage(array('error' => 'Při ukládání faktury nastala chyba!<br />' . $e->getMessage()));
                    $db->rollBack();
                }

                $module = $this->getRequest()->getModuleName();
                $controller = $this->getRequest()->getControllerName();
                $this->_redirect($module . '/' . $controller);
            } else {
                $form->populate($form->getValues());
            }
        }
    }

    public function deleteAction() {
        $invoice_id = $this->getRequest()->getParam('id');

        $db = $this->_invoiceMapper->getDbTable()->getDefaultAdapter();
        $db->beginTransaction();
        try {

            $invoice = $this->_invoiceMapper->getInvoiceById($invoice_id);
            if ($invoice->getInvoice_id()) {
                $invoicePath = INVOICE_UPLOAD_PATH . '/' . $invoice->getInvoice_path();
                unlink($invoicePath);
            }

            $this->_invoiceMapper->delete($invoice_id);

            $this->_flashMessenger->addMessage(array('info' => 'Faktura byla úspěšně smazána.'));
            $db->commit();
        } catch (Exception $e) {
            $this->_flashMessenger->addMessage(array('error' => 'Při mazání faktury nastala chyba!<br />' . $e->getMessage()));
            $db->rollback();
        }

        $module = $this->getRequest()->getModuleName();
        $controller = $this->getRequest()->getControllerName();
        $this->_redirect($module . '/' . $controller);
    }

}

