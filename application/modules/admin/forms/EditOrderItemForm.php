<?php

class Admin_Form_EditOrderItemForm extends Zend_Form {

    private $_variant = array();

    public function setVariant($variant) {
        $this->_variant = $variant;
    }

    public function getVariant() {
        return $this->_variant;
    }

    public function init() {
        
    }

    public function startForm() {

        $this->setMethod(self::METHOD_POST);

        $order_item_pieces = $this->createElement('text', 'order_item_pieces');
        $order_item_pieces->setLabel('Ks')
                ->setOptions(array('class' => 'form-control'))
                ->setRequired(true);

        $order_item_price = $this->createElement('text', 'order_item_price');
        $order_item_price->setLabel('Cena za ks')
                ->setOptions(array('class' => 'form-control'))
                ->setRequired(true);


        $order_item_variant_id = $this->createElement('select', 'order_item_variant_id');
        $order_item_variant_id->setLabel('Varianta')
                ->setOptions(array('class' => 'form-control'))
                ->addMultiOptions($this->getVariant());


        $submit = $this->createElement('submit', 'submit');
        $submit->setLabel('Uložit')
                ->removeDecorator('DtDdWrapper')
                ->setOptions(array('class' => 'btn btn-primary'));
        $this->addElement($submit);

        if ($this->getVariant()) {
            $this->addElements(array(
                $order_item_variant_id
            ));
        }

        $this->addElements(array(
            $order_item_pieces,
            $order_item_price,
            $submit
        ));

        $elements = $this->getElements();
        foreach ($elements as $element) {
            $element->removeDecorator('HtmlTag');
            $element->removeDecorator('Label');
        }
    }

}

