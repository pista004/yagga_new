<?php

class Admin_ManufactureraffiliateController extends Zend_Controller_Action {

    private $_flashMessenger;
    private $_manufacturerAffiliateManufacturerMapper;
    private $_manufacturerMapper;

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

        $this->_manufacturerAffiliateManufacturerMapper = new Admin_Model_ManufacturerAffiliateManufacturerMapper();
        $this->_manufacturerMapper = new Admin_Model_ManufacturerMapper();
    }

    public function indexAction() {

        $manufacturerAffiliateResult = $this->_manufacturerAffiliateManufacturerMapper->getManufacturers(0, -1, array('order' => 'mam.manufacturer_affiliate_manufacturer_id DESC'));
        $manufacturerResult = $this->_manufacturerMapper->getManufacturers();


        foreach ($manufacturerAffiliateResult as $manufacturerAffiliate) {
            $manufacturerID = $manufacturerAffiliate->getManufacturer_affiliate_manufacturer_manufacturer_id();
            if (isset($manufacturerID) && array_key_exists($manufacturerID, $manufacturerResult)) {
                $manufacturerAffiliate->setManufacturer($manufacturerResult[$manufacturerID]);
            }
        }

        $this->view->manufacturerAffiliateResult = $manufacturerAffiliateResult;
    }

    public function editAction() {

        $id = $this->getRequest()->getParam('id');
        $manufacturerAffiliateById = $this->_manufacturerAffiliateManufacturerMapper->getManufacturerAffiliateById($id);

//        print_r($manufacturerAffiliateById);die;

        if ($manufacturerAffiliateById) {

            $manufacturerResult = $this->_manufacturerMapper->getManufacturers();

            $manufacturerToForm = array(0 => 'Nespárováno');
            foreach ($manufacturerResult as $manufacturerId => $manufacturer) {
                $manufacturerToForm[$manufacturerId] = $manufacturer->getManufacturer_name();
            }

            $form = new Admin_Form_EditManufacturerAffiliateForm();
            $form->setManufacturers($manufacturerToForm);
            $form->startForm();
            if ($manufacturerAffiliateById->getManufacturer_affiliate_manufacturer_manufacturer_id()) {
                $form->populate(array('manufacturer_id' => $manufacturerAffiliateById->getManufacturer_affiliate_manufacturer_manufacturer_id()));
            }
            $this->view->form = $form;

            $this->view->manufacturerAffiliateById = $manufacturerAffiliateById;


            if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
                $manufacturerAffiliate = new Admin_Model_ManufacturerAffiliateManufacturer();
                $manufacturerAffiliate->setManufacturer_affiliate_manufacturer_id($id);

                if ($form->getValue('manufacturer_id') == 0) {
                    $manufacturerAffiliate->setManufacturer_affiliate_manufacturer_manufacturer_id(new Zend_Db_Expr('NULL'));
                } else {
                    $manufacturerAffiliate->setManufacturer_affiliate_manufacturer_manufacturer_id($form->getValue('manufacturer_id'));
                }


                $db = $this->_manufacturerAffiliateManufacturerMapper->getDbTable()->getDefaultAdapter();
                $db->beginTransaction();
                try {
                    $this->_manufacturerAffiliateManufacturerMapper->save($manufacturerAffiliate);


                    $db->commit();

                    $this->_flashMessenger->addMessage(array('info' => 'Výrobce byl úspěšně spárován.'));
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

}

