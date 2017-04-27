<?php

class Admin_Model_InvoiceMapper {

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

        if (!$this->_dbTable instanceof Admin_Model_Invoice) {

            $this->setDbTable("Admin_Model_DbTable_Invoice");
        }

        return $this->_dbTable;
    }

    //ziskam vsechny faktury   
    public function getInvoices($page = 0, $ipp = -1) {

        $select = $this->getDbTable()
                ->select()
                ->from(array('i' => 'invoice'));

        $paginator = Zend_Paginator::factory($select);
        $paginator->setCurrentPageNumber($page);
        $paginator->setDefaultItemCountPerPage($ipp);
        $result = $paginator->getCurrentItems();

        $entries = array();

        $this->_paginator = $paginator;

        foreach ($result as $row) {
            $invoice = new Admin_Model_Invoice();
            $entries[] = $invoice->setOptions($row->toArray());
        }

        return $entries;
    }

    //find by id - detail
    public function getInvoiceById($id) {

        $select = $this->getDbTable()
                ->select()
                ->from(array('i' => 'invoice'))
                ->where('i.invoice_id = ?', (int) $id);

        $row = $this->getDbTable()->getDefaultAdapter()->fetchRow($select);

        if (!$row || 0 == count($row)) {
            return;
        }

        $invoice = new Admin_Model_Invoice();
        $invoice->setOptions($row);

        return $invoice;
    }

    //find by id - detail
    public function getInvoiceByOrderId($id) {

        $select = $this->getDbTable()
                ->select()
                ->from(array('i' => 'invoice'))
                ->where('i.invoice_order_id = ?', (int) $id);

        $row = $this->getDbTable()->getDefaultAdapter()->fetchRow($select);

        if (!$row || 0 == count($row)) {
            return;
        }

        $invoice = new Admin_Model_Invoice();
        $invoice->setOptions($row);

        return $invoice;
    }

    public function getMaxInvoiceId() {
        $select = $this->getDbTable()
                ->select()
                ->from(array('i' => 'invoice'), array('MAX(i.invoice_id) as invoice_number'));

//        echo $select->__toString();die;

        $result = $this->getDbTable()->getDefaultAdapter()->fetchRow($select);

        return $result['invoice_number'];
    }

    public function save(Admin_Model_Invoice $invoice) {

        $data = array(
            'invoice_id' => $invoice->getInvoice_id(),
            'invoice_number' => $invoice->getInvoice_number(),
            'invoice_path' => $invoice->getInvoice_path(),
            'invoice_creating_date' => $invoice->getInvoice_creating_date(),
            'invoice_due_date' => $invoice->getInvoice_due_date(),
            'invoice_order_id' => $invoice->getInvoice_order_id(),
            'invoice_is_sent' => $invoice->getInvoice_is_sent(),
        );

        if (null === ($id = $invoice->getInvoice_id())) {
            unset($data['invoice_id']);
            $this->getDbTable()->insert($data);
        } else {

            //projdu ukladana data a pokud obsahuji prazdny retezec nebo null, tak je odstranim, zbytek ulozim
            foreach ($data as $key => $d) {
                if (in_array($d, array("", null))) {
                    unset($data[$key]);
                }
            }
//print_r($data);die;
            $this->getDbTable()->update($data, array('invoice_id = ?' => $id));
        }
    }

    public function delete($id) {
        $dbAdapter = $this->getDbTable()->getDefaultAdapter();
        $where = $dbAdapter->quoteInto('invoice_id = ?', (int) $id);
        $dbAdapter->delete("invoice", $where);
    }

}
