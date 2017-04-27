<?php

class Admin_Model_ParameterDial extends Model_Model {

    protected $_id;
    protected $_value;
    protected $_parameter_id;

    public function setParameter_dial_id($id) {
        $this->_id = (int) $id;
    }

    public function getParameter_dial_id() {
        return $this->_id;
    }

    public function setParameter_dial_value($value) {
        $this->_value = $value;
    }

    public function getParameter_dial_value() {
        return $this->_value;
    }
    
    public function setParameter_dial_parameter_id($parameter_id) {
        $this->_parameter_id = $parameter_id;
    }

    public function getParameter_dial_parameter_id() {
        return $this->_parameter_id;
    }
    

}
