<?php

/**
 * This is the DbTable class for the guestbook table.
 */
class Admin_Model_DbTable_ProductRecommend extends Zend_Db_Table_Abstract {

    /** Table name */
    protected $_name = "product_recommend";
    protected $_primary = array("product_recommend_id", "product_recommend_product_id");

}