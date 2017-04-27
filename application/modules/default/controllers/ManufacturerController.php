<?php

class ManufacturerController extends Zend_Controller_Action {

    private $_manufacturerMapper;
    private $_categoryMapper;
    private $_productMapper;

    public function init() {

        $this->_manufacturerMapper = new Default_Model_ManufacturerMapper();
        $this->_categoryMapper = new Default_Model_CategoryMapper();
        $this->_productMapper = new Default_Model_ProductMapper();
    }

    public function indexAction() {

        $this->view->headTitle = "Značky";
        $this->view->metaDescription = "Značkové oblečení, boty, kšiltovky, snapbacky, doplňky a další. Na SwagWear.cz najdeš značky jako Adidas, Nike, Reebok, Vans, Komono, Casio, Converse a další.";

        $manufacturers = $this->_manufacturerMapper->getManufacturers();
        
        $manufacturersByAZ = array();
        foreach($manufacturers as $manufacturer){
            $firstChar = strtoupper(substr($manufacturer->getManufacturer_name(), 0, 1));
            $manufacturersByAZ[$firstChar][$manufacturer->getManufacturer_id()] = $manufacturer;
        }
        
        ksort($manufacturersByAZ);
        
        $this->view->manufacturers = $manufacturersByAZ;
    }

    public function detailAction() {


        $url = $this->getRequest()->getParam('manufacturerurl');
        $manufacturer = $this->_manufacturerMapper->getManufacturerByUrl($url);
        if (!empty($manufacturer)) {




            $mCategories = array();
            $mCategories = $this->_categoryMapper->getCategoriesByManufacturer(array('manufacturer_id' => $manufacturer->getManufacturer_id()));

            //projdu mcategories a vytvorim url s parametrem pro filtr vyrobce
            foreach ($mCategories as $mCategory) {

                $mAllUrl = $this->_categoryMapper->getParentsUrl($mCategory->getCategory_parent(), $mCategory->getCategory_url());
                $mUrl = "/" . $mAllUrl . "?" . urldecode(http_build_query(array('manufacturer_id[]' => $manufacturer->getManufacturer_id())));
                $mCategory->setCategory_url($mUrl);
            }

            $this->view->mCategories = $mCategories;

            $page = 0;
            if ($this->getRequest()->getParam('page')) {
                $page = $this->getRequest()->getParam('page');
            }

            $products = $this->_productMapper->getProducts($page, 32, array('where' => 'manufacturer_id = ' . $manufacturer->getManufacturer_id()));
            $this->view->products = $products;
            $this->view->paginator = $this->_productMapper->_paginator;


            $this->view->manufacturer = $manufacturer;

            $this->view->headTitle = $manufacturer->getManufacturer_seo_title() ? $manufacturer->getManufacturer_seo_title() : $manufacturer->getManufacturer_name();

            $StripTagsfilter = new Zend_Filter_StripTags();
            $this->view->metaDescription = $manufacturer->getManufacturer_seo_meta_description() ? $manufacturer->getManufacturer_seo_meta_description() : $StripTagsfilter->filter($manufacturer->getManufacturer_note());

            $this->view->facebookImageUrl = "/images/upload_manufacturer/manufacturer_" . $manufacturer->getManufacturer_id() . "/" . $manufacturer->getManufacturer_photography()->getPhotography_path();

            $this->view->urlManufacturer = preg_replace('%/%', '', $this->view->url(), 1);

        } else {
            throw new ErrorException;
        }
    }

}
