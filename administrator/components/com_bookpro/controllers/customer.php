<?php
/**
 * @package 	Bookpro
 * @author 		Ngo Van Quan
 * @link 		http://joombooking.com
 * @copyright 	Copyright (C) 2011 - 2012 Ngo Van Quan
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: customer.php 23 2012-07-08 02:20:56Z quannv $
 **/

defined('_JEXEC') or die('Restricted access');


class BookProControllerCustomer extends JControllerForm
{
	
	public function cancelregister(){
		parent::cancel();
		$this->setRedirect('index.php?option=com_bookpro&view=customers&state=0&layout=registration');
		return true;
	}

	public function directapprove(){
		$input = JFactory::getApplication ()->input;
		$id = $input->getInt('id');
		//debug($id); die;
		//$this->setRedirect('index.php?option=com_bookpro&controller=customer&task=approve&id='.$id);
		return;
	}
	
	function agent(){

		AImporter::model('customer');
		$config		 		= JComponentHelper::getParams('com_bookpro');
		$agent_group 		= $config->get('agent_usergroup');

		$db		= JFactory::getDbo();
		$app	= JFactory::getApplication();
		$input	= $app->input;
		$cid	= $input->get('cid','','array');
		$checked=0;
		

			if($cid){
				foreach ($cid as $key => $customer_id){
					$customerModel 	= new BookProModelCustomer();
					$customer 		= $customerModel->getItem($customer_id);
					
					if($customer->user){
						$checked++;
						$user_id = (int)$customer->user;
						
						$query	= $db->getQuery(true);
						$query->delete('#__user_usergroup_map');
						$query->where('user_id='.$user_id);
						$db->setQuery($query);
						$db->query();
					
						
						$query1=$db->getQuery(true);
						$query1->insert('#__user_usergroup_map');
						$query1->columns('user_id,group_id');
						$temp=array($user_id, $agent_group);
						$values=implode(',', $temp);
						$query1->values($values);
						$db->setQuery($query1);
						$db->execute();
						$query1->clear();
					}
				}
			}

		if($checked){
			JFactory::getApplication ()->enqueueMessage ( JText::_('Update successful'), 'message');
		}else{
			JFactory::getApplication ()->enqueueMessage(JText::_('Update failed'), 'error');
		}
		
		$mainframe = JFactory::getApplication();
		$mainframe->redirect('index.php?option=com_bookpro&view=customers');
	}
	
// 	function supplier(){

// 		AImporter::model('customer');
// 		$config		 		= JComponentHelper::getParams('com_bookpro');
// 		$supplier_usergroup	= $config->get('supplier_usergroup');

// 		$db		= JFactory::getDbo();
// 		$app	= JFactory::getApplication();
// 		$input	= $app->input;
// 		$cid	= $input->get('cid','','array');
// 		$checked=0;
		

// 			if($cid){
// 				foreach ($cid as $key => $customer_id){
// 					$customerModel 	= new BookProModelCustomer();
// 					$customer 		= $customerModel->getItem($customer_id);
					
// 					if($customer->user){
// 						$checked++;
// 						$user_id = (int)$customer->user;
						
// 						$query	= $db->getQuery(true);
// 						$query->delete('#__user_usergroup_map');
// 						$query->where('user_id='.$user_id);
// 						$db->setQuery($query);
// 						$db->query();
					
						
// 						$query1=$db->getQuery(true);
// 						$query1->insert('#__user_usergroup_map');
// 						$query1->columns('user_id,group_id');
// 						$temp=array($user_id, $supplier_usergroup);
// 						$values=implode(',', $temp);
// 						$query1->values($values);
// 						$db->setQuery($query1);
// 						$db->execute();
// 						$query1->clear();
// 					}
// 				}
// 			}

// 		if($checked){
// 			JFactory::getApplication ()->enqueueMessage ( JText::_('Update successful'), 'message');
// 		}else{
// 			JFactory::getApplication ()->enqueueMessage(JText::_('Update failed'), 'error');
// 		}
		
// 		$mainframe = JFactory::getApplication();
// 		$mainframe->redirect('index.php?option=com_bookpro&view=customers');
// 	}
	
	public function applyApprove(){
		$this->approve();
		$this->setRedirect('index.php?option=com_bookpro&view=customer&layout=edit&id='.$this->input->getInt('id'));
		return true;
	}
	
	public function approve() {
	
		$input = JFactory::getApplication ()->input;
		$id = $input->getInt('id');
		try {
			AImporter::model('customer');
			$model=new BookproModelCustomer();
			$this->customer_user=$model->getItemCustomer_user($id);
			// update all
			$db = JFactory::getDbo();
			$query = $db->getQuery ( true );
			$query->update ( '#__bookpro_customer' );
			$state = 1;
			$query->set ( 'state = '.$state.' where id='.$id );
			$db->setQuery ( $query );
			$db->execute ();
	
			//change status block in table users
			$user = JFactory::getUser( $this->customer_user->user_id);
		//	$user->email = $data ['email'];
		//	$user->name= $data ['name'];
		//	$user->username = $data ['email'];
			$user->block=0;
			//			if($data['active'] == 1)
				// 			{
				// 				$user->block= 0;
				// 			}elseif ($data['active'] == 0){
				// 				$user->block=1;
				// 			}
			$user->save();
	//		JFactory::getApplication ()->enqueueMessage ( 'Update successful' );
			$db->transactionCommit ();
	
			$this->sendEmailCustomerApprove($id);
			
		}catch(Exception $e){
			JFactory::getApplication ()->enqueueMessage ($e->getMessage(),'error');
		}
		$this->setRedirect ( 'index.php?option=com_bookpro&view=customers&state=0&layout=registration',JText::_('COM_BOOKPRO_SENDMAIL_SUCCESS') );
		JFactory::getApplication ()->enqueueMessage ( 'Update successful' );
		return;
	}
	
	public  function sendEmailCustomerApprove($customer_id)
	{
		AImporter::helper('email');
		$mail=new EmailHelper();
		$email = $mail->sendEmailCustomerApprove($customer_id);
	
	
	}
	
	public function blockuser(){
		$input = JFactory::getApplication ()->input;
		$user_id = $input->getInt('user_id');
		$block = $input->getInt('block');
		//debug($user_id); debug($block);die();
		//change status block in table users
		$user = JFactory::getUser( $user_id);
		if($block == 1)
		{
			$user->block=0;
		}else 
		{
			$user->block=1;
		}
		$user->save();
		JFactory::getApplication ()->enqueueMessage ( 'Update successful' );
		$this->setRedirect ( 'index.php?option=com_bookpro&view=customers' );
		return;
	}
	

}

?>