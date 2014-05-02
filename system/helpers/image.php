<?php
class Image {
    public function is_image($path) {
	    $a = getimagesize($path);
	    $image_type = $a[2];
	     
	    if(in_array($image_type , array(IMAGETYPE_GIF , IMAGETYPE_JPEG ,IMAGETYPE_PNG , IMAGETYPE_BMP))) {
	        return true;
	    }

	    return false;
	}
}