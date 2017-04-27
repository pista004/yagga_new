<?php

class Default_Model_ManufacturerMapper {

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

        if (!$this->_dbTable instanceof Default_Model_Manufacturer) {

            $this->setDbTable("Default_Model_DbTable_Manufacturer");
        }

        return $this->_dbTable;
    }

    //ziskam znacky resp. vyrobce

    public function getManufacturers($page = 0, $ipp = -1, $others = array()) {

//      SELECT `m`.*, p.* FROM `manufacturer` AS `m` LEFT JOIN photography p ON m.manufacturer_id = p.photography_manufacturer_id WHERE (m.manufacturer_is_active = 1) ORDER BY `m`.`manufacturer_name` ASC

        $select = $this->getDbTable()
                ->select()
                ->setIntegrityCheck(false)
                ->from(array('m' => 'manufacturer'))
                ->joinLeft(array('ph' => 'photography'), 'm.manufacturer_id = ph.photography_manufacturer_id')
                ->order('m.manufacturer_name ASC')
                ->where('m.manufacturer_is_active = 1');

        if (!empty($others)) {
            if ($others['where']) {
                $select->where($others['where']);
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

            $manufacturer = new Default_Model_Manufacturer();
            $manufacturer->setOptions($row->toArray());

            $photography = new Default_Model_Photography();
            $photography->setOptions($row->toArray());

            $manufacturer->setManufacturer_photography($photography);

            $entries[$row['manufacturer_id']] = $manufacturer;
        }

//        print_r($entries);die;
        return $entries;
    }

    //vyberu znacky do filtru, beru jen ty ktere jsou u nejakeho produktu
    public function getManufacturersToFilter($idCategory = 0) {

        $select = $this->getDbTable()
                ->select()
                ->setIntegrityCheck(false)
                ->from(array('p' => 'product'), array())
                ->join(array('m' => 'manufacturer'), 'p.product_manufacturer_id = m.manufacturer_id')
                ->join(array('pc' => 'product_category'), 'p.product_id = pc.product_category_product_id')
                ->where('m.manufacturer_is_active = 1')
                ->where('p.product_is_active = 1')
                ->group('m.manufacturer_id')
                ->order('m.manufacturer_name');

        if($idCategory > 0){
            $select->where('pc.product_category_category_id IN (?)', $idCategory);
        }

//        echo $select->__toString();
//        die;

        $rows = $this->getDbTable()->fetchAll($select);
        $entries = array();

        foreach ($rows as $row) {

            $manufacturer = new Default_Model_Manufacturer();
            $manufacturer->setOptions($row->toArray());

            $entries[$row['manufacturer_id']] = $manufacturer;
        }

//        print_r($entries);die;
        return $entries;
    }

    public function getManufacturerByUrl($url) {

        $select = $this->getDbTable()
                ->select()
                ->setIntegrityCheck(false)
                ->from(array('m' => 'manufacturer'))
                ->joinLeft(array('ph' => 'photography'), 'm.manufacturer_id = ph.photography_manufacturer_id')
                ->where("m.manufacturer_url = ?", $url)
                ->where("m.manufacturer_is_active = 1");
//        echo $select->__toString();die;

        $row = $this->getDbTable()->fetchRow($select);

        $entries = array();


        $manufacturer = new Default_Model_Manufacturer();
        $photography = new Default_Model_Photography();

        if ($row) {

            $photography->setOptions($row->toArray());
            $manufacturer->setOptions($row->toArray());
            $manufacturer->setManufacturer_photography($photography);

            $entries = $manufacturer;
        }

        return $entries;
    }

}
