<?php

class Admin_Model_ParameterType extends Model_Model {

    protected $_type = array(
        1 => 'Hodnota',
        2 => 'Číselník',
        3 => 'Ano/ne'
    );
    protected $_id;
    protected $_name;

    const VALUE_BOOL = array(null => '--Vyberte--', 1 => 'Ano', 0 => 'Ne');

    public function getParameter_type() {
        return $this->_type;
    }

    public function setParameter_type_id($id) {
        $this->_id = $id;
        $this->setParameter_type_name($id);
    }

    public function getParameter_type_id() {
        return $this->_id;
    }

    public function setParameter_type_name($type_id) {
        return $this->_name = $this->_type[$type_id];
    }

    public function getParameter_type_name() {
        return $this->_name;
    }

}
