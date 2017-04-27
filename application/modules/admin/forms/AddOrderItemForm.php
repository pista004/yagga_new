<?php

class Admin_Form_AddOrderItemForm extends Zend_Form {

    private $_products = array();

    public function setProducts($products) {
        $this->_products = $products;
    }

    public function getProducts() {
        return $this->_products;
    }

    private $_variants = array();

    public function setVariants($variants) {
        $this->_variants = $variants;
    }

    public function getVariants() {
        return $this->_variants;
    }

    public function init() {
        
    }

    public function startForm() {

        $this->setMethod(self::METHOD_POST);


        //overeni, jestli se select nerovna nule
        $required_product = new Zend_Validate_NotEmpty();
        $required_product->setType($required_product->getType() | Zend_Validate_NotEmpty::INTEGER | Zend_Validate_NotEmpty::ZERO);


        $order_item_product = $this->createElement('select', 'order_item_product_id');
        $order_item_product->setLabel('Produkt')
                ->setOptions(array('class' => 'form-control'))
                ->setAttrib('id', 'order-item-product')
                ->addValidator($required_product)
                ->addMultiOptions($this->getProducts());


        $variants = $this->getVariants();
        if (!empty($variants)) {
            $required_variant = new Zend_Validate_NotEmpty();
            $required_variant->setType($required_variant->getType() | Zend_Validate_NotEmpty::INTEGER | Zend_Validate_NotEmpty::ZERO);

            $order_item_variant_id = $this->createElement('select', 'order_item_variant_id');
            $order_item_variant_id->setLabel('Varianta')
                    ->setOptions(array('class' => 'form-control'))
                    ->addValidator($required_variant)
                    ->addMultiOptions($this->getVariants());


            $this->addElements(array(
                $order_item_variant_id
            ));
        }


        $order_item_pieces = $this->createElement('text', 'order_item_pieces');
        $order_item_pieces->setLabel('Množství')
                ->setOptions(array('class' => 'form-control'))
                ->addValidator(new Zend_Validate_Digits)
                ->setRequired();




        $submit = $this->createElement('submit', 'submit');
        $submit->setLabel('Uložit')
                ->removeDecorator('DtDdWrapper')
                ->setOptions(array('class' => 'btn btn-primary'));
        $this->addElement($submit);

        $this->addElements(array(
            $order_item_product,
            $order_item_pieces,
            $submit
        ));

        $elements = $this->getElements();
        foreach ($elements as $element) {
            $element->removeDecorator('HtmlTag');
            $element->removeDecorator('Label');
        }
    }

//    public function startFormVariants() {
//
//        //overeni, jestli se select nerovna nule
//        $required = new Zend_Validate_NotEmpty();
//        $required->setType($required->getType() | Zend_Validate_NotEmpty::INTEGER | Zend_Validate_NotEmpty::ZERO);
//
//        $order_item_variant_id = $this->createElement('select', 'order_item_variant_id');
//        $order_item_variant_id->setLabel('Varianta')
//                ->setOptions(array('class' => 'form-control'))
//                ->addValidator($required)
//                ->addMultiOptions($this->getVariants());
//
//
//        $this->addElements(array(
//            $order_item_variant_id,
//        ));
//
//        $elements = $this->getElements();
//        foreach ($elements as $element) {
//            $element->removeDecorator('HtmlTag');
//            $element->removeDecorator('Label');
//        }
//    }
}

