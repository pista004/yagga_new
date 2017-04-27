<?php

class Admin_Model_ManufacturerAffiliateManufacturerMapper {

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

        if (!$this->_dbTable instanceof Admin_Model_ManufacturerAffiliateManufacturer) {

            $this->setDbTable("Admin_Model_DbTable_ManufacturerAffiliateManufacturer");
        }

        return $this->_dbTable;
    }

    //find all
    public function getManufacturers($page = 0, $ipp = -1, $others = array()) {

        $select = $this->getDbTable()
                ->select()
                ->from(array('mam' => 'manufacturer_affiliate_manufacturer'));

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
            $manufacturerAffiliateManufacturer = new Admin_Model_ManufacturerAffiliateManufacturer();

            $entries[] = $manufacturerAffiliateManufacturer->setOptions($row->toArray());
        }

        return $entries;
    }
    
    
    
        //find by id - detail
//    SELECT * FROM manufacturer_affiliate_manufacturer mam INNER JOIN manufacturer m ON mam.manufacturer_affiliate_manufacturer_manufacturer_id = m.manufacturer_id WHERE mam.manufacturer_affiliate_manufacturer_id = 1
    public function getManufacturerAffiliateById($id) {

        $select = $this->getDbTable()
                ->select()
                ->setIntegrityCheck(false)
                ->from(array('mam' => 'manufacturer_affiliate_manufacturer'))
                ->joinLeft(array('m' => 'manufacturer'), 'mam.manufacturer_affiliate_manufacturer_manufacturer_id = m.manufacturer_id')
                ->where('mam.manufacturer_affiliate_manufacturer_id = ?', (int) $id);

//        echo $select->__toString();die;

        $row = $this->getDbTable()->getDefaultAdapter()->fetchRow($select);

        if (!$row || 0 == count($row)) {
            return;
        }

//        print_r($row);die;

        $manufacturerAffiliate = new Admin_Model_ManufacturerAffiliateManufacturer();
        $manufacturerAffiliate->setOptions($row);
        
        $manufacturer = new Admin_Model_Manufacturer();
        $manufacturer->setOptions($row);


        $manufacturerAffiliate->setManufacturer($manufacturer);

        return $manufacturerAffiliate;
    }
    
    
    
    

    public function save(Admin_Model_ManufacturerAffiliateManufacturer $manufacturerAffiliateManufacturer) {
        $data = array(
            'manufacturer_affiliate_manufacturer_id' => $manufacturerAffiliateManufacturer->getManufacturer_affiliate_manufacturer_id(),
            'manufacturer_affiliate_manufacturer_name' => $manufacturerAffiliateManufacturer->getManufacturer_affiliate_manufacturer_name(),
            'manufacturer_affiliate_manufacturer_manufacturer_id' => $manufacturerAffiliateManufacturer->getManufacturer_affiliate_manufacturer_manufacturer_id(),
        );

        if (null === ($id = $manufacturerAffiliateManufacturer->getManufacturer_affiliate_manufacturer_id())) {
            unset($data['manufacturer_affiliate_manufacturer_id']);
            $this->getDbTable()->insert($data);
        } else {

            //projdu ukladana data a pokud obsahuji prazdny retezec nebo null, tak je odstranim, zbytek ulozim
            foreach ($data as $key => $d) {
                if (in_array($d, array("", null))) {
                    unset($data[$key]);
                }
            }

            $this->getDbTable()->update($data, array('manufacturer_affiliate_manufacturer_id = ?' => $id));
        }
    }

    public function bulkInsert(array $manufacturerAffiliateManufacturers) {
        if (!empty($manufacturerAffiliateManufacturers)) {
            
            $db = $this->getDbTable()->getAdapter();
            $query = 'INSERT INTO manufacturer_affiliate_manufacturer (manufacturer_affiliate_manufacturer_name) VALUES ';
            $queryVals = array();
            foreach ($manufacturerAffiliateManufacturers as $affiliateManufacturer) {
                $queryVals[] = '('.$db->quote($affiliateManufacturer).')';
            }
//            echo $query . implode(',', $queryVals);die;
            $db->query($query . implode(',', $queryVals));
        }

    }

}
