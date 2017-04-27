<?php

class Plugin_Authorization extends Zend_Controller_Plugin_Abstract {

    
    public function preDispatch(Zend_Controller_Request_Abstract $request) {

        if(!$this->_isRestrictedRequest($request)){
            return;
        }
        
        if(!Zend_Auth::getInstance()->hasIdentity()){
            $r = Zend_Controller_Action_HelperBroker::getStaticHelper('redirector');
            $r->gotoUrlAndExit("/admin/index/login");
        }
        
    }
    
    protected function _isRestrictedRequest($request){
        
        $controller = $request->getControllerName();
        $action = $request->getActionName();
        $module = $request->getModuleName();
        
        //zamezim pristupu neprihlasenemu uzivateli do controlleru message
        if($module == "admin" && !in_array($controller, array('index', 'affiliate'))){
            return true;
        }else{
            return false;
        }
        
    }

}
