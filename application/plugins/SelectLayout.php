<?php

/* Plugin pro automaticke zvoleni layoutu podle modulu */

class Plugin_SelectLayout extends Zend_Controller_Plugin_Abstract {

    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        
        //presmeruju na stranku rekonstrukce
//        if($request->getControllerName() != 'rekonstrukce' && $request->getModuleName() != 'admin'){
//             $redirector = new Zend_Controller_Action_Helper_Redirector;
//             $redirector->gotoUrl("http://www.yagga.cz/rekonstrukce");
//        }
        
        
        $layout = Zend_Layout::getMvcInstance();
        $filename = $layout->getLayoutPath() . '/' . $request->getModuleName() . '.' . $layout->getViewSuffix();

        //check if the layout template exists, if not use the default layout set in application.ini
        if (file_exists($filename))
        {
            $layout->setLayout($request->getModuleName());
        }
    }

}