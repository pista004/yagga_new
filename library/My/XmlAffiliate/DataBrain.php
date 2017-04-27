<?php

/*
 * 
 * DataBrain - zpracovava vsechny data
 * 
 * ziskam affiliate xml
 * vyberu existujici produkty, pokud nejake existuji
 * porovnam produkty s hodnotama v xml a v pripade zmeny delam update
 * nejsou produkty v db? delam insert
 * jsou produkty v db a nejsou v xml, nastavim produkty jako neaktivni
 * 
 * 
 */

//set_time_limit(500); //500 sec limit

class My_XmlAffiliate_DataBrain {

    private $_log;

    public function __construct() {

        $this->_log = new My_XmlAffiliate_Log();
    }

    /*
     * Projde vyrobce z xml a ti kteří nejsou v DB uloží
     */

    private function setManufacturers($programName, $affiliate) {

        $this->_log->setLog('Zahájeno zpracování výrobců.', 'INFO', $programName);

        /*
         * Ziskam znacky z DB
         */
        $manufacturerAffiliateManufacturerMapper = new Admin_Model_ManufacturerAffiliateManufacturerMapper();

        $manufacturersAffiliate = array();
        $manufacturersAffiliate = $manufacturerAffiliateManufacturerMapper->getManufacturers();

        $dbManufacturers = array();
        foreach ($manufacturersAffiliate as $dbManufacturer) {
            $dbManufacturers[] = mb_convert_case($dbManufacturer->getManufacturer_affiliate_manufacturer_name(), MB_CASE_LOWER, "UTF-8");
        }

        /*
         * ziskam znacky z affiliate
         */
        $affilManufacturers = array();
        foreach ($affiliate as $affilProduct) {
            $manufacturerName = mb_convert_case($affilProduct['manufacturer'], MB_CASE_LOWER, "UTF-8");

            if (!in_array($manufacturerName, $affilManufacturers) && $manufacturerName != "") {
                $affilManufacturers[] = $manufacturerName;
            }
        }

        /*
         * Porovnam kategorie affiliate a DB
         */
        $diffManufacturers = array();
        if (!empty($dbManufacturers)) {
            $diffManufacturers = array_diff($affilManufacturers, $dbManufacturers);
        } else {
            $diffManufacturers = $affilManufacturers;
        }

        /*
         * Ty co nejsou v DB pridam
         */

        $db = $manufacturerAffiliateManufacturerMapper->getDbTable()->getDefaultAdapter();
        $db->beginTransaction();

        try {

            $manufacturerAffiliateManufacturerMapper->bulkInsert($diffManufacturers);

            $db->commit();

            $this->_log->setLog('Výrobci byly úspěšně zpracováni.', 'INFO', $programName);
        } catch (Exception $e) {

            $logMessage = "Chyba při zpracování kategorií: " . $e;
            $this->_log->setLog($logMessage, 'ERROR', $programName);

            $db->rollBack();
        }
    }

    /*
     * Projde kategorie z xml a ty ktere nejsou v DB uloží
     */

    private function setCategories($programName, $affiliate) {

        $this->_log->setLog('Zahájeno zpracování kategorií.', 'INFO', $programName);


        /*
         * Ziskam kategorie z DB
         */
        $categoryAffiliateCategoryMapper = new Admin_Model_CategoryAffiliateCategoryMapper();

        $categoriesAffiliate = array();
        $categoriesAffiliate = $categoryAffiliateCategoryMapper->getCategories();

        $dbCategories = array();
        foreach ($categoriesAffiliate as $dbCategory) {
            $dbCategories[] = mb_convert_case($dbCategory->getCategory_affiliate_category_name(), MB_CASE_LOWER, "UTF-8");
        }

        /*
         * ziskam kategorie z affiliate
         */
        $affilCategories = array();
        foreach ($affiliate as $affilProduct) {
            $categoryName = mb_convert_case((string) $affilProduct['category'], MB_CASE_LOWER, "UTF-8");

            if (!in_array($categoryName, $affilCategories) && $categoryName != "") {
                $affilCategories[] = $categoryName;
            }
        }


        /*
         * Porovnam kategorie affiliate a DB
         */
        $diffCategories = array();
        if (!empty($dbCategories)) {
            $diffCategories = array_diff($affilCategories, $dbCategories);
        } else {
            $diffCategories = $affilCategories;
        }


        /*
         * Ty co nejsou v DB pridam
         */
        $db = $categoryAffiliateCategoryMapper->getDbTable()->getDefaultAdapter();
        $db->beginTransaction();

        try {

            $categoryAffiliateCategoryMapper->bulkInsert($diffCategories);

            $db->commit();

            $this->_log->setLog('Kategorie byly úspěšně zpracovány.', 'INFO', $programName);
        } catch (Exception $e) {

            $logMessage = "Chyba při zpracování kategorií: " . $e;
            $this->_log->setLog($logMessage, 'ERROR', $programName);

            $db->rollBack();
        }
    }

