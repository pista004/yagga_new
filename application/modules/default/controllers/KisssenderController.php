<?php

/*
 * speciální akce, Pošli pusinku a pošli pussynku
 */

class KisssenderController extends Zend_Controller_Action {

    private $_kisssenderMapper;

    public function init() {

        $this->_flashMessenger = $this->_helper->FlashMessenger;

        $flashMessenger = $this->_flashMessenger->getMessages();
        if (!empty($flashMessenger)) {
            $currentMessage = current($flashMessenger);
            if (!empty($currentMessage['info'])) {
                $this->view->infoFlashMessage = $currentMessage['info'];
            } else if (!empty($currentMessage['error'])) {
                $this->view->errorFlashMessage = $currentMessage['error'];
            }
        }


        $this->_kisssenderMapper = new Default_Model_KisssenderMapper();
    }

    /*
     * hlavní stránka pošlipusinku
     */

    public function indexAction() {

        $this->_helper->layout()->disableLayout();

        $this->view->headTitle = "Pošli pusinku";


        $kisssenderIps = $this->_kisssenderMapper->getIpAddressCount();
        $this->view->ipAddressCount = count($kisssenderIps);

        $kisssenderLatLongs = $this->_kisssenderMapper->getLatLongs();

        $toJsonAry = array();
        foreach ($kisssenderLatLongs as $kllItem) {
            if ($kllItem->getKisssender_latitude() && $kllItem->getKisssender_longitude()) {
                $toJsonAry[] = array(
                    'latitude' => $kllItem->getKisssender_latitude(),
                    'longitude' => $kllItem->getKisssender_longitude()
                );
            }
        }


        $this->view->latLngJson = json_encode($toJsonAry);

        $this->view->kissCount = count($kisssenderLatLongs);


//        $this->_helper->viewRenderer->setNoRender(TRUE);
//        echo "posilam"; die;
//        $this->view->headTitle = "Hodinky Geneva - Doplňky pro dokonalý outfit";
//        $this->view->metaDescription = "Módní doplňky, především hodinky značky Geneva za skvělé ceny. Vyladí Váš outfit k naprosté dokonalosti. Módní doplňky z Yagga.cz Vám udělají radost a budou Vám moc slušet.";
//
//        $form = new Default_Form_AddToCartForm();
//        $form->startForm();
//
//        $this->view->form = $form;
//
//        //where pro vyber produktu - vyberu jen doporucene produkty
//        $sqlOthers = array();
//        $sqlOthers['where'] = "product_recommend = 1";
//        
//        $recommended_products = $this->_productMapper->getProducts(1,3, $sqlOthers);
//        $this->view->recommended_products = $recommended_products;
        //TODO - uvazovat o cache!!! at se porad nenacita
//        $recommended_products = $this->_productMapper->getRecommendedProducts(1, 3);
//        $this->view->recommended_products = $recommended_products;
//        print_r($recommended_products);die;
    }

    public function sendkissAction() {
        $this->_helper->layout()->disableLayout();

        $this->view->headTitle = "Pošli pusinku a vzkaz";

        $form = new Default_Form_SendKissForm();
        $form->startForm();
        $this->view->form = $form;


        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {

            //kontrola ochrany proti spamu
            if ($this->getRequest()->getParam('rbt') == 'nospam') {
                $kisssender = new Default_Model_Kisssender();
                $kisssender->setOptions($form->getValues());

                $clientIpAddress = $this->getRequest()->getClientIp();
                $kisssender->setKisssender_ip_address($clientIpAddress);

                //zjistim, jestli nebyla zjistena poloha javascriptem, pokud ne, urcuju polohu podle ip adresy

                if ($this->getRequest()->getParam('kisssender_latitude') != "" && $this->getRequest()->getParam('kisssender_longitude') != "") {
                    $kisssender->setKisssender_latitude($this->getRequest()->getParam('kisssender_latitude'));
                    $kisssender->setKisssender_longitude($this->getRequest()->getParam('kisssender_longitude'));
                } else {
//podle ip adresy zjistim latitude a longitude                
                    $ipLite = new My_Ip2locationLite();

                    $ipLite->setKey('33ac952a414d9b454ab542621c966dfaa3e350f6facf3c772a9e3dfca25a6654');


//Get errors and locations
                    $locations = $ipLite->getCity($clientIpAddress);
//Getting the result
                    if (!empty($locations) && is_array($locations)) {
                        if ($locations['latitude'] && $locations['longitude']) {
                            $kisssender->setKisssender_latitude($locations['latitude']);
                            $kisssender->setKisssender_longitude($locations['longitude']);
                        }
                    }
                }


                $sendDate = time();
                $hash = md5($sendDate . '' . rand(1, 999999));

                $kisssender->setKisssender_send_date(time());
                $kisssender->setKisssender_hash($hash);

                try {

                    $this->_kisssenderMapper->save($kisssender);

                    $mailer = new My_Mailer();
                    $mailer->setUsername('poslipusinku@yagga.cz');
                    $mailer->setPassword('Mimi2LibaYaggaPusinka');
                    $mailer->setEmail_from('poslipusinku@yagga.cz');
                    $mailer->setEmail_from_name($kisssender->getKisssender_sender_name());

                    //nastaveni a odeslani emailu
                    $view = new Zend_View();
                    //nastavim cestu k sablonam(template) emailu
                    $view->setScriptPath(APPLICATION_PATH . '/modules/default/views/emails/kisssender/');

                    $view->sender_name = $kisssender->getKisssender_sender_name();
                    $view->hash = $kisssender->getKisssender_hash();
                    $html = $view->render('kisssender.phtml');

                    $subject = $kisssender->getKisssender_sender_name() . ' Vám posílá pusinku. Vyzvedněte si ji...';
                    $mailer->sendEmail($kisssender->getKisssender_email_to(), $subject, $html);

                    $this->_flashMessenger->addMessage(array('info' => 'MUCK! Pusinka byla úspěšně odeslána :-*'));

                    $this->_redirect($this->getRequest()->getRequestUri());
                } catch (Exception $e) {
                    $this->_flashMessenger->addMessage(array('error' => 'Při odesílání pusinky nastala chyba!<br />'));
                    $this->_redirect($this->getRequest()->getRequestUri());
                }
            }
        } else {
            $form->populate($form->getValues());
        }
    }

    public function detailAction() {
        $this->_helper->layout()->disableLayout();

        $hash = $this->getRequest()->getParam('hash');
        $kisssender = $this->_kisssenderMapper->getKisssenderByHash($hash);
        if (!empty($kisssender)) {

            $this->view->kisssender = $kisssender;

            $this->view->uri = $this->getRequest()->getRequestUri();


//            print_r($kisssender);die;

            $this->view->headTitle = "Pusinka a vzkaz od: " . $kisssender->getKisssender_sender_name();
            $this->view->facebookImageUrl = "/images/default/kisssender/kiss-mouth-600w.png";
//            $StripTagsfilter = new Zend_Filter_StripTags();
//            $this->view->metaDescription = $article->getArticle_seo_meta_description() ? $article->getArticle_seo_meta_description() : $StripTagsfilter->filter($article->getArticle_perex());
//            $this->view->facebookImageUrl = "/images/upload_article/article_" . $article->getArticle_id() . "/thumb/" . $article->getArticle_photography()->getPhotography_path();
        } else {
            throw new ErrorException;
        }
    }

}
