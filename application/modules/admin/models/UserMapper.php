<?php

class Admin_Model_UserMapper {

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

        if (!$this->_dbTable instanceof Admin_Model_User) {

            $this->setDbTable("Admin_Model_DbTable_User");
        }

        return $this->_dbTable;
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
    public function save(Admin_Model_User $user) {
        
        $salt = $this->generateSalt();
        $data = array(
            'user_id' => $user->getUser_id(),
            'user_login' => $user->getUser_login(),
            'user_password' => sha1($user->getUser_password().$salt),
            'user_salt' => $salt,
            'user_created_date' => $user->getUser_created_date(),
            'user_last_login' => $user->getUser_last_login(),
            'user_newsletter' => $user->getUser_newsletter(),
            'user_is_active' => $user->getUser_is_active(),
        );

//        print_r($data);die;

        if (null === ($id = $user->getUser_id())) {
            unset($data['user_id']);
            $this->getDbTable()->insert($data);
        } else {

            //projdu ukladana data a pokud obsahuji prazdny retezec nebo null, tak je odstranim, zbytek ulozim
            foreach ($data as $key => $d) {
                if (in_array($d, array("", null))) {
                    unset($data[$key]);
                }
            }

            $this->getDbTable()->update($data, array('user_id = ?' => $id));
        }
    }
    
    //salt pro bezpecnejsi heslo - nahodne generuje retezec o delce 16 znaku
    private function generateSalt(){
        return substr(sha1(rand(0, 999).uniqid()), 0, 16);
    }
    
//
//    public function delete($id) {
//        $dbAdapter = $this->getDbTable()->getDefaultAdapter();
//        $where = $dbAdapter->quoteInto('order_id = ?', (int) $id);
//        $dbAdapter->delete("order", $where);
//    }
}
