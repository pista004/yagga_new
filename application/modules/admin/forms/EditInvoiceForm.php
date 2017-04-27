<?php

class Admin_Form_EditInvoiceForm extends Zend_Form {

    public function init() {
        
    }

    public function startForm() {

        $this->setMethod(self::METHOD_POST);

        $monthNames = array("Leden", "Únor", "Březen", "Duben", "Květen", "Červen", "Červenec", "Srpen", "Září", "Říjen", "Listopad", "Prosinec");
        $dayNames = array("Ne", "Po", "Út", "St", "Čt", "Pá", "So");

        $creating_date = new ZendX_JQuery_Form_Element_DatePicker('invoice_creating_date',
                        array('jQueryParams' => array(
                                'dateFormat' => 'dd. mm. yy',
                                'monthNames' => $monthNames,
                                'dayNamesMin' => $dayNames,
                                'minDate' => 0,
                                'firstDay' => 1,
                        ))
        );
        $creating_date->setLabel('Vystaveno');
        $creating_date->setOptions(array('class' => 'form-control'));

        //kontrola data, sice existuj validator Zend_Validate_Date, ale je tam bug
        $regex = new Zend_Validate_Regex(array('pattern' => '#^(0[1-9]|[12][0-9]|3[01])(. )(0[1-9]|1[012])(. )(19|20)\d\d#'));
        $regex->setMessage('Datum musí být ve formátu den. měsíc. rok (např.: 01. 01. 2013)');
        $creating_date->addValidator($regex, true);
        $creating_date->setOptions(array('placeholder' => 'Datum'));
        $creating_date->setRequired();

        
        $due_date = new ZendX_JQuery_Form_Element_DatePicker('invoice_due_date',
                        array('jQueryParams' => array(
                                'dateFormat' => 'dd. mm. yy',
                                'monthNames' => $monthNames,
                                'dayNamesMin' => $dayNames,
                                'minDate' => 0,
                                'firstDay' => 1,
                        ))
        );
        $due_date->setLabel('Splatnost');
        $due_date->setOptions(array('class' => 'form-control'));

        //kontrola data, sice existuj validator Zend_Validate_Date, ale je tam bug
        $regex = new Zend_Validate_Regex(array('pattern' => '#^(0[1-9]|[12][0-9]|3[01])(. )(0[1-9]|1[012])(. )(19|20)\d\d#'));
        $regex->setMessage('Datum musí být ve formátu den. měsíc. rok (např.: 01. 01. 2013)');
        $due_date->addValidator($regex, true);
        $due_date->setOptions(array('placeholder' => 'Datum'));
        $due_date->setRequired();

        
        $submit = $this->createElement('submit', 'submit');
        $submit->setLabel('Uložit')
                ->removeDecorator('DtDdWrapper')
                ->setOptions(array('class' => 'btn btn-primary'));


        $this->addElements(array(
            $creating_date,
            $due_date,
            $submit
        ));


        $elements = $this->getElements();
        foreach ($elements as $element) {
            $element->removeDecorator('HtmlTag');
            $element->removeDecorator('Label');
        }
    }

}

