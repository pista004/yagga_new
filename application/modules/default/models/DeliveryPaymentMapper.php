<?php

class Default_Model_DeliveryPaymentMapper {

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

        if (!$this->_dbTable instanceof Default_Model_DeliveryPayment) {

            $this->setDbTable("Default_Model_DbTable_DeliveryPayment");
        }

        return $this->_dbTable;
    }

    
    
    //  ziskam aktivni kombinace dopravy a platby  
    public function getDeliveriesPayments() {

        $select = $this->getDbTable()
                ->select()
                ->setIntegrityCheck(false)
                ->from(array('d' => 'delivery'))
                ->join(array('dp' => 'delivery_payment'), 'd.delivery_id = dp.delivery_payment_delivery_id')
                ->join(array('p' => 'payment'), 'dp.delivery_payment_payment_id = p.payment_id')
                ->where('d.delivery_is_active = 1')
                ->where('p.payment_is_active = 1');

//        echo $select->__toString();die;
        
        $result = $this->getDbTable()->getDefaultAdapter()->fetchAll($select);

        $entries = array();

        foreach ($result as $row) {
            $delivery = new Default_Model_Delivery();
            $delivery->setOptions($row);

            $payment = new Default_Model_Payment();
            $payment->setOptions($row);
            
            $deliveryPayment = new Default_Model_DeliveryPayment();
            $deliveryPayment->setOptions($row);
            $deliveryPayment->setDelivery($delivery);
            $deliveryPayment->setPayment($payment);
            
            $entries[] = $deliveryPayment;
        }

        return $entries;
    }
    
    
    
    
    
//  ziskam dopravu zavislou na platbe a aktivni - do vypisu - kombinace dopravy a platby v objednavce  
    public function getDeliveries() {

//        SELECT * FROM delivery d INNER JOIN delivery_payment dp ON d.delivery_id = dp.delivery_payment_delivery_id INNER JOIN payment p ON dp.delivery_payment_payment_id = p.payment_id WHERE d.delivery_is_active = 1 AND p.payment_is_active = 1 GROUP BY d.delivery_id


        $select = $this->getDbTable()
                ->select()
                ->setIntegrityCheck(false)
                ->from(array('d' => 'delivery'))
                ->join(array('dp' => 'delivery_payment'), 'd.delivery_id = dp.delivery_payment_delivery_id')
                ->join(array('p' => 'payment'), 'dp.delivery_payment_payment_id = p.payment_id')
                ->where('d.delivery_is_active = 1')
                ->where('p.payment_is_active = 1')
                ->group('d.delivery_id');

//        echo $select->__toString();die;
        
        $result = $this->getDbTable()->getDefaultAdapter()->fetchAll($select);

        $entries = array();

        foreach ($result as $row) {
            $delivery = new Default_Model_Delivery();
            $delivery->setOptions($row);

            $deliveryPayment = new Default_Model_DeliveryPayment();
            $deliveryPayment->setOptions($row);
            $deliveryPayment->setDelivery($delivery);

            $entries[] = $deliveryPayment;
        }

        return $entries;
    }
    
    
    
    //  ziskam platbu zavislou na doprave a aktivni - do vypisu - kombinace dopravy a platby v objednavce  
    public function getPayments() {

        $select = $this->getDbTable()
                ->select()
                ->setIntegrityCheck(false)
                ->from(array('d' => 'delivery'))
                ->join(array('dp' => 'delivery_payment'), 'd.delivery_id = dp.delivery_payment_delivery_id')
                ->join(array('p' => 'payment'), 'dp.delivery_payment_payment_id = p.payment_id')
                ->where('d.delivery_is_active = 1')
                ->where('p.payment_is_active = 1')
                ->group('p.payment_id');

        $result = $this->getDbTable()->getDefaultAdapter()->fetchAll($select);

        $entries = array();

        foreach ($result as $row) {
            $payment = new Default_Model_Payment();
            $payment->setOptions($row);

            $deliveryPayment = new Default_Model_DeliveryPayment();
            $deliveryPayment->setOptions($row);
            $deliveryPayment->setPayment($payment);

            $entries[] = $deliveryPayment;
        }

        return $entries;
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

    
    //kontrola existence kombinace dopravy a platby - kontrola napriklad pri vypnutem javascriptu
    public function checkDeliveryPaymentCombination($deliveryId, $paymentId) {

//        SELECT * FROM 
//	bpb_doprava d 
//		INNER JOIN 
//	bpb_doprava_platba dp ON d.doprava_id = dp.doprava_platba_doprava_id 
//		INNER JOIN 
//	bpb_platba p ON dp.doprava_platba_platba_id = p.platba_id 
//	WHERE 
//		d.doprava_aktivni = 1 
//	AND 
//		p.platba_aktivni = 1 
//	AND
//		d.doprava_id = " . $db->sql_escape((int) $doprava) . "
//	AND
//		p.platba_id = " . $db->sql_escape((int) $platba);
        
        
        
        
        $select = $this->getDbTable()
                ->select()
                ->setIntegrityCheck(false)
                ->from(array('d' => 'delivery'))
                ->join(array('dp' => 'delivery_payment'), 'd.delivery_id = dp.delivery_payment_delivery_id')
                ->join(array('p' => 'payment'), 'dp.delivery_payment_payment_id = p.payment_id')
                ->where('d.delivery_is_active = 1')
                ->where('p.payment_is_active = 1')
                ->where('d.delivery_id = ?', $deliveryId)
                ->where('p.payment_id = ?', $paymentId);

        
        $result = $this->getDbTable()->getDefaultAdapter()->fetchRow($select);

        $return = 0;
        
        if($result){
            $return = 1;
            return $return;
        }
        
        return $return;
    }
    
}