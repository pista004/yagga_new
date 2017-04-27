<?php

class My_Images {

    private $_awsProductUrl = 'https://swagwear.s3.eu-central-1.amazonaws.com/products/images/';

    /**
     * Retrun <img src='...'> for product set in param
     *
     * @param object $product
     * @param string $imgType image type(list, thumb, original)
     * @param array $imgClass classes for image
     * @return string image in <img> tag.
     */
    public function getProductImage($product, $imgType, $imgClass = array()) {

        $image = "";

        if ($product instanceof Default_Model_Product || $product instanceof Admin_Model_Product) {


            $view = new Zend_View();

            $imgAlt = $view->escape($product->getProduct_name());
            $imgTitle = $view->escape($product->getProduct_name());


            // pro všechny obrázky nastavím img responsive
            $imgClass[] = "img-responsive";

            if (strpos($product->getMain_photography()->getPhotography_path(), 'http://') !== false || strpos($product->getMain_photography()->getPhotography_path(), 'https://') !== false) {

                $imgSrc = $view->escape($product->getMain_photography()->getPhotography_path());

                $image = "<img class='" . $view->escape(implode(" ", $imgClass)) . "' src='" . $imgSrc . "' alt='" . $imgAlt . "' title='" . $imgTitle . "' />";
            } elseif ((strpos($product->getMain_photography()->getPhotography_path(), '_' . $product->getProduct_id() . '.')) !== false) {

                $awsImagepath = $this->_awsProductUrl . $view->escape($product->getProduct_id());

                $imgTypeToUse = "list";

                switch ($imgType) {
                    case 'list':

                        $imgTypeToUse = $imgType;
                        break;
                    case 'original':

                        $imgTypeToUse = $imgType;
                        break;
                    case 'thumb':

                        $imgTypeToUse = $imgType;
                        break;

                    default:

                        $imgTypeToUse = "list";
                        break;
                }

                $image = "<img class='" . $view->escape(implode(" ", $imgClass)) . "' src='" . $awsImagepath . "/" . $imgTypeToUse . "/" . $view->escape($product->getMain_photography()->getPhotography_path()) . "' alt='" . $imgAlt . "' title='" . $imgTitle . "' />";
            } elseif (strpos($product->getMain_photography()->getPhotography_path(), '_' . $product->getProduct_id() . '_') == true) {

                $cloudinary = new My_Cloudinary();
                $image = "<img class='" . $view->escape(implode(" ", $imgClass)) . "' src='" . cloudinary_url($view->escape($product->getMain_photography()->getPhotography_path()), array("width" => 400)) . "' alt='" . $imgAlt . "' title='" . $imgTitle . "' />";
            } else {

                $imgTypeToUse = "list";
                switch ($imgType) {
                    case 'list':

                        $imgTypeToUse = $imgType;
                        break;
                    case 'original':

                        $imgTypeToUse = $imgType;
                        break;
                    case 'thumb':

                        $imgTypeToUse = $imgType;
                        break;

                    default:

                        $imgTypeToUse = $imgType;
                        break;
                }

                $imgPath = IMAGE_UPLOAD_PATH . "/product_" . $view->escape($product->getProduct_id()) . "/" . $imgTypeToUse . "/" . $view->escape($product->getMain_photography()->getPhotography_path());
                $imgSrc = "/images/upload/product_" . $view->escape($product->getProduct_id()) . "/" . $imgTypeToUse . "/" . $view->escape($product->getMain_photography()->getPhotography_path());

                if (file_exists($imgPath)) {
                    $image = "<img class='" . $view->escape(implode(" ", $imgClass)) . "' src='" . $imgSrc . "' alt='" . $imgAlt . "' title= '" . $imgTitle . "' />";
                }
            }
        }
        
        return (string) $image;
    }

    /**
     * Retrun image path by product ID, photo path and image type for use in product list or product detail
     *
     * @param int $productId
     * @param array $imgPath image path/name
     * @param string $imgType image type(list, thumb, original)

     * @return string image path 
     */
    public function getProductImagePath($productId, $imgPath, $imgType) {

        $view = new Zend_View();

        $image = "";

        if (strpos($imgPath, 'http://') !== false || strpos($imgPath, 'https://') !== false) {

            $image = $imgPath;
        } elseif ((strpos($imgPath, '_' . $productId . '.')) !== false) {

            $awsImagepath = $this->_awsProductUrl . $view->escape($productId);

            $imgTypeToUse = "list";

            switch ($imgType) {
                case 'list':

                    $imgTypeToUse = $imgType;
                    break;
                case 'original':

                    $imgTypeToUse = $imgType;
                    break;
                case 'thumb':

                    $imgTypeToUse = $imgType;
                    break;

                default:

                    $imgTypeToUse = "list";
                    break;
            }

            $image = $awsImagepath . "/" . $imgTypeToUse . "/" . $view->escape($imgPath);
        } elseif (strpos($imgPath, '_' . $productId . '_') == true) {

            $cloudinary = new My_Cloudinary();
            $image = cloudinary_url($view->escape($imgPath), array("width" => 400));
        } else {

            $imgTypeToUse = "list";
            switch ($imgType) {
                case 'list':
                    $imgTypeToUse = $imgType;
                    break;
                case 'original':
                    $imgTypeToUse = $imgType;
                    break;
                case 'thumb':
                    $imgTypeToUse = $imgType;
                    break;
                default:
                    $imgTypeToUse = "list";
                    break;
            }

            $imgFullPath = IMAGE_UPLOAD_PATH . "/product_" . $view->escape($productId) . "/" . $imgTypeToUse . "/" . $view->escape($imgPath);
            $imgSrc = "/images/upload/product_" . $view->escape($productId) . "/" . $imgTypeToUse . "/" . $view->escape($imgPath);

            if (file_exists($imgFullPath)) {
                $image = $imgSrc;
            }
        }

        return (string) $image;
    }

    /**
     * Delete image dir with all subdirs
     *
     * @param string $dir image full path dir
     *
     */
    public function rrmdir($dir) {
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if (filetype($dir . "/" . $object) == "dir")
                        $this->rrmdir($dir . "/" . $object);
                    else
                        unlink($dir . "/" . $object);
                }
            }
            reset($objects);
            rmdir($dir);
        }
    }

}