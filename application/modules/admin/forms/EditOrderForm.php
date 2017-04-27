<?php

class Admin_Form_EditOrderForm extends Zend_Form {

    private $_country = array(1 => "Česká republika");
    private $_order_state;

    public function getCountry() {
        return $this->_country;
    }
    
    public function setOrder_state($order_state) {
        $this->_order_state = $order_state;
    }
    
    public function getOrder_state() {
        return $this->_order_state;
    }

    public function init() {
        
    }

    public function startForm() {

        $this->setMethod(self::METHOD_POST);

        /*
         * fakturační údaje
         */

        $order_i_name = $this->createElement('text', 'order_i_name');
        $order_i_name->setLabel('Jméno')
                ->setOptions(array('class' => 'form-control'))
                ->setRequired(true);

        $order_i_surname = $this->createElement('text', 'order_i_surname');
        $order_i_surname->setLabel('Příjmení')
                ->setOptions(array('class' => 'form-control'))
                ->setRequired(true);

        $order_email = $this->createElement('text', 'order_email');
        $order_email->setLabel('Email')
                ->setOptions(array('class' => 'form-control'))
                ->setRequired(true);


        $order_phone = $this->createElement('text', 'order_phone');
        $order_phone->setLabel('Telefon')
                ->setOptions(array('class' => 'form-control'))
                ->setRequired(true);

        $order_i_street = $this->createElement('text', 'order_i_street');
        $order_i_street->setLabel('Ulice')
                ->setOptions(array('class' => 'form-control'));

        $order_i_city = $this->createElement('text', 'order_i_city');
        $order_i_city->setLabel('Město')
                ->setOptions(array('class' => 'form-control'));

        $order_i_street = $this->createElement('text', 'order_i_street');
        $order_i_street->setLabel('Ulice')
                ->setOptions(array('class' => 'form-control'));

        $order_i_zip_code = $this->createElement('text', 'order_i_zip_code');
        $order_i_zip_code->setLabel('PSČ')
                ->setOptions(array('class' => 'form-control'));

        $order_i_country = $this->createElement('select', 'order_i_country_id');
        $order_i_country->setLabel('Stát')
                ->setOptions(array('class' => 'form-control'))
                ->addMultiOptions($this->getCountry());


        $order_i_company = $this->createElement('text', 'order_i_company');
        $order_i_company->setLabel('Firma')
                ->setOptions(array('class' => 'form-control'));

        $order_i_zip_code = $this->createElement('text', 'order_i_zip_code');
        $order_i_zip_code->setLabel('PSČ')
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
                ->setOptions(array('class' => 'form-control'));

        $order_d_country = $this->createElement('select', 'order_d_country_id');
        $order_d_country->setLabel('Stát')
                ->setOptions(array('class' => 'form-control'))
                ->addMultiOptions($this->getCountry());

        $order_note = $this->createElement('textarea', 'order_note');
        $order_note->setLabel('Poznámka')
                ->setOptions(array('class' => 'form-control'))
                ->setAttribs(array('cols' => '40', 'rows' => '2', 'disabled' => 'disabled'));




        /*
         * administrace objednávky
         */

        $order_number = $this->createElement('text', 'order_number');
        $order_number->setLabel('Číslo objednávky')
                ->setOptions(array('class' => 'form-control'))
                ->setAttribs(array('disabled' => 'disabled'));

        $order_state_id = $this->createElement('select', 'order_order_state_id');
        $order_state_id->setLabel('Stav')
                ->setOptions(array('class' => 'form-control'))
                ->addMultiOptions($this->getOrder_state());


        $order_admin_note = $this->createElement('textarea', 'order_admin_note');
        $order_admin_note->setLabel('Poznámka administrátora')
                ->setOptions(array('class' => 'form-control'))
                ->setAttribs(array('cols' => '40', 'rows' => '3'));



        $submit = $this->createElement('submit', 'submit');
        $submit->setLabel('Uložit')
                ->removeDecorator('DtDdWrapper')
                ->setOptions(array('class' => 'btn btn-primary'));
        $this->addElement($submit);

        $this->addElements(array(
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
            $order_d_name,
            $order_d_surname,
            $order_d_company,
            $order_d_street,
            $order_d_city,
            $order_d_zip_code,
            $order_d_country,
            $order_note,
            $order_number,
            $order_state_id,
            $order_admin_note,
            $submit
        ));

        $elements = $this->getElements();
        foreach ($elements as $element) {
            $element->removeDecorator('HtmlTag');
            $element->removeDecorator('Label');
        }
    }

}

