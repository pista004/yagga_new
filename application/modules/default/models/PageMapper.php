<?php

class Default_Model_PageMapper {

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

        if (!$this->_dbTable instanceof Default_Model_Page) {

            $this->setDbTable("Default_Model_DbTable_Page");
        }

        return $this->_dbTable;
    }

    public function getPages() {

        $select = $this->getDbTable()
                ->select()
                ->from(array('p' => 'page'))
                ->where('page_is_active = 1');

//        echo $select->__toString();die;

        $result = $this->getDbTable()->fetchAll($select);
        $entries = array();

        foreach ($result as $row) {
            $page = new Default_Model_Page();
            $entries[$row['page_id']] = $page->setOptions($row->toArray());
        }

        return $entries;
    }

    public function getPageByUrl($url) {
        $select = $this->getDbTable()
                ->select()
                ->setIntegrityCheck(false)
                ->from(array('p' => 'page'))
                ->where("p.page_url = ?", $url)
                ->where("p.page_is_active = 1");

//        echo $select->__toString();die;

        $row = $this->getDbTable()->fetchRow($select);

        $entries = array();


        $page = new Default_Model_Page();

        if ($row) {

            $entries[$row['page_id']] = $page->setOptions($row->toArray());
        }

        return $entries;
    }

}
