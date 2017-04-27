<?php

class Admin_Form_EditParameterUnitForm extends Zend_Form {

    
    public function init() {
        
    }

    public function startForm() {

        $this->setMethod(self::METHOD_POST);


        $name = $this->createElement('text', 'parameter_unit_name');
        $name->setLabel('Název')
                ->setOptions(array('class' => 'form-control'))
                ->setRequired(true);

        
        $shortcut = $this->createElement('text', 'parameter_unit_shortcut');
        $shortcut->setLabel('Zkratka')
                ->setOptions(array('class' => 'form-control'))
                ->setRequired(true);
        
        
        $note = $this->createElement('textarea', 'parameter_unit_note');
        $note->setLabel('Popis')
                ->setOptions(array('class' => 'form-control'))
                ->setAttribs(array('cols' => '40', 'rows' => '4'));


        $submit = $this->createElement('submit', 'submit');
        $submit->setLabel('Uložit')
                ->removeDecorator('DtDdWrapper')
                ->setOptions(array('class' => 'btn btn-primary'));


        $this->addElements(array(
            $name,
            $shortcut,
            $note,
            $submit
        ));


        $elements = $this->getElements();
        foreach ($elements as $element) {
            $element->removeDecorator('HtmlTag');
            $element->removeDecorator('Label');
        }
    }

}

