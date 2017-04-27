<?php

class PageController extends Zend_Controller_Action {

    private $_pageMapper;

    public function init() {
        $this->_pageMapper = new Default_Model_PageMapper();
    }

    public function indexAction() {
        $pageUrl = $this->getRequest()->getParam('pageurl');
        $page = $this->_pageMapper->getPageByUrl($pageUrl);

//        print_r($page);die;
        
        if (!empty($page)) {

            $pageDetail = current($page);

            $this->view->headTitle = $pageDetail->getPage_seo_title() ? $pageDetail->getPage_seo_title() : $pageDetail->getPage_name();
            $this->view->metaDescription = $pageDetail->getPage_seo_meta_description();
            $this->view->page = $pageDetail;
            
        }
    }


}
