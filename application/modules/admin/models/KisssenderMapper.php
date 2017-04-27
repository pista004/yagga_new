<?php

class Admin_Model_KisssenderMapper {

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

        if (!$this->_dbTable instanceof Admin_Model_Kisssender) {

            $this->setDbTable("Admin_Model_DbTable_Kisssender");
        }

        return $this->_dbTable;
    }

    public function getKisssenders($page = 0, $ipp = -1) {
        $select = $this->getDbTable()
                ->select()
                ->from(array('k' => 'kisssender'))
                ->order('k.kisssender_id DESC');

//        echo $select->__toString();die;

//        $rows = $this->getDbTable()->fetchAll($select);
        
        $paginator = Zend_Paginator::factory($select);
        $paginator->setCurrentPageNumber($page);
        $paginator->setDefaultItemCountPerPage($ipp);
        $result = $paginator->getCurrentItems();

        $entries = array();

        $this->_paginator = $paginator;
        
        if($result){
            foreach($result as $row){
                $kisssender = new Admin_Model_Kisssender();
                $kisssender->setOptions($row->toArray());
                $entries[] = $kisssender;
            }
        }
        
        return $entries;
    }
    

}
