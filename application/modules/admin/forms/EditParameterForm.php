<?php

class Admin_Form_EditParameterForm extends Zend_Form {

    private $_category;
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


        $parameterType = new Admin_Model_ParameterType();

        $type = $this->createElement('radio', 'parameter_type');
        $type->setLabel('Typ')
                ->setOptions(array('class' => 'parameter-radio'))
                ->addMultiOptions($parameterType->getParameter_type())
                ->setValue(1)
                ->setRequired(true);


        $name = $this->createElement('text', 'parameter_name');
        $name->setLabel('Název')
                ->setOptions(array('class' => 'form-control'))
                ->setRequired(true);


        $note = $this->createElement('textarea', 'parameter_note');
        $note->setLabel('Popis')
                ->setOptions(array('class' => 'form-control'))
                ->setAttribs(array('cols' => '40', 'rows' => '4'));


//        $dial_value = $this->createElement('text', 'parameter_dial_value');
//        $dial_value->setLabel('Číselník')
//                ->setOptions(array('class' => 'form-control'))
//                ->setRequired(true);

        $add_dial_btn = $this->createElement('button', 'add_dial_btn');
        $add_dial_btn->setLabel('Přidat hodnotu')
                ->removeDecorator('DtDdWrapper')
                ->setOptions(array('class' => 'btn btn-default'));


        $unit = $this->createElement('select', 'parameter_parameter_unit_id');
        $unit->setLabel('Jednotka')
                ->setOptions(array('class' => 'form-control'))
                ->addMultiOptions($this->_units);


        $category = $this->createElement('multiCheckbox', 'parameter_category');
        $category->setLabel('Kategorie')
                ->addMultiOptions($this->_category);


        $submit = $this->createElement('submit', 'submit');
        $submit->setLabel('Uložit')
                ->removeDecorator('DtDdWrapper')
                ->setOptions(array('class' => 'btn btn-primary'));


        $this->addElements(array(
            $type,
            $name,
            $note,
            $add_dial_btn,
            $unit,
            $category,
            $submit
        ));


        $elements = $this->getElements();
        foreach ($elements as $element) {
            $element->removeDecorator('HtmlTag');
            $element->removeDecorator('Label');
        }
    }

    public function addParameterDialValue($num) {

        $return = null;
        
        if ($num) {
            $parameter_dial = 'parameter_dial_value_' . $num;

            $dial_value = $this->createElement('text', $parameter_dial);
            $dial_value->setLabel('Číselník')
                    ->setOptions(array(
                        'class' => 'form-control parameter_dial_value', 
                        'id' => $parameter_dial,
                        'data-num' => $num
                        ))
                    ->setBelongsTo('parameter_dial_value')
                    ->removeDecorator('HtmlTag')
                    ->removeDecorator('Label');
//                    ->setRequired(true);


            $return = $dial_value;
            
        }
        return $return;
    }

}

