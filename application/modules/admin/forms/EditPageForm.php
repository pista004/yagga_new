<?php

class Admin_Form_EditPageForm extends Zend_Form {

    private $_url;

    public function setUrl($url) {
        $this->_url = $url;
    }

    public function init() {
        
    }

    public function startForm() {

        $this->setMethod(self::METHOD_POST);

        $name = $this->createElement('text', 'page_name');
        $name->setLabel('Název')
                ->setOptions(array('class' => 'form-control'))
                ->setRequired(true);

        $db_lookup_validator = new Zend_Validate_Db_NoRecordExists(array(
                    'table' => 'page',
                    'field' => 'page_url',
                    'exclude' => 'page_url != "' . $this->_url . '"'
                ));

        $url = $this->createElement('text', 'page_url');
        $url->setLabel('Url')
                ->addValidator($db_lookup_validator)
                ->setOptions(array('class' => 'form-control'));



        $seoTitle = $this->createElement('text', 'page_seo_title');
        $seoTitle->setLabel('SEO titulek')
                ->setOptions(array('class' => 'form-control'));


        $seoMetaDescription = $this->createElement('textarea', 'page_seo_meta_description');
        $seoMetaDescription->setLabel('SEO meta description')
                ->setOptions(array('class' => 'form-control'))
                ->setAttribs(array('cols' => '40', 'rows' => '4'));


        $text = $this->createElement('textarea', 'page_text');
        $text->setOptions(array('class' => 'form-control ckeditor'))
                ->setAttribs(array('cols' => '40', 'rows' => '4'))
                ->setLabel('Text');


        $is_active = $this->createElement('checkbox', 'page_is_active');
        $is_active->setOptions(array('class' => 'form-control'))
                ->setLabel('Aktivní');


        $submit = $this->createElement('submit', 'submit');
        $submit->setLabel('Uložit')
                ->removeDecorator('DtDdWrapper')
                ->setOptions(array('class' => 'btn btn-primary'));


        $this->addElements(array(
            $name,
            $url,
            $seoTitle,
            $seoMetaDescription,
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

