<?php

    /**
    * @package 	Bookpro
    * @author 		Ngo Van Quan
    * @link 		http://joombooking.com
    * @copyright 	Copyright (C) 2011 - 2012 Ngo Van Quan
    * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
    * @version 	$Id: view.html.php 26 2012-07-08 16:07:54Z quannv $
    **/

    defined('_JEXEC') or die('Restricted access');

    //import needed Joomla! libraries
    jimport('joomla.application.component.view');

    //import needed JoomLIB helpers
    AImporter::helper('route', 'bookpro', 'request');
    AImporter::model('customers');
    //import needed assets
 
    class BookProViewRegistrations extends JViewLegacy
    {
        function display($tpl = null)
        {
        	
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
        	//$this->active=BookProHelper::getValidateStatusCustomer($this->state->get('filter.active'));
			$this->customertype=BookProHelper::getTypeRegistrationSelect($this->state->get('filter.user_type'));
        	$this->addToolbar ();

         //  $mainframe = JFactory::getApplication();
            /* @var $mainframe JApplication */

           // $document = JFactory::getDocument();
            /* @var $document JDocument */

            //$document->setTitle(JText::_('COM_BOOKPRO_COUPON_LIST'));

           // $model = new BookProModelCustomers();
            //$this->state	= $model->getState();
            
            //$this->state->set('list.limit', 5);
          //  $this->pagination	= $model->getPagination();
           // $this->items=$model->getItemsRegistration();
         
            parent::display($tpl);
        }
        
        protected function addToolbar() {
        	JToolbarHelper::title ( JText::_ ( 'COM_BOOKPRO_REGISTRATION_MANAGER' ),'user');
        	//JToolbarHelper::addNew ( 'registration.add' );
        	JToolbarHelper::editList ( 'registration.edit' );
			JToolbarHelper::deleteList ( '', 'customers.deleteregistration' );
        }
        
        protected function getSortFields()
        {
        	return array(
        			'a.name' => JText::_('COM_CUSTOMERS_HEADING_NAME'),
        			'a.email' => JText::_('JGLOBAL_EMAIL'),
        			'a.id' => JText::_('JGRID_HEADING_ID')
        	);
        }
    }

?>