<?php

class Admin_AjaxController extends Zend_Controller_Action {

    private $_variantMapper;
    private $_productMapper;
    private $_photographyMapper;
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

        $this->_variantMapper = new Admin_Model_VariantMapper();
        $this->_productMapper = new Admin_Model_ProductMapper();
        $this->_photographyMapper = new Admin_Model_PhotographyMapper();
    }

    public function indexAction() {
        
    }

    //funkce je volana javascriptem - ajaxove vytvoreni prvku formulare - slozi pro pridani varianty
    public function editvariantAction() {
        $this->_helper->layout()->disableLayout();

        $product_id = (int) $this->getRequest()->getParam('product_id');

        $form_variant = new Admin_Form_EditVariantForm();
        $form_variant->startForm();
        //zmenim id submit button kvuli ajax funkci
        $form_variant->getElement('submit')->setAttrib('id', 'edit-variant-ajax');

        $this->view->form_variant = $form_variant;

        $variantData = array();
        //dostanu data z jquery serialize do pole $variantData
        parse_str($this->getRequest()->getParam('data'), $variantData);

        if ($this->getRequest()->isPost() && (int) $product_id > 0) {
            if ($form_variant->isValid($variantData)) {

                $db = $this->_variantMapper->getDbTable()->getDefaultAdapter();
                $db->beginTransaction();
                try {
                    $variant = new Admin_Model_Variant();
                    $variant->setOptions($variantData);
                    $variant->setVariant_product_id($product_id);

                    $this->_variantMapper->save($variant);


                    //ziskam vsechny varianty, jejich sumu a ulozim jako product_stock
                    $this->_productMapper->setProductStockFromVariantStocksByProductId($product_id);

                    $this->_flashMessenger->addMessage(array('info' => 'Varianta byla úspěšně vložena.'));
                    $db->commit();
//              zrusim view a poslu jen indormaci o tom, ze vse probehlo v poradku - data ulozena atd - pak v jquery zavru modal
                    $this->_helper->viewRenderer->setNoRender(true);

                    echo true;
                } catch (Exception $e) {
                    $this->_flashMessenger->addMessage(array('error' => 'Při ukládání varianty nastala chyba!<br />' . $e->getMessage()));

                    //dojde-li k chybe, provedu rollback, data nebudou ulozena do db
                    $db->rollBack();
                }
            } else {
                $form_variant->populate($form_variant->getValues());
            }
        }
    }

    //nahrani obrazku k produktu
    public function imageuploadAction() {
        $this->_helper->layout()->disableLayout();

        $product_id = (int) $this->getRequest()->getParam("product_id");
        $photography_note = $this->getRequest()->getParam("photography_note");



        if ($product_id) {

            $upload = new Zend_File_Transfer_Adapter_Http();

            if ($upload->isUploaded()) {

                $db = $this->_photographyMapper->getDbTable()->getDefaultAdapter();
                $db->beginTransaction();
                //pokud se nekde vyskytne chyba, provedu rollback a data nebudou ulozena, pokud vse probehne dobre, provedu commit
                try {

                    $productId = "product_" . $product_id;

                    $formatDirProduct = "%s/%s";
                    $dirProduct = sprintf($formatDirProduct, IMAGE_UPLOAD_PATH, $productId);


                    $formatPath = '%s/%s/%s';
                    $dirOriginal = sprintf($formatPath, IMAGE_UPLOAD_PATH, $productId, "original");
                    $dirList = sprintf($formatPath, IMAGE_UPLOAD_PATH, $productId, "list");
                    $dirThumb = sprintf($formatPath, IMAGE_UPLOAD_PATH, $productId, "thumb");


                    //ukladam fotky do slozek podle id produktu, pokud slozka neexistuje, tak ji vytvorim
                    if (!is_dir($dirProduct)) {
                        mkdir($dirProduct);
                    }

                    if (!is_dir($dirOriginal)) {
                        mkdir($dirOriginal);
                    }

                    if (!is_dir($dirList)) {
                        mkdir($dirList);
                    }

                    if (!is_dir($dirThumb)) {
                        mkdir($dirThumb);
                    }


                    $upload->setDestination($dirOriginal);

                    $baseName = new Zend_Filter_BaseName();
                    $fileName = $baseName->filter($upload->getFileName());

                    $filter = new Zend_Filter_Word_SeparatorToDash($searchSeparator = ' ');
                    $img = $filter->filter(time() . "_" . $fileName);


                    $img_path = $upload->getDestination() . "/" . $img;

                    $upload->addFilter('Rename', $img_path);

                    $upload->receive();

                    $img_thumb_path = $dirThumb . "/" . $img;
                    $img_list_path = $dirList . "/" . $img;

                    $resize = new Filter_File_Resize_Adapter_Gd();
                    $resize->resize(100, 100, true, $upload->getFileName(), $img_thumb_path);
                    $resize->resize(350, 300, true, $upload->getFileName(), $img_list_path);

                    $photography = new Admin_Model_Photography();
                    $photography->setPhotography_path($img);
                    $photography->setPhotography_product_id($product_id);
                    $photography->setPhotography_note($photography_note);

                    $this->_photographyMapper->save($photography);
                    $lastPhotographyId = $this->_photographyMapper->getDbTable()->getDefaultAdapter()->lastInsertId();

                    //kontrola, jestli uz je u produktu nastavena hlavni fotka, pokud ne, tak nastavim
                    $photographyIsMain = $this->_photographyMapper->checkPhotographyIsMain($product_id);
                    if ($photographyIsMain <= 0) {
                        $this->_photographyMapper->setPhotographyMain($lastPhotographyId, $product_id);
                    }

                    $this->view->product_id = $product_id;

                    $db->commit();

                    $this->_helper->viewRenderer->setNoRender(true);
                    echo true;
                    $this->_flashMessenger->addMessage(array('info' => 'Fotografie byla úspěšně vložena.'));
                } catch (Exception $e) {

//                    $this->_flashMessenger->addMessage(array('error' => 'Při ukládání varianty nastala chyba!<br />' . $e->getMessage()));
                    //dojde-li k chybe, provedu rollback, data nebudou ulozena do db
                    $db->rollBack();
                }

//                $photos = $this->_photographyMapper->getPhotosByProductId($product_id);
//
//
//                $form = new Admin_Form_ImageUploadForm();
//
//                foreach ($photos as $photo) {
//                    $form->addPhotographySubForm($photo->getPhotography_id(), $photo->toArray());
//                }
//                $form->startForm();
//
//                $this->view->form_photography = $form;
            } else {
                $this->_helper->viewRenderer->setNoRender(TRUE);
            }
        }
    }

    //smazani obrazku u produktu
    public function imagedeleteAction() {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);

        $photography_id = $this->getRequest()->getParam('photography_id');

        $this->_photographyMapper->delete($photography_id);

        /*
         * TODO dodelat mazani fotek z adresaru
         * nedelat pres ajax, udelat normalne s redirectem zpet na fotogalerie
         * pri mazani staci id, podle id fotografie vybrat z db, tak zjistim path atd, smazu z adresare a potom smazu z db
         */

