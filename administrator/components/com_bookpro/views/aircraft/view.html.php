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

class BookproViewAircraft extends JViewLegacy{
	protected $item;
	protected $form;
	
	public function display($tpl=null){
		$this->item = $this->get('Item');
		$this->form = $this->get('Form');
		
		if (count($errors = $this->get('Errors'))){
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}
		
		$this->addToolbar();
		parent::display($tpl);
	}
	
	protected function addToolbar(){
		JFactory::getApplication()->input->set('hidemainmenu', true);
		$edit		= $this->item->id;
		$text = !$edit ? JText::_( 'JTOOLBAR_NEW' ) : JText::_( 'JACTION_EDIT' );
		JToolbarHelper::title(JText::_('COM_BOOKPRO_AIRCRAFT_MANAGER').': '.$text);
		JToolbarHelper::apply('aircraft.apply');
		JToolbarHelper::save('aircraft.save');
	
		if(empty($this->item->id)){
			JToolbarHelper::cancel('aircraft.cancel');
		}
		else{
			JToolbarHelper::cancel('aircraft.cancel', 'JTOOLBAR_CLOSE');
		}
	
	}
	
	
}

?>