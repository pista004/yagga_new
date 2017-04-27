<?php

class Admin_ProductController extends Zend_Controller_Action {

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
        $page = 0;
        if ($this->getRequest()->getParam('page')) {
            $page = $this->getRequest()->getParam('page');
        }


        $searchForm = new Admin_Form_SearchForm();
        $this->view->formSearch = $searchForm;





        $formFilter = new Admin_Form_FilterForm();

        /*
         * 
         * POZOR TODO: do filtru se davaji i vyrobci, kteri maji nektivni produkty
         * 
         */
        $childs = $this->_categoryMapper->getChildsCache(0, true);
        $categoryChildsAry = $this->_categoryMapper->getCategoryChildsAryCache($childs);
//        print_r($categoryChildsAry);die;

        $categoryToForm = array();

        foreach ($categoryChildsAry as $category) {
            $categoryToForm[$category->getCategory_id()] = $category->getCategory_structure();
        }

        $formFilter->setCategories($categoryToForm);

        $paramsToForm = array();
        $paramsToForm = array('category_id' => $categoryToForm);


        $formFilter->startForm();


        /*
         * 
         * ziskani parametru z url a nastaveni where parametru
         * 
         */

        $whereParams = array();
        $categoriesParams = array();
        $categoriesParams = $this->getRequest()->getParam('category_id');


        if (!empty($categoriesParams)) {
            $whereParams['category_id'] = $categoriesParams;
        }

        if (!empty($categoriesParams)) {
            $formFilter->populate($this->getRequest()->getParams());
        }
        $this->view->formFilter = $formFilter;

        $this->view->filterParams = array_diff_assoc($this->getRequest()->getParams(), $this->getRequest()->getUserParams());




        /*
         * poslu do view aktivni filtery - zobrazim s krizkem pro zruseni filtru
         */
        $currentUrl = $this->getRequest()->getHttpHost() . '' . $this->getRequest()->getRequestUri();
        $activeFilterItems = array();

//                                print_r($whereParams);die;


        foreach ($whereParams as $wKey => $wParams) {
            foreach ($wParams as $wParam) {
                $urlParams = array_diff_assoc($this->getRequest()->getParams(), $this->getRequest()->getUserParams());

                if (array_key_exists($wKey, $urlParams)) {
                    unset($urlParams[$wKey][array_search($wParam, $urlParams[$wKey])]);
                }

                $urlWithoutParams = substr($this->getRequest()->getRequestUri(), 0, strpos($this->getRequest()->getRequestUri(), "?"));

                $filterItemUrl = 'http://' . $this->getRequest()->getHttpHost() . '' . $urlWithoutParams . '?' . urldecode(http_build_query($urlParams));

                /*
                 * variant name - ted bere jen manufacturer
                 */
                $activeFilterItems[$wParam] = array('name' => $paramsToForm[$wKey][$wParam], 'url' => $filterItemUrl);
            }
        }

        $this->view->activeFilterParams = $activeFilterItems;


        $orderParams = array('p.product_id' => 'DESC');


        $productResult = $this->_productMapper->getProducts($page, 20, $whereParams, $orderParams);

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

        $this->view->productResult = $productResult;

