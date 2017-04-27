<?php

class Admin_Model_ParameterUnit extends Model_Model {

    protected $_id;
    protected $_name;
    protected $_shortcut;
    protected $_note;

    public function setParameter_unit_id($id) {
        $this->_id = (int) $id;
    }

    public function getParameter_unit_id() {
        return $this->_id;
    }

    public function setParameter_unit_name($name) {
        $this->_name = $name;
    }

    public function getParameter_unit_name() {
        return $this->_name;
    }
    
    public function setParameter_unit_shortcut($shortcut) {
        $this->_shortcut = $shortcut;
    }

    public function getParameter_unit_shortcut() {
        return $this->_shortcut;
    }
    
    
    public function setParameter_unit_note($note) {
        $this->_note = $note;
    }

    public function getParameter_unit_note() {
        return $this->_note;
    }

}
