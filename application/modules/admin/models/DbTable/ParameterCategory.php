<?php

/**
 * This is the DbTable class for the guestbook table.
 */
class Admin_Model_DbTable_ParameterCategory extends Zend_Db_Table_Abstract {

    /** Table name */
    protected $_name = "parameter_category";
    protected $_primary = array("parameter_category_parameter_id", "parameter_category_category_id");

}