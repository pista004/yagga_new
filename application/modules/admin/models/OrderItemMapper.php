<?php

class Admin_Model_OrderItemMapper {

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

        if (!$this->_dbTable instanceof Admin_Model_OrderItem) {

            $this->setDbTable("Admin_Model_DbTable_OrderItem");
        }

        return $this->_dbTable;
    }

    public function getOrderItemsByOrderId($id) {

        $entries = array();
        if ($id) {
            $select = $this->getDbTable()
                    ->select()
                    ->setIntegrityCheck(false)
                    ->from(array('oi' => 'order_item'))
                    ->joinLeft(array('p' => 'product'), 'oi.order_item_product_id = p.product_id')
                    ->joinLeft(array('v' => 'variant'), 'oi.order_item_variant_id = v.variant_id')
                    ->where('oi.order_item_order_id = ?', $id);

            $result = $this->getDbTable()->getDefaultAdapter()->fetchAll($select);

            foreach ($result as $row) {
                $orderItem = new Admin_Model_OrderItem();

                $product = new Admin_Model_Product();
                $product->setOptions($row);
                $orderItem->setOrder_item_product($product);

                $variant = new Admin_Model_Variant();
                $variant->setOptions($row);
                $orderItem->setOrder_item_variant($variant);

                $orderItem->setOptions($row);

                $entries[] = $orderItem;
            }
        }

        return $entries;
    }

    public function getOrderItemsById($id) {

        $entries = array();
        if ($id) {
            $select = $this->getDbTable()
                    ->select()
                    ->from(array('oi' => 'order_item'))
                    ->where('oi.order_item_id = ?', $id);

            $row = $this->getDbTable()->getDefaultAdapter()->fetchRow($select);

            $orderItem = new Admin_Model_OrderItem();
            $entries = $orderItem->setOptions($row);
        }

        return $entries;
    }

    public function save(Admin_Model_OrderItem $orderItem) {

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
        } else {
            unset($data['order_item_order_id']);
            unset($data['order_item_product_name']);
            unset($data['order_item_product_id']);
            $this->getDbTable()->update($data, array('order_item_id = ?' => $id));
        }
    }

    public function delete($id) {
        $dbAdapter = $this->getDbTable()->getDefaultAdapter();
        $where = $dbAdapter->quoteInto('order_item_id = ?', (int) $id);
        $dbAdapter->delete("order_item", $where);
    }

}
