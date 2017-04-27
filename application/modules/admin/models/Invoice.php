<?php

class Admin_Model_Invoice extends Model_Model {
    
    protected $_id;
    protected $_number;
    protected $_path;
    protected $_creating_date;
    protected $_due_date;
    protected $_order_id;
    protected $_is_sent;

    public function setInvoice_id($id) {
        $this->_id = (int) $id;
    }

    public function getInvoice_id() {
        return $this->_id;
    }
    
    public function setInvoice_number($number) {
        $this->_number = $number;
    }

    public function getInvoice_number() {
        return $this->_number;
    }
    
    public function setInvoice_path($path) {
        $this->_path = $path;
    }

    public function getInvoice_path() {
        return $this->_path;
    }
    
    public function setInvoice_creating_date($creating_date) {
        $this->_creating_date = $creating_date;
    }

    public function getInvoice_creating_date() {
        return $this->_creating_date;
    }
    
    public function setInvoice_due_date($due_date) {
        $this->_due_date = $due_date;
    }

    public function getInvoice_due_date() {
        return $this->_due_date;
    }
    
    public function setInvoice_order_id($order_id) {
        $this->_order_id = $order_id;
    }

    public function getInvoice_order_id() {
        return $this->_order_id;
    }
    
    public function setInvoice_is_sent($is_sent) {
        $this->_is_sent = $is_sent;
    }

    public function getInvoice_is_sent() {
        return $this->_is_sent;
    }

}
