<?php

class My_XmlAffiliate_Perfektnipradlo {

    CONST PROGRAMNAME = 'perfektnipradlocz';

    private $file = 'https://www.dropshipping.cz/xml/client-eshop/product-list/1007/';
    private $localFile = 'xml_feeds/perfektnipradlocz.xml';
    private $username;
    private $password;

    public function getProgramName() {
        return self::PROGRAMNAME;
    }

    /*
     * Ziskam data z externiho zdroje - muze byt xml, csv, atd.
     */

    private function getSourceData() {

        $returnXml = false;

        $xml = file_get_contents($this->file); // your file is in the string "$xml" now.

        if (!file_put_contents($this->localFile, $xml)) {
            throw new Zend_Exception('Při ukládání XML feedu ' . $this->file . ' nastala CHYBA!');
        } else {
            $returnXml = true;
        }

        return $returnXml;
    }

    public function getData() {

        $isXmlData = $this->getSourceData();

        $products = array();


        if ($isXmlData) {
            $xml_reader = new XMLReader;
            $xml_reader->open($this->localFile);

            $xml_reader->read();
// move the pointer to the first product
            while ($xml_reader->read() && $xml_reader->name != 'SHOPITEM');

// loop through the products
            while ($xml_reader->name == 'SHOPITEM') {
                // load the current xml element into simplexml and we’re off and running!
                $xmlItem = simplexml_load_string($xml_reader->readOuterXML());
//print_r($xmlItem);die;
                // kontroluju, jestli existuje kategorie, pokud ne, tak produkt nepridavam

                $itemId = (string) $xmlItem->ITEM_ID;

                $itemGroupId = "";
                $itemGroupId = (string) $xmlItem->ITEMGROUP_ID;
                if ($itemGroupId != "") {
                    $itemId = $itemGroupId;
                }

                if (array_key_exists($itemId, $products)) {

                    $productname = (string) trim($xmlItem->PRODUCT);

// kontroluju, jestli v nazvu produktu existuje pomlcka, pokud ano, jedna se o variantu a potrebuju ji z nazvu dostat
                    if (strpos($productname, '-') !== false) {
                        $pieces = explode("-", $productname);

                        if (count($pieces) > 1) {
                            $products[$itemId]['variant'][] = trim(end($pieces));
                        }
                    }
                } else {

                    $product = array();

                    $productname = (string) trim($xmlItem->PRODUCT);
                    $product['name'] = $productname;

// v nazvu produktu je uvadena varianta, ziskam jen nazev
                    if (strpos($productname, '-') !== false) {
                        $pieces = explode("-", $productname);

                        if (count($pieces) > 1) {
                            $product['name'] = (string) trim(current($pieces));
                        }
                    }


                    $product['description'] = (string) trim($xmlItem->DESCRIPTION);

//feed obsahuje i html description, proto pokud existuje, vyuziju jej
                    if ((string) trim($xmlItem->DESCRIPTION_HTML) != "") {
                        $product['description'] = (string) trim($xmlItem->DESCRIPTION_HTML);
                    }



                    $product['code'] = (string) $itemId;
                    $product['ean'] = "";

                    $product['manufacturer'] = (string) trim($xmlItem->MANUFACTURER);

                    $product['price'] = (int) trim($xmlItem->PRICE_VAT);
                    $product['recommended_price'] = 0;

                    $product['affiliate_url'] = self::AFFILIATEURLPREFIX . "" . (string) trim($xmlItem->URL);

                    $product['photography_path'] = (string) trim($xmlItem->IMGURL);

                    $product['category'] = (string) trim($xmlItem->CATEGORYTEXT);

                    foreach ($xmlItem->PARAM as $param) {
                        if (strtolower((string) $param->PARAM_NAME) == 'velikost') {
                            if ((string) $param->VAL != "") {
                                $product['variant'][] = (string) $param->VAL;
                            }
                        }
                        if (strtolower((string) $param->PARAM_NAME) == 'určení') {
                            $params = explode(",", (string) $param->VAL);
                            if (array_key_exists(0, $params)) {
                                if ($params[0] != "" && $product['category'] != "") {
                                    $product['category'] = $product['category'] . " | " . (string) $params[0];
                                }
                            }
                        }
                    }

                    // urbanlux uvadi delivery date, ale vsude maji delivery date = 3, takze uvadnime skladem pro hodnotu 3 i 0
                    $inStock = (int) trim($xmlItem->DELIVERY_DATE);
                    if ($inStock == 3 || $inStock == 0) {
                        $product['stock'] = 1;
                    } else {
                        $product['stock'] = 0;
                    }

                    $products[$itemId] = $product;
                }
                // move the pointer to the next product
                $xml_reader->next('SHOPITEM');
            }


// don’t forget to close the file
            $xml_reader->close();
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