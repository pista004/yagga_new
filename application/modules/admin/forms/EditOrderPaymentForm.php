<?php

class Admin_Form_EditOrderPaymentForm extends Zend_Form {

    private $_payments = array();

    public function setPayments($payments) {
        $this->_payments = $payments;
    }

    public function getPayments() {
        return $this->_payments;
    }

    public function init() {
        
    }

    public function startForm() {

        $this->setMethod(self::METHOD_POST);

        $order_payments = $this->createElement('select', 'order_payment_id');
        $order_payments->setLabel('Platba')
                ->setOptions(array('class' => 'form-control'))
                ->addMultiOptions($this->_payments);

        
        $order_payment_price = $this->createElement('text', 'order_payment_price');
        $order_payment_price->setLabel('Cena')
                ->addValidator(new Zend_Validate_Digits())
                ->setOptions(array('class' => 'form-control'));
        

        $submit = $this->createElement('submit', 'submit');
        $submit->setLabel('Uložit')
                ->removeDecorator('DtDdWrapper')
                ->setOptions(array('class' => 'btn btn-primary'));
        $this->addElement($submit);

        $this->addElements(array(
            $order_payments,
            $order_payment_price,
            $submit
        ));

        $elements = $this->getElements();
        foreach ($elements as $element) {
            $element->removeDecorator('HtmlTag');
            $element->removeDecorator('Label');
        }
    }

}

