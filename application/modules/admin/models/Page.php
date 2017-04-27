<?php

class Admin_Model_Page extends Model_Model {
    
    protected $_id;
    protected $_name;
    protected $_url;
    protected $_seo_title;
    protected $_seo_meta_description;
    protected $_text;
    protected $_is_active;

    public function setPage_id($id) {
        $this->_id = (int) $id;
    }

    public function getPage_id() {
        return $this->_id;
    }

    public function setPage_name($name) {
        $this->_name = $name;
    }

    public function getPage_name() {
        return $this->_name;
    }
    
    public function setPage_url($url) {
        $this->_url = $url;
    }

    public function getPage_url() {
        return $this->_url;
    }
   
     public function setPage_seo_title($seo_title) {
        $this->_seo_title = $seo_title;
    }

    public function getPage_seo_title() {
        return $this->_seo_title;
    }
    
    public function setPage_seo_meta_description($seo_meta_description) {
        $this->_seo_meta_description = $seo_meta_description;
    }

    public function getPage_seo_meta_description() {
        return $this->_seo_meta_description;
    }
    
    public function setPage_text($text) {
        $this->_text = $text;
    }

    public function getPage_text() {
        return $this->_text;
    }
    
    public function setPage_is_active($is_active) {
        $this->_is_active = $is_active;
    }

    public function getPage_is_active() {
        return $this->_is_active;
    }
    

}
