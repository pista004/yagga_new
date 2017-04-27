<?php

class Default_Model_Category extends Model_Model {

    protected $_id;
    protected $_name;
    protected $_description;
    protected $_parent;
    protected $_url;
    protected $_h1;
    protected $_seo_title;
    protected $_seo_meta_description;
    protected $_category_heureka;
    protected $_is_active;
    
    //parametry mimo DB
    protected $_structure;
    protected $_childs;
    protected $_level;

    public function setCategory_id($id) {
        $this->_id = (int) $id;
    }

    public function getCategory_id() {
        return $this->_id;
    }

    public function setCategory_name($name) {
        $this->_name = $name;
    }

    public function getCategory_name() {
        return $this->_name;
    }

    public function setCategory_description($description) {
        $this->_description = $description;
    }

    public function getCategory_description() {
        return $this->_description;
    }

    public function setCategory_parent($parent) {
        $this->_parent = $parent;
    }

    public function getCategory_parent() {
        return $this->_parent;
    }

    public function setCategory_url($url) {
        $this->_url = $url;
    }

    public function getCategory_url() {
        return $this->_url;
    }

    public function setCategory_h1($h1) {
        $this->_h1 = $h1;
    }

    public function getCategory_h1() {
        return $this->_h1;
    }
    
    public function setCategory_seo_title($seo_title) {
        $this->_seo_title = $seo_title;
    }

    public function getCategory_seo_title() {
        return $this->_seo_title;
    }

    public function setCategory_seo_meta_description($seo_meta_description) {
        $this->_seo_meta_description = $seo_meta_description;
    }

    public function getCategory_seo_meta_description() {
        return $this->_seo_meta_description;
    }
    
    public function setCategory_category_heureka($category_heureka) {
        $this->_category_heureka = $category_heureka;
    }

    public function getCategory_category_heureka() {
        return $this->_category_heureka;
    }
    

    public function setCategory_is_active($is_active) {
        $this->_is_active = $is_active;
    }

    public function getCategory_is_active() {
        return $this->_is_active;
    }

    
    
    //parametry mimo DB
    public function setCategory_structure($structure) {
        $this->_structure = $structure;
    }

    public function getCategory_structure() {
        return $this->_structure;
    }
    
    public function setCategory_childs(array $childs) {
        $this->_childs = $childs;
    }

    public function getCategory_childs() {
        return $this->_childs;
    }
    
    public function setCategory_level($level) {
        $this->_level = $level;
    }

    public function getCategory_level() {
        return $this->_level;
    }
}
