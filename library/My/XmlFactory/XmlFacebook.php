<?php

class My_XmlFactory_XmlFacebook {

    public function getXml($data) {
//        ini_set('memory_limit', '1280M');
//        print_r($data);
//        die;

        $shop_url = "http://www.yagga.cz";

        $output = "";
        if (!empty($data)) {

            $xml = new DOMDocument('1.0', 'utf-8');
            $xmlShop = $xml->createElement('feed');
            $xmlShop->setAttribute('xmlns', 'http://www.w3.org/2005/Atom');
            $xmlShop->setAttribute('xmlns:g', 'http://base.google.com/ns/1.0');

            $xmlShop->appendChild($xml->createElement('title', 'www.yagga.cz'));

            $link = $xml->createElement('link');
            $link->setAttribute('rel', 'self');
            $link->setAttribute('href', 'http://www.yagga.cz');
            $xmlShop->appendChild($link);

//            $data = array();
            foreach ($data as $product) {

                $variants = array();
                if (array_key_exists('variants', $product)) {
                    $variants = $product['variants'];
                }

                if (!empty($variants)) {
                    foreach ($variants as $variant) {
                        $xmlShopitem = $xml->createElement('entry');
                        $item_id = $product['product_id'] . "_" . $variant['variant_id'];
                        $xmlShopitem->appendChild($xml->createElement('g:id', $item_id));


                        $productname = $product['product_name'] . " " . $variant['variant_name'];

                        $productNameElement = $xmlShopitem->appendChild($xml->createElement('g:title'));
                        $productNameElement->appendChild($xml->createCDATASection($productname));


                        $description = $xmlShopitem->appendChild($xml->createElement('g:description'));
                        $description->appendChild($xml->createCDATASection($product['product_description']));

                        $url = $shop_url . "/" . $product['product_url'] . "?variant=" . $variant['variant_id'];
                        $xmlShopitem->appendChild($xml->createElement('g:link', $url));


                        $imgurl = $shop_url . "/images/upload/product_" . $product['product_id'] . "/original/" . $product['photography_path'];
                        $xmlShopitem->appendChild($xml->createElement('g:image_link', $imgurl));

                        $manufacturer = $xmlShopitem->appendChild($xml->createElement('g:brand'));
                        $manufacturer->appendChild($xml->createCDATASection($product['manufacturer_name']));

                        $xmlShopitem->appendChild($xml->createElement('g:condition', 'new'));


                        if ($variant['variant_stock'] > 0) {
                            $xmlShopitem->appendChild($xml->createElement('g:availability', 'in stock'));
                        } else {
                            $xmlShopitem->appendChild($xml->createElement('g:availability', 'out of stock'));
                        }


                        $xmlShopitem->appendChild($xml->createElement('g:price', $variant['variant_price']));

                        $xmlShopitem->appendChild($xml->createElement('g:gtin', $product['product_ean']));


                        $xmlShopitem->appendChild($xml->createElement('item_group_id', $product['product_id']));


                        $xmlShop->appendChild($xmlShopitem);
                    }
                } else {
                    $xmlShopitem = $xml->createElement('entry');
                    $xmlShopitem->appendChild($xml->createElement('g:id', $product['product_id']));


                    $productname = $product['product_name'] . " " . $variant['variant_name'];

                    $productNameElement = $xmlShopitem->appendChild($xml->createElement('g:title'));
                    $productNameElement->appendChild($xml->createCDATASection($productname));


                    $description = $xmlShopitem->appendChild($xml->createElement('g:description'));
                    $description->appendChild($xml->createCDATASection($product['product_description']));

                    $url = $shop_url . "/" . $product['product_url'];
                    $xmlShopitem->appendChild($xml->createElement('g:link', $url));

                    $imgurl = $shop_url . "/images/upload/product_" . $product['product_id'] . "/original/" . $product['photography_path'];
                    $xmlShopitem->appendChild($xml->createElement('g:image_link', $imgurl));

                    $manufacturer = $xmlShopitem->appendChild($xml->createElement('g:brand'));
                    $manufacturer->appendChild($xml->createCDATASection($product['manufacturer_name']));

                    $xmlShopitem->appendChild($xml->createElement('g:condition', 'new'));


                    if ($product['product_stock'] > 0) {
                        $xmlShopitem->appendChild($xml->createElement('g:availability', 'in stock'));
                    } else {
                        $xmlShopitem->appendChild($xml->createElement('g:availability', 'out of stock'));
                    }



                    $xmlShopitem->appendChild($xml->createElement('g:price', $product['product_price']));

                    $xmlShopitem->appendChild($xml->createElement('g:gtin', $product['product_ean']));

                    $xmlShopitem->appendChild($xml->createElement('item_group_id', $product['product_id']));

                    $xmlShop->appendChild($xmlShopitem);
                }
            }
            $xml->appendChild($xmlShop);
            $output = $xml->saveXML();
        }
        return $output;
    }

}
