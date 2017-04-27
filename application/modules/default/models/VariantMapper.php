<?php

class Default_Model_VariantMapper {

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

        if (!$this->_dbTable instanceof Default_Model_Variant) {

            $this->setDbTable("Default_Model_DbTable_Variant");
        }

        return $this->_dbTable;
    }

    //ziskam doporucene produkty, zatim beru tri produkty, ktere jsou nejvic skladem

    public function getVariantsByProductId($product_id) {
        $select = $this->getDbTable()
                ->select()
                ->setIntegrityCheck(false)
                ->from(array('v' => 'variant'))
                ->joinLeft(array('p' => 'product'), "v.variant_product_id = p.product_id", array('p.product_price'))
                ->where("v.variant_product_id = ?", $product_id)
                ->where("v.variant_is_active = 1")
                ->order("FIELD(v.variant_name, 'XXS', 'XS', 'S', 'M', 'L', 'XL', 'XXL', 'XXXL'), v.variant_name ASC");

//        echo $select->__toString();die;

        $result = $this->getDbTable()->fetchAll($select);

        $entries = array();
        foreach ($result as $row) {
            $variants = new Default_Model_Variant();

            $variants->setOptions($row->toArray());

            if (($row['variant_price'] <= 0) || ($row['variant_price'] == $row['product_price'])) {
                $variants->setVariant_price($row['product_price']);
            }

            $entries[$row['variant_id']] = $variants;
        }

//        print_r($entries);die;
        return $entries;
    }

    //ziskam varianty podle id produktů
    public function getVariantsByProductIds($product_ids) {
        $select = $this->getDbTable()
                ->select()
                ->setIntegrityCheck(false)
                ->from(array('v' => 'variant'))
                ->join(array('p' => 'product'), "v.variant_product_id = p.product_id")
                ->where("p.product_id IN (?)", $product_ids)
                ->where("v.variant_is_active = 1")
                ->order("FIELD(v.variant_name, 'XXS', 'XS', 'S', 'M', 'L', 'XL', 'XXL', 'XXXL'), v.variant_name ASC");

//        echo $select->__toString();die;

        $result = $this->getDbTable()->fetchAll($select);

        $entries = array();
        foreach ($result as $row) {
            $variants = new Default_Model_Variant();

            $variants->setOptions($row->toArray());

            if (($row['variant_price'] <= 0) || ($row['variant_price'] == $row['product_price'])) {
                $variants->setVariant_price($row['product_price']);
            }

            $entries[$row['product_id']][$row['variant_id']] = $variants;
        }

//        print_r($entries);die;
        return $entries;
    }

    //ziskam varianty podle
    public function getVariantsToFeed() {
        $select = $this->getDbTable()
                ->select()
                ->setIntegrityCheck(false)
                ->from(array('p' => 'product'), array('product_id', 'product_name', 'product_code', 'product_url', 'product_category_heureka', 'product_code', 'product_price', 'product_stock'))
                ->joinLeft(array('v' => 'variant'), 'p.product_id = v.variant_product_id')
                ->join(array('ph' => 'photography'), "p.product_id = ph.photography_product_id", array('photography_path'))
                ->joinLeft(array('m' => 'manufacturer'), 'p.product_manufacturer_id = m.manufacturer_id', array('manufacturer_name'))
                ->join(array('pc' => 'product_category'), 'p.product_id = pc.product_category_product_id', array())
                ->join(array('c' => 'category'), 'pc.product_category_category_id = c.category_id', array('category_id', 'category_name', 'category_category_heureka'))
                ->where('p.product_is_active = 1')
                ->where('ph.photography_is_main = 1')
                ->where('p.product_price > 0')
                ->where('c.category_is_active = 1')
                ->group('v.variant_id');
//        $result = $this->getDbTable()->fetchAll($select);

//        echo $select->__toString();die;


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
                $entries[] = $row;
            }
        }
        $conn->close();

        return $entries;
    }

    public function getVariantById($variant_id) {
        $select = $this->getDbTable()
                ->select()
                ->setIntegrityCheck(false)
                ->from(array('v' => 'variant'))
                ->joinLeft(array('p' => 'product'), "v.variant_product_id = p.product_id", array('p.product_price'))
                ->where("v.variant_id = ?", $variant_id)
                ->where("v.variant_is_active = 1");

//        echo $select->__toString();die;

        $row = $this->getDbTable()->fetchRow($select);

        $entries = array();

        if ($row) {
            $variants = new Default_Model_Variant();

            $variants->setOptions($row->toArray());

            if (($row['variant_price'] <= 0) || ($row['variant_price'] == $row['product_price'])) {
                $variants->setVariant_price($row['product_price']);
            }
            $entries = $variants;
        }

        return $entries;
    }

    public function getVariantsByIds($variant_ids) {
        $select = $this->getDbTable()
                ->select()
                ->setIntegrityCheck(false)
                ->from(array('v' => 'variant'))
                ->joinLeft(array('p' => 'product'), "v.variant_product_id = p.product_id", array('p.product_price'))
                ->where("v.variant_id IN(?)", $variant_ids)
                ->where("v.variant_is_active = 1");

//        echo $select->__toString();die;

        $rows = $this->getDbTable()->fetchAll($select);

        $entries = array();

        foreach ($rows as $row) {
            $variants = new Default_Model_Variant();

            $variants->setOptions($row->toArray());

            if (($row['variant_price'] <= 0) || ($row['variant_price'] == $row['product_price'])) {
                $variants->setVariant_price($row['product_price']);
            }

            $entries[] = $variants;
        }


        return $entries;
    }

