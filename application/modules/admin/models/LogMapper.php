<?php

class Admin_Model_LogMapper {

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

        if (!$this->_dbTable instanceof Admin_Model_Log) {

            $this->setDbTable("Admin_Model_DbTable_Log");
        }

        return $this->_dbTable;
    }

    //ziskam vsechny logy    
    public function getLogs($page = 0, $ipp = -1) {

        $select = $this->getDbTable()
                ->select()
                ->from(array('l' => 'log'))
                ->order('l.log_inserted DESC');
        
        $paginator = Zend_Paginator::factory($select);
        $paginator->setCurrentPageNumber($page);
        $paginator->setDefaultItemCountPerPage($ipp);
        $result = $paginator->getCurrentItems();

        $entries = array();

        $this->_paginator = $paginator;

        foreach ($result as $row) {
            $log = new Admin_Model_Log();
            $entries[] = $log->setOptions($row->toArray());
        }

        return $entries;
    }


    public function save(Admin_Model_Log $log) {

        $data = array(
            'log_id' => $log->getLog_id(),
            'log_message' => $log->getLog_message(),
            'log_inserted' => $log->getLog_inserted(),
        );

        if (null === ($id = $log->getLog_id())) {
            unset($data['log_id']);
            $this->getDbTable()->insert($data);
        } else {
            $this->getDbTable()->update($data, array('log_id = ?' => $id));
        }
    }

//    public function delete($id) {
//        $dbAdapter = $this->getDbTable()->getDefaultAdapter();
//        $where = $dbAdapter->quoteInto('manufacturer_id = ?', (int) $id);
//        $dbAdapter->delete("manufacturer", $where);
//    }

}
