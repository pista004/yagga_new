<?php

class Router_PageUrl extends Zend_Controller_Router_Route {

    public function match($path) {

//        echo $path;die;
        
        if ($path == "/" || !$path || strpos($path, '/admin') !== false)
            return false;

        $detail_url = substr($path, 1);

        $pageMapper = new Default_Model_PageMapper();

        $pageByUrl = $pageMapper->getPageByUrl($detail_url);

        
        if (empty($pageByUrl)) {
            return false;
        }

        $this->_defaults["pageurl"] = current($pageByUrl)->getPage_url();
        $this->_defaults["action"] = "index";
        $this->_defaults["controller"] = "page";
        $this->_defaults["module"] = "default";


        return $this->_defaults;
    }

}