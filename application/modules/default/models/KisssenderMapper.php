<?php

class Default_Model_KisssenderMapper {

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

        if (!$this->_dbTable instanceof Default_Model_Kisssender) {

            $this->setDbTable("Default_Model_DbTable_Kisssender");
        }

        return $this->_dbTable;
    }

    public function getLatLongs() {
        $select = $this->getDbTable()
                ->select()
                ->from(array('k' => 'kisssender'));

//        echo $select->__toString();die;

        $rows = $this->getDbTable()->fetchAll($select);
        
        $entries = array();
        
        if($rows){
            foreach($rows as $row){
                $kisssender = new Default_Model_Kisssender();
                $kisssender->setOptions($row->toArray());
                $entries[] = $kisssender;
            }
        }
        
        return $entries;
    }
    
//   ziskam pocet uzivatelu, od kterych byl poslan email
    public function getIpAddressCount() {
        $select = $this->getDbTable()
                ->select()
                ->from(array('k' => 'kisssender'))
                ->group('k.kisssender_ip_address');


        $rows = $this->getDbTable()->fetchAll($select);
        
        $entries = array();
        
        if($rows){
            foreach($rows as $row){
                $kisssender = new Default_Model_Kisssender();
                $kisssender->setOptions($row->toArray());
                $entries[] = $kisssender;
            }
        }
        
        return $entries;
    }

    public function save(Default_Model_Kisssender $kisssender) {
        $data = array(
            'kisssender_id' => $kisssender->getKisssender_id(),
            'kisssender_hash' => $kisssender->getKisssender_hash(),
            'kisssender_send_date' => $kisssender->getKisssender_send_date(),
            'kisssender_sender_name' => $kisssender->getKisssender_sender_name(),
            'kisssender_email_to' => $kisssender->getKisssender_email_to(),
            'kisssender_email_from' => $kisssender->getKisssender_email_from(),
            'kisssender_text' => $kisssender->getKisssender_text(),
            'kisssender_ip_address' => $kisssender->getKisssender_ip_address(),
            'kisssender_latitude' => $kisssender->getKisssender_latitude(),
            'kisssender_longitude' => $kisssender->getKisssender_longitude(),
        );


        if (null === ($id = $kisssender->getKisssender_id())) {
            unset($data['kisssender_id']);
//            print_r($data);die;

            $this->getDbTable()->insert($data);
        }
    }
    
    
    
    
    
    
    public function getKisssenderByHash($hash) {

        $select = $this->getDbTable()
                ->select()
                ->setIntegrityCheck(false)
                ->from(array('k' => 'kisssender'))
                ->where("k.kisssender_hash = ?", $hash);
//        echo $select->__toString();die;

        $row = $this->getDbTable()->fetchRow($select);

        $entries = array();


        $kisssender = new Default_Model_Kisssender();

        if ($row) {

            $kisssender->setOptions($row->toArray());

            $entries = $kisssender;
        }

        return $entries;
    }
    
    
    
    
    
    
    
    

//    public function getMaxOrderId() {
//        $select = $this->getDbTable()
//                ->select()
//                ->from(array('o' => 'order'), array('MAX(o.order_id) as order_number'));
//
////        echo $select->__toString();die;
//
//        $result = $this->getDbTable()->getDefaultAdapter()->fetchRow($select);
//
//        return $result['order_number'];
//    }
//
//    public function delete($id) {
//        $dbAdapter = $this->getDbTable()->getDefaultAdapter();
//        $where = $dbAdapter->quoteInto('order_id = ?', (int) $id);
//        $dbAdapter->delete("order", $where);
//    }

}
