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
jimport('joomla.filesystem.file');
AImporter::helper('html');
class BookproViewPayments extends JViewLegacy{
	
	protected $items;
	
	public function display($tpl = null){
		$data = JFile::read(JPATH_ADMINISTRATOR.'/components/com_bookpro/data/payment.json');		
		$this->items = json_decode($data);
		$method=$this->items->methods;
		if (count($error = $this->get('Errors'))){
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}
		
		$this->addToolbar();
		parent::display($tpl);
	}
	
	protected function addToolbar(){
		JToolbarHelper::title(JText::_('COM_BOOKPRO_PAYMENT_MANAGER'),'cube');
		JToolbarHelper::addNew('payment.add');
//		JToolbarHelper::editList('payment.edit');
//		JToolbarHelper::deleteList('','payments.delete','JTOOLBAR_DELETE', true);
		//JToolbarHelper::custom('payment.directview','arrow-last','icon over','Gateway', false);
	}
	
	
	
}