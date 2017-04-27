<?php

/**
 * This is the DbTable class for the guestbook table.
 */
class Admin_Model_DbTable_Page extends Zend_Db_Table_Abstract {

    /** Table name */
    protected $_name = "page";
    protected $_primary = "page_id";

}