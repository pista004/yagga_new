<?php

class Admin_Model_ProductCategory extends Model_Model {
    
    protected $_product_id;
    protected $_category_id;

    public function setProduct_category_product_id($product_id) {
        $this->_product_id = (int) $product_id;
    }

    public function getProduct_category_product_id() {
        return $this->_product_id;
    }

    public function setProduct_category_category_id($category_id) {
        $this->_category_id = (int)$category_id;
    }

    public function getProduct_category_category_id() {
        return $this->_category_id;
    }
    
}
