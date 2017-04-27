<?php

class Admin_Model_Delivery extends Model_Model {

    protected $_id;
    protected $_name;
    protected $_info;
    protected $_note;
    protected $_price_czk;
    protected $_free_shipping_limit;
    protected $_is_address;
    protected $_is_active;

    public function setDelivery_id($id) {
        $this->_id = (int) $id;
    }

    public function getDelivery_id() {
        return $this->_id;
    }
    
    public function setDelivery_name($name) {
        $this->_name = $name;
    }

    public function getDelivery_name() {
        return $this->_name;
    }
    
    public function setDelivery_info($info) {
        $this->_info = $info;
    }

    public function getDelivery_info() {
        return $this->_info;
    }
    
    public function setDelivery_note($note) {
        $this->_note = $note;
    }

    public function getDelivery_note() {
        return $this->_note;
    }
    
    public function setDelivery_price_czk($price_czk) {
        $this->_price_czk = $price_czk;
    }

    public function getDelivery_price_czk() {
        return $this->_price_czk;
    }
    
    public function setDelivery_free_shipping_limit($free_shipping_limit) {
        $this->_free_shipping_limit = $free_shipping_limit;
    }

    public function getDelivery_free_shipping_limit() {
        return $this->_free_shipping_limit;
    }
    
    public function setDelivery_is_address($is_address) {
        $this->_is_address = $is_address;
    }

    public function getDelivery_is_address() {
        return $this->_is_address;
    }
    
    public function setDelivery_is_active($is_active) {
        $this->_is_active = $is_active;
    }

    public function getDelivery_is_active() {
        return $this->_is_active;
    }

}
