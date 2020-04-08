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
AImporter::helper('date');
class BookProViewCustomers extends JViewLegacy {
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
		
		$this->customergroup = BookProHelper::getTypeCustomerSelect ( $this->state->get ( 'filter.user_type' ) );
		
		$this->addToolbar ();
		
		parent::display ( $tpl );
	}
	protected function addToolbar() {
		
	}
	protected function getSortFields() {
		return array (
				'a.name' => JText::_ ( 'COM_CUSTOMERS_HEADING_NAME' ),
				'a.email' => JText::_ ( 'JGLOBAL_EMAIL' ),
				'a.id' => JText::_ ( 'JGRID_HEADING_ID' ) 
		);
	}
}
?>