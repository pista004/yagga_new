<?php

/**
 * This is the DbTable class for the guestbook table.
 */
class Admin_Model_DbTable_Order extends Zend_Db_Table_Abstract {

    /** Table name */
    protected $_name = "order";
    protected $_primary = "order_id";

}