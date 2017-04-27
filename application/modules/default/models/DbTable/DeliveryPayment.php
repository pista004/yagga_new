<?php

/**
 * This is the DbTable class for the guestbook table.
 */
class Default_Model_DbTable_DeliveryPayment extends Zend_Db_Table_Abstract {

    /** Table name */
    protected $_name = "delivery_payment";
    protected $_primary = array("delivery_payment_delivery_id", "delivery_payment_payment_id");

}