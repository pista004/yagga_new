<?php

class Admin_Form_EditUserForm extends Zend_Form {

    private $_country = array(1 => "Česká republika");

    public function getCountry() {
        return $this->_country;
    }
    
    public function init() {}
    
    public function startForm() {
        $this->setMethod(self::METHOD_POST);

        $email = $this->createElement('text', 'user_login');
        $email->setLabel('Email')
                ->setOptions(array('class' => 'form-control'))
                ->addValidator('EmailAddress')
                ->setRequired(true);

        $password = $this->createElement('password', 'user_password');
        $password->setLabel('Heslo')
                ->setOptions(array('class' => 'form-control'))
                ->setRequired(true);

        /*
         * Fakturacni udaj
         */

        $i_name = $this->createElement('text', 'user_profile_i_name');
        $i_name->setLabel('Jméno')
                ->setOptions(array('class' => 'form-control'))
                ->setRequired(true);

        $i_surname = $this->createElement('text', 'user_profile_i_surname');
        $i_surname->setLabel('Příjmení')
                ->setOptions(array('class' => 'form-control'))
                ->setRequired(true);

        $phone = $this->createElement('text', 'user_profile_phone');
        $phone->setLabel('Telefon')
                ->setOptions(array('class' => 'form-control'))
                ->setRequired(true);

        $i_street = $this->createElement('text', 'user_profile_i_street');
        $i_street->setLabel('Ulice a číslo')
                ->setOptions(array('class' => 'form-control'))
                ->setRequired(true);

        $i_city = $this->createElement('text', 'user_profile_i_city');
        $i_city->setLabel('Město')
                ->setOptions(array('class' => 'form-control'))
                ->setRequired(true);

        $i_psc = $this->createElement('text', 'user_profile_i_zip_code');
        $i_psc->setLabel('PSČ')
                ->setOptions(array('class' => 'form-control'))
                ->setRequired(true);

        $i_country = $this->createElement('select', 'user_profile_i_country_id');
        $i_country->setLabel('Stát')
                ->setOptions(array('class' => 'form-control'))
                ->addMultiOptions($this->getCountry());

        $i_company = $this->createElement('text', 'user_profile_i_company');
        $i_company->setLabel('Firma')
                ->setOptions(array('class' => 'form-control'));

        $i_ico = $this->createElement('text', 'user_profile_i_ico');
        $i_ico->setLabel('IČO')
                ->setOptions(array('class' => 'form-control'));

        $i_dic = $this->createElement('text', 'user_profile_i_dic');
        $i_dic->setLabel('DIČ')
                ->setOptions(array('class' => 'form-control'));



        /*
         * Dodaci udaj
         */
        $d_name = $this->createElement('text', 'user_profile_d_name');
        $d_name->setLabel('Jméno')
                ->setOptions(array('class' => 'form-control'));

        $d_surname = $this->createElement('text', 'user_profile_d_surname');
        $d_surname->setLabel('Příjmení')
                ->setOptions(array('class' => 'form-control'));


        $d_company = $this->createElement('text', 'user_profile_d_company');
        $d_company->setLabel('Firma')
                ->setOptions(array('class' => 'form-control'));

        $d_street = $this->createElement('text', 'user_profile_d_street');
        $d_street->setLabel('Ulice a číslo')
                ->setOptions(array('class' => 'form-control'));

        $d_city = $this->createElement('text', 'user_profile_d_city');
        $d_city->setLabel('Město')
                ->setOptions(array('class' => 'form-control'));

        $d_psc = $this->createElement('text', 'user_profile_d_zip_code');
        $d_psc->setLabel('PSČ')
                ->setOptions(array('class' => 'form-control'));

        $d_country = $this->createElement('select', 'user_profile_d_country_id');
        $d_country->setLabel('Stát')
                ->setOptions(array('class' => 'form-control'))
                ->addMultiOptions($this->getCountry());




        $newsletter = $this->createElement('checkbox', 'user_newsletter');
        $newsletter->setOptions(array('class' => 'form-control'))
                ->setLabel('Newsletter');
        
        
        $admin_note = $this->createElement('textarea', 'user_profile_admin_note');
        $admin_note->setLabel('Poznámka administrátora')
                ->setOptions(array('class' => 'form-control'))
                ->setAttribs(array('cols' => '40', 'rows' => '2'));


        $submit = $this->createElement('submit', 'submit');
        $submit->setLabel('Uložit')
                ->removeDecorator('DtDdWrapper')
                ->setOptions(array('class' => 'btn btn-primary'));


        $this->addElements(array(
            $email,
            $password,
            $i_name,
            $i_surname,
            $phone,
            $i_street,
            $i_city,
            $i_psc,
            $i_country,
            $i_company,
            $i_ico,
            $i_dic,
            $d_name,
            $d_surname,
            $d_company,
            $d_street,
            $d_city,
            $d_psc,
            $d_country,
            $newsletter,
            $admin_note,
            $submit
        ));

        $elements = $this->getElements();
        foreach ($elements as $element) {
            $element->removeDecorator('HtmlTag');
            $element->removeDecorator('Label');
        }
    }

}
