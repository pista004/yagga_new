<?php

// Skoch/Filter/File/Resize/Adapter/Gd.php
/**
 * Zend Framework addition by skoch
 * 
 * @category   Skoch
 * @package    Skoch_Filter
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author     Stefan Koch <cct@stefan-koch.name>
 */
//require_once 'Skoch/Filter/File/Resize/Adapter/Abstract.php';

/**
 * Resizes a given file with the gd adapter and saves the created file
 *
 * @category   Skoch
 * @package    Skoch_Filter
 */
class Filter_File_Resize_Adapter_Gd extends
Filter_File_Resize_Adapter_Abstract {

    public function resize($width, $height, $keepRatio, $file, $target, $keepSmaller = true) {

        $tmp_width = $width;
        $tmp_height = $height;

        list($oldWidth, $oldHeight, $type) = getimagesize($file);

        $thumb = imagecreatetruecolor($width, $height);


        switch ($type) {
            case IMAGETYPE_PNG:
                $source = imagecreatefrompng($file);
                break;
            case IMAGETYPE_JPEG:
                $source = imagecreatefromjpeg($file);
                break;
            case IMAGETYPE_GIF:
                $source = imagecreatefromgif($file);
                break;
        }


        if (!$keepSmaller || $oldWidth > $width || $oldHeight > $height) {
            if ($keepRatio) {
                list($width, $height) = $this->_calculateWidth($oldWidth, $oldHeight, $width, $height);
            }
        } else {
            $width = $oldWidth;
            $height = $oldHeight;
        }

        $dst_x = 0;
        if ($tmp_width > $width) {
            $dst_x = round(($tmp_width - $width) / 2);
        }

        $dst_y = 0;
        if ($tmp_height > $height) {
            $dst_y = round(($tmp_height - $height) / 2);
        }

        imagesavealpha($thumb, true);
        imagealphablending($thumb, false);

        $white = imagecolorallocatealpha($thumb, 255, 255, 255, 127);
        imagefill($thumb, 0, 0, $white);

        imagecopyresampled($thumb, $source, $dst_x, $dst_y, 0, 0, $width, $height, $oldWidth, $oldHeight);

        switch ($type) {
            case IMAGETYPE_PNG:
                imagepng($thumb, $target);
                break;
            case IMAGETYPE_JPEG:
                imagejpeg($thumb, $target);
                break;
            case IMAGETYPE_GIF:
                imagegif($thumb, $target);
                break;
        }
        return $target;
    }

}