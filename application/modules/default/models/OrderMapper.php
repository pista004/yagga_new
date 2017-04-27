<?php

class Default_Model_OrderMapper {

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

        if (!$this->_dbTable instanceof Default_Model_Order) {

            $this->setDbTable("Default_Model_DbTable_Order");
        }

        return $this->_dbTable;
    }

    public function getOrderByOrderId($orderNumber) {
        $select = $this->getDbTable()
                ->select()
                ->from(array('o' => 'order'))
                ->where('o.order_id = ?', $orderNumber);

//        echo $select->__toString();die;

        $row = $this->getDbTable()->fetchRow($select);
        
        $entries = array();
        
        if($row){
            $order = new Default_Model_Order();
            $order->setOptions($row->toArray());
            $entries = $order;
        }
        
        return $entries;
    }

    public function save(Default_Model_Order $order) {
        $data = array(
            'order_id' => $order->getOrder_id(),
            'order_number' => $order->getOrder_number(),
            'order_date' => $order->getOrder_date(),
            'order_email' => $order->getOrder_email(),
            'order_phone' => $order->getOrder_phone(),
            'order_i_name' => $order->getOrder_i_name(),
            'order_i_surname' => $order->getOrder_i_surname(),
            'order_i_street' => $order->getOrder_i_street(),
            'order_i_city' => $order->getOrder_i_city(),
            'order_i_zip_code' => $order->getOrder_i_zip_code(),
            'order_i_country_id' => $order->getOrder_i_country_id(),
            'order_i_company' => $order->getOrder_i_company(),
            'order_i_ico' => $order->getOrder_i_ico(),
            'order_i_dic' => $order->getOrder_i_dic(),
            'order_d_name' => $order->getOrder_d_name(),
            'order_d_surname' => $order->getOrder_d_surname(),
            'order_d_company' => $order->getOrder_d_company(),
            'order_d_street' => $order->getOrder_d_street(),
            'order_d_city' => $order->getOrder_d_city(),
            'order_d_zip_code' => $order->getOrder_d_zip_code(),
            'order_d_country_id' => $order->getOrder_d_country_id(),
            'order_delivery_id' => $order->getOrder_delivery_id(),
            'order_payment_id' => $order->getOrder_payment_id(),
            'order_delivery_name' => $order->getOrder_delivery_name(),
            'order_payment_name' => $order->getOrder_payment_name(),
            'order_delivery_price' => $order->getOrder_delivery_price(),
            'order_payment_price' => $order->getOrder_payment_price(),
            'order_newsletter' => $order->getOrder_newsletter(),
            'order_admin_note' => $order->getOrder_admin_note(),
            'order_order_state_id' => $order->getOrder_order_state_id(),
            'order_ip_address' => $order->getOrder_ip_address()
        );


        if (null === ($id = $order->getOrder_id())) {
            unset($data['order_id']);
//            print_r($data);die;

            $this->getDbTable()->insert($data);
        } else {
            //projdu ukladana data a pokud obsahuji prazdny retezec nebo null, tak je odstranim, zbytek ulozim
            foreach ($data as $key => $d) {
                if (in_array($d, array("", null))) {
                    unset($data[$key]);
                }
            }
//            print_r($data);die;
            $this->getDbTable()->update($data, array('order_id = ?' => $id));
        }
    }

    public function getMaxOrderId() {
        $select = $this->getDbTable()
                ->select()
                ->from(array('o' => 'order'), array('MAX(o.order_id) as order_number'));

//        echo $select->__toString();die;

        $result = $this->getDbTable()->getDefaultAdapter()->fetchRow($select);

        return $result['order_number'];
    }

    public function delete($id) {
        $dbAdapter = $this->getDbTable()->getDefaultAdapter();
        $where = $dbAdapter->quoteInto('order_id = ?', (int) $id);
        $dbAdapter->delete("order", $where);
    }

}
