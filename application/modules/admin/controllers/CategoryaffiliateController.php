<?php

class Admin_CategoryaffiliateController extends Zend_Controller_Action {

    private $_flashMessenger;
    private $_categoryAffiliateCategoryMapper;
    private $_categoryMapper;

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

        $this->_categoryAffiliateCategoryMapper = new Admin_Model_CategoryAffiliateCategoryMapper();
        $this->_categoryMapper = new Admin_Model_CategoryMapper();
    }

    public function indexAction() {

        $categoryAffiliateResult = $this->_categoryAffiliateCategoryMapper->getCategories(0, -1, array('order' => 'cac.category_affiliate_category_id DESC'));
//        $categoryResult = $this->_categoryMapper->getCategories();

        
        $childs = $this->_categoryMapper->getChildsCache(0, true);
        $categoryChildsAry = $this->_categoryMapper->getCategoryChildsAryCache($childs);
//        print_r($categoryChildsAry);die;


        foreach ($categoryAffiliateResult as $categoryAffiliate) {
            $categoryId = $categoryAffiliate->getCategory_affiliate_category_category_id();
            if (isset($categoryId)) {
                $categoryAffiliate->setCategory($categoryChildsAry[$categoryId]);
            }
        }
        
        $this->view->categoryAffiliateResult = $categoryAffiliateResult;
    }

    public function editAction() {

        $id = $this->getRequest()->getParam('id');
        $categoryAffiliateById = $this->_categoryAffiliateCategoryMapper->getCategoryAffiliateById($id);

//        print_r($manufacturerAffiliateById);die;

        if ($categoryAffiliateById) {

            $childs = $this->_categoryMapper->getChildsCache(0, true);
        $categoryChildsAry = $this->_categoryMapper->getCategoryChildsAryCache($childs);

            $categoryToForm = array(0 => 'Nespárováno');
            foreach ($categoryChildsAry as $categoryId => $category) {
                $categoryToForm[$categoryId] = $category->getCategory_structure();
            }

            $form = new Admin_Form_EditCategoryAffiliateForm();
            $form->setCategories($categoryToForm);
            $form->startForm();
            if ($categoryAffiliateById->getCategory_affiliate_category_category_id()) {
                $form->populate(array('category_id' => $categoryAffiliateById->getCategory_affiliate_category_category_id()));
            }
            $this->view->form = $form;

            $this->view->categoryAffiliateById = $categoryAffiliateById;


            if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
                $categoryAffiliate = new Admin_Model_CategoryAffiliateCategory();
                $categoryAffiliate->setCategory_affiliate_category_id($id);

                if ($form->getValue('category_id') == 0) {
                    $categoryAffiliate->setCategory_affiliate_category_category_id(new Zend_Db_Expr('NULL'));
                } else {
                    $categoryAffiliate->setCategory_affiliate_category_category_id($form->getValue('category_id'));
                }


                $db = $this->_categoryAffiliateCategoryMapper->getDbTable()->getDefaultAdapter();
                $db->beginTransaction();
                try {
                    $this->_categoryAffiliateCategoryMapper->save($categoryAffiliate);


                    $db->commit();

                    $this->_flashMessenger->addMessage(array('info' => 'Kategorie byla úspěšně spárována.'));
                } catch (Exception $e) {

                    $this->_flashMessenger->addMessage(array('error' => 'Při ukládání nastala chyba!<br />' . $e->getMessage()));
                    $db->rollBack();
                }

                $module = $this->getRequest()->getModuleName();
                $controller = $this->getRequest()->getControllerName();
                $this->_redirect($module . '/' . $controller);
            } else {
                $form->populate($form->getValues());
            }
        }
    }

}