    /*
     * Projde Produkty z xml
     * Insert - pokud je v XML a neni v DB
     * Update - pokud byla zmenena nektera hodnota
     * Is active = 0 - neni v XML a je v DB
     */

    private function setProducts($programName, $affiliate) {

        $this->_log->setLog('Zahájeno zpracování produktů.', 'INFO', $programName);

        /*
         * ziskam vyrobce z DB
         */

        $manufacturerAffiliateManufacturerMapper = new Admin_Model_ManufacturerAffiliateManufacturerMapper();

        $manufacturersAffiliate = array();
        $sqlOthers['where'] = "mam.manufacturer_affiliate_manufacturer_manufacturer_id";
        $manufacturersAffiliate = $manufacturerAffiliateManufacturerMapper->getManufacturers(0, -1, $sqlOthers);

        $dbManufacturers = array();
        $dbManufacturersManufacturerIds = array();
        foreach ($manufacturersAffiliate as $dbManufacturer) {
            $dbManufacturers[$dbManufacturer->getManufacturer_affiliate_manufacturer_id()] = $dbManufacturer->getManufacturer_affiliate_manufacturer_name();
            $dbManufacturersManufacturerIds[$dbManufacturer->getManufacturer_affiliate_manufacturer_id()] = $dbManufacturer->getManufacturer_affiliate_manufacturer_manufacturer_id();
        }


        /*
         * ziskam kategorie z DB
         */

        $categoryAffiliateCategoryMapper = new Admin_Model_CategoryAffiliateCategoryMapper();

        $categoriesAffiliate = array();
        $sqlOthers['where'] = "cac.category_affiliate_category_category_id";
        $categoriesAffiliate = $categoryAffiliateCategoryMapper->getCategories(0, -1, $sqlOthers);

        $dbCategories = array();
        $dbCategoriesCategoryIds = array();
        foreach ($categoriesAffiliate as $dbCategory) {
            $dbCategories[$dbCategory->getCategory_affiliate_category_id()] = $dbCategory->getCategory_affiliate_category_name();
            $dbCategoriesCategoryIds[$dbCategory->getCategory_affiliate_category_id()] = $dbCategory->getCategory_affiliate_category_category_id();
        }

        /*
         * Vyberu produkty podle affiliate programu z DB - vsechny i neaktivni
         */
        $productMapper = new Admin_Model_ProductMapper();

        $products = array();
        $products = $productMapper->getProductsByAffiliateProgramName($programName);



        /*
         * INSERT - produkty, ktere jsou ve zdrojovem souboru a nejsou v DB
         */
        $productsToInsert = array();
        $productsToInsert = array_diff_key($affiliate, $products);
        $this->setProductsInsert($productsToInsert, $programName, $dbManufacturers, $dbCategories, $dbManufacturersManufacturerIds, $dbCategoriesCategoryIds);


        /*
         * DEACTIVATE - produkty, ktere jsou v DB a nejsou ve zdrojovem souboru
         */
        $productsToDeactive = array();
        $productsToDeactive = array_diff_key($products, $affiliate);
//        print_r($productsToDeactive);die;
        $this->setProductsDeactive($productsToDeactive, $programName);




        /*
         * UPDATE - produkty, ktere jsou v DB i ve zdrojovem souboru
         */
        $productsToUpdate = array();
        $productsToUpdate = array_intersect_key($products, $affiliate);
        $this->setProductsUpdate($productsToUpdate, $affiliate, $programName);
    }

