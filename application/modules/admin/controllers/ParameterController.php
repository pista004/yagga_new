<?php

class Admin_ParameterController extends Zend_Controller_Action {

    private $_parameterMapper;
    private $_parameterUnitMapper;
    private $_parameterDialMapper;
    private $_categoryMapper;
    private $_parameterCategoryMapper;
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

        $this->_parameterMapper = new Admin_Model_ParameterMapper();
        $this->_parameterUnitMapper = new Admin_Model_ParameterUnitMapper();
        $this->_parameterDialMapper = new Admin_Model_ParameterDialMapper();
        $this->_categoryMapper = new Admin_Model_CategoryMapper();
        $this->_parameterCategoryMapper = new Admin_Model_ParameterCategoryMapper();
    }

    public function indexAction() {
        $this->_parameterMapper = new Admin_Model_ParameterMapper();
        $parameters = $this->_parameterMapper->getParameters();

        $this->view->parameterResult = $parameters;
        $this->view->paginator = $this->_parameterMapper->_paginator;
    }

    public function addAction() {

        //ziskam jednotky do formulare
        $units = $this->_parameterUnitMapper->getParameterUnits();
        $unitsToForm = array();
        $unitsToForm[0] = '--Vyberte--';
        foreach ($units as $unit) {
            $unitsToForm[$unit->getParameter_unit_id()] = $unit->getParameter_unit_name();
        }

        //ziskam kategorie pro prirazeni parametru ke kategoriim
        $childs = $this->_categoryMapper->getChildsCache(0, true);
        $categoryChildsAry = $this->_categoryMapper->getCategoryChildsAryCache($childs);
        $categoryToForm = array();

        foreach ($categoryChildsAry as $category) {
            $categoryToForm[$category->getCategory_id()] = $category->getCategory_structure();
        }


        $form = new Admin_Form_EditParameterForm();
        $form->setUnits($unitsToForm);
        $form->setCategory($categoryToForm);

//      přidám form element text pro vlozeni hodnoty ciselniku  
        $form->addElement($form->addParameterDialValue(1));

        $form->startForm();


        $this->view->form = $form;


        if ($this->getRequest()->isPost()) {

            $postedData = $this->getRequest()->getPost();

//            zkontroluju, jaky typ parametru je ukladan, podle toho zpracuju posted data
            if ($postedData['parameter_type'] == 1) {
                if (array_key_exists('parameter_dial_value', $postedData)) {
                    unset($postedData['parameter_dial_value']);
                }
            } elseif ($postedData['parameter_type'] == 3) {
                if (array_key_exists('parameter_dial_value', $postedData)) {
                    unset($postedData['parameter_dial_value']);
                }

                if (array_key_exists('parameter_parameter_unit_id', $postedData)) {
                    unset($postedData['parameter_parameter_unit_id']);
                }
            }

            if ($form->isValidPartial($postedData)) {
                $parameter = new Admin_Model_Parameter();
                $parameter->setOptions($postedData);
                if ($parameter->getParameter_parameter_unit_id() < 1) {
                    $parameter->setParameter_parameter_unit_id(null);
                }


                $db = $this->_parameterMapper->getDbTable()->getDefaultAdapter();
                $db->beginTransaction();

                $lastParameterId = 0;
                try {

                    $this->_parameterMapper->save($parameter);

                    $lastParameterId = $this->_parameterMapper->getDbTable()->getDefaultAdapter()->lastInsertId();

//nastavim kategorie pro parametr
                    $parameterCategoryIds = $this->getRequest()->getParam('parameter_category');

                    if (!is_array($parameterCategoryIds)) {
                        $parameterCategoryIds = array();
                    }

                    if (!empty($parameterCategoryIds)) {

                        $bulkParameterCategoryAry = array();
                        foreach ($parameterCategoryIds as $parameterCategoryId) {
                            $bulkParameterCategoryAry[] = array(
                                'category_id' => $parameterCategoryId,
                                'parameter_id' => $lastParameterId
                            );
                        }

                        $this->_parameterCategoryMapper->bulkInsert($bulkParameterCategoryAry);
                    }


//pokud je ukladany parameter ciselnik, tak musim ulozit hodnoty ciselniku do db  
                    $parameterDialValues = array();

                    if (array_key_exists('parameter_dial_value', $postedData)) {
                        foreach ($postedData['parameter_dial_value'] as $parameterDialValue) {
                            if ($parameterDialValue != "") {
                                $parameterDialValues[] = array(
                                    'parameter_dial_value' => $parameterDialValue,
                                    'parameter_dial_parameter_id' => $lastParameterId
                                );
                            }
                        }

                        if (!empty($parameterDialValues)) {
                            $this->_parameterDialMapper->bulkInsert($parameterDialValues);
                        }
                    }

                    $db->commit();
                    $this->_flashMessenger->addMessage(array('info' => 'Parametr byl úspěšně vložen.'));
                } catch (Exception $e) {
                    $this->_flashMessenger->addMessage(array('error' => 'Při ukládání parametru nastala chyba!<br />' . $e->getMessage()));
                    $db->rollBack();
                }

                $module = $this->getRequest()->getModuleName();
                $controller = $this->getRequest()->getControllerName();
                $this->_redirect($module . '/' . $controller);
            } else {
                $postedData = $this->getRequest()->getPost();

                //pokud pri odeslani formulare existuji textboxy parameter_dial_value, tak je musim znovu vytvorit, protoze se pridavaji ajaxem a pri odeslani formulare mizi
                if (array_key_exists('parameter_dial_value', $postedData)) {
                    if (count($postedData['parameter_dial_value']) > 1) {
                        foreach ($postedData['parameter_dial_value'] as $parameterDialKey => $parameterDial) {
                            $parameterDialNum = (int) str_replace('parameter_dial_value_', '', $parameterDialKey);
                            if ($parameterDialNum && $parameterDial != "") {
                                $form->addElement($form->addParameterDialValue($parameterDialNum));
                            }
                        }
                    }
                }

                $form->populate($this->getRequest()->getPost());
            }
        }
    }

    public function editAction() {

        $parameterId = (int) $this->getRequest()->getParam('id');

        if ($parameterId) {
            $parameter = $this->_parameterMapper->find($parameterId);
            if (!empty($parameter)) {

                //ziskam jednotky do formulare
                $units = $this->_parameterUnitMapper->getParameterUnits();
                $unitsToForm = array();
                $unitsToForm[0] = '--Vyberte--';
                foreach ($units as $unit) {
                    $unitsToForm[$unit->getParameter_unit_id()] = $unit->getParameter_unit_name();
                }

//ziskam kategorie do formulare
                $childs = $this->_categoryMapper->getChildsCache(0, true);
                $categoryChildsAry = $this->_categoryMapper->getCategoryChildsAryCache($childs);
                $categoryToForm = array();
                foreach ($categoryChildsAry as $category) {
                    $categoryToForm[$category->getCategory_id()] = $category->getCategory_structure();
                }

//ziskam kategorie, ktere jsou prirazene k danemu parametru
                $parameterCategoriesToPopulate = array();
                $parameterCategories = $this->_parameterCategoryMapper->getByParameterId($parameterId);
                if (!empty($parameterCategories)) {
                    foreach ($parameterCategories as $pc) {
                        $parameterCategoriesToPopulate[] = $pc->getParameter_category_category_id();
                    }
                }

                $form = new Admin_Form_EditParameterForm();
                $form->setCategory($categoryToForm);
                $form->setUnits($unitsToForm);
                $form->startForm();

//              pro parameter_type nastavim disabled, protoze nechci aby se tato hodnota menila pri uprave parametru, 
//              kdyz je ale disabled, tak se neposila pri submit, musim tedy pri zpracovani dat-pri kliku na submit nastavit puvodni hodnotu parametru
                $form->getElement('parameter_type')->setOptions(array('disabled' => '1'));


                //zjistím, jestli je parametr typ 2(ciselnik), pokud ano, tak nactu hodnoty z tabulky parameter_dial - hodnoty ciselniku
                $parameterDials = array();
                $parameterDialsToForm['parameter_dial_value'] = array();
                if ($parameter->getParameter_type() == 2) {
                    $parameterDials = $this->_parameterDialMapper->findByParameterId($parameterId);
                    if (!empty($parameterDials)) {
                        foreach ($parameterDials as $parameterDialId => $parameterDial) {
                            $form->addElement($form->addParameterDialValue($parameterDialId));
                            $parameterDialElement = 'parameter_dial_value_' . $parameterDialId;
                            $parameterDialsToForm['parameter_dial_value'][$parameterDialElement] = $parameterDial->getParameter_dial_value();
                        }
                        $form->populate($parameterDialsToForm);
                    }
                }

                $form->populate($parameter->toArray());


                if (!empty($parameterCategoriesToPopulate)) {
                    $values = array(
                        'parameter_category' => $parameterCategoriesToPopulate
                    );
                    $form->populate($values);
                }

                $this->view->form = $form;


                if ($this->getRequest()->isPost()) {

//                  nastavim parameter_type, protoze pri editaci jsou radio buttony disabled, tak at nejdou menit, takze se neodeslou pres submit
                    $postData = $this->getRequest()->getPost();
                    $postData['parameter_type'] = $parameter->getParameter_type();


                    if ($form->isValid($postData)) {

                        //ziskam aktualni kategorie pro ulozeni
                        $parameterCategoryIds = $this->getRequest()->getParam('parameter_category');

                        if (!is_array($parameterCategoryIds)) {
                            $parameterCategoryIds = array();
                        }

                        //porovnam puvodni kategorie($parameterCategoriesToPopulate) s aktualne odeslanymi($parameterCategoryIds) k ulozeni, ty ktere k ulozeni nejsou, ty smazu
                        //porovnam puvodni kategorie($parameterCategoriesToPopulate) s aktualne odeslanymi($parameterCategoryIds) k ulozeni, ty ktere k ulozeni nejsou, ty smazu
                        $toDelete = array();
                        $toAdd = array();
                        if (is_array($parameterCategoriesToPopulate) && is_array($parameterCategoryIds)) {
                            $toDelete = array_diff($parameterCategoriesToPopulate, $parameterCategoryIds);
                            $toAdd = array_diff($parameterCategoryIds, $parameterCategoriesToPopulate);
                        }

                        $db = $this->_parameterMapper->getDbTable()->getDefaultAdapter();
                        $db->beginTransaction();

                        try {

                            $parameterChanged = false;


                            if ($parameter->getParameter_type() == 2) {
                                if (array_key_exists('parameter_dial_value', $postData)) {
                                    //porovnam odeslane hodnoty ciselniku ($postData['parameter_dial_value']) s puvodnima hodnotama($parameterDialsToForm['parameter_dial_value']) pokud doslo ke zene a jsou prazdne, tak smazu, pokud doslo ke zmene a neco tam je, tak ukladam
                                    $dialsToUpdate = array();
                                    if (array_key_exists('parameter_dial_value', $postData) && array_key_exists('parameter_dial_value', $parameterDialsToForm)) {
                                        if (is_array($postData['parameter_dial_value']) && is_array($parameterDialsToForm['parameter_dial_value'])) {
                                            $dialsToUpdate = array_diff_assoc($postData['parameter_dial_value'], $parameterDialsToForm['parameter_dial_value']);

                                            foreach ($dialsToUpdate as $dialToUpdateKey => $dialToUpdate) {
// pokud existuje dialToUpdateKey v poli parameterDoalsToForm, tak dial uz existuje, provedu jen aktualizaci, pokud neexistuje, tak jej pridaim

                                                if (array_key_exists($dialToUpdateKey, $parameterDialsToForm['parameter_dial_value'])) {
                                                    $dialId = (int) str_replace('parameter_dial_value_', '', $dialToUpdateKey);

                                                    if ($dialToUpdate == "") {
                                                        $this->_parameterDialMapper->delete($dialId);
                                                        $parameterChanged = true;
                                                    } else {
                                                        $parameterDialObj = new Admin_Model_ParameterDial();
                                                        $parameterDialObj->setParameter_dial_id($dialId);
                                                        $parameterDialObj->setParameter_dial_value($dialToUpdate);
                                                        $parameterDialObj->setParameter_dial_parameter_id($parameterId);

                                                        $this->_parameterDialMapper->save($parameterDialObj);
                                                        $parameterChanged = true;
                                                    }
                                                } else {

                                                    if ($dialToUpdate != "") {
                                                        $parameterDialObj = new Admin_Model_ParameterDial();
                                                        $parameterDialObj->setParameter_dial_value($dialToUpdate);
                                                        $parameterDialObj->setParameter_dial_parameter_id($parameterId);

                                                        $this->_parameterDialMapper->save($parameterDialObj);
                                                        $parameterChanged = true;
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }


                            if (!empty($toDelete)) {
                                foreach ($toDelete as $deleteParameterCategoryId) {
                                    $this->_parameterCategoryMapper->deleteByCategoryIdParameterId($deleteParameterCategoryId, $parameterId);
                                }
                                $parameterChanged = true;
                            }

                            if (!empty($toAdd)) {
                                $bulkParameterCategoryAry = array();
                                foreach ($toAdd as $parameterCategoryId) {
                                    $bulkParameterCategoryAry[] = array(
                                        'category_id' => $parameterCategoryId,
                                        'parameter_id' => $parameterId
                                    );
                                }

                                $this->_parameterCategoryMapper->bulkInsert($bulkParameterCategoryAry);
                                $parameterChanged = true;
                            }

                            //pro porovnani zmen u parametru odeberu parameter_category, protoze to se resi vyse
                            $parameterFormValues = $form->getValues();
                            unset($parameterFormValues['parameter_category']);

                            //zkontroluju ke kolika zmenam doslo, pokud byly nejake polozky zmeneny, tak provedu update
                            $changedValues = array_diff_assoc($parameterFormValues, $parameter->toArray());

                            if ((count($changedValues) > 0)) {

                                $parameterEdit = new Admin_Model_Parameter();
                                $parameterEdit->setOptions($form->getValues());
// pokud je jednotka mensi nez 1, nastavim na null  
                                if ($parameterEdit->getParameter_parameter_unit_id() < 1) {
                                    $parameterEdit->setParameter_parameter_unit_id(null);
                                }
                                $parameterEdit->setParameter_id($parameterId);

                                $this->_parameterMapper->save($parameterEdit);

                                $parameterChanged = true;
                            }

                            $db->commit();

                            if ($parameterChanged) {
                                $this->_flashMessenger->addMessage(array('info' => 'Parametr byl úspěšně upraven.'));
                            } else {
                                $this->_flashMessenger->addMessage(array('info' => 'Nebyla provedena žádná změna.'));
                            }
                        } catch (Exception $e) {
                            $this->_flashMessenger->addMessage(array('error' => 'Při ukládání parametru nastala chyba!<br />' . $e->getMessage()));
                            $db->rollBack();
                        }

                        $module = $this->getRequest()->getModuleName();
                        $controller = $this->getRequest()->getControllerName();
                        $this->_redirect($module . '/' . $controller);
                    } else {

                        $postedData = $this->getRequest()->getPost();

                        //pokud pri odeslani formulare existuji textboxy parameter_dial_value, tak je musim znovu vytvorit, protoze se pridavaji ajaxem a pri odeslani formulare mizi
                        if (array_key_exists('parameter_dial_value', $postedData)) {
                            if (count($postedData['parameter_dial_value']) > 1) {
                                foreach ($postedData['parameter_dial_value'] as $parameterDialKey => $parameterDial) {
                                    $parameterDialNum = (int) str_replace('parameter_dial_value_', '', $parameterDialKey);
                                    if ($parameterDialNum && $parameterDial != "") {
                                        $form->addElement($form->addParameterDialValue($parameterDialNum));
                                    }
                                }
                            }
                        }

                        $form->populate($postedData);
                    }
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
                $this->_parameterMapper->delete($id);
                $this->_flashMessenger->addMessage(array('info' => 'Parametr byl úspěšně smazán.'));
            } catch (Exception $e) {
                $this->_flashMessenger->addMessage(array('error' => 'Při pokusu o smazání došlo k chybě!<br />' . $e->getMessage()));
            }
            $module = $this->getRequest()->getModuleName();
            $controller = $this->getRequest()->getControllerName();
            $this->_redirect($module . '/' . $controller);
        }
    }

}

