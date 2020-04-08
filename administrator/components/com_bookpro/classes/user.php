<?php

/**
 * @package 	Bookpro
* @author 		Ngo Van Quan
* @link 		http://joombooking.com
* @copyright 	Copyright (C) 2011 - 2012 Ngo Van Quan
* @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
* @version 	$Id: airport.php 66 2012-07-31 23:46:01Z quannv $
**/

/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access');

require_once JPATH_ADMINISTRATOR.'/components/com_bookpro/classes/session.php';
class BookproUser extends JObject
{
	public $data;
	public $id;
	public $session;
	public $type_list;
	public $type;
	public $error_code;
	public $location;
	public $table;
	
	public function __construct($id = null){
		$this->id = $id;
		$this->type_list = array(
			'particular'=>"1",
			'enterprise'=>"2",
			'driver'=>"3"
			);
		$this->error_code = 0;
	}
	
	private function getUserByName($username){
		if(empty($username)){
			return false;
		}
		$db =JFactory::getDbo();
		$query = $db->getQuery(true)
			->select('*')
			->from($db->quoteName('#__users'))
			->where('username LIKE ' .$db->quote($username));
		$db->setQuery($query);
		$result =  $db->loadObject();
		return $result;
	}
	
	public function login($credentials){
		
		$app = JFactory::getApplication();		
		$credentials['username'] = $credentials['email'];
		$options = array();
    	$options['silent'] = true;
		
		if ($app->login($credentials, $options) === true)
	    {
			
	      	$user = JFactory::getUser();
	      	$userid= $user->get('id');
			
	      	if(!$userid){
				$user = $this->getUserByName($credentials['username']);
				if($user->id && $user->block){
					$userid = $user->id;
				}else{
					$this->error_code = '13';//user is blocked
					return false;
				}
	      		
	      	}
		  	$db =JFactory::getDbo();
			$query = $db->getQuery(true)
				->select('*')
				->from('#__bookpro_customer')
				->where('user_id =' .$userid);
			$db->setQuery($query);
			$result =  $db->loadObject();
			if(!$result->id){
				$this->error_code = '11';
				return false;
			}else{
				$this->data = $result;
				$this->id 	= $result->id;
				
				if($result->state){
					$this->session = new BookproSession(array('userid'=>$result->id));
					$this->session->loadSessionByUserId();
					
					if(!$this->session->session_id){
						if($result->user_type == $this->type_list['driver']){
						
							$db =JFactory::getDbo();
							$query = $db->getQuery(true);
							$query->select('count(*)')
							->from('#__bookpro_orders')
							->where('(driver_id = '.(int)$this->session->userid.')')
							->where('trip_status !=2 AND is_accepted = 1 AND is_cancelled = 0');
							$db->setQuery($query);
							//			echo $query;die;
							$result = $db->loadResult();
							if($result){
								$this->session->free = 0;
							}else{
								$this->session->free = 1;
							}
							$is_driver = true;
							
						}else{
							$is_driver= false;
							$this->session->free  = 0;
						}						
						
				
						//set current vehicle for the driver
						$this->session->setCurrentVehicle($is_driver);
						
					}else{
						//delete session if it is login already
						if($result->user_type == $this->type_list['driver']){
							$this->session->destroy();
							$this->session->session_id = false;
							$this->error_code = 17;
							$this->session->setCurrentVehicle(true);
						}
						
					}
					
					$this->session->time = JFactory::getDate()->toUnix();
					if(!$this->session->saveSession()){
						$this->error_code = '11';//save session error
					}else{
						$this->error_code = '0';
					}					
					return true;
				}else{
					$this->error_code = '12';
				}
			}
	    }else{
		    $this->error_code = '11';//invalid email or password
	    }
		return false;
	}
	
	public function updateSession(){
		if($this->session->userid){				
			return $this->session->saveSession();
		}
		return false;
		
	}
	
	public function updateLocation(){
		if(isset($this->session->data->current_order)){
			//update trip every 120s
			if($this->session->data->current_order->lastupdate < ($this->session->time - 120)){
				$this->session->data->current_order->lastupdate = JFactory::getDate()->toUnix();				
				//update location of trip
				require_once JPATH_ADMINISTRATOR.'/components/com_bookpro/tables/orders.php';
				$db = JFactory::getDbo();
				$table = new TableOrders($db);
				$table->load($this->session->data->current_order->id);
				//if order is cancelled or end or not existed
				if($table->id && $table->trip_status != 2 && !$table->is_cancelled){
					$trip_location = json_decode($table->trip_location);
					$trip_location[] = (object)array('latitude'=>$this->session->lat,'longitude'=>$this->session->lng,'updated_time'=>JHtml::_('date','now','Y-m-d H:i:s'));
					$table->trip_location = json_encode($trip_location);									
					$table->store();
				}else{
					unset($this->session->data->current_order);
				}
			}
		}
		$this->session->time = JFactory::getDate()->toUnix();
		return $this->updateSession();
	}
	
	private function createActiveCode(){
		$session_id = '';
		$chars = "0123456789ABCDEFGHIJKLMNOPQRSTWVXYZ";
		srand((double)microtime()*1000000);
		$i = 0;
		$total_length = strlen($chars);
		while ($i <= 5) {
			$num = rand() % $total_length;
			$tmp = substr($chars, $num, 1);
			$session_id .= $tmp;
			$i++;
		}
		return $session_id;
	}
	
