<?php

/**
 * @package 	Bookpro
 * @author 		Ngo Van Quan
 * @link 		http://joombooking.com
 * @copyright 	Copyright (C) 2011 - 2012 Ngo Van Quan
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: view.html.php 63 2012-07-29 10:43:08Z quannv $
 **/

defined('_JEXEC') or die('Restricted access');



class BookProViewTransaction extends JViewLegacy
{

   	protected $form;
	protected $item;
	protected $state;	
    public function display($tpl = null)
    {
    	
    	// Initialise variables.
    	$this->form		= $this->get('Form');
    	$this->item		= $this->get('Item');
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
    	JFactory::getApplication()->input->set('hidemainmenu', true);
		$edit = $this->item->id;
		$text = !$edit ? JText::_( 'JTOOLBAR_NEW' ) : JText::_( 'JACTION_EDIT' );
    	JToolbarHelper::title(JText::_('COM_BOOKPRO_TRANSACTIONS_MANAGER').': '.$text,'');
    	JToolbarHelper::save ( 'transaction.save' );
    	JToolBarHelper::apply('transaction.apply');
    	JToolbarHelper::cancel ('transaction.cancel');

    }
    
}

?>