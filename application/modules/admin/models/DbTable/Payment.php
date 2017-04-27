<?php

/**
 * This is the DbTable class for the guestbook table.
 */
class Admin_Model_DbTable_Payment extends Zend_Db_Table_Abstract {

    /** Table name */
    protected $_name = "payment";
    protected $_primary = "payment_id";

}