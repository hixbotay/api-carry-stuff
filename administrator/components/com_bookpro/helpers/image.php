<?php

/**
 * Bookpro Image class
 *
 * @package Bookpro
 * @author Ngo Van Quan
 * @link http://joombooking.com
 * @copyright Copyright (C) 2011 - 2012 Ngo Van Quan
 * @version $Id: image.php 44 2012-07-12 08:05:38Z quannv $
 */

defined('_JEXEC') or die('Restricted access');


class AImage
{
	static $image;
	public static function getSize(){
		if(!isset(self::$image)){
			self::$image = array(
				'unselected'=>array('w'=>0,'h'=>0),
				'selected'=>array('w'=>0,'h'=>0),
				'map'=>array('w'=>0,'h'=>0)
			);
		}
		return self::$image;
	}
	
	public static function resize($file,$width,$height,$path){
	  
		if($width > 0 && $height > 0){
				/* Get original image x y*/
		   $ext = substr($file['type'], -3);
		  $func = "imagecreatefrom".$ext;
		   $source = $func($file['tmp_name']);
			 list($w, $h) = getimagesize($file['tmp_name']);
			   $k_w = 1;
				$k_h = 1;
				$dst_x =0;
				$dst_y =0;
				$src_x =0;
				$src_y =0;
				if($width>$w )//by width
				{
					$dst_x = ($width-$w)/2;
				}
				if($h>$h)//by height
				{
					$dst_y = ($h-$h)/2;
				}
			 
				if( $width<$w || $new_height<$h )
				{
					$k_w = $width/$w;
					$k_h = $height/$h;

					if($height>$h)
					{
						$src_x  = ($w-$width)/2;
					}
					else if ($width>$w)
					{
							$src_y  = ($h-$height)/2;
					}
					else
					{
						if($k_h>$k_w)
						{
							$src_x = round(($w-($width/$k_h))/2);
						}
						else
						{
							$src_y = round(($h-($height/$k_w))/2);
						}
					}
				}
			  /* calculate new image size with ratio */
			  $ratio = max($width/$w, $height/$h);
			  $h = ceil($height / $ratio);
			  $x = ($w - $width / $ratio) / 2;
			  $w = ceil($width / $ratio);
			  $output = imagecreatetruecolor( $height, $width);
			  //to preserve PNG transparency
			 
		//        debug($file);die;
				if($ext == "png")
				{
					//saving all full alpha channel information
					imagesavealpha($output, true);
					//setting completely transparent color
					$transparent = imagecolorallocatealpha($output, 0, 0, 0, 127);
					//filling created image with transparent color
					imagefill($output, 0, 0, $transparent);
				}
				imagecopyresampled( $output, $source,  $dst_x, $dst_y, $src_x, $src_y, 
							$width-2*$dst_x, $height-2*$dst_y, 
							$w-2*$src_x, $h-2*$src_y);
				//free resources
				
				ImageDestroy($source);
				
			
		}else{
			move_uploaded_file($file["tmp_name"], $path);
			return true;
		}
		
		
       
	  /* Save image */
	  switch ($file['type']) {
	    case 'image/jpeg':
	      imagejpeg($output, $path, 100);
	      break;
	    case 'image/png':
	      imagepng($output, $path, 0);
	      break;
	    case 'image/gif':
	      imagegif($output, $path);
	      break;
	    default:
//	      imagedestroy($image);
	 	  imagedestroy($output);
	      return false;
	      break;
	  }
	  imageDestroy($output);
	  /* cleanup memory */
//	  imagedestroy($image);
	  //imagedestroy($tmp);
	  return true;
	  
	}
    
}

?>