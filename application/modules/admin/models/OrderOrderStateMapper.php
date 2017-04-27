<?php

class Admin_Model_OrderOrderStateMapper {

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

        if (!$this->_dbTable instanceof Admin_Model_OrderOrderState) {

            $this->setDbTable("Admin_Model_DbTable_OrderOrderState");
        }

        return $this->_dbTable;
    }
    
    
    
    public function getOrderOrderStateByOrderId($orderId) {
        $select = $this->getDbTable()
                ->select()
                ->setIntegrityCheck(false)
                ->from(array('oos' => 'order_order_state'))
                ->join(array('o' => 'order'), 'oos.order_order_state_order_id = o.order_id')
                ->join(array('os' => 'order_state'), 'oos.order_order_state_order_state_id = os.order_state_id')
                ->where('o.order_id = ?', $orderId);

        
        $result = $this->getDbTable()->fetchAll($select);

        $entries = array();

        foreach ($result as $row) {
            $order = new Admin_Model_Order();
            $order->setOptions($row->toArray());
            
            $orderState = new Admin_Model_OrderState();
            $orderState->setOptions($row->toArray());
            
            $orderOrderState = new Admin_Model_OrderOrderState();
            $orderOrderState->setOptions($row->toArray());
            
            $orderOrderState->setOrder($order);
            $orderOrderState->setOrder_state($orderState);
            
            $entries[] = $orderOrderState;
        }

        return $entries;
    }
    
    //pro kontrolu, jestli uz byl poslan email
    public function getOrderOrderStateByOrderIdAndOrderStateId($orderId, $orderStateId) {
        $select = $this->getDbTable()
                ->select()
                ->setIntegrityCheck(false)
                ->from(array('oos' => 'order_order_state'))
                ->where('oos.order_order_state_order_id = ?', $orderId)
                ->where('oos.order_order_state_order_state_id = ?', $orderStateId);

        
        $result = $this->getDbTable()->fetchRow($select);
        
        $entries = array();

        if($result){
            $entries = current($result);
        }

        return $entries;
    }
    
    

    public function save(Admin_Model_OrderOrderState $orderOrderState) {
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
