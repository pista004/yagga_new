<?php

class My_XmlFactory_XmlGoogle {

    public function getXml($data) {

        $yagga_url = "http://www.yagga.cz";

        $categoryMapper = new Default_Model_CategoryMapper();
        $categories = $categoryMapper->getCategories();

        $heurekaCategoriesAry = array();
        $heurekaCategories = new My_HeurekaCategories();
        $heurekaCategoriesAry = $heurekaCategories->getCategories();




        $output = "";
        if (!empty($data)) {

            $xml = new DOMDocument('1.0', 'utf-8');
            $rss = $xml->createElement("rss");

            $rss_node = $xml->appendChild($rss); //add RSS element to XML node
            $rss_node->setAttribute("version", "2.0"); //set RSS version
            $rss_node->setAttribute("xmlns:g", "http://base.google.com/ns/1.0");
            $xmlShop = $xml->createElement('channel');
            foreach ($data as $product) {


                $variants = $product->getVariants();
                if (!empty($variants)) {
                    foreach ($variants as $variant) {
                        $xmlShopitem = $xml->createElement('item');

                        $google_item_id = $product->getProduct_id() . "_" . $variant->getVariant_id();
                        $xmlShopitem->appendChild($xml->createElement('g:id', $google_item_id));

                        $xmlShopitem->appendChild($xml->createElement('g:title', $product->getProduct_name()));

                        $description = $xmlShopitem->appendChild($xml->createElement('g:description'));
                        $description->appendChild($xml->createCDATASection($product->getProduct_perex()));

//TODO: google_product_category
//TODO: product_type
                        $variantUrl = $yagga_url . "/" . $product->getProduct_url() . "?variant=" . $variant->getVariant_id();
                        $xmlShopitem->appendChild($xml->createElement('g:link', $variantUrl));


                        $price = $variant->getVariant_price() . " CZK";
                        $xmlShopitem->appendChild($xml->createElement('g:price', $price));



                        if ($variant->getVariant_stock() > 0) {
                            $xmlShopitem->appendChild($xml->createElement('g:availability', 'in stock'));
                        } else {
                            $xmlShopitem->appendChild($xml->createElement('g:availability', 'out of stock'));
                        }


                        $imgurl = $yagga_url . "/images/upload/product_" . $product->getProduct_id() . "/original/" . $product->getMain_photography()->getPhotography_path();
                        $xmlShopitem->appendChild($xml->createElement('g:image_link', $imgurl));


                        $imgurl_alternative = "";
                        $photographies = $product->getPhotographies();
                        if (!empty($photographies)) {
                            foreach ($product->getPhotographies() as $photography) {
                                $imgurl_alternative = $yagga_url . "/images/upload/product_" . $product->getProduct_id() . "/original/" . $photography->getPhotography_path();
                                $xmlShopitem->appendChild($xml->createElement('g:additional_image_link', $imgurl_alternative));
                            }
                        }

                        $xmlShopitem->appendChild($xml->createElement('g:condition', 'new'));

                        $xmlShopitem->appendChild($xml->createElement('g:identifier_exists', 'FALSE'));

                        $xmlShopitem->appendChild($xml->createElement('item_group_id', $product->getProduct_id()));

                        $xmlShopitem->appendChild($xml->createElement('g:size', $variant->getVariant_name()));


                        $xmlShop->appendChild($xmlShopitem);
                    }
                } else {

                    $xmlShopitem = $xml->createElement('item');

                    $xmlShopitem->appendChild($xml->createElement('g:id', $product->getProduct_id()));

                    $xmlShopitem->appendChild($xml->createElement('g:title', $product->getProduct_name()));


                    $description = $xmlShopitem->appendChild($xml->createElement('g:description'));
                    $description->appendChild($xml->createCDATASection($product->getProduct_perex()));


//TODO: google_product_category
//TODO: product_type

                    $productUrl = $yagga_url . "/" . $product->getProduct_url();
                    $xmlShopitem->appendChild($xml->createElement('g:link', $productUrl));

                    $price = $product->getProduct_price() . " CZK";
                    $xmlShopitem->appendChild($xml->createElement('g:price', $price));


                    if ($product->getProduct_stock() > 0) {
                        $xmlShopitem->appendChild($xml->createElement('g:availability', 'in stock'));
                    } else {
                        $xmlShopitem->appendChild($xml->createElement('g:availability', 'out of stock'));
                    }

                    $imgurl = $yagga_url . "/images/upload/product_" . $product->getProduct_id() . "/original/" . $product->getMain_photography()->getPhotography_path();
                    $xmlShopitem->appendChild($xml->createElement('g:image_link', $imgurl));


                    $imgurl_alternative = "";
                    $photographies = $product->getPhotographies();
                    if (!empty($photographies)) {
                        foreach ($product->getPhotographies() as $photography) {
                            $imgurl_alternative = $yagga_url . "/images/upload/product_" . $product->getProduct_id() . "/original/" . $photography->getPhotography_path();
                            $xmlShopitem->appendChild($xml->createElement('g:additional_image_link', $imgurl_alternative));
                        }
                    }

                    $xmlShopitem->appendChild($xml->createElement('g:condition', 'new'));

                    $xmlShopitem->appendChild($xml->createElement('g:identifier_exists', 'FALSE'));

                    $xmlShopitem->appendChild($xml->createElement('item_group_id', $product->getProduct_id()));


                    $xmlShop->appendChild($xmlShopitem);
                }
            }

            $xml->appendChild($xmlShop);
            $rss->appendChild($xmlShop);
            $output = $xml->saveXML();
        }
        return $output;
    }

}