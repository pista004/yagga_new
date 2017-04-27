<?php

class Admin_Model_ProductMapper {

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

        if (!$this->_dbTable instanceof Admin_Model_Product) {

            $this->setDbTable("Admin_Model_DbTable_Product");
        }

        return $this->_dbTable;
    }

    //update product stock podle poctu kusu skladem ve variantach
    public function setProductStockFromVariantStocksByProductId($product_id) {

        $select = $this->getDbTable()
                ->select()
                ->setIntegrityCheck(false)
                ->from(array('v' => 'variant'), array('SUM(v.variant_stock) as variants_stock'))
                ->where('v.variant_product_id = ?', $product_id)
                ->where('v.variant_is_active = ?', 1);

        $row = $this->getDbTable()->fetchRow($select);

        if ($row['variants_stock'] > 0) {
            $product = new Admin_Model_Product();
            $product->setProduct_id($product_id);
            $product->setProduct_stock($row['variants_stock']);
            $this->save($product);
        }
    }

    public function save(Admin_Model_Product $product, $rows_to_update = array()) {

        $data = array(
            'product_id' => $product->getProduct_id(),
            'product_name' => $product->getProduct_name(),
            'product_variant_name' => $product->getProduct_variant_name(),
            'product_stock' => $product->getProduct_stock(),
            'product_purchase_price' => $product->getProduct_purchase_price(),
            'product_recommended_price' => $product->getProduct_recommended_price(),
            'product_price' => $product->getProduct_price(),
            'product_perex' => $product->getProduct_perex(),
            'product_code' => $product->getProduct_code(),
            'product_ean' => $product->getProduct_ean(),
            'product_url' => $product->getProduct_url(),
            'product_affiliate_url' => $product->getProduct_affiliate_url(),
            'product_seo_title' => $product->getProduct_seo_title(),
            'product_seo_meta_description' => $product->getProduct_seo_meta_description(),
            'product_description' => $product->getProduct_description(),
            'product_insert_date' => $product->getProduct_insert_date(),
            'product_active_from_date' => $product->getProduct_active_from_date(),
            'product_expire_date' => $product->getProduct_expire_date(),
            'product_category_heureka' => $product->getProduct_category_heureka(),
            'product_action' => $product->getProduct_action(),
            'product_sale' => $product->getProduct_sale(),
            'product_new' => $product->getProduct_new(),
            'product_recommend' => $product->getProduct_recommend(),
            'product_affiliate_program_name' => $product->getProduct_affiliate_program_name(),
            'product_is_active' => $product->getProduct_is_active(),
            'product_manufacturer_id' => $product->getProduct_manufacturer_id(),
            'product_itemgroup_product_id' => $product->getProduct_itemgroup_product_id(),
        );

        if (null === ($id = $product->getProduct_id())) {
            unset($data['product_id']);
            $this->getDbTable()->insert($data);
        } else {

// provedu update jen tech polozek, ktere jsou uvedeny v poli $rows_to_update, pokud polozka neni v $_rows to update, bude odstranena
            if (!empty($rows_to_update)) {
                $rowsToUpdate = array_diff_key($rows_to_update, $data);

                foreach ($data as $dataCol => $dataValue) {
                    if (!in_array($dataCol, $rowsToUpdate)) {
                        unset($data[$dataCol]);
                    }
                }
            }

            $this->getDbTable()->update($data, array('product_id = ?' => $id));
        }
    }

    
    /**
     * find product by id with variants count
     *
     * @param int $id product id
     */
    public function find($id) {

        $select = $this->getDbTable()
                ->select()
                ->from(array('p' => 'product'), array('COUNT(p2.product_id) AS variants_count', 'p.*'))
                ->joinLeft(array('p2' => 'product'), 'p.product_id = p2.product_itemgroup_product_id', array())
                ->where('p.product_id = ?', (int) $id)
                ->group('p.product_id');

//echo $select->__toString();die;
        $row = $this->getDbTable()->getDefaultAdapter()->fetchRow($select);

        if (!$row) {
            return;
        }

        if (0 == count($row)) {
            return;
        }

        $entries = array();
        $product = new Admin_Model_Product();
        $product->setOptions($row);
        $entries = $product;

        return $entries;
    }

    public function delete($id) {
        $dbAdapter = $this->getDbTable()->getDefaultAdapter();
        $where = $dbAdapter->quoteInto('product_id = ?', (int) $id);
        $dbAdapter->delete("product", $where);
    }

    //ziskam vsechny produkty
    public function getProducts($page = 0, $ipp = -1, $where = array(), $order = array()) {

        $select = $this->getDbTable()
                ->select()
                ->setIntegrityCheck(false)
                ->from(array('p' => 'product'))
                ->columns('(SELECT count(p2.product_id) FROM product p2 WHERE p2.product_itemgroup_product_id = p.product_id GROUP BY p2.product_itemgroup_product_id) AS variants_count')
                ->joinLeft(array('pc' => 'product_category'), 'p.product_id = pc.product_category_product_id')
                ->joinLeft(array('c' => 'category'), 'c.category_id = pc.product_category_category_id')
                ->joinLeft(array('ph' => 'photography'), 'ph.photography_product_id = p.product_id')
                ->joinLeft(array('m' => 'manufacturer'), 'p.product_manufacturer_id = m.manufacturer_id')
                ->joinLeft(array('ap' => 'affiliate_program'), 'p.product_affiliate_program_name = ap.affiliate_program_id')
                ->where('ph.photography_is_main = 1')
                ->group('p.product_id');


        if (!empty($where)) {
            if (count($where) > 0) {
                foreach ($where as $whereKey => $whereItems) {
                    $select->where($whereKey . ' IN(?) ', $whereItems);
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


//        echo $select->__toString();die;


        $paginator = Zend_Paginator::factory($select);
        $paginator->setCurrentPageNumber($page);
        $paginator->setDefaultItemCountPerPage($ipp);
        $result = $paginator->getCurrentItems();
        $entries = array();

        $this->_paginator = $paginator;

        foreach ($result as $row) {
            $products = new Admin_Model_Product();
            $photography = new Admin_Model_Photography();
            $photography->setOptions($row->toArray());

            $products->setMain_photography($photography);

            $manufacturer = new Admin_Model_Manufacturer();
            $manufacturer->setOptions($row->toArray());

            $category = new Admin_Model_Category();
            $category->setOptions($row->toArray());

            $affiliateProgram = new Admin_Model_AffiliateProgram();
            $affiliateProgram->setOptions($row->toArray());

            $products->setOptions($row->toArray());
            $products->setPhotographies($photography);
            $products->setManufacturer($manufacturer);

            $products->setCategory($category);
            $products->setAffiliate_program($affiliateProgram);
            $entries[$row['product_id']] = $products;
        }

        return $entries;
    }

    //ziskam vsechny produkty
    public function getProductsByItemgroupId($page = 0, $ipp = -1, $others = array()) {

        $select = $this->getDbTable()
                ->select()
                ->setIntegrityCheck(false)
                ->from(array('p' => 'product'))
                ->joinLeft(array('ph' => 'photography'), 'p.product_id = ph.photography_product_id OR p.product_itemgroup_product_id = ph.photography_product_id')
                ->where('ph.photography_is_main = 1');
//        array('COALESCE(ph.photography_path, (SELECT ph2.photography_path FROM photography ph2 WHERE ph2.photography_product_id = p.product_itemgroup_id AND ph2.photography_is_main = 1)) as photography_path'


        if (!empty($others)) {
            if (array_key_exists('where', $others)) {
                if (!empty($others['where'])) {
                    foreach ($others['where'] as $whereCol => $whereValue) {
                        $select->where($whereCol . ' = ?', $whereValue);
                    }
                }
            }
            if (array_key_exists('order', $others)) {

                if ($others['order']) {
                    $select->order($others['order']);
                }
            }
        }


//        echo $select->__toString();
//        die;


        $paginator = Zend_Paginator::factory($select);
        $paginator->setCurrentPageNumber($page);
        $paginator->setDefaultItemCountPerPage($ipp);
        $result = $paginator->getCurrentItems();
        $entries = array();

        $this->_paginator = $paginator;

        foreach ($result as $row) {
            $product = new Admin_Model_Product();
            $product->setOptions($row->toArray());

            $photography = new Admin_Model_Photography();
            $photography->setOptions($row->toArray());

            $product->setPhotographies($photography);

            $entries[$row['product_id']] = $product;
        }

        return $entries;
    }

    //ziskam produkty - pouzito pro vyhledavani
    public function getProductsBySearchTerm($page = 0, $ipp = -1, $where = array()) {

        $select = $this->getDbTable()
                ->select()
                ->setIntegrityCheck(false)
                ->from(array('p' => 'product'))
                ->joinLeft(array('pc' => 'product_category'), 'p.product_id = pc.product_category_product_id')
                ->joinLeft(array('c' => 'category'), 'c.category_id = pc.product_category_category_id')
                ->joinLeft(array('ph' => 'photography'), 'ph.photography_product_id = p.product_id')
                ->joinLeft(array('m' => 'manufacturer'), 'p.product_manufacturer_id = m.manufacturer_id')
                ->joinLeft(array('ap' => 'affiliate_program'), 'p.product_affiliate_program_name = ap.affiliate_program_id')
                ->where('ph.photography_is_main = 1')
                ->order('p.product_insert_date DESC');


        if (!empty($where)) {
            if (count($where) > 0) {
                $quotes = array();
                foreach ($where as $whereKey => $whereItems) {
//                    $select->where($whereKey . ' LIKE ?', '%'.$whereItems.'%');
                    $quote = Zend_Db_Table::getDefaultAdapter()->quoteInto($whereKey . ' LIKE ?', '%' . $whereItems . '%');

                    $quotes[] = $quote;
//                    $select->orWhere($quote);
                }
                $select->where(implode(' OR ', $quotes));
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
            $products = new Admin_Model_Product();
            $photography = new Admin_Model_Photography();
            $photography->setOptions($row->toArray());

            $products->setMain_photography($photography);

            $manufacturer = new Admin_Model_Manufacturer();
            $manufacturer->setOptions($row->toArray());

            $category = new Admin_Model_Category();
            $category->setOptions($row->toArray());

            $affiliateProgram = new Admin_Model_AffiliateProgram();
            $affiliateProgram->setOptions($row->toArray());

            $products->setOptions($row->toArray());
            $products->setPhotographies($photography);
            $products->setCategory($category);
            $products->setManufacturer($manufacturer);
            $products->setAffiliate_program($affiliateProgram);
            $entries[$row['product_id']] = $products;
        }

        return $entries;
    }

    //ziskam vsechny produkty, kde je obrazek jako odkaz, zacina http://
    public function getProductsWithHttpImage() {

        $select = $this->getDbTable()
                ->select()
                ->setIntegrityCheck(false)
                ->from(array('p' => 'product'))
                ->join(array('ph' => 'photography'), 'p.product_id = ph.photography_product_id')
//                ->where('ph.photography_is_main = 1')
                ->where('ph.photography_path LIKE "http://%"')
                ->limit(100);

//        echo $select->__toString();die;

        $result = $this->getDbTable()->fetchAll($select);
        $entries = array();

        foreach ($result as $row) {

            $entries[$row['product_id']] = array(
                'photography_path' => $row['photography_path'],
                'product_name' => $row['product_name'],
                'photography_id' => $row['photography_id']
            );
        }

        return $entries;
    }

    //ziskam vsechny produkty podle affiliate programu
    public function getProductsByAffiliateProgramName($affiliateProgramName) {

        $select = $this->getDbTable()
                ->select()
                ->setIntegrityCheck(false)
                ->from(array('p' => 'product'))
                ->join(array('ap' => 'affiliate_program'), 'p.product_affiliate_program_name = ap.affiliate_program_id')
                ->where('ap.affiliate_program_name = ?', $affiliateProgramName);

//        echo $select->__toString();die;

        $result = $this->getDbTable()->fetchAll($select);

        $entries = array();

        foreach ($result as $row) {

            $entries[$row['product_code']] = $row->toArray();
        }

        return $entries;
    }

    //ziskam max id produktu
    public function getMaxProductId() {

        $select = $this->getDbTable()
                ->select()
                ->setIntegrityCheck(false)
                ->from(array('p' => 'product'), 'MAX(p.product_id) as max_id');

        $row = $this->getDbTable()->fetchRow($select);

        $maxId = 0;
        if ($row['max_id']) {
            $maxId = (int) $row['max_id'];
        }

        return $maxId;
    }

    public function urlExists($url) {

        $existsUrl = false;
        // kontrola, zda zaznam existuje, pokud ano, vracim true, jinak false
        if ($url != "") {
            $select = $this->getDbTable()
                    ->select()
                    ->from(array('p' => 'product'), array('row_count' => 'COUNT(1)'))
                    ->where('p.product_url = ?', $url);

            $row = $this->getDbTable()->fetchRow($select);

            if ($row['row_count']) {
                $existsUrl = true;
            }
        }


        return $existsUrl;
    }

    public function bulkInsert(array $products) {

        //pokud pole obsahuje mnoho polozek, nejde provest hromadny insert - (mysql error 2006 server gone) - rozdelim pole a provedu insert vickrat


        if (!empty($products)) {

            $db = $this->getDbTable()->getAdapter();
            $query = 'INSERT INTO product (' .
                    'product_id, ' .
                    'product_name, ' .
                    'product_stock, ' .
                    'product_recommended_price, ' .
                    'product_price, ' .
                    'product_code, ' .
                    'product_ean, ' .
                    'product_url, ' .
                    'product_affiliate_url, ' .
                    'product_description, ' .
                    'product_insert_date, ' .
                    'product_affiliate_program_name, ' .
                    'product_is_active, ' .
                    'product_manufacturer_id' .
                    ') VALUES ';

            $queryVals = array();
//print_r($products);die;
            foreach ($products as $product) {
                $queryVals[] = '(' .
//                        $db->quote($product->getProduct_id()) . ', ' .
//                        $db->quote($product->getProduct_name()) . ', ' .
//                        $db->quote($product->getProduct_stock()) . ', ' .
//                        $db->quote($product->getProduct_recommended_price()) . ', ' .
//                        $db->quote($product->getProduct_price()) . ', ' .
//                        $db->quote($product->getProduct_code()) . ', ' .
//                        $db->quote($product->getProduct_ean()) . ', ' .
//                        $db->quote($product->getProduct_url()) . ', ' .
//                        $db->quote($product->getProduct_affiliate_url()) . ', ' .
//                        $db->quote($product->getProduct_description()) . ', ' .
//                        $db->quote($product->getProduct_insert_date()) . ', ' .
//                        $db->quote($product->getProduct_affiliate_program_name()) . ', ' .
//                        $db->quote($product->getProduct_is_active()) . ', ' .
//                        $db->quote($product->getProduct_manufacturer_id()) . ')';
                        $db->quote($product['id']) . ', ' .
                        $db->quote($product['name']) . ', ' .
                        $db->quote($product['stock']) . ', ' .
                        $db->quote($product['recommended_price']) . ', ' .
                        $db->quote($product['price']) . ', ' .
                        $db->quote($product['code']) . ', ' .
                        $db->quote($product['ean']) . ', ' .
                        $db->quote($product['url']) . ', ' .
                        $db->quote($product['affiliate_url']) . ', ' .
                        $db->quote($product['description']) . ', ' .
                        $db->quote($product['insert_date']) . ', ' .
                        $db->quote($product['affiliate_program_name']) . ', ' .
                        $db->quote($product['is_active']) . ', ' .
                        $db->quote($product['manufacturer_id']) . ')';
            }
//echo $query . implode(',', $queryVals);die;

            $db->getConnection()->query($query . implode(',', $queryVals));

//            $db->exec($query . implode(',', $queryVals));
        }
    }

}
