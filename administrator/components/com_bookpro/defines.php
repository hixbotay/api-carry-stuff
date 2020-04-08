<?php

/**
 * @package 	Bookpro
 * @author 		Ngo Van Quan
 * @link 		http://joombooking.com
 * @copyright 	Copyright (C) 2011 - 2012 Ngo Van Quan
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: defines.php 104 2012-08-29 18:01:09Z quannv $
 **/
defined('_JEXEC') or die('Restricted access');
if(!defined('DS')){
	define('DS',DIRECTORY_SEPARATOR);
}


//
////Set defines for component location
$mainframe = JFactory::getApplication();
/* @var $mainframe JApplication */
define('IS_ADMIN', $mainframe->isAdmin());
define('IS_SITE', $mainframe->isSite());

//Display component name
define('COMPONENT_NAME', 'BookPro');
//Unique component option use for navigation in Joomla!
define('OPTION', 'com_bookpro');
define('NAME', 'bookpro');

//default component encoding
define('ENCODING', 'UTF-8');

define('ADMIN_ROOT', JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . OPTION);

define('SITE_ROOT', JPATH_ROOT . DS . 'components' . DS . OPTION);

//define('MANIFEST', ADMIN_ROOT . DS  . 'manifest.xml');
//define('CONFIG', ADMIN_ROOT . DS . 'config.xml');

//Component table prefix
define('PREFIX', 'bookpro');
AImporter::helper('bookpro','route','factory','html');


//$config = AFactory::getConfig();
////default date formats
//
//$aDateFormatNormal = $config->dateTypeJoomla ? JText::_('DATE_FORMAT_LC3') : ($config->dateNormal ? $config->dateNormal : JText::_('DATE_FORMAT_LC3'));
//define('ADATE_FORMAT_NORMAL',AHtml::strftime2date($aDateFormatNormal));
//define('ADATE_FORMAT_NORMAL_CAL', $aDateFormatNormal);
//
//$aDateFormatLong = $config->dateTypeJoomla ? JText::_('DATE_FORMAT_LC2') : ($config->dateLong ? $config->dateLong : JText::_('DATE_FORMAT_LC2'));
//define('ADATE_FORMAT_LONG', AHtml::strftime2date($aDateFormatLong));
//define('ADATE_FORMAT_LONG_CAL', $aDateFormatLong);
//
////used in calendars daily/weekly as header
//$aDateFormatNice = $config->dateTypeJoomla ? JText::_('DATE_FORMAT_LC5') : $config->dateDay;
//define('ADATE_FORMAT_NICE', AHtml::strftime2date($aDateFormatNice));
//
////used in monthly calendars in day box
//$aDateFormatNiceShort = $config->dateTypeJoomla ? JText::_('DATE_FORMAT_LC6') : $config->dateDayShort;
//define('ADATE_FORMAT_NICE_SHORT', AHtml::strftime2date($aDateFormatNiceShort));
//
////time format
//$aTimeFormatShort = $config->dateTypeJoomla ? '%H:%M' : ($config->time ? JText::_($config->time) : '%H:%M');
//define('ATIME_FORMAT', AHtml::strftime2date($aTimeFormatShort) );
//define('ATIME_FORMAT_SHORT', AHtml::strftime2date($aTimeFormatShort) );
//define('ATIME_FORMAT_CAL', AHtml::strftime2date('%H:%M')); //for time picket 
//
//
//// MYSQL date formats - internal no display
//define('ADATE_FORMAT_MYSQL_DATE','Y-m-d');
//define('ADATE_FORMAT_MYSQL_DATE_CAL', '%Y-%m-%d');
//define('ADATE_FORMAT_MYSQL_DATETIME_CAL','%Y-%m-%d %H:%M:%S');
//define('ADATE_FORMAT_MYSQL_TIME','H:i:s');
//define('ADATE_FORMAT_MYSQL_DATETIME','Y-m-d H:i:s');
//
////Name of default controller
define('CONTROLLER', 'BookproController');
////Define IDs for component controllers
//define('CONTROLLER_CUSTOMER', 'customer');
//define('CONTROLLER_AGENT', 'agent');
//define('CONTROLLER_AGENTBUS', 'agentbus');
//define('CONTROLLER_AGENTBUSTRIP','agentbustrip');
//define('CONTROLLER_AGENTBUSSTATION', 'agentbusstation');
//define('CONTROLLER_AGENT_REPORT', 'report');
//define('CONTROLLER_AIRLINE', 'airline');
//define('CONTROLLER_BUS', 'bus');
//define('CONTROLLER_SEATTEMPLATE', 'seattemplate');
//define('CONTROLLER_SEATTEMPLATES', 'seattemplates');
//define('CONTROLLER_GERNERATE', 'generate');
//define('CONTROLLER_BUSSTATION', 'busstation');
//define('CONTROLLER_BAGGAGE', 'baggage');
//define('CONTROLLER_PASSENGER', 'passenger');
//define('CONTROLLER_AIRPORT','airport');
//define('CONTROLLER_CURRENCY','currency');
//define('CONTROLLER_FLIGHT','flight');
//define('CONTROLLER_CATEGORY','category');
//define('CONTROLLER_PAYMENT','payment');
//define('CONTROLLER_COUNTRY','country');
//define('CONTROLLER_REGION','region');
//define('CONTROLLER_COUPON','coupon');
//define('CONTROLLER_ORDER','order');
//define('CONTROLLER_APPLICATION','application');
//define('CONTROLLER_HOTEL','hotel');
//define('CONTROLLER_CGROUP','cgroup');
//define('CONTROLLER_CONFIG', 'config');
//
//
//define('IMAGES', JURI::root() . 'components/' . OPTION . '/assets/images/');
//
//define('CACHE_IMAGES_DEPTH', 5);
//
//define('ADMIN_SET_IMAGES_WIDTH', 80);
//
//
//
//define('CUSTOMER_STATE_ACTIVE', 1);
//define('CUSTOMER_STATE_DELETED', 0);
//define('CUSTOMER_STATE_BLOCK', - 1);
//
//
//define('CUSTOMER_SENDEMAIL', 0);
//
//
////Defines for frontend views
//define('VIEW_FLIGHTS', 'flights');
//define('VIEW_AIRPORTS', 'airports');
//define('VIEW_CUSTOMER', 'customer');
//define('VIEW_AGENT', 'agent');
//define('VIEW_CUSTOMERS', 'customers');
//define('VIEW_AGENTS', 'agents');
//define('VIEW_AIRPORT', 'airport');
//define('VIEW_PASSENGERS', 'passengers');
//define('VIEW_PAYMENTS', 'payments');
//define('VIEW_APPLICATION', 'application');
//define('VIEW_APPLICATIONS', 'applications');
//define('VIEW_PAYMENT', 'payment');
//define('VIEW_AIRLINES', 'airlines');
//define('VIEW_IMAGES', 'images');
//define('VIEW_FILES', 'files');
//
//
//define('DAY_LENGTH', 24 * 60 * 60);
//define('YEAR_LENGTH', 365 * 24 * 60 * 60);
//
//
//define('ADMIN_VIEWS', ADMIN_ROOT . DS . 'views');
//define('SITE_VIEWS', SITE_ROOT . DS . 'views');



?>