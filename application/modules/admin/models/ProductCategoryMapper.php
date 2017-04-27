<?php

class Admin_Model_ProductCategoryMapper {

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

        if (!$this->_dbTable instanceof Admin_Model_ProductCategory) {

            $this->setDbTable("Admin_Model_DbTable_ProductCategory");
        }

        return $this->_dbTable;
    }

    //vkladan do product_category
//    public function insert(Admin_Model_ProductCategory $productCategory) {
//        $data = array(
//            'product_id' => $productCategory->getProduct_id(),
//            'category_id' => $productCategory->getCategory_id(),
//        );
//
//        if (null != $productCategory->getProduct_id() && null != $productCategory->getCategory_id()) {
//            $this->getDbTable()->insert($data);
//        }
//    }
//    //update product_category
//    public function update($product_id, $category_id, Admin_Model_ProductCategory $productCategory) {
//        $data = array(
//            'product_id' => $productCategory->getProduct_id(),
//            'category_id' => $productCategory->getCategory_id(),
//        );
//
//        if (null != $productCategory->getProduct_id() && null != $productCategory->getCategory_id()) {
//            $this->getDbTable()->update($data, array('product_id = ?' => $product_id, 'category_id = ?' => $category_id));
//        }
//    }
    //find by product id and category id - najde zaznam v tabulce product_category podle id produktu
    public function getByProductId($product_id) {

        $select = $this->getDbTable()
                ->select()
                ->from(array('pc' => 'product_category'))
                ->where('pc.product_category_product_id = ?', $product_id);

        $result = $this->getDbTable()->fetchAll($select);

        if (0 == count($result)) {
            return;
        }

        $entries = array();
        foreach ($result as $row) {
            $productCategory = new Admin_Model_ProductCategory();
            $productCategory->setOptions($row->toArray());
            $entries[] = $productCategory;
        }

        return $entries;
    }

    /*
     * 
     * vybrat vsechny zaznamy podle ID, potom porovnat s ukladanyma hodnotama, pokud chybi, tak smazat, pokud nadbyva, tak vlozit
     * 
     */

    public function save(Admin_Model_ProductCategory $productCategory) {

        $data = array(
            'product_category_category_id' => $productCategory->getProduct_Category_Category_id(),
            'product_category_product_id' => $productCategory->getProduct_Category_Product_id(),
        );

        // kontrola, zda zaznam existuje, pokud ano, nedelam nic, jinak insert
        if ($productCategory->getProduct_Category_Product_id() && $productCategory->getProduct_Category_Category_id()) {
            $select = $this->getDbTable()
                    ->select()
                    ->from(array('pc' => 'product_category'), array('row_count' => 'COUNT(1)'))
                    ->where('pc.product_category_product_id = ?', $productCategory->getProduct_Category_Product_id())
                    ->where('pc.product_category_category_id = ?', $productCategory->getProduct_Category_Category_id());


            $row = $this->getDbTable()->fetchRow($select);

            if (!$row['row_count']) {
                $this->getDbTable()->insert($data);
            }
        }
    }

    public function deleteByCategoryIdProductId($category_id, $product_id) {
        $dbAdapter = $this->getDbTable()->getDefaultAdapter();

        $where = array();
        $where[] = $dbAdapter->quoteInto('product_category_category_id = ?', (int) $category_id);
        $where[] = $dbAdapter->quoteInto('product_category_product_id = ?', (int) $product_id);
//        print_r($where);die;
        $dbAdapter->delete("product_category", $where);
    }

    
    public function bulkInsert(array $productCategories) {

        if (!empty($productCategories)) {

            $db = $this->getDbTable()->getAdapter();
            $query = 'INSERT INTO product_category (' .
                    'product_category_category_id, ' .
                    'product_category_product_id' .
                    ') VALUES ';
            $queryVals = array();
            foreach ($productCategories as $productCategory) {
                $queryVals[] = '(' .
                        $db->quote($productCategory['category_id']) . ', ' .
                        $db->quote($productCategory['product_id']) . ')';
            }

//            echo $query . implode(',', $queryVals);die;
            
            $db->query($query . implode(',', $queryVals));
        }
    }

    
    
}
