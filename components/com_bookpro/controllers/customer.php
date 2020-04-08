<?php
/**
 * @package 	Bookpro
 * @author 		Ngo Van Quan
 * @link 		http://joombooking.com
 * @copyright 	Copyright (C) 2011 - 2012 Ngo Van Quan
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id$
 **/
defined('_JEXEC') or die('Restricted access');

class BookProControllerCustomer extends JControllerLegacy{
	function __construct($cachable = false, $config=array())
	{

		parent::__construct($config);
	}

	function changepassword()
	{
		$mainframe = &JFactory::getApplication();
		$return = JRequest::getVar('return',0);
		$return = base64_decode($return);
		$user_data = $_POST;
		if($user_data['password'] == $user_data['password2']){
			$user = JFactory::getUser();
			$salt = JUserHelper::genRandomPassword(32);
			$crypt = JUserHelper::getCryptedPassword(JString::trim($user_data['password']), $salt);
			$password = $crypt.':'.$salt;
			$user->set('password',  $password);
			if($user->save()) $mainframe->enqueueMessage(JText::_('Successfully saved'), 'message');
		}else {
			JError::raiseWarning('', JText::_('Passwords do not match. Please re-enter password.'));
		}
		$mainframe->redirect('index.php?option=com_bookpro&view=mypage&form=password');
	}

	function save($apply = false)
	{
		JRequest::checkToken() or jexit('Invalid Token');
		$mainframe = &JFactory::getApplication();
		/* @var $mainframe JApplication */
		$user = &JFactory::getUser();
		/* @var $user JUser */
		$config = &AFactory::getConfig();
		$post = JRequest::get('post');
		AImporter::model('customer');
		$model=new BookProModelCustomer();

		if ($user->id) {
			$customer=$model->getItemByUser();
			$post['id'] = $customer->id;
		}else{
			$post['id'] = 0;
		}
		$isNew = $post['id'] == 0;
		$id = $model->save($post);

		if ($id !== false) {
			$mainframe->enqueueMessage(JText::_('Successfully saved'), 'message');
		}else{
			$mainframe->enqueueMessage(JText::_('Save failed'), 'error');
		}
		$mainframe->redirect('index.php?option=com_bookpro&view=mypage&form=profile&Itemid='.JRequest::getVar('Itemid'));
	}


	function getcustomer(){

		if(!class_exists('BookProModelCustomer')){
			AImporter::model('customer');
		}
		$user=JFactory::getUser();
		$model=new BookProModelCustomer();
		$customer=$model->getItem( $model->getIdByUserId());
		echo json_encode($customer);
		die() ;

	}
	function cancel_order(){
		$mainframe=JFactory::getApplication('site');
		$order_id=JRequest::getVar('order_id');
		if (! class_exists('BookProModelOrder')) {
			AImporter::model('orders');
		}
		$model= new BookProModelOrders();
		$model->setId($order_id);
		$order=$model->getObject();
		$order->order_status='CANCELLED';
		if (!$order->store()) {
			JError::raiseError(500, $row->getError() );
		}
		$mainframe->redirect(JURI::root().'index.php?option=com_bookpro&view=mypage');
		return;

	}
	function login(){

		$mainframe=JFactory::getApplication('site');
		$return = JRequest::getVar('return', '', 'method', 'base64');
		$return = base64_decode($return);
		$options = array();
		$options['remember'] = JRequest::getBool('remember', false);
		$options['return'] = $return;
		$credentials = array();
		$credentials['username'] = JRequest::getVar('username', '', 'method', 'username');
		$credentials['password'] = JRequest::getString('passwd', '', 'post', JREQUEST_ALLOWRAW);
		//preform the login action
		$error = $mainframe->login($credentials, $options);
		echo $error;
		die();
	}

	public function bplogin()
	{
		//JSession::checkToken('post') or jexit(JText::_('JInvalid_Token'));
		
		$input = JFactory::getApplication()->input;

		$mainframe=JFactory::getApplication('site');
		$return = JRequest::getVar('return', '', 'method', 'base64');
		$return1 = JRequest::getVar('return', '', 'method', 'base64');
		$return = base64_decode($return);
		$options = array();
		$options['remember'] = $input->getInt('remember');
		$options['return'] = $return;
		$credentials = array();
		$credentials['username'] = JRequest::getVar('username', '', 'method', 'username');
		$credentials['password'] = JRequest::getString('password', '', 'post', JREQUEST_ALLOWRAW);
		$result= $mainframe->login($credentials, $options);

		if ($result) {
			// Success
			//Check user permission
			AImporter::helper('bookpro');
			$isAgent = BookProHelper::isAgent();
			if($isAgent){				
				$mainframe->redirect(JRoute::_('index.php?option=com_bookpro&view=agentorders'));
			}
			else{
				$mainframe->redirect(JRoute::_('index.php?option=com_bookpro&view=mypage'));
			}
		}else{
			$mainframe->redirect(JURI::base().'index.php?option=com_bookpro&view=login&return='.$return1);
		}
	}


