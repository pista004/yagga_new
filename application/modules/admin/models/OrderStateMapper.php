<?php

class Admin_Model_OrderStateMapper {

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

        if (!$this->_dbTable instanceof Admin_Model_OrderState) {

            $this->setDbTable("Admin_Model_DbTable_OrderState");
        }

        return $this->_dbTable;
    }

    public function getOrderStates($page = 0, $ipp = -1) {

        $select = $this->getDbTable()
                ->select()
                ->from(array('os' => 'order_state'));

        $paginator = Zend_Paginator::factory($select);
        $paginator->setCurrentPageNumber($page);
        $paginator->setDefaultItemCountPerPage($ipp);
        $result = $paginator->getCurrentItems();

        $entries = array();

        $this->_paginator = $paginator;

        foreach ($result as $row) {
            $orderState = new Admin_Model_OrderState();
            $entries[$row['order_state_id']] = $orderState->setOptions($row->toArray());
        }

        return $entries;
    }

    public function getOrderStateById($id) {

        $select = $this->getDbTable()
                ->select()
                ->from(array('os' => 'order_state'))
                ->where('os.order_state_id = ?', $id);

        $row = $this->getDbTable()->fetchRow($select);

        $entries = array();

        $orderState = new Admin_Model_OrderState();
        $entries = $orderState->setOptions($row->toArray());

        return $entries;
    }

    public function save(Admin_Model_OrderState $orderState) {
        $data = array(
            'order_state_id' => $orderState->getOrder_state_id(),
            'order_state_name' => $orderState->getOrder_state_name(),
            'order_state_color' => $orderState->getOrder_state_color(),
            'order_state_text' => $orderState->getOrder_state_text()
        );
        
        if (null === ($id = $orderState->getOrder_state_id())) {
            unset($data['order_state_id']);
            $this->getDbTable()->insert($data);
        } else {
            $this->getDbTable()->update($data, array('order_state_id = ?' => $id));
        }
    }
    
    
    public function delete($id) {
        $dbAdapter = $this->getDbTable()->getDefaultAdapter();
        $where = $dbAdapter->quoteInto('order_state_id = ?', (int) $id);
        $dbAdapter->delete("order_state", $where);
    }
    
}
