<?php

class Admin_Form_EditOrderDeliveryForm extends Zend_Form {

    private $_deliveries = array();

    public function setDeliveries($deliveries) {
        $this->_deliveries = $deliveries;
    }

    public function getDeliveries() {
        return $this->_deliveries;
    }

    public function init() {
        
    }

    public function startForm() {

        $this->setMethod(self::METHOD_POST);

        $order_deliveries = $this->createElement('select', 'order_delivery_id');
        $order_deliveries->setLabel('Doprava')
                ->setOptions(array('class' => 'form-control'))
                ->addMultiOptions($this->_deliveries);

        
        $order_delivery_price = $this->createElement('text', 'order_delivery_price');
        $order_delivery_price->setLabel('Cena')
                ->addValidator(new Zend_Validate_Digits())
                ->setOptions(array('class' => 'form-control'));
        

        $submit = $this->createElement('submit', 'submit');
        $submit->setLabel('Uložit')
                ->removeDecorator('DtDdWrapper')
                ->setOptions(array('class' => 'btn btn-primary'));
        $this->addElement($submit);

        $this->addElements(array(
            $order_deliveries,
            $order_delivery_price,
            $submit
        ));

        $elements = $this->getElements();
        foreach ($elements as $element) {
            $element->removeDecorator('HtmlTag');
            $element->removeDecorator('Label');
        }
    }

}

