<?php

class Admin_Form_FilterForm extends Zend_Form {

    private $_manufacturers = array();
    private $_categories = array();

    public function setManufacturers($manufacturer) {
        $this->_manufacturers = $manufacturer;
    }

    public function getManufacturers() {
        return $this->_manufacturers;
    }

    public function setCategories($categories) {
        $this->_categories = $categories;
    }

    public function getCategories() {
        return $this->_categories;
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
         * kategorie
         */
        $categories = $this->createElement('multiCheckbox', 'category_id');
        $categories->setLabel('Kategorie')
                ->addMultiOptions($this->_categories);



        $this->addDisplayGroup(
                array($categories), 'categories', array('class' => 'filter-fieldset')
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

