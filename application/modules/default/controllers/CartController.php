<?php

class CartController extends Zend_Controller_Action {

    private $_productMapper;
    private $_variantMapper;
    private $_photographyMapper;
    private $_categoryMapper;

    public function init() {
        $this->_productMapper = new Default_Model_ProductMapper();
        $this->_variantMapper = new Default_Model_VariantMapper();
        $this->_photographyMapper = new Default_Model_PhotographyMapper();
        $this->_categoryMapper = new Default_Model_CategoryMapper();
    }

    public function indexAction() {
//        $sessionCart = new Zend_Session_Namespace('shoppingCart');
//        $cartItems = $sessionCart->cartItems;

        $cart = new My_ShoppingCart();
        $cartItems = $cart->getCartItems();
        $cartShort = $cart->getShortCart();

//        print_r($cartItems);die;
//        print_r($cartShort);die;

        $products = array();
        if (!empty($cartItems)) {

            $cartItemsKeys = array_keys($cartItems);

            $products = $this->_productMapper->getProductById($cartItemsKeys);

            //zjistim varianty a priradim k produktu

            foreach ($cartItems as $productKey => $cartItem) {
                if (!empty($cartItem['variants'])) {
                    $cartItemsVariantKeys = array_keys($cartItem['variants']);
                    $variants = $this->_variantMapper->getVariantsByIds($cartItemsVariantKeys);
                    $products[$productKey]->setVariants($variants);
                }
            }

            $this->view->cartItems = $cartItems;
        }


        $this->view->headTitle = "Nákupní košík";
        $this->view->shortCart = $cartShort;
        $this->view->products = $products;
    }

    
    public function removecartitemAction() {

        $productId = (int) $this->getRequest()->getParam('item');

        $cart = new My_ShoppingCart();

        if ($productId) {
            $cart->removeItem($productId);
        }

        $this->_redirect("/kosik");
    }

}
