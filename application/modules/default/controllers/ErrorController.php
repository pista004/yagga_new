<?php

class ErrorController extends Zend_Controller_Action
{
    public function errorAction()
    {
        
        $this->view->headTitle = "Omlouváme se, požadovaná stránka neexistuje";
        
        $this->getResponse()->setRawHeader('HTTP/1.1 404 Not Found');
    }

}
