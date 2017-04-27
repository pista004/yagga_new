<?php

class Admin_Form_EditManufacturerForm extends Zend_Form {

    private $_url;
    
    public function setUrl($url) {
        $this->_url = $url;
    }
    
    public function init() {
        
    }

    public function startForm() {

        $this->setMethod(self::METHOD_POST);

        $name = $this->createElement('text', 'manufacturer_name');
        $name->setLabel('Název')
                ->setOptions(array('class' => 'form-control'))
                ->setRequired(true);
        
        $image = $this->createElement('file', 'file');
        $image->setLabel('Obrázek (nejlépe 80x80px)')
                ->addValidator('Extension', false, 'jpg,png,gif')
                ->getValidator('Extension')->setMessage('Tento typ souboru není podporován.');
        
        
        $db_lookup_validator = new Zend_Validate_Db_NoRecordExists(array(
                    'table' => 'manufacturer',
                    'field' => 'manufacturer_url',
                    'exclude' => 'manufacturer_url != "' . $this->_url . '"'
                ));

        $url = $this->createElement('text', 'manufacturer_url');
        $url->setLabel('Url')
                ->addValidator($db_lookup_validator)
                ->setOptions(array('class' => 'form-control'));
        

        $note = $this->createElement('textarea', 'manufacturer_note');
        $note->setOptions(array('class' => 'form-control ckeditor'))
                ->setAttrib('cols', '40')
                ->setAttrib('rows', '4')
                ->setLabel('Popis');
        
        
        $seoTitle = $this->createElement('text', 'manufacturer_seo_title');
        $seoTitle->setLabel('SEO titulek')
                ->setOptions(array('class' => 'form-control'));
        
        
        $seoMetaDescription = $this->createElement('textarea', 'manufacturer_seo_meta_description');
        $seoMetaDescription->setLabel('SEO meta description')
                ->setOptions(array('class' => 'form-control'))
                ->setAttribs(array('cols' => '40', 'rows' => '4'));
        
        
        $is_active = $this->createElement('checkbox', 'manufacturer_is_active');
        $is_active->setOptions(array('class' => 'form-control'))
                ->setLabel('Aktivní');
        

        $submit = $this->createElement('submit', 'submit');
        $submit->setLabel('Uložit')
                ->removeDecorator('DtDdWrapper')
                ->setOptions(array('class' => 'btn btn-primary'));
        $this->addElement($submit);

        $this->addElements(array(
            $name,
            $image,
            $url,
            $note,
            $seoTitle,
            $seoMetaDescription,
            $is_active,
            $submit,
        ));

        $elements = $this->getElements();
        foreach ($elements as $element) {
            $element->removeDecorator('HtmlTag');
            $element->removeDecorator('Label');
        }
    }

}

