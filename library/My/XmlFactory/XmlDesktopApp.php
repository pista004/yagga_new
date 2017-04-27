<?php

class My_XmlFactory_XmlDesktopApp {

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
            $xmlShop = $xml->createElement('SHOP');
            foreach ($data as $product) {


                $variants = $product->getVariants();
                if (!empty($variants)) {
                    foreach ($variants as $variant) {
                        $xmlShopitem = $xml->createElement('SHOPITEM');
                        $heureka_item_id = $product->getProduct_id() . "_" . $variant->getVariant_id();
                        $xmlShopitem->appendChild($xml->createElement('ITEM_ID', $heureka_item_id));

                        $heureka_productname = $product->getProduct_name() . " " . $variant->getVariant_name();
                        $heureka_product = $product->getProduct_name() . " " . $variant->getVariant_name();
                        $xmlShopitem->appendChild($xml->createElement('PRODUCTNAME', $heureka_productname));
                        $xmlShopitem->appendChild($xml->createElement('PRODUCT', $heureka_product));

                        $description = $xmlShopitem->appendChild($xml->createElement('DESCRIPTION'));
                        $description->appendChild($xml->createCDATASection($product->getProduct_description()));

                        $perex = $xmlShopitem->appendChild($xml->createElement('PEREX'));
                        $perex->appendChild($xml->createCDATASection($product->getProduct_perex()));

                        $heureka_url = $yagga_url . "/" . $product->getProduct_url() . "?variant=" . $variant->getVariant_id();
                        $xmlShopitem->appendChild($xml->createElement('URL', $heureka_url));


                        $heureka_imgurl = $yagga_url . "/images/upload/product_" . $product->getProduct_id() . "/original/" . $product->getMain_photography()->getPhotography_path();
                        $xmlShopitem->appendChild($xml->createElement('IMGURL', $heureka_imgurl));

                        $photographies = $product->getPhotographies();
                        if (!empty($photographies)) {
                            foreach ($product->getPhotographies() as $photography) {
                                $heureka_imgurl_alternative = $yagga_url . "/images/upload/product_" . $product->getProduct_id() . "/original/" . $photography->getPhotography_path();
                                $xmlShopitem->appendChild($xml->createElement('IMGURL_ALTERNATIVE', $heureka_imgurl_alternative));
                            }
                        }

                        $xmlShopitem->appendChild($xml->createElement('PRICE_VAT', $variant->getVariant_price()));


                        if (array_key_exists($product->getCategory()->getCategory_id(), $categories)) {
                            $xmlShopitem->appendChild($xml->createElement('CATEGORYTEXT', $categories[$product->getCategory()->getCategory_id()]->getCategory_structure()));
                        } else {
                            $xmlShopitem->appendChild($xml->createElement('CATEGORYTEXT', ""));
                        }

                        if ($variant->getVariant_stock() > 0) {
                            $xmlShopitem->appendChild($xml->createElement('DELIVERY_DATE', 0));
                        }

                        $xmlShopitem->appendChild($xml->createElement('ITEMGROUP_ID', $product->getProduct_id()));


                        $xmlShop->appendChild($xmlShopitem);
                    }
                } else {
                    $xmlShopitem = $xml->createElement('SHOPITEM');
                    $xmlShopitem->appendChild($xml->createElement('ITEM_ID', $product->getProduct_id()));
                    $xmlShopitem->appendChild($xml->createElement('PRODUCTNAME', $product->getProduct_name()));
                    $xmlShopitem->appendChild($xml->createElement('PRODUCT', $product->getProduct_name()));
                    $description = $xmlShopitem->appendChild($xml->createElement('DESCRIPTION'));
                    $description->appendChild($xml->createCDATASection($product->getProduct_description()));


                    $perex = $xmlShopitem->appendChild($xml->createElement('PEREX'));
                    $perex->appendChild($xml->createCDATASection($product->getProduct_perex()));

                    $heureka_url = $yagga_url . "/" . $product->getProduct_url();
                    $xmlShopitem->appendChild($xml->createElement('URL', $heureka_url));

                    $heureka_imgurl = $yagga_url . "/images/upload/product_" . $product->getProduct_id() . "/original/" . $product->getMain_photography()->getPhotography_path();
                    $xmlShopitem->appendChild($xml->createElement('IMGURL', $heureka_imgurl));


                    $photographies = $product->getPhotographies();
                    if (!empty($photographies)) {
                        foreach ($product->getPhotographies() as $photography) {
                            $heureka_imgurl_alternative = $yagga_url . "/images/upload/product_" . $product->getProduct_id() . "/original/" . $photography->getPhotography_path();
                            $xmlShopitem->appendChild($xml->createElement('IMGURL_ALTERNATIVE', $heureka_imgurl_alternative));
                        }
                    }


                    $xmlShopitem->appendChild($xml->createElement('PRICE_VAT', $product->getProduct_price()));


                    if (array_key_exists($product->getCategory()->getCategory_id(), $categories)) {
                        $xmlShopitem->appendChild($xml->createElement('CATEGORYTEXT', $categories[$product->getCategory()->getCategory_id()]->getCategory_structure()));
                    } else {
                        $xmlShopitem->appendChild($xml->createElement('CATEGORYTEXT', ""));
                    }

                    if ($product->getProduct_stock() > 0) {
                        $xmlShopitem->appendChild($xml->createElement('DELIVERY_DATE', 0));
                    }

                    $xmlShopitem->appendChild($xml->createElement('ITEMGROUP_ID', $product->getProduct_id()));

                    $xmlShop->appendChild($xmlShopitem);
                }
            }
            $xml->appendChild($xmlShop);
            $output = $xml->saveXML();
        }
        return $output;
    }

}