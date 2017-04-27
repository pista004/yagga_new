<?php

class Admin_Model_PhotographyMapper {

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

        if (!$this->_dbTable instanceof Admin_Model_Photography) {

            $this->setDbTable("Admin_Model_DbTable_Photography");
        }

        return $this->_dbTable;
    }

    public function save(Admin_Model_Photography $photography) {
        $data = array(
            'photography_id' => $photography->getPhotography_id(),
            'photography_path' => $photography->getPhotography_path(),
            'photography_note' => $photography->getPhotography_note(),
            'photography_is_main' => $photography->getPhotography_is_main(),
            'photography_product_id' => $photography->getPhotography_product_id(),
            'photography_article_id' => $photography->getPhotography_article_id(),
            'photography_manufacturer_id' => $photography->getPhotography_manufacturer_id(),
        );

        if (null === ($id = $photography->getPhotography_id())) {
            unset($data['photography_id']);
            $this->getDbTable()->insert($data);
        } else {

            //projdu ukladana data a pokud obsahuji prazdny retezec nebo null, tak je odstranim, zbytek ulozim
            foreach ($data as $key => $d) {
                if (in_array($d, array("", null))) {
                    unset($data[$key]);
                }
            }

            $this->getDbTable()->update($data, array('photography_id = ?' => $id));
        }
    }

    public function getPhotosByProductId($product_id) {

        $entries = array();

        if ($product_id) {
            $select = $this->getDbTable()
                    ->select()
                    ->from(array('p' => 'photography'))
                    ->where('p.photography_product_id = ?', $product_id);


            $result = $this->getDbTable()->fetchAll($select);

            foreach ($result as $row) {
                $photography = new Admin_Model_Photography();
                $entries[] = $photography->setOptions($row->toArray());
            }
        }
        return $entries;
    }

    public function getPhotoByArticleId($article_id) {

        $entries = array();

        if ($article_id) {
            $select = $this->getDbTable()
                    ->select()
                    ->from(array('p' => 'photography'))
                    ->where('p.photography_article_id = ?', $article_id);


            $result = $this->getDbTable()->fetchRow($select);

            $photography = new Admin_Model_Photography();
            $entries = $photography->setOptions(current($result));
        }
        return $entries;
    }

    public function getPhotoByManufacturerId($manufacturer_id) {

        $entries = array();

        if ($manufacturer_id) {
            $select = $this->getDbTable()
                    ->select()
                    ->from(array('p' => 'photography'))
                    ->where('p.photography_manufacturer_id = ?', $manufacturer_id);

            $result = $this->getDbTable()->fetchRow($select);

            if ($result) {
                $photography = new Admin_Model_Photography();
                $entries = $photography->setOptions(current($result));
            }
        }
        return $entries;
    }

    public function getPhotographyById($photography_id) {
        $select = $this->getDbTable()
                ->select()
                ->from(array('p' => 'photography'))
                ->where('p.photography_id = ?', (int) $photography_id);

        $row = $this->getDbTable()->getDefaultAdapter()->fetchRow($select);

        if (!$row || (0 == count($row))) {
            return;
        }

        $entries = array();
        $photography = new Admin_Model_Photography();
        $photography->setOptions($row);

        $entries = $photography;

        return $entries;
    }

    public function delete($id) {
        $dbAdapter = $this->getDbTable()->getDefaultAdapter();
        $where = $dbAdapter->quoteInto('photography_id = ?', (int) $id);
        $dbAdapter->delete("photography", $where);
    }

    public function deletePhotosByProductId($productId) {
        if ($productId) {
            $dbAdapter = $this->getDbTable()->getDefaultAdapter();
            $where = $dbAdapter->quoteInto('photography_product_id = ?', (int) $productId);
            $dbAdapter->delete("photography", $where);
        }
    }

    public function setPhotographyMain($photography_id, $product_id) {

        if ($photography_id > 0 && $product_id > 0) {

            $dbTable = $this->getDbTable();
            $dbAdapter = $this->getDbTable()->getDefaultAdapter();

            //nejprve nastavim vsechny fotografie prirazene k produktu na 0
            $data1 = array(
                'photography_is_main' => 0,
            );
            $where1 = $dbAdapter->quoteInto('photography_product_id = ?', (int) $product_id);
            $dbTable->update($data1, $where1);

            //potom nastavim konkretni fotografii jako hlavni
            $data2 = array(
                'photography_is_main' => 1,
            );
            $where2 = $dbAdapter->quoteInto('photography_id = ?', (int) $photography_id);
            $dbTable->update($data2, $where2);
        }
    }

    public function checkPhotographyIsMain($product_id) {
//        SELECT count(p.product_id) FROM product p INNER JOIN photography ph ON p.product_id = ph.photography_product_id WHERE p.product_id = 32 AND ph.photography_is_main = 1

        $select = $this->getDbTable()
                ->select()
                ->setIntegrityCheck(false)
                ->from(array('p' => 'product'), "count(p.product_id) as count")
                ->join(array('ph' => 'photography'), "p.product_id = ph.photography_product_id", array())
                ->where("p.product_id = ?", $product_id)
                ->where("ph.photography_is_main = 1");

//        echo $select->__toString();die;

        $result = $this->getDbTable()->fetchRow($select);

        return $result->count;
    }

    public function bulkInsert(array $photographies) {

        if (!empty($photographies)) {

            $db = $this->getDbTable()->getAdapter();
            $query = 'INSERT INTO photography (' .
                    'photography_path, ' .
                    'photography_is_main, ' .
                    'photography_product_id' .
                    ') VALUES ';
            $queryVals = array();
            foreach ($photographies as $photography) {
                $queryVals[] = '(' .
                        $db->quote($photography['path']) . ', ' .
                        $db->quote($photography['is_main']) . ', ' .
                        $db->quote($photography['product_id']) . ')';
            }

            $db->query($query . implode(',', $queryVals));
        }
    }

}
