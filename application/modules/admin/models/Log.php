<?php

class Admin_Model_Log extends Model_Model {
    
    protected $_id;
    protected $_message;
    protected $_inserted;
    
    public function setLog_id($id) {
        $this->_id = (int) $id;
    }

    public function getLog_id() {
        return $this->_id;
    }
    
    public function setLog_message($message) {
        $this->_message = $message;
    }

    public function getLog_message() {
        return $this->_message;
    }

    public function setLog_inserted($inserted) {
        $this->_inserted = $inserted;
    }

    public function getLog_inserted() {
        return $this->_inserted;
    }
    
}
