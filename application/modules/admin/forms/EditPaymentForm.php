<?php

class Admin_Form_EditPaymentForm extends Zend_Form {

    private $_deliveries;
    
    public function setDeliveries($deliveries){
        $this->_deliveries = $deliveries;
    }
    
    public function getDeliveries(){
        return $this->_deliveries;
    }
    
    public function init() {
        
    }

    public function startForm() {

        $this->setMethod(self::METHOD_POST);

        $name = $this->createElement('text', 'payment_name');
        $name->setLabel('Název')
                ->setOptions(array('class' => 'form-control'))
                ->setRequired(true);


        $info = $this->createElement('text', 'payment_info');
        $info->setLabel('Krátké info')
                ->setOptions(array('class' => 'form-control'));

        $note = $this->createElement('textarea', 'payment_note');
        $note->setLabel('Popis')
                ->setOptions(array('class' => 'form-control'))
                ->setAttribs(array('cols' => '40', 'rows' => '4'))
                ->setOptions(array('class' => 'form-control'));


        $price = $this->createElement('text', 'payment_price_czk');
        $price->setLabel('Cena')
                ->setOptions(array('class' => 'form-control'))
                ->addValidator(new Zend_Validate_Digits())
                ->setRequired(true);


        $limit = $this->createElement('text', 'payment_free_shipping_limit');
        $limit->setLabel('Limit pro dopravu zdarma')
                ->setOptions(array('class' => 'form-control'))
                ->addValidator(new Zend_Validate_Digits())
                ->setRequired(true);

        $is_active = $this->createElement('checkbox', 'payment_is_active');
        $is_active->setLabel('Aktivní')
                ->setOptions(array('class' => 'form-control'))
                ->setRequired(true);

        
        $deliveries = $this->createElement('multiCheckbox', 'payment_deliveries');
        $deliveries->setLabel('Doprava')
                ->addMultiOptions($this->_deliveries);
        

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
            $is_active,
            $deliveries,
            $submit
        ));


        $elements = $this->getElements();
        foreach ($elements as $element) {
            $element->removeDecorator('HtmlTag');
            $element->removeDecorator('Label');
        }
    }

}

