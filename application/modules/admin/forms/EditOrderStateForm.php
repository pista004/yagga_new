<?php

class Admin_Form_EditOrderStateForm extends Zend_Form {

    public function init() {
        
    }

    public function startForm() {

        $this->setMethod(self::METHOD_POST);

        /*
         * fakturační údaje
         */

        $order_state_name = $this->createElement('text', 'order_state_name');
        $order_state_name->setLabel('Stav')
                ->setOptions(array('class' => 'form-control'))
                ->setRequired(true);

        $order_state_color = $this->createElement('text', 'order_state_color');
        $order_state_color->setLabel('Barva')
                ->setOptions(array('class' => 'form-control'))
                ->addValidator('Regex', true, array('/^#([a-zA-Z0-9]{6})$/'))
                ->addErrorMessages(array(
                    Zend_Validate_Regex::INVALID => 'Barva musí být zadána v HEX tvaru např: #fefefe',
                ))
                ->setRequired(true);
        
        
        $order_state_text = $this->createElement('textarea', 'order_state_text');
        $order_state_text->setLabel('Text emailu');
        $order_state_text->setOptions(array('class' => 'form-control ckeditor'))
                ->setAttribs(array('cols' => '40', 'rows' => '4'))
                ->setLabel('Text emailu');
        

        $submit = $this->createElement('submit', 'submit');
        $submit->setLabel('Uložit')
                ->removeDecorator('DtDdWrapper')
                ->setOptions(array('class' => 'btn btn-primary'));
        $this->addElement($submit);

        $this->addElements(array(
            $order_state_name,
            $order_state_color,
            $order_state_text,
            $submit
        ));

        $elements = $this->getElements();
        foreach ($elements as $element) {
            $element->removeDecorator('HtmlTag');
            $element->removeDecorator('Label');
        }
    }

}

