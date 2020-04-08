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

class BookProViewCustomer_docs extends JViewLegacy {
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
		
		
		JHtml::addIncludePath ( JPATH_COMPONENT . '/helpers/html' );
	//	$this->active=BookProHelper::getValidateStatusCustomer($this->state->get('filter.active'));
	//	$this->customertype=BookProHelper::getTypeCustomerSelect($this->state->get('filter.user_type'));
		$this->addToolbar ();
		
		parent::display ( $tpl );
	}
	protected function addToolbar() {
		$customer = JFactory::getUser ();
		
		// Get the toolbar object instance
		
		JToolbarHelper::title ( JText::_ ( 'COM_BOOKPRO_CUSTOMERS_MANAGER' ),'user');
		//JToolbarHelper::addNew ( 'customer_doc.add' );
		//JToolbarHelper::editList ( 'customer_doc.edit' );
		JToolbarHelper::divider ();
		JToolbarHelper::deleteList ( '', 'customer_docs.delete' );
	}
	
	protected function getSortFields()
	{
		return array(
				'a.name' => JText::_('COM_CUSTOMERS_HEADING_NAME'),
				'a.id' => JText::_('JGRID_HEADING_ID')
		);
	}
}
?>