<?php

class Default_Model_CategoryMapper {

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

        if (!$this->_dbTable instanceof Default_Model_Category) {

            $this->setDbTable("Default_Model_DbTable_Category");
        }

        return $this->_dbTable;
    }

    
    
    
    
    public function getCategories2($page = 0, $ipp = -1) {

        $select = $this->getDbTable()
                ->select()
                ->from(array('c' => 'category'))
                ->order('c.category_order DESC')
                ->where('c.category_is_active = 1');

        $paginator = Zend_Paginator::factory($select);
        $paginator->setCurrentPageNumber($page);
        $paginator->setDefaultItemCountPerPage($ipp);
        $result = $paginator->getCurrentItems();
        $entries = array();

        $this->_paginator = $paginator;

        foreach ($result as $row) {
            $category = new Default_Model_Category();

            $entries[$row['category_id']] = $category->setOptions($row->toArray());
        }

        return $entries;
    }
    
    
    
    
    
    
    //find all
    public function getCategories($page = 0, $ipp = -1) {

        $select = $this->getDbTable()
                ->select()
                ->from(array('c' => 'category'))
                ->order('c.category_order DESC')
                ->where('c.category_is_active = 1');

        $paginator = Zend_Paginator::factory($select);
        $paginator->setCurrentPageNumber($page);
        $paginator->setDefaultItemCountPerPage($ipp);
        $result = $paginator->getCurrentItems();
        $entries = array();

        $this->_paginator = $paginator;

        foreach ($result as $row) {
            $category = new Default_Model_Category();

            $entries[$row['category_id']] = $category->setOptions($row->toArray());

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
        $category = new Default_Model_Category();
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

    //podle id kategorie ziskam pole objektu s nadrazenymi kategoriemi - rodici - poiziti napr pro breadcrumbs
    public function getParentsObjects($parent, $structure = array()) {

        if (!$parent) {
            return $structure;
        }

        $select = $this->getDbTable()
                ->select()
                ->from(array('c' => 'category'))
                ->where('c.category_id = ?', $parent)
                ->order('c.category_order DESC');

        $row = $this->getDbTable()->fetchRow($select);

        $obj = new Default_Model_Category();
        $obj->setOptions($row->toArray());

        if (!empty($structure)) {
            $obj->setCategory_childs($structure);
        }
        $structure = array($obj);

        if ($obj->getCategory_parent()) {
            return $this->getParentsObjects($obj->getCategory_parent(), $structure);
        }

        return $structure;
    }

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

        $structure = $row['category_name'] . " | " . $structure;

        if ($row['category_parent']) {
            return $this->getParents($row['category_parent'], $structure);
        }

        return $structure;
    }

    //prevedu objekt se zanorenim - kategorie -> podkategorie do jednorozmerneho pole
    public function setParentsToAry($categories, $ary = array(), $result = array()) {

        foreach ($categories as $category) {

            $childs = $category->getCategory_childs();
            $ary[] = $category;

            $category->setCategory_childs(array());
            $result[] = $category;
            if (!empty($childs)) {
                return $this->setParentsToAry($childs, $ary, $result);
            }
        }

        return $result;
    }

    public function getParentsIds($id, $structure = array()) {

        if (!$id) {
            return $structure;
        }

        $select = $this->getDbTable()
                ->select()
                ->from(array('c' => 'category'))
                ->where('c.category_id = ?', $id);

        $row = $this->getDbTable()->fetchRow($select);

        $structure[] = $row['category_id'];

        if ($row['category_parent']) {
            return $this->getParentsIds($row['category_parent'], $structure);
        }

        return $structure;
    }

    //rekurze, postupne ziskam vsechny rodice url - napr elektro/pc/notebooky
    public function getParentsUrl($parent, $structure) {

        if (!$parent) {
            return $structure;
        }

        $select = $this->getDbTable()
                ->select()
                ->from(array('c' => 'category'))
                ->where('c.category_id = ?', $parent)
                ->order('c.category_order DESC');

        $row = $this->getDbTable()->fetchRow($select);

        $structure = $row['category_url'] . "/" . $structure;

        if ($row['category_parent']) {
            return $this->getParentsUrl($row['category_parent'], $structure);
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
                ->where('c.category_is_active = 1')
                ->order('c.category_order DESC');

        $rows = $this->getDbTable()->fetchAll($select);

        $entries = array();
        foreach ($rows as $row) {
            $category = new Default_Model_Category();

            $category->setOptions($row->toArray());

//            $category->setCategory_url($this->getParentsUrl($category->getCategory_parent(), $category->getCategory_url()));


            if ($category->getCategory_id()) {
                $category->setCategory_childs($this->getChilds($category->getCategory_id()));
            }
            $entries[] = $category;
        }

        return $entries;
    }

    public function save(Default_Model_Category $category) {
        $data = array(
            'category_id' => $category->getCategory_id(),
            'category_name' => $category->getCategory_name(),
            'category_description' => $category->getCategory_description(),
            'category_parent' => $category->getCategory_parent(),
            'category_url' => $category->getCategory_url(),
            'category_seo_title' => $category->getCategory_seo_title(),
            'category_seo_meta_description' => $category->getCategory_seo_meta_description(),
            'category_is_active' => $category->getCategory_is_active(),
        );

        if (null === ($id = $category->getCategory_id())) {
            unset($data['category_id']);
            $this->getDbTable()->insert($data);
        } else {
            $this->getDbTable()->update($data, array('category_id = ?' => $id));
        }
    }

    //smaze kategorie, vstup je pole, protoze mazu i subkategorie
    public function delete($ids) {
        $dbAdapter = $this->getDbTable()->getDefaultAdapter();

        $where = $dbAdapter->quoteInto('category_id IN(?)', $ids);
        $dbAdapter->delete("category", $where);
    }

    //z potomku ziskam jen id potomku, pouzito pro mazani podkategorii
    public function getCategoryChildsIds($childs) {

        if (!is_array($childs)) {
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

            $current_child = new Default_Model_Category();
            $current_child = clone $child;
            $current_child->setCategory_childs(array());
            $current_child->setCategory_url($this->getParentsUrl($current_child->getCategory_parent(), $current_child->getCategory_url()));
            $current_child->setCategory_level($n);
            $current_child->setCategory_structure($this->getParents($current_child->getCategory_parent(), $current_child->getCategory_name()));

            $this->_childsAry[] = $current_child;

            if (is_array($child->getCategory_childs())) {
                if (count($child->getCategory_childs()) > 0) {
                    $num = $n + 1;
                    $this->getCategoryChildsAry($child->getCategory_childs(), $num);
                }
            }
        }

        return $this->_childsAry;
    }

    //vraci pole, kde klic je id kategorie a hodnota je path url kategorie
    public function getCategoriesUrlsAry() {

//        TODO cache urcite dodelat a doladit s pridavanim kategorii, protoze kdyz se prida kategorie, tak neni dostupna, protoze nebyla nactena, cache...
        $frontendOptions = array(
            'lifetime' => 3600 * 12, // cache lifetime 24 hodin
            'automatic_serialization' => true,
            'automatic_cleaning_factor' => 50
        );
        $backendOptions = array(
            'cache_dir' => '../cache' // Directory where to put the cache files
        );

// getting a Zend_Cache_Core object
        $zend_cache = Zend_Cache::factory('Core', 'File', $frontendOptions, $backendOptions);

        $categoryUrls = array();

//k nacitani informaci o pobockach ulozenky pouzivam cache - nepotrebuju nacitat porad
        if (!$categoryUrls = $zend_cache->load('CategoryUrls')) {

            $categories = $this->getChilds(0, true);

            $categoriesAry = $this->getCategoryChildsAry($categories);

            $categoryUrls = array();
            foreach ($categoriesAry as $category) {
                if ($category instanceof Default_Model_Category) {
                    if ($category->getCategory_is_active() == 1) {
                        $categoryUrls[$category->getCategory_id()] = $category->getCategory_url();
                    }
                }
            }

            $zend_cache->save($categoryUrls, 'CategoryUrls');
        } else {
            $categoryUrls = $zend_cache->load('CategoryUrls');
        }

        return $categoryUrls;
    }

    //find cateogry by manufacturers
    public function getCategoriesByManufacturer($where = array()) {

//        SELECT DISTINCT(c.category_id), c.* FROM category c 
//	INNER JOIN product_category pc ON c.category_id = pc.product_category_category_id
//	INNER JOIN product p ON pc.product_category_product_id = p.product_id
//	INNER JOIN manufacturer m ON p.product_manufacturer_id = m.manufacturer_id 
//	WHERE m.manufacturer_id = 257 AND m.manufacturer_is_active = 1 AND c.category_is_active = 1

        $select = $this->getDbTable()
                ->select()
                ->setIntegrityCheck(false)
                ->from(array('c' => 'category'))
                ->join(array('pc' => 'product_category'), 'c.category_id = pc.product_category_category_id')
                ->join(array('p' => 'product'), 'pc.product_category_product_id = p.product_id')
                ->join(array('m' => 'manufacturer'), 'p.product_manufacturer_id = m.manufacturer_id')
                ->where('m.manufacturer_is_active = 1')
                ->where('c.category_is_active = 1')
                ->group('c.category_id')
                ->order('c.category_order DESC');


        if (!empty($where)) {
            if (count($where) > 0) {
                foreach ($where as $whereKey => $whereItems) {
                    $select->where($whereKey . ' IN(?) ', $whereItems);
                }
            }
        }


        $result = $this->getDbTable()->fetchAll($select);
//echo $select->__toString();die;
//        print_r($result);die;
        $entries = array();

        foreach ($result as $row) {
            $category = new Default_Model_Category();


            $entries[$row['category_id']] = $category->setOptions($row->toArray());

//            $category->setCategory_structure($this->getParents($category->getCategory_parent(), $category->getCategory_name()));
//            $category->setCategory_structure($this->getParents($category->getCategory_parent()));
        }

        return $entries;
    }

}
