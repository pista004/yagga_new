<?php

class My_XmlFactory_XmlHeureka {

    private $_productMapper;
    private $_variantMapper;
    private $_photographyMapper;

    public function getXml() {

        $this->_productMapper = new Default_Model_ProductMapper();
        $this->_variantMapper = new Default_Model_VariantMapper();
        $this->_photographyMapper = new Default_Model_PhotographyMapper();

//        print_r($data);
//        die;

        $yagga_url = "http://www.yagga.cz";





        $data = array();
        $variants = array();
        $variants = $this->_variantMapper->getVariantsToFeed();
        $data = $variants;

        $categoryMapper = new Default_Model_CategoryMapper();
        $categories = $categoryMapper->getCategories();

        $heurekaCategoriesAry = array();
        $heurekaCategories = new My_HeurekaCategories();
        $heurekaCategoriesAry = $heurekaCategories->getCategories();


        $output = "";
        if (!empty($data)) {

            $xml = new DOMDocument('1.0', 'utf-8');
//            $xml = new DOMDocument();

            $xmlShop = $xml->createElement('SHOP');
            foreach ($data as $product) {

                $itemId = $product['product_id'];
                $itemGroupId = $product['product_id'];
                if ($product['variant_id']) {
                    $itemId = $product['variant_id'];
                }

                $xmlShopitem = $xml->createElement('SHOPITEM');
                $xmlShopitem->appendChild($xml->createElement('ITEM_ID', $itemId));



                $heureka_productname = $product['product_name'];

                if ($product['manufacturer_name'] != "") {
                    $heureka_productname = $product['manufacturer_name'] . " " . $heureka_productname;
                }

                if ($product['product_code']) {
                    $heureka_productname .= " " . $product['product_code'];
                }

                if ($product['variant_name'] != "") {
                    $heureka_productname .= " " . $product['variant_name'];
                }

                $heureka_product = $heureka_productname;


                $productNameElement = $xmlShopitem->appendChild($xml->createElement('PRODUCTNAME'));
                $productNameElement->appendChild($xml->createCDATASection($heureka_productname));


                $productElement = $xmlShopitem->appendChild($xml->createElement('PRODUCT'));
                $productElement->appendChild($xml->createCDATASection($heureka_product));


                $manufacturerElement = $xmlShopitem->appendChild($xml->createElement('MANUFACTURER'));
                $manufacturerElement->appendChild($xml->createCDATASection($product['manufacturer_name']));

                $heureka_url = $yagga_url . "/" . $product['product_url'];
                if ($product['variant_id']) {
                    $heureka_url .= "?variant=" . $product['variant_id'];
                }
                $xmlShopitem->appendChild($xml->createElement('URL', $heureka_url));


                $heureka_imgurl = $yagga_url . "/images/upload/product_" . $product['product_id'] . "/original/" . $product['photography_path'];
                $xmlShopitem->appendChild($xml->createElement('IMGURL', $heureka_imgurl));

                if (array_key_exists('photographies', $product)) {

                    $photographies = $product['photographies'];
                    if (!empty($photographies)) {
                        foreach ($photographies as $photography) {
                            $heureka_imgurl_alternative = $yagga_url . "/images/upload/product_" . $product['product_id'] . "/original/" . $photography['photography_path'];
                            $xmlShopitem->appendChild($xml->createElement('IMGURL_ALTERNATIVE', $heureka_imgurl_alternative));
                        }
                    }
                }

                $xmlShopitem->appendChild($xml->createElement('PRICE_VAT', $product['product_price']));

                if ($product['product_category_heureka']) {
                    if (array_key_exists($product['product_category_heureka'], $heurekaCategoriesAry)) {
                        $xmlShopitem->appendChild($xml->createElement('CATEGORYTEXT', $heurekaCategoriesAry[$product['product_category_heureka']]));
                    }
                } else if ($product['category_category_heureka']) {
                    if (array_key_exists($product['category_category_heureka'], $heurekaCategoriesAry)) {
                        $xmlShopitem->appendChild($xml->createElement('CATEGORYTEXT', $heurekaCategoriesAry[$product['category_category_heureka']]));
                    }
                } else if (array_key_exists($product['category_id'], $categories)) {
                    $xmlShopitem->appendChild($xml->createElement('CATEGORYTEXT', $categories[$product['category_id']]->getCategory_name()));
                } else {
                    $xmlShopitem->appendChild($xml->createElement('CATEGORYTEXT', ""));
                }


                $productNoElement = $xmlShopitem->appendChild($xml->createElement('PRODUCTNO'));
                $productNoElement->appendChild($xml->createCDATASection($product['product_code']));


                $stock = $product['product_stock'];
                if ($product['variant_id']) {
                    $stock = $product['variant_stock'];
                }


                if ($stock > 0) {
                    $xmlShopitem->appendChild($xml->createElement('DELIVERY_DATE', 0));
                }

                $paramElement = $xml->createElement('PARAM');
                $paramElement->appendChild($xml->createElement('PARAM_NAME', 'velikost'));
                $paramElement->appendChild($xml->createElement('VAL', $product['variant_name']));
                $xmlShopitem->appendChild($paramElement);


                $xmlShopitem->appendChild($xml->createElement('ITEMGROUP_ID', $itemGroupId));

                $xmlShop->appendChild($xmlShopitem);
            }
            $xml->appendChild($xmlShop);

            $output = $xml->saveXML();
        }
        return $output;
    }

}