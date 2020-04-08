<?php
/**
 * 
 * @package 	Bookpro
 * @author 		Ngo Van Quan
 * @link 		http://joombooking.com
 * @copyright 	Copyright (C) 2011 - 2012 Ngo Van Quan
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id$
 **/

defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );

class BookproViewAircrafts extends JViewLegacy{
	
	protected $items;
	protected $state;
	protected $pagination;
	
	public function display($tpl = null){
		$this->items = $this->get('Items');
		$this->state = $this->get('State');
		$this->pagination = $this->get('Pagination');
		
		if (count($error = $this->get('Errors'))){
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}
		
		$this->addToolbar();
		parent::display($tpl);
	}
	
	protected function addToolbar(){
		JToolbarHelper::title(JText::_('COM_BOOKPRO_AIRCRAFTS_MANAGER'),'cube');
		JToolbarHelper::addNew('aircraft.add');
		JToolbarHelper::editList('aircraft.edit');
		JToolbarHelper::publish('aircrafts.publish', 'JTOOLBAR_PUBLISH', true);
		JToolbarHelper::unpublish('aircrafts.unpublish', 'JTOOLBAR_UNPUBLISH', true);
		JToolbarHelper::deleteList('','aircrafts.delete','JTOOLBAR_DELETE', true);
	}
	
	protected function getSortFields()
	{
		return array(
			'a.state'=> JText::_('JSTATUS'),
			'a.code' => Jtext::_('Code'),
			'a.title' => JText::_('JGLOBAL_TITLE'),
			'a.seat' => JText::_('COM_BOOKPRO_SEAT'),
			'a.ordering' => JText::_('COM_BOOKPRO_ORDER'),
		);
	}
}