<?php

class Default_Model_PhotographyMapper {

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

        if (!$this->_dbTable instanceof Default_Model_Photography) {

            $this->setDbTable("Default_Model_DbTable_Photography");
        }

        return $this->_dbTable;
    }

    //vyberu fotografie krome main fotografie
    public function getPhotographiesByProductId($product_id) {
        $select = $this->getDbTable()
                ->select()
                ->setIntegrityCheck(false)
                ->from(array('ph' => 'photography'))
                ->where("ph.photography_product_id = ?", $product_id)
                ->where("ph.photography_is_main = 0 OR ph.photography_is_main IS NULL");

//        echo $select->__toString();die;

        $result = $this->getDbTable()->fetchAll($select);

        $entries = array();

        foreach ($result as $row) {

            $photography = new Default_Model_Photography();
            $photography->setOptions($row->toArray());

            $entries[] = $photography;
        }

        return $entries;
    }

    
    
    //vyberu vsechny fotografie s group by podle produktu
    public function getPhotographiesByProductsIds($product_ids) {
        $select = $this->getDbTable()
                ->select()
                ->setIntegrityCheck(false)
                ->from(array('ph' => 'photography'))
                ->where("ph.photography_product_id IN (?)", $product_ids)
                ->where("ph.photography_is_main = 0 OR ph.photography_is_main IS NULL");

//        echo $select->__toString();die;

        $result = $this->getDbTable()->fetchAll($select);

        $entries = array();

        foreach ($result as $row) {

            $photography = new Default_Model_Photography();
            $photography->setOptions($row->toArray());

            $entries[$row['photography_product_id']][$row['photography_id']] = $photography;
        }

        return $entries;
    }
    
    
    
    
    
    public function getPhotographiesByProductsIdsToFeed($product_ids) {
        $select = $this->getDbTable()
                ->select()
                ->setIntegrityCheck(false)
                ->from(array('ph' => 'photography'))
                ->where("ph.photography_product_id IN (?)", $product_ids)
                ->where("ph.photography_is_main = 0 OR ph.photography_is_main IS NULL");

//        echo $select->__toString();die;

        $result = $this->getDbTable()->fetchAll($select);

        $entries = array();

        foreach ($result as $row) {

//            $photography = new Default_Model_Photography();
//            $photography->setOptions($row->toArray());

            $entries[$row['photography_product_id']][$row['photography_id']] = $row->toArray();
        }

        return $entries;
    }
    
    
    
    
    
    
    
    
    
}
