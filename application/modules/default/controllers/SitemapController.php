<?php

class SitemapController extends Zend_Controller_Action {

    private $_pageMapper;
    private $_categoryMapper;
    private $_productMapper;
    private $_articleMapper;
    private $_manufacturerMapper;

    public function init() {
        $this->_pageMapper = new Default_Model_PageMapper();
        $this->_categoryMapper = new Default_Model_CategoryMapper();
        $this->_productMapper = new Default_Model_ProductMapper();
        $this->_articleMapper = new Default_Model_ArticleMapper();
        $this->_manufacturerMapper = new Default_Model_ManufacturerMapper();
    }

    public function indexAction() {

        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);

        $webUrl = "http://www.yagga.cz";

        $sitemapAry = array();

        $sitemapAry[] = array(
            'loc' => 'http://www.yagga.cz',
            'changefreq' => 'weekly'
        );

        /*
         * Aktivní Znacky/vyrobci 
         */
        
        $sitemapAry[] = array(
            'loc' => $webUrl . '/znacky',
            'changefreq' => 'weekly'
        );
        
        
        $manufacturers = $this->_manufacturerMapper->getManufacturers();
        
        if (!empty($manufacturers)) {
            foreach ($manufacturers as $manufacturer) {
                $sitemapAry[] = array(
                    'loc' => $webUrl . '/znacky/' . $manufacturer->getManufacturer_url(),
                    'changefreq' => 'weekly'
                );
            }
        }


        /*
         * Aktivní Stránky 
         */
        $pages = $this->_pageMapper->getPages();

        if (!empty($pages)) {
            foreach ($pages as $page) {
                $sitemapAry[] = array(
                    'loc' => $webUrl . '/' . $page->getPage_url(),
                    'changefreq' => 'weekly'
                );
            }
        }



        /*
         * Aktivní blogy 
         */

        $sitemapAry[] = array(
            'loc' => $webUrl . '/blog',
            'changefreq' => 'weekly'
        );

        $articles = $this->_articleMapper->getArticles('blog');

        if (!empty($articles)) {
            foreach ($articles as $article) {
                $sitemapAry[] = array(
                    'loc' => $webUrl . '/blog/' . $article->getArticle_url(),
                    'changefreq' => 'weekly'
                );
            }
        }





        /*
         * Aktivní Kategorie 
         */
        $categoriesUrlsAry = $this->_categoryMapper->getCategoriesUrlsAry();

        if (!empty($categoriesUrlsAry)) {
            foreach ($categoriesUrlsAry as $categoryUrlAry) {
                $sitemapAry[] = array(
                    'loc' => $webUrl . '/' . $categoryUrlAry,
                    'changefreq' => 'weekly'
                );
            }
        }


        /*
         * Aktivní Produkty 
         */
        $products = $this->_productMapper->getProductsToSitemap();

        if (!empty($products)) {
            foreach ($products as $product) {
                $sitemapAry[] = array(
                    'loc' => $webUrl . '/' . $product,
                    'changefreq' => 'weekly'
                );
            }
        }


        $this->getSitemap($sitemapAry);
    }

    // z pole vezme hodnoty a udela sitemap
    private function getSitemap($sitemapAry) {

        $sitemap = "<urlset xmlns='http://www.sitemaps.org/schemas/sitemap/0.9'>";

        foreach ($sitemapAry as $sitemapItem) {
            $sitemap .= "<url>";
            $sitemap .= "<loc>" . $sitemapItem['loc'] . "</loc>";
            $sitemap .= "<changefreq>" . $sitemapItem['changefreq'] . "</changefreq>";
            $sitemap .= "</url>";
        }
        $sitemap .= "</urlset>";
        return $this->_response->setHeader('Content-Type', 'text/xml; charset=utf-8')
                        ->setBody($sitemap);
    }

}
