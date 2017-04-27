<?php

class Admin_Form_EditPhotographyForm extends Zend_Form {

    private $_product_id;
    
    public function setProductId($product_id){
        $this->_product_id = $product_id;
    }
    
    public function init() {
        
    }

    public function startForm() {

        $this->setMethod(self::METHOD_POST);
        
        $image = $this->createElement('file', 'file');
        $image->setLabel('Obrázek')
                ->addValidator('Extension', false, 'jpg,png,gif')
                ->getValidator('Extension')->setMessage('Tento typ souboru není podporován.');

        
        $photography_note = $this->createElement('text', 'photography_note');
        $photography_note->setLabel('Popis')
                ->setOptions(array('class' => 'form-control'));
        
        $submit_image = $this->createElement('button', 'submit_image');
        $submit_image->setLabel('Uložit')
                ->removeDecorator('DtDdWrapper')
                ->setOptions(array('class' => 'btn btn-success', 'id' => 'add-photography', 'data-product' => $this->_product_id));

        
        $this->addElements(array(
            $image,
            $photography_note,
            $submit_image
        ));


        $elements = $this->getElements();
        foreach ($elements as $element) {
            $element->removeDecorator('HtmlTag');
            $element->removeDecorator('Label');
        }
    }

}
