<?php

class Default_Model_AffiliateProgram extends Model_Model {

    protected $_id;
    protected $_name;
    protected $_is_ready;
    protected $_affiliate_url;
    protected $_title;

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

    public function setAffiliate_program_is_ready($isReady) {
        $this->_is_ready = $isReady;
    }

    public function getAffiliate_program_is_ready() {
        return $this->_is_ready;
    }

    public function setAffiliate_program_affiliate_url($affiliateUrl) {
        $this->_affiliate_url = $affiliateUrl;
    }

    public function getAffiliate_program_affiliate_url() {
        return $this->_affiliate_url;
    }

    public function setAffiliate_program_title($title) {
        $this->_title = $title;
    }

    public function getAffiliate_program_title() {
        return $this->_title;
    }


}
