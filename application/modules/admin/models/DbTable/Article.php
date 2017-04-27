<?php

/**
 * This is the DbTable class for the guestbook table.
 */
class Admin_Model_DbTable_Article extends Zend_Db_Table_Abstract {

    /** Table name */
    protected $_name = "article";
    protected $_primary = "article_id";

}