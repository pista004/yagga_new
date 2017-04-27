<?php

class Zend_View_Helper_NumberFormat extends Zend_View_Helper_Abstract {

    public function numberFormat($price, $isVariant = false, $currency = 'Kč', $decimals = 0, $decPoint = ',', $thousandsSep = ' ') {

        $return = "";
        if ($price > 0) {
            $variantText = "";
            if ($isVariant) {
                $variantText = "od ";
            }
            $return = $variantText . '' . number_format($price, $decimals, $decPoint, $thousandsSep) . "&nbsp;" . $currency;
        }

        return $return;
    }

}

?>
