<?php

/**
 * @package 	Bookpro
 * @author 		Ngo Van Quan
 * @link 		http://joombooking.com
 * @copyright 	Copyright (C) 2011 - 2012 Ngo Van Quan
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: view.html.php 63 2012-07-29 10:43:08Z quannv $
 **/

defined('_JEXEC') or die( 'Restricted access' );
jimport( 'joomla.application.component.view' );
class BookProViewGooglemap extends JViewLegacy
{
	protected $form;
	protected $items;
	protected $state;	
    public function display($tpl = null)
    {
    	$this->config=AFactory::getConfig();
		//$this->_prepare();

		
    	// Initialise variables.
    	$this->form		= $this->get('Form');
    	$this->items	= $this->get('Items');
    	$this->state	= $this->get('State');
    	
    	// Check for errors.
    	if (count($errors = $this->get('Errors')))
    	{
    		JError::raiseError(500, implode("\n", $errors));
    		return false;
    	}
    	
    	$this->addToolbar();
    	parent::display($tpl);
       
    }
    
   protected function addToolbar() {
    	JToolbarHelper::title(JText::_('COM_BOOKPRO_ORDERS_LOCATION'));
		JToolbarHelper::back('Back','index.php?option=com_bookpro&view=orders');

    }
    
	protected function getSortFields()
	{
		return array(
			'a.title' => JText::_('JGLOBAL_TITLE'),
		);
	}
	
}

?>