<?php

class Admin_Model_ParameterCategory extends Model_Model {
    
    protected $_parameter_id;
    protected $_category_id;

    public function setParameter_category_parameter_id($parameter_id) {
        $this->_parameter_id = (int) $parameter_id;
    }

    public function getParameter_category_parameter_id() {
        return $this->_parameter_id;
    }

    public function setParameter_category_category_id($category_id) {
        $this->_category_id = (int)$category_id;
    }

    public function getParameter_category_category_id() {
        return $this->_category_id;
    }
    
}
