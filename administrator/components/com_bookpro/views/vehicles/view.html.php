<?php

/**
 * @package 	Bookpro
 * @author 		Ngo Van Quan
 * @link 		http://joombooking.com
 * @copyright 	Copyright (C) 2011 - 2012 Ngo Van Quan
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: view.html.php 47 2012-07-13 09:43:14Z quannv $
 **/
defined ( '_JEXEC' ) or die ( 'Restricted access' );
require_once JPATH_COMPONENT .'/helpers/order.php';
class BookProViewVehicles extends JViewLegacy {
	protected $items;
	protected $pagination;
	protected $state;
	
	public function display($tpl = null) {
		$this->items = $this->get ( 'Items' );
		$this->pagination = $this->get ( 'Pagination' );
		$this->state = $this->get ( 'State' );
		
		// Check for errors.
		if (count ( $errors = $this->get ( 'Errors' ) )) {
			JError::raiseError ( 500, implode ( "\n", $errors ) );
			
			return false;
		}
		// Include the component HTML helpers.
		//JHtml::addIncludePath ( JPATH_COMPONENT . '/helpers/html' );
		
		
		$this->addToolbar ();
		
		parent::display ( $tpl );
	}
	protected function addToolbar() {
		$vehicle = JFactory::getUser ();
		
		// Get the toolbar object instance
		
		JToolbarHelper::title ( JText::_ ( 'COM_BOOKPRO_VEHICLES_MANAGER' ),'list-view');
		JToolbarHelper::addNew ( 'vehicle.add' );
		JToolbarHelper::editList ( 'vehicle.edit' );
		JToolbarHelper::deleteList ( '', 'vehicles.delete' );
		
	}
	
	protected function getVehicleType(){
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('id,name')
			->from('#__bookpro_vehicle_type');
		$db->setQuery($query);
		$options = $db->loadObjectList();
		foreach($options as &$option){
			$option->name = BookproHelper::formatLang($option->name);
		}
		$select = (object)array('id'=>'','name'=>JText::_("COM_BOOKPRO_VEHICLE_TYPE"));
		array_unshift($options , $select);
		return JHTML::_('select.genericlist', $options, 'filter_type', ' class="inputbox" ', 'id', 'name', $this->state->get('filter.type'),'filter_type') ;
	}
	
	protected function getSortFields()
	{
		return array(
				'a.name' => JText::_('COM_BOOKPRO_VEHICLES_NAME'),
				'a.capacity' => JText::_('COM_BOOKPRO_VEHICLES_CAPACITY'),
				'a.id' => JText::_('COM_BOOKPRO_VEHICLES_ID'),
		);
	}
}?>