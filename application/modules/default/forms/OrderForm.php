<?php

class Default_Form_OrderForm extends Zend_Form {

    private $_country = array(1 => "Česká republika");
    private $_delivery;
    private $_payment;

    public function getCountry() {
        return $this->_country;
    }

    public function setDelivery($delivery) {
        $this->_delivery = $delivery;
    }
    
    public function setPayment($payment) {
        $this->_payment = $payment;
    }
    
    public function init() {
        
    }

    public function startForm() {

        $this->setMethod(self::METHOD_POST);

        /*
         * Doprava 
         */
        
        $delivery = $this->createElement('radio', 'delivery');
        $delivery->setLabel('Doprava')
                ->addMultiOptions($this->_delivery)
                ->setOptions(array('escape' => false, 'label_class' => 'delivery-item'))
                ->setValue(13)
                ->setRequired(true);
        
        
        /*
         * Platba 
         */
        
        $payment = $this->createElement('radio', 'payment');
        $payment->setLabel('Platba')
                ->addMultiOptions($this->_payment)
                ->setOptions(array('escape' => false, 'label_class' => 'payment-item'))
                ->setValue(8)
                ->setRequired(true);
        
        
        /*
         * fakturační údaje
         */

        $order_i_name = $this->createElement('text', 'order_i_name');
        $order_i_name->setLabel('Jméno *')
                ->setOptions(array('class' => 'form-control'))
                ->setRequired(true);

        $order_i_surname = $this->createElement('text', 'order_i_surname');
        $order_i_surname->setLabel('Příjmení *')
                ->setOptions(array('class' => 'form-control'))
                ->setRequired(true);

        $order_email = $this->createElement('text', 'order_email');
        $order_email->setLabel('Email *')
                ->setOptions(array('class' => 'form-control'))
                ->addValidator('EmailAddress')
                ->addErrorMessage('Email má chybný formát')
                ->setRequired(true);


        $order_phone = $this->createElement('text', 'order_phone');
        $order_phone->setLabel('Telefon *')
                ->setOptions(array('class' => 'form-control'))
                ->addValidator('Regex', true, array('/^(\+420)? ?[0-9]{3} ?[0-9]{3} ?[0-9]{3}$/'))
                ->addErrorMessage('Telefon má chybný formát')
                ->setRequired(true);

        $order_i_street = $this->createElement('text', 'order_i_street');
        $order_i_street->setLabel('Ulice *')
                ->setOptions(array('class' => 'form-control'))
                ->setRequired(true);

        $order_i_city = $this->createElement('text', 'order_i_city');
        $order_i_city->setLabel('Město *')
                ->setOptions(array('class' => 'form-control'))
                ->setRequired(true);

        $order_i_zip_code = $this->createElement('text', 'order_i_zip_code');
        $order_i_zip_code->setLabel('PSČ *')
                ->setOptions(array('class' => 'form-control'))
                ->addValidator('Regex', true, array('~^(\d){3} ?(\d){2}$~'))
                ->addErrorMessage('PSČ má chybný formát')
                ->setRequired(true);

        $order_i_country = $this->createElement('select', 'order_i_country_id');
        $order_i_country->setLabel('Stát')
                ->setOptions(array('class' => 'form-control'))
                ->addMultiOptions($this->getCountry());


        $order_i_company = $this->createElement('text', 'order_i_company');
        $order_i_company->setLabel('Firma')
                ->setOptions(array('class' => 'form-control'));


        $order_i_ico = $this->createElement('text', 'order_i_ico');
        $order_i_ico->setLabel('IČ')
                ->setOptions(array('class' => 'form-control'));

        $order_i_dic = $this->createElement('text', 'order_i_dic');
        $order_i_dic->setLabel('DIČ')
                ->setOptions(array('class' => 'form-control'));



        /*
         * Dodací údaje - doručovací adresa
         */

        $order_is_d_address = $this->createElement('checkbox', 'order_is_d_address');
        $order_is_d_address->setOptions(array('class' => 'form-control'))
                ->setLabel('Doručit na jinou adresu');


        $order_d_name = $this->createElement('text', 'order_d_name');
        $order_d_name->setLabel('Jméno')
                ->setOptions(array('class' => 'form-control'));


        $order_d_surname = $this->createElement('text', 'order_d_surname');
        $order_d_surname->setLabel('Příjmení')
                ->setOptions(array('class' => 'form-control'));


        $order_d_company = $this->createElement('text', 'order_d_company');
        $order_d_company->setLabel('Firma')
                ->setOptions(array('class' => 'form-control'));


        $order_d_street = $this->createElement('text', 'order_d_street');
        $order_d_street->setLabel('Ulice')
                ->setOptions(array('class' => 'form-control'));


        $order_d_city = $this->createElement('text', 'order_d_city');
        $order_d_city->setLabel('Město')
                ->setOptions(array('class' => 'form-control'));


        $order_d_zip_code = $this->createElement('text', 'order_d_zip_code');
        $order_d_zip_code->setLabel('PSČ')
                ->addValidator('Regex', true, array('~^(\d){3} ?(\d){2}$~'))
//                ->addErrorMessage($message)
                ->setOptions(array('class' => 'form-control'));

        $order_d_country = $this->createElement('select', 'order_d_country_id');
        $order_d_country->setLabel('Stát')
                ->setOptions(array('class' => 'form-control'))
                ->addMultiOptions($this->getCountry());
        
        $order_newsletter = $this->createElement('checkbox', 'order_newsletter');
        $order_newsletter->setOptions(array('class' => 'form-control'))
                ->setLabel('Chci dostávat novinky emailem')
                ->setValue(1);

        $order_note = $this->createElement('textarea', 'order_note');
        $order_note->setLabel('Poznámka')
                ->setOptions(array('class' => 'form-control'))
                ->setAttribs(array('cols' => '40', 'rows' => '2'));

        $submit = $this->createElement('submit', 'submit');
        $submit->setLabel('Odeslat objednávku')
                ->removeDecorator('DtDdWrapper')
                ->setOptions(array('class' => 'btn btn-default next'));

        $this->addElements(array(
            $delivery,
            $payment,
            $order_i_name,
            $order_i_surname,
            $order_email,
            $order_phone,
            $order_i_street,
            $order_i_city,
            $order_i_street,
            $order_i_zip_code,
            $order_i_country,
            $order_i_company,
            $order_i_zip_code,
            $order_i_ico,
            $order_i_dic,
            $order_is_d_address,
            $order_d_name,
            $order_d_surname,
            $order_d_company,
            $order_d_street,
            $order_d_city,
            $order_d_zip_code,
            $order_d_country,
            $order_note,
            $order_newsletter,
            $submit
        ));

        $elements = $this->getElements();
        foreach ($elements as $element) {
            $element->removeDecorator('HtmlTag');
            $element->removeDecorator('Label');
        }


    }

}

