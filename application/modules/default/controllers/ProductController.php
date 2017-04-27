<?php

class ProductController extends Zend_Controller_Action {

    private $_productMapper;
    private $_photographyMapper;
    private $_categoryMapper;
    private $_variantMapper;
    private $_manufacturerMapper;

    public function init() {
        $this->_productMapper = new Default_Model_ProductMapper();
        $this->_photographyMapper = new Default_Model_PhotographyMapper();
        $this->_categoryMapper = new Default_Model_CategoryMapper();
        $this->_variantMapper = new Default_Model_VariantMapper();
        $this->_manufacturerMapper = new Default_Model_ManufacturerMapper();
    }

    public function detailAction() {
        $productUrl = $this->getRequest()->getParam('detailurl');

        $product = $this->_productMapper->getProductByUrl($productUrl);

//        print_r($product);die;
//        $selectedVariant = $this->getRequest()->getParam('variant');

        if (!empty($product)) {

            //        TODO, podle itemgroupid zjistit, zda se jedna o variantu a varianty vybrat, atd
// pri selektu nastavim i variant count, at rovnou vim, jestli product ma varianty nebo ne

            $productDetail = current($product);

            $variants = array();

            if ($productDetail->getProduct_itemgroup_product_id()) {
                //varianta
//                hlavni produkt 123
//                  varianta 1234 - itemgroupid: 123
//                * varianta 12345 - itemgroupid: 123
//                  varianta 123456 - itemgroupid: 123
//               select where itemgroupid = 123 (product->itemgroupid)
//               TODO, podle itemgroupid ziskam ostatni produkty a nastavim jako varianty 
//                echo "jsem varianta";

                $variants = $this->_productMapper->getVariantsByItemgroupId($productDetail->getProduct_itemgroup_product_id());

//                print_r($variants);
//                die;
            } else {

                // hlavni produkt
                //TODO zjistit, jestli ma varianty

                if ($productDetail->getVariants_count() > 0) {

                    $variants = $this->_productMapper->getVariantsByItemgroupId($productDetail->getProduct_id());

//                    echo "jsem hlavni produkt s variantama";
//                    die;
                }


//              * hlavni produkt 123
//                  varianta 1234 - itemgroupid: 123
//                  varianta 12345 - itemgroupid: 123
//                  varianta 123456 - itemgroupid: 123
//               select where itemgroupid = 123
            }





//            $photographies = $this->_photographyMapper->getPhotographiesByProductId($productDetail->getProduct_id());
            //ziskam varianty z DB
            $productDetail->setVariants($variants);
//print_r($productDetail);die;
            $form = new Default_Form_AddToCartForm();
//            $form->setVariants($variants);
//            $isVariant = false;
//            if (!empty($variants)) {
//                $isVariant = true;
//                $form->setIsVariant(true);
////                if (array_key_exists($selectedVariant, $variants)) {
////                    $form->setVariant($selectedVariant);
////                }
//            }
            $form->startForm();
            $form->getElement('product_id')->setValue($productDetail->getProduct_id());

            $form->getElement('price')->setValue($productDetail->getProduct_price());

//          pokud existuje varianta, nastavim cenu varianty do formulare - cena varianty se muze lisit od produktu  
//            if ($isVariant) {
//                if (array_key_exists($selectedVariant, $variants)) {
//                    $form->getElement('price')->setValue($variants[$selectedVariant]->getVariant_price());
//                }
//            }

//            print_r($form->getElements());die;



            /*
             * breadcrumbs
             */

            $categoriesUrlsAry = $this->_categoryMapper->getCategoriesUrlsAry();

            $parentsCategory = $this->_categoryMapper->getParentsObjects($productDetail->getCategory()->getCategory_id());

            $breadcrumbs = array();
            //ziskam kategorie pro breadcrumb a nastavim url pro breadcrumbs
            $breadcrumbsAry = array();
            $breadcrumbs = $this->_categoryMapper->setParentsToAry($parentsCategory);
//            print_r($categoriesUrlsAry);die;
            foreach ($breadcrumbs as $breadcrumb) {
                $breadcrumb->setCategory_url("/" . $categoriesUrlsAry[$breadcrumb->getCategory_id()]);
                $breadcrumbsAry[] = $breadcrumb;
            }

            $this->view->breadcrumbs = $breadcrumbsAry;


            $this->view->form = $form;

//            print_r($photographies);die;
            $this->view->headTitle = $productDetail->getProduct_seo_title() ? $productDetail->getProduct_seo_title() : $productDetail->getProduct_name();
            $this->view->metaDescription = $productDetail->getProduct_seo_meta_description() ? $productDetail->getProduct_seo_meta_description() : $productDetail->getProduct_perex();

            $this->view->product = $productDetail;
//            $this->view->photographies = $photographies;
//            if (array_key_exists($selectedVariant, $variants)) {
//                $this->view->selectedVariant = $selectedVariant;
//            }
//            print_r($productDetail);
//            die;
//            $defaultDbAdapter = Zend_Db_Table::getDefaultAdapter();
//
//            $whereItems = array();
//            if ($productDetail->getProduct_id()) {
//                $whereItems['category'] = $defaultDbAdapter->quoteInto('pc.product_category_category_id = ?', $productDetail->getCategory()->getCategory_id());
//            }
//
//            if ($productDetail->getProduct_manufacturer_id()) {
//                $whereItems['manufacturer'] = $defaultDbAdapter->quoteInto('m.manufacturer_id = ?', $productDetail->getProduct_manufacturer_id());
//            }
//
//            $othersWhere = array();
//            if (!empty($whereItems)) {
//                $othersWhere['where'] = $whereItems;
//            }
//
//            $recommended_products = $this->_productMapper->getRecommendedProducts($productDetail->getProduct_id(), 1, 16, $othersWhere);
//            $this->view->recommended_products = $recommended_products;
//
//
//            if (!empty($recommended_products)) {
//                $variants = $this->_variantMapper->getVariantsByProductIds(array_keys($recommended_products));
//            }
//            $this->view->variants = $variants;
            // promenna pro rozliseni normalniho vypisu a vypisu doporucenych produktu
            $this->view->isRecommend = true;

            $this->view->facebookImageUrl = "/images/upload/product_" . $productDetail->getProduct_id() . "/thumb/" . $productDetail->getMain_photography()->getPhotography_path();

//            $ns = new Zend_Session_Namespace('shoppingCart');
//            print_r($ns->cartItems);
        }
    }

