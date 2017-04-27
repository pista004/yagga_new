<?php

class Zend_View_Helper_AppendParams  extends Zend_View_Helper_Abstract {
   //put your code here

   public function appendParams($url ,$params) {
//      Zend_Debug::dump($params);die;
//      echo $url."<br />";
//      echo print_r($params)."<br />";
//      die;
       $params_string = '';
      $params_ary = array();
      if(count($params)) {
         foreach($params as  $name=>$value) {

            if(is_array($value)) {
               foreach($value as $val) {
                  $params_ary[]= urlencode($name). '[]=' . urlencode($val);
               }
            }
            else {
               $params_ary[]= urlencode($name) . '=' . urlencode($value);
            }
         }

         return $url . '?' . implode('&amp;', $params_ary);
      }
      return $url;
   }

}



//class Zend_View_Helper_StockInfo extends Zend_View_Helper_FormElement
//{
//    public function stockInfo($stock)
//    {
//        
//        $return = "<span class='unavailable'>Na dotaz</span>";
//        if($stock > 0){
//            $return = "<span class='available'>Skladem</span>";
//        }
//
//        return $return;
//    }
//}

?>
