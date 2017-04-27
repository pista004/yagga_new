<?php

class Admin_Model_Variant extends Model_Model {
    
    protected $_id;
    protected $_name;
    protected $_stock;
    protected $_purchase_price;
    protected $_price;
    protected $_is_active;
    protected $_product_id;
    
    protected $_productAndVariant;
    
    public function setVariant_id($id) {
        $this->_id = (int) $id;
    }

    public function getVariant_id() {
        return $this->_id;
    }

    public function setVariant_name($name) {
        $this->_name = $name;
    }

    public function getVariant_name() {
        return $this->_name;
    }
    
    public function setVariant_stock($stock) {
        $this->_stock = $stock;
    }

    public function getVariant_stock() {
        return $this->_stock;
    }
    
    public function setVariant_purchase_price($purchase_price) {
        $this->_purchase_price = $purchase_price;
    }

    public function getVariant_purchase_price() {
        return $this->_purchase_price;
    }
    
    public function setVariant_price($price) {
        $this->_price = $price;
    }

    public function getVariant_price() {
        return $this->_price;
    }
    
    public function setVariant_is_active($is_active) {
        $this->_is_active = $is_active;
    }

    public function getVariant_is_active() {
        return $this->_is_active;
    }
    
    public function setVariant_product_id($product_id) {
        $this->_product_id = $product_id;
    }

    public function getVariant_product_id() {
        return $this->_product_id;
    }
   
    
    /*
     * mimo DB
     */
    
     public function setproductAndVariant($productAndVariant) {
        $this->_productAndVariant = $productAndVariant;
    }

    public function getproductAndVariant() {
        return $this->_productAndVariant;
    }
    
}
