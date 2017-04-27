<?php

class OrderController extends Zend_Controller_Action {

    private $_productMapper;
    private $_variantMapper;
    private $_photographyMapper;
    private $_categoryMapper;
    private $_orderMapper;
    private $_orderItemMapper;
    private $_deliveryPaymentMapper;
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

        $this->_productMapper = new Default_Model_ProductMapper();
        $this->_variantMapper = new Default_Model_VariantMapper();
        $this->_photographyMapper = new Default_Model_PhotographyMapper();
        $this->_categoryMapper = new Default_Model_CategoryMapper();
        $this->_orderMapper = new Default_Model_OrderMapper();
        $this->_orderItemMapper = new Default_Model_OrderItemMapper();
        $this->_deliveryPaymentMapper = new Default_Model_DeliveryPaymentMapper();
        $this->_orderOrderStateMapper = new Default_Model_OrderOrderStateMapper();
    }

    public function indexAction() {

        $this->view->headTitle = "Objednávka";


        $cart = new My_ShoppingCart();
        $cartItems = $cart->getCartItems();
        $cartShort = $cart->getShortCart();

//print_r($cartShort);die;

        $products = array();
        if (!empty($cartItems)) {

            $myPrices = new My_Prices();

            $cartItemsKeys = array_keys($cartItems);

            $products = $this->_productMapper->getProductById($cartItemsKeys);

            foreach ($cartItems as $productKey => $cartItem) {
                if (!empty($cartItem['variants'])) {
                    $cartItemsVariantKeys = array_keys($cartItem['variants']);
                    $variants = $this->_variantMapper->getVariantsByIds($cartItemsVariantKeys);
                    $products[$productKey]->setVariants($variants);
                }
            }

//print_r($products);die;
            $this->view->cartItems = $cartItems;


            $deliveriesPayments = $this->_deliveryPaymentMapper->getDeliveriesPayments();

//            print_r($deliveriesPayments);die;
            //data do json, potrebne pro disable dopravu a platby pri vyberu - javascriptem
            $deliveriesPaymentsToJson = array();
            foreach ($deliveriesPayments as $deliveryPayment) {
//                echo $myPrices->getPrice((int) $deliveryPayment->getDelivery()->getDelivery_price_czk());die;
                $deliveryPrice = $deliveryPayment->getDelivery()->getDelivery_price_czk();
                $deliveryPriceToDisplay = 'Zdarma';

                $deliveriesPaymentsToJson['delivery'][$deliveryPayment->getDelivery_payment_delivery_id()]['payments'][] = $deliveryPayment->getDelivery_payment_payment_id();
                $deliveriesPaymentsToJson['delivery'][$deliveryPayment->getDelivery_payment_delivery_id()]['price'] = $deliveryPrice;
                if ($deliveryPrice > 0) {
                    $deliveryPriceToDisplay = $myPrices->getPrice($deliveryPrice);
                }
                $deliveriesPaymentsToJson['delivery'][$deliveryPayment->getDelivery_payment_delivery_id()]['price_to_display'] = $deliveryPriceToDisplay;

                
                
                $paymentPrice = $deliveryPayment->getPayment()->getPayment_price_czk();
                $paymentPriceToDisplay = 'Zdarma';
                $deliveriesPaymentsToJson['payment'][$deliveryPayment->getDelivery_payment_payment_id()]['deliveries'][] = $deliveryPayment->getDelivery_payment_delivery_id();
                $deliveriesPaymentsToJson['payment'][$deliveryPayment->getDelivery_payment_payment_id()]['price'] = $paymentPrice;
                if ($paymentPrice > 0) {
                    $paymentPriceToDisplay = $myPrices->getPrice($paymentPrice);
                }
                $deliveriesPaymentsToJson['payment'][$deliveryPayment->getDelivery_payment_payment_id()]['price_to_display'] = $paymentPriceToDisplay;
            }
//            print_r($deliveriesPaymentsToJson);
//            die;
//          json deliveries and payments to view - for combinate delivery/payment in order  
            $this->view->jsDeliveryPayment = json_encode($deliveriesPaymentsToJson);
//            print_r(json_encode($deliveriesPaymentsToJson));die;

            
            
            $deliveries = $this->_deliveryPaymentMapper->getDeliveries();
            $deliveriesToForm = array();
            $deliveriesAry = array();
            foreach ($deliveries as $delivery) {
                $deliveryPrice = $delivery->getDelivery()->getDelivery_price_czk();
                $deliveryPriceToDisplay = "Zdarma";
                if($deliveryPrice > 0){
                    $deliveryPriceToDisplay = $myPrices->getPrice($deliveryPrice);
                }
                $deliveriesToForm[$delivery->getDelivery_payment_delivery_id()] = "<span class='delivery-name'>" . $delivery->getDelivery()->getDelivery_name() . "</span><span class='delivery-price'>" . $deliveryPriceToDisplay . "</span>";
                $deliveriesAry[$delivery->getDelivery_payment_delivery_id()] = $delivery->getDelivery()->getDelivery_name();
            }
            

            $payments = $this->_deliveryPaymentMapper->getPayments();
            $paymentsToForm = array();
            $paymentsAry = array();
            foreach ($payments as $payment) {
                $paymentPrice = $payment->getPayment()->getPayment_price_czk();
                $paymentPriceToDisplay = "Zdarma";
                if($paymentPrice > 0){
                    $paymentPriceToDisplay = $myPrices->getPrice($paymentPrice);
                }
                $paymentsToForm[$payment->getDelivery_payment_payment_id()] = "<span class='payment-name'>" . $payment->getPayment()->getPayment_name() . "</span><span class='payment-price'>" . $paymentPriceToDisplay . "</span>";
                $paymentsAry[$payment->getDelivery_payment_payment_id()] = $payment->getPayment()->getPayment_name();
            }

            $form = new Default_Form_OrderForm();
            $form->setDelivery($deliveriesToForm);
            $form->setPayment($paymentsToForm);
            $form->startForm();
            $this->view->form = $form;


            if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {

                $deliveryPaymentChecker = $this->_deliveryPaymentMapper->checkDeliveryPaymentCombination($form->getValue('delivery'), $form->getValue('payment'));

                if ($deliveryPaymentChecker) {

                    $db = $this->_orderMapper->getDbTable()->getDefaultAdapter();
                    $db->beginTransaction();

                    try {

                        $orderNumber = $this->_orderMapper->getMaxOrderId();
                        $orderNumber++;
                        $orderNumber = 1000 + $orderNumber;

                        $order = new Default_Model_Order();
                        $order->setOptions($form->getValues());
                        $order->setOrder_number($orderNumber);
                        $order->setOrder_date(time());

                        $order->setOrder_delivery_id($form->getValue('delivery'));
                        $order->setOrder_delivery_name($deliveriesAry[$form->getValue('delivery')]);
                        $order->setOrder_delivery_price($deliveriesPaymentsToJson['delivery'][$form->getValue('delivery')]['price']);

                        $order->setOrder_payment_id($form->getValue('payment'));
                        $order->setOrder_payment_name($paymentsAry[$form->getValue('payment')]);
                        $order->setOrder_payment_price($deliveriesPaymentsToJson['payment'][$form->getValue('payment')]['price']);

                        $order->setOrder_order_state_id(1);

                        $clientIpAddress = $this->getRequest()->getClientIp();
                        $order->setOrder_ip_address($clientIpAddress);

                        $this->_orderMapper->save($order);


//ziskam cislo objednavky a ulozim polozky objednavky
                        $lastOrderId = $this->_orderMapper->getDbTable()->getDefaultAdapter()->lastInsertId();

                        $orderItemsAry = array();
                        foreach ($products as $item) {

                            $variantsAry = $item->getVariants();
                            if (!empty($variantsAry)) {
                                foreach ($variantsAry as $variantItem) {

                                    $orderItem = new Default_Model_OrderItem();
                                    $orderItem->setOrder_item_order_id($lastOrderId);

                                    $orderItem->setOrder_item_product_id($item->getProduct_id());
                                    $orderItem->setOrder_item_product_name($item->getProduct_name());

                                    $orderItem->setOrder_item_variant_id($variantItem->getVariant_id());
                                    $orderItem->setOrder_item_variant_name($variantItem->getVariant_name());

                                    $orderItem->setOrder_item_pieces($cartItems[$item->getProduct_id()]['variants'][$variantItem->getVariant_id()]['variant_pieces']);
                                    $orderItem->setOrder_item_price($variantItem->getVariant_price());
                                    $orderItemsAry[] = $orderItem;
                                    $this->_orderItemMapper->save($orderItem);

                                    //odecitani skladu jednotlivych produktu/variant
                                    if (($item->getProduct_stock() > 0) && ($variantItem->getVariant_stock() > 0)) {
                                        $variantUpdateStock = new Default_Model_Variant();
                                        $orderPieces = (int) $cartItems[$item->getProduct_id()]['variants'][$variantItem->getVariant_id()]['variant_pieces'];
                                        $variantStock = $variantItem->getVariant_stock() - $orderPieces;
//                                kdyz je pocet kusu vetsi nebo roven, nastavim na nulu
                                        if ($orderPieces >= $variantItem->getVariant_stock()) {
                                            $variantStock = 0;
                                        }

                                        $variantUpdateStock->setVariant_stock($variantStock);
                                        $variantUpdateStock->setVariant_id($variantItem->getVariant_id());

                                        $this->_variantMapper->save($variantUpdateStock);

                                        //jeste odectu sklady u produktu
                                        $productUpdateStock = new Default_Model_Product();
                                        $productStock = (int) $item->getProduct_stock() - $orderPieces;

                                        if ($orderPieces >= $item->getProduct_stock()) {
                                            $productStock = 0;
                                        }

                                        $productUpdateStock->setProduct_stock($productStock);
                                        $productUpdateStock->setProduct_id($item->getProduct_id());

                                        $this->_productMapper->save($productUpdateStock);

//                                print_r($productUpdateStock);die;
//                                        $this->_productMapper->save($productUpdateStock);
                                    }
                                }
                            } else {
                                $orderItem = new Default_Model_OrderItem();
                                $orderItem->setOrder_item_order_id($lastOrderId);

                                $orderItem->setOrder_item_product_id($item->getProduct_id());
                                $orderItem->setOrder_item_product_name($item->getProduct_name());

                                $orderItem->setOrder_item_pieces($cartItems[$item->getProduct_id()]['pieces']);
                                $orderItem->setOrder_item_price($item->getProduct_price());
                                $orderItemsAry[] = $orderItem;
                                $this->_orderItemMapper->save($orderItem);

                                //odecitani skladu jednotlivych produktu
                                if ($item->getProduct_stock() > 0) {
                                    $productUpdateStock = new Default_Model_Product();
                                    $orderPieces = (int) $cartItems[$item->getProduct_id()]['pieces'];
                                    $stock = $item->getProduct_stock() - $orderPieces;
//                                kdyz je pocet kusu vetsi nebo roven, nastavim na nulu
                                    if ($orderPieces >= $item->getProduct_stock()) {
                                        $stock = 0;
                                    }

                                    $productUpdateStock->setProduct_stock($stock);
                                    $productUpdateStock->setProduct_id($item->getProduct_id());
//                                print_r($productUpdateStock);die;
                                    $this->_productMapper->save($productUpdateStock);
                                }
                            }
//    TODO doresit varianty
                        }

                        $orderNumberSession = new Zend_Session_Namespace('orderNumber');
                        $orderNumberSession->number = $lastOrderId;


                        //nastaveni a odeslani emailu
                        $view = new Zend_View();

                        $view->order = $order;
                        $view->orderItems = $orderItemsAry;
                        $view->cartItems = $cartItems;
                        $view->cartShort = $cartShort;
//print_r($orderItemsAry);die;
//                    print_r($cartShort);die;
                        //smazu kosik
                        $cart->unsetAll();


                        //nastavim cestu k sablonam(template) emailu
                        $view->setScriptPath(APPLICATION_PATH . '/modules/default/views/emails/');

                        $html = $view->render('ordercomplete.phtml');

                        $mailer = new My_Mailer();
                        $mailer->sendEmail($order->getOrder_email(), 'Yagga.cz - Potvrzení objednávky', $html);

//                      stejny email o vytvoreni objednavky odeslu i na info@yagga.cz   
                        $mailer->sendEmail("info@yagga.cz", 'Yagga.cz - Nová objednávka', $html);

                        $orderOrderState = new Default_Model_OrderOrderState();
                        $orderOrderState->setOrder_order_state_order_id($lastOrderId);
                        $orderOrderState->setOrder_order_state_order_state_id(1);
                        $orderOrderState->setOrder_order_state_date(time());

                        $this->_orderOrderStateMapper->save($orderOrderState);

                        $db->commit();
                        $this->_redirect('/objednavka/dekujeme');
                    } catch (Exception $e) {
                        $db->rollBack();
                        $this->_flashMessenger->addMessage(array('error' => 'Při ukládání objednávky nastala chyba!<br />' . $e->getMessage()));
                    }
                } else {
                    $this->view->deliveryPaymentError = 'Zvolenou kombinaci dopravy a platby nelze použít. Zvolte prosím jinou kombinaci dopravy a platby.';
                }
            } else {
                if ($form->getValue('order_is_d_address') == 1) {
                    $this->view->showFields = true;
                }
                $form->populate($form->getValues());
            }
        } else {
            $this->_redirect('/kosik');
        }

        $this->view->shortCart = $cartShort;

        $this->view->products = $products;
    }

