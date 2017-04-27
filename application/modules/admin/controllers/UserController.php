<?php

class Admin_UserController extends Zend_Controller_Action {

    private $_flashMessenger;
    private $_orderMapper;
    private $_orderItemMapper;
    private $_orderStateMapper;
    private $_productMapper;
    private $_variantMapper;
    private $_deliveryMapper;
    private $_paymentMapper;
    private $_invoiceMapper;
    private $_userProfileMapper;
    private $_userMapper;

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
        $this->_userProfileMapper = new Admin_Model_UserProfileMapper();
        $this->_userMapper = new Admin_Model_UserMapper();
    }

    /*
     * Order
     */

    public function indexAction() {

        $userProfiles = $this->_userProfileMapper->getUserProfiles();
        $this->view->userProfiles = $userProfiles;
    }

    public function addAction() {

        $form = new Admin_Form_EditUserForm();
        $form->startForm();
        $this->view->form = $form;

        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
            $user = new Admin_Model_User();
            $user->setOptions($this->getRequest()->getPost());
            $user->setUser_created_date(time());

            $userProfile = new Admin_Model_UserProfile();
            $userProfile->setOptions($this->getRequest()->getPost());

            try {

                $this->_userMapper->save($user);

                $lastUserId = $this->_userMapper->getDbTable()->getDefaultAdapter()->lastInsertId();

                $userProfile->setUser_profile_user_id($lastUserId);
                $this->_userProfileMapper->save($userProfile);

                $this->_flashMessenger->addMessage(array('info' => 'Uživatel byl úspěšně vložen.'));
            } catch (Exception $e) {
                $this->_flashMessenger->addMessage(array('error' => 'Při ukládání uživatele nastala chyba!<br />' . $e->getMessage()));
            }

            $module = $this->getRequest()->getModuleName();
            $controller = $this->getRequest()->getControllerName();
            $this->_redirect($module . '/' . $controller);
        } else {
            $form->populate($form->getValues());
        }
    }

