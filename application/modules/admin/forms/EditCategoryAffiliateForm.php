<?php

class Admin_Form_EditCategoryAffiliateForm extends Zend_Form {

    private $_categories;

    public function setCategories($categories) {
        $this->_categories = $categories;
    }


    public function init() {
        
    }

    public function startForm() {

        $this->setMethod(self::METHOD_POST);


        $category = $this->createElement('select', 'category_id');
        $category->setLabel('Kategorie')
                ->setOptions(array('class' => 'form-control'))
                ->addMultiOptions($this->_categories);


        $submit = $this->createElement('submit', 'submit');
        $submit->setLabel('Uložit')
                ->removeDecorator('DtDdWrapper')
                ->setOptions(array('class' => 'btn btn-primary'));
        $this->addElement($submit);

        $this->addElements(array(
            $category,
            $submit,
        ));

        $elements = $this->getElements();
        foreach ($elements as $element) {
            $element->removeDecorator('HtmlTag');
            $element->removeDecorator('Label');
        }
    }

}

