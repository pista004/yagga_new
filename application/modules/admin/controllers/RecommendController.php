<?php

class Admin_RecommendController extends Zend_Controller_Action {

    private $_productMapper;
    private $_productRecommendMapper;
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
        $this->_productRecommendMapper = new Admin_Model_ProductRecommendMapper();
    }

    public function editAction() {

        $product_id = (int) $this->getRequest()->getParam('id');
        if ($product_id) {

            $this->view->product_id = $product_id;

            $products = $this->_productMapper->getProducts();

            $productRecommend = $this->_productRecommendMapper->getProductRecommend($product_id);

            $productsRecommend = array();
            foreach ($productRecommend as $pr) {
                $productsRecommend[$pr->getProduct_recommend_id()] = $products[$pr->getProduct_recommend_id()];
            }

            $this->view->recommendProducts = $productsRecommend;

//            print_r($productsRecommend);
//            die;
            //odstranim aktualni produkt, ten do selectu nechci
            unset($products[$product_id]);

            $productsToForm = array();
            $productsToForm[0] = "--Vyberte--";
            foreach ($products as $product) {
                if (!array_key_exists($product->getProduct_id(), $productsRecommend)) {
                    $productsToForm[$product->getProduct_id()] = $product->getProduct_name();
                }
            }

            $form = new Admin_Form_EditRecommendForm();
            $form->setProduct($productsToForm);
            $form->startForm();

            $this->view->form = $form;


            if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
                $productRecommend = new Admin_Model_ProductRecommend();
                $productRecommend->setProduct_recommend_id($form->getValue('product_recommend_id'));
                $productRecommend->setProduct_recommend_product_id($product_id);

                try {
                    $this->_productRecommendMapper->save($productRecommend);

                    $this->_flashMessenger->addMessage(array('info' => 'Produkt byl úspěšně vložen.'));
                } catch (Exception $e) {
                    $this->_flashMessenger->addMessage(array('error' => 'Při ukládání produktu nastala chyba!<br />' . $e->getMessage()));
                }

                $module = $this->getRequest()->getModuleName();
                $controller = $this->getRequest()->getControllerName();
                $this->_redirect($module . "/" . $controller . "/edit/id/" . $product_id);
            } else {
                $form->populate($form->getValues());
            }
        }
    }

    public function deleteAction() {
        $id = (int) $this->getRequest()->getParam('id');
        $product_id = (int) $this->getRequest()->getParam('product_id');

        try {
            $this->_productRecommendMapper->delete($id, $product_id);
            $this->_flashMessenger->addMessage(array('info' => 'Doporučený produkt byl úspěšně smazán.'));
        } catch (Exception $e) {
            $this->_flashMessenger->addMessage(array('error' => 'Při pokusu o smazání došlo k chybě!<br />' . $e->getMessage()));
        }

        $module = $this->getRequest()->getModuleName();
        $controller = $this->getRequest()->getControllerName();
        $this->_redirect($module . "/" . $controller . "/edit/id/" . $product_id);
    }

