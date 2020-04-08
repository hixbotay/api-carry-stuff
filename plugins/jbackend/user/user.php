<?php


use Joomla\Registry\Registry;

/**
 * jBackend user plugin for Joomla
 *
 * @author selfget.com (info@selfget.com)
 * @package jBackend
 * @copyright Copyright 2014 - 2015
 * @license GNU Public License
 * @link http://www.selfget.com
 * @version 2.1.3
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

require_once JPATH_ADMINISTRATOR.'/components/com_bookpro/helpers/android.php';
require_once JPATH_ADMINISTRATOR.'/components/com_bookpro/classes/user.php';
require_once JPATH_ADMINISTRATOR.'/components/com_bookpro/classes/session.php';


class plgJBackendUser extends JPlugin
{
	static $language;
	static $gateway = array();
  public function __construct(& $subject, $config)
  {
    parent::__construct($subject, $config);
    $this->loadLanguage();
    JFactory::getLanguage()->load('com_bookpro_msg_group');
  }
  
 public static function generateSuccess()
  {
  	$msg = array();
    $msg['status'] = 'ok';
    $msg['message'] = array(
        					'en'=>JText::_('EN_BOOKPRO_SUCCESS'),
        					'fr'=>JText::_('FR_BOOKPRO_SUCCESS'));
    return $msg;
  }

  public static function generateError($errorCode)
  {
    $error = array();
    $error['status'] = 'ko';
    switch($errorCode) {
    	case '0':
    		$error['code'] = '0';
    		$error['message'] = array(
    				'en'=>JText::_('System error'),
    				'fr'=>JText::_('System error'));
    		break;
      case '10':
        $error['code'] = '10';
        $error['message'] = array(
        					'en'=>JText::_('EN_BOOKPRO_SESSION_EXPIRED'),
        					'fr'=>JText::_('FR_BOOKPRO_SESSION_EXPIRED'));
        break;
      case '11':
        $error['code'] = '11';
        $error['message'] = array(
        					'en'=>JText::_('EN_BOOKPRO_ACCOUNT_INVAILD_USERNAME'),
        					'fr'=>JText::_('FR_BOOKPRO_ACCOUNT_INVAILD_USERNAME'));
        break;
      case '12':
        $error['code'] = '12';
        $error['message'] = array(
        					'en'=>JText::_('EN_BOOKPRO_ACCOUNT_INACTIVE'),
        					'fr'=>JText::_('FR_BOOKPRO_ACCOUNT_INACTIVE'));
        break;
      case '13':
        $error['code'] = '13';
        $error['message'] = array(
        					'en'=>JText::_('EN_BOOKPRO_ACCOUNT_IS_BLOCKED'),
        					'fr'=>JText::_('FR_BOOKPRO_ACCOUNT_IS_BLOCKED'));
        break;
      case '14':
        $error['code'] = '14';
        $error['message'] = array(
        					'en'=>JText::_('EN_BOOKPRO_EMAIL_IS_EXISTED'),
        					'fr'=>JText::_('FR_BOOKPRO_EMAIL_IS_EXISTED'));
        break;
        case '15':
        	$error['code'] = '15';
        	$error['message'] = array(
        			'en'=>JText::_('EN_BOOKPRO_SMS_CONFIRM_MSG_ACTIVE_CODE_INVALID'),
        			'fr'=>JText::_('FR_BOOKPRO_SMS_CONFIRM_MSG_ACTIVE_CODE_INVALID'));
        	break;
		case '16':
        	$error['code'] = '16';
        	$error['message'] = array(
        			'en'=>JText::_('EN_BOOKPRO_FORMAT_DATA_WRONG'),
        			'fr'=>JText::_('FR_BOOKPRO_FORMAT_DATA_WRONG'));
        	break;
		case '17':
        	$error['code'] = '17';
        	$error['message'] = array(
        			'en'=>JText::_('EN_BOOKPRO_LOGIN_ALREADY'),
        			'fr'=>JText::_('FR_BOOKPRO_LOGIN_ALREADY'));
        	break;
		case '18':
        	$error['code'] = '18';
        	$error['message'] = array(
        			'en'=>JText::_('EN_BOOKPRO_CANNOT_SEND_SMS_WARN'),
        			'fr'=>JText::_('FR_BOOKPRO_CANNOT_SEND_SMS_WARN'));
        	break;
        case '31':
        	$error['code'] = '31';
        	$error['message'] = array(
        			'en'=>JText::_('EN_BOOKPRO_SMS_CONFIRM_MSG_PASSWORD_NOT_CORRECT'),
        			'fr'=>JText::_('FR_BOOKPRO_SMS_CONFIRM_MSG_PASSWORD_NOT_CORRECT'));
        	break;
        case '33':
        	$error['code'] = '33';
        	$error['message'] = array(
        			'en'=>JText::_('EN_BOOKPRO_REPORT_DESCRIPTION_NULL'),
        			'fr'=>JText::_('FR_BOOKPRO_REPORT_DESCRIPTION_NULL'));
        	break;
        case '35':
        	$error['code'] = '35';
        	$error['message'] = array(
        			'en'=>JText::_('EN_BOOKPRO_DISCOUNT_CODE_INVALID'),
        			'fr'=>JText::_('FR_BOOKPRO_DISCOUNT_CODE_INVALID'));
        	break;		
        case '48':
        		$error['code'] = '48';
        		$error['message'] = array(
        				'en'=>JText::_('EN_BOOKPRO_ERROR_PAYMENT_FALIED_INVAILD_CARD'),
        				'fr'=>JText::_('FR_BOOKPRO_ERROR_PAYMENT_FALIED_INVAILD_CARD'));
        		break;
		 case '49':
        		$error['code'] = '49';
        		$error['message'] = array(
        				'en'=>JText::_('EN_BOOKPRO_ERROR_PAYMENT_DELETE_CARD'),
        				'fr'=>JText::_('FR_BOOKPRO_ERROR_PAYMENT_DELETE_CARD'));
        		break;
		case '98':
        	$error['code'] = '98';
        	$error['message'] = array(
        			'en'=>JText::_('EN_BOOKPRO_ERROR_INVALID_ACTION'),
        			'fr'=>JText::_('FR_BOOKPRO_ERROR_INVALID_ACTION'));
        	break;
		case '99':
        	$error['code'] = '99';
        	$error['message'] = array(
        			'en'=>JText::_('EN_BOOKPRO_ERROR_DUPLICATE_ACTION'),
        			'fr'=>JText::_('FR_BOOKPRO_ERROR_DUPLICATE_ACTION'));
        	break;
      default:
	      	$error['code'] = (string)$errorCode;
	        $error['message'] = array(
	        					'en'=>JText::_('EN_BOOKPRO_ERROR_EXCEPTION'),
	        					'fr'=>JText::_('EN_BOOKPRO_ERROR_EXCEPTION'));
	        break;
    }
    return $error;
  }


  /**
   * Action login
   *
   * @param   object    &$response   The response generated
   * @param   object    $status      The boundary conditions (e.g. authentication status)
   *
   * @return  boolean   true if there are no problems (status = ok), false in case of errors (status = ko)
   */
  
  public function actionLogin(&$response, &$status = null)
  {
//    $app = JFactory::getApplication();
    $request = AndroidHelper::getBodyRequest();
	
    $credentials = array();
    $credentials['email'] = $request->email;
    $credentials['password'] = ($request->password);
	
    $user = new BookproUser();
    $result = false;
	 
    if ($user->login($credentials) === true)
    {
      
        // Success
		if($user->error_code){
			$response = self::generateError($user->error_code);
		}else{
			$response['status'] = 'ok';
			$response['message'] = array(
        					'en'=>JText::_('EN_BOOKPRO_LOGIN_SUCCESS'),
        					'fr'=>JText::_('FR_BOOKPRO_LOGIN_SUCCESS'));
		}
        
        $response['data'] = new JObject();
        $response['data']->session_id 		= $user->session->session_id;
        $response['data']->user				= new JObject();
		$response['data']->user->id 		= $user->data->id;
        $response['data']->user->user_type 	= $user->data->user_type;
        $response['data']->user->company_name = $user->data->company_name;
        $response['data']->user->name 		= $user->data->name;
        $response['data']->user->function 	= $user->data->function;
        $response['data']->user->address 	= $user->data->address;
        $response['data']->user->city 		= $user->data->city;
        $response['data']->user->post_code 	= $user->data->post_code;
        $response['data']->user->mobile 	= $user->data->mobile;
        $response['data']->user->phone 		= $user->data->phone;
        $response['data']->user->email 		= $user->data->email;
        $result = true;
      
    } else {
      $response = self::generateError($user->error_code); // Login failed
    }
	//AndroidHelper::write_log('request.txt',json_encode($response));
    return $result;
  }
  
  public function actionLogout(&$response, &$status = null)
  {
		$session_id = AndroidHelper::getSessionId();
		$session = $this->loadSession($session_id);
		$response['status'] = 'ok';
		$response['message'] = array(
								'en'=>JText::_('EN_BOOKPRO_LOGOUT_SUCCESS'),
								'fr'=>JText::_('FR_BOOKPRO_LOGOUT_SUCCESS'));
		$response['data'] = new JObject();
		if(!$session){
			return true;
		}else{
			if($session->destroy()){
				return true;
			}else{
				$response = self::generateError(0);
				return false;
			}
						
		}		
  }
  
   public function actionUpdatelocation(&$response, &$status = null){
   		$session_id = AndroidHelper::getSessionId();
		$session = $this->loadSession($session_id);
		if(!$session){
			$response = self::generateError('10');
			return false;
		}else{
			$response['status'] = 'ok';
			$response['message'] = array(
		        					'en'=>JText::_('EN_BOOKPRO_UPDATE_SUCCESS'),
		        					'fr'=>JText::_('FR_BOOKPRO_UPDATE_SUCCESS'));
			$response['data'] = new JObject();
			$request = AndroidHelper::getBodyRequest();
			$session->lat = $request->location->latitude;
			$session->lng = $request->location->longitude;
			$user = new BookproUser();
			$user->session = $session;
			$user->updateLocation();
			return true;			
		}		
   		
   }
   
  public function actionRegister(&$response, &$status = null)
  {  
  	require_once JPATH_ADMINISTRATOR.'/components/com_bookpro/models/application.php';
  	$data = AndroidHelper::getBodyRequest();
	//print_r($data);die;
	$data = (array)$data;
  	$input = JFactory::getApplication()->input;
  	
  	if(empty($data['email']) || empty($data['password'])){
  		$response = self::generateError('14');
  		return false;
  	}
  	
	
  	$user = new BookproUser();
	$db = JFactory::getDbo();
	try{
		$db->transactionStart();
		$save = $user->register($data);
		$db->transactionCommit();
	}
	catch (Exception $e){
		$db->transactionRollback();
  		$response['status'] = 'ko';
  		$response['code'] = $e->getCode();
		$response['message'] = array(
				'en'=>$e->getMessage(),
				'fr'=>$e->getMessage()
		);
  		return false;
	}
  	if($save){
  		$response['status'] = 'ok';
  		$response['message'] = array(
        					'en'=>JText::_('EN_REGISTER_SUCCESS'),
        					'fr'=>JText::_('FR_REGISTER_SUCCESS'));
  		$response_data = new JObject();
		
  		if($data['user_type'] == $user->type_list['driver']){
  			$response_data->confirm_message = array(
        					'en'=>JText::_('EN_BOOKPRO_SMS_CONFIRM_MSG_ENTERPRISE'),
        					'fr'=>JText::_('FR_BOOKPRO_SMS_CONFIRM_MSG_ENTERPRISE'));
  			
  			
  		}else{
			/*
  			$response_data->confirm_message = array(
        					'en'=>JText::_('EN_BOOKPRO_SMS_CONFIRM_MSG_PARTICULAR'),
        					'fr'=>JText::_('FR_BOOKPRO_SMS_CONFIRM_MSG_PARTICULAR'));
							*/
			$response_data->confirm_message = array(
        					'en'=>JText::_('EN_REGISTER_SUCCESS'),
        					'fr'=>JText::_('FR_REGISTER_SUCCESS'));
  		}
		
  		$response['data'] = $response_data;
  		return true;
  	}else{
  		$response = self::generateError($user->error_code);
		$response['data'] = (object)array('confirm_message'=>$response['message']);
  		return false;
  	}
  }
 
  public function actionActive(&$response, &$status = null)
  {
  	$app = JFactory::getApplication();
  	$request = AndroidHelper::getBodyRequest();
  	$credentials = array();
  	require_once JPATH_ADMINISTRATOR.'/components/com_bookpro/tables/customer.php';
  	$db = JFactory::getDbo();
  	$table = new TableCustomer($db);
  	$table->load(array('email'=>trim($request->email)));
  	$result = false;
  	$params = json_decode($table->params);
	//var_dump($table);die;
  	if($table->id && $table->user_id && $params->active_code == $request->activation_code && !empty($request->activation_code))
  	{
  			  	
  			$table->state = 1;
  			$table->store();
  			$user = JFactory::getUser( $table->user_id);
  			//$user->bind($data);
  			$user->block = 0;
  			$user->save();
  			
  			//success
  			$response['status'] = 'ok';
  			$response['message'] = array(
  					'en'=>JText::_('EN_BOOKPRO_SMS_CONFIRM_MSG_ACTIVE_CODE'),
        			'fr'=>JText::_('FR_BOOKPRO_SMS_CONFIRM_MSG_ACTIVE_CODE'));
  			$response['data'] = new JObject();
  			$result = true;
  		
  	} 
  	else {
  		$response = self::generateError('15'); // active failed
  		$response['data'] = new JObject();
  	}
  	return $result;
  }
  
	private function getVehicleType($db){
		$query = $db->getQuery(true);
		$query->select('id as vehicle_type_id, name as vehicle_type_name, capacity, icon')
				->from('#__bookpro_vehicle_type')
				->where('state =1');
		$db->setQuery($query);
		$result = $db->loadObjectList();
		foreach($result as &$item){
			$item->vehicle_type_id=$item->vehicle_type_id;
			$item->vehicle_type_name=json_decode($item->vehicle_type_name);
			$item->capacity=json_decode($item->capacity);
			$item->icon=json_decode($item->icon);
			foreach ($item->icon as &$icon){
				$icon = JUri::root().$icon;
			}
			if(!$item->icon){
				$item->icon=new JObject();
			}
			$item=AndroidHelper::format($item);
		}
		return $result ;
	}
	
	private function getPackageNature($db){
		$query = $db->getQuery(true);
		$query->select('id,name')
				->from('#__bookpro_package');
		$db->setQuery($query);
		$result = $db->loadObjectList();
		foreach($result as &$item){
			$item->id=$item->id;
			$item->name=json_decode($item->name);
			$item=AndroidHelper::format($item);
		}
		return $result ;
	}
	private function getTransportType($db,$default){
		$query = $db->getQuery(true);
		$query->select('id,name')
				->from('#__bookpro_transport_type');
		$db->setQuery($query);
		$result = $db->loadObjectList();
		foreach($result as &$item){
			$item->name=json_decode($item->name);
			$item->default = (string)(int)($item->id == $default);
			$item=AndroidHelper::format($item);
		}
		return $result;
	}
	
	
	private function getInforVehicle($customer_id){
			$db = JFactory::getDbo();		
			$query = $db->getQuery(true);
			$query->select('a.id, a.name, a.vehicle_type_id,a.driver_id, type.name as vehicle_type_name, a.plate_number, a.capacity, a.desc')
			->leftJoin('#__bookpro_vehicle_type as type ON a.vehicle_type_id = type.id')
			->from('#__bookpro_vehicle a')->where('a.driver_id='.$db->quote($customer_id).' or '.'a.customer_id='.$db->quote($customer_id));
			$db->setQuery($query);
			return $db->loadObject();
	}
	private function getInforDriver($customer_id){
			$db = JFactory::getDbo();		
			$query = $db->getQuery(true);
			$query->select('a.id as user_id, a.name, a.mobile, b.plate_number')
			->leftJoin('#__bookpro_vehicle as b ON a.id = b.driver_id')
			->from('#__bookpro_customer as a')->where('a.id='.$db->quote($customer_id));
			$db->setQuery($query);
			return $db->loadObject();
	}
	
	public function actionLoadcommon(&$response, $status){
		
		$session_id = AndroidHelper::getSessionId();
		$session = $this->loadSession($session_id);
		if(!$session){
			$response = self::generateError('10');
			return false;
		}else{
			$config = JComponentHelper::getParams('com_bookpro');
			$transport_type_default = $config->get('transport_type_default');
			$response['status'] = 'ok';
			$response['message'] = array(
  					'en'=>JText::_('EN_BOOKPRO_LOAD_COMMON'),
        			'fr'=>JText::_('FR_BOOKPRO_LOAD_COMMON'));
			$db = JFactory::getDbo();
			$data = new JObject();
			$data->vehicle_types = $this->getVehicleType($db);
			$data->package_natures = $this->getPackageNature($db);
//			$data->transport_types = $this->getTransportType($db,$transport_type_default);
			$params = JComponentHelper::getParams('com_bookpro');
			$data->settings = new JObject();
			$data->settings->soonest_booking_time = (string)$params->get('soonest_booking_time',10080);
			$data->settings->latest_booking_time = (string)$params->get('latest_booking_time',30);
			$data->settings->update_location_period = (string)$params->get('update_location_period',5);
			$data->settings->get_order_period = (string)$params->get('get_order_period',5);
			$data->settings->delivery_validation_price = AndroidHelper::getPriceSetting($params);
			$query = $db->getQuery(true);
			$query->select('params')->from('#__bookpro_price')->where('code LIKE '.$db->quote('VALIDATE_END'));
			$db->setQuery($query);
			$data->settings->delivery_validation_price->value = (string)$db->loadResult();
			$data->payment_settings = AndroidHelper::getPaymentSetting();
			$response['data'] = $data;
			return true;
		}		
	}
	private function getCustomer($user_id){
		$db = JFactory::getDbo();		
		$query = $db->getQuery(true);
		$query->select('id, user_type, company_name, name, function, address, city, post_code, mobile, phone, email')
		->from('#__bookpro_customer')
		->where('id='.$user_id);
		$db->setQuery($query);
		return $db->loadObject();
	}
	public function actionEdit(&$response, $status){		
		$session_id = AndroidHelper::getSessionId();
		
		$session = $this->loadSession($session_id);

		if(!$session){
			$response = self::generateError('10');
			return false;
		}else{
			$user = new BookproUser($session->userid);
			$data = AndroidHelper::getBodyRequest();
			$user->data = (array)$data;
			$user->data['id'] = $session->userid;
			$data = null;unset($data);//clear memory
			$resultchange = $user->save();
			
			if($resultchange){				
				$response['status'] = 'ok';
				$response['message'] = array(
						'en'=>JText::_('EN_BOOKPRO_SMS_CONFIRM_MSG_CHANGE_PROFILE'),
						'fr'=>JText::_('FR_BOOKPRO_SMS_CONFIRM_MSG_CHANGE_PROFILE'));     
		        $customer = new JObject();
				$customer->user = $this->getCustomer($session->userid);
				$response['data'] = $customer;
				return true;
			}else{
				$response = self::generateError($user->error_code);
				return false;
			}
					
		}	
	}
	
	public function actionChangePassword(&$response, $status){
		$session_id = AndroidHelper::getSessionId();
		$session = $this->loadSession($session_id);
		if(!$session){
			$response = self::generateError('10');
			return false;
		}else{
			//$response['status'] = 'ok';
			$user = new BookproUser($session->userid);
			$data = AndroidHelper::getBodyRequest();
			$user->data = (array)$data;
			$data = null;unset($data);//clear memory
			$result = $user->changePassword();
			if($result){
				$response['status'] = 'ok';
				$response['message'] = array(
						'en'=>JText::_('EN_BOOKPRO_SMS_CONFIRM_MSG_CHANGE_PASSWORD'),
						'fr'=>JText::_('FR_BOOKPRO_SMS_CONFIRM_MSG_CHANGE_PASSWORD'));
				$response['data'] = new JObject();
				return true;
			}else{
				$response = self::generateError('31');
				return false;
			}
				
		}
	}
	private function getCustomerInfo($id){
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('id as user_id, name, company_name,function,phone,mobile,address,city, post_code,user_type')->from('#__bookpro_customer')->where('id='.(int)$id);
		$db->setQuery($query);
		return $db->loadObject();
	}
	public function actionGetMyData(&$response, $status){
		$session_id = AndroidHelper::getSessionId();
		$session = $this->loadSession($session_id);
		if(!$session){
			$response = self::generateError('10');
			return false;
		}else{
			$customer_id = $session->userid;
			//get list order id
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);
			$is_driver = $session->is_driver();
			$query->select('o.id as order_id, o.receiver_name, o.is_pick_arrived, o.created_time,o.trip_start_time, 
					o.from, o.to, o.customer_id, o.driver_id, o.recipient_info, o.distance, o.start_time, 
					o.end_time, o.trip_status, o.packages, o.vehicle_type_id, o.vehicle_id, o.total as price, 
					o.currency, o.recipient_validate, o.note as note, o.delivery_code, o.is_booked, 
					o.is_paid, o.is_accepted, o.is_cancelled, o.params')
				->from('#__bookpro_orders as o')
				->where('(o.customer_id = '.$customer_id.' OR o.driver_id = '.$customer_id.')')
				->where('(o.trip_status =2 OR o.is_cancelled = 1)')
				->order('o.created_time DESC');
			$db->setQuery($query);
			if($is_driver){
				$query->select('o.total+o.discount as price');
			}
			$order_array = $db->loadObjectList();
//			var_dump($order_array);die;
			$response['status'] = 'ok';
			$response['message'] = array(
					'en'=>JText::_('EN_BOOKPRO_SMS_CONFIRM_MSG_GETMYDATA_SUCCESS'),
					'fr'=>JText::_('FR_BOOKPRO_SMS_CONFIRM_MSG_GETMYDATA_SUCCESS'));
			$result = new JObject();
			$result->list= array();
			
			foreach ($order_array as $order) {
				$result->list[] = AndroidHelper::formatDataByOrder($order,true);
			}
		    $response['data']= $result;
		   
		    return true;
	
		}
	}
	
	private function getPromoCodeCoupon(){
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('id, description as value, code')
		->from('#__bookpro_coupon')
		->where('unpublish_date >='. $db->q(JFactory::getDate()->toSql()));
		$db->setQuery($query);
		$result = $db->loadObjectList();
		if($result){
			foreach($result as &$item){
				$item->value = json_decode($item->value);
			}
		}
		return $result;
	}
	
	public function actionGetPromoCode(&$response, $status){
		$session_id = AndroidHelper::getSessionId();
		$session = $this->loadSession($session_id);
		if(!$session){
			$response = self::generateError('10');
			return false;
		}else{
			$response['status'] = 'ok';
			$response['message'] = array(
					'en'=>JText::_('EN_BOOKPRO_GET_PROMOCODE_SUCCESS'),
					'fr'=>JText::_('FR_BOOKPRO_GET_PROMOCODE_SUCCESS'));			
			$data = new JObject();
			$data->promo_codes = $this->getPromoCodeCoupon();
			
			$response['data'] = $data;
			return true;	
	
		}
	}
	private function checkPromoCode($promocode){
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('*')
		->from('#__bookpro_coupon')
		->where('code ='.$db->quote($promocode).' and '.'unpublish_date >='. $db->q(JFactory::getDate()->toSql()).' and '.'publish_date <='. $db->q(JFactory::getDate()->toSql()).' and '.'remain > 0');
		$db->setQuery($query);
		$result = $db->loadObjectList();		
		return $result;
	}
	
	public function actionCheckPromoCode(&$response, $status){
		$session_id = AndroidHelper::getSessionId();
		$session = $this->loadSession($session_id);
		if(!$session){
			$response = self::generateError('10');
			return false;
		}else{
			$request = AndroidHelper::getBodyRequest();	
		  	$promocode=$request->promo_code;
		  	if($this->checkPromoCode($promocode)){
				$response['status'] = 'ok';
				$response['message'] = array(
						'en'=>JText::_('EN_BOOKPRO_PROMOCODE_VALID'),
						'fr'=>JText::_('FR_BOOKPRO_PROMOCODE_VALID'));			
				$data = new JObject();			
				$response['data'] = $data;
				return true;	
		  	}else {
		  		$response = self::generateError('35');
		  		return false;
		  	}
		}
	}
	
	//get closet driver 
	private function getDriverClosest($vehicle_type_id,$location,$distance){
		require_once JPATH_ADMINISTRATOR.'/components/com_bookpro/helpers/math.php';
		
		$data = MathHelper::getClosestDriver($vehicle_type_id,$location,$distance);
		$result = array();
		foreach ($data as $item){
			$data = json_decode($item->data);
			$object = new JObject();
			$object->driver = AndroidHelper::format((object)array('user_id'=>$item->id,'company_name'=>$item->company_name,'name'=>$item->name,'mobile'=>$item->mobile));
			$object->location = AndroidHelper::format((object)array('latitude'=>$item->lat,'longitude'=>$item->lng,'distance'=>$item->distance));
			$object->vehicle = AndroidHelper::format((object)array('vehicle_type_id'=>$data->vehicle->current_type,'vehicle_type_name'=>$data->vehicle->current_type_name,'plate_number'=>$data->vehicle->plate_number));
			$result[] = $object;
		}
		return $result;
	}
	
	public function actionGetclosest(&$response, $status,$distance = 50){
		$session_id = AndroidHelper::getSessionId();
		$request = AndroidHelper::getBodyRequest();
		$session = $this->loadSession($session_id);
		if(!$session){
			$response = self::generateError('10');
			return false;
		}else{
			$response['status'] = 'ok';
			$response['message'] = array(
					'en'=>JText::_('EN_BOOKPRO_GET_DRIVER_CLOSEST_SUCCESS'),
					'fr'=>JText::_('FR_BOOKPRO_GET_DRIVER_CLOSEST_SUCCESS'));
			$data = new JObject();
			$data->drivers = $this->getDriverClosest((int)$request->vehicle_type_id,$request->location,$distance);			
			$response['data'] = $data;
			return true;	
	
		}
	}
	
	public function actionGetAlldrivers(&$response, $status){
		//$t = microtime(true);
		AndroidHelper::write_log('getalldriver.txt', 'Start');
		$session_id = AndroidHelper::getSessionId();
		$request = AndroidHelper::getBodyRequest();
		$session = $this->loadSession($session_id);
		if(!$session){
			AndroidHelper::write_log('getalldriver.txt', 'Session expired');
			$response = self::generateError('10');
			return false;
		}else{
			//AndroidHelper::write_log('getalldriver.txt', 'session: '.$session->session_id);
			$session->time = JFactory::getDate()->toUnix();
			$session->saveSession();
			$response['status'] = 'ok';
			$response['message'] = array(
					'en'=>JText::_('EN_BOOKPRO_GET_DRIVER_CLOSEST_SUCCESS'),
					'fr'=>JText::_('FR_BOOKPRO_GET_DRIVER_CLOSEST_SUCCESS'));
			$data = new JObject();
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);
			$current_time = JFactory::getDate()->toUnix() - JComponentHelper::getParams('com_bookpro')->get('timeout_online',5);
			$distance_sql="( 6378 * acos( cos( radians('%s') ) * cos( radians( s.lat ) ) * cos( radians( s.lng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( s.lat ) ) ) ) AS distance";
			$query->select('s.lat, s.lng, s.data,'.sprintf($distance_sql,$session->lat,$session->lng,$session->lat).' , 
						d.company_name,d.name,d.mobile,d.id');
			$query->from('#__bookpro_session as s')
				->innerJoin('#__bookpro_customer as d ON d.id = s.userid')
				->where('s.free = 1')			
				->where('s.time > '.$current_time);
			$db->setQuery($query);	
			$driver_list = $db->loadObjectList();
			$result = array();
			foreach ($driver_list as $item){
				$d = json_decode($item->data);
				$object = new JObject();
				$object->driver = AndroidHelper::format((object)array('user_id'=>$item->id,'company_name'=>$item->company_name,'name'=>$item->name,'mobile'=>$item->mobile));
				$object->location = AndroidHelper::format((object)array('latitude'=>$item->lat,'longitude'=>$item->lng,'distance'=>$item->distance));
				$object->vehicle = AndroidHelper::format((object)array('vehicle_type_id'=>$d->vehicle->current_type,'vehicle_type_name'=>$d->vehicle->current_type_name,'plate_number'=>$d->vehicle->plate_number));
				$result[] = $object;
			}
			$data->drivers = $result;		
			$response['data'] = $data;
			//AndroidHelper::write_log('getalldriver.txt', 'Finished - time: '.(microtime(1) - $t));
			return true;	
	
		}
	}
	
	public function actionGetcurrent(&$response, $status){
		$session_id = AndroidHelper::getSessionId();
		$request = AndroidHelper::getBodyRequest();
		$session = $this->loadSession($session_id);
		if(!$session){
			$response = self::generateError('10');
			return false;
		}else{			
			$session = new BookproSession();
			$session->userid = $request->driver_id;
			$response['status'] = 'ok';	
			$response['message'] = array(
				'en'=>JText::_('EN_BOOKPRO_GET_DRIVER_CURRENT_SUCCESS'),
				'fr'=>JText::_('FR_BOOKPRO_GET_DRIVER_CURRENT_SUCCESS'));			
			$data = new JObject();
			if(!$session->loadSessionByUserId()){
				$data->location = new JObject();
			}else{
				$data->location = AndroidHelper::format((object)array('latitude'=>$session->lat,'longitude'=>$session->lng));
			}			
			$response['data'] = $data;
			return true;	
			
		}
	}
	
	public function actionCurrentDeliveryOrder(&$response, $status){
		$session_id = AndroidHelper::getSessionId();
		$session = $this->loadSession($session_id);		
		$request = AndroidHelper::getBodyRequest();	
		if(!$session){
			$response = plgJBackendUser::generateError('10');
			$response['status'] = 'ko';
			return false;
		}else{
			$customer_id = $session->userid;
			
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->select('id')
				->from('#__bookpro_orders')					
				->where('trip_status !=2 AND is_accepted = 1 AND is_cancelled = 0 AND is_paid=1');
			if($session->data->vehicle){
				//is driver
				$query->where('driver_id = '.$customer_id);
				$query->where('(is_booked = 0 OR (is_booked=1 AND is_pick_arrived = 1))');
			}else{
				$query->where('customer_id = '.$customer_id);
			}
// 			echo $query->dump();
			$db->setQuery($query);
			$order_array = $db->loadColumn();
			
			$response = self::generateSuccess();
			$result = new JObject();
			$result->list= array();
			foreach ($order_array as $id) {
				$result->list[] = AndroidHelper::getOrderDetail($id);
			}
			$response['status'] = 'ok';	
			$response['message'] = array(
					'en'=>JText::_('EN_BOOKPRO_GET_CURRENT_DELIVERY_ORDER_SUCCESS'),
					'fr'=>JText::_('FR_BOOKPRO_GET_CURRENT_DELIVERY_ORDER_SUCCESS'));
			$response['data'] = $result;
			return true;
			
		}
	}
	
	/**
	 * Get pending booking that is booking type and is accped but not is arrive pick
	 * @param unknown $response
	 * @param unknown $status
	 */
	public function actionCurrentPendingOrder(&$response, $status){
		$session_id = AndroidHelper::getSessionId();
		$session = $this->loadSession($session_id);
		$request = AndroidHelper::getBodyRequest();
		if(!$session){
			$response = plgJBackendUser::generateError('10');
			$response['status'] = 'ko';
			return false;
		}else{
			$customer_id = $session->userid;
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->select('id')
			->from('#__bookpro_orders')
			->where('(driver_id = '.$customer_id.')')
			->where('is_paid = 1 AND is_accepted = 1 AND is_cancelled = 0 AND is_pick_arrived = 0 AND is_booked=1');
			$db->setQuery($query);
			$order_array = $db->loadColumn();
				
			$response = self::generateSuccess();
			$result = new JObject();
			$result->list= array();
			foreach ($order_array as $id) {
				$result->list[] = AndroidHelper::getOrderDetail($id);
			}
			$response['status'] = 'ok';
			$response['message'] = array(
					'en'=>JText::_('EN_BOOKPRO_GET_CURRENT_PENDING_ORDER_SUCCESS'),
					'fr'=>JText::_('FR_BOOKPRO_GET_CURRENT_PENDING_ORDER_SUCCESS'));
			$response['data'] = $result;
			return true;
				
		}
	}
	
	private function getPaymentConfig($id){
		$customer = new BookproUser($id);
		$data = $customer->getData();
		$params = json_decode($data['params']);
		$customer= null;unset($customer);//clear memory
		$data = new JObject();
		if(!empty($params->payment_config)){
			$data->payment_config = $params->payment_config;
		}else{
			$data->payment_config = array();
		}
		return $data;
	}
	
	public function actionPaymentConfigLoad(&$response, $status){
		$session_id = AndroidHelper::getSessionId();
		$session = $this->loadSession($session_id);		
		if(!$session){
			$response = plgJBackendUser::generateError('10');
			$response['status'] = 'ko';
			return false;
		}else{
			$data = $this->getPaymentConfig($session->userid);
			
			$response['status'] = 'ok';	
			$response['message'] = array(
					'en'=>JText::_('EN_BOOKPRO_LOAD_PAYMENT_CONFIG_SUCCESS'),
					'fr'=>JText::_('FR_BOOKPRO_LOAD_PAYMENT_CONFIG_SUCCESS'));
			$response['data'] = AndroidHelper::format($data);
			return true;
		}
	}
	
	public function actionPaymentConfigSet(&$response, $status){
		$session_id = AndroidHelper::getSessionId();
		$session = $this->loadSession($session_id);		
		if(!$session){
			$response = plgJBackendUser::generateError('10');
			return false;
		}else{
			$request = AndroidHelper::getBodyRequest();	
			require_once JPATH_ADMINISTRATOR.'/components/com_bookpro/tables/customer.php';
		  	$db = JFactory::getDbo();
		  	$table = new TableCustomer($db);
		  	$table->load($session->userid);
		  	if(!$table->id){
		  		$response = plgJBackendUser::generateError('10');		  	
				return false;
		  	}
		  	$params = json_decode($table->params);
		  	$params->payment_config = $request->payment_config;
		  	//check if braintree generate card token
		  	foreach ($params->payment_config as $i=>&$payment){
				if($payment->deleted){
					if($payment->payment_gateway=='braintree'){						
						if($payment->data->customer_id){
							$braintree = self::getGateway('braintree');
							if(!$braintree){
								$response = self::generateError(0);
								return false;
							}
							if(!$braintree->delete($payment->data->customer_id,$payment->data->access_token)){
								$response = self::generateError(49);
								return false;
							}
						}
					}
					unset($params->payment_config[$i]);
				}else{
					if($payment->payment_gateway=='braintree'){
						$braintree = self::getGateway('braintree');
						if(!$braintree){
							$response = self::generateError(0);
							return false;
						}
						
						if (empty($payment->data->token) || empty($payment->data->customer_id)){
							$db->setQuery("select * from #__bookpro_customer where id={$session->userid}");
							$customer = $db->loadAssoc();
							$customer['payment_method_nonce'] = $payment->data->payment_method_nonce;		  				
							//print_r($customer);die;
							$data = $braintree->generatePaymentToken($customer);
							if($data){
								$payment->data->token = $data['token'];
								$payment->data->customer_id = $data['customer_id'];
							}else{
								$response = self::generateError(48);
								return false;
							}
							
						}
					}
				}
		  		
		  	}
			
			$table->params = json_encode($params);
			$table->store();
			
			$data = $this->getPaymentConfig($session->userid);
			$response['status'] = 'ok';	
			$response['message'] = array(
					'en'=>JText::_('EN_BOOKPRO_SET_PAYMENT_CONFIG_SUCCESS'),
					'fr'=>JText::_('FR_BOOKPRO_SET_PAYMENT_CONFIG_SUCCESS'));
			$response['data'] = AndroidHelper::format($data);
			return true;
		}
	}
	
	//get gateway
	private static function getGateway($name){
		if(empty(self::$gateway[$name])){			
			$filename = JPATH_ROOT.'/plugins/bookpro/payment_'.$name.'/payment_'.$name.'.php';
			
			if(is_file($filename)){
				require_once $filename;
			}else{
				
				return false;
			}
			$class= "plgBookproPayment_$name";
			$subject = new JEventDispatcher();
			self::$gateway[$name] = new $class($subject,array());
		}
		return self::$gateway[$name];
		
	}
	//----------------------------get trip location -----------------------------///
	public function actionGetTripLocation(&$response, $status){
		$session_id = AndroidHelper::getSessionId();
		$session = $this->loadSession($session_id);		
		if(!$session){
			$response = plgJBackendUser::generateError('10');
			return false;
		}else{
			$request = AndroidHelper::getBodyRequest();	
		  	$db = JFactory::getDbo();		
			$query = $db->getQuery(true);
			$query->select('trip_location')
			->from('#__bookpro_orders')->where('id='.(int)$request->order_id);
			$db->setQuery($query);
			$trip = json_decode($db->loadResult());
		  	if(!$trip){
		  		$trip = array();
		  	}
			$response['status'] = 'ok';	
			$response['message'] = array(
					'en'=>JText::_('EN_BOOKPRO_LOAD_TRIP_SUCCESS'),
					'fr'=>JText::_('FR_BOOKPRO_LOAD_TRIP_SUCCESS'));
			$response['data'] = (object)array('locations'=>($trip));
			return true;
		}
	}
	
	public function actionCheckExpired(&$response, $status){		
		$session_id = AndroidHelper::getSessionId();
		$session = $this->loadSession($session_id);		
		if(!$session){
			$response = plgJBackendUser::generateError('10');
			return false;
		}else{
			$response['status'] = 'ok';	
			$response['message'] = array(
						'en'=>JText::_('SUCCESS'),
						'fr'=>JText::_('SUCCESS'));
			$response['data'] = new JObject();				
			return true;
		}
	}
	
	
	public function actionReport(&$response, $status){
		$session_id = AndroidHelper::getSessionId();
		$session = $this->loadSession($session_id);		
		if(!$session){
			$response = plgJBackendUser::generateError('10');
			return false;
		}else{
			$request = AndroidHelper::getBodyRequest();	
		  	if($request->problem!=""){
				require_once JPATH_ADMINISTRATOR.'/components/com_bookpro/tables/report.php';				
			  	$db = JFactory::getDbo();
			  	$table = new TableReport($db);
			  	$table->save(array('desc'=>$request->problem,'customer_id'=>$session->userid));
			  	$response['status'] = 'ok';	
				$response['message'] = array(
							'en'=>JText::_('EN_BOOKPRO_CREATE_REPORT_SUCCESS'),
							'fr'=>JText::_('FR_BOOKPRO_CREATE_REPORT_SUCCESS'));
				$response['data'] = new JObject();				
				return true;
		  	}
		  	else
		  	{
		  		$response = self::generateError('33');
		  		return false;
		  	}
		}
	}
	
	//--------------------------actionRegisterPush-------------------------------------
	public function actionRegisterPush(&$response, $status){
		$request = AndroidHelper::getBodyRequest();		
		
		if(empty($request->device_token) || empty($request->user_id) || empty($request->os)){
			$response = plgJBackendUser::generateError('16');
			return false;
		}
		
		$user = $this->getCustomerInfo($request->user_id);
		if(!isset($user->user_id)){
			$response = plgJBackendUser::generateError('10');
			return false;
		}else{
			require_once JPATH_ADMINISTRATOR.'/components/com_bookpro/tables/customerpn.php';
			//check api_key
			require_once JPATH_ADMINISTRATOR.'/components/com_bookpro/classes/pn/common/config.php';
			if(strtolower($request->os) == 'android'){
				$key_os = API_KEY_ANDROID;
			}else{
				$key_os = API_KEY_IOS;
			}
			/*
			if($request->api_key != $key_os){
				$response = self::generateError('16');
				return false;
			}
			*/
			
			$db = JFactory::getDbo();
			
			//delete expired token
			//$db->setQuery('delete * from #__bookpro_customer_pn where modify < '.JFactory::getDate('-30 days')->toUnix());
			//$db->execute();
			
			$table = new TableCustomerPn($db);
			$data = (array)$request;
			$table->load(array('os'=>$data['os'],'device_token'=>$data['device_token']));
		  	if($table->save($data)){
			  	$response['status'] = 'ok';	
				$response['message'] = array(
							'en'=>JText::_('success'),
							'fr'=>JText::_('success'));
				$response['data'] = new JObject();		
				return true;
		  	}
		  	else
		  	{
		  		$response = self::generateError('100');
		  		return false;
		  	}
		}
	}
	
	//--------------------------actionRegisterPush-------------------------------------
	public function actionUnRegisterPush(&$response, $status){
		$request = AndroidHelper::getBodyRequest();		
		require_once JPATH_ADMINISTRATOR.'/components/com_bookpro/tables/customerpn.php';	
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->delete('#__bookpro_customer_pn')->where('user_id = '.$request->user_id.' AND os LIKE '.$db->quote($request->os).' AND device_token LIKE '.$db->quote($request->device_token));
		$db->setQuery($query);
		if($db->execute()){
			$response['status'] = 'ok';	
			$response['message'] = array(
						'en'=>JText::_('success'),
						'fr'=>JText::_('success'));
			$response['data'] = new JObject();				
			return true;
		}
		else
		{
			$response = self::generateError('100');
			return false;
		}
	}
	
	
	private function loadSession($session_id){
		$session = new BookproSession(array('session_id'=>$session_id));
		if($session->loadSessionById()){
			return $session;
		}
		return false;
	}

  public function onRequestUser($module, &$response, &$status = null)
  {
  
    if ($module !== 'user') return true;

    // Add to module call stack
    jBackendHelper::moduleStack($status, 'user');

    $app = JFactory::getApplication();
    $action = $app->input->getString('action');
	$resource = $app->input->getString('resource');
	
	//AndroidHelper::write_log('request.txt','function '.$resource.PHP_EOL.' request'.json_encode(AndroidHelper::getBodyRequest()).PHP_EOL);
    
	try{
		 switch ($resource)
	    {
	      case 'login':
		        if ($action == 'post')
		        {
		          return $this->actionLogin($response, $status);
		        }
		        break;
	       case 'updatelocation':
		        if ($action == 'post')
		        {
		          return $this->actionUpdatelocation($response, $status);
		        }
		        break;
	        case 'getlocation':
		        if ($action == 'post')
		        {
		          return $this->actionGetlocation($response, $status);
		        }
		        break;
	      case 'logout':
		        return $this->actionLogout($response, $status);
	      case 'register':
		        if ($action == 'post')
		        {
		          return $this->actionRegister($response, $status);
		        }
		        break;
	      case 'loadcommon':
		        if ($action == 'get')
		        {
		          return $this->actionLoadcommon($response, $status);
		        }
		        break;
	      case 'activate':
		          return $this->actionActive($response, $status);
		        break;
		   case 'edit':
		          return $this->actionEdit($response, $status);
		        break;
		   case 'getmydata':
		   		  return  $this->actionGetMyData($response, $status);
		   		break;
		   case 'changepassword':
		   		  return $this->actionChangePassword($response, $status);
		   		break;
		   case 'get_promo_code':
		   		  return $this->actionGetPromoCode($response, $status);
		   		break;	
		   case 'getclosest':
		   		  return $this->actionGetclosest($response, $status);
		   		break;
		   case 'getalldrivers':
				return $this->actionGetAlldrivers($response, $status);
				break;
		   case 'currentlocation':
		   		  return $this->actionGetcurrent($response, $status);
		   		break;
		   case 'currentdeliveryorder':
		   		return $this->actionCurrentDeliveryOrder($response, $status);
		   		break;
		   case 'pendingbookings':
		   		return $this->actionCurrentPendingOrder($response, $status);
		   		break;
		   case 'paymentconfigload':
		   		return $this->actionPaymentConfigLoad($response, $status);
		   		break;
		    case 'paymentconfigset':
		   		return $this->actionPaymentConfigSet($response, $status);
		   		break;
		   	case 'gettriplocation':
		   		return $this->actionGetTripLocation($response, $status);
		   		break;
		   	case 'report':
		   		return $this->actionReport($response, $status);
		   		break;
		   	case 'check_promo_code':
		   		return $this->actionCheckPromoCode($response, $status);
		   		break;
			case 'register_push':
		   		return $this->actionRegisterPush($response, $status);
		   		break;
			case 'unregister_push':
		   		return $this->actionUnRegisterPush($response, $status);
		   		break;
			case 'check_expired':
		   		return $this->actionCheckExpired($response, $status);
		   		break;
		   default:
		   		$response = self::generateError('0'); // Action not specified
      			return false;
		   		break;
	    }
			
	}catch (Exception $e){
		 $response = self::generateError('Error'); 
		 $response['message'] = array('en'=>'System error','fr'=>'System error');
		 AndroidHelper::write_log('plg_exeption.txt','function '.$resource.PHP_EOL.' request'.json_encode(AndroidHelper::getBodyRequest()).PHP_EOL.'Msg: '.$e->getMessage());
		 $response['status'] = 'ko';
		 $response['msg_error'] = $e->getMessage();
     	 return false;
	}
    return true;
  }
  

}
