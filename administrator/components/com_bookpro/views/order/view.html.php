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



class BookProViewOrder extends JViewLegacy
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
    	
    	$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('*')
			->from('#__bookpro_customer')
			->where ('id ='.(int)$this->item->customer_id);
		$db->setQuery($query);
		$this->customer = $db->loadObject();	    
		//var_dump($this->items);die;
    	// Check for errors.
    	if (count($errors = $this->get('Errors')))
    	{
    		JError::raiseError(500, implode("\n", $errors));
    		return false;
    	}
		if($this->item->id){
			$model = $this->getModel();
			$this->transactions = $model->getTransactions($this->item->id);
			//debug($this->transactions);
		}
    	
    	$this->addToolbar();
    	parent::display($tpl);
       
    }
    
    protected function addToolbar() {
    	JFactory::getApplication()->input->set('hidemainmenu', true);
		$edit = $this->item->id;
		$text = !$edit ? JText::_( 'JTOOLBAR_NEW' ) : JText::_( 'JACTION_EDIT' );
    	JToolbarHelper::title(JText::_('COM_BOOKPRO_ORDERS_MANAGER').': '.$text,'list-2');
    	JToolbarHelper::save ( 'order.save' );
    	JToolBarHelper::apply('order.apply');
    	JToolbarHelper::cancel ('order.cancel');

    }
	
	
   
}

?>