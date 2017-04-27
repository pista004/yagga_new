<?php

/*
 * helper pro vypis variant
 */

class Zend_View_Helper_VariantsList extends Zend_View_Helper_Abstract {
    
    /*
     * vypis variant ve vypisu produktu
     */
    public function variantsList($variants, $productUrl) {

        $return = "";

        if (!empty($variants)) {
            $return = "<ul class='variants-list'>";
            foreach ($variants as $variant) {
                $cssVariantClass = "variant-unavailable";

                if ($variant->getVariant_stock() > 0) {
                    $cssVariantClass = "variant-available";
                }

                $return .= "<li class='".$cssVariantClass."'>";

                
                $return .= "<a href='".$productUrl."?variant=" . $this->view->escape($variant->getVariant_id()) ."'>" . $this->view->escape($variant->getVariant_name()) . "</a>";
                $return .= "</li>";
            }
            $return .= "</ul>";
        }
        
        return $return;
    }

}
?>
