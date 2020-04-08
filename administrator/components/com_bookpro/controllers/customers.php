<?php

/**
 * @package 	Bookpro
 * @author 		Ngo Van Quan
 * @link 		http://joombooking.com
 * @copyright 	Copyright (C) 2011 - 2012 Ngo Van Quan
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: controller.php 104 2012-08-29 18:01:09Z quannv $
 **/

defined('_JEXEC') or die;

jimport('joomla.application.component.controlleradmin');

class BookproControllerCustomers extends JControllerAdmin
{
	/**
	 * (non-PHPdoc)
	 * @see JControllerLegacy::getModel()
	 */
	public function getModel($name = 'Customer', $prefix = 'BookproModel', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);
		return $model;
	}
	
	public function delete(){
		JSession::checkToken() or die(JText::_('JINVALID_TOKEN'));
		// Get items to remove from the request.
		$cid = JFactory::getApplication()->input->get('cid', array(), 'array');

		if (!is_array($cid) || count($cid) < 1)
		{
			$this->setMessage('No item is selected','error');
		}
		else
		{
			$count = 0;
			$db = JFactory::getDbo();
			try{
				$db->transactionStart();
				foreach ($cid as $id){
					$this->deleteCustomer($id);	
				}
				$this->setMessage(JText::plural($this->text_prefix . '_N_ITEMS_DELETED', $count));
				
				$db->transactionCommit();
				
			}
			catch (Exception $e){
				$db->transactionRollback();
				$this->setMessage($e->getMessage());
			}
			
		}
			
		$this->setRedirect(JRoute::_('index.php?option=com_bookpro&view=customers', false));
		return;																					
	}
	
	public function deleteregistration(){
		JSession::checkToken() or die(JText::_('JINVALID_TOKEN'));
		// Get items to remove from the request.
		$cid = JFactory::getApplication()->input->get('cid', array(), 'array');

		if (!is_array($cid) || count($cid) < 1)
		{
			$this->setMessage('No item is selected','error');
		}
		else
		{
			$count = 0;
			$db = JFactory::getDbo();
			try{
				$db->transactionStart();
				foreach ($cid as $id){
					$this->deleteCustomer($id);	
				}
				$this->setMessage(JText::plural($this->text_prefix . '_N_ITEMS_DELETED', $count));
				
				$db->transactionCommit();
				
			}
			catch (Exception $e){
				$db->transactionRollback();
				$this->setMessage($e->getMessage());
			}
			
		}
			
		$this->setRedirect(JRoute::_('index.php?option=com_bookpro&view=customers&state=0&layout=registration', false));
		return;					
	}
	
	private function deleteCustomer($id){
		$model = $this->getModel();
		$table = $model->getTable();
		//remove the user 
		$table->load($id);
		$result = false;
		if($table->user_id){		
			//delete session of the customer
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->delete('#__bookpro_session')->where('userid='.$table->id);
			$db->setQuery($query);
			$db->execute();
			//delete doc of customer
			jimport('joomla.filesystem.folder');
			if(JFolder::exists(JPATH_ADMINISTRATOR.'/documents/'.$table->id)){
				JFolder::delete(JPATH_ADMINISTRATOR.'/documents/'.$table->id);
			}			
			$query->clear();
			$query->delete('#__bookpro_customer_doc')->where('customer_id='.$table->id);
			$db->setQuery($query);
			$db->execute();
			//delete pn and pn log of customer
			$query->clear();
			$query->delete('#__bookpro_customer_pn')->where('user_id='.$table->id);
			$db->setQuery($query);
			$db->execute();
			//delete vehicle if this is driver
			$query->clear();
			$query->delete('#__bookpro_vehicle')->where('driver_id='.$table->id);
			$db->setQuery($query);
			$db->execute();
			//delete joomla user and customer will be delete by constrain in database
			$user = JFactory::getUser($table->user_id);
			if($user->id){				
				$result = $user->delete();
			}else{
				JFactory::getApplication()->enqueueMessage('User not existed');
				AImporter::helper('android');
				AndroidHelper::write_log('bookpro.txt','delete user failed. User id: '.$table->user_id.'. Customer id: '.$table->id);
			}
		}
		return $result;
	}
	
	
	public function vieworder(){
		
		JSession::checkToken() or die(JText::_('JINVALID_TOKEN'));
		
		// Get items to remove from the request.
		$cid = JFactory::getApplication()->input->get('cid', array(), 'array');
		
		if (!is_array($cid) || count($cid) < 1)
		{
			JLog::add(JText::_($this->text_prefix . '_NO_ITEM_SELECTED'), JLog::WARNING, 'jerror');
			$this->setRedirect(JRoute::_('index.php?option=com_bookpro&view=customers', false));
		}
		else
		{
		
			// Sanitize the input
			jimport('joomla.utilities.arrayhelper');
			JArrayHelper::toInteger($cid);
			$customer_id = $cid[0];
			
			AImporter::model('orders');
			$model = new BookProModelOrders();
			$state = $model->getState();
			require_once JPATH_ADMINISTRATOR.'/components/com_bookpro/tables/customer.php';
		  	$db = JFactory::getDbo();
		  	$table = new TableCustomer($db);
		  	$table->load($customer_id);
			//var_dump($table);die;
			if($table->user_type!=3){
				$state->set('filter.customer_id',$customer_id);
			}else{
				$state->set('filter.driver_id',$customer_id);
			}			
			$view = $this->getView('Orders', 'html', 'BookProView' );
			$view->setModel($model,true);
			$view->display();
			return;
			//dump($customer_id); die;
			//$this->setRedirect(JRoute::_('index.php?option=com_bookpro&view=orders&customer_id='. $customer_id, false));
		}
	}
	
	public function filterOnlineCustomer(){
		$user_type = JFactory::getApplication()->input->getInt('user_type');
		$model = $this->getModel('Customers');
		$state = $model->getState();
		$state->set('filter.user_type',$user_type);
		$state->set('filter.timeout',JComponentHelper::getParams('com_bookpro')->get('timeout_online',5));
		$view = $this->getView('Customers', 'html', 'BookProView' );
		$view->setModel($model,true);
		$view->display();
		return;
	}
	
}