    /*
     * update pokud je zmenena hodnota u produktu
     * 
     * TODO - nastavit aktualizaci variant v pripade zmeny skladovych zasob produktu, zatim neupravovano, protoze kompletni update produktu probiha jen jednou, vecer, pote probiha variants update, skoro kazdou hodinu pres den, takze v podstate se to resit nemusi, ale bylo by to dobre
     * 
     */

    private function setProductsUpdate($data, $affilData, $programName) {

        $toUpdate = array();

        $variantsToInsert = array();
//        $variantsToDelete = array();


        foreach ($data as $product) {
//print_r($affilData[$product['product_code']]);die;
            $isUpdate = false;

            $productToUpdate = new Admin_Model_Product();

            if ($affilData[$product['product_code']]['name'] != $product['product_name']) {

                $logMessage = "Produkt " . $product['product_name'] . ": úprava názvu z " . $product['product_name'] . " na " . $affilData[$product['product_code']]['name'];
                $this->_log->setLog($logMessage, 'INFO', $programName);

                $filterUrl = new Filter_Url();
                $url = $filterUrl->filter((string) $affilData[$product['product_code']]['name']);
                $checkedUrl = $filterUrl->checkProductUrl($url, $product['id']);

                $productToUpdate->setProduct_name((string) $affilData[$product['product_code']]['name']);
                $productToUpdate->setProduct_seo_title((string) $affilData[$product['product_code']]['name']);
                $productToUpdate->setProduct_url($checkedUrl);

                $isUpdate = true;
            }



            if ($affilData[$product['product_code']]['price'] != $product['product_price']) {

                $logMessage = "Produkt " . $product['product_code'] . ": úprava ceny z " . $product['product_price'] . " na " . $affilData[$product['product_code']]['price'];
                $this->_log->setLog($logMessage, 'INFO', $programName);

                $productToUpdate->setProduct_price((int) $affilData[$product['product_code']]['price']);

                $isUpdate = true;
            }

            if ($affilData[$product['product_code']]['recommended_price'] != "" && ($affilData[$product['product_code']]['recommended_price'] != $product['product_recommended_price'])) {

                $logMessage = "Produkt " . $product['product_code'] . ": úprava doporučené ceny z " . $product['product_recommended_price'] . " na " . $affilData[$product['product_code']]['recommended_price'];
                $this->_log->setLog($logMessage, 'INFO', $programName);

                $productToUpdate->setProduct_recommended_price($affilData[$product['product_code']]['recommended_price']);

                $isUpdate = true;
            }


            if ($affilData[$product['product_code']]['affiliate_url'] != $product['product_affiliate_url']) {

                $logMessage = "Produkt " . $product['product_code'] . ": úprava affiliate url z " . $product['product_affiliate_url'] . " na " . $affilData[$product['product_code']]['affiliate_url'];
                $this->_log->setLog($logMessage, 'INFO', $programName);

                $productToUpdate->setProduct_affiliate_url((string) $affilData[$product['product_code']]['affiliate_url']);

                $isUpdate = true;
            }



            if ($affilData[$product['product_code']]['stock'] != $product['product_stock']) {

                $logMessage = "Produkt " . $product['product_code'] . ": úprava skladu z " . $product['product_stock'] . " na " . $affilData[$product['product_code']]['stock'];
                $this->_log->setLog($logMessage, 'INFO', $programName);

                $productToUpdate->setProduct_stock($affilData[$product['product_code']]['stock']);

                $isUpdate = true;
            }

            if ($isUpdate == true) {
                $productToUpdate->setProduct_id($product['product_id']);
                $toUpdate[$product['product_code']] = $productToUpdate;
            }

            //projdu varianty a nastavim pro vlozeni
//            foreach ($affilData[$product['product_code']]['variant'] as $variant) {
//
//                $variantItem = array();
//                $variantItem['name'] = $variant['name'];
//                $variantItem['product_id'] = $product['product_id'];
//                $variantItem['stock'] = $variant['stock'];
//                $variantItem['purchase_price'] = 0;
//                $variantItem['price'] = 0;
//                $variantItem['is_active'] = 1;
//
//                $variantsToInsert[] = $variantItem;
//            }
//            $variantsToDelete[] = $product['product_id'];
        }


        /*
         * Update polozek
         */

        $productMapper = new Admin_Model_ProductMapper();
        $db = $productMapper->getDbTable()->getDefaultAdapter();
        $db->beginTransaction();
        try {

            foreach ($toUpdate as $pCode => $pToUpdate) {
                $productMapper->save($pToUpdate);

                $logMessage = "Produkt " . $pCode . ": upraven";
                $this->_log->setLog($logMessage, 'INFO', $programName);
            }

            $logMessage = "Produkty byly úspěšně upraveny.";
            $this->_log->setLog($logMessage, 'INFO', $programName);


            //smazu vsechny varianty a vlozim nove, aktualni - v budoucnu mozna upravit lepe, ale zatim se zda byt nejlepsi varianta tohle
//            $variantMapper = new Admin_Model_VariantMapper();
//            $variantMapper->deleteByProductIds($variantsToDelete);
//
//            $variantMapper->bulkInsert($variantsToInsert);
//            $logMessage = "Varianty byly úspěšně aktualizovany.";
//            $this->_log->setLog($logMessage, 'INFO', $programName);


            $db->commit();
        } catch (Exception $e) {

            $logMessage = "Chyba při úpravě produktů: " . $e;
            $this->_log->setLog($logMessage, 'ERROR', $programName);

            $db->rollBack();
        }
    }

