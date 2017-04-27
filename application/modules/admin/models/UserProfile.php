<?php

class Admin_Model_UserProfile extends Model_Model {

    protected $_id;
    protected $_phone;
    protected $_i_name;
    protected $_i_surname;
    protected $_i_street;
    protected $_i_city;
    protected $_i_zip_code;
    protected $_i_country_id;
    protected $_i_company;
    protected $_i_ico;
    protected $_i_dic;
    protected $_d_name;
    protected $_d_surname;
    protected $_d_company;
    protected $_d_street;
    protected $_d_city;
    protected $_d_zip_code;
    protected $_d_country_id;
    protected $_admin_note;
    protected $_user_id;
    protected $_user;

    public function setUser_profile_id($id) {
        $this->_id = (int) $id;
    }

    public function getUser_profile_id() {
        return $this->_id;
    }

    public function setUser_profile_phone($phone) {
        $this->_phone = $phone;
    }

    public function getUser_profile_phone() {
        return $this->_phone;
    }

    public function setUser_profile_i_name($i_name) {
        $this->_i_name = $i_name;
    }

    public function getUser_profile_i_name() {
        return $this->_i_name;
    }

    public function setUser_profile_i_surname($i_surname) {
        $this->_i_surname = $i_surname;
    }

    public function getUser_profile_i_surname() {
        return $this->_i_surname;
    }

    public function setUser_profile_i_street($i_street) {
        $this->_i_street = $i_street;
    }

    public function getUser_profile_i_street() {
        return $this->_i_street;
    }

    public function setUser_profile_i_city($i_city) {
        $this->_i_city = $i_city;
    }

    public function getUser_profile_i_city() {
        return $this->_i_city;
    }

    public function setUser_profile_i_zip_code($i_zip_code) {
        $this->_i_zip_code = $i_zip_code;
    }

    public function getUser_profile_i_zip_code() {
        return $this->_i_zip_code;
    }

    public function setUser_profile_i_country_id($i_country_id) {
        $this->_i_country_id = $i_country_id;
    }

    public function getUser_profile_i_country_id() {
        return $this->_i_country_id;
    }

    public function setUser_profile_i_company($i_company) {
        $this->_i_company = $i_company;
    }

    public function getUser_profile_i_company() {
        return $this->_i_company;
    }

    public function setUser_profile_i_ico($i_ico) {
        $this->_i_ico = $i_ico;
    }

    public function getUser_profile_i_ico() {
        return $this->_i_ico;
    }

    public function setUser_profile_i_dic($i_dic) {
        $this->_i_dic = $i_dic;
    }

    public function getUser_profile_i_dic() {
        return $this->_i_dic;
    }

    public function setUser_profile_d_name($d_name) {
        $this->_d_name = $d_name;
    }

    public function getUser_profile_d_name() {
        return $this->_d_name;
    }

    public function setUser_profile_d_surname($d_surname) {
        $this->_d_surname = $d_surname;
    }

    public function getUser_profile_d_surname() {
        return $this->_d_surname;
    }

    public function setUser_profile_d_company($d_company) {
        $this->_d_company = $d_company;
    }

    public function getUser_profile_d_company() {
        return $this->_d_company;
    }

    public function setUser_profile_d_street($d_street) {
        $this->_d_street = $d_street;
    }

    public function getUser_profile_d_street() {
        return $this->_d_street;
    }

    public function setUser_profile_d_city($d_city) {
        $this->_d_city = $d_city;
    }

    public function getUser_profile_d_city() {
        return $this->_d_city;
    }

    public function setUser_profile_d_zip_code($d_zip_code) {
        $this->_d_zip_code = $d_zip_code;
    }

    public function getUser_profile_d_zip_code() {
        return $this->_d_zip_code;
    }

    public function setUser_profile_d_country_id($d_country_id) {
        $this->_d_country_id = $d_country_id;
    }

    public function getUser_profile_d_country_id() {
        return $this->_d_country_id;
    }

    public function setUser_profile_admin_note($admin_note) {
        $this->_admin_note = $admin_note;
    }

    public function getUser_profile_admin_note() {
        return $this->_admin_note;
    }

    public function setUser_profile_user_id($user_id) {
        $this->_user_id = $user_id;
    }

    public function getUser_profile_user_id() {
        return $this->_user_id;
    }

    public function setUser_profile_user(Admin_Model_User $user) {
        $this->_user = $user;
    }

    public function getUser_profile_user() {
        return $this->_user;
    }

}
