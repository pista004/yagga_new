<?php

/**
 * This is the DbTable class for the guestbook table.
 */
class Admin_Model_DbTable_Admin extends Zend_Db_Table_Abstract {

    /** Table name */
    protected $_name = "admin";
    protected $_primary = "admin_id";

}