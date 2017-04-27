<?php

class Admin_Model_ParameterMapper {

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

        if (!$this->_dbTable instanceof Admin_Model_Parameter) {

            $this->setDbTable("Admin_Model_DbTable_Parameter");
        }

        return $this->_dbTable;
    }

    public function save(Admin_Model_Parameter $parameter) {

        $data = array(
            'parameter_id' => $parameter->getParameter_id(),
            'parameter_name' => $parameter->getParameter_name(),
            'parameter_note' => $parameter->getParameter_note(),
            'parameter_type' => $parameter->getParameter_type(),
            'parameter_parameter_unit_id' => $parameter->getParameter_parameter_unit_id(),
        );

        if (null === ($id = $parameter->getParameter_id())) {
            unset($data['parameter_id']);

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

            $this->getDbTable()->update($data, array('parameter_id = ?' => $id));
        }
    }

    //ziskam vsechny parametry
    public function getParameters($page = 0, $ipp = -1, $others = array()) {

        $select = $this->getDbTable()
                ->select()
                ->setIntegrityCheck(false)
                ->from(array('p' => 'parameter'))
                ->joinLeft(array('pu' => 'parameter_unit'), 'p.parameter_parameter_unit_id = pu.parameter_unit_id')
                ->order('p.parameter_id DESC');

//        echo $select->__toString();die;


        $paginator = Zend_Paginator::factory($select);
        $paginator->setCurrentPageNumber($page);
        $paginator->setDefaultItemCountPerPage($ipp);
        $result = $paginator->getCurrentItems();
        $entries = array();

        $this->_paginator = $paginator;

        foreach ($result as $row) {
            $parameter = new Admin_Model_Parameter();

            $parameterUnit = new Admin_Model_ParameterUnit();
            $parameterUnit->setOptions($row->toArray());
            $parameter->setParameter_unit($parameterUnit);

            $parameterType = new Admin_Model_ParameterType();
            $parameterType->setParameter_type_id($row['parameter_type']);
            $parameter->setParameter_type_obj($parameterType);

            $parameter->setOptions($row->toArray());

            $entries[$row['parameter_id']] = $parameter;
        }

        return $entries;
    }

    /**
     * Retrun parameters and values for product by product ID
     *
     * @param int $product_id
     * @return array od parameter objects
     */
    //        
//SELECT p.*, pc.*, c.*, prmc.*, prm.*, pprmv.*
//FROM `product` AS `p` 
//INNER JOIN `product_category` AS `pc` ON p.product_id = pc.product_category_product_id 
//INNER JOIN category c ON pc.product_category_category_id = c.category_id
//INNER JOIN parameter_category prmc ON c.category_id = prmc.parameter_category_category_id
//INNER JOIN parameter prm ON prmc.parameter_category_parameter_id = prm.parameter_id
//LEFT JOIN product_parameter_value pprmv ON prm.parameter_id = pprmv.product_parameter_value_parameter_id AND p.product_id = pprmv.product_parameter_value_product_id
//WHERE (p.product_id = 71535) GROUP BY prm.parameter_id
    public function getProductParametersValuesByProductId($product_id) {

        $select = $this->getDbTable()
                ->select()
                ->setIntegrityCheck(false)
                ->from(array('p' => 'product'), array())
                ->join(array('pc' => 'product_category'), 'p.product_id = pc.product_category_product_id', array())
                ->join(array('c' => 'category'), 'pc.product_category_category_id = c.category_id', array())
                ->join(array('prmc' => 'parameter_category'), 'c.category_id = prmc.parameter_category_category_id')
                ->join(array('prm' => 'parameter'), 'prmc.parameter_category_parameter_id = prm.parameter_id')
                ->joinLeft(array('pprmv' => 'product_parameter_value'), 'prm.parameter_id = pprmv.product_parameter_value_parameter_id AND p.product_id = pprmv.product_parameter_value_product_id')
                ->where('p.product_id = ?', (int) $product_id)
                ->group('prm.parameter_id');

//        echo $select->__toString();die;


        $result = $this->getDbTable()->getDefaultAdapter()->fetchAll($select);

        $entries = array();

        foreach ($result as $row) {
            $parameter = new Admin_Model_Parameter();

            $productParameterValue = new Admin_Model_ProductParameterValue();
            $productParameterValue->setOptions($row);

            $parameter->setOptions($row);
            $parameter->setProduct_parameter_value($productParameterValue);


            $entries[$row['parameter_id']] = $parameter;
        }
//print_r($entries);die;
        return $entries;
    }

    public function getVariantParametersValuesByProductId($product_id) {

        $select = $this->getDbTable()
                ->select()
                ->setIntegrityCheck(false)
                ->from(array('p' => 'product'), array())
                ->join(array('pc' => 'product_category'), 'p.product_itemgroup_product_id = pc.product_category_product_id', array())
                ->join(array('c' => 'category'), 'pc.product_category_category_id = c.category_id', array())
                ->join(array('prmc' => 'parameter_category'), 'c.category_id = prmc.parameter_category_category_id')
                ->join(array('prm' => 'parameter'), 'prmc.parameter_category_parameter_id = prm.parameter_id')
                ->joinLeft(array('pprmv' => 'product_parameter_value'), 'prm.parameter_id = pprmv.product_parameter_value_parameter_id AND p.product_id = pprmv.product_parameter_value_product_id')
                ->where('p.product_id = ?', (int) $product_id)
                ->group('prm.parameter_id');

//        echo $select->__toString();die;


        $result = $this->getDbTable()->getDefaultAdapter()->fetchAll($select);

        $entries = array();

        foreach ($result as $row) {
            $parameter = new Admin_Model_Parameter();

            $productParameterValue = new Admin_Model_ProductParameterValue();
            $productParameterValue->setOptions($row);

            $parameter->setOptions($row);
            $parameter->setProduct_parameter_value($productParameterValue);


            $entries[$row['parameter_id']] = $parameter;
        }
//print_r($entries);die;
        return $entries;
    }

    

    //find by id - detail
    public function find($id) {

        $select = $this->getDbTable()
                ->select()
                ->from(array('p' => 'parameter'))
                ->where('p.parameter_id = ?', (int) $id);

        $row = $this->getDbTable()->getDefaultAdapter()->fetchRow($select);

        if (!$row) {
            return;
        }

        if (0 == count($row)) {
            return;
        }

        $entries = array();
        $parameter = new Admin_Model_Parameter();
        $parameter->setOptions($row);
        $entries = $parameter;

        return $entries;
    }

    public function delete($id) {
        if ($id) {
            $dbAdapter = $this->getDbTable()->getDefaultAdapter();
            $where = $dbAdapter->quoteInto('parameter_id = ?', (int) $id);
            $dbAdapter->delete("parameter", $where);
        }
    }

}
