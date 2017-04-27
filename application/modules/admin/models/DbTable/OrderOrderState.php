<?php

/**
 * This is the DbTable class for the guestbook table.
 */
class Admin_Model_DbTable_OrderOrderState extends Zend_Db_Table_Abstract {

    /** Table name */
    protected $_name = "order_order_state";
    protected $_primary = array("order_order_state_order_id", "order_order_state_order_state_id");

}