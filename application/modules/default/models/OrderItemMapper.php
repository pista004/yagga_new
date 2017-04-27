<?php

class Default_Model_OrderItemMapper {

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

        if (!$this->_dbTable instanceof Default_Model_OrderItem) {

            $this->setDbTable("Default_Model_DbTable_OrderItem");
        }

        return $this->_dbTable;
    }

    public function getOrderItemsByOrderId($orderId) {
        $select = $this->getDbTable()
                ->select()
                ->from(array('oi' => 'order_item'))
                ->where('oi.order_item_order_id = ?', $orderId);

//        echo $select->__toString();die;

        $result = $this->getDbTable()->fetchAll($select);

        $entries = array();

        foreach ($result as $row) {
            $orderItem = new Default_Model_OrderItem();
            $orderItem->setOptions($row->toArray());
            $entries[] = $orderItem;
        }

        return $entries;
    }

    public function save(Default_Model_OrderItem $orderItem) {

        $data = array(
            'order_item_id' => $orderItem->getOrder_item_id(),
            'order_item_order_id' => $orderItem->getOrder_item_order_id(),
            'order_item_product_name' => $orderItem->getOrder_item_product_name(),
            'order_item_variant_name' => $orderItem->getOrder_item_variant_name(),
            'order_item_product_id' => $orderItem->getOrder_item_product_id(),
            'order_item_variant_id' => $orderItem->getOrder_item_variant_id(),
            'order_item_pieces' => $orderItem->getOrder_item_pieces(),
            'order_item_price' => $orderItem->getOrder_item_price(),
        );



        if (null === ($id = $orderItem->getOrder_item_id())) {
            unset($data['order_item_id']);
            $this->getDbTable()->insert($data);
        }
    }

}
