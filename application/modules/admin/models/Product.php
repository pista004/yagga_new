<?php

class Admin_Model_Product extends Model_Model {

    protected $_id;
    protected $_name;
    protected $_variant_name;
    protected $_stock;
    protected $_purchase_price;
    protected $_recommended_price;
    protected $_price;
    protected $_perex;
    protected $_code;
    protected $_ean;
    protected $_url;
    protected $_affiliate_url;
    protected $_seo_title;
    protected $_seo_meta_description;
    protected $_description;
    protected $_insert_date;
    protected $_active_from_date;
    protected $_expire_date;
    protected $_category_heureka;
    protected $_action;
    protected $_sale;
    protected $_new;
    protected $_recommend;
    protected $_affiliate_program_name;
    protected $_is_active;
    protected $_manufacturer_id;
    protected $_itemgroup_product_id;
    protected $_manufacturer;
    protected $_variant;
    protected $_variant_stock_sum;
    protected $_photographies;
    protected $_main_photography;
    protected $_category;
    protected $_categories;
    protected $_affiliate_program;
    protected $_variants_count;

    public function setProduct_id($id) {
        $this->_id = (int) $id;
    }

    public function getProduct_id() {
        return $this->_id;
    }

    public function setProduct_name($name) {
        $this->_name = $name;
    }

    public function getProduct_name() {
        return $this->_name;
    }

    public function setProduct_variant_name($variant_name) {
        $this->_variant_name = $variant_name;
    }

    public function getProduct_variant_name() {
        return $this->_variant_name;
    }

    public function setProduct_stock($stock = NULL) {
        $this->_stock = $stock;
    }

    public function getProduct_stock() {
        return $this->_stock;
    }

    public function setProduct_purchase_price($purchase_price = NULL) {
        $this->_purchase_price = $purchase_price;
    }

    public function getProduct_purchase_price() {
        return $this->_purchase_price;
    }

    public function setProduct_recommended_price($recommended_price = NULL) {
        $this->_recommended_price = $recommended_price;
    }

    public function getProduct_recommended_price() {
        return $this->_recommended_price;
    }

    public function setProduct_price($price) {
        $this->_price = $price;
    }

    public function getProduct_price() {
        return $this->_price;
    }

    public function setProduct_perex($perex) {
        $this->_perex = $perex;
    }

    public function getProduct_perex() {
        return $this->_perex;
    }

    public function setProduct_code($code) {
        $this->_code = $code;
    }

    public function getProduct_code() {
        return $this->_code;
    }

    public function setProduct_ean($ean) {
        $this->_ean = $ean;
    }

    public function getProduct_ean() {
        return $this->_ean;
    }

    public function setProduct_url($url) {
        $this->_url = $url;
    }

    public function getProduct_url() {
        return $this->_url;
    }

    public function setProduct_affiliate_url($affiliate_url) {
        $this->_affiliate_url = $affiliate_url;
    }

    public function getProduct_affiliate_url() {
        return $this->_affiliate_url;
    }

    public function setProduct_seo_title($seo_title) {
        $this->_seo_title = $seo_title;
    }

    public function getProduct_seo_title() {
        return $this->_seo_title;
    }

    public function setProduct_seo_meta_description($seo_meta_description) {
        $this->_seo_meta_description = $seo_meta_description;
    }

    public function getProduct_seo_meta_description() {
        return $this->_seo_meta_description;
    }

    public function setProduct_description($description) {
        $this->_description = $description;
    }

    public function getProduct_description() {
        return $this->_description;
    }

    public function setProduct_insert_date($insert_date) {
        $this->_insert_date = $insert_date;
    }

    public function getProduct_insert_date() {
        return $this->_insert_date;
    }

    public function setProduct_active_from_date($active_from_date) {
        $this->_active_from_date = $active_from_date;
    }

    public function getProduct_active_from_date() {
        return $this->_active_from_date;
    }

    public function setProduct_expire_date($expire_date) {
        $this->_expire_date = $expire_date;
    }

    public function getProduct_expire_date() {
        return $this->_expire_date;
    }

    public function setProduct_category_heureka($category_heureka) {
        $this->_category_heureka = $category_heureka;
    }

    public function getProduct_category_heureka() {
        return $this->_category_heureka;
    }

    public function setProduct_action($action) {
        $this->_action = $action;
    }

    public function getProduct_action() {
        return $this->_action;
    }

    public function setProduct_sale($sale) {
        $this->_sale = $sale;
    }

    public function getProduct_sale() {
        return $this->_sale;
    }

    public function setProduct_new($new) {
        $this->_new = $new;
    }

    public function getProduct_new() {
        return $this->_new;
    }

    public function setProduct_recommend($recommend) {
        $this->_recommend = $recommend;
    }

    public function getProduct_recommend() {
        return $this->_recommend;
    }

    public function setProduct_affiliate_program_name($affiliate_program_name) {
        $this->_affiliate_program_name = $affiliate_program_name;
    }

    public function getProduct_affiliate_program_name() {
        return $this->_affiliate_program_name;
    }

    public function setProduct_is_active($is_active) {
        $this->_is_active = $is_active;
    }

    public function getProduct_is_active() {
        return $this->_is_active;
    }

    public function setProduct_manufacturer_id($manufacturer_id) {
        $this->_manufacturer_id = $manufacturer_id;
    }

    public function getProduct_manufacturer_id() {
        return $this->_manufacturer_id;
    }

    public function setProduct_itemgroup_product_id($itemgroup_product_id) {
        $this->_itemgroup_product_id = $itemgroup_product_id;
    }

    public function getProduct_itemgroup_product_id() {
        return $this->_itemgroup_product_id;
    }

    public function setManufacturer(Admin_Model_Manufacturer $manufacturer) {
        $this->_manufacturer = $manufacturer;
    }

    public function getManufacturer() {
        return $this->_manufacturer;
    }

    /*
     * 
     * parametry mimo DB
     * 
     */

    public function setVariant(array $variant) {
        $this->_variant = $variant;
    }

    public function getVariant() {
        return $this->_variant;
    }

    public function setVariant_stock_sum($variant_stock_sum) {
        $this->_variant_stock_sum = $variant_stock_sum;
    }

    public function getVariant_stock_sum() {
        return $this->_variant_stock_sum;
    }

    public function setPhotographies(Admin_Model_Photography $photography) {
        $this->_photographies = $photography;
    }

    public function getPhotographies() {
        return $this->_photographies;
    }

    public function setMain_photography(Admin_Model_Photography $main_photography) {
        $this->_main_photography = $main_photography;
    }

    public function getMain_photography() {
        return $this->_main_photography;
    }

    public function setCategory(Admin_Model_Category $category) {
        $this->_category = $category;
    }

    public function getCategory() {
        return $this->_category;
    }

    public function setCategories(array $categories) {
        $this->_categories = $categories;
    }

    public function getCategories() {
        return $this->_categories;
    }

    public function setAffiliate_program(Admin_Model_AffiliateProgram $affiliateProgram) {
        $this->_affiliate_program = $affiliateProgram;
    }

    public function getAffiliate_program() {
        return $this->_affiliate_program;
    }

    public function setVariants_count($variantsCount) {
        $this->_variants_count = $variantsCount;
    }

    public function getVariants_count() {
        return $this->_variants_count;
    }

}
