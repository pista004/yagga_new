<?php

class Admin_ProductparameterController extends Zend_Controller_Action {

    private $_parameterMapper;
    private $_parameterUnitMapper;
    private $_parameterDialMapper;
    private $_categoryMapper;
    private $_parameterCategoryMapper;
    private $_productParameterValueMapper;
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

        $this->_parameterMapper = new Admin_Model_ParameterMapper();
        $this->_parameterUnitMapper = new Admin_Model_ParameterUnitMapper();
        $this->_parameterDialMapper = new Admin_Model_ParameterDialMapper();
        $this->_categoryMapper = new Admin_Model_CategoryMapper();
        $this->_parameterCategoryMapper = new Admin_Model_ParameterCategoryMapper();
        $this->_productParameterValueMapper = new Admin_Model_ProductParameterValueMapper();
        $this->_productMapper = new Admin_Model_ProductMapper();
    }

    public function editAction() {

        $productId = (int) $this->getRequest()->getParam('id');

        if ($productId) {

            $productMap = $this->_productMapper->find($productId);

            $this->view->product = $productMap;

// false if product has no parameters, true if product has parameters            
            $isParameter = false;

// if is posible to update parameters, because if variant has no parameters, we show main product parameters, if user save variant parameters, it have to be insert            
            $isUpdatePosible = false;

            $isMainProduct = false;

            $parameters = array();

// check if is variant and select parameters for variant, if variant has no parameters, then select parameters of parent product            
            //is variant
            if ($productMap->getProduct_itemgroup_product_id()) {
                $parameters = $this->_parameterMapper->getVariantParametersValuesByProductId($productId);

                foreach ($parameters as $parameter) {

                    if ($parameter->getProduct_parameter_value()->getProduct_parameter_value_product_id()) {
                        $isUpdatePosible = true;
                        break;
                    };
                }

//                if ($isUpdatePosible == false) {
//                    $this->view->variantParametersInfo = "Tato varianta nemá nastavené parametry. Zobrazují se parametry hlavního produktu.";
//                    $parameters = $this->_parameterMapper->getProductParametersValuesByProductId($productMap->getProduct_itemgroup_product_id());
//                }
            } else {
                //no variant
                //main product
                if ($productMap->getVariants_count() > 0) {
                    $isMainProduct = true;
                } else {
                    $parameters = $this->_parameterMapper->getProductParametersValuesByProductId($productId);
                    $isUpdatePosible = true;
                }
            }



            if (!empty($parameters)) {

                $isParameter = true;

                $form = new Admin_Form_EditProductParameterForm();

                $parametersToPopulate = array();

                $productParameterElementNames = array(
                    1 => 'product_parameter_value_value',
                    2 => 'product_parameter_value_parameter_dial_id',
                    3 => 'product_parameter_value_value_bool'
                );


                foreach ($parameters as $parameter) {


                    $multiOptions = array();
                    $name = '';
                    $belongsTo = '';

                    switch ($parameter->getParameter_type()) {
                        case 1:

                            $belongsTo = $productParameterElementNames[$parameter->getParameter_type()];
                            $name = $belongsTo . '_' . $parameter->getParameter_id();

                            if ($parameter->getProduct_parameter_value()->getProduct_parameter_value_product_id()) {
                                $parametersToPopulate[$belongsTo][$name] = $parameter->getProduct_parameter_value()->getProduct_parameter_value_value();
                            }
                            break;
                        case 2:

                            $belongsTo = $productParameterElementNames[$parameter->getParameter_type()];
                            $name = $belongsTo . '_' . $parameter->getParameter_id();

                            if ($parameter->getProduct_parameter_value()->getProduct_parameter_value_product_id()) {

                                $parametersToPopulate[$belongsTo][$name] = $parameter->getProduct_parameter_value()->getProduct_parameter_value_parameter_dial_id();
                            }

                            $parameterDials = $this->_parameterDialMapper->findByParameterId($parameter->getParameter_id());

                            $multiOptions[null] = '--Vyberte--';
                            foreach ($parameterDials as $parameterDial) {
                                $multiOptions[$parameterDial->getParameter_dial_id()] = $parameterDial->getParameter_dial_value();
                            }

                            break;
                        case 3:

                            $belongsTo = $productParameterElementNames[$parameter->getParameter_type()];
                            $name = $belongsTo . '_' . $parameter->getParameter_id();

                            if ($parameter->getProduct_parameter_value()->getProduct_parameter_value_product_id()) {

                                $parametersToPopulate[$belongsTo][$name] = $parameter->getProduct_parameter_value()->getProduct_parameter_value_value_bool();
                            }

                            $multiOptions = Admin_Model_ParameterType::VALUE_BOOL;
                            break;

                        default:
                            break;
                    }

                    $form->addElement($form->addParameter($parameter->getParameter_type(), $name, $parameter->getParameter_name(), $belongsTo, $multiOptions, array('class' => 'form-control')));
                }

                $form->startForm();

                $form->populate($parametersToPopulate);

                $this->view->form = $form;

                if ($this->getRequest()->isPost()) {

                    $paramsToDb = array();

                    $postData = $this->getRequest()->getPost();
                    unset($postData['submit']);

                    foreach ($postData as $postDataParameterKey => $postDataParameterValues) {

                        foreach ($postDataParameterValues as $postDataParameterValueKey => $postDataParameterValue) {

                            if ($postDataParameterValue == "") {
                                $postDataParameterValue = NULL;
                            }

                            if (array_key_exists($postDataParameterKey, $parametersToPopulate) && $isUpdatePosible == true) {

                                if (array_key_exists($postDataParameterValueKey, $parametersToPopulate[$postDataParameterKey])) {

                                    if ($postDataParameterValue != $parametersToPopulate[$postDataParameterKey][$postDataParameterValueKey]) {
                                        $paramsToDb['update'][$postDataParameterKey][$postDataParameterValueKey] = $postDataParameterValue;
                                    }
                                } else {
                                    $paramsToDb['add'][$postDataParameterKey][$postDataParameterValueKey] = $postDataParameterValue;
                                }
                            } else {
                                $paramsToDb['add'][$postDataParameterKey][$postDataParameterValueKey] = $postDataParameterValue;
                            }
                        }
                    }


                    $db = $this->_productParameterValueMapper->getDbTable()->getDefaultAdapter();
                    $db->beginTransaction();

                    try {

                        foreach ($paramsToDb as $paramsAction => $params) {
                            foreach ($params as $paramToDbKey => $paramToDb) {
                                foreach ($paramToDb as $paramKey => $paramValue) {

//ziskam z parametru ID parametru
                                    $paramId = (int) substr($paramKey, (strrpos($paramKey, '_') + 1));

                                    $productParameterValue = new Admin_Model_ProductParameterValue();
                                    $productParameterValue->setProduct_parameter_value_product_id($productId);
                                    $productParameterValue->setProduct_parameter_value_parameter_id($paramId);

                                    switch ($paramToDbKey) {
                                        case 'product_parameter_value_value':
                                            $productParameterValue->setProduct_parameter_value_value($paramValue);
                                            break;

                                        case 'product_parameter_value_parameter_dial_id':
                                            $productParameterValue->setProduct_parameter_value_parameter_dial_id($paramValue);
                                            break;

                                        case 'product_parameter_value_value_bool':
                                            $productParameterValue->setProduct_parameter_value_value_bool($paramValue);
                                            break;
                                        default:
                                            break;
                                    }

                                    if ($paramsAction == 'add') {

                                        $this->_productParameterValueMapper->insert($productParameterValue);
                                    } else if ($paramsAction == 'update') {

                                        $this->_productParameterValueMapper->update($productParameterValue);
                                    }
                                }
                            }
                        }

                        $db->commit();
                        $this->_flashMessenger->addMessage(array('info' => 'Parametry byly úspěšně upraveny.'));
                    } catch (Exception $e) {
                        $this->_flashMessenger->addMessage(array('error' => 'Při ukládání nastala chyba!<br />' . $e->getMessage()));
                        $db->rollBack();
                    }

                    $this->_redirect($this->getRequest()->getRequestUri());
                }
            } else {

                if ($isMainProduct) {
                    $this->view->variantParametersInfo = "Parametry jsou přebírány z variant.";
                } else {
                    $this->view->variantParametersInfo = "K produktu nenáleží žádné parametry. Přidejte parametry ke kategorii, ke které je přiřazen i tento produkt.";
                }
            }

            $this->view->isParameters = $isParameter;
        } else {
            $module = $this->getRequest()->getModuleName();
            $controller = $this->getRequest()->getControllerName();
            $this->_redirect($module . '/' . $controller);
        }
    }