	function checkUsernamePHP($username){
		// check if already used
		$db	   = JFactory::getDbo();
		$query =$db->getQuery(true);
		$query->select('id')->from('#__users AS u')->where('u.username='.$db->quote($username));
		$db->setQuery( $query );
		$usralreadyexist = $db->loadResult();
		if( $usralreadyexist){
			return false;
		}
		return true;
	}
	/**
	 * Check email of old user
	 * @param unknown $email
	 * @param unknown $user_id
	 * @return boolean
	 */
	function checkEmailPHPofUser($email,$user_id){
		$email = trim($email);
		$email = str_replace("'", "", $email);
		$db	   =& JFactory::getDBO();
		$query = "SELECT id FROM #__users WHERE `email`='".$email."' AND `id` != ".$user_id." LIMIT 1";
		$db->setQuery( $query );
		$emailalreadyexist = $db->loadResult();
		if($emailalreadyexist){
			return false;
		}
		return true;
	}

	/**
	 * Check email of new user
	 * @param unknown $email
	 * @param unknown $user_id
	 * @return boolean
	 */
	function checkEmailPHP($email){
		$email = trim($email);
		$email = str_replace("'", "", $email);
		$db	   = JFactory::getDBO();
		$query = "SELECT id FROM #__users WHERE `email`='".$email."' LIMIT 1";
		$db->setQuery( $query );
		$emailalreadyexist = $db->loadResult();
		if($emailalreadyexist){
			return false;
		}
		return true;
	}
	
	function registrationparticular()
	{
		JSession::checkToken() or die( JText::_( 'Invalid Token' ));
		$config=AFactory::getConfig();
		$mainframe = &JFactory::getApplication();
		$params=JComponentHelper::getParams('com_users');
		$useractivation = $params->get('useractivation');
		$input=$mainframe->input;
		$post = $input->getArray($_POST);
		$post['password']= JApplicationHelper::getHash(JUserHelper::genRandomPassword());
		//debug($useractivation);
		//debug(json_encode($post)); die;
		//$post = json_encode($post);
		$db=JFactory::getDbo();
		try{
			$db->transactionStart();
	
			if(!$this->checkEmailPHP($post['email'])){
				$mainframe->enqueueMessage(JText::_('COM_BOOKPRO_EMAIL_EXISTS'), 'warning');
				$view=$this->getView('registrationparticular','html','BookProView');
				$view->assign('obj',(object)$post);
				$view->display();
				return;
			}

			AImporter::table('customer');
			$customerTable=new TableCustomer($db);
			$customerTable->bind($post);
			$customerTable->social_reason='';
			$customerTable->function='';
			$customerTable->store();
			$db->transactionCommit();
	
	
		}catch (Exception $e){
			$mainframe->enqueueMessage($e->getMessage());
			$db->transactionRollback();
			$this->setRedirect(JUri::base().'index.php?option=com_bookpro&view=registrationparticular');
		}
	
		//handle email notification
	
			$config = JFactory::getConfig();
			$post['fromname'] = $config->get('fromname');
			$post['mailfrom'] = $config->get('mailfrom');
			$post['sitename'] = $config->get('sitename');
			$post['siteurl'] = JUri::root();
			$uri = JUri::getInstance();
			$base = $uri->toString(array('scheme', 'user', 'pass', 'host', 'port'));
			//$post['activate'] = $base . JRoute::_('index.php?option=com_users&task=registration.activate&token=' . $post['activation'], false);
				
				
			$emailSubject = JText::sprintf(
					'COM_BOOKPRO_EMAIL_ACCOUNT_DETAILS',
					$post['name'],
					JUri::base()
			);
	
			$emailBody = JText::sprintf('COM_BOOKPRO_EMAIL_REGISTERED_WITH_ACTIVATION_BODY',
					$post['name'],
					$post['sitename']
			);
			$return = JFactory::getMailer()->sendMail($post['mailfrom'], $post['fromname'], $post['email'], $emailSubject, $emailBody);
	
		//redirect to complete view
			//$this->setMessage(JText::_('COM_USERS_REGISTRATION_COMPLETE_ACTIVATE'));
			//$this->setRedirect(JRoute::_('index.php?option=com_bookpro&view=registration&layout=complete', false));
			return;
	
	}

