<?php

class Admin_CategoryController extends Zend_Controller_Action {

    private $_categoryMapper;
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

        $this->_categoryMapper = new Admin_Model_CategoryMapper();
    }

    public function indexAction() {

        $childs = $this->_categoryMapper->getChildsCache(0, true);

        $categoryChildsAry = $this->_categoryMapper->getCategoryChildsAryCache($childs);

        $this->view->categories = $categoryChildsAry;
    }

    public function editAction() {

        $childs = $this->_categoryMapper->getChildsCache(0, true);
        $categpruChildsAry = $this->_categoryMapper->getCategoryChildsAryCache($childs);

        //ziskam vsechny kategorie a potom id a strukturu pouziju do formulare        
        $categoriesStructureParents = array(0 => 'Hlavní');
        foreach ($categpruChildsAry as $category) {
            $categoriesStructureParents[$category->getCategory_id()] = $category->getCategory_structure();
        }

        $category_id = $this->getRequest()->getParam('id');
        $categoryMap = $this->_categoryMapper->getCategoryById($category_id);

        $heurekaCategoriesAry = array();
        $heurekaCategories = new My_HeurekaCategories();
        $heurekaCategoriesAry[0] = '--Vyberte--';
        $heurekaCategoriesAry += $heurekaCategories->getCategories();

        $form = new Admin_Form_EditCategoryForm();
        $form->setCategory($categoriesStructureParents);
        $form->setUrl($categoryMap->getCategory_url());
        $form->setHeurekaCategory($heurekaCategoriesAry);
        $form->startForm();
        $form->populate($categoryMap->toArray());
        $this->view->form = $form;

        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
            $category = new Admin_Model_Category();
            $category->setOptions($form->getValues());
            $category->setCategory_id($category_id);

            // u editace kontroluju jestli byl zmenen nazev, potom menim i url, jinak url nemenim  
            if (($categoryMap->getCategory_name() != $category->getCategory_name())) {
                $toUrl = $category->getCategory_name();
                $filterUrl = new Filter_Url();
                $url = $filterUrl->filter($toUrl);
                $category->setCategory_url($url);
            } else if (($categoryMap->getCategory_url() != $category->getCategory_url()) && ($category->getCategory_url() != "")) {
                $toUrl = $category->getCategory_url();
                $filterUrl = new Filter_Url();
                $url = $filterUrl->filter($toUrl);
                $category->setCategory_url($url);
            } else {
                $category->setCategory_url($categoryMap->getCategory_url());
            }


            try {
                $this->_categoryMapper->save($category);

                //obnovim cache, tak aby byla nove upravena kategorie viditelna
                $zend_cache = Zend_Registry::get('AdminCache24h');
                $zend_cache->remove('AdmCategoryChilds');
                $zend_cache->remove('AdmCategoryChildsAry');

                $this->_flashMessenger->addMessage(array('info' => 'Kategorie byla úspěšně upravena.'));
            } catch (Exception $e) {
                $this->_flashMessenger->addMessage(array('error' => 'Při ukládání kategorie nastala chyba!<br />' . $e->getMessage()));
            }

            $module = $this->getRequest()->getModuleName();
            $controller = $this->getRequest()->getControllerName();
            $this->_redirect($module . '/' . $controller);
        } else {
            $form->populate($form->getValues());
        }
    }

    public function addAction() {

//ziskam vsechny kategorie a potom id a strukturu pouziju do formulare        
        $childs = $this->_categoryMapper->getChildsCache(0, true);
        $categpruChildsAry = $this->_categoryMapper->getCategoryChildsAryCache($childs);

        //ziskam vsechny kategorie a potom id a strukturu pouziju do formulare        
        $categoriesStructureParents = array(0 => 'Hlavní');
        foreach ($categpruChildsAry as $category) {
            $categoriesStructureParents[$category->getCategory_id()] = $category->getCategory_structure();
        }

        $heurekaCategoriesAry = array();
        $heurekaCategories = new My_HeurekaCategories();
        $heurekaCategoriesAry[0] = '--Vyberte--';
        $heurekaCategoriesAry += $heurekaCategories->getCategories();

        $form = new Admin_Form_EditCategoryForm();
        $form->setCategory($categoriesStructureParents);
        $form->setHeurekaCategory($heurekaCategoriesAry);
        $form->startForm();
        $this->view->form = $form;

        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
            $category = new Admin_Model_Category();
            $category->setOptions($form->getValues());

            $toUrl = $category->getCategory_name();
            $filterUrl = new Filter_Url();
            $url = $filterUrl->filter($toUrl);

            $category->setCategory_url($url);

            $this->_categoryMapper->save($category);

            //obnovim cache, tak aby byla nove pridana kategorie viditelna
            $zend_cache = Zend_Registry::get('AdminCache24h');
            $zend_cache->remove('AdmCategoryChilds');
            $zend_cache->remove('AdmCategoryChildsAry');



            $this->_flashMessenger->addMessage(array('info' => 'Kategorie byla úspěšně vložena.'));

            $module = $this->getRequest()->getModuleName();
            $controller = $this->getRequest()->getControllerName();
            $this->_redirect($module . '/' . $controller);
        } else {
            $form->populate($form->getValues());
        }
    }

    public function deleteAction() {
        $id = $this->getRequest()->getParam('id');

        $childs = $this->_categoryMapper->getChilds($id);

//      podle id ziskam potomky kategorii  
        $childsIds = $this->_categoryMapper->getCategoryChildsIds($childs);

//      pridam id, pro ktere byli zjistovani potomci
        $childsIds[] = $id;

        try {
            $this->_categoryMapper->delete($childsIds);

            //obnovim cache, tak aby byla nove upravena kategorie viditelna
            $zend_cache = Zend_Registry::get('AdminCache24h');
            $zend_cache->remove('AdmCategoryChilds');
            $zend_cache->remove('AdmCategoryChildsAry');

            $this->_flashMessenger->addMessage(array('info' => 'Kategorie byla úspěšně smazána.'));
        } catch (Exception $e) {
            $this->_flashMessenger->addMessage(array('error' => 'Při mazání kategorie nastala chyba!<br />' . $e->getMessage()));
        }

        $module = $this->getRequest()->getModuleName();
        $controller = $this->getRequest()->getControllerName();
        $this->_redirect($module . '/' . $controller);
    }

}