    private function setVariantsUpdate($data, $affilData, $programName) {

        $variantsToUpdate = array();
        $variantsToInsert = array();

        foreach ($affilData as $affilKey => $affilItem) {
            foreach ($affilItem['variant'] as $affilItemVariant) {

//              zjistim, jestli existuje v DB datech product_code
                if (array_key_exists($affilKey, $data)) {

//              zjistim, jestli v DB datech s product_code existuje varianta s variant_name z Feedu, pokud varianta neexistuje, dam ji do pole insert
                    if (array_key_exists($affilItemVariant['name'], $data[$affilKey])) {
//                  kontrola, jestli u variant sedi sklady, pokud ne, dám do pole update  
                        if ($affilItemVariant['stock'] != $data[$affilKey][$affilItemVariant['name']]['variant_stock']) {
//                            $variantsToUpdate[$affilKey][$affilItemVariant['name']] = $affilItemVariant['stock'];
                            $variantToUpdate = new Admin_Model_Variant();
                            $variantToUpdate->setVariant_id($data[$affilKey][$affilItemVariant['name']]['variant_id']);
                            $variantToUpdate->setVariant_stock($affilItemVariant['stock']);

                            $variantsToUpdate[] = $variantToUpdate;
                        }
                    } else {
                        $dataProduct = array();
                        $dataProduct = current($data[$affilKey]);

                        if (!empty($dataProduct)) {
//                  pokud varianta neexistuje, dam ji do pole insert a vlozim

                            $variantToInsert = array();
                            $variantToInsert['name'] = $affilItemVariant['name'];
                            $variantToInsert['product_id'] = $dataProduct['product_id'];
                            $variantToInsert['stock'] = $affilItemVariant['stock'];
                            $variantToInsert['purchase_price'] = 0;
                            $variantToInsert['price'] = 0;
                            $variantToInsert['is_active'] = 1;

                            $variantsToInsert[] = $variantToInsert;

                        }
                    }
                }
            }
        }

        /*
         * Update a insert variant
         */

        $variantMapper = new Admin_Model_VariantMapper();
        $db = $variantMapper->getDbTable()->getDefaultAdapter();
        $db->beginTransaction();
        try {

            foreach ($variantsToUpdate as $vToUpdate) {
                $variantMapper->save($vToUpdate);
            }

            $logMessage = "Varianty byly úspěšně aktualizovany.";
            $this->_log->setLog($logMessage, 'INFO', $programName);

            if (!empty($variantsToInsert)) {
                $variantMapper->bulkInsert($variantsToInsert);
                $logMessage = "Varianty byly úspěšně vloženy.";
                $this->_log->setLog($logMessage, 'INFO', $programName);
            }

            $db->commit();
        } catch (Exception $e) {

            $logMessage = "Chyba při aktualizaci variant: " . $e;
            $this->_log->setLog($logMessage, 'ERROR', $programName);

            $db->rollBack();
        }
    }

