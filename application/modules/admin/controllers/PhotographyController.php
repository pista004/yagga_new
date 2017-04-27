<?php

class Admin_PhotographyController extends Zend_Controller_Action {

    private $_photographyMapper;
    private $_productMapper;
    private $_flashMessenger;

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

        $this->_photographyMapper = new Admin_Model_PhotographyMapper();
        $this->_productMapper = new Admin_Model_ProductMapper();
    }

    public function editAction() {

        $product_id = (int) $this->getRequest()->getParam('id');

        if ($product_id) {

            $productMap = $this->_productMapper->find($product_id);

            $this->view->product = $productMap;

            //formular pro vlozeni fotografie, je zpracovavan ajaxem
            $form = new Admin_Form_EditPhotographyForm();
            $form->setProductId($product_id);
            $form->startForm();

            $this->view->form = $form;


            //ziskani dat pro vypis
            $photos = $this->_photographyMapper->getPhotosByProductId($product_id);

            $this->view->photos = $photos;

            $productPath = "product_" . $product_id;

            $this->view->photoPathOriginal = "/images/upload/" . $productPath . "/original/";
            $this->view->photoPathThumb = "/images/upload/" . $productPath . "/thumb/";
        }
    }

    public function deleteAction() {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        $photography_id = (int) $this->getRequest()->getParam('id');

//      mazu obrazek, podle toho jestli je product nebo articel/clanek/blok podle parametru v url, pak mazu i soubory a slozky  

        $product_id = (int) $this->getRequest()->getParam('product_id');
        $article_id = (int) $this->getRequest()->getParam('article_id');
        $manufacturer_id = (int) $this->getRequest()->getParam('manufacturer_id');

        $photography = $this->_photographyMapper->getPhotographyById($photography_id);

        if (!empty($photography)) {

//           zde musim rozlisit co se bude mazat pokud mazu fotky produktu nebo pokud mazu fotky article

            if ($product_id) {

                $imgOriginalPath = IMAGE_UPLOAD_PATH . "/product_" . $product_id . "/original/" . $photography->getPhotography_path();
                $imgListPath = IMAGE_UPLOAD_PATH . "/product_" . $product_id . "/list/" . $photography->getPhotography_path();
                $imgThumbPath = IMAGE_UPLOAD_PATH . "/product_" . $product_id . "/thumb/" . $photography->getPhotography_path();

                chmod($imgOriginalPath, 0777);
                chmod($imgListPath, 0777);
                chmod($imgThumbPath, 0777);

//smazu soubory, potom smazu z databaze a pokud je prazdny adrear, tak jej taky smazu
                if (unlink($imgOriginalPath) && unlink($imgListPath) && unlink($imgThumbPath)) {
                    $this->_photographyMapper->delete($photography_id);

                    //pokud je prazdny adresar, tak ho smazu
                    if (count(glob(IMAGE_UPLOAD_PATH . "/product_" . $product_id . "/original/*")) === 0) {
                        rmdir(IMAGE_UPLOAD_PATH . "/product_" . $product_id . "/original");
                    }

                    if (count(glob(IMAGE_UPLOAD_PATH . "/product_" . $product_id . "/list/*")) === 0) {
                        rmdir(IMAGE_UPLOAD_PATH . "/product_" . $product_id . "/list");
                    }

                    if (count(glob(IMAGE_UPLOAD_PATH . "/product_" . $product_id . "/thumb/*")) === 0) {
                        rmdir(IMAGE_UPLOAD_PATH . "/product_" . $product_id . "/thumb");
                    }

                    if (count(glob(IMAGE_UPLOAD_PATH . "/product_" . $product_id . "/*")) === 0) {
                        rmdir(IMAGE_UPLOAD_PATH . "/product_" . $product_id);
                    }
                }




                $module = $this->getRequest()->getModuleName();
                $controller = $this->getRequest()->getControllerName();
                $this->_redirect($module . "/" . $controller . "/edit/id/" . $product_id);
            }


            if ($article_id) {

                $imgMainPath = IMAGE_UPLOAD_PATH_ARTICLE . "/article_" . $article_id . "/main/" . $photography->getPhotography_path();
                $imgThumbPath = IMAGE_UPLOAD_PATH_ARTICLE . "/article_" . $article_id . "/thumb/" . $photography->getPhotography_path();

                chmod($imgMainPath, 0777);
                chmod($imgThumbPath, 0777);

//smazu soubory, potom smazu z databaze a pokud je prazdny adrear, tak jej taky smazu
                if (unlink($imgMainPath) && unlink($imgThumbPath)) {
                    $this->_photographyMapper->delete($photography_id);

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

                $module = $this->getRequest()->getModuleName();
                $this->_redirect($module . "/article/edit/id/" . $article_id);
            }



            if ($manufacturer_id) {

                $imgPath = IMAGE_UPLOAD_PATH_MANUFACTURER . "/manufacturer_" . $manufacturer_id . "/" . $photography->getPhotography_path();

                chmod($imgPath, 0777);

//smazu soubory, potom smazu z databaze a pokud je prazdny adrear, tak jej taky smazu
                if (unlink($imgPath)) {
                    $this->_photographyMapper->delete($photography_id);

                    //pokud je prazdny adresar, tak ho smazu
                    if (count(glob(IMAGE_UPLOAD_PATH_MANUFACTURER . "/manufacturer_" . $manufacturer_id . "/*")) === 0) {
                        rmdir(IMAGE_UPLOAD_PATH_MANUFACTURER . "/manufacturer_" . $manufacturer_id);
                    }
                }

                $module = $this->getRequest()->getModuleName();
                $this->_redirect($module . "/manufacturer/edit/id/" . $manufacturer_id);
            }
        }
    }

    //pro dany produkt smaze vsechny fotky z DB a celou slozku s obrazky
    public function deletepathAction() {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);

        $product_id = (int) $this->getRequest()->getParam('product_id');

        if ($product_id) {

            $imgPath = IMAGE_UPLOAD_PATH . "/product_" . $product_id . "/";

            chmod($imgPath, 0777);

//smazu adresar, potom smazu z databaze
            $this->rrmdir($imgPath);

            if (!is_dir($imgPath)) {
                $this->_photographyMapper->deletePhotosByProductId($product_id);
            }

            $module = $this->getRequest()->getModuleName();
            $controller = $this->getRequest()->getControllerName();
            $this->_redirect($module . "/" . $controller . "/edit/id/" . $product_id);
        }
    }

    public function setphotographymainAction() {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);

        $photography_id = $this->getRequest()->getParam("id");
        $product_id = $this->getRequest()->getParam("product_id");

        $db = $this->_photographyMapper->getDbTable()->getDefaultAdapter();
        $db->beginTransaction();
        try {

            $this->_photographyMapper->setPhotographyMain($photography_id, $product_id);

            $db->commit();

            $this->_flashMessenger->addMessage(array('info' => 'Hlavní fotografie byla úspěšně změněna.'));
        } catch (Exception $e) {

            $this->_flashMessenger->addMessage(array('error' => 'Při nastavování hlavní fotografie nastala chyba!<br />' . $e->getMessage()));

            //dojde-li k chybe, provedu rollback, data nebudou ulozena do db
            $db->rollBack();
        }

        $module = $this->getRequest()->getModuleName();
        $controller = $this->getRequest()->getControllerName();
        $this->_redirect($module . "/" . $controller . "/edit/id/" . $product_id);
    }

    //smaze slozku(dir) vcetne vsech podslozek a souboru
    private function rrmdir($dir) {
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if (filetype($dir . "/" . $object) == "dir")
                        $this->rrmdir($dir . "/" . $object);
                    else
                        unlink($dir . "/" . $object);
                }
            }
            reset($objects);
            rmdir($dir);
        }
    }

}

