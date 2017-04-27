<?php

/*
 * 
 * TODO - upravit, spousti se i u jinych stranek, napr /ajax, pak nejde spustit ajax ze souboru functions.js
 * 
 */
class Router_ProductUrl extends Zend_Controller_Router_Route {

    public function match($path) {

        if ($path == "/" || !$path || strpos($path, '/admin') !== false)
            return false;

        $detail_url = substr($path, 1);

        $productMapper = new Default_Model_ProductMapper();

        $productByUrl = $productMapper->getProductByUrl($detail_url);
//        print_r($productByUrl);die;
        if (empty($productByUrl)) {
            return false;
        }

        $this->_defaults["detailurl"] = current($productByUrl)->getProduct_url();
        $this->_defaults["action"] = "detail";
        $this->_defaults["controller"] = "product";
        $this->_defaults["module"] = "default";

        return $this->_defaults;
    }

}