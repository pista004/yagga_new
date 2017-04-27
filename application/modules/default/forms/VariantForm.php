<?php

class Default_Form_VariantForm extends Zend_Form {

    private $_variants = array(1 => "cerna", 2 => "zelena");
    
    public function getVariants() {
        return $this->_variants;
    }

    public function setVariants($variants) {
        $this->_variants = $variants;
    }
    
    
    public function init(){
        
    }
    
    public function startForm() {
        $this->setMethod(self::METHOD_POST);
        $this->setOptions(array('class' => 'form-variant'));

        $productId = $this->createElement('se', 'product_id');
        $productId->setOptions(array('class' => 'form-control'));

        $pieces = $this->createElement('hidden', 'pieces');
        $pieces->setOptions(array('class' => 'form-control'));
        $pieces->setValue(1);

        $price = $this->createElement('hidden', 'price');
        $price->setOptions(array('class' => 'form-control'));
        
        $submit = $this->createElement('submit', 'submit');
        $submit->setLabel('Vložit do košíku')
                ->setOptions(array('class' => 'btn btn-default add-to-cart'))
                ->removeDecorator('DtDdWrapper')
                ->setIgnore(true);

        $this->addElements(array(
            $productId,
            $pieces,
            $price,
            $submit
        ));
        
        $elements = $this->getElements();
        foreach ($elements as $element) {
            $element->removeDecorator('HtmlTag');
            $element->removeDecorator('Label');
        }
        
    }

}
