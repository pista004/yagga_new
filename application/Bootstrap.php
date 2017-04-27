<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap {

    protected function _initSessions() {
        $this->bootstrap('session');
    }

    protected function _initAutoloadModules() {

        $moduleLoader = new Zend_Application_Module_Autoloader(
                        array(
                            'namespace' => '',
                            'basePath' => APPLICATION_PATH
                        )
        );

        $moduleLoader->addResourceType('filter', 'filters/', 'Filter');
        $moduleLoader->addResourceType('router', 'routers/', 'Router');

        return $moduleLoader;
    }

    protected function _initHeadMeta() {
        $this->bootstrap('view');
        $view = $this->getResource('view');
        $view->headMeta()->appendHttpEquiv('content-type', 'text/html; charset=utf-8');
        $view->headMeta()->appendHttpEquiv('Content-Language', 'cs');
    }

    protected function _initJquery() {
        $this->bootstrap('view');
        $view = $this->getResource('view');

        $view->addHelperPath("ZendX/JQuery/View/Helper", "ZendX_JQuery_View_Helper");
    }

    protected function _initLanguage() {

        Zend_Loader::loadClass('Zend_Translate');

        $translate = new Zend_Translate(
                        array(
                            'adapter' => 'array',
                            'content' => APPLICATION_PATH . "/language/language.cs",
                            'locale' => 'cs',
                            'scan' => Zend_Translate::LOCALE_DIRECTORY
                        )
        );
        $translate->addTranslation(array('content' => APPLICATION_PATH . "/language/language.en", 'locale' => 'en'));

        $translate->setLocale('cs');

        // nastaveni zprav pro validaci formularu
        Zend_Validate_Abstract::setDefaultTranslator($translate);

        Zend_Registry::set('Zend_Translate', $translate);

        $this->bootstrap('view');
        $view = $this->getResource('view');
        $view->translate = $translate;
    }

    /*
     * routers
     */

    protected function _initRoute() {

        $router = Zend_Controller_Front::getInstance()->getRouter();

//        $router->addRoute('cart', new Zend_Controller_Router_Route('kosik',
//                        array('module' => 'default', 'controller' => 'cart', 'action' => 'index'))
//        );
//
//        $route_cart = new Zend_Controller_Router_Route('kosik', array('module' => 'default', 'controller' => 'cart', 'action' => 'index'));
//         $router->addRoute('cart_url', $route_cart);
//         
//

        $router->addRoute('cart', new Zend_Controller_Router_Route('kosik',
                        array('module' => 'default', 'controller' => 'cart', 'action' => 'index'))
        );

        $router->addRoute('order', new Zend_Controller_Router_Route('objednavka',
                        array('module' => 'default', 'controller' => 'order', 'action' => 'index'))
        );

        $router->addRoute('order/complete', new Zend_Controller_Router_Route('objednavka/dekujeme',
                        array('module' => 'default', 'controller' => 'order', 'action' => 'complete'))
        );


        $route_category = new Router_CategoryUrl(':categoryurl');
        $router->addRoute('category_url', $route_category);

        $route_detail = new Router_ProductUrl(':detailurl');
        $router->addRoute('product_url', $route_detail);

        $route_other_page = new Router_PageUrl(':pageurl');
        $router->addRoute('other_page', $route_other_page);

//        $route_article = new Router_ProductUrl('/blog/:articleurl');
//        $router->addRoute('article_url', $route_article);


        $router->addRoute('article_blog', new Zend_Controller_Router_Route('blog',
                        array(
                            'module' => 'default',
                            'controller' => 'article',
                            'action' => 'index')
                )
        );

        $router->addRoute('article_url', new Zend_Controller_Router_Route('/blog/:articleurl',
                        array(
                            'module' => 'default',
                            'controller' => 'article',
                            'action' => 'detail')
                )
        );

        $router->addRoute('sitemap', new Zend_Controller_Router_Route('sitemap.xml',
                        array('module' => 'default', 'controller' => 'sitemap', 'action' => 'index'))
        );


        $router->addRoute('kisssender_url', new Zend_Controller_Router_Route('/poslipusinku',
                        array(
                            'module' => 'default',
                            'controller' => 'kisssender',
                            'action' => 'index')
                )
        );


        $router->addRoute('kisssender_send_url', new Zend_Controller_Router_Route('/poslipusinku/formular',
                        array(
                            'module' => 'default',
                            'controller' => 'kisssender',
                            'action' => 'sendkiss')
                )
        );

        $router->addRoute('kisssender_detail_url', new Zend_Controller_Router_Route('/poslipusinku/vzkaz/:hash',
                        array(
                            'module' => 'default',
                            'controller' => 'kisssender',
                            'action' => 'detail')
                )
        );


        $router->addRoute('manufacturer_url', new Zend_Controller_Router_Route('/znacky',
                        array(
                            'module' => 'default',
                            'controller' => 'manufacturer',
                            'action' => 'index')
                )
        );

        $router->addRoute('manufacturer_detail_url', new Zend_Controller_Router_Route('/znacky/:manufacturerurl',
                        array(
                            'module' => 'default',
                            'controller' => 'manufacturer',
                            'action' => 'detail')
                )
        );
    }

    protected function _initCache() {

        $cache = Zend_Cache::factory(
                        'Core', 'File', array(
                    'lifetime' => 3600 * 24, //cache is cleaned once a day
                    'automatic_serialization' => true,
                    'automatic_cleaning_factor' => 50
                        ), array('cache_dir' => '../cache')
        );
        Zend_Registry::set('AdminCache24h', $cache);
    }

}

