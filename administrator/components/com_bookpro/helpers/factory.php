<?php

/**
* Bookpro check class
*
* @package Bookpro
* @author Ngo Van Quan
* @link http://joombooking.com
* @copyright Copyright (C) 2011 - 2012 Ngo Van Quan
* @version $Id: factory.php 47 2012-07-13 09:43:14Z quannv $
*/

defined('_JEXEC') or die('Restricted access');

class AFactory
{

    function getLoggedCustomer()
    {
        static $instance;
        if (empty($instance)) {
            
            AImporter::model('customer');
            $model=new BookProModelCustomer();
            $instance=$model->getItemByUser();
            return $instance;
            
        }
        return $instance;
    }

    /**
     * Create config helper object
     * 
     * @return BookingConfig
     */
    static function getConfig()
    {
    	
        static $instance;        
        if (empty($instance)) {
            AImporter::helper('config', 'parameter');
            $instance = new BookProConfig();
        }
        return $instance;
    }
}

?>