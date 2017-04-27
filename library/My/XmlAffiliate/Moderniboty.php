<?php

class My_XmlAffiliate_Moderniboty {

    CONST PROGRAMNAME = 'modernibotycz';

    private $file = 'http://www.moderni-boty.cz/api/feed/5647-jedinakovavo1x5tS-full.xml';
    private $localFile = 'xml_feeds/modernibotycz.xml';
    private $username;
    private $password;

    public function getProgramName() {
        return self::PROGRAMNAME;
    }

    /*
     * Ziskam data z externiho zdroje - muze byt xml, csv, atd.
     */

    private function getSourceData() {

        $client = new Zend_Http_Client($this->file);
//        $client->setAuth($this->username, $this->password);
        $response = $client->request();

        $returnXml = false;

        if ($response->isSuccessful() && ($xml = $response->getBody())) {

//            $uncompressed = gzinflate(substr($xml, 10, -8));
            if (!file_put_contents($this->localFile, $xml)) {
                throw new Zend_Exception('Při ukládání XML feedu ' . $this->file . ' nastala CHYBA!');
            }

            $returnXml = true;
        } else {
            throw new Zend_Exception('Při načtení XML feedu ' . $this->file . ' nastala CHYBA!');
        }


        return $returnXml;
    }

    public function getData() {

        $isXmlData = $this->getSourceData();
//die;
        $products = array();
        $categories = array();


        if ($isXmlData) {
            $xml_reader = new XMLReader;
            $xml_reader->open($this->localFile);

            while ($xml_reader->read()) {
// move the pointer to the first product
//            while ($xml_reader->read() && $xml_reader->name != 'category' && $xml_reader->name != 'product');

                if ($xml_reader->nodeType == XMLReader::ELEMENT && $xml_reader->name == 'category') {
                    $xmlItemCategory = simplexml_load_string($xml_reader->readOuterXML());
//                    print_r($xmlItemCategory);die;
                    $categories[(int) $xmlItemCategory->id] = $xmlItemCategory;

//                    $xml_reader->next('category');
                }
            }
            $xml_reader->close();

            //seradim kategorie podle id
            ksort($categories);

            $categoriesAry = array();
            $categoriesAry = $this->getCategories($categories);
//            print_r($categoriesAry);die;
// loop through the products
            //TODO - nedostane se do while product

            $xml_reader2 = new XMLReader;
            $xml_reader2->open($this->localFile);
            while ($xml_reader2->read()) {
                if ($xml_reader2->nodeType == XMLReader::ELEMENT && $xml_reader2->name == 'product') {

                    // load the current xml element into simplexml and we’re off and running!
                    $xmlItem = simplexml_load_string($xml_reader2->readOuterXML());
                    $product = array();

                    $product['name'] = (string) trim($xmlItem->name);

                    $product['description'] = "";
                    $product['description'] = (string) trim($xmlItem->description);

                    if ((string) trim($xmlItem->description_short)) {
                        $product['description'] .= "<br />" . (string) trim($xmlItem->description_short);
                    }


//                    
//                    echo (string) trim($xmlItem->description);die;
//                    $product['description'] = (string) trim($xmlItem->description);
//                    
//echo $product['description'];die;
                    $product['code'] = (string) trim($xmlItem->reference);
//                $product['ean'] = (string) trim($xmlItem->upc);
//print_r($product);die;
                    $product['ean'] = new Zend_Db_Expr('NULL');

                    $product['manufacturer'] = (string) trim($xmlItem->producer);

                    $product['price'] = (int) trim($xmlItem->pricewithdph);
                    $product['recommended_price'] = 0;
                    if ((int) trim($xmlItem->oldprice) > (int) trim($xmlItem->pricewithdph)) {
                        $product['recommended_price'] = (int) trim($xmlItem->oldprice);
                    }


                    $product['affiliate_url'] = "";

                    if ($xmlItem->images->generalimage != null) {
                        $product['photography_path'] = (string) trim($xmlItem->images->generalimage->attributes()->link);
                    }
//die;
                    foreach ($xmlItem->images->otherimage as $otherImage) {
                        $product['other_photographies'][] = (string) trim($otherImage->attributes()->link);
                    }

                    $product['category'] = $categoriesAry[(int) trim($xmlItem->categorys->categoryid)];
//
                    foreach ($xmlItem->velikost as $variant) {
                        $productStock = 0;

                        foreach ($variant as $variantItem) {
                            $velikostId = (string) $variantItem['id'];
                            $product['variant'][$velikostId]['name'] = (string) $variantItem;

                            //ziskam hodnotu skladu pro danou velikost
                            $xpathQuery = "//stock/value[@id_size='" . $velikostId . "']/text()";
                            $stock = 0;
                            $stock = $xmlItem->xpath($xpathQuery);
                            $stock = (int) $stock[0];

                            $product['variant'][$velikostId]['stock'] = $stock;

                            $productStock += $stock;
                        }
                    }

                    $product['stock'] = $productStock;

                    $products[$product['code']] = $product;

                    // move the pointer to the next product
//                    $xml_reader->next('product');
                }
            }
// don’t forget to close the file
            $xml_reader2->close();
        }

        return $products;
    }

    //vezmu kategorie z xml a utvorim z nich "breadcrumbs", strom kategorii, tak at muzu priradit kategorii k produktu, napr. damske boty | tenisky
    private function getCategories($categories) {

        $result = array();

        foreach ($categories as $category) {

            if ((int) $category->parentid == 0) {
                $result[(int) $category->id] = (string) $category->name;
            } else {
                if (isset($result[(int) $category->parentid])) {
                    $result[(int) $category->id] = $result[(int) $category->parentid] . " | " . (string) $category->name;
                }
            }
        }

        return $result;
    }

}