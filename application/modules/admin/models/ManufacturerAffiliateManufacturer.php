<?php

class Admin_Model_ManufacturerAffiliateManufacturer extends Model_Model {

    protected $_id;
    protected $_name;
    protected $_manufacturer_id;
    
    protected $_manufacturer;

    public function setManufacturer_affiliate_manufacturer_id($id) {
        $this->_id = (int) $id;
    }

    public function getManufacturer_affiliate_manufacturer_id() {
        return $this->_id;
    }

    public function setManufacturer_affiliate_manufacturer_name($name) {
        $this->_name = $name;
    }

    public function getManufacturer_affiliate_manufacturer_name() {
        return $this->_name;
    }
    
    public function setManufacturer_affiliate_manufacturer_manufacturer_id($manufacturer_id) {
        $this->_manufacturer_id = $manufacturer_id;
    }

    public function getManufacturer_affiliate_manufacturer_manufacturer_id() {
        return $this->_manufacturer_id;
    }

    public function setManufacturer(Admin_Model_Manufacturer $manufacturer) {
        $this->_manufacturer = $manufacturer;
    }

    public function getManufacturer() {
        return $this->_manufacturer;
    }
    
}
