<?php

class Admin_LogController extends Zend_Controller_Action {

    private $_logMapper;
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

        $this->_logMapper = new Admin_Model_LogMapper();
    }

    public function indexAction() {
        $page = 0;
        if ($this->getRequest()->getParam('page')) {
            $page = $this->getRequest()->getParam('page');
        }
        $logResult = $this->_logMapper->getLogs($page, 20);
        $this->view->logResult = $logResult;
        $this->view->paginator = $this->_logMapper->_paginator;
    }


}

