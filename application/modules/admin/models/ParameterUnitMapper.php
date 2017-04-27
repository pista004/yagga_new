<?php

class Admin_Model_ParameterUnitMapper {

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

        if (!$this->_dbTable instanceof Admin_Model_ParameterUnit) {

            $this->setDbTable("Admin_Model_DbTable_ParameterUnit");
        }

        return $this->_dbTable;
    }

    public function save(Admin_Model_ParameterUnit $parameter_unit) {

        $data = array(
            'parameter_unit_id' => $parameter_unit->getParameter_unit_id(),
            'parameter_unit_name' => $parameter_unit->getParameter_unit_name(),
            'parameter_unit_shortcut' => $parameter_unit->getParameter_unit_shortcut(),
            'parameter_unit_note' => $parameter_unit->getParameter_unit_note(),
        );

        if (null === ($id = $parameter_unit->getParameter_unit_id())) {
            unset($data['parameter_unit_id']);
            $this->getDbTable()->insert($data);
        } else {

            //pole specialnich parametru, ktere pri updatu nemuzou byt 0
//            $unsetIfZero = array('product_manufacturer_id');
            //projdu ukladana data a pokud obsahuji prazdny retezec nebo null, tak je odstranim, zbytek ulozim
//            foreach ($data as $key => $d) {
//                if (in_array($d, array("", null))) {
////                if (in_array($d, array("", null)) && ($d !== 0 || in_array($key, $unsetIfZero))) {
//                    unset($data[$key]);
//                }
//            }

            $this->getDbTable()->update($data, array('parameter_unit_id = ?' => $id));
        }
    }

    //ziskam vsechny unit parametry
    public function getParameterUnits($page = 0, $ipp = -1, $others = array()) {

        $select = $this->getDbTable()
                ->select()
                ->setIntegrityCheck(false)
                ->from(array('pu' => 'parameter_unit'));

//        echo $select->__toString();die;


        $paginator = Zend_Paginator::factory($select);
        $paginator->setCurrentPageNumber($page);
        $paginator->setDefaultItemCountPerPage($ipp);
        $result = $paginator->getCurrentItems();
        $entries = array();

        $this->_paginator = $paginator;

        foreach ($result as $row) {
            $parameterUnit = new Admin_Model_ParameterUnit();
            $parameterUnit->setOptions($row->toArray());

            $entries[$row['parameter_unit_id']] = $parameterUnit;
        }

        return $entries;
    }

        //find by id - detail
    public function find($id) {

        $select = $this->getDbTable()
                ->select()
                ->from(array('pu' => 'parameter_unit'))
                ->where('pu.parameter_unit_id = ?', (int) $id);

        $row = $this->getDbTable()->getDefaultAdapter()->fetchRow($select);

        if (!$row) {
            return;
        }

        if (0 == count($row)) {
            return;
        }

        $entries = array();
        $parameterUnit = new Admin_Model_ParameterUnit();
        $parameterUnit->setOptions($row);
        $entries = $parameterUnit;

        return $entries;
    }
    
    public function delete($id) {
        if ($id) {
            $dbAdapter = $this->getDbTable()->getDefaultAdapter();
            $where = $dbAdapter->quoteInto('parameter_unit_id = ?', (int) $id);
            $dbAdapter->delete("parameter_unit", $where);
        }
    }

}
