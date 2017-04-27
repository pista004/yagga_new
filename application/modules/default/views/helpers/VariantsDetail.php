<?php

/*
 * helper pro vypis variant
 */

class Zend_View_Helper_VariantsDetail extends Zend_View_Helper_Abstract {

    /*
     * vypis variant v detailu produktu
     */
    public function variantsDetail($variants, $selectedVariant = null) {

        $return = "";
//print_r($variants);die;
        if (!empty($variants)) {
                                    
            $return = "<p class='variants-title'>Dostupné varianty:</p>";
            
            $return .= "<ul class='variants'>";
            foreach ($variants as $variant) {
                $cssVariantClass = "variant-unavailable";

                if ($variant->getProduct_stock() > 0) {
                    $cssVariantClass = "variant-available";
                }
                
                
                if ($selectedVariant == $variant->getProduct_id()) {
                    $cssVariantClass .= " variant-selected";
                }


                $return .= "<li class='$cssVariantClass'>";

                
                $return .= "<a href='".$this->view->escape($variant->getProduct_url())."'>" . $this->view->escape($variant->getProduct_variant_name()) . "</a>";
                $return .= "</li>";
            }
            $return .= "</ul>";
        }
        
        return $return;
    }

}
?>
