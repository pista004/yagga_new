<?php

class Admin_Model_AffiliateProgramMapper {

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

        if (!$this->_dbTable instanceof Admin_Model_AffiliateProgram) {

            $this->setDbTable("Admin_Model_DbTable_AffiliateProgram");
        }

        return $this->_dbTable;
    }

    //find all
    public function getAffiliateProgramByName($name) {

        $select = $this->getDbTable()
                ->select()
                ->from(array('ap' => 'affiliate_program'))
                ->where('ap.affiliate_program_name = ?', $name);

        $row = $this->getDbTable()->getDefaultAdapter()->fetchRow($select);

        if (!$row) {
            return;
        }

        if (0 == count($row)) {
            return;
        }

        $entries = array();
        $affiliateProgram = new Admin_Model_AffiliateProgram();
        $affiliateProgram->setOptions($row);
        $entries = $affiliateProgram;

        return $entries;
    }


}
