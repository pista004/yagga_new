<?php

class Router_CategoryUrl extends Zend_Controller_Router_Route {

    //ziskam z url path kategorie a overim zda existuje
    public function match($path) {
        
        if ($path == "/" || !$path || strpos($path, '/admin') !== false)
            return false;

        $categoryPath = substr($path, 1);

        if(substr($path, -1) == "/"){
            $categoryPath = substr($categoryPath, 0, -1);
        }

        $categoryMapper = new Default_Model_CategoryMapper();
        $categoriesUrlsAry = $categoryMapper->getCategoriesUrlsAry();

        if(!in_array($categoryPath, $categoriesUrlsAry)){
            return false;
        }

        $this->_defaults["categoryurl"] = $categoryPath;
        $this->_defaults["action"] = "list";
        $this->_defaults["controller"] = "product";
        $this->_defaults["module"] = "default";

        return $this->_defaults;
    }

}