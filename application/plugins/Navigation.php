<?php

class Plugin_Navigation extends Zend_Controller_Plugin_Abstract {
    /* test function */

    private function testGetStructure($id, $categories = array()) {

//        $resultCateogires = array();
//        foreach($categories as $categoryId => $category){
//            $resultCateogires[$category->getCategory_parent()][$categoryId] = $category;
//        }
//        
//        
//        return $resultCateogires;
//        


        if (empty($categories)) {
            return $categories;
        }

        $resultCategories = array();
        foreach ($categories as $idCategory => $category) {

            if ($category->getCategory_parent() == $id) {
                $categoryObj = new Default_Model_Category();

                $categoryObj = $category;
                
                $categoryObj->setCategory_childs($this->testGetStructure($idCategory, $categories));
                $resultCategories[$idCategory] = $categoryObj;
            }
        }

        return $resultCategories;
    }

    public function preDispatch(Zend_Controller_Request_Abstract $request) {

        $categoryMapper = new Default_Model_CategoryMapper();

        $categories2 = $categoryMapper->getCategories2();

        $categories2 = $this->testGetStructure(0, $categories2);
        



//        $categories = $categoryMapper->getChilds(0, true);
//        $categoriesMenu = $this->getMenuCategories($categories);

        
        $categoriesMenu = $this->getMenuCategories($categories2);



        $pageMapper = new Default_Model_PageMapper();
        $pages = $pageMapper->getPages();

        $pagesMenu = array();
        if (!empty($pages)) {
            foreach ($pages as $page) {
                $pagesMenu[] = array(
                    'label' => $page->getPage_name(),
                    'title' => $page->getPage_name(),
                    'class' => 'page-menu',
                    'uri' => '/' . $page->getPage_url(),
                    'route' => 'other_page',
                );
            }
        }

        $defaultFooterMenu = array();
        $defaultFooterMenu = $pagesMenu;






        $pageMapper = new Default_Model_PageMapper();
        $pages = $pageMapper->getPages();

        $pagesMenu = array();
        if (!empty($pages)) {
            foreach ($pages as $page) {
                $pagesMenu[] = array(
                    'label' => $page->getPage_name(),
                    'title' => $page->getPage_name(),
                    'class' => 'page-menu',
                    'uri' => '/' . $page->getPage_url(),
                    'route' => 'other_page',
                );
            }
        }

        $defaultFooterMenu = array();
        $defaultFooterMenu = $pagesMenu;

//        $defaultMenu = array_merge($categoriesMenu, $pagesMenu);
        $defaultMenu = array();
        $defaultMenu = $categoriesMenu;

        $defaultMenu[] = array(
            'label' => 'Značky',
            'title' => 'Značky',
            'class' => 'page-menu',
            'uri' => '/znacky',
            'route' => 'manufacturer_url'
        );

//        $defaultMenu[] = array(
//            'label' => 'Blog',
//            'title' => 'Blog',
//            'class' => 'page-menu',
//            'uri' => '/blog',
//            'route' => 'article_blog'
//        );
//        print_r($defaultMenu);
//        die;

        $pages = array(
            array(
                'label' => 'Default',
                'title' => 'Default',
                'module' => 'default',
                'pages' => $defaultMenu,
            ),
            array(
                'label' => 'Default_footer',
                'title' => 'Default_footer',
                'module' => 'default',
                'pages' => $defaultFooterMenu,
            ),
            array(
                'label' => 'Admin',
                'title' => 'Admin',
                'module' => 'admin',
                'pages' => array(
                    array(
                        'label' => 'Přehledy',
                        'module' => 'admin',
                        'controller' => 'index',
                        'action' => 'index',
                    ),
                    array(
                        'label' => 'Objednávky',
                        'module' => 'admin',
                        'controller' => 'order',
                        'action' => 'index',
                        'pages' => array(
                            array(
                                'label' => 'Faktury',
                                'module' => 'admin',
                                'controller' => 'invoice',
                                'action' => 'index',
                            ),
                            array(
                                'label' => 'Doprava',
                                'module' => 'admin',
                                'controller' => 'delivery',
                                'action' => 'index',
                            ),
                            array(
                                'label' => 'Platba',
                                'module' => 'admin',
                                'controller' => 'payment',
                                'action' => 'index',
                            ),
                            array(
                                'label' => 'Stavy',
                                'module' => 'admin',
                                'controller' => 'orderstate',
                                'action' => 'index',
                            )
                        )
                    ),
                    array(
                        'label' => 'Produkty',
                        'module' => 'admin',
                        'controller' => 'product',
                        'action' => 'index',
                        'pages' => array(
                            array(
                                'label' => 'Výrobci',
                                'module' => 'admin',
                                'controller' => 'manufacturer',
                                'action' => 'index',
                            ),
                            array(
                                'label' => 'Kategorie',
                                'module' => 'admin',
                                'controller' => 'category',
                                'action' => 'index',
                            ),
                            array(
                                'label' => 'Parametry',
                                'module' => 'admin',
                                'controller' => 'parameter',
                                'action' => 'index'),
                            array(
                                'label' => 'Jednotky',
                                'module' => 'admin',
                                'controller' => 'parameterunit',
                                'action' => 'index')
                        )
                    ),
                    array(
                        'label' => 'Stránky',
                        'module' => 'admin',
                        'controller' => 'page',
                        'action' => 'index',
                        'pages' => array(
                            array(
                                'label' => 'Články',
                                'module' => 'admin',
                                'controller' => 'article',
                                'action' => 'index',
                            )
                        )
                    ),
                    array(
                        'label' => 'Zákazníci',
                        'module' => 'admin',
                        'controller' => 'user',
                        'action' => 'index',
                    ),
                    array(
                        'label' => 'Log',
                        'module' => 'admin',
                        'controller' => 'log',
                        'action' => 'index',
                    ),
//                    array(
//                        'label' => 'Pusinky',
//                        'module' => 'admin',
//                        'controller' => 'kisssender',
//                        'action' => 'index',
//                    ),
                ),
            ),
        );

//        print_r($pages);die;

        $view = Zend_Layout::getMvcInstance()->getView();

        $navigation = new Zend_Navigation($pages);

        $view->navigation($navigation);
    }

    //vstupem jsou kategorie jako objekt, projdu a převedu do pole - pages pro pouziti do menu
    private function getMenuCategories($ary, $url = "") {
        if (empty($ary)) {
            return;
        }

        $entries = array();
        foreach ($ary as $ar) {

            $tmp = $url;
            $url = $url . "/" . $ar->getCategory_url();

            $cur = array('label' => $ar->getCategory_name(), 'title' => $ar->getCategory_name(), 'class' => 'category-menu', 'uri' => $url, 'route' => 'category_url', 'pages' => array());


            $childs = $ar->getCategory_childs();
            if (!empty($childs)) {
                $cur['pages'] = $this->getMenuCategories($childs, $url);
            }

            $url = $tmp;
            $entries[] = $cur;
        }

        return $entries;
    }

}
