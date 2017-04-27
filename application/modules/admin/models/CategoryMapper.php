<?php

class Admin_Model_CategoryMapper {

    protected $_dbTable;
    public $_paginator;
    private $_childsIds = array();
    private $_childsAry = array();

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

        if (!$this->_dbTable instanceof Admin_Model_Category) {

            $this->setDbTable("Admin_Model_DbTable_Category");
        }

        return $this->_dbTable;
    }

    //find all
    public function getCategories($page = 0, $ipp = -1) {

        $select = $this->getDbTable()
                ->select()
                ->from(array('c' => 'category'))
                ->order('c.category_order DESC');

        $paginator = Zend_Paginator::factory($select);
        $paginator->setCurrentPageNumber($page);
        $paginator->setDefaultItemCountPerPage($ipp);
        $result = $paginator->getCurrentItems();
        $entries = array();

        $this->_paginator = $paginator;

        foreach ($result as $row) {
            $category = new Admin_Model_Category();

            $entries[] = $category->setOptions($row->toArray());

            $category->setCategory_structure($this->getParents($category->getCategory_parent(), $category->getCategory_name()));

//            $category->setCategory_structure($this->getParents($category->getCategory_parent()));
        }

        return $entries;
    }

    //find by id - detail
    public function getCategoryById($id) {

        $select = $this->getDbTable()
                ->select()
                ->from(array('c' => 'category'))
                ->where('c.category_id = ?', (int) $id);

        $row = $this->getDbTable()->getDefaultAdapter()->fetchRow($select);

        if (!$row || 0 == count($row)) {
            return;
        }

        $entries = array();
        $category = new Admin_Model_Category();
        $category->setOptions($row);
        $entries = $category;

        return $entries;
    }

    /*
     * id=1, parent=null = elektro
     * id=2, parent=1 = elektro/pc
     * id=3, parent=1 = elektro/notebooky
     * id=4, parent=1 = elektro/mobilni
     * id=5, parent=4 = elektro/mobilni/tablety
     * id=6, parent=4 = elektro/mobilni/telefony
     * 
     */

//rekurze, postupne ziskam vsechny rodice - napr elektro/pc/notebooky
    public function getParents($parent, $structure) {

        if (!$parent) {
            return $structure;
        }

        $select = $this->getDbTable()
                ->select()
                ->from(array('c' => 'category'))
                ->where('c.category_id = ?', $parent)
                ->order('c.category_order DESC');

        $row = $this->getDbTable()->fetchRow($select);

        $structure = $row['category_name'] . " / " . $structure;

        if ($row['category_parent']) {
            return $this->getParents($row['category_parent'], $structure);
        }

        return $structure;
    }

    //rekurze, postupne ziskam vsechny podkategorie/deti/child - napr elektro/pc/notebooky
    public function getChilds($id, $root = false) {
        if (!$id && !$root) {
            return;
        }

        $select = $this->getDbTable()
                ->select()
                ->from(array('c' => 'category'))
                ->where('c.category_parent = ?', $id)
                ->order('c.category_order DESC');

        $rows = $this->getDbTable()->fetchAll($select);

        $entries = array();
        foreach ($rows as $row) {
            $category = new Admin_Model_Category();

            $category->setOptions($row->toArray());

            if ($category->getCategory_id()) {
                $category->setCategory_childs($this->getChilds($category->getCategory_id()));
            }
            $entries[$category->getCategory_id()] = $category;
        }

        return $entries;
    }

    
    
    
    public function getChildsCache($id, $root = false) {
        
        $zend_cache = Zend_Registry::get('AdminCache24h');

        $categoryChilds = array();

//k nacitani informaci o pobockach ulozenky pouzivam cache - nepotrebuju nacitat porad
        if (!$categoryChilds = $zend_cache->load('AdmCategoryChilds')) {

            $categoryChilds = $this->getChilds($id, $root);

            $zend_cache->save($categoryChilds, 'AdmCategoryChilds');
        } else {
            $categoryChilds = $zend_cache->load('AdmCategoryChilds');
        }

        return $categoryChilds;
    }
    
    
    
    public function save(Admin_Model_Category $category) {
        $data = array(
            'category_id' => $category->getCategory_id(),
            'category_name' => $category->getCategory_name(),
            'category_h1' => $category->getCategory_h1(),
            'category_description' => $category->getCategory_description(),
            'category_parent' => $category->getCategory_parent(),
            'category_url' => $category->getCategory_url(),
            'category_seo_title' => $category->getCategory_seo_title(),
            'category_seo_meta_description' => $category->getCategory_seo_meta_description(),
            'category_category_heureka' => $category->getCategory_category_heureka(),
            'category_is_active' => $category->getCategory_is_active(),
            'category_order' => $category->getCategory_order(),
        );

        if (null === ($id = $category->getCategory_id())) {
            unset($data['category_id']);
            $this->getDbTable()->insert($data);
        } else {

            //pole parametru, ktere mohou obsahovat prazdny text
            $noUnsetIfEmpty = array('category_h1');

            //projdu ukladana data a pokud obsahuji prazdny retezec nebo null, tak je odstranim, zbytek ulozim
            foreach ($data as $key => $d) {
                if (in_array($d, array("", null)) && !in_array($key, $noUnsetIfEmpty)) {
                    unset($data[$key]);
                }
            }

            $this->getDbTable()->update($data, array('category_id = ?' => $id));
        }
    }

    //smaze kategorie, vstup je pole, protoze mazu i subkategorie
    public function delete($ids) {

//        print_r($ids);die;
        $dbAdapter = $this->getDbTable()->getDefaultAdapter();

        $where = $dbAdapter->quoteInto('category_id IN(?)', $ids);
        $dbAdapter->delete("category", $where);
    }

    //z potomku ziskam jen id potomku, pouzito pro mazani podkategorii
    public function getCategoryChildsIds($childs) {

        if (!is_array($childs) || empty($childs)) {
            return;
        }

        foreach ($childs as $child) {

            $this->_childsIds[] = $child->getCategory_id();

            if (is_array($child->getCategory_childs())) {
                $this->getCategoryChildsIds($child->getCategory_childs());
            }
        }

        return $this->_childsIds;
    }