//    public function indexAction() {
//        $this->_parameterMapper = new Admin_Model_ParameterMapper();
//        $parameters = $this->_parameterMapper->getParameters();
//
//        $this->view->parameterResult = $parameters;
//        $this->view->paginator = $this->_parameterMapper->_paginator;
//    }
//
//    public function addAction() {
//
//        //ziskam jednotky do formulare
//        $units = $this->_parameterUnitMapper->getParameterUnits();
//        $unitsToForm = array();
//        $unitsToForm[0] = '--Vyberte--';
//        foreach ($units as $unit) {
//            $unitsToForm[$unit->getParameter_unit_id()] = $unit->getParameter_unit_name();
//        }
//
//        //ziskam kategorie pro prirazeni parametru ke kategoriim
//        $childs = $this->_categoryMapper->getChildsCache(0, true);
//        $categoryChildsAry = $this->_categoryMapper->getCategoryChildsAryCache($childs);
//        $categoryToForm = array();
//
//        foreach ($categoryChildsAry as $category) {
//            $categoryToForm[$category->getCategory_id()] = $category->getCategory_structure();
//        }
//
//
//        $form = new Admin_Form_EditParameterForm();
//        $form->setUnits($unitsToForm);
//        $form->setCategory($categoryToForm);
//
////      přidám form element text pro vlozeni hodnoty ciselniku  
//        $form->addElement($form->addParameterDialValue(1));
//
//        $form->startForm();
//
//
//        $this->view->form = $form;
//
//
//        if ($this->getRequest()->isPost()) {
//
//            $postedData = $this->getRequest()->getPost();
//
////            zkontroluju, jaky typ parametru je ukladan, podle toho zpracuju posted data
//            if ($postedData['parameter_type'] == 1) {
//                if (array_key_exists('parameter_dial_value', $postedData)) {
//                    unset($postedData['parameter_dial_value']);
//                }
//            } elseif ($postedData['parameter_type'] == 3) {
//                if (array_key_exists('parameter_dial_value', $postedData)) {
//                    unset($postedData['parameter_dial_value']);
//                }
//
//                if (array_key_exists('parameter_parameter_unit_id', $postedData)) {
//                    unset($postedData['parameter_parameter_unit_id']);
//                }
//            }
//
//            if ($form->isValidPartial($postedData)) {
//                $parameter = new Admin_Model_Parameter();
//                $parameter->setOptions($postedData);
//                if ($parameter->getParameter_parameter_unit_id() < 1) {
//                    $parameter->setParameter_parameter_unit_id(null);
//                }
//
//
//                $db = $this->_parameterMapper->getDbTable()->getDefaultAdapter();
//                $db->beginTransaction();
//
//                $lastParameterId = 0;
//                try {
//
//                    $this->_parameterMapper->save($parameter);
//
//                    $lastParameterId = $this->_parameterMapper->getDbTable()->getDefaultAdapter()->lastInsertId();
//
////nastavim kategorie pro parametr
//                    $parameterCategoryIds = $this->getRequest()->getParam('parameter_category');
//
//                    if (!is_array($parameterCategoryIds)) {
//                        $parameterCategoryIds = array();
//                    }
//
//                    if (!empty($parameterCategoryIds)) {
//
//                        $bulkParameterCategoryAry = array();
//                        foreach ($parameterCategoryIds as $parameterCategoryId) {
//                            $bulkParameterCategoryAry[] = array(
//                                'category_id' => $parameterCategoryId,
//                                'parameter_id' => $lastParameterId
//                            );
//                        }
//
//                        $this->_parameterCategoryMapper->bulkInsert($bulkParameterCategoryAry);
//                    }
//
//
////pokud je ukladany parameter ciselnik, tak musim ulozit hodnoty ciselniku do db  
//                    $parameterDialValues = array();
//
//                    if (array_key_exists('parameter_dial_value', $postedData)) {
//                        foreach ($postedData['parameter_dial_value'] as $parameterDialValue) {
//                            if ($parameterDialValue != "") {
//                                $parameterDialValues[] = array(
//                                    'parameter_dial_value' => $parameterDialValue,
//                                    'parameter_dial_parameter_id' => $lastParameterId
//                                );
//                            }
//                        }
//
//                        if (!empty($parameterDialValues)) {
//                            $this->_parameterDialMapper->bulkInsert($parameterDialValues);
//                        }
//                    }
//
//                    $db->commit();
//                    $this->_flashMessenger->addMessage(array('info' => 'Parametr byl úspěšně vložen.'));
//                } catch (Exception $e) {
//                    $this->_flashMessenger->addMessage(array('error' => 'Při ukládání parametru nastala chyba!<br />' . $e->getMessage()));
//                    $db->rollBack();
//                }
//
//                $module = $this->getRequest()->getModuleName();
//                $controller = $this->getRequest()->getControllerName();
//                $this->_redirect($module . '/' . $controller);
//            } else {
//                $postedData = $this->getRequest()->getPost();
//
//                //pokud pri odeslani formulare existuji textboxy parameter_dial_value, tak je musim znovu vytvorit, protoze se pridavaji ajaxem a pri odeslani formulare mizi
//                if (array_key_exists('parameter_dial_value', $postedData)) {
//                    if (count($postedData['parameter_dial_value']) > 1) {
//                        foreach ($postedData['parameter_dial_value'] as $parameterDialKey => $parameterDial) {
//                            $parameterDialNum = (int) str_replace('parameter_dial_value_', '', $parameterDialKey);
//                            if ($parameterDialNum && $parameterDial != "") {
//                                $form->addElement($form->addParameterDialValue($parameterDialNum));
//                            }
//                        }
//                    }
//                }
//
//                $form->populate($this->getRequest()->getPost());
//            }
//        }
//    }


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

