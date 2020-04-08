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

class BookProViewPackages extends JViewLegacy {
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

		//$this->type=BookProHelper::getTypeCustomerSelect($this->state->get('filter.type'));
		$this->addToolbar ();
		
		parent::display ( $tpl );
	}
	protected function addToolbar() {
		$vehicle = JFactory::getUser ();
		
		// Get the toolbar object instance
		
		JToolbarHelper::title ( JText::_ ( 'COM_BOOKPRO_PACKAGES_MANAGER' ),'box-add');
		JToolbarHelper::addNew ( 'package.add' );
		JToolbarHelper::editList ( 'package.edit' );
		JToolbarHelper::deleteList ( '', 'packages.delete' );	
	}
	
	protected function getSortFields()
	{
		return array(
				'a.name' => JText::_('COM_BOOKPRO_VEHICLES_NAME'),
		);
	}
}?>