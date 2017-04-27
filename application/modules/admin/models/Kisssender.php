<?php

class Admin_Model_Kisssender extends Model_Model {
    
    protected $_id;
    protected $_hash;
    protected $_send_date;
    protected $_sender_name;
    protected $_email_to;
    protected $_email_from;
    protected $_text;
    protected $_ip_address;
    protected $_latitude;
    protected $_longitude;
    
    public function setKisssender_id($id) {
        $this->_id = (int) $id;
    }

    public function getKisssender_id() {
        return $this->_id;
    }

    public function setKisssender_hash($hash) {
        $this->_hash = $hash;
    }

    public function getKisssender_hash() {
        return $this->_hash;
    }
    
    public function setKisssender_send_date($send_date) {
        $this->_send_date = $send_date;
    }

    public function getKisssender_send_date() {
        return $this->_send_date;
    }
    
    public function setKisssender_sender_name($sender_name) {
        $this->_sender_name = $sender_name;
    }

    public function getKisssender_sender_name() {
        return $this->_sender_name;
    }
    
    public function setKisssender_email_to($email_to) {
        $this->_email_to = $email_to;
    }

    public function getKisssender_email_to() {
        return $this->_email_to;
    }
    
    public function setKisssender_email_from($email_from) {
        $this->_email_from = $email_from;
    }

    public function getKisssender_email_from() {
        return $this->_email_from;
    }
    
    public function setKisssender_text($text) {
        $this->_text = $text;
    }

    public function getKisssender_text() {
        return $this->_text;
    }
    
    public function setKisssender_ip_address($ip_address) {
        $this->_ip_address = $ip_address;
    }

    public function getKisssender_ip_address() {
        return $this->_ip_address;
    }
    
    public function setKisssender_latitude($latitude) {
        $this->_latitude = $latitude;
    }

    public function getKisssender_latitude() {
        return $this->_latitude;
    }
   
    public function setKisssender_longitude($longitude) {
        $this->_longitude = $longitude;
    }

    public function getKisssender_longitude() {
        return $this->_longitude;
    }
    
}