//    SELECT `v`.*, `pc`.* 
//	FROM 
//		`product` AS `p` 
//	INNER JOIN 
//		`variant` AS `v` 
//	ON 
//		p.product_id = v.variant_product_id 
//	INNER JOIN 
//		`product_category` AS `pc` 
//	ON 
//		p.product_id = pc.product_category_product_id 
//		
//		
//	WHERE 
//		(v.variant_is_active = 1) 
//	AND 
//		(pc.product_category_category_id = 306) 
//	
//	
//	GROUP BY 
//		`v`.`variant_name` 
//	
//		
//	
//	ORDER BY 
//		FIELD(v.variant_name, 'XXS', 'XS', 'S', 'M', 'L', 'XL', 'XXL', 'XXXL'), v.variant_name ASC
    //vyberu varianty do filtru, beru jen ty ktere jsou u nejakeho produktu
    public function getVariantsToFilter($idCategory = 0) {

        $select = $this->getDbTable()
                ->select()
                ->setIntegrityCheck(false)
                ->from(array('p' => 'product'), array())
                ->join(array('v' => 'variant'), 'p.product_id = v.variant_product_id')
                ->join(array('pc' => 'product_category'), 'p.product_id = pc.product_category_product_id')
                ->where('v.variant_is_active = 1')
                ->where('p.product_is_active = 1')
                ->group('v.variant_name')
                ->order("FIELD(v.variant_name, 'XXS', 'XS', 'S', 'M', 'L', 'XL', 'XXL', 'XXXL'), v.variant_name+0 ASC");

        if ($idCategory > 0) {
            $select->where('pc.product_category_category_id IN (?)', $idCategory);
        }

//        echo $select->__toString();
//        die;

        $rows = $this->getDbTable()->fetchAll($select);
        $entries = array();

        foreach ($rows as $row) {

            $variant = new Default_Model_Variant();
            $variant->setOptions($row->toArray());

            $entries[$row['variant_id']] = $variant;
        }

        return $entries;
    }

    public function save(Default_Model_Variant $variant) {

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
//            unset($data['product_insert_date']);
            //projdu ukladana data a pokud obsahuji prazdny retezec nebo null, tak je odstranim, zbytek ulozim
            foreach ($data as $key => $d) {
                if (in_array($d, array("", null)) && $d !== 0) {
                    unset($data[$key]);
                }
            }

            $this->getDbTable()->update($data, array('variant_id = ?' => $id));
        }
    }

}
