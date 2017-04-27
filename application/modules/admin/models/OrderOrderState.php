<?php

class Admin_Model_OrderOrderState extends Model_Model {

    protected $_order_id;
    protected $_order_state_id;
    protected $_date;
    protected $_order;
    protected $_order_state;

    public function setOrder_order_state_order_id($order_id) {
        $this->_order_id = (int) $order_id;
    }

    public function getOrder_order_state_order_id() {
        return $this->_order_id;
    }

    public function setOrder_order_state_order_state_id($order_state_id) {
        $this->_order_state_id = (int) $order_state_id;
    }

    public function getOrder_order_state_order_state_id() {
        return $this->_order_state_id;
    }

    public function setOrder_order_state_date($date) {
        $this->_date = $date;
    }

    public function getOrder_order_state_date() {
        return $this->_date;
    }
    
    
    
    public function setOrder(Admin_Model_Order $order) {
        $this->_order = $order;
    }

    public function getOrder() {
        return $this->_order;
    }
    
    public function setOrder_state(Admin_Model_OrderState $orderState) {
        $this->_order_state = $orderState;
    }

    public function getOrder_state() {
        return $this->_order_state;
    }
}
