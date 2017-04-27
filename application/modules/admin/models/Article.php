<?php

class Admin_Model_Article extends Model_Model {
    
    protected $_id;
    protected $_name;
    protected $_insert_date;
    protected $_active_from_date;
    protected $_url;
    protected $_seo_title;
    protected $_seo_meta_description;
    protected $_perex;
    protected $_text;
    protected $_is_active;
    protected $_article_type_id;
    
    protected $_photography;

    public function setArticle_id($id) {
        $this->_id = (int) $id;
    }

    public function getArticle_id() {
        return $this->_id;
    }

    public function setArticle_name($name) {
        $this->_name = $name;
    }

    public function getArticle_name() {
        return $this->_name;
    }
    
    public function setArticle_insert_date($insert_date) {
        $this->_insert_date = $insert_date;
    }

    public function getArticle_insert_date() {
        return $this->_insert_date;
    }
    
    public function setArticle_active_from_date($active_from_date) {
        $this->_active_from_date = $active_from_date;
    }

    public function getArticle_active_from_date() {
        return $this->_active_from_date;
    }
    
    public function setArticle_url($url) {
        $this->_url = $url;
    }

    public function getArticle_url() {
        return $this->_url;
    }
   
     public function setArticle_seo_title($seo_title) {
        $this->_seo_title = $seo_title;
    }

    public function getArticle_seo_title() {
        return $this->_seo_title;
    }
    
    public function setArticle_seo_meta_description($seo_meta_description) {
        $this->_seo_meta_description = $seo_meta_description;
    }

    public function getArticle_seo_meta_description() {
        return $this->_seo_meta_description;
    }
    
    public function setArticle_perex($perex) {
        $this->_perex = $perex;
    }

    public function getArticle_perex() {
        return $this->_perex;
    }
    
    public function setArticle_text($text) {
        $this->_text = $text;
    }

    public function getArticle_text() {
        return $this->_text;
    }
    
    public function setArticle_is_active($is_active) {
        $this->_is_active = $is_active;
    }

    public function getArticle_is_active() {
        return $this->_is_active;
    }
    
    public function setArticle_article_type_id($article_type_id) {
        $this->_article_type_id = $article_type_id;
    }

    public function getArticle_article_type_id() {
        return $this->_article_type_id;
    }
    
    
    
    
    public function setArticle_photography(Admin_Model_Photography $photography) {
        $this->_photography = $photography;
    }

    public function getArticle_photography() {
        return $this->_photography;
    }

}
