<?php

class Admin_Model_DeliveryMapper {

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

        if (!$this->_dbTable instanceof Admin_Model_Delivery) {

            $this->setDbTable("Admin_Model_DbTable_Delivery");
        }

        return $this->_dbTable;
    }

    public function getDeliveries($page = 0, $ipp = -1) {

        $select = $this->getDbTable()
                ->select()
                ->from(array('d' => 'delivery'));

        $paginator = Zend_Paginator::factory($select);
        $paginator->setCurrentPageNumber($page);
        $paginator->setDefaultItemCountPerPage($ipp);
        $result = $paginator->getCurrentItems();

        $entries = array();

        $this->_paginator = $paginator;

        foreach ($result as $row) {
            $delivery = new Admin_Model_Delivery();
            $entries[] = $delivery->setOptions($row->toArray());
        }

        return $entries;
    }

    public function getDeliveryById($id) {

        $select = $this->getDbTable()
                ->select()
                ->from(array('d' => 'delivery'))
                ->where('d.delivery_id = ?', $id);

        $row = $this->getDbTable()->getDefaultAdapter()->fetchRow($select);

        $entries = array();

        if (!empty($row)) {
            $delivery = new Admin_Model_Delivery();
            $entries = $delivery->setOptions($row);
        }

        return $entries;
    }

    public function save(Admin_Model_Delivery $delivery) {
        $data = array(
            'delivery_id' => $delivery->getDelivery_id(),
            'delivery_name' => $delivery->getDelivery_name(),
            'delivery_info' => $delivery->getDelivery_info(),
            'delivery_note' => $delivery->getDelivery_note(),
            'delivery_price_czk' => $delivery->getDelivery_price_czk(),
            'delivery_free_shipping_limit' => $delivery->getDelivery_free_shipping_limit(),
            'delivery_is_address' => $delivery->getDelivery_is_address(),
            'delivery_is_active' => $delivery->getDelivery_is_active()
        );

//        print_r($data);die;

        if (null === ($id = $delivery->getDelivery_id())) {
            unset($data['delivery_id']);
            $this->getDbTable()->insert($data);
        } else {
            
            //projdu ukladana data a pokud obsahuji prazdny retezec nebo null, tak je odstranim, zbytek ulozim
            foreach ($data as $key => $d) {
                if (in_array($d, array("", null))) {
                    unset($data[$key]);
                }
            }
            
//           print_r($data);die;

            
            $this->getDbTable()->update($data, array('delivery_id = ?' => $id));
        }
    }

    public function delete($id) {
        $dbAdapter = $this->getDbTable()->getDefaultAdapter();
        $where = $dbAdapter->quoteInto('delivery_id = ?', (int) $id);
        $dbAdapter->delete("delivery", $where);
    }
}
