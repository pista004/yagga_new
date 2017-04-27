<?php

class Admin_Model_ProductRecommend extends Model_Model {

    protected $_id;
    protected $_product_id;

    public function setProduct_recommend_id($id) {
        $this->_id = (int) $id;
    }

    public function getProduct_recommend_id() {
        return $this->_id;
    }

    public function setProduct_recommend_product_id($product_id) {
        $this->_product_id = (int) $product_id;
    }

    public function getProduct_recommend_product_id() {
        return $this->_product_id;
    }

}
