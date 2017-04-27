<?php

class Admin_OrderController extends Zend_Controller_Action {

    private $_flashMessenger;
    private $_orderMapper;
    private $_orderItemMapper;
    private $_orderStateMapper;
    private $_productMapper;
    private $_variantMapper;
    private $_deliveryMapper;
    private $_paymentMapper;
    private $_invoiceMapper;
    private $_orderOrderStateMapper;

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
        $this->_orderOrderStateMapper = new Admin_Model_OrderOrderStateMapper();
    }

    /*
     * Order
     */

    public function indexAction() {
        $orderResult = $this->_orderMapper->getOrders(0, -1, "order_date DESC");
        $this->view->orderResult = $orderResult;
    }

    public function editAction() {

        $order_id = (int) $this->getRequest()->getParam('id');

        if ($order_id) {

            $orderItems = $this->_orderItemMapper->getOrderItemsByOrderId($order_id);
            $this->view->orderItems = $orderItems;

            $order = $this->_orderMapper->getOrderById($order_id);
            $this->view->order = $order;
//print_r($order);die;
            $orderStates = $this->_orderStateMapper->getOrderStates();
//            print_r($orderStates);die;
            $orderStateToForm = array();
            foreach ($orderStates as $orderState) {
                $orderStateToForm[$orderState->getOrder_state_id()] = $orderState->getOrder_state_name();
            }


            $orderOrderStates = $this->_orderOrderStateMapper->getOrderOrderStateByOrderId($order_id);
            $this->view->orderOrderStates = $orderOrderStates;

            $form = new Admin_Form_EditOrderForm();
            $form->setOrder_state($orderStateToForm);
            $form->startForm();

            $form->populate($order->toArray());

//            print_r($orderStates[$order->getOrder_order_state_id()]);die;
            
            $this->view->form = $form;

            if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {

                $orderBeforeUpdate = clone $order;

                $order = new Admin_Model_Order();
                $order->setOptions($form->getValues());
                $order->setOrder_id($order_id);

                $db = $this->_orderMapper->getDbTable()->getDefaultAdapter();
                $db->beginTransaction();

                try {

                    //kontrola zmeny stavu objednavky, pokud byl stav zmenen a nebyl jeste odeslan email, tak odelsu email
                    if (($orderBeforeUpdate->getOrder_order_state_id() != $order->getOrder_order_state_id())) {
                        $emailSent = $this->_orderOrderStateMapper->getOrderOrderStateByOrderIdAndOrderStateId($order->getOrder_id(), $order->getOrder_order_state_id());
                        if (empty($emailSent)) {

                            //nastaveni a odeslani emailu
                            $view = new Zend_View();

                            //uprava order state textu - pokud obsahuje parametr(napr cislo objednavky, tak nahradim)
                            if($orderStates[$order->getOrder_order_state_id()]->getOrder_state_text()){
                                $orderStates[$order->getOrder_order_state_id()]->setOrder_state_text(str_replace('%order_number%', $orderBeforeUpdate->getOrder_number(), $orderStates[$order->getOrder_order_state_id()]->getOrder_state_text())); 
                                $orderStates[$order->getOrder_order_state_id()]->setOrder_state_text(str_replace('%order_sum%', $orderBeforeUpdate->getOrder_sum_with_delivery_payment(), $orderStates[$order->getOrder_order_state_id()]->getOrder_state_text())); 
                            }
                            
                            $view->orderState = $orderStates[$order->getOrder_order_state_id()];
                            $view->orderNumber = $orderBeforeUpdate->getOrder_number();
                            

                            //nastavim cestu k sablonam(template) emailu
                            $view->setScriptPath(APPLICATION_PATH . '/modules/default/views/emails/');

                            $html = $view->render('orderstate.phtml');

                            $mailer = new My_Mailer();
                            $subject = "Yagga.cz - " . $orderStateToForm[$order->getOrder_order_state_id()];
                            $mailer->sendEmail($order->getOrder_email(), $subject, $html);

                            $orderOrderState = new Admin_Model_OrderOrderState();
                            $orderOrderState->setOrder_order_state_order_id($order_id);
                            $orderOrderState->setOrder_order_state_order_state_id($order->getOrder_order_state_id());
                            $orderOrderState->setOrder_order_state_date(time());

                            $this->_orderOrderStateMapper->save($orderOrderState);
                        }
                    }


                    $this->_orderMapper->save($order);

                    $invoice = $this->_invoiceMapper->getInvoiceByOrderId($order_id);

                    if (isset($invoice)) {
                        $invoiceId = $invoice->getInvoice_id();
                        if ($invoiceId) {
                            $invoiceCreatingDate = $invoice->getInvoice_creating_date();
                            $invoiceDueDate = $invoice->getInvoice_due_date();

                            $order = $this->_orderMapper->getOrderById($order_id);
//                            print_r($order);die;
                            //generuju fakturu
                            $invoicePdfGenerator = new My_InvoicePdfGenerator();
//                            print_r($order);die;
//                            print_r($orderItems);die;
                            $invoicePdfGenerator->generateInvoice($invoiceId, $invoiceCreatingDate, $invoiceDueDate, $order, $orderItems);
                        }
                    }

                    $this->_flashMessenger->addMessage(array('info' => 'Objednávka byla úspěšně upravena'));

                    $db->commit();
                } catch (Exception $e) {
                    $this->_flashMessenger->addMessage(array('error' => 'Při ukládání objednávky nastala chyba!<br />' . $e->getMessage()));
                    $db->rollback();
                }

                $module = $this->getRequest()->getModuleName();
                $controller = $this->getRequest()->getControllerName();
                $this->_redirect($module . '/' . $controller);
            } else {
                $form->populate($order->toArray());
            }
        }
    }

    public function deleteAction() {
        $id = $this->getRequest()->getParam('id');

        $db = $this->_orderMapper->getDbTable()->getDefaultAdapter();
        $db->beginTransaction();
        try {

            $invoice = $this->_invoiceMapper->getInvoiceByOrderId($id);
            if (isset($invoice)) {
                if ($invoice->getInvoice_id()) {
                    $invoicePath = INVOICE_UPLOAD_PATH . '/' . $invoice->getInvoice_path();
                    unlink($invoicePath);
                }
            }

            $this->_orderMapper->delete($id);

            $this->_flashMessenger->addMessage(array('info' => 'Objednávka byla úspěšně smazána.'));
            $db->commit();
        } catch (Exception $e) {
            $this->_flashMessenger->addMessage(array('error' => 'Při mazání objednávky nastala chyba!<br />' . $e->getMessage()));
            $db->rollback();
        }

        $module = $this->getRequest()->getModuleName();
        $controller = $this->getRequest()->getControllerName();
        $this->_redirect($module . '/' . $controller);
    }

    /*
     * Order Item
     */

    public function edititemAction() {
        $order_item_id = (int) $this->getRequest()->getParam('id');

        if ($order_item_id) {

            $orderItem = $this->_orderItemMapper->getOrderItemsById($order_item_id);
            $this->view->orderItem = $orderItem;

            $order_id = $orderItem->getOrder_item_order_id();

            $variantsToForm = array();

            if ($orderItem->getOrder_item_variant_id()) {
                $variants = $this->_variantMapper->getVariantsByProductId($orderItem->getOrder_item_product_id());
                if (!empty($variants)) {
                    foreach ($variants as $variant) {
                        $variantsToForm[$variant->getVariant_id()] = $variant->getVariant_name();
                    }
                }
            }

            $form = new Admin_Form_EditOrderItemForm();
            $form->setVariant($variantsToForm);
            $form->startForm();

            $form->populate($orderItem->toArray());

            $this->view->form = $form;

            if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
                $orderItem = new Admin_Model_OrderItem();
                $orderItem->setOptions($form->getValues());
                $orderItem->setOrder_item_variant_name($variantsToForm[$orderItem->getOrder_item_variant_id()]);
                $orderItem->setOrder_item_id($order_item_id);

//                print_r($orderItem);die;
                $db = $this->_orderItemMapper->getDbTable()->getDefaultAdapter();
                $db->beginTransaction();
                try {
                    $this->_orderItemMapper->save($orderItem);

                    $order = $this->_orderMapper->getOrderById($order_id);
                    $orderItems = $this->_orderItemMapper->getOrderItemsByOrderId($order_id);

                    $invoiceId = $order->getOrder_invoice()->getInvoice_id();
                    if ($invoiceId) {
                        $invoiceCreatingDate = $order->getOrder_invoice()->getInvoice_creating_date();
                        $invoiceDueDate = $order->getOrder_invoice()->getInvoice_due_date();

                        //generuju fakturu
                        $invoicePdfGenerator = new My_InvoicePdfGenerator();
                        $invoicePdfGenerator->generateInvoice($invoiceId, $invoiceCreatingDate, $invoiceDueDate, $order, $orderItems);
                    }

                    $this->_flashMessenger->addMessage(array('info' => 'Položka objednávky byla úspěšně upravena'));

                    $db->commit();
                } catch (Exception $e) {
                    $this->_flashMessenger->addMessage(array('error' => 'Při ukládání položky objednávky nastala chyba!<br />' . $e->getMessage()));
                    $db->rollback();
                }

                $module = $this->getRequest()->getModuleName();
                $controller = $this->getRequest()->getControllerName();
                $action = $this->getRequest()->getActionName();
//                http://weeker.cz/admin/order/edititem/id/1
                $this->_redirect($module . '/' . $controller . '/edit/id/' . $order_id);

                $this->_redirect($this->getRequestuest()->getServer('HTTP_REFERER'));
            } else {
                $form->populate($orderItem->toArray());
            }
        }
    }

    public function additemAction() {
        $order_id = (int) $this->getRequest()->getParam('id');

        if ($order_id) {
            $products = $this->_productMapper->getProducts();

            $productsToForm = array();
            $productsToForm[0] = '--Vyberte--';

            foreach ($products as $product) {
                $productsToForm[$product->getProduct_id()] = $product->getProduct_name();
            }


            $form = new Admin_Form_AddOrderItemForm();
            $form->setProducts($productsToForm);

            $form->startForm();

            if ($this->getRequest()->isPost()) {

                $variant_id = $this->getRequest()->getPost('order_item_variant_id');

                $variants = array();
                $variantsToForm = array();
                if (isset($variant_id)) {
                    $variants = $this->_variantMapper->getVariantsByProductId($this->getRequest()->getPost('order_item_product_id'));

                    $variantsToForm[0] = '--Vyberte--';
                    foreach ($variants as $variant) {
                        $variantsToForm[$variant->getVariant_id()] = $variant->getVariant_name();
                    }

                    $form->setVariants($variantsToForm);
                    $form->startForm();
                }

                if ($form->isValid($this->getRequest()->getPost())) {

                    $orderItem = new Admin_Model_OrderItem();
                    $orderItem->setOptions($this->getRequest()->getPost());
                    $orderItem->setOrder_item_order_id($order_id);
                    if ($orderItem->getOrder_item_variant_id()) {
                        $orderItem->setOrder_item_variant_name($variantsToForm[$orderItem->getOrder_item_variant_id()]);
                    }

                    $orderItem->setOrder_item_product_name($productsToForm[$orderItem->getOrder_item_product_id()]);

                    if ($orderItem->getOrder_item_variant_id() && $variants[$orderItem->getOrder_item_variant_id()]->getVariant_price()) {
                        $orderItem->setOrder_item_price($variants[$orderItem->getOrder_item_variant_id()]->getVariant_price());
                    } else {
                        $orderItem->setOrder_item_price($products[$orderItem->getOrder_item_product_id()]->getProduct_price());
                    }


                    $db = $this->_orderItemMapper->getDbTable()->getDefaultAdapter();
                    $db->beginTransaction();
                    try {
                        $this->_orderItemMapper->save($orderItem);

                        $order = $this->_orderMapper->getOrderById($order_id);
                        $orderItems = $this->_orderItemMapper->getOrderItemsByOrderId($order_id);

                        $invoiceId = $order->getOrder_invoice()->getInvoice_id();
                        if ($invoiceId) {
                            $invoiceCreatingDate = $order->getOrder_invoice()->getInvoice_creating_date();
                            $invoiceDueDate = $order->getOrder_invoice()->getInvoice_due_date();

                            //generuju fakturu
                            $invoicePdfGenerator = new My_InvoicePdfGenerator();
                            $invoicePdfGenerator->generateInvoice($invoiceId, $invoiceCreatingDate, $invoiceDueDate, $order, $orderItems);
                        }

                        $this->_flashMessenger->addMessage(array('info' => 'Položka objednávky byla úspěšně vložena'));

                        $db->commit();
                    } catch (Exception $e) {
                        $this->_flashMessenger->addMessage(array('error' => 'Při ukládání položky objednávky nastala chyba!<br />' . $e->getMessage()));
                        $db->rollback();
                    }

                    $module = $this->getRequest()->getModuleName();
                    $controller = $this->getRequest()->getControllerName();
//                http://weeker.cz/admin/order/edititem/id/1
                    $this->_redirect($module . '/' . $controller . '/edit/id/' . $order_id);
                } else {
                    $form->populate($this->getRequest()->getPost());
                }
            }

            $this->view->form = $form;
        }
    }

    public function deleteitemAction() {
        $id = $this->getRequest()->getParam('id');
        $order_id = $this->getRequest()->getParam('orderid');

        $db = $this->_orderItemMapper->getDbTable()->getDefaultAdapter();
        $db->beginTransaction();
        try {
            $this->_orderItemMapper->delete($id);

            $order = $this->_orderMapper->getOrderById($order_id);
            $orderItems = $this->_orderItemMapper->getOrderItemsByOrderId($order_id);

            $invoiceId = $order->getOrder_invoice()->getInvoice_id();
            if ($invoiceId) {
                $invoiceCreatingDate = $order->getOrder_invoice()->getInvoice_creating_date();
                $invoiceDueDate = $order->getOrder_invoice()->getInvoice_due_date();

                //generuju fakturu
                $invoicePdfGenerator = new My_InvoicePdfGenerator();
                $invoicePdfGenerator->generateInvoice($invoiceId, $invoiceCreatingDate, $invoiceDueDate, $order, $orderItems);
            }

            $db->commit();
            $this->_flashMessenger->addMessage(array('info' => 'Položka byla úspěšně smazána.'));
        } catch (Exception $e) {
            $this->_flashMessenger->addMessage(array('error' => 'Při mazání položky nastala chyba!<br />' . $e->getMessage()));
            $db->rollback();
        }

        $module = $this->getRequest()->getModuleName();
        $controller = $this->getRequest()->getControllerName();
        $this->_redirect($module . '/' . $controller . '/edit/id/' . $order_id);
    }

    /*
     * Order delivery
     */

    public function editdeliveryAction() {
        $order_id = (int) $this->getRequest()->getParam('id');

        if ($order_id) {

            $order = $this->_orderMapper->getOrderById($order_id);
            $this->view->order = $order;

            $deliveries = $this->_deliveryMapper->getDeliveries();

            if (!empty($deliveries)) {

                $deliveryToForm = array();
                $deliveriesAry = array();
                foreach ($deliveries as $delivery) {
                    $deliveryToForm[$delivery->getDelivery_id()] = $delivery->getDelivery_name() . "(" . $delivery->getDelivery_price_czk() . ")";
                    $deliveriesAry[$delivery->getDelivery_id()] = $delivery->getDelivery_name();
                }


                $form = new Admin_Form_EditOrderDeliveryForm();
                $form->setDeliveries($deliveryToForm);
                $form->startForm();

                $form->populate($order->toArray());

                $this->view->form = $form;


                if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {

                    $order = new Admin_Model_Order();
                    $order->setOptions($this->getRequest()->getPost());
                    $order->setOrder_delivery_name($deliveriesAry[$this->getRequest()->getParam('order_delivery_id')]);
                    $order->setOrder_id($order_id);

//                    print_r($order);die;

                    $db = $this->_orderMapper->getDbTable()->getDefaultAdapter();
                    $db->beginTransaction();
                    try {
                        $this->_orderMapper->save($order);

                        $order = $this->_orderMapper->getOrderById($order_id);
                        $orderItems = $this->_orderItemMapper->getOrderItemsByOrderId($order_id);

                        $invoiceId = $order->getOrder_invoice()->getInvoice_id();
                        if ($invoiceId) {
                            $invoiceCreatingDate = $order->getOrder_invoice()->getInvoice_creating_date();
                            $invoiceDueDate = $order->getOrder_invoice()->getInvoice_due_date();

                            //generuju fakturu
                            $invoicePdfGenerator = new My_InvoicePdfGenerator();
                            $invoicePdfGenerator->generateInvoice($invoiceId, $invoiceCreatingDate, $invoiceDueDate, $order, $orderItems);
                        }

                        $this->_flashMessenger->addMessage(array('info' => 'Doprava byla úspěšně upravena'));
                        $db->commit();
                    } catch (Exception $e) {
                        $this->_flashMessenger->addMessage(array('error' => 'Při ukládání položky nastala chyba!<br />' . $e->getMessage()));
                        $db->rollback();
                    }

                    $module = $this->getRequest()->getModuleName();
                    $controller = $this->getRequest()->getControllerName();

                    $this->_redirect($module . '/' . $controller . '/edit/id/' . $order_id);
                } else {
                    $form->populate($order->toArray());
                }
            }
        }
    }

    /*
     * Order Payment
     */

    public function editpaymentAction() {
        $order_id = (int) $this->getRequest()->getParam('id');

        if ($order_id) {

            $order = $this->_orderMapper->getOrderById($order_id);
            $this->view->order = $order;

            $payments = $this->_paymentMapper->getPayments();

            if (!empty($payments)) {

                $paymentToForm = array();
                $paymentsAry = array();
                foreach ($payments as $payment) {
                    $paymentToForm[$payment->getPayment_id()] = $payment->getPayment_name() . "(" . $payment->getPayment_price_czk() . ")";
                    $paymentsAry[$payment->getPayment_id()] = $payment->getPayment_name();
                }


                $form = new Admin_Form_EditOrderPaymentForm();
                $form->setPayments($paymentToForm);
                $form->startForm();

                $form->populate($order->toArray());

                $this->view->form = $form;


                if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {

                    $order = new Admin_Model_Order();
                    $order->setOptions($this->getRequest()->getPost());
                    $order->setOrder_payment_name($paymentsAry[$this->getRequest()->getParam('order_payment_id')]);
                    $order->setOrder_id($order_id);

//                    print_r($order);die;
                    $db = $this->_orderMapper->getDbTable()->getDefaultAdapter();
                    $db->beginTransaction();
                    try {
                        $this->_orderMapper->save($order);

                        $order = $this->_orderMapper->getOrderById($order_id);
                        $orderItems = $this->_orderItemMapper->getOrderItemsByOrderId($order_id);

                        $invoiceId = $order->getOrder_invoice()->getInvoice_id();
                        if ($invoiceId) {
                            $invoiceCreatingDate = $order->getOrder_invoice()->getInvoice_creating_date();
                            $invoiceDueDate = $order->getOrder_invoice()->getInvoice_due_date();

                            //generuju fakturu
                            $invoicePdfGenerator = new My_InvoicePdfGenerator();
                            $invoicePdfGenerator->generateInvoice($invoiceId, $invoiceCreatingDate, $invoiceDueDate, $order, $orderItems);
                        }

                        $this->_flashMessenger->addMessage(array('info' => 'Platba byla úspěšně upravena'));
                        $db->commit();
                    } catch (Exception $e) {
                        $this->_flashMessenger->addMessage(array('error' => 'Při ukládání položky nastala chyba!<br />' . $e->getMessage()));
                        $db->rollback();
                    }

                    $module = $this->getRequest()->getModuleName();
                    $controller = $this->getRequest()->getControllerName();

                    $this->_redirect($module . '/' . $controller . '/edit/id/' . $order_id);
                } else {
                    $form->populate($order->toArray());
                }
            }
        }
    }

    /*
     * generate invoice
     */

    public function orderinvoiceAction() {

        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $order_id = $this->getRequest()->getParam('id');

        $order = $this->_orderMapper->getOrderById($order_id);
        $orderItems = $this->_orderItemMapper->getOrderItemsByOrderId($order_id);


        //kontrola jestli uz neexistuje faktura
        $orderInvoice = $this->_invoiceMapper->getInvoiceByOrderId($order_id);
        if ($orderInvoice) {
            $this->_flashMessenger->addMessage(array('error' => 'Při generování faktury nastala chyba! Faktura již existuje!'));

//            $module = $this->getRequest()->getModuleName();
//            $controller = $this->getRequest()->getControllerName();
            $this->_redirect($this->getRequest()->getServer('HTTP_REFERER'));
        }


        //datum vytvoreni faktury
        $invoiceCreatingDate = time();
        //datum splatnosti faktury - 14 dnu
        $invoiceDueDate = time() + (7 * 24 * 60 * 60);

        $invoice = new Admin_Model_Invoice();
        $invoice->setInvoice_creating_date($invoiceCreatingDate);
        $invoice->setInvoice_due_date($invoiceDueDate);
        $invoice->setInvoice_order_id($order_id);


        //cislo faktury
        $invoiceNumber = $this->_invoiceMapper->getMaxInvoiceId();
        $invoiceNumber++;
        if (strlen($order_id) < 8 && strlen($order_id) > 0) {
            $invoiceNumber = sprintf("15/%07d", $invoiceNumber);
        }

        $invoice->setInvoice_number($invoiceNumber);
        $invoice->setInvoice_is_sent(0);


        $lastInvoiceId = 0;

        $db = $this->_invoiceMapper->getDbTable()->getDefaultAdapter();
        $db->beginTransaction();

        try {
            $this->_invoiceMapper->save($invoice);
            $lastInvoiceId = $this->_invoiceMapper->getDbTable()->getDefaultAdapter()->lastInsertId();

            $invoicePdfGenerator = new My_InvoicePdfGenerator();
            $invoicePdfGenerator->generateInvoice($invoiceNumber, $invoiceCreatingDate, $invoiceDueDate, $order, $orderItems);

            //update faktury - vlozim path
            $invoice = new Admin_Model_Invoice();
            $invoice->setInvoice_path($invoicePdfGenerator->getPdfPath());
            $invoice->setInvoice_id($lastInvoiceId);
            $this->_invoiceMapper->save($invoice);

            $this->_flashMessenger->addMessage(array('info' => 'Faktura byla úspěšně vygenerována.'));
            $db->commit();

//            $module = $this->getRequest()->getModuleName();
//            $controller = $this->getRequest()->getControllerName();
//            $this->_redirect($module . '/' . $controller);

            $this->_redirect($this->getRequest()->getServer('HTTP_REFERER'));
        } catch (Exception $e) {
            $this->_flashMessenger->addMessage(array('error' => 'Při generování faktury nastala chyba!<br />' . $e->getMessage()));

            //dojde-li k chybe, provedu rollback, data nebudou ulozena do db
            $db->rollBack();

//            $module = $this->getRequest()->getModuleName();
//            $controller = $this->getRequest()->getControllerName();
//            $this->_redirect($module . '/' . $controller);

            $this->_redirect($this->getRequest()->getServer('HTTP_REFERER'));
        }
    }

    //odeslani faktury emailem
    public function sendinvoiceAction() {
        $orderId = (int) $this->getRequest()->getParam('orderid');
        if ($orderId) {

            $orderMap = $this->_orderMapper->getOrderById($orderId);

            if ($orderMap->getOrder_id() && $orderMap->getOrder_invoice()->getInvoice_id() && $orderMap->getOrder_invoice()->getInvoice_path()) {
                //nastaveni a odeslani emailu
                $view = new Zend_View();

                $view->orderNumber = $orderMap->getOrder_number();

                //nastavim cestu k sablonam(template) emailu
                $view->setScriptPath(APPLICATION_PATH . '/modules/default/views/emails/');

                $html = $view->render('invoice.phtml');

                $mailer = new My_Mailer();
                $subject = "Yagga.cz - Faktura k objednávce č. " . $orderMap->getOrder_number();

                $others = array();
                $others['attachment'] = "../public/invoices/" . $orderMap->getOrder_invoice()->getInvoice_path();

                try {
                    $invoice = new Admin_Model_Invoice();
                    $invoice->setInvoice_is_sent(time());
                    $invoice->setInvoice_id($orderMap->getOrder_invoice()->getInvoice_id());

                    $this->_invoiceMapper->save($invoice);

                    $mailIsSent = $mailer->sendEmail($orderMap->getOrder_email(), $subject, $html, $others);

                    if($mailIsSent != 1){
                        throw new Exception();
                    }
                    
                    $this->_flashMessenger->addMessage(array('info' => 'Faktura byla úspěšně odeslána.'));
                } catch (Exception $e) {
                    $this->_flashMessenger->addMessage(array('error' => 'Při odesílání faktury nastala chyba!<br />' . $e->getMessage()));
                }

                $this->_redirect($this->getRequest()->getServer('HTTP_REFERER'));
            }
        }
    }

}

