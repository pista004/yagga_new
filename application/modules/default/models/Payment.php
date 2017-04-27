<?php

class Default_Model_Payment extends Model_Model {

    protected $_id;
    protected $_name;
    protected $_info;
    protected $_note;
    protected $_price_czk;
    protected $_free_shipping_limit;
    protected $_is_active;

    public function setPayment_id($id) {
        $this->_id = (int) $id;
    }

    public function getPayment_id() {
        return $this->_id;
    }
    
    public function setPayment_name($name) {
        $this->_name = $name;
    }

    public function getPayment_name() {
        return $this->_name;
    }
    
    public function setPayment_info($info) {
        $this->_info = $info;
    }

    public function getPayment_info() {
        return $this->_info;
    }
    
    public function setPayment_note($note) {
        $this->_note = $note;
    }

    public function getPayment_note() {
        return $this->_note;
    }
    
    public function setPayment_price_czk($price_czk) {
        $this->_price_czk = $price_czk;
    }

    public function getPayment_price_czk() {
        return $this->_price_czk;
    }
    
    public function setPayment_free_shipping_limit($free_shipping_limit) {
        $this->_free_shipping_limit = $free_shipping_limit;
    }

    public function getPayment_free_shipping_limit() {
        return $this->_free_shipping_limit;
    }
    
    public function setPayment_is_active($is_active) {
        $this->_is_active = $is_active;
    }

    public function getPayment_is_active() {
        return $this->_is_active;
    }

}
