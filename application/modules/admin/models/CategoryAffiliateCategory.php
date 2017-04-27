<?php

class Admin_Model_CategoryAffiliateCategory extends Model_Model {

    protected $_id;
    protected $_name;
    protected $_category_id;

    protected $_category;
    
    public function setCategory_affiliate_category_id($id) {
        $this->_id = (int) $id;
    }

    public function getCategory_affiliate_category_id() {
        return $this->_id;
    }

    public function setCategory_affiliate_category_name($name) {
        $this->_name = $name;
    }

    public function getCategory_affiliate_category_name() {
        return $this->_name;
    }
    
    public function setCategory_affiliate_category_category_id($category_id) {
        $this->_category_id = $category_id;
    }

    public function getCategory_affiliate_category_category_id() {
        return $this->_category_id;
    }
    
    public function setCategory($category) {
        $this->_category = $category;
    }

    public function getCategory() {
        return $this->_category;
    }

}
