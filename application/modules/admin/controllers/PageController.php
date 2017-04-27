<?php

class Admin_PageController extends Zend_Controller_Action {

    private $_flashMessenger;
    private $_pageMapper;

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

        $this->_pageMapper = new Admin_Model_PageMapper();
    }

    public function indexAction() {

        $pages = $this->_pageMapper->getPages();
//        print_r($pages);die;
        $this->view->pages = $pages;
    }

    public function addAction() {
        $form = new Admin_Form_EditPageForm();
        $form->startForm();
        $this->view->form = $form;

        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
            $page = new Admin_Model_Page();
            $page->setOptions($form->getValues());

            //pokud existuj page url - vlozim url jinak delam url z nazvu
            if ($page->getPage_url()) {
                $toUrl = $page->getPage_url();
                $filterUrl = new Filter_Url();
                $url = $filterUrl->filter($toUrl);
                $page->setPage_url($url);
            } else {
                $toUrl = $page->getPage_name();
                $filterUrl = new Filter_Url();
                $url = $filterUrl->filter($toUrl);
                $page->setPage_url($url);
            }

            try {
                $this->_pageMapper->save($page);
                $this->_flashMessenger->addMessage(array('info' => 'Stránka byla úspěšně vložena.'));
            } catch (Exception $e) {
                $this->_flashMessenger->addMessage(array('error' => 'Při ukládání stránky nastala chyba!<br />' . $e->getMessage()));
            }

            $module = $this->getRequest()->getModuleName();
            $controller = $this->getRequest()->getControllerName();
            $this->_redirect($module . '/' . $controller);
        } else {
            $form->populate($form->getValues());
        }
    }

    public function editAction() {
        $pageId = $this->getRequest()->getParam('id');
        $pageMap = $this->_pageMapper->getPageById($pageId);

        if (!empty($pageMap)) {

            //formular pro produkt + naplnim daty
            $form = new Admin_Form_EditPageForm();
            $form->setUrl($pageMap->getPage_url());
            $form->startForm();
            $form->populate($pageMap->toArray());
            $this->view->form = $form;

            if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {

                $page = new Admin_Model_Page();
                $page->setOptions($form->getValues());
                $page->setPage_id($pageId);

// u editace kontroluju jestli byl zmenen nazev, potom menim i url, jinak url nemenim  
                if ((($pageMap->getPage_name() != $page->getPage_name()) && ($page->getPage_url() == "")) || $page->getPage_url() == "") {
                    $toUrl = $page->getPage_name();
                    $filterUrl = new Filter_Url();
                    $url = $filterUrl->filter($toUrl);
                    $page->setPage_url($url);
                } else {
                    $page->setPage_url($page->getPage_url());
                }

                try {
                    $this->_pageMapper->save($page);

                    $this->_flashMessenger->addMessage(array('info' => 'Stránka byla úspěšně upravena.'));
                } catch (Exception $e) {
                    $this->_flashMessenger->addMessage(array('error' => 'Při ukládání stránky nastala chyba!<br />' . $e->getMessage()));
                }

                $module = $this->getRequest()->getModuleName();
                $controller = $this->getRequest()->getControllerName();
                $this->_redirect($module . '/' . $controller);
            } else {
                $form->populate($form->getValues());
            }
        }
    }

    public function deleteAction() {
         $id = (int) $this->getRequest()->getParam('id');

        try {
            $this->_pageMapper->delete($id);
            $this->_flashMessenger->addMessage(array('info' => 'Stránka byla úspěšně smazána.'));
        } catch (Exception $e) {
            $this->_flashMessenger->addMessage(array('error' => 'Při pokusu o smazání došlo k chybě!<br />' . $e->getMessage()));
        }
        $module = $this->getRequest()->getModuleName();
        $controller = $this->getRequest()->getControllerName();
        $this->_redirect($module . '/' . $controller);
    }

}

