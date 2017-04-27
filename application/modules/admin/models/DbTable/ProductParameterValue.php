<?php

class Admin_Model_DbTable_ProductParameterValue extends Zend_Db_Table_Abstract {

    /** Table name */
    protected $_name = "product_parameter_value";
    protected $_primary = array("product_parameter_value_product_id", "product_parameter_value_parameter_id");

}