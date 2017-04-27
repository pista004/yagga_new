<?php

class Admin_Form_RegistrationForm extends Zend_Form {
    
    public function init() {
        $this->setMethod(self::METHOD_POST);                    
        $this->setOptions(array('class' => 'form-signin'));
        
        $email = $this->createElement('text', 'admin_email');
        $email->setLabel('Email')
                ->setOptions(array('class' => 'form-control'))
                ->setRequired(true)
                ->addValidator('EmailAddress')
                ->addErrorMessage('Chybně zadaný email');
        
        $password = $this->createElement('password', 'admin_password');
        $password->setLabel('Heslo')
                ->setOptions(array('class' => 'form-control'))
                ->setRequired(true);
        $password->addValidator(new Zend_Validate_StringLength(array('min'=>6)));
        
        $password_confirm = $this->createElement('password', 'password_confirm');
        $password_confirm->setLabel('Heslo znovu')
                ->setOptions(array('class' => 'form-control'))
                ->setRequired(true)
                ->addValidator(new Zend_Validate_Identical('admin_password'));
        $password_confirm->addValidator(new Zend_Validate_StringLength(array('min'=>6)));


        $signin = $this->createElement('submit', 'submit');
        $signin->setLabel('Registrovat')
                ->setOptions(array('class' => 'btn btn-lg btn-primary btn-block'))
                ->setIgnore(true);

        $this->addElements(array(
            $email,
            $password,
            $password_confirm,
            $signin
        ));

    }

}
