<?php

class Admin_Model_PageMapper {

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

        if (!$this->_dbTable instanceof Admin_Model_Page) {

            $this->setDbTable("Admin_Model_DbTable_Page");
        }

        return $this->_dbTable;
    }

    
     public function getPages($page = 0, $ipp = -1) {

        $select = $this->getDbTable()
                ->select()
                ->from(array('p' => 'page'));

//        echo $select->__toString();die;

        $paginator = Zend_Paginator::factory($select);
        $paginator->setCurrentPageNumber($page);
        $paginator->setDefaultItemCountPerPage($ipp);
        $result = $paginator->getCurrentItems();
        $entries = array();

        $this->_paginator = $paginator;

        foreach ($result as $row) {
            $page = new Admin_Model_Page();
            $entries[$row['page_id']] = $page->setOptions($row->toArray());
        }

        return $entries;
    }
    
    
    public function getPageById($pageId) {

        $select = $this->getDbTable()
                ->select()
                ->from(array('p' => 'page'))
                ->where('p.page_id = ?', $pageId);

//        echo $select->__toString();die;

        $result = $this->getDbTable()->fetchRow($select);
        
        $entries = array();

        if($result){
            $page = new Admin_Model_Page();
            $page->setOptions(current($result));
            $entries = $page;
        }

        return $entries;
    }
    

    public function save(Admin_Model_Page $page) {

        $data = array(
            'page_id' => $page->getPage_id(),
            'page_name' => $page->getPage_name(),
            'page_url' => $page->getPage_url(),
            'page_seo_title' => $page->getPage_seo_title(),
            'page_seo_meta_description' => $page->getPage_seo_meta_description(),
            'page_text' => $page->getPage_text(),
            'page_is_active' => $page->getPage_is_active(),
        );

        if (null === ($id = $page->getPage_id())) {
            unset($data['page_id']);
            $this->getDbTable()->insert($data);
        } else {
            
            //projdu ukladana data a pokud obsahuji prazdny retezec nebo null, tak je odstranim, zbytek ulozim
            foreach ($data as $key => $d) {
                if (in_array($d, array("", null))) {
                    unset($data[$key]);
                }
            }
            $this->getDbTable()->update($data, array('page_id = ?' => $id));
        }
    }
    
    
    
    public function delete($id) {
        $dbAdapter = $this->getDbTable()->getDefaultAdapter();
        $where = $dbAdapter->quoteInto('page_id = ?', (int) $id);
        $dbAdapter->delete("page", $where);
    }



}
