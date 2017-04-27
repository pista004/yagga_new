<?php

class Admin_Model_CategoryAffiliateCategoryMapper {

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

        if (!$this->_dbTable instanceof Admin_Model_CategoryAffiliateCategory) {

            $this->setDbTable("Admin_Model_DbTable_CategoryAffiliateCategory");
        }

        return $this->_dbTable;
    }

    //find all
    public function getCategories($page = 0, $ipp = -1, $others = array()) {

        $select = $this->getDbTable()
                ->select()
                ->from(array('cac' => 'category_affiliate_category'));

        if (!empty($others)) {
            if (array_key_exists('where', $others)) {
                if ($others['where']) {
                    $select->where($others['where']);
                }
            }
            if (array_key_exists('order', $others)) {

                if ($others['order']) {
                    $select->order($others['order']);
                }
            }
        }

//        echo $select->__toString();die;

        $paginator = Zend_Paginator::factory($select);
        $paginator->setCurrentPageNumber($page);
        $paginator->setDefaultItemCountPerPage($ipp);
        $result = $paginator->getCurrentItems();
        $entries = array();

        $this->_paginator = $paginator;

        foreach ($result as $row) {
            $categoryAffiliateCategory = new Admin_Model_CategoryAffiliateCategory();

            $entries[] = $categoryAffiliateCategory->setOptions($row->toArray());
        }

        return $entries;
    }

    
    
        public function getCategoryAffiliateById($id) {

        $select = $this->getDbTable()
                ->select()
                ->setIntegrityCheck(false)
                ->from(array('cac' => 'category_affiliate_category'))
                ->joinLeft(array('c' => 'category'), 'cac.category_affiliate_category_category_id = c.category_id')
                ->where('cac.category_affiliate_category_id = ?', (int) $id);

//        echo $select->__toString();die;

        $row = $this->getDbTable()->getDefaultAdapter()->fetchRow($select);

        if (!$row || 0 == count($row)) {
            return;
        }

//        print_r($row);die;

        $categoryAffiliate = new Admin_Model_CategoryAffiliateCategory();
        $categoryAffiliate->setOptions($row);
        
        $category = new Admin_Model_Category();
        $category->setOptions($row);


        $categoryAffiliate->setCategory($category);

        return $categoryAffiliate;
    }
    
    
    
    
    public function save(Admin_Model_CategoryAffiliateCategory $categoryAffiliateCategory) {
        $data = array(
            'category_affiliate_category_id' => $categoryAffiliateCategory->getCategory_affiliate_category_id(),
            'category_affiliate_category_name' => $categoryAffiliateCategory->getCategory_affiliate_category_name(),
            'category_affiliate_category_category_id' => $categoryAffiliateCategory->getCategory_affiliate_category_category_id(),
        );

        if (null === ($id = $categoryAffiliateCategory->getCategory_affiliate_category_id())) {
            unset($data['category_affiliate_category_id']);
            $this->getDbTable()->insert($data);
        } else {

            //projdu ukladana data a pokud obsahuji prazdny retezec nebo null, tak je odstranim, zbytek ulozim
            foreach ($data as $key => $d) {
                if (in_array($d, array("", null))) {
                    unset($data[$key]);
                }
            }

            $this->getDbTable()->update($data, array('category_affiliate_category_id = ?' => $id));
        }
    }

    public function bulkInsert(array $categoryAffiliateCategories) {
        if (!empty($categoryAffiliateCategories)) {
            
            $db = $this->getDbTable()->getAdapter();
            $query = 'INSERT INTO category_affiliate_category (category_affiliate_category_name) VALUES ';
            $queryVals = array();
            foreach ($categoryAffiliateCategories as $affiliateCategory) {
                $queryVals[] = '('.$db->quote($affiliateCategory).')';
            }
//            echo $query . implode(',', $queryVals);die;
            $db->query($query . implode(',', $queryVals));
        }

    }

}
