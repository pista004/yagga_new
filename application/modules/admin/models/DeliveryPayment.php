<?php

class Admin_Model_DeliveryPayment extends Model_Model {

    protected $_delivery_id;
    protected $_payment_id;

    public function setDelivery_payment_delivery_id($delivery_id) {
        $this->_delivery_id = (int) $delivery_id;
    }

    public function getDelivery_payment_delivery_id() {
        return $this->_delivery_id;
    }

    public function setDelivery_payment_payment_id($payment_id) {
        $this->_payment_id = (int) $payment_id;
    }

    public function getDelivery_payment_payment_id() {
        return $this->_payment_id;
    }

}
