<?php

class Admin_OrderstateController extends Zend_Controller_Action {

    private $_flashMessenger;
    private $_orderStateMapper;

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

        $this->_orderStateMapper = new Admin_Model_OrderStateMapper();
    }

    public function indexAction() {
        $orderStatesResult = $this->_orderStateMapper->getOrderStates();
        $this->view->orderStatesResult = $orderStatesResult;
    }

    public function editAction() {

        $order_state_id = (int) $this->getRequest()->getParam('id');
        if ($order_state_id) {

            $orderStateResult = $this->_orderStateMapper->getOrderStateById($order_state_id);

            $form = new Admin_Form_EditOrderStateForm();
            $form->startForm();

            $form->populate($orderStateResult->toArray());

            $this->view->form = $form;

            if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
                $orderState = new Admin_Model_OrderState();
                $orderState->setOptions($form->getValues());
                $orderState->setOrder_state_id($order_state_id);

                try {
                    $this->_orderStateMapper->save($orderState);
                    $this->_flashMessenger->addMessage(array('info' => 'Stav objednávky byl úspěšně upraven'));
                } catch (Exception $e) {
                    $this->_flashMessenger->addMessage(array('error' => 'Při ukládání stavu objednávky nastala chyba!<br />' . $e->getMessage()));
                }

                $module = $this->getRequest()->getModuleName();
                $controller = $this->getRequest()->getControllerName();
                $this->_redirect($module . '/' . $controller);
            } else {
                $form->populate($form->getValues());
            }
        }
    }
    
    
    public function addAction() {

            $form = new Admin_Form_EditOrderStateForm();
            $form->startForm();

            $this->view->form = $form;

            if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
                $orderState = new Admin_Model_OrderState();
                $orderState->setOptions($form->getValues());

                try {
                    $this->_orderStateMapper->save($orderState);
                    $this->_flashMessenger->addMessage(array('info' => 'Stav objednávky byl úspěšně vložen'));
                } catch (Exception $e) {
                    $this->_flashMessenger->addMessage(array('error' => 'Při ukládání stavu objednávky nastala chyba!<br />' . $e->getMessage()));
                }

                $module = $this->getRequest()->getModuleName();
                $controller = $this->getRequest()->getControllerName();
                $this->_redirect($module . '/' . $controller);
            } else {
                $form->populate($form->getValues());
            }
    }
    
    

    public function deleteAction() {
        $id = $this->getRequest()->getParam('id');

        try {
            $this->_orderStateMapper->delete($id);
            $this->_flashMessenger->addMessage(array('info' => 'Stav objednávky byl úspěšně smazána.'));
        } catch (Exception $e) {
            $this->_flashMessenger->addMessage(array('error' => 'Při mazíní stavu objednávky nastala chyba!<br />' . $e->getMessage()));
        }

        $module = $this->getRequest()->getModuleName();
        $controller = $this->getRequest()->getControllerName();
        $this->_redirect($module . '/' . $controller);
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
//            $product->setProduct_default_url($url);
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

