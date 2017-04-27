<?php

/**
 * This is the DbTable class for the guestbook table.
 */
class Admin_Model_DbTable_Log extends Zend_Db_Table_Abstract {

    /** Table name */
    protected $_name = "log";
    protected $_primary = "log_id";

}