    /*
     * TEST TEST TEST INSERT - vlozeni produktu do DB
     */

    private function setProductsInsert($data, $programName, $dbManufacturers, $dbCategories, $dbManufacturersManufacturerIds, $dbCategoriesCategoryIds) {
        $this->_log->setLog('INSERT: Zahájen INSERT produktů.', 'INFO', $programName);

        $productMapper = new Admin_Model_ProductMapper();
        $photographyMapper = new Admin_Model_PhotographyMapper();
        $productCategoryMapper = new Admin_Model_ProductCategoryMapper();
        $variantMapper = new Admin_Model_VariantMapper();

        $maxProductId = $productMapper->getMaxProductId();
        $productId = (int) $maxProductId + 1;

        $db = $productMapper->getDbTable()->getDefaultAdapter();
        $db->beginTransaction();
        try {

            //ziskam affiliate program id
            $affiliateProgramMapper = new Admin_Model_AffiliateProgramMapper();
            $affiliateProgram = $affiliateProgramMapper->getAffiliateProgramByName($programName);
            $affiliateProgramId = $affiliateProgram->getAffiliate_program_id();

//          pole vsech url ke vlozeni, slouzi ke kontrola, zda uz neexistuje stejna url pro vlozeni  
            $urlsToInsert = array();

            $productsToInsert = array();
            $photographiesToInsert = array();
            $productCategoriesToInsert = array();
            $variantsToInsert = array();


            foreach ($data as $product) {
                /*
                 * set Product
                 */

                $product['id'] = $productId;

                $filterUrl = new Filter_Url();

                $url = $filterUrl->filter($product['name']);

// provadi hodne selektu a zpomaluje beh celeho skriptu                
//                $checkedUrl = $filterUrl->checkProductUrl($url, $productId);

//                $product['url'] = $checkedUrl;

//                if (in_array($product['url'], $urlsToInsert)) {
                    $product['url'] = $url . '-' . $productId;
//                }

//                $urlsToInsert[] = $product['url'];



                $product['affiliate_program_name'] = (int) $affiliateProgramId;


                $product['is_active'] = 1;

                $manufacturer = mb_convert_case((string) $product['manufacturer'], MB_CASE_LOWER, "UTF-8");

                //defaultne nastavim znacku jako null
                $manufacturerId = new Zend_Db_Expr('NULL');
                if (!empty($dbManufacturersManufacturerIds) && !empty($dbManufacturers)) {
                    $manufacturerKey = array_search($manufacturer, $dbManufacturers);
                    if ($manufacturerKey) {
                        if (array_key_exists($manufacturerKey, $dbManufacturersManufacturerIds)) {
                            $manufacturerId = $dbManufacturersManufacturerIds[array_search($manufacturer, $dbManufacturers)];
                        }
                    }
                }

                $product['manufacturer_id'] = $manufacturerId;

                $product['insert_date'] = time();

                $productsToInsert[] = $product;
                $logMessage = "INSERT: produkt k uložení: " . $product['code'] . ': ' . $product['name'];

                $this->_log->setLog($logMessage, 'INFO', $programName);


                /*
                 * set Photography - main
                 */
                $photography = array();
                $photography['path'] = (string) $product['photography_path'];
                $photography['is_main'] = 1;
                $photography['product_id'] = $productId;

                $photographiesToInsert[] = $photography;

                $logMessage = "INSERT: Uložena fotografie produktu: " . $product['name'];
                $this->_log->setLog($logMessage, 'INFO', $programName);


                /*
                 * set other Photography
                 * ukladam dalsi fotografie
                 */


                if (array_key_exists('other_photographies', $product)) {
                    foreach ($product['other_photographies'] as $otherPhotographyPath) {
                        $oPhotography = array();
                        $oPhotography['path'] = (string) $otherPhotographyPath;
                        $oPhotography['is_main'] = new Zend_Db_Expr('NULL');
                        $oPhotography['product_id'] = $productId;

                        $photographiesToInsert[] = $oPhotography;

                        $logMessage = "INSERT: Uložena další fotografie produktu: " . $product['name'];
                        $this->_log->setLog($logMessage, 'INFO', $programName);
                    }
                }



                /*
                 * set Product Category
                 */

                $productCategory = array();
                $productCategory['product_id'] = $productId;

                $category = mb_convert_case((string) $product['category'], MB_CASE_LOWER, "UTF-8");

                //defaultne nastavim category id na NEZAŘAZENE
                $categoryId = 340;
                if (!empty($dbCategoriesCategoryIds) && !empty($dbCategories)) {
                    $categoryKey = array_search($category, $dbCategories);
                    if ($categoryKey) {
                        if (array_key_exists($categoryKey, $dbCategoriesCategoryIds)) {
                            $categoryId = $dbCategoriesCategoryIds[array_search($category, $dbCategories)];
                        }
                    }
                }

                $productCategory['category_id'] = $categoryId;

                $productCategoriesToInsert[] = $productCategory;

                $logMessage = "INSERT: Uložena kategorie produktu: " . $product['name'];

                $this->_log->setLog($logMessage, 'INFO', $programName);


                /*
                 * set Varianty 
                 */
                $variants = array();
//print_r($product['variant']);die;
                if (isset($product['variant'])) {
                    $variants = $product['variant'];

                    if (!empty($variants)) {

                        foreach ($variants as $variantItem) {

//                            if (is_array($variantItem)) {
                            $variant = array();
                            $variant['name'] = $variantItem['name'];
                            $variant['product_id'] = $productId;
                            $variant['stock'] = $variantItem['stock'];
                            $variant['purchase_price'] = 0;
                            $variant['price'] = 0;
                            $variant['is_active'] = 1;
//                            } else {
//                                $variant = array();
//                                $variant['name'] = $variantItem;
//                                $variant['product_id'] = $productId;
//                                $variant['stock'] = 1;
//                                $variant['purchase_price'] = 0;
//                                $variant['price'] = 0;
//                                $variant['is_active'] = 1;
//                            }

                            $variantsToInsert[] = $variant;
//print_r($variantsToInsert);die;
                            $logMessage = "INSERT: Uložena varianta produktu: " . $product['name'] . " " . $variant['name'];

                            $this->_log->setLog($logMessage, 'INFO', $programName);
                        }
                    }
                }

                $productId = $productId + 1;
            }
//print_r($productsToInsert);die;
            $productMapper->bulkInsert($productsToInsert);
//            echo "jo2";die;

            $photographyMapper->bulkInsert($photographiesToInsert);
            $productCategoryMapper->bulkInsert($productCategoriesToInsert);
            $variantMapper->bulkInsert($variantsToInsert);

            $db->commit();
            $this->_log->setLog('INSERT: Vložení produktů bylo úspěšně zpracováno.', 'INFO', $programName);
        } catch (Exception $e) {

            $logMessage = "INSERT: Chyba při vkládání produktů: " . $e;
            $this->_log->setLog($logMessage, 'ERROR', $programName);

            $db->rollBack();
        }
    }

