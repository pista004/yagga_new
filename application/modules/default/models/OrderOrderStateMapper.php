<?php

class Default_Model_OrderOrderStateMapper {

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

        if (!$this->_dbTable instanceof Default_Model_OrderOrderState) {

            $this->setDbTable("Default_Model_DbTable_OrderOrderState");
        }

        return $this->_dbTable;
    }

    public function save(Default_Model_OrderOrderState $orderOrderState) {
        $data = array(
            'order_order_state_order_id' => $orderOrderState->getOrder_order_state_order_id(),
            'order_order_state_order_state_id' => $orderOrderState->getOrder_order_state_order_state_id(),
            'order_order_state_date' => $orderOrderState->getOrder_order_state_date()
        );

        if ($orderOrderState->getOrder_order_state_order_id() && $orderOrderState->getOrder_order_state_order_state_id()){

            $this->getDbTable()->insert($data);
        }
    }


}
