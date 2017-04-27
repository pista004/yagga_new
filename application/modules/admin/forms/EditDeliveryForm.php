<?php

class Admin_Form_EditDeliveryForm extends Zend_Form {

    private $_payments;
    
    public function setPayments($payments){
        $this->_payments = $payments;
    }
    
    public function getPayments(){
        return $this->_payments;
    }
    
    public function init() {
        
    }

    public function startForm() {

        $this->setMethod(self::METHOD_POST);

        $name = $this->createElement('text', 'delivery_name');
        $name->setLabel('Název')
                ->setOptions(array('class' => 'form-control'))
                ->setRequired(true);


        $info = $this->createElement('text', 'delivery_info');
        $info->setLabel('Krátké info')
                ->setOptions(array('class' => 'form-control'));

        $note = $this->createElement('textarea', 'delivery_note');
        $note->setLabel('Popis')
                ->setOptions(array('class' => 'form-control'))
                ->setAttribs(array('cols' => '40', 'rows' => '4'))
                ->setOptions(array('class' => 'form-control'));


        $price = $this->createElement('text', 'delivery_price_czk');
        $price->setLabel('Cena')
                ->setOptions(array('class' => 'form-control'))
                ->addValidator(new Zend_Validate_Digits())
                ->setRequired(true);


        $limit = $this->createElement('text', 'delivery_free_shipping_limit');
        $limit->setLabel('Limit pro dopravu zdarma')
                ->setOptions(array('class' => 'form-control'))
                ->addValidator(new Zend_Validate_Digits())
                ->setRequired(true);

        $is_address = $this->createElement('checkbox', 'delivery_is_address');
        $is_address->setLabel('Nutno zadat adresu')
                ->setOptions(array('class' => 'form-control'))
                ->setRequired(true);

        $is_active = $this->createElement('checkbox', 'delivery_is_active');
        $is_active->setLabel('Aktivní')
                ->setOptions(array('class' => 'form-control'))
                ->setRequired(true);

        $payments = $this->createElement('multiCheckbox', 'delivery_payments');
        $payments->setLabel('Platby')
                ->addMultiOptions($this->_payments);

        $submit = $this->createElement('submit', 'submit');
        $submit->setLabel('Uložit')
                ->removeDecorator('DtDdWrapper')
                ->setOptions(array('class' => 'btn btn-primary'));


        $this->addElements(array(
            $name,
            $info,
            $note,
            $price,
            $limit,
            $is_address,
            $is_active,
            $payments,
            $submit
        ));


        $elements = $this->getElements();
        foreach ($elements as $element) {
            $element->removeDecorator('HtmlTag');
            $element->removeDecorator('Label');
        }
    }

}