    /*
     * DEACTIVE - jsou v db, ale nejsou ve zdrojovem souboru, nastavim na neaktivni
     */

    private function setProductsDeactive($data, $programName) {

        $productMapper = new Admin_Model_ProductMapper();
        $db = $productMapper->getDbTable()->getDefaultAdapter();
        $db->beginTransaction();
        try {

            foreach ($data as $product) {


                if ($product['product_is_active'] != 0) {
                    $productId = $product['product_id'];

                    $productObj = new Admin_Model_Product();
                    $productObj->setProduct_id($productId);
                    $productObj->setProduct_is_active(0);

                    $productMapper->save($productObj);

                    $logMessage = "Is_active = 0 pro produkt: " . $productObj->getProduct_id();
                    $this->_log->setLog($logMessage, 'INFO', $programName);
                }
            }

            $db->commit();

            $this->_log->setLog('Deaktivace produktů byla úspěšnš zpracována.', 'INFO', $programName);
        } catch (Exception $e) {

            $logMessage = "Chyba při deaktivaci produktů: " . $e;
            $this->_log->setLog($logMessage, 'ERROR', $programName);

            $db->rollBack();
        }
    }

    /*
     * hlavni funkce - prochazi vsechny affiliate, nastavuje a uklada do db
     */

    public function setAffiliateData($programName, $affiliateData) {

//        set_time_limit(90);

        $affiliateProgramMapper = new Admin_Model_AffiliateProgramMapper();
        $affiliateProgram = $affiliateProgramMapper->getAffiliateProgramByName($programName);
        if ($affiliateProgram instanceof Admin_Model_AffiliateProgram) {

            if ($affiliateProgram->getAffiliate_program_is_ready() == 2) {
                $this->setCategories($programName, $affiliateData);

                $this->setManufacturers($programName, $affiliateData);

                $this->setProducts($programName, $affiliateData);
            } else {
                $logMessage = "Affiliate program " . $programName . " nemá povolenou aktualizaci produktů. Proveďte prvotní import produktů, poté nastavte v DB parametr affiliate_program_is_ready na 2";
                $this->_log->setLog($logMessage, 'ERROR', $programName);
            }
        }

        $log = $this->_log->getLog();
        $logMessage = $this->_log->getLogStringMessage($log);

        $logObj = new Admin_Model_Log();
        $logObj->setLog_message($logMessage);
        $logObj->setLog_inserted(time());

        try {
            $logMapper = new Admin_Model_LogMapper();
            $logMapper->save($logObj);
        } catch (Exception $e) {
            echo "Zápis do tabulky log se nezdařil: " . $e;
        }
    }

