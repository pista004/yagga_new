<?php

class Admin_Model_PaymentMapper {

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

        if (!$this->_dbTable instanceof Admin_Model_Payment) {

            $this->setDbTable("Admin_Model_DbTable_Payment");
        }

        return $this->_dbTable;
    }

    public function getPayments($page = 0, $ipp = -1) {

        $select = $this->getDbTable()
                ->select()
                ->from(array('p' => 'payment'));

        $paginator = Zend_Paginator::factory($select);
        $paginator->setCurrentPageNumber($page);
        $paginator->setDefaultItemCountPerPage($ipp);
        $result = $paginator->getCurrentItems();

        $entries = array();

        $this->_paginator = $paginator;

        foreach ($result as $row) {
            $payment = new Admin_Model_Payment();
            $entries[] = $payment->setOptions($row->toArray());
        }

        return $entries;
    }

    public function getPaymentById($id) {

        $select = $this->getDbTable()
                ->select()
                ->from(array('p' => 'payment'))
                ->where('p.payment_id = ?', $id);

        $row = $this->getDbTable()->getDefaultAdapter()->fetchRow($select);

        $entries = array();

        if (!empty($row)) {
            $payment = new Admin_Model_Payment();
            $entries = $payment->setOptions($row);
        }

        return $entries;
    }

    public function save(Admin_Model_Payment $payment) {
        $data = array(
            'payment_id' => $payment->getPayment_id(),
            'payment_name' => $payment->getPayment_name(),
            'payment_info' => $payment->getPayment_info(),
            'payment_note' => $payment->getPayment_note(),
            'payment_price_czk' => $payment->getPayment_price_czk(),
            'payment_free_shipping_limit' => $payment->getPayment_free_shipping_limit(),
            'payment_is_active' => $payment->getPayment_is_active()
        );

//        print_r($data);die;

        if (null === ($id = $payment->getPayment_id())) {
            unset($data['payment_id']);
            $this->getDbTable()->insert($data);
        } else {
            
            //projdu ukladana data a pokud obsahuji prazdny retezec nebo null, tak je odstranim, zbytek ulozim
            foreach ($data as $key => $d) {
                if (in_array($d, array("", null))) {
                    unset($data[$key]);
                }
            }
            
            $this->getDbTable()->update($data, array('payment_id = ?' => $id));
        }
    }

    public function delete($id) {
        $dbAdapter = $this->getDbTable()->getDefaultAdapter();
        $where = $dbAdapter->quoteInto('payment_id = ?', (int) $id);
        $dbAdapter->delete("payment", $where);
    }
}
