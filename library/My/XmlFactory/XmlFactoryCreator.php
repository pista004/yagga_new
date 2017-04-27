<?php

/*
 * 
 * navrhovy vzor tovarna, na zaklade parametru volam konkretni instanci - vytvarim xml feedy
 * nejcasteji je creator volan z controlleru, kde jsou predana data z sqk
 * 
 */

class My_XmlFactory_XmlFactoryCreator {

    public function createFeed($xmlType) {

        switch ($xmlType) {
            case "heureka":
                $xmlHeureka = new My_XmlFactory_XmlHeureka();
                return $xmlHeureka->getXml();
                break;
            case "google":
                $xmlGoogle = new My_XmlFactory_XmlGoogle();
                return $xmlGoogle->getXml();
                break;
            case "desktopapp":
                $xmlDesktopApp = new My_XmlFactory_XmlDesktopApp();
                return $xmlDesktopApp->getXml();
                break;
            case "facebook":
                $xmlFacebook = new My_XmlFactory_XmlFacebook();
                return $xmlFacebook->getXml();
                break;
            default:
                break;
        }
    }

}