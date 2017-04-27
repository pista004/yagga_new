<?php

class Admin_Model_ArticleType extends Model_Model {
    
    protected $_id;
    protected $_name;

    public function setArticle_type_id($id) {
        $this->_id = (int) $id;
    }

    public function getArticle_type_id() {
        return $this->_id;
    }

    public function setArticle_type_name($name) {
        $this->_name = $name;
    }

    public function getArticle_type_name() {
        return $this->_name;
    }

}
