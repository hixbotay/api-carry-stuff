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
if(!defined('JPATH_COMPONENT')){
	define('JPATH_COMPONENT',JPATH_ROOT.'/components/com_jbdebug');
}

// import joomla controller library
jimport('joomla.application.component.controller');
include (JPATH_ROOT.'/components/com_jbdebug/helpers/importer.php');
include (JPATH_ROOT.'/components/com_jbdebug/helpers/helper.php');
echo '<script src="'.JUri::root().'media/jui/js/jquery.min.js"></script>';

$controller = JFactory::getApplication()->input->getString('controller');

if (!$controller) {
	$controller = JControllerLegacy::getInstance('Jbdebug');
}
else {
	if (file_exists(JPATH_COMPONENT.'/controllers/'.$controller.'.php'))
		require_once( JPATH_COMPONENT.'/controllers/'.$controller.'.php' );
	else
		echo 'Retric access';die;

	$classname = 'JbdebugController'.$controller;
	$controller = new $classname();
}
$controller->execute(JFactory::getApplication()->input->get('task','display'));
$controller->redirect();
