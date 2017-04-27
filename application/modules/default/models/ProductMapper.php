<?php

class Default_Model_ProductMapper {

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

        if (!$this->_dbTable instanceof Default_Model_Product) {

            $this->setDbTable("Default_Model_DbTable_Product");
        }

        return $this->_dbTable;
    }

    //ziskam doporucene produkty, zatim beru tri produkty, ktere jsou nejvic skladem

    public function getProducts($page = 0, $ipp = -1, $others = array()) {
//TODO - dodelat v dotazu kontrolu zobrazovani data od....
//
//
//        SELECT p.*, ph.* FROM product AS p INNER JOIN photography as ph ON p.product_id = ph.photography_product_id 
//WHERE p.product_is_active = 1 AND ph.photography_is_main = 1

        $select = $this->getDbTable()
                ->select()
                ->setIntegrityCheck(false)
                ->from(array('p' => 'product'))
                ->join(array('ph' => 'photography'), "p.product_id = ph.photography_product_id")
                ->join(array('pc' => 'product_category'), "p.product_id = pc.product_category_product_id")
                ->join(array('c' => 'category'), "pc.product_category_category_id = c.category_id")
                ->joinLeft(array('m' => 'manufacturer'), "p.product_manufacturer_id = m.manufacturer_id")
                ->joinLeft(array('ap' => 'affiliate_program'), 'p.product_affiliate_program_name = ap.affiliate_program_id')
                ->where("p.product_is_active = 1")
                ->where("ph.photography_is_main = 1")
                ->where("c.category_is_active = 1")
                ->group('p.product_id');
//                ->order('p.product_stock DESC');

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
        $paginator->setDefaultPageRange(5);
        $result = $paginator->getCurrentItems();
        $entries = array();

        $this->_paginator = $paginator;

        foreach ($result as $row) {
            $products = new Default_Model_Product();

            $category = new Default_Model_Category();
            $category->setOptions($row->toArray());

            $manufacturer = new Default_Model_Manufacturer();
            $manufacturer->setOptions($row->toArray());

            $photography = new Default_Model_Photography();
            $photography->setOptions($row->toArray());

            $products->setMain_photography($photography);
            $products->setCategory($category);

            $products->setManufacturer($manufacturer);

            $affiliateProgram = new Default_Model_AffiliateProgram();
            $affiliateProgram->setOptions($row->toArray());
            $products->setAffiliate_program($affiliateProgram);

            $entries[$row['product_id']] = $products->setOptions($row->toArray());
        }

//        print_r($entries);die;
        return $entries;
    }

    public function getProductsToFeed($page = 0, $ipp = -1, $others = array()) {

        $select = $this->getDbTable()
                ->select()
                ->setIntegrityCheck(false)
                ->from(array('p' => 'product'), array('product_id', 'product_name', 'product_code', 'product_url', 'product_category_heureka', 'product_code', 'product_price', 'product_stock'))
                ->join(array('ph' => 'photography'), "p.product_id = ph.photography_product_id", array('photography_path'))
                ->join(array('pc' => 'product_category'), "p.product_id = pc.product_category_product_id", array())
                ->join(array('c' => 'category'), "pc.product_category_category_id = c.category_id", array('category_id', 'category_name', 'category_category_heureka'))
                ->joinLeft(array('m' => 'manufacturer'), "p.product_manufacturer_id = m.manufacturer_id", array('manufacturer_name'))
//                ->where("p.product_is_active = 1")
                ->where("ph.photography_is_main = 1")
                ->where("p.product_price > 0")
//                ->where("c.category_is_active = 1")
                ->group('p.product_id')
                ->order('p.product_stock DESC');

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

        echo $select->__toString();
        die;



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
                $entries[$row['product_id']] = $row;
            }
        }
        $conn->close();

        return $entries;
    }

    //ziskam doporucene produkty, zatim beru tri produkty, ktere jsou nejvic skladem

    public function getProductsToSitemap($page = 0, $ipp = -1, $others = array()) {
//TODO - dodelat v dotazu kontrolu zobrazovani data od....
//
//
//        SELECT p.*, ph.* FROM product AS p INNER JOIN photography as ph ON p.product_id = ph.photography_product_id 
//WHERE p.product_is_active = 1 AND ph.photography_is_main = 1

        $select = $this->getDbTable()
                ->select()
                ->setIntegrityCheck(false)
                ->from(array('p' => 'product'), array('p.product_id', 'p.product_url'))
                ->join(array('pc' => 'product_category'), "p.product_id = pc.product_category_product_id", array())
                ->join(array('c' => 'category'), "pc.product_category_category_id = c.category_id", array())
                ->joinLeft(array('m' => 'manufacturer'), "p.product_manufacturer_id = m.manufacturer_id", array())
//                ->where("p.product_is_active = 1")
                ->where("c.category_is_active = 1")
                ->group('p.product_id');

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
        $paginator->setDefaultPageRange(5);
        $result = $paginator->getCurrentItems();
        $entries = array();

        $this->_paginator = $paginator;

        foreach ($result as $row) {

//            $products = new Default_Model_Product();
//
//            $category = new Default_Model_Category();
//            $category->setOptions($row->toArray());
//
//            $manufacturer = new Default_Model_Manufacturer();
//            $manufacturer->setOptions($row->toArray());
//
//            $photography = new Default_Model_Photography();
//            $photography->setOptions($row->toArray());
//
//            $products->setMain_photography($photography);
//            $products->setCategory($category);
//
//            $products->setManufacturer($manufacturer);
//            $entries[$row['product_id']] = $products->setOptions($row->toArray());

            $entries[$row['product_id']] = $row['product_url'];
        }

//        print_r($entries);die;
        return $entries;
    }

    public function getRecommendedProducts($product_id, $page = 0, $ipp = -1, $others = array()) {

        $select1 = $this->getDbTable()
                ->select()
                ->setIntegrityCheck(false)
                ->from(array('pr' => 'product_recommend'))
                ->join(array('p' => 'product'), "pr.product_recommend_id = p.product_id")
                ->join(array('ph' => 'photography'), "p.product_id = ph.photography_product_id")
                ->joinLeft(array('ap' => 'affiliate_program'), 'p.product_affiliate_program_name = ap.affiliate_program_id')
                ->where("pr.product_recommend_product_id = ?", $product_id)
                ->where("p.product_is_active = 1")
                ->where("ph.photography_is_main = 1");

//        echo $select->__toString();
//        die;
//        SELECT * FROM product_recommend pr LEFT JOIN product p ON pr.product_recommend_id = p.product_id WHERE pr.product_recommend_product_id = 31

        $paginator1 = Zend_Paginator::factory($select1);
        $paginator1->setCurrentPageNumber($page);
        $paginator1->setDefaultItemCountPerPage($ipp);
        $result = $paginator1->getCurrentItems();
        $this->_paginator = $paginator1;


        $entries = array();

        if ($paginator1->getTotalItemCount() < 3) {

            $select2 = $this->getDbTable()
                    ->select()
                    ->setIntegrityCheck(false)
                    ->from(array('p' => 'product'))
                    ->join(array('pc' => 'product_category'), "p.product_id = pc.product_category_product_id")
                    ->join(array('c' => 'category'), "pc.product_category_category_id = c.category_id")
                    ->join(array('ph' => 'photography'), "p.product_id = ph.photography_product_id")
                    ->joinLeft(array('m' => 'manufacturer'), "p.product_manufacturer_id = m.manufacturer_id")
                    ->joinLeft(array('ap' => 'affiliate_program'), 'p.product_affiliate_program_name = ap.affiliate_program_id')
//                    ->where("pc.product_category_category_id = ?", new Zend_Db_Expr('(' . $subSql . ')'))
                    ->where("p.product_is_active = 1")
                    ->where("ph.photography_is_main = 1")
                    ->where("p.product_id NOT IN(?)", $product_id);

            if (!empty($others)) {
                if (array_key_exists('where', $others)) {
                    foreach ($others['where'] as $where) {
                        if ($where) {
                            $select2->where($where);
                        }
                    }
                }
            }


            $paginator2 = Zend_Paginator::factory($select2);
            $paginator2->setCurrentPageNumber($page);
            $paginator2->setDefaultItemCountPerPage($ipp);
            $result = $paginator2->getCurrentItems();
            $this->_paginator = $paginator2;


            // pokud vyber podle kategorie a vyrobce je mensi, nez 4, tak vyberu jen podle kategorie
            if ($paginator2->getTotalItemCount() < 4) {

                $select3 = $this->getDbTable()
                        ->select()
                        ->setIntegrityCheck(false)
                        ->from(array('p' => 'product'))
                        ->join(array('pc' => 'product_category'), "p.product_id = pc.product_category_product_id")
                        ->join(array('c' => 'category'), "pc.product_category_category_id = c.category_id")
                        ->join(array('ph' => 'photography'), "p.product_id = ph.photography_product_id")
                        ->joinLeft(array('m' => 'manufacturer'), "p.product_manufacturer_id = m.manufacturer_id")
                        ->joinLeft(array('ap' => 'affiliate_program'), 'p.product_affiliate_program_name = ap.affiliate_program_id')
//                    ->where("pc.product_category_category_id = ?", new Zend_Db_Expr('(' . $subSql . ')'))
                        ->where("p.product_is_active = 1")
                        ->where("ph.photography_is_main = 1")
                        ->where("p.product_id NOT IN(?)", $product_id);


                if (!empty($others)) {
                    if (array_key_exists('where', $others)) {
                        foreach ($others['where'] as $key => $where) {
                            if ($key != "manufacturer") {
                                if ($where) {
                                    $select3->where($where);
                                }
                            }
                        }
                    }
                }


                $paginator3 = Zend_Paginator::factory($select3);
                $paginator3->setCurrentPageNumber($page);
                $paginator3->setDefaultItemCountPerPage($ipp);
                $result = $paginator3->getCurrentItems();
                $this->_paginator = $paginator3;
            }
        }






        foreach ($result as $row) {
            $products = new Default_Model_Product();

            $photography = new Default_Model_Photography();
            $photography->setOptions($row->toArray());
            $products->setMain_photography($photography);

            $affiliateProgram = new Default_Model_AffiliateProgram();
            $affiliateProgram->setOptions($row->toArray());
            $products->setAffiliate_program($affiliateProgram);

            $entries[$row['product_id']] = $products->setOptions($row->toArray());
        }


//        print_r($entries);
//        die;
        return $entries;
    }

    public function getProductByUrl($url) {
        $select = $this->getDbTable()
                ->select()
                ->setIntegrityCheck(false)
                ->from(array('p' => 'product'))
                ->joinLeft(array('p2' => 'product'), "p.product_itemgroup_product_id = p2.product_id", array())
                ->joinLeft(array('p3' => 'product'), "p.product_id = p3.product_itemgroup_product_id", array('COUNT(p3.product_id) AS variants_count', 'SUM(p3.product_stock) AS variants_sum_stock'))
                ->joinLeft(array('pc' => 'product_category'), "p.product_id = pc.product_category_product_id OR p2.product_id = pc.product_category_product_id")
                ->joinLeft(array('c' => 'category'), "pc.product_category_category_id = c.category_id")
                ->join(array('ph' => 'photography'), "p.product_id = ph.photography_product_id OR p2.product_id = ph.photography_product_id")
                ->joinLeft(array('m' => 'manufacturer'), "p.product_manufacturer_id = m.manufacturer_id OR p2.product_manufacturer_id = m.manufacturer_id")
                ->joinLeft(array('ph2' => 'photography'), "m.manufacturer_id = ph2.photography_manufacturer_id", array('ph2.photography_path AS photography_manufacturer'))
                ->where('p.product_url = ?', $url)
                ->where('ph.photography_is_main = 1')
                ->where('c.category_is_active = 1')
                ->where('p.product_price > 0')
                ->group('p.product_id')
                ->order("c.category_id DESC")
                ->order("c.category_order ASC")
                ->limit(1);

//                ->where("p.product_is_active = 1");
//        echo $select->__toString();
//        die;

        $row = $this->getDbTable()->fetchRow($select);

        $entries = array();


        $products = new Default_Model_Product();

        if ($row) {
            $photography = new Default_Model_Photography();
            $photography->setOptions($row->toArray());
            $products->setMain_photography($photography);

            $category = new Default_Model_Category();
            $category->setOptions($row->toArray());
            $products->setCategory($category);


            $manufacturer = new Default_Model_Manufacturer();
            $manufacturer->setOptions($row->toArray());

            $photographyManufacturer = new Default_Model_Photography();
            $photographyManufacturer->setPhotography_path($row['photography_manufacturer']);

            $manufacturer->setManufacturer_photography($photographyManufacturer);

            $products->setManufacturer($manufacturer);
//            print_r($products);die;
            $entries[$row['product_id']] = $products->setOptions($row->toArray());
        }
//        print_r($entries);die;
        return $entries;
    }

    public function getProductById($ids) {
        
//        TODO - upravit, at se berou i varianty i produkty, momentalne haze chybovou stranku
        
        $select = $this->getDbTable()
                ->select()
                ->setIntegrityCheck(false)
                ->from(array('p' => 'product'))
//                ->join(array('ph' => 'photography'), "p.product_id = ph.photography_product_id")
                ->where("p.product_id IN(?)", $ids);
//                ->where("ph.photography_is_main = 1")
//                ->where("p.product_is_active = 1");
//echo $select->_toString();die;
        $result = $this->getDbTable()->fetchAll($select);

        $entries = array();


        foreach ($result as $row) {

            $products = new Default_Model_Product();

            $photography = new Default_Model_Photography();
            $photography->setOptions($row->toArray());
            $products->setMain_photography($photography);

            $entries[$row['product_id']] = $products->setOptions($row->toArray());
        }
        return $entries;
    }

    
    
    
    public function getVariantsByItemgroupId($itemgroupId) {
        $select = $this->getDbTable()
                ->select()
                ->setIntegrityCheck(false)
                ->from(array('p' => 'product'))
                ->where('p.product_itemgroup_product_id = ?', $itemgroupId)
                ->where('p.product_price > 0')
                ->where('p.product_is_active = 1');

//        echo $select->__toString();die;

        $result = $this->getDbTable()->fetchAll($select);

        $entries = array();

        foreach ($result as $row) {

            $products = new Default_Model_Product();

            $entries[$row['product_id']] = $products->setOptions($row->toArray());
        }
        
//        print_r($entries);die;
        return $entries;
        
    }
    
    
    
    
    
    public function getProductsByCategoryIds($categoryIds, $where = array(), $order = array(), $page = 0, $ipp = -1) {

        $select = $this->getDbTable()
                ->select()
                ->setIntegrityCheck(false)
                ->from(array('p' => 'product'))
                ->joinLeft(array('pc' => 'product_category'), 'p.product_id = pc.product_category_product_id')
                ->joinLeft(array('c' => 'category'), 'c.category_id = pc.product_category_category_id')
                ->joinLeft(array('ph' => 'photography'), 'ph.photography_product_id = p.product_id')
                ->joinLeft(array('m' => 'manufacturer'), 'p.product_manufacturer_id = m.manufacturer_id')
                ->joinLeft(array('v' => 'variant'), 'v.variant_product_id = p.product_id')
                ->joinLeft(array('ap' => 'affiliate_program'), 'p.product_affiliate_program_name = ap.affiliate_program_id')
                ->where('p.product_is_active = 1')
                ->where('c.category_is_active = 1')
                ->where('ph.photography_is_main = 1')
                ->where('pc.product_category_category_id IN(?)', $categoryIds)
                ->group('p.product_id');

//        print_r($where);die;
        if (!empty($where)) {
            if (count($where) > 0) {
                foreach ($where as $whereKey => $whereItems) {

                    /*
                     * Specialni podminka pro varianty - do budoucna predelat, jak DB, tak kod
                     */
                    if ($whereKey == 'variant_id') {

                        $selectVariant = $this->getDbTable()
                                ->select()
                                ->setIntegrityCheck(false)
                                ->from(array('v' => 'variant'), array('variant_name'))
                                ->where('v.variant_id IN (?) ', $whereItems);

                        $select->where('v.variant_name IN (?) ', $selectVariant)
                                ->where('v.variant_stock > 0');
                    } else {
                        $select->where($whereKey . ' IN(?) ', $whereItems);
                    }
                }
            }
        }


        if (!empty($order)) {
            if (count($order) > 0) {
                foreach ($order as $orderKey => $orderItem) {
                    $select->order($orderKey . ' ' . $orderItem);
                }
            }
        }

//        echo $select->__toString() . "<br />";
//        die;

        $paginator = Zend_Paginator::factory($select);
        $paginator->setCurrentPageNumber($page);
        $paginator->setDefaultItemCountPerPage($ipp);
        $paginator->setDefaultPageRange(5);
        $result = $paginator->getCurrentItems();

        $entries = array();

        $this->_paginator = $paginator;

        foreach ($result as $row) {
            $products = new Default_Model_Product();

            $photography = new Default_Model_Photography();
            $photography->setOptions($row->toArray());
            $products->setMain_photography($photography);

            $affiliateProgram = new Default_Model_AffiliateProgram();
            $affiliateProgram->setOptions($row->toArray());
            $products->setAffiliate_program($affiliateProgram);

            $entries[$row['product_id']] = $products->setOptions($row->toArray());
        }

//        print_r($entries);die;
        return $entries;
    }

    public function save(Default_Model_Product $product) {

        $data = array(
            'product_id' => $product->getProduct_id(),
            'product_name' => $product->getProduct_name(),
            'product_stock' => $product->getProduct_stock(),
            'product_purchase_price' => $product->getProduct_purchase_price(),
            'product_recommended_price' => $product->getProduct_recommended_price(),
            'product_price' => $product->getProduct_price(),
            'product_perex' => $product->getProduct_perex(),
            'product_code' => $product->getProduct_code(),
            'product_ean' => $product->getProduct_ean(),
            'product_url' => $product->getProduct_url(),
            'product_seo_title' => $product->getProduct_seo_title(),
            'product_seo_meta_description' => $product->getProduct_seo_meta_description(),
            'product_description' => $product->getProduct_description(),
            'product_insert_date' => $product->getProduct_insert_date(),
            'product_active_from_date' => $product->getProduct_active_from_date(),
            'product_expire_date' => $product->getProduct_expire_date(),
            'product_is_active' => $product->getProduct_is_active(),
            'product_manufacturer_id' => $product->getProduct_manufacturer_id(),
        );

        if (null === ($id = $product->getProduct_id())) {
            unset($data['product_id']);
            $this->getDbTable()->insert($data);
        } else {
//            unset($data['product_insert_date']);
            //projdu ukladana data a pokud obsahuji prazdny retezec nebo null, tak je odstranim, zbytek ulozim
            foreach ($data as $key => $d) {
                if (in_array($d, array("", null)) && $d !== 0) {
                    unset($data[$key]);
                }
            }

            $this->getDbTable()->update($data, array('product_id = ?' => $id));
        }
    }

//    //find by id - detail
//    public function find($id) {
//
//        $select = $this->getDbTable()
//                ->select()
//                ->from(array('p' => 'product'))
//                ->where('p.product_id = ?', (int) $id);
//
//        $row = $this->getDbTable()->getDefaultAdapter()->fetchRow($select);
//
//        if (!$row) {
//            return;
//        }
//
//        if (0 == count($row)) {
//            return;
//        }
//
//        $entries = array();
//        $product = new Admin_Model_Product();
//        $product->setOptions($row);
//        $entries = $product;
//
//        return $entries;
//    }
}