	public function register($data){
		if(empty($data) || empty($data['email']) || empty($data['password'])){
			$this->error_code = '0';
			return false;
		}
		//debug($data);die;
		if(!$this->checkEmailPHP($data['email'])){
			$this->error_code = '14';
			return false;
		}
		$data['state'] = 0;
  		$data['active']  = 1;
		$data['name'] = $data['name'] ? $data['name'] : $data['firstname'].' '.$data['lastname'];
		//add joomla user
		$user = new JUser();				
		$user->bind($data);	
		$user->name = $data['name'];
		$user->username = $data['email'];
		//$user->block = 0;
		$user->sendEmail = 0;
		$user->groups = array(2);	
		$user->save();		
		//add customer
		if(!$user->id){
			throw new Exception($user->getError());
			$this->error_code = '0';
			$this->error_msg=  $user->getError();
			return false;
		}
		$data['user_id'] = $user->id;
					
		$data['params'] = new JObject();
		if($data['user_type'] == $this->type_list['particular'] || $data['user_type'] == $this->type_list['enterprise']){
			$data['params']->active_code = $this->createActiveCode();
			$data['state'] = 1;
		}		
		
		$this->data = $data;
		$result = $this->save();
		
		if($result){
			return true;
			//send sms if type is particular
	  		if($data['user_type'] == $this->type_list['particular'] || $data['user_type'] == $this->type_list['enterprise']){	  			
	  			$dispatcher    = JDispatcher::getInstance();
				$import 	= JPluginHelper::importPlugin('bookpro','product_sms' );				
				if ($import){				
					require_once JPATH_ADMINISTRATOR.'/components/com_bookpro/models/application.php';
					$applicationModel	= new BookProModelApplication();
  					$msg = $applicationModel->getObjectByCode('REGISTER_CONFIRM_MSG_SMS');
					
					$sms_content = array(
						'sms'=>str_replace('{code}', $data['params']->active_code, $msg->email_customer_body),
						'phone'=>$data['mobile']
					);
					$send = $dispatcher->trigger( "onBookproSendSms", array($sms_content));
					
					if(empty($send[0][0]->id)){
						$this->error_code = '18';
						return false;
					}
				}else{
					$this->error_code = '18';
					return false;
				}
				
	  		}
	  		return true;
		}else{
			$this->error_code = '0';
			return false;
		}
		
	}	
	
	private function checkEmailPHP($email){
		$email = trim($email);
		$email = str_replace("'", "", $email);
		$db	   = JFactory::getDBO();
		$query = "SELECT id FROM #__bookpro_customer WHERE `email`='".$email."' LIMIT 1";
		$db->setQuery( $query );
		$emailalreadyexist = $db->loadResult();
		if($emailalreadyexist){
			return false;
		}
		return true;
	}

	
	private function loadTable(){
		if($this->table->id){
			return true;
		}
		if($this->id){
			return $this->table->load($this->id);			
		}
		return false;
	}
	
	private function getTable(){
		require_once JPATH_ADMINISTRATOR.'/components/com_bookpro/tables/customer.php';
		$db = JFactory::getDbo();
		$this->table = new TableCustomer($db);
		return $this->table;
	}
	
	public function getData(){
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('*')
			->from('#__bookpro_customer')
			->where('id = '.(int)$this->id);
		$db->setQuery($query);
		$data = $db->loadObject();
		$this->data = (array)$data;
		$data= null;unset($data);//clear memory
		return $this->data;
	}
	
	public function save(){		
		$data = $this->data;
		if(is_object($this->data['params'])){
			$data['params'] = json_encode($this->data['params']);
		}else{
			//create new Object for params
			$data['params'] = '{}';
		}
		if(!$this->table){
			$this->getTable();
		}
		return $this->table->save($data);
	}
	
	public function changePassword(){
		//use $this->id;		
		JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_bookpro/tables');
		$customer = JTable::getInstance('customer', 'table');
		$customer->load(array('id'=>$this->id));
		$user = JFactory::getUser( $customer->user_id);
		$salt = JUserHelper::genRandomPassword(32);
	//	$crypt = JUserHelper::getCryptedPassword(JString::trim($this->data->current_password), $salt);
	//	$current_password = $crypt.':'.$salt;
		//$this->data->current_password = JUserHelper::hashPassword($this->data->current_password);
		//$this->data->current_password= JApplicationHelper::getHash(JUserHelper::genRandomPassword());
		//$this->data->current_password = md5($this->data->current_password);
		//var_dump($current_password);
		//var_dump($user->password);
	//	die;
	
		//check current password by login with this current pass
		$username = $user->username;
		$password = ($this->data['current_password']);
		$options = array();
    	$options['silent'] = true;
		if(JFactory::getApplication()->login(array("username"=>$username,"password"=>$password))) {
			//echo "dung"; change password $user->password = new password
			$crypt = JUserHelper::getCryptedPassword(JString::trim($this->data['new_password']), $salt);
			$new_password = $crypt.':'.$salt;
			$user->password= $new_password;
			return $user->save();
		}else {
			return false;
				
		}

		if(!$save){
			return false;
		}
		return true;
	}	
}	

?>

		
	