//
//    public function deleteAction() {
//        $id = $this->getRequest()->getParam('id');
//
//        $db = $this->_orderMapper->getDbTable()->getDefaultAdapter();
//        $db->beginTransaction();
//        try {
//
//            $invoice = $this->_invoiceMapper->getInvoiceByOrderId($id);
//            if ($invoice->getInvoice_id()) {
//                $invoicePath = INVOICE_UPLOAD_PATH . '/' . $invoice->getInvoice_path();
//                unlink($invoicePath);
//            }
//
//            $this->_orderMapper->delete($id);
//
//            $this->_flashMessenger->addMessage(array('info' => 'Objednávka byla úspěšně smazána.'));
//            $db->commit();
//        } catch (Exception $e) {
//            $this->_flashMessenger->addMessage(array('error' => 'Při mazání objednávky nastala chyba!<br />' . $e->getMessage()));
//            $db->rollback();
//        }
//
//        $module = $this->getRequest()->getModuleName();
//        $controller = $this->getRequest()->getControllerName();
//        $this->_redirect($module . '/' . $controller);
//    }
//
//    /*
//     * Order Item
//     */
//
//    public function edititemAction() {
//        $order_item_id = (int) $this->getRequest()->getParam('id');
//
//        if ($order_item_id) {
//
//            $orderItem = $this->_orderItemMapper->getOrderItemsById($order_item_id);
//            $this->view->orderItem = $orderItem;
//
//            $order_id = $orderItem->getOrder_item_order_id();
//
//            $variantsToForm = array();
//
//            if ($orderItem->getOrder_item_variant_id()) {
//                $variants = $this->_variantMapper->getVariantsByProductId($orderItem->getOrder_item_product_id());
//                if (!empty($variants)) {
//                    foreach ($variants as $variant) {
//                        $variantsToForm[$variant->getVariant_id()] = $variant->getVariant_name();
//                    }
//                }
//            }
//
//            $form = new Admin_Form_EditOrderItemForm();
//            $form->setVariant($variantsToForm);
//            $form->startForm();
//
//            $form->populate($orderItem->toArray());
//
//            $this->view->form = $form;
//
//            if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
//                $orderItem = new Admin_Model_OrderItem();
//                $orderItem->setOptions($form->getValues());
//                $orderItem->setOrder_item_variant_name($variantsToForm[$orderItem->getOrder_item_variant_id()]);
//                $orderItem->setOrder_item_id($order_item_id);
//
////                print_r($orderItem);die;
//                $db = $this->_orderItemMapper->getDbTable()->getDefaultAdapter();
//                $db->beginTransaction();
//                try {
//                    $this->_orderItemMapper->save($orderItem);
//
//                    $order = $this->_orderMapper->getOrderById($order_id);
//                    $orderItems = $this->_orderItemMapper->getOrderItemsByOrderId($order_id);
//
//                    $invoiceId = $order->getOrder_invoice()->getInvoice_id();
//                    if ($invoiceId) {
//                        $invoiceCreatingDate = $order->getOrder_invoice()->getInvoice_creating_date();
//                        $invoiceDueDate = $order->getOrder_invoice()->getInvoice_due_date();
//
//                        //generuju fakturu
//                        $invoicePdfGenerator = new My_InvoicePdfGenerator();
//                        $invoicePdfGenerator->generateInvoice($invoiceId, $invoiceCreatingDate, $invoiceDueDate, $order, $orderItems);
//                    }
//
//                    $this->_flashMessenger->addMessage(array('info' => 'Položka objednávky byla úspěšně upravena'));
//
//                    $db->commit();
//                } catch (Exception $e) {
//                    $this->_flashMessenger->addMessage(array('error' => 'Při ukládání položky objednávky nastala chyba!<br />' . $e->getMessage()));
//                    $db->rollback();
//                }
//
//                $module = $this->getRequest()->getModuleName();
//                $controller = $this->getRequest()->getControllerName();
//                $action = $this->getRequest()->getActionName();
////                http://weeker.cz/admin/order/edititem/id/1
//                $this->_redirect($module . '/' . $controller . '/edit/id/' . $order_id);
//
//                $this->_redirect($this->getRequestuest()->getServer('HTTP_REFERER'));
//            } else {
//                $form->populate($orderItem->toArray());
//            }
//        }
//    }
//
//    public function additemAction() {
//        $order_id = (int) $this->getRequest()->getParam('id');
//
//        if ($order_id) {
//            $products = $this->_productMapper->getProducts();
//
//            $productsToForm = array();
//            $productsToForm[0] = '--Vyberte--';
//
//            foreach ($products as $product) {
//                $productsToForm[$product->getProduct_id()] = $product->getProduct_name();
//            }
//
//
//            $form = new Admin_Form_AddOrderItemForm();
//            $form->setProducts($productsToForm);
//
//            $form->startForm();
//
//            if ($this->getRequest()->isPost()) {
//
//                $variant_id = $this->getRequest()->getPost('order_item_variant_id');
//
//                $variants = array();
//                $variantsToForm = array();
//                if (isset($variant_id)) {
//                    $variants = $this->_variantMapper->getVariantsByProductId($this->getRequest()->getPost('order_item_product_id'));
//
//                    $variantsToForm[0] = '--Vyberte--';
//                    foreach ($variants as $variant) {
//                        $variantsToForm[$variant->getVariant_id()] = $variant->getVariant_name();
//                    }
//
//                    $form->setVariants($variantsToForm);
//                    $form->startForm();
//                }
//
//                if ($form->isValid($this->getRequest()->getPost())) {
//
//                    $orderItem = new Admin_Model_OrderItem();
//                    $orderItem->setOptions($this->getRequest()->getPost());
//                    $orderItem->setOrder_item_order_id($order_id);
//                    if ($orderItem->getOrder_item_variant_id()) {
//                        $orderItem->setOrder_item_variant_name($variantsToForm[$orderItem->getOrder_item_variant_id()]);
//                    }
//
//                    $orderItem->setOrder_item_product_name($productsToForm[$orderItem->getOrder_item_product_id()]);
//
//                    if ($orderItem->getOrder_item_variant_id() && $variants[$orderItem->getOrder_item_variant_id()]->getVariant_price()) {
//                        $orderItem->setOrder_item_price($variants[$orderItem->getOrder_item_variant_id()]->getVariant_price());
//                    } else {
//                        $orderItem->setOrder_item_price($products[$orderItem->getOrder_item_product_id()]->getProduct_price());
//                    }
//
//
//                    $db = $this->_orderItemMapper->getDbTable()->getDefaultAdapter();
//                    $db->beginTransaction();
//                    try {
//                        $this->_orderItemMapper->save($orderItem);
//
//                        $order = $this->_orderMapper->getOrderById($order_id);
//                        $orderItems = $this->_orderItemMapper->getOrderItemsByOrderId($order_id);
//
//                        $invoiceId = $order->getOrder_invoice()->getInvoice_id();
//                        if ($invoiceId) {
//                            $invoiceCreatingDate = $order->getOrder_invoice()->getInvoice_creating_date();
//                            $invoiceDueDate = $order->getOrder_invoice()->getInvoice_due_date();
//
//                            //generuju fakturu
//                            $invoicePdfGenerator = new My_InvoicePdfGenerator();
//                            $invoicePdfGenerator->generateInvoice($invoiceId, $invoiceCreatingDate, $invoiceDueDate, $order, $orderItems);
//                        }
//
//                        $this->_flashMessenger->addMessage(array('info' => 'Položka objednávky byla úspěšně vložena'));
//
//                        $db->commit();
//                    } catch (Exception $e) {
//                        $this->_flashMessenger->addMessage(array('error' => 'Při ukládání položky objednávky nastala chyba!<br />' . $e->getMessage()));
//                        $db->rollback();
//                    }
//
//                    $module = $this->getRequest()->getModuleName();
//                    $controller = $this->getRequest()->getControllerName();
////                http://weeker.cz/admin/order/edititem/id/1
//                    $this->_redirect($module . '/' . $controller . '/edit/id/' . $order_id);
//                } else {
//                    $form->populate($this->getRequest()->getPost());
//                }
//            }
//
//            $this->view->form = $form;
//        }
//    }
//
//    public function deleteitemAction() {
//        $id = $this->getRequest()->getParam('id');
//        $order_id = $this->getRequest()->getParam('orderid');
//
//        $db = $this->_orderItemMapper->getDbTable()->getDefaultAdapter();
//        $db->beginTransaction();
//        try {
//            $this->_orderItemMapper->delete($id);
//
//            $order = $this->_orderMapper->getOrderById($order_id);
//            $orderItems = $this->_orderItemMapper->getOrderItemsByOrderId($order_id);
//
//            $invoiceId = $order->getOrder_invoice()->getInvoice_id();
//            if ($invoiceId) {
//                $invoiceCreatingDate = $order->getOrder_invoice()->getInvoice_creating_date();
//                $invoiceDueDate = $order->getOrder_invoice()->getInvoice_due_date();
//
//                //generuju fakturu
//                $invoicePdfGenerator = new My_InvoicePdfGenerator();
//                $invoicePdfGenerator->generateInvoice($invoiceId, $invoiceCreatingDate, $invoiceDueDate, $order, $orderItems);
//            }
//
//            $db->commit();
//            $this->_flashMessenger->addMessage(array('info' => 'Položka byla úspěšně smazána.'));
//        } catch (Exception $e) {
//            $this->_flashMessenger->addMessage(array('error' => 'Při mazání položky nastala chyba!<br />' . $e->getMessage()));
//            $db->rollback();
//        }
//
//        $module = $this->getRequest()->getModuleName();
//        $controller = $this->getRequest()->getControllerName();
//        $this->_redirect($module . '/' . $controller . '/edit/id/' . $order_id);
//    }
//
//    /*
//     * Order delivery
//     */
//
//    public function editdeliveryAction() {
//        $order_id = (int) $this->getRequest()->getParam('id');
//
//        if ($order_id) {
//
//            $order = $this->_orderMapper->getOrderById($order_id);
//            $this->view->order = $order;
//
//            $deliveries = $this->_deliveryMapper->getDeliveries();
//
//            if (!empty($deliveries)) {
//
//                $deliveryToForm = array();
//                $deliveriesAry = array();
//                foreach ($deliveries as $delivery) {
//                    $deliveryToForm[$delivery->getDelivery_id()] = $delivery->getDelivery_name() . "(" . $delivery->getDelivery_price_czk() . ")";
//                    $deliveriesAry[$delivery->getDelivery_id()] = $delivery->getDelivery_name();
//                }
//
//
//                $form = new Admin_Form_EditOrderDeliveryForm();
//                $form->setDeliveries($deliveryToForm);
//                $form->startForm();
//
//                $form->populate($order->toArray());
//
//                $this->view->form = $form;
//
//
//                if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
//
//                    $order = new Admin_Model_Order();
//                    $order->setOptions($this->getRequest()->getPost());
//                    $order->setOrder_delivery_name($deliveriesAry[$this->getRequest()->getParam('order_delivery_id')]);
//                    $order->setOrder_id($order_id);
//
////                    print_r($order);die;
//
//                    $db = $this->_orderMapper->getDbTable()->getDefaultAdapter();
//                    $db->beginTransaction();
//                    try {
//                        $this->_orderMapper->save($order);
//
//                        $order = $this->_orderMapper->getOrderById($order_id);
//                        $orderItems = $this->_orderItemMapper->getOrderItemsByOrderId($order_id);
//
//                        $invoiceId = $order->getOrder_invoice()->getInvoice_id();
//                        if ($invoiceId) {
//                            $invoiceCreatingDate = $order->getOrder_invoice()->getInvoice_creating_date();
//                            $invoiceDueDate = $order->getOrder_invoice()->getInvoice_due_date();
//
//                            //generuju fakturu
//                            $invoicePdfGenerator = new My_InvoicePdfGenerator();
//                            $invoicePdfGenerator->generateInvoice($invoiceId, $invoiceCreatingDate, $invoiceDueDate, $order, $orderItems);
//                        }
//
//                        $this->_flashMessenger->addMessage(array('info' => 'Doprava byla úspěšně upravena'));
//                        $db->commit();
//                    } catch (Exception $e) {
//                        $this->_flashMessenger->addMessage(array('error' => 'Při ukládání položky nastala chyba!<br />' . $e->getMessage()));
//                        $db->rollback();
//                    }
//
//                    $module = $this->getRequest()->getModuleName();
//                    $controller = $this->getRequest()->getControllerName();
//
//                    $this->_redirect($module . '/' . $controller . '/edit/id/' . $order_id);
//                } else {
//                    $form->populate($order->toArray());
//                }
//            }
//        }
//    }
//
//    /*
//     * Order Payment
//     */
//
//    public function editpaymentAction() {
//        $order_id = (int) $this->getRequest()->getParam('id');
//
//        if ($order_id) {
//
//            $order = $this->_orderMapper->getOrderById($order_id);
//            $this->view->order = $order;
//
//            $payments = $this->_paymentMapper->getPayments();
//
//            if (!empty($payments)) {
//
//                $paymentToForm = array();
//                $paymentsAry = array();
//                foreach ($payments as $payment) {
//                    $paymentToForm[$payment->getPayment_id()] = $payment->getPayment_name() . "(" . $payment->getPayment_price_czk() . ")";
//                    $paymentsAry[$payment->getPayment_id()] = $payment->getPayment_name();
//                }
//
//
//                $form = new Admin_Form_EditOrderPaymentForm();
//                $form->setPayments($paymentToForm);
//                $form->startForm();
//
//                $form->populate($order->toArray());
//
//                $this->view->form = $form;
//
//
//                if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
//
//                    $order = new Admin_Model_Order();
//                    $order->setOptions($this->getRequest()->getPost());
//                    $order->setOrder_payment_name($paymentsAry[$this->getRequest()->getParam('order_payment_id')]);
//                    $order->setOrder_id($order_id);
//
////                    print_r($order);die;
//                    $db = $this->_orderMapper->getDbTable()->getDefaultAdapter();
//                    $db->beginTransaction();
//                    try {
//                        $this->_orderMapper->save($order);
//
//                        $order = $this->_orderMapper->getOrderById($order_id);
//                        $orderItems = $this->_orderItemMapper->getOrderItemsByOrderId($order_id);
//
//                        $invoiceId = $order->getOrder_invoice()->getInvoice_id();
//                        if ($invoiceId) {
//                            $invoiceCreatingDate = $order->getOrder_invoice()->getInvoice_creating_date();
//                            $invoiceDueDate = $order->getOrder_invoice()->getInvoice_due_date();
//
//                            //generuju fakturu
//                            $invoicePdfGenerator = new My_InvoicePdfGenerator();
//                            $invoicePdfGenerator->generateInvoice($invoiceId, $invoiceCreatingDate, $invoiceDueDate, $order, $orderItems);
//                        }
//
//                        $this->_flashMessenger->addMessage(array('info' => 'Platba byla úspěšně upravena'));
//                        $db->commit();
//                    } catch (Exception $e) {
//                        $this->_flashMessenger->addMessage(array('error' => 'Při ukládání položky nastala chyba!<br />' . $e->getMessage()));
//                        $db->rollback();
//                    }
//
//                    $module = $this->getRequest()->getModuleName();
//                    $controller = $this->getRequest()->getControllerName();
//
//                    $this->_redirect($module . '/' . $controller . '/edit/id/' . $order_id);
//                } else {
//                    $form->populate($order->toArray());
//                }
//            }
//        }
//    }
//
//    /*
//     * generate invoice
//     */
//
//    public function orderinvoiceAction() {
//
//        $this->_helper->layout()->disableLayout();
//        $this->_helper->viewRenderer->setNoRender(true);
//
//        $order_id = $this->getRequest()->getParam('id');
//
//        $order = $this->_orderMapper->getOrderById($order_id);
//        $orderItems = $this->_orderItemMapper->getOrderItemsByOrderId($order_id);
//
//
//        //kontrola jestli uz neexistuje faktura
//        $orderInvoice = $this->_invoiceMapper->getInvoiceByOrderId($order_id);
//        if ($orderInvoice) {
//            $this->_flashMessenger->addMessage(array('error' => 'Při generování faktury nastala chyba! Faktura již existuje!'));
//
////            $module = $this->getRequest()->getModuleName();
////            $controller = $this->getRequest()->getControllerName();
//            $this->_redirect($this->getRequest()->getServer('HTTP_REFERER'));
//        }
//
//
//        //datum vytvoreni faktury
//        $invoiceCreatingDate = time();
//        //datum splatnosti faktury - 14 dnu
//        $invoiceDueDate = time() + (14 * 24 * 60 * 60);
//
//        $invoice = new Admin_Model_Invoice();
//        $invoice->setInvoice_creating_date($invoiceCreatingDate);
//        $invoice->setInvoice_due_date($invoiceDueDate);
//        $invoice->setInvoice_order_id($order_id);
//
//
//        $lastInvoiceId = 0;
//
//        $db = $this->_invoiceMapper->getDbTable()->getDefaultAdapter();
//        $db->beginTransaction();
//
//        try {
//            $this->_invoiceMapper->save($invoice);
//            $lastInvoiceId = $this->_invoiceMapper->getDbTable()->getDefaultAdapter()->lastInsertId();
//
//            $invoicePdfGenerator = new My_InvoicePdfGenerator();
//            $invoicePdfGenerator->generateInvoice($lastInvoiceId, $invoiceCreatingDate, $invoiceDueDate, $order, $orderItems);
//
//            //update faktury - vlozim path
//            $invoice = new Admin_Model_Invoice();
//            $invoice->setInvoice_path($invoicePdfGenerator->getPdfPath());
//            $invoice->setInvoice_id($lastInvoiceId);
//            $this->_invoiceMapper->save($invoice);
//
//            $this->_flashMessenger->addMessage(array('info' => 'Faktura byla úspěšně vygenerována.'));
//            $db->commit();
//
////            $module = $this->getRequest()->getModuleName();
////            $controller = $this->getRequest()->getControllerName();
////            $this->_redirect($module . '/' . $controller);
//
//            $this->_redirect($this->getRequest()->getServer('HTTP_REFERER'));
//        } catch (Exception $e) {
//            $this->_flashMessenger->addMessage(array('error' => 'Při generování faktury nastala chyba!<br />' . $e->getMessage()));
//
//            //dojde-li k chybe, provedu rollback, data nebudou ulozena do db
//            $db->rollBack();
//
////            $module = $this->getRequest()->getModuleName();
////            $controller = $this->getRequest()->getControllerName();
////            $this->_redirect($module . '/' . $controller);
//
//            $this->_redirect($this->getRequest()->getServer('HTTP_REFERER'));
//        }
//    }
}

