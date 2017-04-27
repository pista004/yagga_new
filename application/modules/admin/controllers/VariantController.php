<?php

class Admin_VariantController extends Zend_Controller_Action {

    private $_productMapper;
    private $_manufacturerMapper;
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
        $this->_categoryMapper = new Admin_Model_CategoryMapper();
        $this->_productCategoryMapper = new Admin_Model_ProductCategoryMapper();
    }

    public function indexAction() {
        $productId = $this->getRequest()->getParam('product_id');
        if ($productId) {

            $productMap = $this->_productMapper->find($productId);

// kontroluju, jestli produkt ma itemgroup id, kdyz nema, jedna se o hlavni produkt a muzu pridat variantu, jinak ne            
            if (!$productMap->getProduct_itemgroup_product_id()) {

                $this->view->product = $productMap;

                $others['where'] = array('product_itemgroup_product_id' => $productId);

                $variants = array();
                $variants = $this->_productMapper->getProductsByItemgroupId(0, -1, $others);

                $this->view->variants = $variants;

                $this->view->paginator = $this->_productMapper->_paginator;
            } else {
                throw new ErrorException;
            }
        } else {
            throw new ErrorException;
        }
    }

    public function addAction() {

        $productId = $this->getRequest()->getParam('product_id');

        if ($productId) {

            $productMap = $this->_productMapper->find($productId);

// kontroluju, jestli produkt ma itemgroup id, kdyz nema, jedna se o hlavni produkt a muzu pridat variantu, jinak ne            
            if (!$productMap->getProduct_itemgroup_product_id()) {

                $form = new Admin_Form_EditProductForm();
                $form->startForm();
                
// set products values from main product to variants               
                $form->populate(
                        array(
                            'product_name' => $productMap->getProduct_name(),
                            'product_price' => $productMap->getProduct_price()
                        )
                );
                $this->view->form = $form;

                if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
                    $product = new Admin_Model_Product();
                    $product->setOptions($this->getRequest()->getPost());
                    $product->setProduct_insert_date(time());

//nastavim url, po vlozeni url ale jeste ziskam ID vlozene varianty a provedu update na url, tak aby obsahovala id varianty - na lepsi zpusob jsem neprisel
                    $toUrl = $product->getProduct_name().'-'.$product->getProduct_variant_name();
                    $filterUrl = new Filter_Url();
                    $url = $filterUrl->filter($toUrl);
                    $product->setProduct_url($url);

//do product_itemgroup_product_id nastavim ID hlavniho produktu
                    $product->setProduct_itemgroup_product_id($productId);


                    $db = $this->_productMapper->getDbTable()->getDefaultAdapter();
                    $db->beginTransaction();

                    try {
                        $this->_productMapper->save($product);

// ziskam id vlozeneho zaznamu, upravim url tak, ze do ni pridam id produktu
                        $lastProductId = $this->_productMapper->getDbTable()->getDefaultAdapter()->lastInsertId();

                        if ($lastProductId) {
                            $variantUrl = $product->getProduct_url();
                            $filterUrl = new Filter_Url();
                            $url = $filterUrl->filter($variantUrl . '-' . $lastProductId);

                            $variant = new Admin_Model_Product();
                            $variant->setProduct_id($lastProductId);
                            $variant->setProduct_url($url);


                            $this->_productMapper->save($variant, array('product_id', 'product_url'));
                        }

                        $db->commit();
                        $this->_flashMessenger->addMessage(array('info' => 'Varianta byla úspěšně vložena.'));
                    } catch (Exception $e) {
                        $this->_flashMessenger->addMessage(array('error' => 'Při ukládání varianty nastala chyba!<br />' . $e->getMessage()));
                        $db->rollBack();
                    }

                    $module = $this->getRequest()->getModuleName();
                    $controller = $this->getRequest()->getControllerName();
                    $this->_redirect($module . '/' . $controller . '/index/product_id/' . $productId);
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

    public function editAction() {

        $variantId = $this->getRequest()->getParam('id');

        if ($variantId) {


            $variantMap = $this->_productMapper->find($variantId);

// kontroluju, jestli produkt ma itemgroup id, kdyz nema, jedna se o hlavni produkt a muzu upravit variantu, jinak ne            
            if ($variantMap->getProduct_itemgroup_product_id()) {

                $this->view->product = $variantMap;

                $form = new Admin_Form_EditProductForm();
                $form->startForm();
                $this->view->form = $form;

                $form->populate($variantMap->toArray());

                if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {

                    $postData = $this->getRequest()->getPost();
                    unset($postData['submit']);

                    $changedValues = array_diff_assoc($postData, $variantMap->toArray());

                    if (!empty($changedValues)) {

                        $product = new Admin_Model_Product();
                        $product->setOptions($postData);
                        $product->setProduct_id($variantId);
                        $product->setProduct_itemgroup_product_id($variantMap->getProduct_itemgroup_product_id());

                        $db = $this->_productMapper->getDbTable()->getDefaultAdapter();
                        $db->beginTransaction();


                        try {
                            $this->_productMapper->save($product);

                            $db->commit();
                            $this->_flashMessenger->addMessage(array('info' => 'Varianta byla úspěšně upravena.'));
                        } catch (Exception $e) {
                            $this->_flashMessenger->addMessage(array('error' => 'Při ukládání varianty nastala chyba!<br />' . $e->getMessage()));
                            $db->rollBack();
                        }
                    }

                    $module = $this->getRequest()->getModuleName();
                    $controller = $this->getRequest()->getControllerName();
                    $this->_redirect($module . '/' . $controller . '/index/product_id/' . $variantMap->getProduct_itemgroup_product_id());
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

        $productId = (int) $this->getRequest()->getParam('product_id');
        $id = (int) $this->getRequest()->getParam('id');

        if ($id && $productId) {

            try {
                $this->_productMapper->delete($id);
                $this->_flashMessenger->addMessage(array('info' => 'Varianta byla úspěšně smazána.'));
            } catch (Exception $e) {
                $this->_flashMessenger->addMessage(array('error' => 'Při pokusu o smazání došlo k chybě!<br />' . $e->getMessage()));
            }

            $module = $this->getRequest()->getModuleName();
            $controller = $this->getRequest()->getControllerName();
            $this->_redirect($module . '/' . $controller . '/index/product_id/' . $productId);
        }
    }

//    public function editAction() {
//
//        $product_id = (int) $this->getRequest()->getParam('id');
//        $this->view->product_id = $product_id;
//
//        /*
//         * ziskani variant podle id produktu
//         */
//        $variants = $this->_variantMapper->getVariantsByProductId($product_id);
//
//        //prvotni formular pro vlozeni nove varianty pote je zpracovavan pomoci ajaxu
//        $form_variant_ajax = new Admin_Form_EditVariantForm();
//        $form_variant_ajax->startForm();
//        $form_variant_ajax->getElement('submit')->setAttrib('id', 'edit-variant-ajax');
//        $this->view->form_variant_ajax = $form_variant_ajax;
//
//        $form_variant = new Admin_Form_EditVariantForm();
//        foreach ($variants as $variant) {
//            $form_variant->addConfigSubForm($variant->getVariant_id(), $variant->toArray());
//        }
//        $form_variant->startForm();
//
//        $this->view->form = $form_variant;
//
//        /*
//         * pri odeslani formulare
//         * prochazim subformy foreach a hodnotu isValid ukladam do pole, potom jen kontroluju, jestli je v poli false, v tom pripade neni formular validni
//         */
//        if ($this->getRequest()->isPost()) {
//            $isValidSubform = array();
//            $postedData = $this->getRequest()->getPost();
//
//
//            foreach ($postedData as $subFormName => $data) {
//                if ($form_variant->getSubForm($subFormName) instanceof Zend_Form_SubForm) {
//                    if ($form_variant->getSubForm($subFormName)->isValid($data)) {
//                        $isValidSubform[] = true;
//                    } else {
//                        $isValidSubform[] = false;
//                    }
//                }
//            }
//
//            if (in_array(false, $isValidSubform)) {
//                $form_variant->populate($form_variant->getValues());
//            } else {
//
//                $db = $this->_variantMapper->getDbTable()->getDefaultAdapter();
//                $db->beginTransaction();
//                //pokud se nekde vyskytne chyba, provedu rollback a data nebudou ulozena, pokud vse probehne dobre, provedu commit
//                try {
//
//                    foreach ($postedData as $sfName => $pData) {
//                        if (is_array($pData)) {
//                            $variant = new Admin_Model_Variant();
//                            $variant->setOptions($pData);
//                            $variant->setVariant_product_id($product_id);
//
//                            $this->_variantMapper->save($variant);
//                        }
//                    }
//
//                    //ziskam vsechny varianty, jejich sumu a ulozim jako product_stock
//                    $this->_productMapper->setProductStockFromVariantStocksByProductId($product_id);
//
//                    $db->commit();
//
//                    $this->_flashMessenger->addMessage(array('info' => 'Varianty byly úspěšně aktualizovány.'));
//                } catch (Exception $e) {
//
//                    $this->_flashMessenger->addMessage(array('error' => 'Při ukládání varianty nastala chyba!<br />' . $e->getMessage()));
//
//                    //dojde-li k chybe, provedu rollback, data nebudou ulozena do db
//                    $db->rollBack();
//                }
//
//                $this->_redirect($this->getRequest()->getRequestUri());
//            }
//        }
//    }
//
//    public function deleteAction() {
//
//        $variant_id = (int) $this->getRequest()->getParam('id');
//        $product_id = (int) $this->getRequest()->getParam('product_id');
//
//        $db = $this->_variantMapper->getDbTable()->getDefaultAdapter();
//        $db->beginTransaction();
//
//        try {
//            $this->_variantMapper->delete($variant_id);
//            //ziskam vsechny varianty, jejich sumu a ulozim jako product_stock
//            $this->_productMapper->setProductStockFromVariantStocksByProductId($product_id);
//            $this->_flashMessenger->addMessage(array('info' => 'Varianta byla úspěšně smazána.'));
//            $db->commit();
//        } catch (Exception $e) {
//            $this->_flashMessenger->addMessage(array('error' => 'Při pokusu o smazání došlo k chybě!<br />' . $e->getMessage()));
//            //dojde-li k chybe, provedu rollback, data nebudou ulozena do db
//            $db->rollBack();
//        }
//        $this->_redirect($this->getRequest()->getHeader('referer'));
//    }
}

