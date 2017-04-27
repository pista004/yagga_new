<?php

class Admin_ManufacturerController extends Zend_Controller_Action {

    private $_flashMessenger;
    private $_manufacturerMapper;
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

        $this->_manufacturerMapper = new Admin_Model_ManufacturerMapper();
        $this->_photographyMapper = new Admin_Model_PhotographyMapper();
    }

    public function indexAction() {
        
        $page = 0;
        if ($this->getRequest()->getParam('page')) {
            $page = $this->getRequest()->getParam('page');
        }
        
        $manufacturerResult = $this->_manufacturerMapper->getManufacturers($page, 20);
        $this->view->manufacturerResult = $manufacturerResult;
        
        $this->view->paginator = $this->_manufacturerMapper->_paginator;
    }

    public function editAction() {

        $id = $this->getRequest()->getParam('id');
        $manufacturerById = $this->_manufacturerMapper->getManufacturerById($id);

        $this->view->manufacturer = $manufacturerById;

//print_r($manufacturerById);die;
        if (!empty($manufacturerById)) {

            $form = new Admin_Form_EditManufacturerForm();
            $form->setUrl($manufacturerById->getManufacturer_url());
            $form->startForm();
            $form->populate($manufacturerById->toArray());

            $this->view->form = $form;

            if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
                $manufacturer = new Admin_Model_Manufacturer();
                $manufacturer->setOptions($form->getValues());
                $manufacturer->setManufacturer_id($id);


                if (($manufacturerById->getManufacturer_url() != $manufacturer->getManufacturer_url()) && ($manufacturer->getManufacturer_url() != "")) {
                    $toUrl = $manufacturer->getManufacturer_url();
                    $filterUrl = new Filter_Url();
                    $url = $filterUrl->filter($toUrl);

                    $filteredUrl = $filterUrl->checkManufacturerUrl($url);
                    $manufacturer->setManufacturer_url($filteredUrl);
                } else {
                    $manufacturer->setManufacturer_url($manufacturerById->getManufacturer_url());
                }

                $db = $this->_manufacturerMapper->getDbTable()->getDefaultAdapter();
                $db->beginTransaction();
                try {
                    $this->_manufacturerMapper->save($manufacturer);

                    $upload = new Zend_File_Transfer_Adapter_Http();

                    if ($upload->isUploaded()) {

                        $manufacturerIdPath = "manufacturer_" . $id;

                        $formatDirManufacturer = "%s/%s";
                        $dirManufacturer = sprintf($formatDirManufacturer, IMAGE_UPLOAD_PATH_MANUFACTURER, $manufacturerIdPath);

                        //ukladam fotky do slozek podle id produktu, pokud slozka neexistuje, tak ji vytvorim
                        if (!is_dir($dirManufacturer)) {
                            mkdir($dirManufacturer);
                        }

                        $baseName = new Zend_Filter_BaseName();
                        $fileName = $baseName->filter($upload->getFileName());

                        $filter = new Zend_Filter_Word_SeparatorToDash($searchSeparator = ' ');
                        $img = $filter->filter(time() . "_" . $fileName);

                        $img_path = $dirManufacturer . "/" . $img;

                        $resize = new Filter_File_Resize_Adapter_Gd();
                        $resize->resize(80, 80, true, $upload->getFileName(), $img_path);

                        $photography = new Admin_Model_Photography();
                        $photography->setPhotography_path($img);
                        $photography->setPhotography_manufacturer_id($id);

                        $this->_photographyMapper->save($photography);
                    }

                    $db->commit();

                    $this->_flashMessenger->addMessage(array('info' => 'Výrobce byl úspěšně upraven.'));
                } catch (Exception $e) {

                    $this->_flashMessenger->addMessage(array('error' => 'Při ukládání nastala chyba!<br />' . $e->getMessage()));
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

    public function addAction() {
        $form = new Admin_Form_EditManufacturerForm();
        $form->startForm();

        $this->view->form = $form;

        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
            $manufacturer = new Admin_Model_Manufacturer();
            $manufacturer->setOptions($form->getValues());

            // pridani url, kontrola existence atd  
            if ($manufacturer->getManufacturer_url()) {
                $toUrl = $manufacturer->getManufacturer_url();
                $filterUrl = new Filter_Url();
                $url = $filterUrl->filter($toUrl);

                $filteredUrl = $filterUrl->checkManufacturerUrl($url);
                $manufacturer->setManufacturer_url($filteredUrl);
            } else {
                $toUrl = $manufacturer->getManufacturer_name();
                $filterUrl = new Filter_Url();
                $url = $filterUrl->filter($toUrl);

                $filteredUrl = $filterUrl->checkManufacturerUrl($url);
                $manufacturer->setManufacturer_url($filteredUrl);
            }

            $db = $this->_manufacturerMapper->getDbTable()->getDefaultAdapter();
            $db->beginTransaction();
            try {
                $this->_manufacturerMapper->save($manufacturer);

                $lastManufacturerId = $this->_manufacturerMapper->getDbTable()->getDefaultAdapter()->lastInsertId();

                $upload = new Zend_File_Transfer_Adapter_Http();

                if ($upload->isUploaded()) {

                    $manufacturerIdPath = "manufacturer_" . $lastManufacturerId;

                    $formatDirManufacturer = "%s/%s";
                    $dirManufacturer = sprintf($formatDirManufacturer, IMAGE_UPLOAD_PATH_MANUFACTURER, $manufacturerIdPath);

                    //ukladam fotky do slozek podle id produktu, pokud slozka neexistuje, tak ji vytvorim
                    if (!is_dir($dirManufacturer)) {
                        mkdir($dirManufacturer);
                    }

                    $baseName = new Zend_Filter_BaseName();
                    $fileName = $baseName->filter($upload->getFileName());

                    $filter = new Zend_Filter_Word_SeparatorToDash($searchSeparator = ' ');
                    $img = $filter->filter(time() . "_" . $fileName);

                    $img_path = $dirManufacturer . "/" . $img;

                    $resize = new Filter_File_Resize_Adapter_Gd();
                    $resize->resize(80, 80, true, $upload->getFileName(), $img_path);
//                    $resize->resize(930, 465, true, $upload->getFileName(), $img_main_path);

                    $photography = new Admin_Model_Photography();
                    $photography->setPhotography_path($img);
                    $photography->setPhotography_manufacturer_id($lastManufacturerId);


                    $this->_photographyMapper->save($photography);
                }

                $db->commit();

                $this->_flashMessenger->addMessage(array('info' => 'Výrobce byl úspěšně vložen.'));
            } catch (Exception $e) {

                $this->_flashMessenger->addMessage(array('error' => 'Při ukládání výrobce nastala chyba!<br />' . $e->getMessage()));
                $db->rollBack();
            }

            $module = $this->getRequest()->getModuleName();
            $controller = $this->getRequest()->getControllerName();
            $this->_redirect($module . '/' . $controller);
        } else {
            $form->populate($form->getValues());
        }
    }

    public function deleteAction() {
        
        $manufacturer_id = (int) $this->getRequest()->getParam('id');

        $photography = $this->_photographyMapper->getPhotoByManufacturerId($manufacturer_id);

//        print_r($photography);die;
        
        $db = $this->_manufacturerMapper->getDbTable()->getDefaultAdapter();
        $db->beginTransaction();
        try {

            if ($manufacturer_id && !empty($photography)) {

                $imgPath = IMAGE_UPLOAD_PATH_MANUFACTURER . "/manufacturer_" . $manufacturer_id . "/" . $photography->getPhotography_path();

                chmod($imgPath, 0777);

//smazu soubory, potom smazu z databaze a pokud je prazdny adrear, tak jej taky smazu
                if (unlink($imgPath)) {
                    $this->_photographyMapper->delete((int) $photography->getPhotography_id());

                    //pokud je prazdny adresar, tak ho smazu

                    if (count(glob(IMAGE_UPLOAD_PATH_MANUFACTURER . "/manufacturer_" . $manufacturer_id . "/*")) === 0) {
                        rmdir(IMAGE_UPLOAD_PATH_MANUFACTURER . "/manufacturer_" . $manufacturer_id);
                    }
                }
            }


            $this->_manufacturerMapper->delete($manufacturer_id);
            $this->_flashMessenger->addMessage(array('info' => 'Výrobce byl úspěšně smazán.'));
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