//  dekujeme - objednavka dokoncena  
    public function completeAction() {

        $this->view->headTitle = "Děkujeme";

        $orderNumberSession = new Zend_Session_Namespace('orderNumber');
        $orderId = $orderNumberSession->number;


        $orderNumber = "";
        $gaEcommerceScript = "";

        $revenue = 0;
        if ($orderId) {
            $order = $this->_orderMapper->getOrderByOrderId($orderId);

            $orderNumber = $order->getOrder_number();

            $orderItems = array();
            if ($order && $order->getOrder_id()) {
                $orderItems = $this->_orderItemMapper->getOrderItemsByOrderId($order->getOrder_id());
            }

            if ($order && !empty($orderItems)) {

                $overeno = new My_HeurekaOvereno('04fadd58c6e78e948876d97cb2f9cdc6');
                $overeno->setEmail((string) $order->getOrder_email());
                $overeno->addOrderId($order->getOrder_number());

                $gaOrderItems = "";
                $heurekaConversionItems = "";
                foreach ($orderItems as $item) {
                    $heurekaItemId = $item->getOrder_item_product_id();
                    if ($item->getOrder_item_variant_name()) {
                        $productVariantName = $item->getOrder_item_product_name() . " " . $item->getOrder_item_variant_name();
                        $item->setOrder_item_product_name($productVariantName);

                        $heurekaItemId = $item->getOrder_item_product_id() . "_" . $item->getOrder_item_variant_id();
                    }
                    $itemAry = $item->toArray();
                    $revenue += (int) $item->getOrder_item_price() * (int) $item->getOrder_item_pieces();
                    $gaOrderItems .= $this->getItemJs($order->getOrder_id(), $itemAry);

                    $heurekaConversionItems .= "_hrq.push(['addProduct', '" . $item->getOrder_item_product_name() . "', '" . (int) $item->getOrder_item_price() . "', '" . (int) $item->getOrder_item_pieces() . "']);";

                    $overeno->addProduct($item->getOrder_item_product_name());
                    $overeno->addProductItemId($heurekaItemId);
                }

                $gaEcommerceScript .= "ga('require', 'ecommerce');";

//k items pridam jeste dopravu a platbu
                $deliveryAry = array(
                    'order_item_product_id' => 'delivery_' . $order->getOrder_delivery_id(),
                    'order_item_product_name' => $order->getOrder_delivery_name(),
                    'order_item_price' => $order->getOrder_delivery_price(),
                    'order_item_pieces' => 1
                );

                $paymentAry = array(
                    'order_item_product_id' => 'payment_' . $order->getOrder_payment_id(),
                    'order_item_product_name' => $order->getOrder_payment_name(),
                    'order_item_price' => $order->getOrder_payment_price(),
                    'order_item_pieces' => 1
                );

                $gaOrderItems .= $this->getItemJs($order->getOrder_id(), $deliveryAry);
                $gaOrderItems .= $this->getItemJs($order->getOrder_id(), $paymentAry);

                $heurekaConversionItems .= "_hrq.push(['addProduct', '" . $order->getOrder_delivery_name() . "', '1', '" . (int) $order->getOrder_delivery_price() . "']);";
                $heurekaConversionItems .= "_hrq.push(['addProduct', '" . $order->getOrder_payment_name() . "', '1', '" . (int) $order->getOrder_payment_price() . "']);";

                $this->view->heurekaConversionScript = $this->getHeurekaConversion($order->getOrder_number(), $heurekaConversionItems);


                $orderAry = $order->toArray();

                $revenue += (int) $order->getOrder_delivery_price();
                $revenue += (int) $order->getOrder_payment_price();

                $orderAry['revenue'] = $revenue;

                $this->view->orderAmount = $revenue;

                $gaEcommerceScript .= $this->getTransactionJs($orderAry);

                $gaEcommerceScript .= $gaOrderItems;

                $gaEcommerceScript .= "ga('ecommerce:send');";

                $overeno->send();
            }
        } else {
            $this->_redirect('/kosik');
        }

        $this->view->gaEcommerceScript = $gaEcommerceScript;
        $this->view->orderNumber = $orderNumber;

        $orderNumberSession->unsetAll();
    }

