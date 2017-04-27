<?php

class Default_Form_AddToCartForm extends Zend_Form {

//    private $_variants = array();

//        private $_variant;
//        private $_isVariant = false;
    
//    public function getVariants() {
//        return $this->_variants;
//    }
//
//    public function setVariants($variants) {
//        $this->_variants = $variants;
//    }

//    public function getVariant() {
//        return $this->_variant;
//    }

//    public function setVariant($variant) {
//        $this->_variant = $variant;
//    }
    
//    public function getIsVariant() {
//        return $this->_isVariant;
//    }
//
//    public function setIsVariant($isVariant) {
//        $this->_isVariant = $isVariant;
//    }
    
    public function init() {
        
    }

    public function startForm() {
        $this->setMethod(self::METHOD_POST);
        $this->setOptions(array('class' => 'form-add-to-cart'));


//        if (!empty($this->_variants)) {
//            $variant = $this->createElement('select', 'variant');
//            $variant->setLabel('Vyberte variantu:')
//                    ->setOptions(array('class' => 'form-control'))
//                    ->addMultiOptions($this->getVariants());
//
//            $this->addElements(array(
//                $variant
//            ));
//        }
        
        
        
//        if ($this->getIsVariant()) {
//            $variant = $this->createElement('hidden', 'variant_id');
//            $variant->setOptions(array('class' => 'form-control'));
//            $variant->setValue($this->getVariant());
//            $variant->setRequired();
//
//            $this->addElements(array(
//                $variant
//            ));
//        }
        

        $productId = $this->createElement('hidden', 'product_id');
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
