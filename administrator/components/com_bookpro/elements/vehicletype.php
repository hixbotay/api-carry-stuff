<?php

/**
 * Popup element to select destination.
 * @package 	Bookpro
 * @author 		Ngo Van Quan
 * @link 		http://joombooking.com
 * @copyright 	Copyright (C) 2011 - 2012 Ngo Van Quan
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: destination.php 44 2012-07-12 08:05:38Z quannv $
 **/

defined('_JEXEC') or die('Restricted access');

jimport('joomla.html.parameter.element');
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');

class JFormFieldVehicletype extends JFormFieldList
{
	
	protected function getInput() {
		require_once JPATH_ADMINISTRATOR.'/components/com_bookpro/helpers/bookpro.php';
		$db = JFactory::getDBO();
		$sql = "SELECT id, name  FROM #__bookpro_vehicle_type ORDER BY id ";
		$db->setQuery($sql);
		$options 	= array();
		$options[] 	= JHTML::_('select.option',  '', JText::_('Select Vehicle Type'), 'id', 'name');
		
		$options = array_merge($options, $db->loadObjectList()) ;
		//var_dump($options);die;
		//$result=$db->loadObjectList();
		foreach($options as &$item){		
			$item->name=BookProHelper::formatLang($item->name);
		}
		
		//var_dump($options);die;
		
		return JHTML::_('select.genericlist', $options, $this->name, ' class="inputbox" ', 'id', 'name', $this->value,$this->id) ;
	
	}

	

}

?>