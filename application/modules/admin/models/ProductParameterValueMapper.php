<?php

class Admin_Model_ProductParameterValueMapper {

    protected $_dbTable;
    public $_paginator;

    public function setDbTable($dbTable) {
        if (is_string($dbTable)) {
            $dbTable = new $dbTable();
        }
        if (!$dbTable instanceof Zend_Db_Table_Abstract) {
            throw new Exception('Invalid table data gateway provided');
        }
        $this->_dbTable = $dbTable;
        return $this;
    }

    /**
     * Get registered Zend_Db_Table instance, if param is filled return Zend_db_table instance and no Save this
     *
     * Lazy loads Default_Model_DbTable_Nabidka if no instance registered
     *
     * @return Zend_Db_Table_Abstract
     */
    public function getDbTable() {

        if (!$this->_dbTable instanceof Admin_Model_ProductParameterValue) {

            $this->setDbTable("Admin_Model_DbTable_ProductParameterValue");
        }

        return $this->_dbTable;
    }

    public function insert(Admin_Model_ProductParameterValue $productParameterValue) {

        $data = array(
            'product_parameter_value_product_id' => $productParameterValue->getProduct_parameter_value_product_id(),
            'product_parameter_value_parameter_id' => $productParameterValue->getProduct_parameter_value_parameter_id(),
            'product_parameter_value_value' => $productParameterValue->getProduct_parameter_value_value(),
            'product_parameter_value_value_bool' => $productParameterValue->getProduct_parameter_value_value_bool(),
            'product_parameter_value_parameter_dial_id' => $productParameterValue->getProduct_parameter_value_parameter_dial_id(),
        );

        if ($productParameterValue->getProduct_parameter_value_product_id() && $productParameterValue->getProduct_parameter_value_product_id()) {
            $this->getDbTable()->insert($data);
        }
    }

    public function update(Admin_Model_ProductParameterValue $productParameterValue) {

        $data = array(
            'product_parameter_value_product_id' => $productParameterValue->getProduct_parameter_value_product_id(),
            'product_parameter_value_parameter_id' => $productParameterValue->getProduct_parameter_value_parameter_id(),
            'product_parameter_value_value' => $productParameterValue->getProduct_parameter_value_value(),
            'product_parameter_value_value_bool' => $productParameterValue->getProduct_parameter_value_value_bool(),
            'product_parameter_value_parameter_dial_id' => $productParameterValue->getProduct_parameter_value_parameter_dial_id(),
        );

        if ($productParameterValue->getProduct_parameter_value_product_id() && $productParameterValue->getProduct_parameter_value_product_id()) {
            $this->getDbTable()->update($data, array(
                'product_parameter_value_product_id = ?' => $productParameterValue->getProduct_parameter_value_product_id(),
                'product_parameter_value_parameter_id = ?' => $productParameterValue->getProduct_parameter_value_parameter_id()
                    )
            );
        }
    }

}
