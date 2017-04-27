<?php

class Admin_SearchController extends Zend_Controller_Action {

    private $_productMapper;
    private $_manufacturerMapper;
    private $_variantMapper;
    private $_categoryMapper;
    private $_productCategoryMapper;
    private $_flashMessenger;

    public function init() {
        $this->_flashMessenger = $this->_helper->FlashMessenger;

        $flashMessenger = $this->_flashMessenger->getMessages();
        if (!empty($flashMessenger)) {
            $currentMessage = current($flashMessenger);
            if (!empty($currentMessage['info'])) {
                $this->view->infoFlashMessage = $currentMessage['info'];
            } else if (!empty($currentMessage['error'])) {
                $this->view->errorFlashMessage = $currentMessage['error'];
            }
        }

        $this->_productMapper = new Admin_Model_ProductMapper();
        $this->_manufacturerMapper = new Admin_Model_ManufacturerMapper();
        $this->_variantMapper = new Admin_Model_VariantMapper();
        $this->_categoryMapper = new Admin_Model_CategoryMapper();
        $this->_productCategoryMapper = new Admin_Model_ProductCategoryMapper();
    }

    public function indexAction() {

        $searchForm = new Admin_Form_SearchForm();
        $this->view->formSearch = $searchForm;


        $productResult = array();

        $searchTerm = trim($this->getRequest()->getParam('term'));
//        $searchTerm = Zend_Db_Table::getDefaultAdapter()->quote($requestTerm);

        $this->view->searchTerm = $searchTerm;

        if (strlen($searchTerm)) {

            $page = 0;
            if ($this->getRequest()->getParam('page')) {
                $page = $this->getRequest()->getParam('page');
            }


//            $orderParams = array('p.product_insert_date' => 'DESC');

            $whereParams = array(
                'p.product_id' => $searchTerm,
                'p.product_name' => $searchTerm,
                'p.product_url' => $searchTerm,
                'p.product_code' => $searchTerm,
                'p.product_ean' => $searchTerm,
            );

            $productResult = $this->_productMapper->getProductsBySearchTerm($page, 20, $whereParams);


            /*
             * ziskam kategorie k produktum, podle id produktu
             */
            if (!empty($productResult)) {
                $productCategories = $this->_categoryMapper->getCategoriesByProductIds(array_keys($productResult));

                //projdu produkty a pridam kategorie do kterych je produkt prirazen
                foreach ($productResult as $product) {
                    if (array_key_exists($product->getProduct_id(), $productCategories)) {
                        $product->setCategories($productCategories[$product->getProduct_id()]);
                    }
                }
            }

            $this->view->filterParams = array_diff_assoc($this->getRequest()->getParams(), $this->getRequest()->getUserParams());


            $this->view->paginator = $this->_productMapper->_paginator;
        }

        $this->view->productResult = $productResult;
    }

}

