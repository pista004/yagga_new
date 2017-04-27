<?php

class Admin_Form_EditProductForm extends Zend_Form {

    private $_manufacturer = array();
    private $_category = array();
    private $_heurekaCategory = array();
    private $_url;
//    private $_category;
    protected $_translator;

    /*
     * settery pro prijem dat z controlleru
     */

    public function setManufacturer($manufacturer) {
        $this->_manufacturer = $manufacturer;
    }

    public function setCategory($category) {
        $this->_category = $category;
    }

    public function setHeurekaCategory($heurekaCategory) {
        $this->_heurekaCategory = $heurekaCategory;
    }

    public function setUrl($url) {
        $this->_url = $url;
    }

    public function init() {
        
    }

    public function startForm() {

        $this->setMethod(self::METHOD_POST);

        $validatorDigits = new Zend_Validate_Digits();


        $name = $this->createElement('text', 'product_name');
        $name->setLabel('Název')
                ->setOptions(array('class' => 'form-control'))
                ->setRequired(true);

        $variant_name = $this->createElement('text', 'product_variant_name');
        $variant_name->setLabel('Krátký název varianty')
                ->setOptions(array('class' => 'form-control'))
                ->setRequired(true);

        $stock = $this->createElement('text', 'product_stock');
        $stock->setLabel('Ks')
                ->setOptions(array('class' => 'form-control'))
                ->addValidator($validatorDigits, true);


        $purchasePriceValidatorGreaterThan = new Zend_Validate_GreaterThan(-1);

        $purchase_price = $this->createElement('text', 'product_purchase_price');
        $purchase_price->setLabel('Nákupní cena')
                ->setOptions(array('class' => 'form-control'))
                ->addValidator($validatorDigits, true)
                ->addValidator($purchasePriceValidatorGreaterThan, true);


        $recommendedPriceValidatorGreaterThan = new Zend_Validate_GreaterThan(-1);
        
        $recommended_price = $this->createElement('text', 'product_recommended_price');
        $recommended_price->setLabel('Doporučená cena')
                ->setOptions(array('class' => 'form-control'))
                ->addValidator($validatorDigits, true)
                ->addValidator($recommendedPriceValidatorGreaterThan, true);


        $priceValidatorGreaterThan = new Zend_Validate_GreaterThan(0);

        $price = $this->createElement('text', 'product_price');
        $price->setLabel('Cena')
                ->setOptions(array('class' => 'form-control'))
                ->setRequired(true)
                ->addValidator($validatorDigits, true)
                ->addValidator($priceValidatorGreaterThan, true);


        $code = $this->createElement('text', 'product_code');
        $code->setLabel('Kód produktu')
                ->setOptions(array('class' => 'form-control'));

        $ean = $this->createElement('text', 'product_ean');
        $ean->setLabel('Ean')
                ->setOptions(array('class' => 'form-control'));

        $seoTitle = $this->createElement('text', 'product_seo_title');
        $seoTitle->setLabel('SEO titulek')
                ->setOptions(array('class' => 'form-control'));

        $seoMetaDescription = $this->createElement('textarea', 'product_seo_meta_description');
        $seoMetaDescription->setLabel('SEO meta description')
                ->setOptions(array('class' => 'form-control'))
                ->setAttribs(array('cols' => '40', 'rows' => '4'));

        $perex = $this->createElement('textarea', 'product_perex');
        $perex->setLabel('Perex')
                ->setOptions(array('class' => 'form-control'))
                ->setAttribs(array('cols' => '40', 'rows' => '4'));



//        $db_lookup_validator = new Zend_Validate_Db_NoRecordExists(array(
//                    'table' => 'product',
//                    'field' => 'product_url',
//                    'exclude' => 'product_url != "' . $this->_url . '"'
//                ));

        $url = $this->createElement('text', 'product_url');
        $url->setLabel('Url')
//                ->addValidator($db_lookup_validator)
                ->setOptions(array('class' => 'form-control'));


        //overeni, jestli se select nerovna nule
//        $required = new Zend_Validate_NotEmpty();
//        $required->setType($required->getType() | Zend_Validate_NotEmpty::INTEGER | Zend_Validate_NotEmpty::ZERO);
        $manufacturer = $this->createElement('select', 'product_manufacturer_id');
        $manufacturer->setLabel('Výrobce')
                ->setOptions(array('class' => 'form-control'))
                ->addMultiOptions($this->_manufacturer);
//        $manufacturer->setRequired(true);
//        $manufacturer->addValidator($required);

        $description = $this->createElement('textarea', 'product_description');
        $description->setOptions(array('class' => 'form-control ckeditor'))
                ->setAttribs(array('cols' => '40', 'rows' => '4'))
                ->setLabel('Popis');


        $monthNames = array("Leden", "Únor", "Březen", "Duben", "Květen", "Červen", "Červenec", "Srpen", "Září", "Říjen", "Listopad", "Prosinec");
        $dayNames = array("Ne", "Po", "Út", "St", "Čt", "Pá", "So");

        $expire_date = new ZendX_JQuery_Form_Element_DatePicker('product_expire_date',
                        array('jQueryParams' => array(
                                'dateFormat' => 'dd. mm. yy',
                                'monthNames' => $monthNames,
                                'dayNamesMin' => $dayNames,
                                'minDate' => 0,
                                'firstDay' => 1,
                        ))
        );
        $expire_date->setLabel('Platné do');
        $expire_date->setOptions(array('class' => 'form-control'));

        //kontrola data, sice existuj validator Zend_Validate_Date, ale je tam bug
        $regex = new Zend_Validate_Regex(array('pattern' => '#^(0[1-9]|[12][0-9]|3[01])(. )(0[1-9]|1[012])(. )(19|20)\d\d#'));
        $regex->setMessage('Datum musí být ve formátu den. měsíc. rok (např.: 01. 01. 2013)');
        $expire_date->addValidator($regex, true);
        $expire_date->setOptions(array('placeholder' => 'Datum'));


        $active_from_date = new ZendX_JQuery_Form_Element_DatePicker('product_active_from_date',
                        array('jQueryParams' => array(
                                'dateFormat' => 'dd. mm. yy',
                                'monthNames' => $monthNames,
                                'dayNamesMin' => $dayNames,
                                'minDate' => 0,
                                'firstDay' => 1,
                        ))
        );
        $active_from_date->setLabel('Zobrazovat od');
        $active_from_date->setOptions(array('class' => 'form-control'));

        //kontrola data, sice existuj validator Zend_Validate_Date, ale je tam bug
        $regex = new Zend_Validate_Regex(array('pattern' => '#^(0[1-9]|[12][0-9]|3[01])(. )(0[1-9]|1[012])(. )(19|20)\d\d#'));
        $regex->setMessage('Datum musí být ve formátu den. měsíc. rok (např.: 01. 01. 2013)');
        $active_from_date->addValidator($regex, true);
        $active_from_date->setOptions(array('placeholder' => 'Datum'));

        $category = $this->createElement('multiCheckbox', 'product_category');
        $category->setLabel('Kategorie')
                ->addMultiOptions($this->_category);


        $heurekaCategory = $this->createElement('select', 'product_category_heureka');
        $heurekaCategory->setLabel('Heureka kategorie')
                ->setOptions(array('class' => 'form-control'))
                ->addMultiOptions($this->_heurekaCategory);


        $action = $this->createElement('checkbox', 'product_action');
        $action->setOptions(array('class' => 'form-control'))
                ->setLabel('Akce');

        $sale = $this->createElement('checkbox', 'product_sale');
        $sale->setOptions(array('class' => 'form-control'))
                ->setLabel('Výprodej');

        $new = $this->createElement('checkbox', 'product_new');
        $new->setOptions(array('class' => 'form-control'))
                ->setLabel('Novinka');

        $recommend = $this->createElement('checkbox', 'product_recommend');
        $recommend->setOptions(array('class' => 'form-control'))
                ->setLabel('Doporučujeme');

        $is_active = $this->createElement('checkbox', 'product_is_active');
        $is_active->setOptions(array('class' => 'form-control'))
                ->setLabel('Aktivní');


        $submit = $this->createElement('submit', 'submit');
        $submit->setLabel('Uložit')
                ->removeDecorator('DtDdWrapper')
                ->setOptions(array('class' => 'btn btn-primary'));


        $this->addElements(array(
            $name,
            $variant_name,
            $stock,
            $price,
            $recommended_price,
            $purchase_price,
            $code,
            $ean,
            $seoTitle,
            $seoMetaDescription,
            $perex,
            $url,
            $manufacturer,
            $description,
            $active_from_date,
            $expire_date,
            $category,
            $heurekaCategory,
            $action,
            $sale,
            $new,
            $recommend,
            $is_active,
            $submit
        ));


        $elements = $this->getElements();
        foreach ($elements as $element) {
            $element->removeDecorator('HtmlTag');
            $element->removeDecorator('Label');
        }
    }

}

