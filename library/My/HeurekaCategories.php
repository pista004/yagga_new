<?php

class My_HeurekaCategories {

    //huereka - ziskam kategoie
    public function getCategories() {

        $url = "http://www.heureka.cz/direct/xml-export/shops/heureka-sekce.xml";

        $frontendOptions = array(
            'lifetime' => 3600 * 24 * 3, // cache lifetime 3 dny - 3600 = hodina, 24 = hodin/den, 3 = dny
            'automatic_serialization' => true,
            'automatic_cleaning_factor' => 50
        );
        $backendOptions = array(
            'cache_dir' => '../cache' // Directory where to put the cache files
        );

// getting a Zend_Cache_Core object
        $zend_cache = Zend_Cache::factory('Core', 'File', $frontendOptions, $backendOptions);

        $heurekaCategories = array();

//k nacitani informaci o heureka kategoriich pouzivam cache - nepotrebuju nacitat porad
        if (!$heurekaCategories = $zend_cache->load('HeurekaCategories')) {

//nactu xml feed
            if (($response_xml_data = @file_get_contents($url)) === false) {
                return array();
            } else {
                $data = simplexml_load_string($response_xml_data);
                if (!$data) {
                    return array();
                } else {
                    $heurekaCategories = $this->getCategoriesFromXmlToArray($data);
                }
            }

            $zend_cache->save($heurekaCategories, 'HeurekaCategories');
        } else {
            $heurekaCategories = $zend_cache->load('HeurekaCategories');
        }

        return $heurekaCategories;
    }

    /*
     * rekurzivni funkce, prochazi data(kategorie) z xml feedu a prevadi do pole
     */
    private function getCategoriesFromXmlToArray($xmlData, $ary = array()) {
        foreach ($xmlData as $d) {

            $categoryName = (string) $d->CATEGORY_FULLNAME ? (string) $d->CATEGORY_FULLNAME : (string) $d->CATEGORY_NAME;

            $ary[(int) $d->CATEGORY_ID] = $categoryName;

            if (!empty($d->CATEGORY)) {
                $ary = $this->getCategoriesFromXmlToArray($d->CATEGORY, $ary);
            }
        }
        return $ary;
    }

}