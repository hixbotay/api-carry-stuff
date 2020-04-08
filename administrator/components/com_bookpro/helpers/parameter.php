<?php

/**
 * Create parameter table for template properties. 
 * @package Bookpro
 * @author Ngo Van Quan
 * @link http://joombooking.com
 * @copyright Copyright (C) 2011 - 2012 Ngo Van Quan
 * @version $Id: parameter.php 44 2012-07-12 08:05:38Z quannv $
 */

defined('_JEXEC') or die('Restricted access');

class AParameter
{
    static $price_list;
    public static function getPrice(){
    	if(!empty(self::$price_list)){
    		return self::$price_list;
    	}
    	if(!class_exists('BookproModelPrices')){
    		require_once JPATH_ADMINISTRATOR.'/components/com_bookpro/models/prices.php';
    	}
    	$model = new BookproModelPrices();
    	self::$price_list = $model->getPriceList();
    	return self::$price_list;
    }
    
}

?>