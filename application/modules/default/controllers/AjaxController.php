<?php

class AjaxController extends Zend_Controller_Action {

    private $_productMapper;
    private $_variantMapper;
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

        $this->_productMapper = new Default_Model_ProductMapper();
        $this->_variantMapper = new Default_Model_VariantMapper();
    }

    public function editcartitemAction() {

        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);

        $action = $this->getRequest()->getParam('set');
        $productId = $this->getRequest()->getParam('product');


        $pieces = 0;
        $price = 0;


        if ($action == "up") {
            $pieces = 1;
            $price = $this->getRequest()->getParam('price');
        } else if ($action == "down") {
            $pieces = -1;
            $price = -$this->getRequest()->getParam('price');
        }

        $cart = new My_ShoppingCart();

        $cart->addItem($productId, $pieces, $price);
    }

    public function addtocartAction() {

        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);

        $productId = $this->getRequest()->getParam('product_id');
        $pieces = $this->getRequest()->getParam('pieces');
        $price = $this->getRequest()->getParam('price');

        if ($productId && $pieces && $price) {

            $cart = new My_ShoppingCart();
            $cart->addItem($productId, $pieces, $price);

            $shortCart = $cart->getShortCart();
//echo $productId;die;

            $product = $this->_productMapper->getProductById($productId);

            $product = current($product);

            $toModalData = array();

            $toModalData['pieces'] = $pieces;

            $myPrices = new My_Prices();
            $toModalData['price'] = $myPrices->getPrice($price * $pieces);

//        $toModalData['name'] = $product->getProduct_name();
//        $toModalData['variant_name'] = $product->getProduct_variant_name();
            $toModalData['name'] = $product->getProduct_name_variant_name();
            $toModalData['count'] = $shortCart['count'];
            $toModalData['amount'] = $myPrices->getPrice($shortCart['amount']);

            $myImages = new My_Images();
            $toModalData['image'] = $myImages->getProductImage($product, 'thumb');
//        $toModal['image'] = "/images/upload/product_" . $product->getProduct_id() . "/thumb/" . $product->getMain_photography()->getPhotography_path();

            $jsonResult = array();
            $jsonResult['view'] = $this->view->partial('cart_modal.phtml', $toModalData);

            $jsonResult['cart']['count'] = $toModalData['count'];
            $jsonResult['cart']['amount'] = $toModalData['amount'];

            $this->_response->setBody(json_encode($jsonResult));
        }

        return false;
    }

    public function getpriceAction() {

        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);

        $result = array();
        
        $price = $this->getRequest()->getParam('price');
        
        if($price){
            $myPrices = new My_Prices();
            $result['amount'] = $myPrices->getPrice($price);
        }
        
        $this->_response->setBody(json_encode($result));
        
    }

}