// Function to return the JavaScript representation of a TransactionData object.
    private function getTransactionJs($trans) {
        return <<<HTML
ga('ecommerce:addTransaction', {
  'id': '{$trans['order_id']}',
  'revenue': '{$trans['revenue']}',
});
HTML;
    }

    // Function to return the JavaScript representation of an ItemData object.
    private function getItemJs($transId, $item) {
        return <<<HTML
ga('ecommerce:addItem', {
  'id': '$transId',
  'sku': '{$item['order_item_product_id']}',      
  'name': '{$item['order_item_product_name']}',
  'price': '{$item['order_item_price']}',
  'quantity': '{$item['order_item_pieces']}'
});
HTML;
    }

    /*
     * HEUREKA - mereni konverzi
     */

    private function getHeurekaConversion($transId, $items) {

        $script = "";
        $script = "var _hrq = _hrq || [];
    _hrq.push(['setKey', '03FAE9A272AE5E14410AC242699A5659']);
    _hrq.push(['setOrderId', '{$transId}']);";

        $script .= $items;

        $script .= "_hrq.push(['trackOrder']);";

        $script .= "(function() {
    var ho = document.createElement('script'); ho.type = 'text/javascript'; ho.async = true;
    ho.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.heureka.cz/direct/js/cache/1-roi-async.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ho, s);})();";

        return $script;
    }

}