<?php
class Zend_View_Helper_StockInfo extends Zend_View_Helper_Abstract
{
    public function stockInfo($stock)
    {
        
        $return = "<span class='unavailable'>Dostupnost na dotaz</span>";
        if($stock > 0){
            $return = "<link itemprop='availability' href='http://schema.org/InStock'/><span class='available'>Skladem</span>";
            
        }

        return $return;
    }
}
?>