    public function listAction() {
        $categoryUrl = $this->getRequest()->getParam('categoryurl');

        $categoriesUrlsAry = $this->_categoryMapper->getCategoriesUrlsAry();
//        print_r($categoriesUrlsAry);die;

        $breadcrumbs = array();
        $currentCategory = new Default_Model_Category();
        $products = array();
        $variants = array();
        if (in_array($categoryUrl, $categoriesUrlsAry)) {

            $categoryId = array_search($categoryUrl, $categoriesUrlsAry);

            $currentCategory = $this->_categoryMapper->getCategoryById($categoryId);

            $this->view->headTitle = $currentCategory->getCategory_seo_title() ? $currentCategory->getCategory_seo_title() : $currentCategory->getCategory_name();
            $this->view->metaDescription = $currentCategory->getCategory_seo_meta_description() ? $currentCategory->getCategory_seo_meta_description() : $currentCategory->getCategory_description();


            $this->view->h1 = $currentCategory->getCategory_h1() ? $currentCategory->getCategory_h1() : $currentCategory->getCategory_name();

            $parentsCategory = $this->_categoryMapper->getParentsObjects($categoryId);

            //ziskam kategorie pro breadcrumb a nastavim url pro breadcrumbs
            $breadcrumbsAry = array();
            $breadcrumbs = $this->_categoryMapper->setParentsToAry($parentsCategory);
            foreach ($breadcrumbs as $breadcrumb) {
                $breadcrumb->setCategory_url("/" . $categoriesUrlsAry[$breadcrumb->getCategory_id()]);
                $breadcrumbsAry[] = $breadcrumb;
            }

            //ziskam potomky kategorii a jejich id, pote id pouziju pro vyber produktu
            $categoryChildsObj = $this->_categoryMapper->getChilds($categoryId);
            $categoryChildsIds = $this->_categoryMapper->getCategoryChildsIds($categoryChildsObj);

            $categoryChildsIds[] = $categoryId;
//print_r($categoryChildsIds);die;


            $this->view->categoryUrls = $categoriesUrlsAry;
            $this->view->categoryChilds = $categoryChildsObj;


            $formFilter = new Default_Form_FilterForm();


            $manufacturersToForm = array();
            $manufacturers = $this->_manufacturerMapper->getManufacturersToFilter($categoryChildsIds);
//            print_r($manufacturers);die;
            foreach ($manufacturers as $mKey => $manufacturer) {
                $manufacturersToForm[$mKey] = $manufacturer->getManufacturer_name();
            }
//            print_r($manufacturersToForm);die;
            $formFilter->setManufacturers($manufacturersToForm);


            $variantsToForm = array();
            $variants = $this->_variantMapper->getVariantsToFilter($categoryChildsIds);
            foreach ($variants as $vKey => $variant) {
                $variantsToForm[$vKey] = $variant->getVariant_name();
            }
            $formFilter->setVariants($variantsToForm);

            $formFilter->startForm();


            $paramsToForm = array();
            $paramsToForm = array('manufacturer_id' => $manufacturersToForm, 'variant_id' => $variantsToForm);


            /*
             * 
             * ziskani parametru z url a nastaveni razeni podle ceny
             * 
             */
            $page = 0;
            if ($this->getRequest()->getParam('page')) {
                $page = $this->getRequest()->getParam('page');
            }

            $urlPriceDescParams = array('price' => 'desc');
            $urlPriceAscParams = array('price' => 'asc');
            if ($page > 1) {
                $urlPriceDescParams['page'] = $page;
                $urlPriceAscParams['page'] = $page;
            }

            //parametry do url pro razeni podle ceny
            $this->view->urlPriceDescParams = $urlPriceDescParams;
            $this->view->urlPriceAscParams = $urlPriceAscParams;

            $orderBy = array();
            $orderPriceParam = array();
            if ($this->getRequest()->getParam('price') == 'asc' || $this->getRequest()->getParam('price') == 'desc') {
                $orderPriceParam['product_price'] = $this->getRequest()->getParam('price');
                $this->view->activePrice = $this->getRequest()->getParam('price');
                $orderBy = $orderPriceParam;
            } else {
//                $orderBy = array('product_name' => 'asc');
                $orderBy = array('product_id' => 'desc');
            }




            /*
             * 
             * ziskani parametru z url a nastaveni - manufacturer a variant
             * 
             */

            $whereParams = array();
            $manufacturersParams = array();
            $manufacturersParams = $this->getRequest()->getParam('manufacturer_id');


            $variantsParams = array();
            $variantsParams = $this->getRequest()->getParam('variant_id');

            if (!empty($manufacturersParams)) {
                $whereParams['manufacturer_id'] = $manufacturersParams;
            }

            if (!empty($variantsParams)) {
                $whereParams['variant_id'] = $variantsParams;
            }

            $products = $this->_productMapper->getProductsByCategoryIds($categoryChildsIds, $whereParams, $orderBy, $page, 32);

            if (!empty($products)) {
                $variants = $this->_variantMapper->getVariantsByProductIds(array_keys($products));
            }


            /*
             * poslu do view aktivni filtery - zobrazim s krizkem pro zruseni filtru
             */
            $currentUrl = $this->getRequest()->getHttpHost() . '' . $this->getRequest()->getRequestUri();
            $activeFilterItems = array();


            foreach ($whereParams as $wKey => $wParams) {

                /*
                 * SEO - pokud je vybran jeden vyrobce nebo jedna varianta, pridam ji do nadpisu napr: panske boty nike, panske boty nike 45
                 */

                if (count($whereParams[$wKey]) == 1) {
                    $wId = current($whereParams[$wKey]);

                    $this->view->headTitle .= " " . $paramsToForm[$wKey][$wId];
//                    echo $this->view->headTitle;die;
                    $this->view->h1 .= " " . $paramsToForm[$wKey][$wId];
                }

                foreach ($wParams as $wParam) {
                    $urlParams = array_diff_assoc($this->getRequest()->getParams(), $this->getRequest()->getUserParams());

                    if (array_key_exists($wKey, $urlParams)) {
                        unset($urlParams[$wKey][array_search($wParam, $urlParams[$wKey])]);
                    }

                    $urlWithoutParams = substr($this->getRequest()->getRequestUri(), 0, strpos($this->getRequest()->getRequestUri(), "?"));

                    $filterItemUrl = 'http://' . $this->getRequest()->getHttpHost() . '' . $urlWithoutParams . '?' . urldecode(http_build_query($urlParams));

                    /*
                     * variant name - ted bere jen manufacturer
                     */
                    $activeFilterItems[$wParam] = array('name' => $paramsToForm[$wKey][$wParam], 'url' => $filterItemUrl);
                }
            }

            $this->view->activeFilterParams = $activeFilterItems;

            $this->view->formFilter = $formFilter;

            if (!empty($manufacturersParams) || !empty($variantsParams)) {
                $formFilter->populate($this->getRequest()->getParams());
            }


            $filterParams = array_diff_assoc($this->getRequest()->getParams(), $this->getRequest()->getUserParams());

            $this->view->filterParams = $filterParams;

            $filterParamsWithoutPage = $filterParams;
            if (array_key_exists('page', $filterParamsWithoutPage)) {
                unset($filterParamsWithoutPage['page']);
            }
            $this->view->filterParamsWithoutPage = $filterParamsWithoutPage;


//print_r($this->view->filterParams);die;
//            print_r($products);die;
            //zjistim slevy a nastavim pro vypis - sleva 20% napr
        }


        //vypocet parametru pro canonical - pokud je z kazdeho jeden, vyrobce, varianta... tak nastavim canonical napr panske boty vans 43

        $paramCanonical = false;
        foreach ($whereParams as $params) {
            if (count($params) == 1) {
                $paramCanonical = true;
            } else {
                $paramCanonical = false;
                break;
            }
        }


        if ($paramCanonical) {
            $this->view->canonicalPage = 'http://' . $this->getRequest()->getHttpHost() . $this->view->url() . $categoryUrl . '?' . urldecode(http_build_query($whereParams));
        } else {
            $this->view->canonicalPage = 'http://' . $this->getRequest()->getHttpHost() . $this->view->url() . $categoryUrl;
        }


        $this->view->breadcrumbs = $breadcrumbsAry;
        $this->view->currentCategory = $currentCategory;
        $this->view->products = $products;
        $this->view->variants = $variants;
        $this->view->categoryUrl = $categoryUrl;

        $this->view->paginator = $this->_productMapper->_paginator;


//        $form = new Default_Form_AddToCartForm();
//        $form->startForm();
//
//        $this->view->form = $form;
//
//
//        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
//            $cart = new My_ShoppingCart();
//            $cart->addItem($form->getValue('product_id'), $form->getValue('pieces'), $form->getValue('price'));
//
//            $this->_redirect($this->getRequest()->getRequestUri());
//        }
    }

}
