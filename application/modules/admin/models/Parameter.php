<?php

class Admin_Model_Parameter extends Model_Model {

    protected $_id;
    protected $_name;
    protected $_note;
    protected $_type;
    protected $_parameter_unit_id;
    protected $_parameter_unit;
    protected $_parameter_type;
    protected $_product_parameter_value;

    public function setParameter_id($id) {
        $this->_id = (int) $id;
    }

    public function getParameter_id() {
        return $this->_id;
    }

    public function setParameter_name($name) {
        $this->_name = $name;
    }

    public function getParameter_name() {
        return $this->_name;
    }

    public function setParameter_note($note) {
        $this->_note = $note;
    }

    public function getParameter_note() {
        return $this->_note;
    }

    public function setParameter_type($type) {
        $this->_type = $type;
    }

    public function getParameter_type() {
        return $this->_type;
    }

    public function setParameter_parameter_unit_id($parameter_unit_id) {
        $this->_parameter_unit_id = $parameter_unit_id;
    }

    public function getParameter_parameter_unit_id() {
        return $this->_parameter_unit_id;
    }

    public function setParameter_type_obj(Admin_Model_ParameterType $parameter_type) {
        $this->_parameter_type = $parameter_type;
    }

    public function getParameter_type_obj() {
        return $this->_parameter_type;
    }

    public function setParameter_unit(Admin_Model_ParameterUnit $parameter_unit) {
        $this->_parameter_unit = $parameter_unit;
    }

    public function getParameter_unit() {
        return $this->_parameter_unit;
    }

    public function setProduct_parameter_value(Admin_Model_ProductParameterValue $product_parameter_value) {
        $this->_product_parameter_value = $product_parameter_value;
    }

    public function getProduct_parameter_value() {
        return $this->_product_parameter_value;
    }

}