    /*
     * hlavni funkce - prochazi vsechny affiliate, nastavuje a uklada do db
     */

    public function setAffiliateDataVariantsUpdate($programName, $affiliateData) {
//        set_time_limit(90);

        $affiliateProgramMapper = new Admin_Model_AffiliateProgramMapper();
        $affiliateProgram = $affiliateProgramMapper->getAffiliateProgramByName($programName);
        if ($affiliateProgram instanceof Admin_Model_AffiliateProgram) {

            if ($affiliateProgram->getAffiliate_program_is_ready() == 2) {
                /*
                 * Vyberu produkty podle affiliate programu z DB - vsechny i neaktivni\
                 * 
                 * 
                 * z DB vezmu vsechny varianty podle affiliate program name 
                 * vsechny varianty projdu, porovnam je s daty z feedu
                 * dosko ke zmene existujici varianty? UPDATE
                 * je varianta ve feedu a není v DB? INSERT (musi existovat produkt)
                 * zbytek variant nastavím na variant_stock = 0
                 * 
                 * 
                 * 
                 */
//                $productMapper = new Admin_Model_ProductMapper();
//
//                $products = array();
//                $products = $productMapper->getProductsByAffiliateProgramName($programName);

                $variantMapper = new Admin_Model_VariantMapper();

                $variants = array();
                $variants = $variantMapper->getVariantsByAffiliateProgramName($programName);

                //vezmu jen produkty, ktere jsou v DB i v affiliate feedu - prunik
//                $productsToUpdate = array();
//                $productsToUpdate = array_intersect_key($products, $affiliateData);


                $this->setVariantsUpdate($variants, $affiliateData, $programName);
            } else {
                $logMessage = "Affiliate program " . $programName . " nemá povolenou aktualizaci produktů. Proveďte prvotní import produktů, poté nastavte v DB parametr affiliate_program_is_ready na 2";
                $this->_log->setLog($logMessage, 'ERROR', $programName);
            }
        }

        $log = $this->_log->getLog();
        $logMessage = $this->_log->getLogStringMessage($log);

        $logObj = new Admin_Model_Log();
        $logObj->setLog_message($logMessage);
        $logObj->setLog_inserted(time());

        try {
            $logMapper = new Admin_Model_LogMapper();
            $logMapper->save($logObj);
        } catch (Exception $e) {
            echo "Zápis do tabulky log se nezdařil: " . $e;
        }
    }

