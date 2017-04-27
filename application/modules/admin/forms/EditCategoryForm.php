<?php

class Admin_Form_EditCategoryForm extends Zend_Form {

    private $_category;
    private $_heurekaCategory;
    private $_url;

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

        $name = $this->createElement('text', 'category_name');
        $name->setLabel('Název')
                ->setOptions(array('class' => 'form-control'))
                ->setRequired(true);

        $description = $this->createElement('textarea', 'category_description');
        $description->setOptions(array('class' => 'form-control'))
                ->setAttrib('cols', '40')
                ->setAttrib('rows', '4')
                ->setLabel('Popis');

        $parent = $this->createElement('select', 'category_parent');
        $parent->setLabel('Nadřazená kategorie')
                ->setOptions(array('class' => 'form-control'))
                ->addMultiOptions($this->_category);


        $is_active = $this->createElement('checkbox', 'category_is_active');
        $is_active->setOptions(array('class' => 'form-control'))
                ->setLabel('Aktivní');

//        $url = $this->createElement('text', 'category_url');
//        $url->setLabel('Url')
//                ->setOptions(array('class' => 'form-control'));

        
        $db_lookup_validator = new Zend_Validate_Db_NoRecordExists(array(
                    'table' => 'category',
                    'field' => 'category_url',
                    'exclude' => 'category_url != "'.$this->_url.'"'
                ));
        
        $url = $this->createElement('text', 'category_url');
        $url->setLabel('Url')
                ->addValidator($db_lookup_validator)
                ->setOptions(array('class' => 'form-control'));
        
        $h1 = $this->createElement('text', 'category_h1');
        $h1->setLabel('H1 nadpis')
                ->setOptions(array('class' => 'form-control'));
        
        
        $seo_title = $this->createElement('text', 'category_seo_title');
        $seo_title->setLabel('Titulek')
                ->setOptions(array('class' => 'form-control'));

        $seo_meta_description = $this->createElement('textarea', 'category_seo_meta_description');
        $seo_meta_description->setOptions(array('class' => 'form-control'))
                ->setAttrib('cols', '40')
                ->setAttrib('rows', '4')
                ->setLabel('META description');
        
        $heurekaCategory = $this->createElement('select', 'category_category_heureka');
        $heurekaCategory->setLabel('Heureka kategorie')
                ->setOptions(array('class' => 'form-control'))
                ->addMultiOptions($this->_heurekaCategory);
        
        $order = $this->createElement('text', 'category_order');
        $order->setLabel('Pořadí')
                ->setOptions(array('class' => 'form-control'))
                ->setValue(0)
                ->setValidators(array(new Zend_Validate_Digits()));

        $submit = $this->createElement('submit', 'submit');
        $submit->setLabel('Uložit')
                ->removeDecorator('DtDdWrapper')
                ->setOptions(array('class' => 'btn btn-primary'));
        $this->addElement($submit);

        $this->addElements(array(
            $name,
            $description,
            $parent,
            $is_active,
            $url,
            $h1,
            $seo_title,
            $seo_meta_description,
            $heurekaCategory,
            $order,
            $submit,
        ));

        $elements = $this->getElements();
        foreach ($elements as $element) {
            $element->removeDecorator('HtmlTag');
            $element->removeDecorator('Label');
        }
    }

}

