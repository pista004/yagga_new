<?php

/**
 * This is the DbTable class for the guestbook table.
 */
class Admin_Model_DbTable_Category extends Zend_Db_Table_Abstract {

    /** Table name */
    protected $_name = "category";
    protected $_primary = "category_id";

}