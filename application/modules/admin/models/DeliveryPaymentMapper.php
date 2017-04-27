<?php

class Admin_Model_DeliveryPaymentMapper {

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

        if (!$this->_dbTable instanceof Admin_Model_DeliveryPayment) {

            $this->setDbTable("Admin_Model_DbTable_DeliveryPayment");
        }

        return $this->_dbTable;
    }

    
    public function getDeliveryPaymentByDeliveryId($id) {

        $select = $this->getDbTable()
                ->select()
                ->from(array('dp' => 'delivery_payment'))
                ->where('dp.delivery_payment_delivery_id = ?', (int) $id);

        $result = $this->getDbTable()->getDefaultAdapter()->fetchAll($select);

        $entries = array();

        foreach ($result as $row) {
            $deliveryPayment = new Admin_Model_DeliveryPayment();
            $entries[] = $deliveryPayment->setOptions($row);
        }

        return $entries;
    }
    
    
    public function getDeliveryPaymentByPaymentId($id) {

        $select = $this->getDbTable()
                ->select()
                ->from(array('dp' => 'delivery_payment'))
                ->where('dp.delivery_payment_payment_id = ?', (int) $id);

        $result = $this->getDbTable()->getDefaultAdapter()->fetchAll($select);

        $entries = array();

        foreach ($result as $row) {
            $deliveryPayment = new Admin_Model_DeliveryPayment();
            $entries[] = $deliveryPayment->setOptions($row);
        }

        return $entries;
    }
    
    
    public function save(Admin_Model_DeliveryPayment $deliveryPayment) {
        $data = array(
            'delivery_payment_delivery_id' => $deliveryPayment->getDelivery_payment_delivery_id(),
            'delivery_payment_payment_id' => $deliveryPayment->getDelivery_payment_payment_id(),
        );

        if ($deliveryPayment->getDelivery_payment_delivery_id() && $deliveryPayment->getDelivery_payment_payment_id()) {
            $this->getDbTable()->insert($data);
        }
    }

    public function deleteByDeliveryId($id) {
        $dbAdapter = $this->getDbTable()->getDefaultAdapter();
        $where = $dbAdapter->quoteInto('delivery_payment_delivery_id = ?', (int) $id);
        $dbAdapter->delete("delivery_payment", $where);
    }
    
    public function deleteByPaymentId($id) {
        $dbAdapter = $this->getDbTable()->getDefaultAdapter();
        $where = $dbAdapter->quoteInto('delivery_payment_payment_id = ?', (int) $id);
        $dbAdapter->delete("delivery_payment", $where);
    }
    
}