	function registrationenterprise()
	{
		JSession::checkToken() or die( JText::_( 'Invalid Token' ));
		$config=AFactory::getConfig();
		$mainframe = &JFactory::getApplication();
		$params=JComponentHelper::getParams('com_users');
		$useractivation = $params->get('useractivation');
		$input=$mainframe->input;
		$post = $input->getArray($_POST);
		$post['password']= JApplicationHelper::getHash(JUserHelper::genRandomPassword());
		//debug($useractivation);
		//debug($post);
		//debug(json_encode($post)); die;
		//$post = json_encode($post);
		// 		if (($useractivation == 1) || ($useractivation == 2)){
		// 			$post['activation']= JApplicationHelper::getHash(JUserHelper::genRandomPassword());
		// 		}
		$db=JFactory::getDbo();
		try{
			$db->transactionStart();
	
	
			if(!$this->checkEmailPHP($post['email'])){
				$mainframe->enqueueMessage(JText::_('COM_BOOKPRO_EMAIL_EXISTS'), 'warning');
				$view=$this->getView('registrationenterprise','html','BookProView');
				$view->assign('obj',(object)$post);
				$view->display();
				return;
			}
	
			AImporter::table('customer');
			$customerTable=new TableCustomer($db);
			$customerTable->bind($post);
			$customerTable->store();
			$db->transactionCommit();
			$this->setRedirect(JUri::base().'index.php?option=com_bookpro&view=registrationenterprise');
			$this->setMessage(JText::_('COM_USERS_REGISTRATION_ENTERPRISE_COMPLETE'));
	
		}catch (Exception $e){
			$mainframe->enqueueMessage($e->getMessage());
			$db->transactionRollback();
			$this->setRedirect(JUri::base().'index.php?option=com_bookpro&view=registrationenterprise');
		}
	
		//handle email notification

		//redirect to complete view
		//$this->setMessage(JText::_('COM_USERS_REGISTRATION_ENTERPRISE_COMPLETE'));
		//$this->setRedirect(JRoute::_('index.php?option=com_users&view=registration&layout=complete', false));
		return;
	
	}
	function registrationdriver()
	{
		JSession::checkToken() or die( JText::_( 'Invalid Token' ));
		$config=AFactory::getConfig();
		$mainframe = &JFactory::getApplication();
		$params=JComponentHelper::getParams('com_users');
		$useractivation = $params->get('useractivation');
		$input=$mainframe->input;
		$post = $input->getArray($_POST);
		$post['password']= JApplicationHelper::getHash(JUserHelper::genRandomPassword());
		$db=JFactory::getDbo();
		try{
			$db->transactionStart();
	
			if(!$this->checkEmailPHP($post['email'])){
				$mainframe->enqueueMessage(JText::_('COM_BOOKPRO_EMAIL_EXISTS'), 'warning');
				$view=$this->getView('registrationdriver','html','BookProView');
				$view->assign('obj',(object)$post);
				$view->display();
				return;
			}
	
			AImporter::table('customer');
			$customerTable=new TableCustomer($db);
			$customerTable->bind($post);
			$customerTable->store();
			$db->transactionCommit();
			
			$this->setRedirect(JUri::base().'index.php?option=com_bookpro&view=registrationdriver');
			$this->setMessage(JText::_('COM_USERS_REGISTRATION_DRIVER_COMPLETE'));
		}catch (Exception $e){
			$mainframe->enqueueMessage($e->getMessage());
			$db->transactionRollback();
			$this->setRedirect(JUri::base().'index.php?option=com_bookpro&view=registrationdriver');
		}
	
		//handle email notification
	
		//redirect to complete view
		//$this->setMessage(JText::_('COM_USERS_REGISTRATION_DRIVER_COMPLETE'));
		//$this->setRedirect(JRoute::_('index.php?option=com_users&view=registration&layout=complete', false));
		return;
	
	}
	
