<?php

class IndexController extends Zend_Controller_Action {

    private $_productMapper;
    private $_categoryMapper;
    private $_manufacturerMapper;
    private $_variantMapper;

    public function init() {

        $this->_productMapper = new Default_Model_ProductMapper();
        $this->_categoryMapper = new Default_Model_CategoryMapper();
        $this->_manufacturerMapper = new Default_Model_ManufacturerMapper();
        $this->_variantMapper = new Default_Model_VariantMapper();
    }

    public function indexAction() {
        $this->view->headTitle = "Váš dokonalý outfit";
        $this->view->metaDescription = "Super boty za skvělé ceny. Vyladí Váš outfit k naprosté dokonalosti. Módní doplňky z Yagga.cz Vám udělají radost a budou Vám moc slušet.";

        $form = new Default_Form_AddToCartForm();
        $form->startForm();

        $this->view->form = $form;

        //where/order pro vyber produktu - vyberu jen doporucene produkty
        $sqlOthers = array();
        $sqlOthers['order'] = "product_insert_date DESC";

        $recommended_products = $this->_productMapper->getProducts(1, 8, $sqlOthers);
        $this->view->recommended_products = $recommended_products;

        $variants = array();
        if (!empty($recommended_products)) {
            $variants = $this->_variantMapper->getVariantsByProductIds(array_keys($recommended_products));
        }
        $this->view->variants = $variants;


        $this->view->isRecommend = true;
//print_r($recommended_products);die;
        $manufacturers = array();
        $manufacturersWhere = array('where' => 'manufacturer_name IN ("adidas originals", "timberland", "nike", "komono", "vans", "converse", "Herschel Supply Co.", "happy socks", "new balance", "reebok", "dvs", "casio")');
        $manufacturers = $this->_manufacturerMapper->getManufacturers(0, 12, $manufacturersWhere);
        $this->view->manufacturers = $manufacturers;



//        $instagram = new My_Instagram_Instagram();
//        $this->view->instagram = $instagram->getUsersSelfMediaRecent();
    }

}
