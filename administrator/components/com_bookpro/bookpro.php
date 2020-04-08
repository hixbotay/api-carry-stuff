<?php 
/**dfdlfjdddddd
 * @package 	Bookpro
 * @author 		Ngo Van Quan
 * @link 		http://joombooking.com
 * @copyright 	Copyright (C) 2011 - 2012 Ngo Van Quan
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: bookpro.php 27 2012-07-08 17:15:11Z quannv $
 **/
defined('_JEXEC') or die('Restricted access');
if(!defined('DS')){
	define('DS',DIRECTORY_SEPARATOR);
}
 
include (JPATH_COMPONENT_ADMINISTRATOR . DS . 'helpers' . DS . 'importer.php');
//require_once JPATH_ROOT . DS . 'components' . DS . 'com_bookpro' . DS . 'libraries' . DS . 'base' . DS . 'libs' . DS . 'basic.php';
$document = JFactory::getDocument();
$document->addStyleSheet('components/com_bookpro/assets/css/joomla3.css');
$document->addStyleSheet('components/com_bookpro/assets/css/style.css');
$language = JFactory::getLanguage();

/* @var $language JLanguage */

$language->load('com_bookpro', JPATH_SITE, null, true);

AImporter::defines();

if (class_exists(($classname = AImporter::controller()))) {
    
	$controller = new $classname();
    /* @var $controller JController */
    //$controller = JController::getInstance('BookPro');
	
    $controller->execute(JFactory::getApplication()->input->getString('task'));
    
    //die;
    $controller->redirect();
}