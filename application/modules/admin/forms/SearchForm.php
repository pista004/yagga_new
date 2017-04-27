<?php

class Admin_Form_SearchForm extends Zend_Form {

    public function init() {
        $this->setMethod(self::METHOD_GET);
        $this->setAction('/admin/search');

        $searchTerm = $this->createElement('text', 'term');
        $searchTerm->setLabel('Hledat')
                ->setOptions(array('class' => 'form-control'));

        $password = $this->createElement('password', 'admin_password');
        $password->setLabel('Heslo')
                ->setOptions(array('class' => 'form-control'))
                ->setRequired(true);

        $submit = $this->createElement('submit', 'submit');
        $submit->setLabel('Hledat')
                ->setOptions(array('class' => 'btn btn-default'))
                ->removeDecorator('DtDdWrapper')
                ->setIgnore(true);

        $this->addElements(array(
            $searchTerm,
            $submit
        ));


        $elements = $this->getElements();
        foreach ($elements as $element) {
            $element->removeDecorator('HtmlTag');
            $element->removeDecorator('Label');
        }
    }

}
