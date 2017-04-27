<?php

class Admin_Model_VariantMapper {

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

        if (!$this->_dbTable instanceof Admin_Model_Variant) {

            $this->setDbTable("Admin_Model_DbTable_Variant");
        }

        return $this->_dbTable;
    }

    public function save(Admin_Model_Variant $variant) {

        $data = array(
            'variant_id' => $variant->getVariant_id(),
            'variant_name' => $variant->getVariant_name(),
            'variant_stock' => $variant->getVariant_stock(),
            'variant_purchase_price' => $variant->getVariant_purchase_price(),
            'variant_price' => $variant->getVariant_price(),
            'variant_is_active' => $variant->getVariant_is_active(),
            'variant_product_id' => $variant->getVariant_product_id(),
        );

        if (null === ($id = $variant->getVariant_id())) {
            unset($data['variant_id']);
            $this->getDbTable()->insert($data);
        } else {
            
//projdu ukladana data a pokud obsahuji prazdny retezec nebo null, tak je odstranim, zbytek ulozim
            foreach ($data as $key => $d) {
                if (in_array($d, array("", null)) && $d !== 0) {
                    unset($data[$key]);
                }
            }
            
            $this->getDbTable()->update($data, array('variant_id = ?' => $id));
        }
    }

    //find by id - detail
    public function getVariantById($id) {

        $select = $this->getDbTable()
                ->select()
                ->from(array('v' => 'variant'))
                ->where('v.variant_id = ?', (int) $id);

        $row = $this->getDbTable()->getDefaultAdapter()->fetchRow($select);

        if (!$row || 0 == count($row)) {
            return;
        }

        $entries = array();
        $variant = new Admin_Model_Variant();
        $variant->setOptions($row);
        $entries = $variant;

        return $entries;
    }

    //ziskam vsechny varianty k produktu
    public function getVariantsByProductId($product_id) {

        $select = $this->getDbTable()
                ->select()
                ->from(array('v' => 'variant'))
                ->where('v.variant_product_id = ?', $product_id);


        $result = $this->getDbTable()->fetchAll($select);
        $entries = array();

        foreach ($result as $row) {
            $products = new Admin_Model_Variant();
            $entries[$row['variant_id']] = $products->setOptions($row->toArray());
        }

        return $entries;
    }

    //ziskam vsechny varianty k produktu, ktere jsou aktivni/neaktivni - defaultne aktivni
    public function getVariantsByProductIdIsActive($product_id, $is_active = 1) {

        $select = $this->getDbTable()
                ->select()
                ->from(array('v' => 'variant'))
                ->where('v.variant_product_id = ?', $product_id)
                ->where('v.variant_is_active = ?', $is_active);


        $result = $this->getDbTable()->fetchAll($select);
        $entries = array();

        foreach ($result as $row) {
            $products = new Admin_Model_Variant();
            $entries[$row['variant_id']] = $products->setOptions($row->toArray());
        }

        return $entries;
    }

    public function delete($id) {
        $dbAdapter = $this->getDbTable()->getDefaultAdapter();
        $where = $dbAdapter->quoteInto('variant_id = ?', (int) $id);
        $dbAdapter->delete("variant", $where);
    }

    public function deleteByProductIds($productIds) {
        if (!empty($productIds)) {
            $dbAdapter = $this->getDbTable()->getDefaultAdapter();
            $where = $dbAdapter->quoteInto('variant_product_id IN (?)', $productIds);
            $dbAdapter->delete("variant", $where);
        }
    }

    public function bulkInsert(array $variants) {

        if (!empty($variants)) {

            $db = $this->getDbTable()->getAdapter();
            $query = 'INSERT INTO variant (' .
                    'variant_name, ' .
                    'variant_product_id, ' .
                    'variant_stock, ' .
                    'variant_purchase_price, ' .
                    'variant_price, ' .
                    'variant_is_active' .
                    ') VALUES ';
            $queryVals = array();
            foreach ($variants as $variant) {
                $queryVals[] = '(' .
                        $db->quote($variant['name']) . ', ' .
                        $db->quote($variant['product_id']) . ', ' .
                        $db->quote($variant['stock']) . ', ' .
                        $db->quote($variant['purchase_price']) . ', ' .
                        $db->quote($variant['price']) . ', ' .
                        $db->quote($variant['is_active']) . ')';
            }

//            echo $query . implode(',', $queryVals);
//            die;

            $db->query($query . implode(',', $queryVals));
        }
    }

    public function resetAutoincrementInsert() {

        $db = $this->getDbTable()->getAdapter();
        $query = 'ALTER TABLE variant AUTO_INCREMENT = 1';

        $db->query($query);
    }

    
    
    public function getVariantsByAffiliateProgramName($affiliateProgramName) {

        $select = $this->getDbTable()
                ->select()
                ->setIntegrityCheck(false)
                ->from(array('p' => 'product'),array('p.product_id', 'product_code'))
                ->join(array('v' => 'variant'), 'p.product_id = v.variant_product_id', array('v.variant_id', 'v.variant_name', 'v.variant_stock'))
                ->join(array('ap' => 'affiliate_program'), 'p.product_affiliate_program_name = ap.affiliate_program_id', array())
                ->where('ap.affiliate_program_name = ?', $affiliateProgramName)
                ->where('p.product_is_active = 1');

        
        
        
                $bootstrap = Zend_Controller_Front::getInstance()->getParam('bootstrap');
        $resource = $bootstrap->getResource('db')->getConfig();

        $servername = $resource['host'];
        $username = $resource['username'];
        $password = $resource['password'];
        $dbname = $resource['dbname'];

// Create connection
        $conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        mysqli_set_charset($conn, "utf8");
        $result = $conn->query($select);

        $entries = array();

        if ($result->num_rows > 0) {

            foreach ($result as $row) {
//                print_r($row);
                $entries[$row['product_code']][$row['variant_name']] = $row;
            }
        }

        $conn->close();
        return $entries;
        
        
        
        
        
        
        
        
        
//        $result = $this->getDbTable()->fetchAll($select);
//
//        $entries = array();
//
//        foreach ($result as $row) {
//print_r($row);die;
//            $entries[$row['product_code']] = $row;
//        }
//
//        return $entries;
    }
    
    
    
    
    
}
