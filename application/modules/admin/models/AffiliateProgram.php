<?php

class Admin_Model_AffiliateProgram extends Model_Model {

    protected $_id;
    protected $_name;
    protected $_title;
    protected $_is_ready;

    public function setAffiliate_program_id($id) {
        $this->_id = (int) $id;
    }

    public function getAffiliate_program_id() {
        return $this->_id;
    }

    public function setAffiliate_program_name($name) {
        $this->_name = $name;
    }

    public function getAffiliate_program_name() {
        return $this->_name;
    }
    
    public function setAffiliate_program_title($title) {
        $this->_title = $title;
    }

    public function getAffiliate_program_title() {
        return $this->_title;
    }
    
    public function setAffiliate_program_is_ready($is_ready) {
        $this->_is_ready = $is_ready;
    }

    public function getAffiliate_program_is_ready() {
        return $this->_is_ready;
    }

}
