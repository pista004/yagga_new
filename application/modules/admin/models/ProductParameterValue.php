<?php

class Admin_Model_ProductParameterValue extends Model_Model {

    protected $_product_id;
    protected $_parameter_id;
    protected $_value;
    protected $_value_bool;
    protected $_parameter_dial_id;

    public function setProduct_parameter_value_product_id($product_id) {
        $this->_product_id = (int) $product_id;
    }

    public function getProduct_parameter_value_product_id() {
        return $this->_product_id;
    }

    public function setProduct_parameter_value_parameter_id($parameter_id) {
        $this->_parameter_id = (int) $parameter_id;
    }

    public function getProduct_parameter_value_parameter_id() {
        return $this->_parameter_id;
    }

    public function setProduct_parameter_value_value($value) {
        $this->_value = $value;
    }

    public function getProduct_parameter_value_value() {
        return $this->_value;
    }

    public function setProduct_parameter_value_value_bool($value_bool) {
        $this->_value_bool = $value_bool;
    }

    public function getProduct_parameter_value_value_bool() {
        return $this->_value_bool;
    }

    public function setProduct_parameter_value_parameter_dial_id($parameter_dial_id) {
        $this->_parameter_dial_id = $parameter_dial_id;
    }

    public function getProduct_parameter_value_parameter_dial_id() {
        return $this->_parameter_dial_id;
    }

}
