<?php

error_reporting(E_ALL|E_STRICT);
ini_set('display_errors', 'on');

//production
//error_reporting(0);
//ini_set('display_errors', 0);

// Define path to application directory
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));

defined('PUBLIC_PATH')
|| define('PUBLIC_PATH', realpath(dirname(__FILE__). '/../public'));

// cesta k uploadovanym obrazkum produktu - orig-velikost
defined('IMAGE_UPLOAD_PATH')
|| define('IMAGE_UPLOAD_PATH', realpath(dirname(__FILE__). '/../public/images/upload'));


// cesta k uploadovanym obrazkum clanku
defined('IMAGE_UPLOAD_PATH_ARTICLE')
|| define('IMAGE_UPLOAD_PATH_ARTICLE', realpath(dirname(__FILE__). '/../public/images/upload_article'));


// cesta k uploadovanym obrazkum vyrobcu/znacek
defined('IMAGE_UPLOAD_PATH_MANUFACTURER')
|| define('IMAGE_UPLOAD_PATH_MANUFACTURER', realpath(dirname(__FILE__). '/../public/images/upload_manufacturer'));

//cesta k uploadovanym nahledum obrazku
//defined('IMAGE_THUMB_UPLOAD_PATH')
//|| define('IMAGE_THUMB_UPLOAD_PATH', realpath(dirname(__FILE__). '/../public/images/upload/thumb'));


// cesta k uploadovanym fakturam - invoices
defined('INVOICE_UPLOAD_PATH')
|| define('INVOICE_UPLOAD_PATH', realpath(dirname(__FILE__). '/../public/invoices'));

// Define application environment
//defined('APPLICATION_ENV')
//    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

define('APPLICATION_ENV', 'development');



// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    realpath(APPLICATION_PATH . '/../library'),     
    get_include_path(),
)));


date_default_timezone_set('Europe/Prague');

/** Zend_Application */
require_once 'Zend/Application.php';

// Create application, bootstrap, and run
$application = new Zend_Application(
    APPLICATION_ENV,
    APPLICATION_PATH . '/configs/application.ini'
);
$application->bootstrap()
            ->run();