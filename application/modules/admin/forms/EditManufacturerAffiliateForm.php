<?php

class Admin_Form_EditManufacturerAffiliateForm extends Zend_Form {

    private $_manufacturers;

    public function setManufacturers($manufacturers) {
        $this->_manufacturers = $manufacturers;
    }


    public function init() {
        
    }

    public function startForm() {

        $this->setMethod(self::METHOD_POST);


        $manufacturer = $this->createElement('select', 'manufacturer_id');
        $manufacturer->setLabel('Výrobce')
                ->setOptions(array('class' => 'form-control'))
                ->addMultiOptions($this->_manufacturers);


        $submit = $this->createElement('submit', 'submit');
        $submit->setLabel('Uložit')
                ->removeDecorator('DtDdWrapper')
                ->setOptions(array('class' => 'btn btn-primary'));
        $this->addElement($submit);

        $this->addElements(array(
            $manufacturer,
            $submit,
        ));

        $elements = $this->getElements();
        foreach ($elements as $element) {
            $element->removeDecorator('HtmlTag');
            $element->removeDecorator('Label');
        }
    }

}

