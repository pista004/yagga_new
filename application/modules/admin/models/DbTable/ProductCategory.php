<?php

/**
 * This is the DbTable class for the guestbook table.
 */
class Admin_Model_DbTable_ProductCategory extends Zend_Db_Table_Abstract {

    /** Table name */
    protected $_name = "product_category";
    protected $_primary = array("product_category_product_id", "product_category_category_id");

}