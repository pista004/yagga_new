<?php

class Default_Form_SendKissForm extends Zend_Form {

    public function init() {
        
    }

    public function startForm() {

        $this->setMethod(self::METHOD_POST);


        $sendkiss_sender_name = $this->createElement('text', 'kisssender_sender_name');
        $sendkiss_sender_name->setLabel('Vaše jméno')
                ->setOptions(array('class' => 'form-control'))
                ->setRequired(true);


        $sendkiss_text = $this->createElement('textarea', 'kisssender_text');
        $sendkiss_text->setLabel('Text vzkazu')
                ->setOptions(array('class' => 'form-control'))
                ->setAttribs(array('cols' => '40', 'rows' => '4'))
                ->setRequired(true);


        $sendkiss_email_to = $this->createElement('text', 'kisssender_email_to');
        $sendkiss_email_to->setLabel('Email příjemce')
                ->setOptions(array('class' => 'form-control'))
                ->addValidator('EmailAddress')
                ->addErrorMessage('Email má chybný formát')
                ->setRequired(true);


        $sendkiss_email_from = $this->createElement('text', 'kisssender_email_from');
        $sendkiss_email_from->setLabel('Email odesílatele')
                ->setOptions(array('class' => 'form-control'))
                ->addValidator('EmailAddress')
                ->addErrorMessage('Email má chybný formát');


        $sendkiss_latitude = $this->createElement('hidden', 'kisssender_latitude');
        $sendkiss_latitude->setOptions(array('class' => 'form-control'));
        
        $sendkiss_longitude = $this->createElement('hidden', 'kisssender_longitude');
        $sendkiss_longitude->setOptions(array('class' => 'form-control'));

        $submit = $this->createElement('submit', 'submit');
        $submit->setLabel('Odeslat')
                ->removeDecorator('DtDdWrapper')
                ->setOptions(array('class' => 'btn btn-sendkiss'));


        $this->addElements(array(
            $sendkiss_sender_name,
            $sendkiss_text,
            $sendkiss_email_from,
            $sendkiss_email_to,
            $sendkiss_latitude,
            $sendkiss_longitude,
            $submit
        ));


        $elements = $this->getElements();
        foreach ($elements as $element) {
            $element->removeDecorator('HtmlTag');
            $element->removeDecorator('Label');
        }
    }

}