	function register()
	{
		JSession::checkToken() or die( JText::_( 'Invalid Token' ));
		$config=AFactory::getConfig();
		$mainframe = &JFactory::getApplication();
		$params=JComponentHelper::getParams('com_users');
		$useractivation = $params->get('useractivation');
		$input=$mainframe->input;
		$post = $input->getArray($_POST);
		if (($useractivation == 1) || ($useractivation == 2)){
			$post['activation']= JApplicationHelper::getHash(JUserHelper::genRandomPassword());
		}
		$db=JFactory::getDbo();
		try{
			$db->transactionStart();
			//save user
			$post['name'] = $post['firstname'].' '.$post['lastname'];
			$cuser=new JUser();
			$cuser->bind($post);
			//check Username
			if(!$this->checkUsernamePHP($post['username'])){
				$mainframe->enqueueMessage(JText::_('COM_BOOKPRO_USERNAME_EXISTS'), 'warning');
				$view=$this->getView('register','html','BookProView');
				$view->assign('customer',(object)$post);
				$view->display();
				return;
			}

			if(!$this->checkEmailPHP($post['email'])){
				$mainframe->enqueueMessage(JText::_('COM_BOOKPRO_EMAIL_EXISTS'), 'warning');
				$view=$this->getView('register','html','BookProView');
				$view->assign('obj',(object)$post);
				$view->display();
				return;
			}

			
			$cuser->block=1;
			if($post['group_id']){
				$cuser->groups = array($post['group_id']);
			}else{
				$cuser->groups = array($config->customersUsergroup);
			}
			$cuser->sendEmail = 1;
			
			$cuser->save();
			//Save customer
			AImporter::table('customer');
			$customerTable=new TableCustomer($db);
			$customerTable->bind($post);
			$customerTable->user=$cuser->id;
			$customerTable->state=1;
			$customerTable->store();
			$db->transactionCommit();


		}catch (Exception $e){
			$mainframe->enqueueMessage($e->getMessage());
			$db->transactionRollback();
			$this->setRedirect(JUri::base().'index.php?option=com_bookpro&view=register');
		}

		//handle email notification
		if($useractivation == 1){

			$config = JFactory::getConfig();
			$post['fromname'] = $config->get('fromname');
			$post['mailfrom'] = $config->get('mailfrom');
			$post['sitename'] = $config->get('sitename');
			$post['siteurl'] = JUri::root();
			$uri = JUri::getInstance();
			$base = $uri->toString(array('scheme', 'user', 'pass', 'host', 'port'));
			$post['activate'] = $base . JRoute::_('index.php?option=com_users&task=registration.activate&token=' . $post['activation'], false);
			
			
			$emailSubject = JText::sprintf(
                    'COM_BOOKPRO_EMAIL_ACCOUNT_DETAILS',
			$post['name'],
			JUri::base()
			);

			$emailBody = JText::sprintf('COM_BOOKPRO_EMAIL_REGISTERED_WITH_ACTIVATION_BODY',
			$post['name'],
			$post['sitename'],
			$post['activate'],
			JUri::base(),
			$post['username']
			);
			$return = JFactory::getMailer()->sendMail($post['mailfrom'], $post['fromname'], $post['email'], $emailSubject, $emailBody);

		}
		//redirect to complete view
		if (($useractivation == 1) || ($useractivation == 2)){

			$this->setMessage(JText::_('COM_USERS_REGISTRATION_COMPLETE_ACTIVATE'));
			$this->setRedirect(JRoute::_('index.php?option=com_users&view=registration&layout=complete', false));
			return;

		}
			
		echo "register failed";
		die;
	}

	function checkusername(){
		$arg_params = &JComponentHelper::getParams( 'com_bookpro' );
		$username = JRequest::getVar('username', '', 'post', 'username');
		if($username)
		{
			// check Username if already used
			$db	   =& JFactory::getDBO();
			$query = "SELECT id FROM #__users WHERE `username`='".$username."' LIMIT 1";
			$db->setQuery( $query );
			$usralreadyexist = $db->loadResult();
			// check if blocked
			$notAccepted = 0;

			if( $usralreadyexist || $notAccepted)
			{
				// already in use
				echo '<span class="invalid">'.JText::_( 'COM_BOOKPRO_USERNAME_EXISTS' ).'</span>';
			}else
			{
				echo 'OK';
			}
		}
		die;
	}

	function checkemail(){
		$email = JRequest::getVar('email', '', 'post', 'string');
		$email = trim($email);
		$email = str_replace("'", "", $email);
		if($email)
		{
			$db	   =& JFactory::getDBO();
			$query = "SELECT id FROM #__users WHERE `email`='".$email."' LIMIT 1";
			$db->setQuery( $query );
			$emailalreadyexist = $db->loadResult();
			if( $emailalreadyexist )
			{
				echo '<span class="invalid">'.JText::_( 'COM_BOOKPRO_EMAIL_EXISTS' ).'</span>';
			}else
			{
				echo 'OK';
			}
		}
		die;
	}
	
	public function logout(){
		
		JFactory::getApplication()->logout();
		$url = JRoute::_('index.php?option=com_bookpro&view=login');
// 		$url = JRoute::_('index.php?option=com_users&task=user.logout&' . JSession::getFormToken() . '=1&return=' . $return, false);		
		$this->setRedirect($url);
		return;		
	
	}

}