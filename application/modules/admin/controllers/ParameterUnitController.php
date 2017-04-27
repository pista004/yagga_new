<?php

class Admin_ParameterunitController extends Zend_Controller_Action {

    private $_parameterUnitMapper;
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

        $this->_parameterUnitMapper = new Admin_Model_ParameterUnitMapper();
    }

    public function indexAction() {
        $this->_parameterUnitMapper = new Admin_Model_ParameterUnitMapper();
        $parameterUnits = $this->_parameterUnitMapper->getParameterUnits();

        $this->view->parameterUnitsResult = $parameterUnits;
        $this->view->paginator = $this->_parameterUnitMapper->_paginator;
    }

    public function addAction() {

        $form = new Admin_Form_EditParameterUnitForm();
        $form->startForm();
        $this->view->form = $form;

        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {

            $parameterUnit = new Admin_Model_ParameterUnit();
            $parameterUnit->setOptions($form->getValues());

            $db = $this->_parameterUnitMapper->getDbTable()->getDefaultAdapter();
            $db->beginTransaction();

            try {

                $this->_parameterUnitMapper->save($parameterUnit);

                $db->commit();
                $this->_flashMessenger->addMessage(array('info' => 'Jednotka byl úspěšně vložena.'));
            } catch (Exception $e) {
                $this->_flashMessenger->addMessage(array('error' => 'Při ukládání jednotky nastala chyba!<br />' . $e->getMessage()));
                $db->rollBack();
            }

            $module = $this->getRequest()->getModuleName();
            $controller = $this->getRequest()->getControllerName();
            $this->_redirect($module . '/' . $controller);
        } else {
            $form->populate($form->getValues());
        }
    }

    public function editAction() {
        $parameterUnitId = (int) $this->getRequest()->getParam('id');

        if ($parameterUnitId) {
            $parameterUnit = $this->_parameterUnitMapper->find($parameterUnitId);

            if (!empty($parameterUnit)) {

                $form = new Admin_Form_EditParameterUnitForm();

                $form->startForm();
                $form->populate($parameterUnit->toArray());
                $this->view->form = $form;


                if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {

                    //zkontroluju ke kolika zmenam doslo, pokud byly nejake polozky zmeneny, tak provedu update
                    $changedValues = array_diff_assoc($form->getValues(), $parameterUnit->toArray());
                    if (count($changedValues) > 0) {

                        $parameterUnitEdit = new Admin_Model_ParameterUnit();
                        $parameterUnitEdit->setOptions($form->getValues());

                        $parameterUnitEdit->setParameter_unit_id($parameterUnitId);

                        $db = $this->_parameterUnitMapper->getDbTable()->getDefaultAdapter();
                        $db->beginTransaction();

                        try {
                            $this->_parameterUnitMapper->save($parameterUnitEdit);

                            $db->commit();
                            $this->_flashMessenger->addMessage(array('info' => 'Jednotka byla úspěšně upravena.'));
                        } catch (Exception $e) {
                            $this->_flashMessenger->addMessage(array('error' => 'Při ukládání jednotky nastala chyba!<br />' . $e->getMessage()));
                            $db->rollBack();
                        }
                    } else {
                        $this->_flashMessenger->addMessage(array('info' => 'Nebyla provedena žádná změna.'));
                    }

                    $module = $this->getRequest()->getModuleName();
                    $controller = $this->getRequest()->getControllerName();
                    $this->_redirect($module . '/' . $controller);
                } else {
                    $form->populate($form->getValues());
                }
            } else {
                $module = $this->getRequest()->getModuleName();
                $controller = $this->getRequest()->getControllerName();
                $this->_redirect($module . '/' . $controller);
            }
        } else {
            $module = $this->getRequest()->getModuleName();
            $controller = $this->getRequest()->getControllerName();
            $this->_redirect($module . '/' . $controller);
        }
    }

    public function deleteAction() {
        $id = (int) $this->getRequest()->getParam('id');

        if ($id) {

            try {
                $this->_parameterUnitMapper->delete($id);
                $this->_flashMessenger->addMessage(array('info' => 'Jednotka byla úspěšně smazána.'));
            } catch (Exception $e) {
                $this->_flashMessenger->addMessage(array('error' => 'Při pokusu o smazání došlo k chybě!<br />' . $e->getMessage()));
            }
            $module = $this->getRequest()->getModuleName();
            $controller = $this->getRequest()->getControllerName();
            $this->_redirect($module . '/' . $controller);
        }
    }

}

