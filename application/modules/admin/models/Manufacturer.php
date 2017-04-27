<?php

class Admin_Model_Manufacturer extends Model_Model {

    protected $_id;
    protected $_name;
    protected $_url;
    protected $_note;
    protected $_seo_title;
    protected $_seo_meta_description;
    protected $_is_active;
    protected $_photography;

    public function setManufacturer_id($id) {
        $this->_id = (int) $id;
    }

    public function getManufacturer_id() {
        return $this->_id;
    }

    public function setManufacturer_name($name) {
        $this->_name = $name;
    }

    public function getManufacturer_name() {
        return $this->_name;
    }

    public function setManufacturer_url($url) {
        $this->_url = $url;
    }

    public function getManufacturer_url() {
        return $this->_url;
    }

    public function setManufacturer_note($note) {
        $this->_note = $note;
    }

    public function getManufacturer_note() {
        return $this->_note;
    }

    public function setManufacturer_seo_title($seo_title) {
        $this->_seo_title = $seo_title;
    }

    public function getManufacturer_seo_title() {
        return $this->_seo_title;
    }

    public function setManufacturer_seo_meta_description($seo_meta_description) {
        $this->_seo_meta_description = $seo_meta_description;
    }

    public function getManufacturer_seo_meta_description() {
        return $this->_seo_meta_description;
    }

    public function setManufacturer_is_active($is_active) {
        $this->_is_active = $is_active;
    }

    public function getManufacturer_is_active() {
        return $this->_is_active;
    }

    
    
    
    public function setManufacturer_photography(Admin_Model_Photography $photography) {
        $this->_photography = $photography;
    }

    public function getManufacturer_photography() {
        return $this->_photography;
    }

}
