<?php

class Admin_Model_ParameterDialMapper {

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

        if (!$this->_dbTable instanceof Admin_Model_ParameterDial) {

            $this->setDbTable("Admin_Model_DbTable_ParameterDial");
        }

        return $this->_dbTable;
    }

    public function save(Admin_Model_ParameterDial $parameter_dial) {

        $data = array(
            'parameter_dial_id' => $parameter_dial->getParameter_dial_id(),
            'parameter_dial_value' => $parameter_dial->getParameter_dial_value(),
            'parameter_dial_parameter_id' => $parameter_dial->getParameter_dial_parameter_id(),
        );

        if (null === ($id = $parameter_dial->getParameter_dial_id())) {
            unset($data['parameter_dial_id']);
            $this->getDbTable()->insert($data);
        } else {

            $this->getDbTable()->update($data, array('parameter_dial_id = ?' => $id));
        }
    }

    public function bulkInsert(array $parameterDials) {

        if (!empty($parameterDials)) {

            $db = $this->getDbTable()->getAdapter();
            $query = 'INSERT INTO parameter_dial (' .
                    'parameter_dial_value, ' .
                    'parameter_dial_parameter_id' .
                    ') VALUES ';
            $queryVals = array();
            foreach ($parameterDials as $parameterDial) {
                $queryVals[] = '(' .
                        $db->quote($parameterDial['parameter_dial_value']) . ', ' .
                        $db->quote($parameterDial['parameter_dial_parameter_id']) . ')';
            }


            $db->query($query . implode(',', $queryVals));
        }
    }

    //find by parameter id - detail
    public function findByParameterId($parameter_id) {

        $select = $this->getDbTable()
                ->select()
                ->from(array('pd' => 'parameter_dial'))
                ->where('pd.parameter_dial_parameter_id = ?', (int) $parameter_id);

        $rows = $this->getDbTable()->getDefaultAdapter()->fetchAll($select);

        $entries = array();
        foreach ($rows as $row) {
            $parameterDial = new Admin_Model_ParameterDial();
            $parameterDial->setOptions($row);
            $entries[$parameterDial->getParameter_dial_id()] = $parameterDial;
        }

        return $entries;
    }

    
    
    
    public function delete($id) {
        if ($id) {
            $dbAdapter = $this->getDbTable()->getDefaultAdapter();
            $where = $dbAdapter->quoteInto('parameter_dial_id = ?', (int) $id);
            $dbAdapter->delete("parameter_dial", $where);
        }
    }
}