//  vraci pole vsech kategorii i podkategorii v jednorozmernem poli s parametrem level pro rozeznani stupne zanoreni
    public function getCategoryChildsAry($childs, $num = 0) {

        if (!is_array($childs)) {
            return;
        }

        $n = 0;

        if ($n != $num) {
            $n = $num;
        }

        foreach ($childs as $child) {

            $current_child = new Admin_Model_Category();
            $current_child = clone $child;
            $current_child->setCategory_childs(array());
            $current_child->setCategory_level($n);
            $current_child->setCategory_structure($this->getParents($current_child->getCategory_parent(), $current_child->getCategory_name()));

            $this->_childsAry[$current_child->getCategory_id()] = $current_child;

            if (is_array($child->getCategory_childs())) {
                if (count($child->getCategory_childs()) > 0) {
                    $num = $n + 1;
                    $this->getCategoryChildsAry($child->getCategory_childs(), $num);
                }
            }
        }

        return $this->_childsAry;
    }

    public function getCategoryChildsAryCache($childs, $num = 0) {

        $zend_cache = Zend_Registry::get('AdminCache24h');
        
        
        $categoryChildsAry = array();

//k nacitani informaci o pobockach ulozenky pouzivam cache - nepotrebuju nacitat porad
        if (!$categoryChildsAry = $zend_cache->load('AdmCategoryChildsAry')) {

            $categoryChildsAry = $this->getCategoryChildsAry($childs, $num);

            $zend_cache->save($categoryChildsAry, 'AdmCategoryChildsAry');
        } else {
            $categoryChildsAry = $zend_cache->load('AdmCategoryChildsAry');
        }

        return $categoryChildsAry;
    }

    //find categories by product ids
    public function getCategoriesByProductIds(array $productIds) {

//        SELECT * FROM category c INNER JOIN product_category pc ON c.category_id = pc.product_category_category_id WHERE pc.product_category_product_id IN(14661)

        $select = $this->getDbTable()
                ->select()
                ->setIntegrityCheck(false)
                ->from(array('c' => 'category'))
                ->join(array('pc' => 'product_category'), 'c.category_id = pc.product_category_category_id')
                ->where('pc.product_category_product_id IN(?)', $productIds);

//        echo $select->__toString();die;

        $rows = $this->getDbTable()->fetchAll($select);

        $entries = array();
        foreach ($rows as $row) {
            $category = new Admin_Model_Category();

            $category->setOptions($row->toArray());

//            echo $category->getCategory_id();die;
            if ($category->getCategory_parent()) {
                $parentStructure = $this->getParents($category->getCategory_parent(), $category->getCategory_name());
                $category->setCategory_structure($parentStructure);
            } else {
                $category->setCategory_structure($category->getCategory_name());
            }


            $entries[$row->product_category_product_id][] = $category;
        }

        return $entries;
    }

}
