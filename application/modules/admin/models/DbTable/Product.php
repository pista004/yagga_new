<?php

/**
 * This is the DbTable class for the guestbook table.
 */
class Admin_Model_DbTable_Product extends Zend_Db_Table_Abstract {

    /** Table name */
    protected $_name = "product";
    protected $_primary = "product_id";

}