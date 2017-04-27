<?php

class Default_Model_Order extends Model_Model {

    protected $_id;
    protected $_number;
    protected $_date;
    protected $_d_name;
    protected $_d_surname;
    protected $_d_company;
    protected $_d_street;
    protected $_d_city;
    protected $_d_zip_code;
    protected $_d_country_id;
    protected $_email;
    protected $_phone;
    protected $_i_name;
    protected $_i_surname;
    protected $_i_company;
    protected $_i_street;
    protected $_i_city;
    protected $_i_zip_code;
    protected $_i_country_id;
    protected $_i_ico;
    protected $_i_dic;
    protected $_note;
    protected $_admin_note;
    protected $_order_state_id;
    protected $_order_state;
    protected $_delivery_id;
    protected $_payment_id;
    protected $_delivery_name;
    protected $_payment_name;
    protected $_delivery_price;
    protected $_payment_price;
    protected $_newsletter;
    protected $_ip_address;
    protected $_invoice;
    
    protected $_order_sum;
    protected $_order_sum_with_delivery_payment;
    
    public function setOrder_id($id) {
        $this->_id = (int) $id;
    }

    public function getOrder_id() {
        return $this->_id;
    }

    public function setOrder_number($number) {
        $this->_number = $number;
    }

    public function getOrder_number() {
        return $this->_number;
    }
    
    public function setOrder_date($date) {
        $this->_date = $date;
    }

    public function getOrder_date() {
        return $this->_date;
    }

    public function setOrder_d_name($d_name) {
        $this->_d_name = $d_name;
    }

    public function getOrder_d_name() {
        return $this->_d_name;
    }

    public function setOrder_d_surname($d_surname) {
        $this->_d_surname = $d_surname;
    }

    public function getOrder_d_surname() {
        return $this->_d_surname;
    }
    
    public function setOrder_d_company($d_company) {
        $this->_d_company = $d_company;
    }

    public function getOrder_d_company() {
        return $this->_d_company;
    }

    public function setOrder_d_street($d_street) {
        $this->_d_street = $d_street;
    }

    public function getOrder_d_street() {
        return $this->_d_street;
    }

    public function setOrder_d_city($d_city) {
        $this->_d_city = $d_city;
    }

    public function getOrder_d_city() {
        return $this->_d_city;
    }

    public function setOrder_d_zip_code($d_zip_code) {
        $this->_d_zip_code = $d_zip_code;
    }

    public function getOrder_d_zip_code() {
        return $this->_d_zip_code;
    }

    public function setOrder_d_country_id($d_country_id) {
        $this->_d_country_id = $d_country_id;
    }

    public function getOrder_d_country_id() {
        return $this->_d_country_id;
    }
    
    public function setOrder_email($email) {
        $this->_email = $email;
    }

    public function getOrder_email() {
        return $this->_email;
    }

    public function setOrder_phone($phone) {
        $this->_phone = $phone;
    }

    public function getOrder_phone() {
        return $this->_phone;
    }

    public function setOrder_i_name($i_name) {
        $this->_i_name = $i_name;
    }

    public function getOrder_i_name() {
        return $this->_i_name;
    }

    public function setOrder_i_surname($i_surname) {
        $this->_i_surname = $i_surname;
    }

    public function getOrder_i_surname() {
        return $this->_i_surname;
    }

    public function setOrder_i_company($i_company) {
        $this->_i_company = $i_company;
    }

    public function getOrder_i_company() {
        return $this->_i_company;
    }

    public function setOrder_i_street($i_street) {
        $this->_i_street = $i_street;
    }

    public function getOrder_i_street() {
        return $this->_i_street;
    }
    
    public function setOrder_i_city($i_city) {
        $this->_i_city = $i_city;
    }

    public function getOrder_i_city() {
        return $this->_i_city;
    }

    public function setOrder_i_zip_code($i_zip_code) {
        $this->_i_zip_code = $i_zip_code;
    }

    public function getOrder_i_zip_code() {
        return $this->_i_zip_code;
    }
    
    public function setOrder_i_country_id($i_country_id) {
        $this->_i_country_id = $i_country_id;
    }

    public function getOrder_i_country_id() {
        return $this->_i_country_id;
    }

    public function setOrder_i_ico($i_ico) {
        $this->_i_ico = $i_ico;
    }

    public function getOrder_i_ico() {
        return $this->_i_ico;
    }

    public function setOrder_i_dic($i_dic) {
        $this->_i_dic = $i_dic;
    }

    public function getOrder_i_dic() {
        return $this->_i_dic;
    }

    public function setOrder_note($note) {
        $this->_note = $note;
    }

    public function getOrder_note() {
        return $this->_note;
    }

    public function setOrder_admin_note($admin_note) {
        $this->_admin_note = $admin_note;
    }

    public function getOrder_admin_note() {
        return $this->_admin_note;
    }
    
    public function setOrder_order_state_id($order_state_id) {
        $this->_order_state_id = $order_state_id;
    }

    public function getOrder_order_state_id() {
        return $this->_order_state_id;
    }
    
    public function setOrder_state(Admin_Model_OrderState $order_state) {
        $this->_order_state = $order_state;
    }

    public function getOrder_state() {
        return $this->_order_state;
    }

    
    public function setOrder_delivery_id($delivery_id) {
        $this->_delivery_id = $delivery_id;
    }

    public function getOrder_delivery_id() {
        return $this->_delivery_id;
    }
    
    public function setOrder_payment_id($payment_id) {
        $this->_payment_id = $payment_id;
    }

    public function getOrder_payment_id() {
        return $this->_payment_id;
    }
    
    public function setOrder_delivery_price($delivery_price) {
        $this->_delivery_price = $delivery_price;
    }

    public function getOrder_delivery_price() {
        return $this->_delivery_price;
    }
    
    public function setOrder_payment_price($payment_price) {
        $this->_payment_price = $payment_price;
    }

    public function getOrder_payment_price() {
        return $this->_payment_price;
    }
    
    public function setOrder_delivery_name($delivery_name) {
        $this->_delivery_name = $delivery_name;
    }

    public function getOrder_delivery_name() {
        return $this->_delivery_name;
    }
    
    public function setOrder_payment_name($payment_name) {
        $this->_payment_name = $payment_name;
    }

    public function getOrder_payment_name() {
        return $this->_payment_name;
    }
    
    public function setOrder_newsletter($newsletter) {
        $this->_newsletter = $newsletter;
    }

    public function getOrder_newsletter() {
        return $this->_newsletter;
    }
    
    public function setOrder_ip_address($ip_address) {
        $this->_ip_address = $ip_address;
    }

    public function getOrder_ip_address() {
        return $this->_ip_address;
    }
    
    public function setOrder_invoice(Admin_Model_Invoice $invoice) {
        $this->_invoice = $invoice;
    }

    public function getOrder_invoice() {
        return $this->_invoice;
    }
    
    /*
     * 
     * parametry mimo DB
     * 
     */

    public function setOrder_sum($order_sum) {
        $this->_order_sum = $order_sum;
    }

    public function getOrder_sum() {
        return $this->_order_sum;
    }
    
    public function setOrder_sum_with_delivery_payment($order_sum_with_delivery_payment) {
        $this->_order_sum_with_delivery_payment = $order_sum_with_delivery_payment;
    }

    public function getOrder_sum_with_delivery_payment() {
        return $this->_order_sum_with_delivery_payment;
    }

}
