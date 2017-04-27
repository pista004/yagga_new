<?php

/*
 * speciální akce, Pošli pusinku a pošli pussynku
 */

class Admin_KisssenderController extends Zend_Controller_Action {

    private $_kisssenderMapper;

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


        $this->_kisssenderMapper = new Admin_Model_KisssenderMapper();
    }

    /*
     * hlavní stránka pošlipusinku
     */

    public function indexAction() {

        
        $page = 0;
        if ($this->getRequest()->getParam('page')) {
            $page = $this->getRequest()->getParam('page');
        }
        
        $kisssenders = $this->_kisssenderMapper->getKisssenders($page, 20);

        $this->view->kisssenders = $kisssenders;
        $this->view->paginator = $this->_kisssenderMapper->_paginator;
        
        


        



    }


}
