<?php

class Admin_Model_ProductRecommendMapper {

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

        if (!$this->_dbTable instanceof Admin_Model_ProductRecommend) {

            $this->setDbTable("Admin_Model_DbTable_ProductRecommend");
        }

        return $this->_dbTable;
    }

    //ziskam doporucene produkty
    public function getProductRecommend($product_id) {

        $select = $this->getDbTable()
                ->select()
                ->setIntegrityCheck(false)
                ->from(array('pr' => 'product_recommend'))
                ->where('pr.product_recommend_product_id = ?', $product_id);

        $result = $this->getDbTable()->fetchAll($select);

        $entries = array();
        foreach ($result as $row) {
            $productRecommend = new Admin_Model_ProductRecommend();
            $productRecommend->setOptions($row->toArray());

            $entries[] = $productRecommend;
        }

        return $entries;
    }

    public function save(Admin_Model_ProductRecommend $productRecommend) {

        $data = array(
            'product_recommend_id' => $productRecommend->getProduct_recommend_id(),
            'product_recommend_product_id' => $productRecommend->getProduct_recommend_product_id(),
        );

        if ($productRecommend->getProduct_recommend_id() && $productRecommend->getProduct_recommend_product_id()) {
            $this->getDbTable()->insert($data);
        }
    }

    public function delete($id, $product_id) {
        $dbAdapter = $this->getDbTable()->getDefaultAdapter();
        $where1 = $dbAdapter->quoteInto('product_recommend_id = ?', (int) $id);
        $where2 = $dbAdapter->quoteInto('product_recommend_product_id = ?', (int) $product_id);
        $dbAdapter->delete("product_recommend", array($where1, $where2));
    }

}