//    public function addAction() {
//
//        $childs = $this->_categoryMapper->getChilds(0, true);
//        $categoryChildsAry = $this->_categoryMapper->getCategoryChildsAry($childs);
//        $categoryToForm = array();
//
//        foreach ($categoryChildsAry as $category) {
//            $categoryToForm[$category->getCategory_id()] = $category->getCategory_structure();
//        }
//
//        $manufacturers = $this->_manufacturerMapper->getManufacturers();
//        $manufacturerToForm = array();
//        $manufacturerToForm[0] = '--Vyberte--';
//        foreach ($manufacturers as $manufacturer) {
//            $manufacturerToForm[$manufacturer->getManufacturer_id()] = $manufacturer->getManufacturer_name();
//        }
//
//        $form = new Admin_Form_EditProductForm();
//        $form->setManufacturer($manufacturerToForm);
//        $form->setCategory($categoryToForm);
//        $form->startForm();
//        $this->view->form = $form;
//
//        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
//            $product = new Admin_Model_Product();
//            $product->setOptions($form->getValues());
//            $product->setProduct_manufacturer_id((int) $form->getValue('product_manufacturer'));
//            $product->setProduct_insert_date(time());
//
//            $toUrl = $product->getProduct_name();
//            $filterUrl = new Filter_Url();
//            $url = $filterUrl->filter($toUrl);
//
//            $product->setProduct_url($url);
//
//
//            if ($this->getRequest()->getParam('product_expire_date')) {
//                //prevod data ve formatu 22. 10. 2014 na timestamp
//                list($day, $month, $year) = explode('. ', $form->getValue('product_expire_date'));
//                $product->setProduct_expire_date(mktime(0, 0, 0, $month, $day, $year));
//            } else {
//                $product->setProduct_expire_date(new Zend_Db_Expr('NULL'));
//            }
//
//            if ($this->getRequest()->getParam('product_active_from_date')) {
//                //prevod data ve formatu 22. 10. 2014 na timestamp
//                list($day, $month, $year) = explode('. ', $form->getValue('product_active_from_date'));
//                $product->setProduct_active_from_date(mktime(0, 0, 0, $month, $day, $year));
//            } else {
//                $product->setProduct_active_from_date(new Zend_Db_Expr('NULL'));
//            }
//
//            try {
//
//                $this->_productMapper->save($product);
//
//                $lastProductId = $this->_productMapper->getDbTable()->getDefaultAdapter()->lastInsertId();
//
//                $productCategoryIds = $this->getRequest()->getParam('product_category');
//
//                if (!is_array($productCategoryIds)) {
//                    $productCategoryIds = array();
//                }
//
//                if (!empty($productCategoryIds)) {
//                    foreach ($productCategoryIds as $productCategoryId) {
//                        $productCategory = new Admin_Model_ProductCategory();
//                        $productCategory->setProduct_Category_Category_id($productCategoryId);
//                        $productCategory->setProduct_Category_Product_id($lastProductId);
//
//                        $this->_productCategoryMapper->save($productCategory);
//                    }
//                }
//
//                $this->_flashMessenger->addMessage(array('info' => 'Produkt byl úspěšně vložen.'));
//            } catch (Exception $e) {
//                $this->_flashMessenger->addMessage(array('error' => 'Při ukládání produktu nastala chyba!<br />' . $e->getMessage()));
//            }
//
//            $module = $this->getRequest()->getModuleName();
//            $controller = $this->getRequest()->getControllerName();
//            $this->_redirect($module . '/' . $controller);
//        } else {
//            $form->populate($form->getValues());
//        }
//    }
//
//    public function editAction() {
//
//        $product_id = (int) $this->getRequest()->getParam('id');
//        $productMap = $this->_productMapper->find($product_id);
//
//        if (!empty($productMap)) {
//
//            $childs = $this->_categoryMapper->getChilds(0, true);
//            $categoryChildsAry = $this->_categoryMapper->getCategoryChildsAry($childs);
//            $categoryToForm = array();
//
//            foreach ($categoryChildsAry as $category) {
//                $categoryToForm[$category->getCategory_id()] = $category->getCategory_structure();
//            }
//
//            //ziskani vyrobcu
//            $manufacturers = $this->_manufacturerMapper->getManufacturers();
//            $manufacturerToForm = array();
//            $manufacturerToForm[0] = '--Vyberte--';
//            foreach ($manufacturers as $manufacturer) {
//                $manufacturerToForm[$manufacturer->getManufacturer_id()] = $manufacturer->getManufacturer_name();
//            }
//
//
//            $productCategoriesToPopulate = array();
//            $productCategories = $this->_productCategoryMapper->getByProductId($product_id);
//            if (!empty($productCategories)) {
//                foreach ($productCategories as $pc) {
//                    $productCategoriesToPopulate[] = $pc->getProduct_category_category_id();
//                }
//            }
//
//            //formular pro produkt + naplnim daty
//            $form = new Admin_Form_EditProductForm();
//            $form->setManufacturer($manufacturerToForm);
//            $form->setCategory($categoryToForm);
//            $form->setUrl($productMap->getProduct_url());
//
//            $form->startForm();
//            $form->populate($productMap->toArray());
//            if (!empty($productCategoriesToPopulate)) {
//                $values = array(
//                    'product_category' => $productCategoriesToPopulate
//                );
//                $form->populate($values);
//            }
////            $form->populate($manufacturerToForm)
//            $form->getElement('product_manufacturer')->setValue($productMap->getProduct_manufacturer_id());
//            if ($productMap->getProduct_expire_date()) {
//                $form->getElement('product_expire_date')->setValue(date('d. m. Y', $productMap->getProduct_expire_date()));
//            }
//            if ($productMap->getProduct_active_from_date()) {
//                $form->getElement('product_active_from_date')->setValue(date('d. m. Y', $productMap->getProduct_active_from_date()));
//            }
//            $this->view->form = $form;
//
//            $this->view->product_id = $product_id;
//
//
//            if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
//
//                $product = new Admin_Model_Product();
//                $product->setOptions($form->getValues());
//                $product->setProduct_manufacturer_id((int) $form->getValue('product_manufacturer'));
//
//                if ($this->getRequest()->getParam('product_expire_date')) {
//                    //prevod data ve formatu 22. 10. 2014 na timestamp
//                    list($day, $month, $year) = explode('. ', $form->getValue('product_expire_date'));
//                    $product->setProduct_expire_date(mktime(0, 0, 0, $month, $day, $year));
//                } else {
//                    $product->setProduct_expire_date(new Zend_Db_Expr('NULL'));
//                }
//
//                if ($this->getRequest()->getParam('product_active_from_date')) {
//                    //prevod data ve formatu 22. 10. 2014 na timestamp
//                    list($day, $month, $year) = explode('. ', $form->getValue('product_active_from_date'));
//                    $product->setProduct_active_from_date(mktime(0, 0, 0, $month, $day, $year));
//                } else {
//                    $product->setProduct_active_from_date(new Zend_Db_Expr('NULL'));
//                }
//
//                $product->setProduct_id($product_id);
//
//
//// u editace kontroluju jestli byl zmenen nazev, potom menim i url, jinak url nemenim  
//                if (($productMap->getProduct_name() != $product->getProduct_name())) {
//                    $toUrl = $product->getProduct_name();
//                    $filterUrl = new Filter_Url();
//                    $url = $filterUrl->filter($toUrl);
//                    $product->setProduct_url($url);
//                } else if (($productMap->getProduct_url() != $product->getProduct_url()) && ($product->getProduct_url() != "")) {
//                    $toUrl = $product->getProduct_url();
//                    $filterUrl = new Filter_Url();
//                    $url = $filterUrl->filter($toUrl);
//                    $product->setProduct_url($url);
//                } else {
//                    $product->setProduct_url($productMap->getProduct_url());
//                }
//
//
//                $db = $this->_productMapper->getDbTable()->getDefaultAdapter();
//                $db->beginTransaction();
//
//                try {
//                    $productCategoryIds = $this->getRequest()->getParam('product_category');
//
//                    if (!is_array($productCategoryIds)) {
//                        $productCategoryIds = array();
//                    }
//
//                    //porovnam puvodni kategorie s odeslanymi k ulozeni, ty ktere k ulozeni nejsou, ty smazu
//                    $toDelete = array();
//                    if (is_array($productCategoriesToPopulate) && is_array($productCategoryIds)) {
//                        $toDelete = array_diff($productCategoriesToPopulate, $productCategoryIds);
//                    }
//
//                    if (!empty($toDelete)) {
//                        foreach ($toDelete as $deleteProductCategoryId) {
//                            $this->_productCategoryMapper->deleteByCategoryId($deleteProductCategoryId);
//                        }
//                    }
//
//                    if (!empty($productCategoryIds)) {
//                        foreach ($productCategoryIds as $productCategoryId) {
//                            $productCategory = new Admin_Model_ProductCategory();
//                            $productCategory->setProduct_Category_Category_id($productCategoryId);
//                            $productCategory->setProduct_Category_Product_id($product_id);
//
//                            $this->_productCategoryMapper->save($productCategory);
//                        }
//                    }
//                    $this->_productMapper->save($product);
//
//                    $db->commit();
//                    $this->_flashMessenger->addMessage(array('info' => 'Produkt byl úspěšně upraven.'));
//                } catch (Exception $e) {
//                    $this->_flashMessenger->addMessage(array('error' => 'Při ukládání produktu nastala chyba!<br />' . $e->getMessage()));
//                    $db->rollBack();
//                }
//
//                $module = $this->getRequest()->getModuleName();
//                $controller = $this->getRequest()->getControllerName();
//                $this->_redirect($module . '/' . $controller);
//            } else {
//                $form->populate($form->getValues());
//            }
//        } else {
//            $module = $this->getRequest()->getModuleName();
//            $controller = $this->getRequest()->getControllerName();
//            $this->_redirect($module . '/' . $controller);
//        }
//    }
//
//    public function deleteAction() {
//
//        $id = (int) $this->getRequest()->getParam('id');
//
//        try {
//            $this->_productMapper->delete($id);
//            $this->_flashMessenger->addMessage(array('info' => 'Produkt byl úspěšně smazán.'));
//        } catch (Exception $e) {
//            $this->_flashMessenger->addMessage(array('error' => 'Při pokusu o smazání došlo k chybě!<br />' . $e->getMessage()));
//        }
//        $module = $this->getRequest()->getModuleName();
//        $controller = $this->getRequest()->getControllerName();
//        $this->_redirect($module . '/' . $controller);
//    }
}

