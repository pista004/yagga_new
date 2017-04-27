<?php

class XmlController extends Zend_Controller_Action {

    /*
     * 
     * xml export
     * 
     */

    public function feedAction() {
//ini_set('memory_limit', '256M');
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);

        $target = $this->getRequest()->getParam('target');

        if ($target) {
            
            $xml = new My_XmlFactory_XmlFactoryCreator();

            $outputXml = $xml->createFeed($target);
            
            if ($outputXml != "") {
                $this->getResponse()->setHeader('Content-Type', 'text/xml; charset=utf-8')->setBody((string) $outputXml);
            }
        }

    }

}
