<?php

class Default_Form_FilterForm extends Zend_Form {

    private $_manufacturers = array();
    private $_variants = array();

    public function setManufacturers($manufacturer) {
        $this->_manufacturers = $manufacturer;
    }

    public function getManufacturers() {
        return $this->_manufacturers;
    }

    public function setVariants($variants) {
        $this->_variants = $variants;
    }

    public function getVariants() {
        return $this->_variants;
    }
    
    public function init() {
        
    }

    public function startForm() {

        $this->setMethod(self::METHOD_GET);


        /*
         * znacky
         */
        $manufacturers = $this->createElement('multiCheckbox', 'manufacturer_id');
        $manufacturers->setLabel('Značky')
                ->addMultiOptions($this->_manufacturers);


//        $manufacturersSubmit = $this->createElement('submit', 'manufacturer_submit');
//        $manufacturersSubmit->setLabel('Filtruj')
//                ->removeDecorator('DtDdWrapper')
//                ->setOptions(array('class' => 'filter-submit'));


        $this->addDisplayGroup(
                array($manufacturers), 'manufacturers', array('class' => 'filter-fieldset')
        );

        
        
        
        /*
         * varianty
         */
        $variants = $this->createElement('multiCheckbox', 'variant_id');
        $variants->setLabel('Velikosti')
                ->addMultiOptions($this->_variants);


//        $variantsSubmit = $this->createElement('submit', 'variant_submit');
//        $variantsSubmit->setLabel('Filtruj')
//                ->removeDecorator('DtDdWrapper')
//                ->setOptions(array('class' => 'filter-submit'));


        $this->addDisplayGroup(
                array($variants), 'variants', array('class' => 'filter-fieldset')
        );
        
        
        
        
        
        // smazu dl a dt decorators
        $this->setDisplayGroupDecorators(array(
            'FormElements',
            'Fieldset',
        ));

        
        $submit = $this->createElement('submit', 'submit');
        $submit->setLabel('Filtruj')
                ->removeDecorator('DtDdWrapper')
                ->setOptions(array('class' => 'filter-submit'));
        
        
        $this->addElements(array(
            $submit,
        ));
        

        $elements = $this->getElements();
        foreach ($elements as $element) {
            $element->removeDecorator('HtmlTag');
            $element->removeDecorator('Label');
        }



    }

}