    /*
     * slouzi pouze pro prvotni import polozek
     */

    public function setAffiliateDataFirstImport($programName, $affiliateData) {
//        set_time_limit(90);


        $affiliateProgramMapper = new Admin_Model_AffiliateProgramMapper();
        $affiliateProgram = $affiliateProgramMapper->getAffiliateProgramByName($programName);


        if ($affiliateProgram instanceof Admin_Model_AffiliateProgram) {

            $this->setCategories($programName, $affiliateData);
            $this->setManufacturers($programName, $affiliateData);

            if ($affiliateProgram->getAffiliate_program_is_ready() == 1) {
                $this->_log->setLog('Zahájeno zpracování produktů.', 'INFO', $programName);



                /*
                 * ziskam znacky z DB
                 */

                $manufacturerAffiliateManufacturerMapper = new Admin_Model_ManufacturerAffiliateManufacturerMapper();

                $manufacturersAffiliate = array();
                $sqlOthers['where'] = "mam.manufacturer_affiliate_manufacturer_manufacturer_id";
                $manufacturersAffiliate = $manufacturerAffiliateManufacturerMapper->getManufacturers(0, -1, $sqlOthers);

                $dbManufacturers = array();
                $dbManufacturersManufacturerIds = array();
                foreach ($manufacturersAffiliate as $dbManufacturer) {
                    $dbManufacturers[$dbManufacturer->getManufacturer_affiliate_manufacturer_id()] = $dbManufacturer->getManufacturer_affiliate_manufacturer_name();
                    $dbManufacturersManufacturerIds[$dbManufacturer->getManufacturer_affiliate_manufacturer_id()] = $dbManufacturer->getManufacturer_affiliate_manufacturer_manufacturer_id();
                }


                /*
                 * ziskam kategorie z DB
                 */

                $categoryAffiliateCategoryMapper = new Admin_Model_CategoryAffiliateCategoryMapper();

                $categoriesAffiliate = array();
                $sqlOthers['where'] = "cac.category_affiliate_category_category_id";
                $categoriesAffiliate = $categoryAffiliateCategoryMapper->getCategories(0, -1, $sqlOthers);

                $dbCategories = array();
                $dbCategoriesCategoryIds = array();
                foreach ($categoriesAffiliate as $dbCategory) {
                    $dbCategories[$dbCategory->getCategory_affiliate_category_id()] = $dbCategory->getCategory_affiliate_category_name();
                    $dbCategoriesCategoryIds[$dbCategory->getCategory_affiliate_category_id()] = $dbCategory->getCategory_affiliate_category_category_id();
                }


                /*
                 * INSERT - produkty, ktere jsou ve zdrojovem souboru
                 */
                $this->setProductsInsert($affiliateData, $programName, $dbManufacturers, $dbCategories, $dbManufacturersManufacturerIds, $dbCategoriesCategoryIds);
            } else {
                $logMessage = "Affiliate program " . $programName . " nemá povolenou aktualizaci produktů. Spárujte kategorie s existujícíma a zkontrolujte značky, poté nastavte v DB parametr affiliate_program_is_ready na hodnotu 1.";
                $this->_log->setLog($logMessage, 'ERROR', $programName);
            }
        } else {
            $logMessage = "Affiliate program " . $programName . " není v databázi.";
            $this->_log->setLog($logMessage, 'ERROR', $programName);
        }

        $log = $this->_log->getLog();
        $logMessage = $this->_log->getLogStringMessage($log);

        $logObj = new Admin_Model_Log();
        $logObj->setLog_message($logMessage);
        $logObj->setLog_inserted(time());

        try {
            $logMapper = new Admin_Model_LogMapper();
            $logMapper->save($logObj);
        } catch (Exception $e) {
            echo "Zápis do tabulky log se nezdařil: " . $e;
        }
    }

}