//        if (is_array($image)) {
//            foreach ($image as $img) {
//                unlink(PUBLIC_PATH . '/images/upload/' . $img);
//                unlink(PUBLIC_PATH . '/images/upload/thumb/' . $img);
//            }
//        } else {
//            unlink(PUBLIC_PATH . '/images/upload/' . $image);
//            unlink(PUBLIC_PATH . '/images/upload/thumb/' . $image);
//        }
    }

    public function addorderitemvariantAction() {
        $this->_helper->layout()->disableLayout();
//        $this->_helper->viewRenderer->setNoRender(TRUE);

        $product_id = $this->getRequest()->getParam('product_id');


        $variants = $this->_variantMapper->getVariantsByProductId($product_id);

        $variantsToForm = array();
        $variantsToForm[0] = '--Vyberte--';
        foreach ($variants as $variant) {
            $variantsToForm[$variant->getVariant_id()] = $variant->getVariant_name();
        }

        if (!empty($variants)) {
            $form = new Admin_Form_AddOrderItemForm();
            $form->setVariants($variantsToForm);
            $form->startForm();

            $this->view->form = $form;
        } else {
            $this->_helper->viewRenderer->setNoRender(TRUE);
        }
    }

    public function addparameterdialvalueAction() {
        $this->_helper->layout()->disableLayout();
//        $this->_helper->viewRenderer->setNoRender(TRUE);

        $parameterDialNum = $this->getRequest()->getParam('parameterDialNum');
        if ($parameterDialNum) {
//TODO dodelat, at se prenasi cislo, ktere bude reprezentovat hodnotu ciselniku + kontrolovat at jsou vyplnene predchozi textboxy, at nejde vytvaret textboxy do nekonecna
            $form = new Admin_Form_EditParameterForm();

//      přidám form element text pro vlozeni hodnoty ciselniku  
            $this->view->parameterDialValue = $form->addParameterDialValue($parameterDialNum);
        }
    }

}
