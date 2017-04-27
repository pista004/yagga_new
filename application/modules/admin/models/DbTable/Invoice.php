<?php

/**
 * This is the DbTable class for the guestbook table.
 */
class Admin_Model_DbTable_Invoice extends Zend_Db_Table_Abstract {

    /** Table name */
    protected $_name = "invoice";
    protected $_primary = "invoice_id";

}