        $this->view->paginator = $this->_productMapper->_paginator;
    }

    public function addAction() {

        $heurekaCategoriesAry = array();
        $heurekaCategories = new My_HeurekaCategories();
        $heurekaCategoriesAry[0] = '--Vyberte--';
        $heurekaCategoriesAry += $heurekaCategories->getCategories();


        $childs = $this->_categoryMapper->getChildsCache(0, true);
        $categoryChildsAry = $this->_categoryMapper->getCategoryChildsAryCache($childs);
        $categoryToForm = array();

        foreach ($categoryChildsAry as $category) {
            $categoryToForm[$category->getCategory_id()] = $category->getCategory_structure();
        }

        $manufacturers = $this->_manufacturerMapper->getManufacturers();
        $manufacturerToForm = array();
        $manufacturerToForm[0] = '--Vyberte--';
        foreach ($manufacturers as $manufacturer) {
            $manufacturerToForm[$manufacturer->getManufacturer_id()] = $manufacturer->getManufacturer_name();
        }

        $form = new Admin_Form_EditProductForm();
        $form->setManufacturer($manufacturerToForm);
        $form->setCategory($categoryToForm);
        $form->setHeurekaCategory($heurekaCategoriesAry);
        $form->startForm();

//smazu element product_variant_name, ten slouzi pouze pro varianty
        $form->removeElement('product_variant_name');

        $this->view->form = $form;

        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
            $product = new Admin_Model_Product();
            $product->setOptions($form->getValues());
            $product->setProduct_manufacturer_id((int) $form->getValue('product_manufacturer'));
            $product->setProduct_insert_date(time());

//            $toUrl = $product->getProduct_name();
//            $filterUrl = new Filter_Url();
//            $url = $filterUrl->filter($toUrl);
//
//            $product->setProduct_url($url);
//            
            // u editace kontroluju jestli byl zmenen nazev, potom menim i url, jinak url nemenim  
            if ($product->getProduct_url()) {
                $toUrl = $product->getProduct_url();
                $filterUrl = new Filter_Url();
                $url = $filterUrl->filter($toUrl);

                $filteredUrl = $filterUrl->checkProductUrl($url);
                $product->setProduct_url($filteredUrl);
            } else {
                $toUrl = $product->getProduct_name();
                $filterUrl = new Filter_Url();
                $url = $filterUrl->filter($toUrl);

                $filteredUrl = $filterUrl->checkProductUrl($url);
                $product->setProduct_url($filteredUrl);
            }

            if ($this->getRequest()->getParam('product_expire_date')) {
                //prevod data ve formatu 22. 10. 2014 na timestamp
                list($day, $month, $year) = explode('. ', $form->getValue('product_expire_date'));
                $product->setProduct_expire_date(mktime(0, 0, 0, $month, $day, $year));
            } else {
                $product->setProduct_expire_date(new Zend_Db_Expr('NULL'));
            }

            if ($this->getRequest()->getParam('product_active_from_date')) {
                //prevod data ve formatu 22. 10. 2014 na timestamp
                list($day, $month, $year) = explode('. ', $form->getValue('product_active_from_date'));
                $product->setProduct_active_from_date(mktime(0, 0, 0, $month, $day, $year));
            } else {
                $product->setProduct_active_from_date(new Zend_Db_Expr('NULL'));
            }

            if ($product->getProduct_manufacturer_id() <= 0) {
                $product->setProduct_manufacturer_id(null);
            }

            $db = $this->_productMapper->getDbTable()->getDefaultAdapter();
            $db->beginTransaction();

            $lastProductId = 0;
            try {

//                print_r($product);die;

                $this->_productMapper->save($product);

                $lastProductId = $this->_productMapper->getDbTable()->getDefaultAdapter()->lastInsertId();

                $productCategoryIds = $this->getRequest()->getParam('product_category');

                if (!is_array($productCategoryIds)) {
                    $productCategoryIds = array();
                }

                if (!empty($productCategoryIds)) {
                    foreach ($productCategoryIds as $productCategoryId) {
                        $productCategory = new Admin_Model_ProductCategory();
                        $productCategory->setProduct_Category_Category_id($productCategoryId);
                        $productCategory->setProduct_Category_Product_id($lastProductId);

                        $this->_productCategoryMapper->save($productCategory);
                    }
                }

                $db->commit();
                $this->_flashMessenger->addMessage(array('info' => 'Produkt byl úspěšně vložen.'));
            } catch (Exception $e) {
                $this->_flashMessenger->addMessage(array('error' => 'Při ukládání produktu nastala chyba!<br />' . $e->getMessage()));
                $db->rollBack();
            }

            if ($lastProductId < 1) {
                $module = $this->getRequest()->getModuleName();
                $controller = $this->getRequest()->getControllerName();
                $this->_redirect($module . '/' . $controller);
            } else {
                $module = $this->getRequest()->getModuleName();
                $controller = $this->getRequest()->getControllerName();
                $this->_redirect($module . '/' . $controller . '/edit/id/' . $lastProductId);
            }
        } else {
            $form->populate($form->getValues());
        }
    }

    public function editAction() {

        $product_id = (int) $this->getRequest()->getParam('id');
        $productMap = $this->_productMapper->find($product_id);


//        print_r($productMap);
//        die;
        if (!empty($productMap)) {


            if (!$productMap->getProduct_itemgroup_product_id()) {

                $this->view->product = $productMap;

                $heurekaCategoriesAry = array();
                $heurekaCategories = new My_HeurekaCategories();
                $heurekaCategoriesAry[NULL] = '--Vyberte--';
                $heurekaCategoriesAry += $heurekaCategories->getCategories();


                $childs = $this->_categoryMapper->getChildsCache(0, true);
                $categoryChildsAry = $this->_categoryMapper->getCategoryChildsAryCache($childs);
                $categoryToForm = array();

                foreach ($categoryChildsAry as $category) {
                    $categoryToForm[$category->getCategory_id()] = $category->getCategory_structure();
                }

                //ziskani vyrobcu
                $manufacturers = $this->_manufacturerMapper->getManufacturers();
                $manufacturerToForm = array();
                $manufacturerToForm[NULL] = '--Vyberte--';
                foreach ($manufacturers as $manufacturer) {
                    $manufacturerToForm[$manufacturer->getManufacturer_id()] = $manufacturer->getManufacturer_name();
                }


                $productCategoriesToPopulate = array();
                $productCategories = $this->_productCategoryMapper->getByProductId($product_id);
                if (!empty($productCategories)) {
                    foreach ($productCategories as $pc) {
                        $productCategoriesToPopulate[] = $pc->getProduct_category_category_id();
                    }
                }

                //formular pro produkt + naplnim daty
                $form = new Admin_Form_EditProductForm();
                $form->setManufacturer($manufacturerToForm);
                $form->setCategory($categoryToForm);
                $form->setHeurekaCategory($heurekaCategoriesAry);
                $form->setUrl($productMap->getProduct_url());

                $form->startForm();

//smazu element product_variant_name, ten slouzi pouze pro varianty
                $form->removeElement('product_variant_name');

                $form->populate($productMap->toArray());
                if (!empty($productCategoriesToPopulate)) {
                    $values = array(
                        'product_category' => $productCategoriesToPopulate
                    );
                    $form->populate($values);
                }
//            $form->populate($manufacturerToForm)
                $form->getElement('product_manufacturer_id')->setValue($productMap->getProduct_manufacturer_id());
                if ($productMap->getProduct_expire_date()) {
                    $form->getElement('product_expire_date')->setValue(date('d. m. Y', $productMap->getProduct_expire_date()));
                }
                if ($productMap->getProduct_active_from_date()) {
                    $form->getElement('product_active_from_date')->setValue(date('d. m. Y', $productMap->getProduct_active_from_date()));
                }
                $this->view->form = $form;


                if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {

                    $postData = $this->getRequest()->getPost();

// k porovnani, zda doslo ke zmene nepotrebuji submit a product_category, proto odstranim z pole
                    unset($postData['submit']);
                    unset($postData['product_category']);

// ziskam pouze zmenena data                    
                    $dataToUpdate = array();
                    $rowsToUpdate = array();

                    $dataToUpdate = array_diff_assoc($postData, $productMap->toArray());

                    if (!empty($dataToUpdate)) {

                        $product = new Admin_Model_Product();
                        $product->setOptions($dataToUpdate);

                        foreach ($dataToUpdate as $productKey => $productValue) {

                            if ($productKey == 'product_expire_date') {
                                //prevod data ve formatu 22. 10. 2014 na timestamp
                                list($day, $month, $year) = explode('. ', $form->getValue('product_expire_date'));
                                $product->setProduct_expire_date(mktime(0, 0, 0, $month, $day, $year));
                            }

                            if ($productKey == 'product_active_from_date') {
                                //prevod data ve formatu 22. 10. 2014 na timestamp
                                list($day, $month, $year) = explode('. ', $form->getValue('product_active_from_date'));
                                $product->setProduct_active_from_date(mktime(0, 0, 0, $month, $day, $year));
                            }

                            if ($productKey == 'product_url') {
                                $toUrl = $productValue;
                                $filterUrl = new Filter_Url();
                                $url = $filterUrl->filter($toUrl);
                                $product->setProduct_url($url);
                            }
                        }

                        $product->setProduct_id($product_id);

                        $rowsToUpdate = array_keys($dataToUpdate);
// k sloupcum, ktere se maji aktualizovat pridam i product_id, bez toho to nejde, to je dulezite                        
                        $rowsToUpdate[] = 'product_id';
                    }


                    $db = $this->_productMapper->getDbTable()->getDefaultAdapter();
                    $db->beginTransaction();

                    try {
                        //ziskam aktualni kategorie pro ulozeni
                        $productCategoryIds = $this->getRequest()->getParam('product_category');

                        if (!is_array($productCategoryIds)) {
                            $productCategoryIds = array();
                        }

                        //porovnam puvodni kategorie($productCategoriesToPopulate) s aktualne odeslanymi($productCategoryIds) k ulozeni, ty ktere k ulozeni nejsou, ty smazu
                        $toDelete = array();
                        if (is_array($productCategoriesToPopulate) && is_array($productCategoryIds)) {
                            $toDelete = array_diff($productCategoriesToPopulate, $productCategoryIds);
                        }

//                    print_r($toDelete);die;

                        if (!empty($toDelete)) {
                            foreach ($toDelete as $deleteProductCategoryId) {
                                $this->_productCategoryMapper->deleteByCategoryIdProductId($deleteProductCategoryId, $product_id);
                            }
                        }

                        if (!empty($productCategoryIds)) {
                            foreach ($productCategoryIds as $productCategoryId) {
                                $productCategory = new Admin_Model_ProductCategory();
                                $productCategory->setProduct_Category_Category_id($productCategoryId);
                                $productCategory->setProduct_Category_Product_id($product_id);

                                $this->_productCategoryMapper->save($productCategory);
                            }
                        }

                        $this->_productMapper->save($product, $rowsToUpdate);

                        $db->commit();
                        $this->_flashMessenger->addMessage(array('info' => 'Produkt byl úspěšně upraven.'));
                    } catch (Exception $e) {
                        $this->_flashMessenger->addMessage(array('error' => 'Při ukládání produktu nastala chyba!<br />' . $e->getMessage()));
                        $db->rollBack();
                    }

                    $this->_redirect($this->getRequest()->getRequestUri());
                } else {
                    $form->populate($form->getValues());
                }
            } else {
                throw new ErrorException;
            }
        } else {
            throw new ErrorException;
        }
    }

    public function deleteAction() {

        /*
         * TODO: mazat i fotky!!!!
         */

        $id = (int) $this->getRequest()->getParam('id');

        try {
            $this->_productMapper->delete($id);
            $this->_flashMessenger->addMessage(array('info' => 'Produkt byl úspěšně smazán.'));
        } catch (Exception $e) {
            $this->_flashMessenger->addMessage(array('error' => 'Při pokusu o smazání došlo k chybě!<br />' . $e->getMessage()));
        }
        $module = $this->getRequest()->getModuleName();
        $controller = $this->getRequest()->getControllerName();
        $this->_redirect($module . '/' . $controller);
    }

}

