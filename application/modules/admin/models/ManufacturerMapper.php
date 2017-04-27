<?php

class Admin_Model_ManufacturerMapper {

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

        if (!$this->_dbTable instanceof Admin_Model_Manufacturer) {

            $this->setDbTable("Admin_Model_DbTable_Manufacturer");
        }

        return $this->_dbTable;
    }

    //ziskam vsechny vyrobce    
    public function getManufacturers($page = 0, $ipp = -1) {

        $select = $this->getDbTable()
                ->select()
                ->setIntegrityCheck(false)
                ->from(array('m' => 'manufacturer'))
                ->joinLeft(array('p' => 'photography'), 'm.manufacturer_id = p.photography_manufacturer_id')
                ->order('m.manufacturer_name');

        $paginator = Zend_Paginator::factory($select);
        $paginator->setCurrentPageNumber($page);
        $paginator->setDefaultItemCountPerPage($ipp);
        $result = $paginator->getCurrentItems();

        $entries = array();

        $this->_paginator = $paginator;

        foreach ($result as $row) {
            $manufacturer = new Admin_Model_Manufacturer();

            $photography = new Admin_Model_Photography();
            $photography->setOptions($row->toArray());

            $manufacturer->setManufacturer_photography($photography);

            $entries[$row['manufacturer_id']] = $manufacturer->setOptions($row->toArray());
        }

        return $entries;
    }

    //find by id - detail
    public function getManufacturerById($id) {

        $select = $this->getDbTable()
                ->select()
                ->setIntegrityCheck(false)
                ->from(array('m' => 'manufacturer'))
                ->joinLeft(array('p' => 'photography'), 'm.manufacturer_id = p.photography_manufacturer_id')
                ->where('m.manufacturer_id = ?', (int) $id);

//        echo $select->__toString();die;

        $row = $this->getDbTable()->getDefaultAdapter()->fetchRow($select);

        if (!$row || 0 == count($row)) {
            return;
        }

//        print_r($row);die;

        $manufacturer = new Admin_Model_Manufacturer();
        $manufacturer->setOptions($row);

        $photography = new Admin_Model_Photography();
        $photography->setOptions($row);
        $manufacturer->setManufacturer_photography($photography);


        return $manufacturer;
    }

    public function save(Admin_Model_Manufacturer $manufacturer) {

        $data = array(
            'manufacturer_id' => $manufacturer->getManufacturer_id(),
            'manufacturer_name' => $manufacturer->getManufacturer_name(),
            'manufacturer_url' => $manufacturer->getManufacturer_url(),
            'manufacturer_note' => $manufacturer->getManufacturer_note(),
            'manufacturer_seo_title' => $manufacturer->getManufacturer_seo_title(),
            'manufacturer_seo_meta_description' => $manufacturer->getManufacturer_seo_meta_description(),
            'manufacturer_is_active' => $manufacturer->getManufacturer_is_active(),
        );

        if (null === ($id = $manufacturer->getManufacturer_id())) {
            unset($data['manufacturer_id']);
            $this->getDbTable()->insert($data);
        } else {

            //projdu ukladana data a pokud obsahuji prazdny retezec nebo null, tak je odstranim, zbytek ulozim
            foreach ($data as $key => $d) {
                if (in_array($d, array("", null)) && $d !== 0) {
                    unset($data[$key]);
                }
            }

            $this->getDbTable()->update($data, array('manufacturer_id = ?' => $id));
        }
    }

    public function bulkInsert(array $manufacturers) {
        if (!empty($manufacturers)) {

            $db = $this->getDbTable()->getAdapter();
            $query = 'INSERT INTO manufacturer (manufacturer_name, manufacturer_url, manufacturer_is_active) VALUES ';
            $queryVals = array();
            foreach ($manufacturers as $manufacturer) {
                $queryVals[] = '(' . $db->quote($manufacturer->getManufacturer_name()) . ', '. $db->quote($manufacturer->getManufacturer_url()) .', ' .$db->quote($manufacturer->getManufacturer_is_active()). ')';
            }

//            echo $query . implode(',', $queryVals);die;
            
            $db->query($query . implode(',', $queryVals));
        }
    }

    public function delete($id) {
        $dbAdapter = $this->getDbTable()->getDefaultAdapter();
        $where = $dbAdapter->quoteInto('manufacturer_id = ?', (int) $id);
        $dbAdapter->delete("manufacturer", $where);
    }

    public function urlExists($url) {

        $existsUrl = false;
        // kontrola, zda zaznam existuje, pokud ano, vracim true, jinak false
        if ($url != "") {
            $select = $this->getDbTable()
                    ->select()
                    ->from(array('m' => 'manufacturer'), array('row_count' => 'COUNT(1)'))
                    ->where('m.manufacturer_url = ?', $url);

            $row = $this->getDbTable()->fetchRow($select);

            if ($row['row_count']) {
                $existsUrl = true;
            }
        }

        return $existsUrl;
    }

}
