<?php

class My_ShoppingCart {

    private $_sessionCart;
    private $_cartItems = array();

    public function init() {
        $this->_sessionCart = new Zend_Session_Namespace('shoppingCart');
//        Zend_Session::rememberMe(864000);
        $this->_sessionCart->setExpirationSeconds(90 * 864000); //90 dni
        $this->_cartItems = (array) $this->_sessionCart->cartItems;
    }

    public function __construct() {
        $this->init();
//        print_r($this->_cartItems);
    }

    public function getCartItems() {
//        print_r($this->_cartItems);
//        die;

        return $this->_cartItems;
    }

    public function addItem($id, $pieces, $price) {
        if ((int) $pieces === 0)
            return false;

        //pokud uz je produkt v kosiku
        if (!empty($this->_cartItems[$id])) {

            $productPieces = $this->_cartItems[$id]['pieces'] + (int) $pieces;
            $productPrice = $this->_cartItems[$id]['price'] + (int) $price;

            if ($productPieces <= 0 || $productPrice <= 0) {
                $this->removeItem($id);
            } else {
                $this->_cartItems[$id] = array(
                    'pieces' => (int) $productPieces,
                    'price' => (int) $productPrice
                );
            }
        } else {
            // pokud produkt neni v kosiku

            if ($pieces > 0 && $price > 0) {

                $this->_cartItems[$id] = array(
                    'pieces' => (int) $pieces,
                    'price' => (int) $price
                );

            }
        }

        return $this->save();
    }


    public function save() {

        if (sizeof($this->_cartItems)) {
            $this->_sessionCart->cartItems = $this->_cartItems;
        } else {
            $this->_sessionCart->cartItems = array();
        }

        return true;
    }

    public function getShortCart() {
        $shortCart = array();
        $count = 0;
        $amount = 0;
//        $shortCart = array('count' => $count, 'amount' => $amount);
        if (!empty($this->_sessionCart->cartItems)) {
            foreach ($this->_sessionCart->cartItems as $item) {
                $count += $item['pieces'];
                $amount += $item['price'];
            }

            $shortCart = array('count' => $count, 'amount' => $amount);
        }

//        print_r($shortCart);die;

        return $shortCart;
    }

    //smazu produkt
    public function removeItem($id) {

        $this->_cartItems[$id] = 0;
        unset($this->_cartItems[$id]);

        // musime ulozit pred redirectem
        $this->save();
        return true;
    }

    
    public function unsetAll() {

        $this->_sessionCart->unsetAll();
    }

}