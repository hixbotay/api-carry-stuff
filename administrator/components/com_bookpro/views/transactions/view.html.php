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

class BookProViewTransactions extends JViewLegacy {
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
		JHtml::addIncludePath ( JPATH_COMPONENT . '/helpers/html' );

		//$this->type=BookProHelper::getTypeCustomerSelect($this->state->get('filter.type'));
		$this->addToolbar ();
		
		parent::display ( $tpl );
	}
	protected function addToolbar() {
		$vehicle = JFactory::getUser ();
		
		// Get the toolbar object instance
		
		JToolbarHelper::title ( JText::_ ( 'COM_BOOKPRO_TRANSACTIONS_MANAGER' ),'');
//		JToolbarHelper::addNew ( 'transaction.add' );
		JToolbarHelper::editList ( 'transaction.edit' );
		JToolbarHelper::deleteList ( '', 'transactions.delete' );
		
	}
	
	protected function getCancelBox(){
		$options = BookProHelper::get('cancel_status','arrayObject');
		$option = JHtml:: _('select.option', '', JText::_("COM_BOOKPRO_SELECT_CANCEL_STATUS"));
		array_unshift($options , $option);
		return JHtml::_('select.genericlist',$options,'filter_cancel_status','class="input input-medium"','value','text',$this->state->get('filter.cancel_status'));
	}
	
	protected function getWhoCanceled(){
		$options = array();
		$options[] = JHtml:: _('select.option','', JText::_("COM_BOOKPRO_SELECT_WHO_CANCELLED"));
		$options[] = JHtml:: _('select.option','driver', JText::_("COM_BOOKPRO_DRIVER"));
		$options[] = JHtml:: _('select.option','customer', JText::_("COM_BOOKPRO_CUSTOMER"));
		$options[] = JHtml:: _('select.option','admin', JText::_("JADMINISTRATOR"));
		//array_unshift($options , $option);
		return JHtml::_('select.genericlist',$options,'filter_order_who_cancelled','class="input input-medium"','value','text',$this->state->get('filter.order_who_cancelled'));
	}
	
}
?>