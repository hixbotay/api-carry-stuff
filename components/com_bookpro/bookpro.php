<?php
/**
 * @package 	Bookpro
 * @author 		Ngo Van Quan
 * @link 		http://joombooking.com
 * @copyright 	Copyright (C) 2011 - 2012 Ngo Van Quan
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: bookpro.php 108 2012-09-04 04:53:31Z quannv $
 **/
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

if(!defined('DS')){ 
	define('DS',DIRECTORY_SEPARATOR);
}

// import joomla controller library
jimport('joomla.application.component.controller');
include (JPATH_ADMINISTRATOR.DS.'components'.DS .'com_bookpro'.DS. 'helpers' . DS . 'importer.php');
//require_once JPATH_ROOT . DS . 'components' . DS . 'com_bookpro' . DS . 'libraries' . DS . 'base' . DS . 'libs' . DS . 'basic.php';
jimport('joomla.application.component.model');
JModelLegacy::addIncludePath(JPATH_COMPONENT.'/models');
//$language = JFactory::getLanguage();
/* @var $language JLanguage */

//$language->load('com_bookpro.common', JPATH_ADMINISTRATOR);
AImporter::defines();

$controller = JRequest::getCmd('controller');

if (!$controller) {
	$controller = JControllerLegacy::getInstance('BookPro');
}
else {
	if (file_exists(JPATH_COMPONENT.DS.'controllers'.DS.$controller.'.php'))
		require_once( JPATH_COMPONENT.DS.'controllers'.DS.$controller.'.php' );
	else
		JError::raiseError( 403, JText::_('Access Forbidden') );

	$classname = 'BookProController'.$controller;
	$controller = new $classname();
}
$controller->execute(JFactory::getApplication()->input->get('task','display'));
$controller->redirect();
