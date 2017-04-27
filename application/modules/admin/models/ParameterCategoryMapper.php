<?php

class Admin_Model_ParameterCategoryMapper {

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

        if (!$this->_dbTable instanceof Admin_Model_ParameterCategory) {

            $this->setDbTable("Admin_Model_DbTable_ParameterCategory");
        }

        return $this->_dbTable;
    }

    //find by parameter id - najde zaznam v tabulce parameter_category podle id parametru
    public function getByParameterId($parameter_id) {

        $select = $this->getDbTable()
                ->select()
                ->from(array('pc' => 'parameter_category'))
                ->where('pc.parameter_category_parameter_id = ?', $parameter_id);

        $result = $this->getDbTable()->fetchAll($select);

        if (0 == count($result)) {
            return;
        }

        $entries = array();
        foreach ($result as $row) {
            $parameterCategory = new Admin_Model_ParameterCategory();
            $parameterCategory->setOptions($row->toArray());
            $entries[] = $parameterCategory;
        }

        return $entries;
    }

    public function save(Admin_Model_ParameterCategory $parameterCategory) {

        $data = array(
            'parameter_category_category_id' => $parameterCategory->getParameter_category_category_id(),
            'parameter_category_parameter_id' => $parameterCategory->getParameter_category_parameter_id(),
        );

        // kontrola, zda zaznam existuje, pokud ano, nedelam nic, jinak insert
        if ($parameterCategory->getParameter_category_category_id() && $parameterCategory->getParameter_category_parameter_id()) {
            $select = $this->getDbTable()
                    ->select()
                    ->from(array('pc' => 'parameter_category'), array('row_count' => 'COUNT(1)'))
                    ->where('pc.parameter_category_parameter_id = ?', $parameterCategory->getParameter_category_parameter_id())
                    ->where('pc.parameter_category_category_id = ?', $parameterCategory->getParameter_category_category_id());


            $row = $this->getDbTable()->fetchRow($select);

            if (!$row['row_count']) {
                $this->getDbTable()->insert($data);
            }
        }
    }

    public function bulkInsert(array $parameterCategories) {

        if (!empty($parameterCategories)) {

            $db = $this->getDbTable()->getAdapter();
            $query = 'INSERT INTO parameter_category (' .
                    'parameter_category_category_id, ' .
                    'parameter_category_parameter_id' .
                    ') VALUES ';
            $queryVals = array();
            foreach ($parameterCategories as $parameterCategory) {
                $queryVals[] = '(' .
                        $db->quote($parameterCategory['category_id']) . ', ' .
                        $db->quote($parameterCategory['parameter_id']) . ')';
            }

//            echo $query . implode(',', $queryVals);die;

            $db->query($query . implode(',', $queryVals));
        }
    }

    public function deleteByCategoryIdParameterId($category_id, $parameter_id) {
        $dbAdapter = $this->getDbTable()->getDefaultAdapter();

        $where = array();
        if ((int) $category_id && (int) $parameter_id) {
            $where[] = $dbAdapter->quoteInto('parameter_category_category_id = ?', (int) $category_id);
            $where[] = $dbAdapter->quoteInto('parameter_category_parameter_id = ?', (int) $parameter_id);
            $dbAdapter->delete("parameter_category", $where);
        }
//        print_r($where);die;
    }

}
