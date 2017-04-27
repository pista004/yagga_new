<?php

class Zend_View_Helper_SaleInfo extends Zend_View_Helper_Abstract {

    public function saleInfo($price, $recommendPrice) {
        //zobrazovat jen slevy vetsi nebo rovno 10%
        $saleLimit = 10;

        $return = 0;

        //vypocitam slevu a poslu do view
        if ($recommendPrice > $price) {
            $sale = 0;
            $sale = (1 - ($price / $recommendPrice)) * 100;
            if ($sale > 0 && $sale >= $saleLimit) {
                $return = $sale;
            }
        }


        return (int)$return;
    }

}

?>
