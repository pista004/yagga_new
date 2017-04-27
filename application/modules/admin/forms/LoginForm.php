<?php

class Admin_Form_LoginForm extends Zend_Form {

    public function init() {
        $this->setMethod(self::METHOD_POST);
        $this->setOptions(array('class' => 'form-signin'));

        $email = $this->createElement('text', 'admin_email');
        $email->setLabel('Email')
                ->setOptions(array('class' => 'form-control'))
                ->setRequired(true);

        $password = $this->createElement('password', 'admin_password');
        $password->setLabel('Heslo')
                ->setOptions(array('class' => 'form-control'))
                ->setRequired(true);
        
        $signin = $this->createElement('submit', 'submit');
        $signin->setLabel('Přihlásit')
                ->setOptions(array('class' => 'btn btn-lg btn-primary btn-block'))
                ->setIgnore(true);

        $this->addElements(array(
            $email,
            $password,
            $signin
        ));
    }

}
