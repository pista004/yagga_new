<?php

class Admin_Model_OrderState extends Model_Model {
    
    protected $_id;
    protected $_name;
    protected $_color;
    protected $_text;

    public function setOrder_state_id($id) {
        $this->_id = (int) $id;
    }

    public function getOrder_state_id() {
        return $this->_id;
    }

    public function setOrder_state_name($name) {
        $this->_name = $name;
    }

    public function getOrder_state_name() {
        return $this->_name;
    }
    
    public function setOrder_state_color($color) {
        $this->_color = $color;
    }

    public function getOrder_state_color() {
        return $this->_color;
    }
    
    public function setOrder_state_text($text) {
        $this->_text = $text;
    }

    public function getOrder_state_text() {
        return $this->_text;
    }
    
}
