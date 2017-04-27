<?php

class Default_Model_OrderItem extends Model_Model {

    protected $_id;
    protected $_order_id;
    protected $_product_name;
    protected $_variant_name;
    protected $_product_id;
    protected $_variant_id;
    protected $_pieces;
    protected $_price;
    
    public function setOrder_item_id($id) {
        $this->_id = (int) $id;
    }

    public function getOrder_item_id() {
        return $this->_id;
    }

    public function setOrder_item_order_id($order_id) {
        $this->_order_id = $order_id;
    }

    public function getOrder_item_order_id() {
        return $this->_order_id;
    }
    
    public function setOrder_item_product_name($product_name) {
        $this->_product_name = $product_name;
    }

    public function getOrder_item_product_name() {
        return $this->_product_name;
    }
    
    public function setOrder_item_variant_name($variant_name) {
        $this->_variant_name = $variant_name;
    }

    public function getOrder_item_variant_name() {
        return $this->_variant_name;
    }

    public function setOrder_item_product_id($product_id) {
        $this->_product_id = $product_id;
    }

    public function getOrder_item_product_id() {
        return $this->_product_id;
    }
    
    public function setOrder_item_variant_id($variant_id) {
        $this->_variant_id = $variant_id;
    }

    public function getOrder_item_variant_id() {
        return $this->_variant_id;
    }
    
    public function setOrder_item_pieces($pieces) {
        $this->_pieces = $pieces;
    }

    public function getOrder_item_pieces() {
        return $this->_pieces;
    }
    
    public function setOrder_item_price($price) {
        $this->_price = $price;
    }

    public function getOrder_item_price() {
        return $this->_price;
    }
    
    
    /*
     * 
     * paramety mimo DB
     * 
     */

    public function getOrder_item_total_price() {
        return $this->_price * $this->_pieces;
    }

}
