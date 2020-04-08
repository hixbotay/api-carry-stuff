<?php
/**
* @package 	Bookpro
* @author 		Ngo Van Quan
* @link 		http://joombooking.com
* @copyright 	Copyright (C) 2011 - 2012 Ngo Van Quan
* @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
* @version 	$Id: bookpro.php 80 2012-08-10 09:25:35Z quannv $
**/

defined('_JEXEC') or die('Restricted access');
class XmlHelper
{
	static function getAttribute($object, $attribute)
	{
		$result =array();
		foreach($object as $ob){
			if(isset($ob[$attribute]))
				$result[] = (string)$ob[$attribute];
		}
	   
		return $result;
	}
 
}

?>