<?php

class Admin_Form_EditProductParameterForm extends Zend_Form {

    private $_value_bool_params = array(
        null => '-Vyberte-',
        1 => 'Ano',
        2 => 'Ne'
    );
    private $_units;

    public function setCategory($category) {
        $this->_category = $category;
    }

    public function setUnits($units) {
        $this->_units = $units;
    }

    public function init() {
        
    }

    public function startForm() {

        $this->setMethod(self::METHOD_POST);


//        $parameterType = new Admin_Model_ParameterType();
//
//        $type = $this->createElement('radio', 'parameter_type');
//        $type->setLabel('Typ')
//                ->setOptions(array('class' => 'parameter-radio'))
//                ->addMultiOptions($parameterType->getParameter_type())
//                ->setValue(1)
//                ->setRequired(true);
//
//
//        $name = $this->createElement('text', 'parameter_name');
//        $name->setLabel('Název')
//                ->setOptions(array('class' => 'form-control'))
//                ->setRequired(true);
//
//
//        $note = $this->createElement('textarea', 'parameter_note');
//        $note->setLabel('Popis')
//                ->setOptions(array('class' => 'form-control'))
//                ->setAttribs(array('cols' => '40', 'rows' => '4'));
//
//
////        $dial_value = $this->createElement('text', 'parameter_dial_value');
////        $dial_value->setLabel('Číselník')
////                ->setOptions(array('class' => 'form-control'))
////                ->setRequired(true);
//
//        $add_dial_btn = $this->createElement('button', 'add_dial_btn');
//        $add_dial_btn->setLabel('Přidat hodnotu')
//                ->removeDecorator('DtDdWrapper')
//                ->setOptions(array('class' => 'btn btn-default'));
//
//
//        $unit = $this->createElement('select', 'parameter_parameter_unit_id');
//        $unit->setLabel('Jednotka')
//                ->setOptions(array('class' => 'form-control'))
//                ->addMultiOptions($this->_units);
//
//
//        $category = $this->createElement('multiCheckbox', 'parameter_category');
//        $category->setLabel('Kategorie')
//                ->addMultiOptions($this->_category);
//
//
        $submit = $this->createElement('submit', 'submit');
        $submit->setLabel('Uložit')
                ->removeDecorator('DtDdWrapper')
                ->setOptions(array('class' => 'btn btn-primary'));


        $this->addElements(array(
//            $type,
//            $name,
//            $note,
//            $add_dial_btn,
//            $unit,
//            $category,
            $submit
        ));
//
//
//        $elements = $this->getElements();
//        foreach ($elements as $element) {
//            $element->removeDecorator('HtmlTag');
//            $element->removeDecorator('Label');
//        }
    }

    /**
     * create element textbox for parameter value
     *
     * @param string $name
     * @param string $label
     * @param array $multi_options
     * @param array $options
     * @return formelement
     */
    public function addParameter($type, $name, $label, $belongsTo, $multi_options = array(), $options = array()) {

        $element = null;

        switch ($type) {
            case 1:

                $parameterValue = $this->createElement('text', $name);
                $parameterValue->setLabel($label)
                        ->setOptions($options)
                        ->setBelongsTo($belongsTo)
                        ->removeDecorator('HtmlTag')
                        ->removeDecorator('Label');

                $element = $parameterValue;

                break;
            case 2:
            case 3:
                if (!empty($multi_options)) {
                    $parameterValue = $this->createElement('select', $name);
                    $parameterValue->setLabel($label)
                            ->addMultiOptions($multi_options)
                            ->setOptions($options)
                            ->setBelongsTo($belongsTo)
                            ->removeDecorator('HtmlTag')
                            ->removeDecorator('Label');
                    
                    $element = $parameterValue;
                }

                break;
            default:
                break;
        }

        return $element;
    }


}

