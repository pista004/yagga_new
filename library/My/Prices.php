<?php

class My_Prices {

    /**
     * Retrun price with currency and thousand separator
     *
     * @param int $price
     * @param boolean $isVariant true, if is variant and have different prices - return number with prefix eg. "from "
     * @param string $currency Kč, Huf, Eur, etc.
     * @param int $decimals number of decimal places
     * @param string $decPoint decimal separator
     * @param string $thousandsSep thousands separator
     * 
     * @return string formated price with currency 
     */
    
    public function getPrice($price, $isVariant = false, $currency = 'Kč', $decimals = 0, $decPoint = ',', $thousandsSep = ' ') {

        $return = "";
        if ($price > 0) {
            $variantText = "";
            if ($isVariant) {
                $variantText = "od ";
            }
            $return = $variantText . '' . number_format($price, $decimals, $decPoint, $thousandsSep) . " " . $currency;
        }

        return $return;
    }
    
    
    

}