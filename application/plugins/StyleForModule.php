<?php

/* Plugin pro nastaveni stylu podle modulu */

class Plugin_StyleForModule extends Zend_Controller_Plugin_Abstract {

    public function preDispatch(Zend_Controller_Request_Abstract $request) {

        $module = $request->getModuleName();

        $view = new Zend_View();

        $view->addHelperPath("ZendX/JQuery/View/Helper", "ZendX_JQuery_View_Helper");

        $view->jQuery()->setLocalPath('/js/jquery/jquery-1.10.2.min.js')
                ->setUiLocalPath('/js/jquery/js/jquery-ui-1.9.2.custom.min.js')
                ->addStylesheet('/js/jquery/css/smoothness/jquery-ui-1.9.2.custom.css')
                ->addJavascriptFile('/js/bootstrap/bootstrap.min.js');
        //styly pro admin module
        if ($module == 'admin') {
            $view->headLink()->appendStylesheet('/css/bootstrap.min.css');
            $view->headLink()->appendStylesheet('/css/bootstrap-theme.min.css');
            $view->headLink()->appendStylesheet('/css/admin_style.css?v=7');
            $view->headLink()->appendStylesheet('//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css');
            $view->headLink()->appendStylesheet('/js/plugins/magnific/magnific.css');

            $view->jQuery()
                    ->addJavaScriptFile('/js/plugins/ckeditor/ckeditor.js?v=1')
                    ->addJavaScriptFile('/js/plugins/jquery_form_plugin/jquery.form.min.js')
                    ->addJavaScriptFile('/js/functions_admin.js?v=2')
                    ->addJavaScriptFile('/js/plugins/magnific/magnific.js');
        }

        //styly pro default module
        if ($module == 'default') {
            $view->headLink()->appendStylesheet('/css/bootstrap.min.css');
            $view->headLink()->appendStylesheet('/css/default_style.css?v=147');
            $view->headLink()->appendStylesheet('//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css');
            $view->headLink()->appendStylesheet('/js/plugins/magnific/magnific.css');

            $view->jQuery()
                    ->addJavaScriptFile('/js/functions.js?v=29')
                    ->addJavaScriptFile('/js/plugins/magnific/magnific.js');
            
        }

        //styly pro vsechny layouty
    }

}