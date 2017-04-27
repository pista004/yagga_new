<?php

/**
 * This is the DbTable class for the guestbook table.
 */
class Admin_Model_DbTable_Parameter extends Zend_Db_Table_Abstract {

    /** Table name */
    protected $_name = "parameter";
    protected $_primary = "parameter_id";

}