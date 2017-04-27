<?php

class Admin_Form_EditArticleForm extends Zend_Form {

    private $_url;
    private $_article_types;

    public function setUrl($url) {
        $this->_url = $url;
    }

    public function setArticle_types($article_types) {
        $this->_article_types = $article_types;
    }

    public function init() {
        
    }

    public function startForm() {

        $this->setMethod(self::METHOD_POST);

        $article_type = $this->createElement('select', 'article_article_type_id');
        $article_type->setLabel('Zařazení')
                ->setOptions(array('class' => 'form-control'))
                ->addMultiOptions($this->_article_types);

        
        $image = $this->createElement('file', 'file');
        $image->setLabel('Obrázek (nejlépe 930x465px)')
                ->addValidator('Extension', false, 'jpg,png,gif')
                ->getValidator('Extension')->setMessage('Tento typ souboru není podporován.');
        
        
        $name = $this->createElement('text', 'article_name');
        $name->setLabel('Nadpis')
                ->setOptions(array('class' => 'form-control'))
                ->setRequired(true);



        $monthNames = array("Leden", "Únor", "Březen", "Duben", "Květen", "Červen", "Červenec", "Srpen", "Září", "Říjen", "Listopad", "Prosinec");
        $dayNames = array("Ne", "Po", "Út", "St", "Čt", "Pá", "So");

        $insert_date = new ZendX_JQuery_Form_Element_DatePicker('article_insert_date',
                        array('jQueryParams' => array(
                                'dateFormat' => 'dd. mm. yy',
                                'monthNames' => $monthNames,
                                'dayNamesMin' => $dayNames,
                                'minDate' => 0,
                                'firstDay' => 1,
                        ))
        );
        $insert_date->setLabel('Vloženo');
        $insert_date->setOptions(array('class' => 'form-control'));

        //kontrola data, sice existuj validator Zend_Validate_Date, ale je tam bug
        $regex = new Zend_Validate_Regex(array('pattern' => '#^(0[1-9]|[12][0-9]|3[01])(. )(0[1-9]|1[012])(. )(19|20)\d\d#'));
        $regex->setMessage('Datum musí být ve formátu den. měsíc. rok (např.: 01. 01. 2013)');
        $insert_date->addValidator($regex, true);


        $active_from_date = new ZendX_JQuery_Form_Element_DatePicker('article_active_from_date',
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


        $db_lookup_validator = new Zend_Validate_Db_NoRecordExists(array(
                    'table' => 'article',
                    'field' => 'article_url',
                    'exclude' => 'article_url != "' . $this->_url . '"'
                ));

        $url = $this->createElement('text', 'article_url');
        $url->setLabel('Url')
                ->addValidator($db_lookup_validator)
                ->setOptions(array('class' => 'form-control'));



        $seoTitle = $this->createElement('text', 'article_seo_title');
        $seoTitle->setLabel('SEO titulek')
                ->setOptions(array('class' => 'form-control'));


        $seoMetaDescription = $this->createElement('textarea', 'article_seo_meta_description');
        $seoMetaDescription->setLabel('SEO meta description')
                ->setOptions(array('class' => 'form-control'))
                ->setAttribs(array('cols' => '40', 'rows' => '4'));


        $perex = $this->createElement('textarea', 'article_perex');
        $perex->setOptions(array('class' => 'form-control ckeditor'))
                ->setAttribs(array('cols' => '40', 'rows' => '4'))
                ->setLabel('Perex');

        $text = $this->createElement('textarea', 'article_text');
        $text->setOptions(array('class' => 'form-control ckeditor'))
                ->setAttribs(array('cols' => '40', 'rows' => '4'))
                ->setLabel('Text')
                ->setRequired(true);


        $is_active = $this->createElement('checkbox', 'article_is_active');
        $is_active->setOptions(array('class' => 'form-control'))
                ->setLabel('Aktivní');


        $submit = $this->createElement('submit', 'submit');
        $submit->setLabel('Uložit')
                ->removeDecorator('DtDdWrapper')
                ->setOptions(array('class' => 'btn btn-primary'));


        $this->addElements(array(
            $article_type,
            $image,
            $name,
            $insert_date,
            $active_from_date,
            $url,
            $seoTitle,
            $seoMetaDescription,
            $perex,
            $text,
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

