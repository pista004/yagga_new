<?php

class Admin_ArticleController extends Zend_Controller_Action {

    private $_flashMessenger;
    private $_articleMapper;
    private $_articleTypeMapper;
    private $_photographyMapper;

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

        $this->_articleMapper = new Admin_Model_ArticleMapper();
        $this->_articleTypeMapper = new Admin_Model_ArticleTypeMapper();
        $this->_photographyMapper = new Admin_Model_PhotographyMapper();
    }

    public function indexAction() {
        
        $articles = $this->_articleMapper->getArticles();

//        print_r($articles);die;
        $this->view->articles = $articles;
        
    }

    public function addAction() {

//      ziskam typy clanku - blog nebo novinka/aktualita
        $articleTypes = $this->_articleTypeMapper->getArticleTypes();

        $articleTypesToForm = array();
        foreach ($articleTypes as $articleType) {
            $articleTypesToForm[$articleType->getArticle_type_id()] = $articleType->getArticle_type_name();
        }

        $form = new Admin_Form_EditArticleForm();
        $form->setArticle_types($articleTypesToForm);
        $form->startForm();
        $this->view->form = $form;


        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {

            $article = new Admin_Model_Article();
            $article->setOptions($form->getValues());
            $article->setArticle_insert_date(time());

            // pridani url, kontrola existence atd  
            if ($article->getArticle_url()) {
                $toUrl = $article->getArticle_url();
                $filterUrl = new Filter_Url();
                $url = $filterUrl->filter($toUrl);

                $filteredUrl = $filterUrl->checkArticleUrl($url);
                $article->setArticle_url($filteredUrl);
            } else {
                $toUrl = $article->getArticle_name();
                $filterUrl = new Filter_Url();
                $url = $filterUrl->filter($toUrl);

                $filteredUrl = $filterUrl->checkArticleUrl($url);
                $article->setArticle_url($filteredUrl);
            }

            if ($this->getRequest()->getParam('article_active_from_date')) {
                //prevod data ve formatu 22. 10. 2014 na timestamp
                list($day, $month, $year) = explode('. ', $form->getValue('article_active_from_date'));
                $article->setArticle_active_from_date(mktime(0, 0, 0, $month, $day, $year));
            } else {
                $article->setArticle_active_from_date(time());
            }

            $article->setArticle_article_type_id($this->getRequest()->getParam('article_article_type_id'));

            $db = $this->_articleMapper->getDbTable()->getDefaultAdapter();
            $db->beginTransaction();
            //pokud se nekde vyskytne chyba, provedu rollback a data nebudou ulozena, pokud vse probehne dobre, provedu commit
            try {

                $this->_articleMapper->save($article);

                $lastArticleId = $this->_articleMapper->getDbTable()->getDefaultAdapter()->lastInsertId();

                $upload = new Zend_File_Transfer_Adapter_Http();

                if ($upload->isUploaded()) {

                    $articleIdPath = "article_" . $lastArticleId;

                    $formatDirArticle = "%s/%s";
                    $dirArticle = sprintf($formatDirArticle, IMAGE_UPLOAD_PATH_ARTICLE, $articleIdPath);


                    $formatPath = '%s/%s/%s';
                    $dirMain = sprintf($formatPath, IMAGE_UPLOAD_PATH_ARTICLE, $articleIdPath, "main");
                    $dirThumb = sprintf($formatPath, IMAGE_UPLOAD_PATH_ARTICLE, $articleIdPath, "thumb");


                    //ukladam fotky do slozek podle id produktu, pokud slozka neexistuje, tak ji vytvorim
                    if (!is_dir($dirArticle)) {
                        mkdir($dirArticle);
                    }

                    if (!is_dir($dirMain)) {
                        mkdir($dirMain);
                    }

                    if (!is_dir($dirThumb)) {
                        mkdir($dirThumb);
                    }

                    $baseName = new Zend_Filter_BaseName();
                    $fileName = $baseName->filter($upload->getFileName());

                    $filter = new Zend_Filter_Word_SeparatorToDash($searchSeparator = ' ');
                    $img = $filter->filter(time() . "_" . $fileName);


                    $img_thumb_path = $dirThumb . "/" . $img;
                    $img_main_path = $dirMain . "/" . $img;

                    $resize = new Filter_File_Resize_Adapter_Gd();
                    $resize->resize(300, 150, true, $upload->getFileName(), $img_thumb_path);
                    $resize->resize(930, 465, true, $upload->getFileName(), $img_main_path);

                    $photography = new Admin_Model_Photography();
                    $photography->setPhotography_path($img);
                    $photography->setPhotography_article_id($lastArticleId);


                    $this->_photographyMapper->save($photography);
                }

                $db->commit();


                $module = $this->getRequest()->getModuleName();
                $controller = $this->getRequest()->getControllerName();
                $this->_redirect($module . '/' . $controller);
            } catch (Exception $e) {

                $this->_flashMessenger->addMessage(array('error' => 'Při ukládání nastala chyba!<br />' . $e->getMessage()));
                //dojde-li k chybe, provedu rollback, data nebudou ulozena do db
                $db->rollBack();
            }
        } else {
            $form->populate($form->getValues());
        }
    }

    public function editAction() {

        $articeId = $this->getRequest()->getParam('id');
        $articleMap = $this->_articleMapper->getArticleById($articeId);

        $this->view->article = $articleMap;

        if (!empty($articleMap)) {

            //      ziskam typy clanku - blog nebo novinka/aktualita
            $articleTypes = $this->_articleTypeMapper->getArticleTypes();

            $articleTypesToForm = array();
            foreach ($articleTypes as $articleType) {
                $articleTypesToForm[$articleType->getArticle_type_id()] = $articleType->getArticle_type_name();
            }
//print_r($articleMap->toArray());die;
            $form = new Admin_Form_EditArticleForm();
            $form->setArticle_types($articleTypesToForm);
            $form->setUrl($articleMap->getArticle_url());
            $form->startForm();
            $form->populate($articleMap->toArray());

            if ($articleMap->getArticle_insert_date()) {
                $form->getElement('article_insert_date')->setValue(date('d. m. Y', $articleMap->getArticle_insert_date()));
            }

            if ($articleMap->getArticle_active_from_date()) {
                $form->getElement('article_active_from_date')->setValue(date('d. m. Y', $articleMap->getArticle_active_from_date()));
            }

            $this->view->form = $form;


            if ($this->getRequest()->isPost()) {

                if ($form->isValid($this->getRequest()->getPost())) {
                    $article = new Admin_Model_Article();
                    $article->setOptions($form->getValues());
                    $article->setArticle_insert_date(time());

                    if (($articleMap->getArticle_url() != $article->getArticle_url()) && ($article->getArticle_url() != "")) {
                        $toUrl = $article->getArticle_url();
                        $filterUrl = new Filter_Url();
                        $url = $filterUrl->filter($toUrl);

                        $filteredUrl = $filterUrl->checkArticleUrl($url);
                        $article->setArticle_url($filteredUrl);
                    } else {
                        $article->setArticle_url($articleMap->getArticle_url());
                    }


                    if ($this->getRequest()->getParam('article_active_from_date')) {
                        //prevod data ve formatu 22. 10. 2014 na timestamp
                        list($day, $month, $year) = explode('. ', $form->getValue('article_active_from_date'));
                        $article->setArticle_active_from_date(mktime(0, 0, 0, $month, $day, $year));
                    } else {
                        $article->setArticle_active_from_date(time());
                    }

                    $article->setArticle_article_type_id($this->getRequest()->getParam('article_article_type_id'));
                    $article->setArticle_id($articeId);

                    $db = $this->_articleMapper->getDbTable()->getDefaultAdapter();
                    $db->beginTransaction();
                    //pokud se nekde vyskytne chyba, provedu rollback a data nebudou ulozena, pokud vse probehne dobre, provedu commit
                    try {

                        $this->_articleMapper->save($article);

                        $upload = new Zend_File_Transfer_Adapter_Http();

                        if ($upload->isUploaded()) {

                            $articleIdPath = "article_" . $articeId;

                            $formatDirArticle = "%s/%s";
                            $dirArticle = sprintf($formatDirArticle, IMAGE_UPLOAD_PATH_ARTICLE, $articleIdPath);


                            $formatPath = '%s/%s/%s';
                            $dirMain = sprintf($formatPath, IMAGE_UPLOAD_PATH_ARTICLE, $articleIdPath, "main");
                            $dirThumb = sprintf($formatPath, IMAGE_UPLOAD_PATH_ARTICLE, $articleIdPath, "thumb");


                            //ukladam fotky do slozek podle id produktu, pokud slozka neexistuje, tak ji vytvorim
                            if (!is_dir($dirArticle)) {
                                mkdir($dirArticle);
                            }

                            if (!is_dir($dirMain)) {
                                mkdir($dirMain);
                            }

                            if (!is_dir($dirThumb)) {
                                mkdir($dirThumb);
                            }

                            $baseName = new Zend_Filter_BaseName();
                            $fileName = $baseName->filter($upload->getFileName());

                            $filter = new Zend_Filter_Word_SeparatorToDash($searchSeparator = ' ');
                            $img = $filter->filter(time() . "_" . $fileName);


                            $img_thumb_path = $dirThumb . "/" . $img;
                            $img_main_path = $dirMain . "/" . $img;

                            $resize = new Filter_File_Resize_Adapter_Gd();
                            $resize->resize(300, 150, true, $upload->getFileName(), $img_thumb_path);
                            $resize->resize(930, 465, true, $upload->getFileName(), $img_main_path);

                            $photography = new Admin_Model_Photography();
                            $photography->setPhotography_path($img);
                            $photography->setPhotography_article_id($articeId);


                            $this->_photographyMapper->save($photography);
                        }

                        $db->commit();
                    } catch (Exception $e) {

                        $this->_flashMessenger->addMessage(array('error' => 'Při ukládání nastala chyba!<br />' . $e->getMessage()));
                        //dojde-li k chybe, provedu rollback, data nebudou ulozena do db
                        $db->rollBack();
                    }

                    $module = $this->getRequest()->getModuleName();
                    $controller = $this->getRequest()->getControllerName();
                    $this->_redirect($module . '/' . $controller);
                } else {
                    $form->populate($form->getValues());
                }
            }
        }
    }

    public function deleteAction() {
        $article_id = (int) $this->getRequest()->getParam('id');

        $photography = $this->_photographyMapper->getPhotoByArticleId($article_id);

//        print_r($photography);die;
        
        $db = $this->_articleMapper->getDbTable()->getDefaultAdapter();
        $db->beginTransaction();
        try {

            if ($article_id) {

                $imgMainPath = IMAGE_UPLOAD_PATH_ARTICLE . "/article_" . $article_id . "/main/" . $photography->getPhotography_path();
                $imgThumbPath = IMAGE_UPLOAD_PATH_ARTICLE . "/article_" . $article_id . "/thumb/" . $photography->getPhotography_path();

                chmod($imgMainPath, 0777);
                chmod($imgThumbPath, 0777);

//smazu soubory, potom smazu z databaze a pokud je prazdny adrear, tak jej taky smazu
                if (unlink($imgMainPath) && unlink($imgThumbPath)) {
                    $this->_photographyMapper->delete((int) $photography->getPhotography_id());

                    //pokud je prazdny adresar, tak ho smazu
                    if (count(glob(IMAGE_UPLOAD_PATH_ARTICLE . "/article_" . $article_id . "/main/*")) === 0) {
                        rmdir(IMAGE_UPLOAD_PATH_ARTICLE . "/article_" . $article_id . "/main");
                    }

                    if (count(glob(IMAGE_UPLOAD_PATH_ARTICLE . "/article_" . $article_id . "/thumb/*")) === 0) {
                        rmdir(IMAGE_UPLOAD_PATH_ARTICLE . "/article_" . $article_id . "/thumb");
                    }

                    if (count(glob(IMAGE_UPLOAD_PATH_ARTICLE . "/article_" . $article_id . "/*")) === 0) {
                        rmdir(IMAGE_UPLOAD_PATH_ARTICLE . "/article_" . $article_id);
                    }
                }
            }


            $this->_articleMapper->delete($article_id);
            $this->_flashMessenger->addMessage(array('info' => 'Článek byl úspěšně smazán.'));
            $db->commit();
        } catch (Exception $e) {
            $this->_flashMessenger->addMessage(array('error' => 'Při pokusu o smazání došlo k chybě!<br />' . $e->getMessage()));
            $db->rollBack();
        }
        
        $module = $this->getRequest()->getModuleName();
        $controller = $this->getRequest()->getControllerName();
        $this->_redirect($module . '/' . $controller);
    }

}

