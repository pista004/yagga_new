<?php

class Admin_Model_Admin extends Model_Model {

    const SALT = '41ac4c3fb66b5d85';
    
    protected $_id;
    protected $_email;
    protected $_password;
    protected $_salt;
    protected $_is_active;

    public function setAdmin_id($id) {
        $this->_id = (int) $id;
    }

    public function getAdmin_id() {
        return $this->_id;
    }

    public function setAdmin_email($email) {
        $this->_email = $email;
    }

    public function getAdmin_email() {
        return $this->_email;
    }
    
    public function setAdmin_password($password) {
        $this->_password = self::SALT.$password;
    }

    public function getAdmin_password() {
        return $this->_password;
    }
    
    public function setAdmin_salt($salt) {
        $this->_salt = $salt;
    }

    public function getAdmin_salt() {
        return $this->_salt;
    }

    public function setAdmin_is_active($is_active) {
        $this->_is_active = $is_active;
    }

    public function getAdmin_is_active() {
        return $this->_is_active;
    }

}
