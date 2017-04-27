<?php

class Admin_AffiliateController extends Zend_Controller_Action {

    public function init() {
        
    }

    public function indexAction() {

        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);

        $shopParams = $this->getRequest()->getParam('shop');

        //parametr shopParam v url muze byt pole - obsahuje vice obchodu pro zpracovani v jednom souboru - reseno pomoci presmerovani
        $arrayToRedirect = array();
        if (is_array($shopParams)) {
            $shops = array('modernibotycz');

            if ((bool) !array_intersect($shops, $shopParams)) {
                die;
            }

//            $shopParam = array_intersect($shops, $shopParam);


            $arrayToRedirect = $shopParams;
            unset($arrayToRedirect[array_search(current($shopParams), $shopParams)]);

            $shopParam = current($shopParams);
        } else {
            $shopParam = $shopParams;
        }

        if ($shopParam) {

            switch ($shopParam) {
                case 'modernibotycz':

                    $moderniboty = new My_XmlAffiliate_Moderniboty();
                    $modernibotyData = $moderniboty->getData();

                    if (!empty($modernibotyData)) {
                        $dataBrain = new My_XmlAffiliate_DataBrain();
                        $dataBrain->setAffiliateData($moderniboty->getProgramName(), $modernibotyData);
                    }

                    break;
                
                default:
                    break;
            }


            if (!empty($arrayToRedirect)) {
                $urlToRedirect = "shop[]=";

                $urlToRedirect .= implode("&shop[]=", $arrayToRedirect);

                $this->_redirect('/admin/affiliate/?' . $urlToRedirect);
            }
        }
    }
    
    
    
    
    
    
    public function variantsupdateAction() {
//        ini_set('memory_limit', '256M');
        
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);

        $shopParams = $this->getRequest()->getParam('shop');

        //parametr shopParam v url muze byt pole - obsahuje vice obchodu pro zpracovani v jednom souboru - reseno pomoci presmerovani
        $arrayToRedirect = array();
        if (is_array($shopParams)) {
            $shops = array('modernibotycz');

            if ((bool) !array_intersect($shops, $shopParams)) {
                die;
            }

//            $shopParam = array_intersect($shops, $shopParam);


            $arrayToRedirect = $shopParams;
            unset($arrayToRedirect[array_search(current($shopParams), $shopParams)]);

            $shopParam = current($shopParams);
        } else {
            $shopParam = $shopParams;
        }

        if ($shopParam) {

            switch ($shopParam) {
                case 'modernibotycz':

                    $moderniboty = new My_XmlAffiliate_Moderniboty();
                    $modernibotyData = $moderniboty->getData();

                    if (!empty($modernibotyData)) {
                        $dataBrain = new My_XmlAffiliate_DataBrain();

                        $dataBrain->setAffiliateDataVariantsUpdate($moderniboty->getProgramName(), $modernibotyData);

                        echo "varianty aktualizovány";
                    }

                    break;
                
                default:
                    break;
            }


            if (!empty($arrayToRedirect)) {
                $urlToRedirect = "shop[]=";

                $urlToRedirect .= implode("&shop[]=", $arrayToRedirect);

                $this->_redirect('/admin/affiliate/variantsupdate?' . $urlToRedirect);
            }
        }
    }
    
    
    
    
    
    
    
    

    /*
     * prvotni import produktu - provadim pri zavedeni noveho feedu
     */

    public function firstimportAction() {

        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);

        $shopParam = $this->getRequest()->getParam('shop');
        $cycleParam = (int) $this->getRequest()->getParam('cycle');

        if ($shopParam) {

            switch ($shopParam) {
              
//                case 'perfektnipradlocz':
//                    $perfektnipradlo = new My_XmlAffiliate_Perfektnipradlo();
//                    $perfektnipradloData = $perfektnipradlo->getData();
//
//                    if (!empty($perfektnipradloData)) {
//
//                        $chunkedArray = array();
//                        $chunkedArray = array_chunk($perfektnipradloData, 1000, true);
////die;
//
//                        if (isset($chunkedArray[$cycleParam])) {
//                            $perfektnipradloData = $chunkedArray[$cycleParam];
////                            print_r($modernibotyData);die;
//                            $dataBrain = new My_XmlAffiliate_DataBrain();
//                            $dataBrain->setAffiliateDataFirstImport($perfektnipradlo->getProgramName(), $perfektnipradloData);
//
//                            $cycleParam++;
//                            if ($cycleParam < count($chunkedArray)) {
//                                $this->_redirect('/admin/affiliate/firstimport?shop=perfektnipradlocz&cycle=' . (int) $cycleParam);
//                            }
//                        }
//                    }
//
//                    break;
                
                
//                
//                case 'modernibotycz':
//                    $moderniboty = new My_XmlAffiliate_Moderniboty();
//                    $modernibotyData = $moderniboty->getData();
//
//                    if (!empty($modernibotyData)) {
//
//                        $chunkedArray = array();
//                        $chunkedArray = array_chunk($modernibotyData, 500, true);
////die;
//
//                        if (isset($chunkedArray[$cycleParam])) {
//                            $modernibotyData = $chunkedArray[$cycleParam];
////                            print_r($modernibotyData);die;
//                            $dataBrain = new My_XmlAffiliate_DataBrain();
//                            $dataBrain->setAffiliateDataFirstImport($moderniboty->getProgramName(), $modernibotyData);
//
//                            $cycleParam++;
//                            if ($cycleParam < count($chunkedArray)) {
//                                $this->_redirect('/admin/affiliate/firstimport?shop=modernibotycz&cycle=' . (int) $cycleParam);
//                            }
//                        }
//                    }
//
//                    break;
                    
                    
                    

                default:
                    break;
            }
        }
    }

    //projdu databazi a stahnu obrazky do slozek

    public function imagesAction() {


//        set_time_limit(90);
//        ini_set('memory_limit', '128M');

        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);


        $cycleParam = (int) $this->getRequest()->getParam('cycle');

        //pokud nejde ani jeden obrazek v chunkedArray stahnout prejdu v na dalsi pole napr chunkedParam 1, default je 0
        $chunkedParam = 0;
        $chunkedParam = (int) $this->getRequest()->getParam('chunked');

        /*
         * TODO: udelat at jde aktualizovat jen z konkretniho HASH kodu v odkazu
         */

        $products = array();

        $productMapper = new Admin_Model_ProductMapper();
        $products = $productMapper->getProductsWithHttpImage();


        if (!empty($products)) {

            //rozdelim pole po 50, tak aby se stihly stahnout vsechny obrazky do time limit 90
            $chunkedArray = array();
            $chunkedArray = array_chunk($products, 40, true);

            //kdyz se obrazek neulozi udelam +1, pokud se zadny obrazek z pole neulozi, zastavim script, jinak vykonavam dokud se obrazky ukladaji
            $isError = 0;


            if (array_key_exists($chunkedParam, $chunkedArray)) {

                foreach ($chunkedArray[$chunkedParam] as $productId => $product) {

                    $photoPath = $product['photography_path'];

//                    $mem_aviable = 100000000; //dostupna pamet v byte = 100M
                    $mem_aviable = 80000000;
                    $image_size = getimagesize($photoPath);
                    $mem_usage = $image_size[0] * $image_size[1] * 4; //4 bytes per pixel (RGBA)
                    if ($mem_usage > $mem_aviable) {
                        $isError++;
//                        if ($isError >= 150 || $isError >= count($chunkedArray[$chunkedParam])) {
//                            echo "obrazky jsou nejspis prilis velke, nelze stahnout<br />";
//                            die;
////                            echo "product_id: " . $productId . ", velikost obrazku: " . $image_size[0] . "x" . $image_size[1] . "<br />";
//                        }
                        //TODO: vypsat chybu do logu
                    } else {

                        //kontrola jestli je v db url na obrazek
                        if (strpos($photoPath, 'http://') !== false) {
                            //kontrola jestli uz existuje slozka
                            $directory = IMAGE_UPLOAD_PATH . '/product_' . $productId;

                            //nejprve vytvorim slozky, pote vkladam soubory
                            $formatPath = '%s/%s';
                            $dirOriginal = sprintf($formatPath, $directory, "original");
                            $dirList = sprintf($formatPath, $directory, "list");
                            $dirThumb = sprintf($formatPath, $directory, "thumb");


                            //ukladam fotky do slozek podle id produktu, pokud slozka neexistuje, tak ji vytvorim
                            if (!is_dir($directory)) {
                                mkdir($directory);
                            }

                            if (!is_dir($dirOriginal)) {
                                mkdir($dirOriginal);
                            }

                            if (!is_dir($dirList)) {
                                mkdir($dirList);
                            }

                            if (!is_dir($dirThumb)) {
                                mkdir($dirThumb);
                            }


                            $filterUrl = new Filter_Url();
                            $imageName = $filterUrl->filter($product['product_name']);

                            $extension = substr($photoPath, strripos($photoPath, "."), strlen($photoPath));

                            $filter = new Zend_Filter_Word_SeparatorToDash($searchSeparator = ' ');
                            $img = $filter->filter($imageName . "_" . time() . $extension);

                            $imageString = file_get_contents($photoPath);

                            $image = $dirOriginal . '/' . $img;
                            $isSaved = file_put_contents($image, $imageString);

                            if ($isSaved !== false) {
                                $img_thumb_path = $dirThumb . "/" . $img;
                                $img_list_path = $dirList . "/" . $img;

                                $resize = new Filter_File_Resize_Adapter_Gd();
                                $resize->resize(100, 100, true, $image, $img_thumb_path);
                                $resize->resize(400, 400, true, $image, $img_list_path);

                                $photography = new Admin_Model_Photography();
                                $photography->setPhotography_id($product['photography_id']);
                                $photography->setPhotography_path($img);
                                $photographyMapper = new Admin_Model_PhotographyMapper();
                                $photographyMapper->save($photography);
                            }
                        }
                    }
                }
//                echo $isError;die;
                $cycleParam++;

                if ($isError >= count($chunkedArray[$chunkedParam])) {
                    $chunkedParam++;
                    $this->_redirect('/admin/affiliate/images?cycle=' . (int) $cycleParam . '&chunked=' . (int) $chunkedParam);
                }

                $this->_redirect('/admin/affiliate/images?cycle=' . (int) $cycleParam);
            }
        }
    }

}
