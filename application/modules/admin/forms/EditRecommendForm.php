<?php

class Admin_Form_EditRecommendForm extends Zend_Form {

    private $_product;
//    private $_category;
    protected $_translator;

    /*
     * settery pro prijem dat z controlleru
     */

    public function setProduct($product) {
        $this->_product = $product;
    }

    public function init() {
        
    }

    public function startForm() {

        $this->setMethod(self::METHOD_POST);


        //overeni, jestli se select nerovna nule
        $required = new Zend_Validate_NotEmpty();
        $required->setType($required->getType() | Zend_Validate_NotEmpty::INTEGER | Zend_Validate_NotEmpty::ZERO);
        $recommend = $this->createElement('select', 'product_recommend_id');
        $recommend->setLabel('Doporučený produkt')
                ->setOptions(array('class' => 'form-control'))
                ->addMultiOptions($this->_product);
        $recommend->setRequired(true);
        $recommend->addValidator($required);


        $submit = $this->createElement('submit', 'submit');
        $submit->setLabel('Přidat')
                ->removeDecorator('DtDdWrapper')
                ->setOptions(array('class' => 'btn btn-primary'));


        $this->addElements(array(
            $recommend,
            $submit
        ));


        $elements = $this->getElements();
        foreach ($elements as $element) {
            $element->removeDecorator('HtmlTag');
            $element->removeDecorator('Label');
        }
    }

}

