<?php

class Default_Model_Photography extends Model_Model {
    
    protected $_id;
    protected $_path;
    protected $_note;
    protected $_is_main;
    protected $_product_id;

    public function setPhotography_id($id) {
        $this->_id = (int) $id;
    }

    public function getPhotography_id() {
        return $this->_id;
    }

    public function setPhotography_path($path) {
        $this->_path = $path;
    }

    public function getPhotography_path() {
        return $this->_path;
    }
    
    public function setPhotography_note($note) {
        $this->_note = $note;
    }

    public function getPhotography_note() {
        return $this->_note;
    }
    
    public function setPhotography_is_main($is_main) {
        $this->_is_main = $is_main;
    }

    public function getPhotography_is_main() {
        return $this->_is_main;
    }

    public function setPhotography_product_id($product_id) {
        $this->_product_id = $product_id;
    }

    public function getPhotography_product_id() {
        return $this->_product_id;
    }
    
}
