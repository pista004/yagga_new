<?php

class Admin_Form_EditVariantForm extends Zend_Form {

    public function init() {
        
    }

    public function startForm() {

        $this->setMethod(self::METHOD_POST);

        $name = $this->createElement('text', 'variant_name');
        $name->setLabel('Název varianty')
                ->setOptions(array('class' => 'form-control'))
                ->setRequired(true);


        $stock = $this->createElement('text', 'variant_stock');
        $stock->setLabel('Ks skladem')
                ->setOptions(array('class' => 'form-control'))
                ->setRequired(true)
                ->addValidator('Digits');

        $purchase_price = $this->createElement('text', 'variant_purchase_price');
        $purchase_price->setLabel('Nákupní cena')
                ->setOptions(array('class' => 'form-control'))
//                ->setRequired(true)
                ->addValidator('Digits');

        $price = $this->createElement('text', 'variant_price');
        $price->setLabel('Cena')
                ->setOptions(array('class' => 'form-control'))
//                ->setRequired(true)
                ->addValidator('Digits');

        $is_active = $this->createElement('checkbox', 'variant_is_active');
        $is_active->setOptions(array('class' => 'form-control'))
                ->setLabel('Aktivní');



        $submit = $this->createElement('submit', 'submit');
        $submit->setLabel('Uložit')
                ->removeDecorator('DtDdWrapper')
                ->setOptions(array('class' => 'btn btn-primary', 'id' => 'edit-variant'));

        $this->addElements(array(
            $name,
            $stock,
            $purchase_price,
            $price,
            $is_active,
            $submit
        ));


        $elements = $this->getElements();
        foreach ($elements as $element) {
            $element->removeDecorator('HtmlTag');
            $element->removeDecorator('Label');
        }
    }

    /*
     * prida subform do formu - pouzito pri naplneni pole variantami
     */

    // $number - 1st, 2nd, 3rd... subform
    // $data - data to populate
    public function addConfigSubForm($num, $data) {
        $configsForm = new SubForm();
        // populate it with $data
        $configsForm->setVariantCount($num);

        $configsForm->startForm();
        $configsForm->populate($data);

        // add it to the form
        $this->addSubForm($configsForm, 'variant_' . $num);
    }


}

class SubForm extends Zend_Form_SubForm {

    private $_variant_count;

    //setter pro nastaveni cisla varianty ve formulari
    public function setVariantCount($variant_count) {
        $this->_variant_count = $variant_count;
    }

    public function getVariantCount() {
        return $this->_variant_count;
    }

    public function init() {
        
    }

    public function startForm() {
        $variant_name = 'variant_' . $this->getVariantCount();

        $name = $this->createElement('text', 'variant_name');
        $name->setLabel('Název varianty')
                ->setOptions(array('class' => 'form-control variant_name', 'id' => $variant_name . '_name'))
                ->setBelongsTo($variant_name)
                ->setRequired(true);


        $stock = $this->createElement('text', 'variant_stock');
        $stock->setLabel('Ks skladem')
                ->setOptions(array('class' => 'form-control', 'id' => $variant_name . '_stock'))
                ->setBelongsTo($variant_name)
                ->setRequired(true)
                ->addValidator('Digits');

        $purchase_price = $this->createElement('text', 'variant_purchase_price');
        $purchase_price->setLabel('Nákupní cena')
                ->setOptions(array('class' => 'form-control', 'id' => $variant_name . '_purchase-price'))
                ->setBelongsTo($variant_name)
//                ->setRequired(true)
                ->addValidator('Digits');

        $price = $this->createElement('text', 'variant_price');
        $price->setLabel('Cena')
                ->setOptions(array('class' => 'form-control', 'id' => $variant_name . '_price'))
                ->setBelongsTo($variant_name)
//                ->setRequired(true)
                ->addValidator('Digits');

        $is_active = $this->createElement('checkbox', 'variant_is_active');
        $is_active->setOptions(array('class' => 'form-control', 'id' => $variant_name . '_is-active'))
                ->setBelongsTo($variant_name)
                ->setLabel('Aktivní');
        
                //hidden input pro rozliseni elementu pridanych ajaxem
        $hidden_id = $this->createElement('hidden', 'variant_id');
        $hidden_id->setValue($this->getVariantCount())
        ->setBelongsTo($variant_name);


        $this->addElements(array(
            $name,
            $stock,
            $purchase_price,
            $price,
            $is_active,
            $hidden_id
        ));

        $elements = $this->getElements();
        foreach ($elements as $element) {
            $element->removeDecorator('HtmlTag');
            $element->removeDecorator('Label');
        }
    }

}