<?php

class Admin_Model_User extends Model_Model {

    protected $_id;
    protected $_login;
    protected $_password;
    protected $_salt;
    protected $_created_date;
    protected $_last_login;
    protected $_newsletter;
    protected $_is_active;

    public function setUser_id($id) {
        $this->_id = (int) $id;
    }

    public function getUser_id() {
        return $this->_id;
    }

    public function setUser_login($login) {
        $this->_login = $login;
    }

    public function getUser_login() {
        return $this->_login;
    }

    public function setUser_password($password) {
        $this->_password = $password;
    }

    public function getUser_password() {
        return $this->_password;
    }

    public function setUser_salt($salt) {
        $this->_salt = $salt;
    }

    public function getUser_salt() {
        return $this->_salt;
    }

    public function setUser_created_date($created_date) {
        $this->_created_date = $created_date;
    }

    public function getUser_created_date() {
        return $this->_created_date;
    }

    public function setUser_last_login($last_login) {
        $this->_last_login = $last_login;
    }

    public function getUser_last_login() {
        return $this->_last_login;
    }

    public function setUser_newsletter($newsletter) {
        $this->_newsletter = $newsletter;
    }

    public function getUser_newsletter() {
        return $this->_newsletter;
    }

    public function setUser_is_active($is_active) {
        $this->_is_active = $is_active;
    }

    public function getUser_is_active() {
        return $this->_is_active;
    }

}
