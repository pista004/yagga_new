<?php

class Admin_Model_UserProfileMapper {

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

        if (!$this->_dbTable instanceof Admin_Model_UserProfile) {

            $this->setDbTable("Admin_Model_DbTable_UserProfile");
        }

        return $this->_dbTable;
    }

    public function getUserProfiles($page = 0, $ipp = -1, $order = "") {

        $select = $this->getDbTable()
                ->select()
                ->setIntegrityCheck(false)
                ->from(array('u' => 'user'))
                ->join(array('up' => 'user_profile'), 'u.user_id = up.user_profile_user_id');

        if ($order) {
            $select->order($order);
        }

        $paginator = Zend_Paginator::factory($select);
        $paginator->setCurrentPageNumber($page);
        $paginator->setDefaultItemCountPerPage($ipp);
        $result = $paginator->getCurrentItems();

        $entries = array();

        $this->_paginator = $paginator;

        foreach ($result as $row) {
            
            $userProfile = new Admin_Model_UserProfile();
            $userProfile->setOptions($row->toArray());
            
            $user = new Admin_Model_User();
            $user->setOptions($row->toArray());
            
            $userProfile->setUser_profile_user($user);
            $userProfile->setOptions($row->toArray());
//
            $entries[] = $userProfile;
        }

        return $entries;
    }

//    public function getOrderById($id) {
//
////      SELECT o.*, SUM(oi.order_item_price*oi.order_item_pieces) AS order_sum FROM weeker.`order` o INNER JOIN order_item oi ON o.order_id = oi.order_item_order_id WHERE o.order_id = 3
//
//        $select = $this->getDbTable()
//                ->select()
//                ->setIntegrityCheck(false)
//                ->from(array('o' => 'order'), array('o.*', 'SUM(oi.order_item_price*oi.order_item_pieces) AS order_sum', 'SUM(oi.order_item_price*oi.order_item_pieces) + (o.order_delivery_price + o.order_payment_price) AS order_sum_with_delivery_payment'))
//                ->joinLeft(array('oi' => 'order_item'), 'o.order_id = oi.order_item_order_id', array())
//                ->joinLeft(array('i' => 'invoice'), 'o.order_id = i.invoice_order_id', array('i.*'))
//                ->where('o.order_id = ?', (int) $id);
//
////        echo $select->__toString();die;
//
//        $row = $this->getDbTable()->getDefaultAdapter()->fetchRow($select);
//
//        $entries = array();
//
//        $order = new Admin_Model_Order();
//
//        $invoice = new Admin_Model_Invoice();
//        $invoice->setOptions($row);
//        $order->setOrder_invoice($invoice);
//
//        $entries = $order->setOptions($row);
//
//
//        return $entries;
//    }
//
//    function myFilter($var) {
//        return ($var !== NULL && $var !== FALSE && $var !== '');
//    }
//
//    public function save(Admin_Model_Order $order) {
//        $data = array(
//            'order_id' => $order->getOrder_id(),
//            'order_email' => $order->getOrder_email(),
//            'order_phone' => $order->getOrder_phone(),
//            'order_i_name' => $order->getOrder_i_name(),
//            'order_i_surname' => $order->getOrder_i_surname(),
//            'order_i_street' => $order->getOrder_i_street(),
//            'order_i_city' => $order->getOrder_i_city(),
//            'order_i_zip_code' => $order->getOrder_i_zip_code(),
//            'order_i_country_id' => $order->getOrder_i_country_id(),
//            'order_i_company' => $order->getOrder_i_company(),
//            'order_i_ico' => $order->getOrder_i_ico(),
//            'order_i_dic' => $order->getOrder_i_dic(),
//            'order_d_name' => $order->getOrder_d_name(),
//            'order_d_surname' => $order->getOrder_d_surname(),
//            'order_d_company' => $order->getOrder_d_company(),
//            'order_d_street' => $order->getOrder_d_street(),
//            'order_d_city' => $order->getOrder_d_city(),
//            'order_d_zip_code' => $order->getOrder_d_zip_code(),
//            'order_d_country_id' => $order->getOrder_d_country_id(),
//            'order_delivery_id' => $order->getOrder_delivery_id(),
//            'order_payment_id' => $order->getOrder_payment_id(),
//            'order_delivery_name' => $order->getOrder_delivery_name(),
//            'order_payment_name' => $order->getOrder_payment_name(),
//            'order_delivery_price' => $order->getOrder_delivery_price(),
//            'order_payment_price' => $order->getOrder_payment_price(),
//            'order_admin_note' => $order->getOrder_admin_note(),
//            'order_order_state_id' => $order->getOrder_order_state_id(),
//            'order_ip_address' => $order->getOrder_ip_address()
//        );
//
////        print_r($data);die;
//
//        if (null === ($id = $order->getOrder_id())) {
//            unset($data['order_id']);
//            $this->getDbTable()->insert($data);
//        } else {
//
//            //projdu ukladana data a pokud obsahuji prazdny retezec nebo null, tak je odstranim, zbytek ulozim
//            foreach ($data as $key => $d) {
//                if (in_array($d, array("", null))) {
//                    unset($data[$key]);
//                }
//            }
//
//            $this->getDbTable()->update($data, array('order_id = ?' => $id));
//        }
//    }
//
//    public function delete($id) {
//        $dbAdapter = $this->getDbTable()->getDefaultAdapter();
//        $where = $dbAdapter->quoteInto('order_id = ?', (int) $id);
//        $dbAdapter->delete("order", $where);
//    }
}
