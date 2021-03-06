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
AImporter::helper('date');
class AString
{

    function getSafe($string)
    {
        $chTable = array('Ã¤' => 'a' , 'Ã„' => 'A' , 'Ã¡' => 'a' , 'Ã�' => 'A' , 'Ä�' => 'c' , 'ÄŒ' => 'C' , 'Ä‡' => 'c' , 'Ä†' => 'C' , 'Ä�' => 'd' , 'ÄŽ' => 'D' , 'Ä›' => 'e' , 'Äš' => 'E' , 'Ã©' => 'e' , 'Ã‰' => 'E' , 'Ã«' => 'e' , 'Ã‹' => 'E' , 'Ã­' => 'i' , 'Ã�' => 'I' , 'Ä¾' => 'l' , 'Ä½' => 'L' , 'Å„' => 'n' , 'Åƒ' => 'N' , 'Åˆ' => 'n' , 'Å‡' => 'N' , 'Ã³' => 'o' , 'Ã“' => 'O' , 'Ã¶' => 'o' , 'Ã–' => 'O' , 'Å™' => 'r' , 'Å˜' => 'R' , 'Å•' => 'r' , 'Å”' => 'R' , 'Å¡' => 's' , 'Å ' => 'S' , 'Å›' => 's' , 'Åš' => 'S' , 'Å¥' => 't' , 'Å¤' => 'T' , 'Å¯' => 'u' , 'Å®' => 'U' , 'Ãº' => 'u' , 'Ãš' => 'U' , 'Ã¼' => 'u' , 'Ãœ' => 'U' , 'Ã½' => 'y' , 'Ã�' => 'Y' , 'Å¾' => 'z' , 'Å½' => 'Z' , 'Åº' => 'z' , 'Å¹' => 'Z');
        $string = strtr($string, $chTable);
        $string = str_replace('-', ' ', $string);
        $string = preg_replace(array('/\s+/' , '/[^A-Za-z0-9\-]/'), array('-' , ''), $string);
        $string = JString::strtolower($string);
        $string = JString::trim($string);
        return $string;
    }
    public static function formatTourDate($str,$glue){
    	$date= explode(',', $str);
    	$result=array();
    	for ($i = 0; $i < count($date); $i++) {
    		$result[]=DateHelper::formatDate($date[$i]);
    	}
    	return implode($glue,$result);
    }
    public static function formatDate($date){
    	$check_in=new JDate($date);
    	return date_format($check_in,'F j, Y');
    }
    public static function formatprice($value,$config){
     
    	if ($value) {
    		$value = number_format($value, 2, ',', ',');
    		$length = JString::strlen($value);
    		if (JString::substr($value, $length - 2) == '00')
    			$newval= JString::substr($value, 0, $length - 3);
    		elseif (JString::substr($value, $length - 1) == '0')
    			$newval= JString::substr($value, 0, $length - 1);
    		else
    			$newval=$value;
    	}
    	return $value?$config->mainCurrency.$newval:JText::_('N/A');
    }
}

?>