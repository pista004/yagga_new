<?php

class Admin_ErrorController extends Zend_Controller_Action
{
    public function errorAction()
    {
        $this->getResponse()->setRawHeader('HTTP/1.1 404 Not Found');
    }

}
