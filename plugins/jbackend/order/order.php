<?php
// use Joomla\String\String;

/**
 * jBackend content plugin for Joomla
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
require_once JPATH_ADMINISTRATOR.'/components/com_bookpro/classes/user.php';
require_once JPATH_ADMINISTRATOR.'/components/com_bookpro/classes/session.php';
require_once JPATH_ADMINISTRATOR.'/components/com_bookpro/classes/order.php';
require_once JPATH_ADMINISTRATOR.'/components/com_bookpro/helpers/android.php';
require_once JPATH_ADMINISTRATOR.'/components/com_bookpro/helpers/currency.php';

class plgJBackendOrder extends JPlugin
{
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
  
  public static function generateError($errorCode,$key = null)
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
						'en'=>JText::sprintf('EN_BOOKPRO_FIELD_NOT_VALID',$key),
						'fr'=>JText::sprintf('FR_BOOKPRO_FIELD_NOT_VALID',$key));   
			break;
        case '21':
        	$error['code'] = '21';
        	$error['message'] = array(
        			'en'=>JText::_('EN_BOOKPRO_PROMO_CODE_INVALID'),
        			'fr'=>JText::_('FR_BOOKPRO_PROMO_CODE_INVALID'));
        	break;	
        case '22':
        	$error['code'] = '22';
        	$error['message'] = array(
        			'en'=>JText::_('EN_BOOKPRO_DELIVERY_CODE_INVALID'),
        			'fr'=>JText::_('FR_BOOKPRO_DELIVERY_CODE_INVALID'));
        	break;		
        case '32':
        	$error['code'] = '32';
        	$error['message'] = array(
        			'en'=>JText::_('EN_BOOKPRO_ERROR_ORDER_IS_ACCEPT_ALREADY'),
        			'fr'=>JText::_('FR_BOOKPRO_ERROR_ORDER_IS_ACCEPT_ALREADY'));
        	break;
        case '33':
        	$error['code'] = '33';
        	$error['message'] = array(
        			'en'=>JText::_('EN_BOOKPRO_ERROR_DRIVER_NOT_FREE_OR_NOT_IS_DRIVER'),
        			'fr'=>JText::_('FR_BOOKPRO_ERROR_DRIVER_NOT_FREE_OR_NOT_IS_DRIVER'));
        	break;
         case '34':
        	$error['code'] = '34';
        	$error['message'] = array(
        			'en'=>JText::_('EN_BOOKPRO_ERROR_EMPTY_CURRENT_VEHICLE'),
        			'fr'=>JText::_('FR_BOOKPRO_ERROR_EMPTY_CURRENT_VEHICLE'));
        	break;	
        case '38':
        	$error['code'] = '38';
        	$error['message'] = array(
        			'en'=>JText::_('EN_BOOKPRO_ERROR_AMOUNT_NOT_CORRECT'),
        			'fr'=>JText::_('FR_BOOKPRO_ERROR_AMOUNT_NOT_CORRECT'));
        	break;
        case '39':
        	$error['code'] = '39';
        	$error['message'] = array(
        			'en'=>JText::_('EN_BOOKPRO_ERROR_PAYMENT_FALIED'),
        			'fr'=>JText::_('FR_BOOKPRO_ERROR_PAYMENT_FALIED'));
        	break;
        case '40':
        	$error['code'] = '40';
        	$error['message'] = array(
        			'en'=>JText::_('EN_BOOKPRO_ERROR_ORDER_NOT_EXIST'),
        			'fr'=>JText::_('FR_BOOKPRO_ERROR_ORDER_NOT_EXIST'));
        	break;
        case '42':
        	$error['code'] = '42';
        	$error['message'] = array(
        			'en'=>JText::_('EN_BOOKPRO_ERROR_DRIVER_INCORRECT'),
        			'fr'=>JText::_('FR_BOOKPRO_ERROR_DRIVER_INCORRECT'));
        	break;
		 case '43':
        	$error['code'] = '43';
        	$error['message'] = array(
        			'en'=>JText::_('EN_BOOKPRO_ORDER_IS_CANCELLED'),
        			'fr'=>JText::_('FR_BOOKPRO_ORDER_IS_CANCELLED'));
        	break;
        case '44':
        	$error['code'] = '44';
        	$error['message'] = array(
        			'en'=>JText::_('EN_BOOKPRO_ORDER_IS_MISS'),
        			'fr'=>JText::_('FR_BOOKPRO_ORDER_IS_MISS'));
        	break;
        case '45':
        	$error['code'] = '45';
        	$error['message'] = array(
        			'en'=>JText::_('EN_BOOKPRO_ORDER_IS_PAID_CANNOT_CANCEL'),
        			'fr'=>JText::_('FR_BOOKPRO_ORDER_IS_PAID_CANNOT_CANCEL'));
        	break;
        case '46':
        	$error['code'] = '46';
        	$error['message'] = array(
        		'en'=>JText::_('EN_BOOKPRO_ORDER_IS_PAID_ALREADY'),
        		'fr'=>JText::_('FR_BOOKPRO_ORDER_IS_PAID_ALREADY'));
        	break;
        case '48':
        	$error['code'] = '48';
        	$error['message'] = array(
        			'en'=>JText::_('EN_BOOKPRO_ERROR_PAYMENT_FALIED_INVAILD_CARD'),
        			'fr'=>JText::_('FR_BOOKPRO_ERROR_PAYMENT_FALIED_INVAILD_CARD'));
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
         case '100':
        	$error['code'] = '100';
        	$error['message'] = array(
        			'en'=>JText::_('EN_BOOKPRO_ERROR_NOT_PERMISSION'),
        			'fr'=>JText::_('FR_BOOKPRO_ERROR_NOT_PERMISSION'));
        	break;
         case '2000':
         case '2001':
         case '2002':
         case '2003':
         case '2004':
         case '2005':
         case '2006':
         case '2007':
         case '2008':
         case '2009':
         case '2010':
         	$error['code'] = (string)$errorCode;
         	$error['message'] = array(
         			'en'=>JText::_('EN_BOOKPRO_BRAINTREE_ERROR_'.$errorCode),
         			'fr'=>JText::_('FR_BOOKPRO_BRAINTREE_ERROR_'.$errorCode));
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
	//check user is driver
	private function checkDriver($user_id){
		if(empty($user_id)){
			return false;
		}
		$db = JFactory::getDbo();		
		$query = $db->getQuery(true);
		$query->select('user_type')
		->from('#__bookpro_customer')
		->where('id='.$user_id);
		$db->setQuery($query);
		if($db->loadResult() == 3){
			return true;
		}
		return false;
	}
	
	
	private function getCustomerInfo($id){
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('id as user_id, name, company_name,function,phone,mobile,address,city, post_code,user_type')->from('#__bookpro_customer')->where('id='.(int)$id);
		$db->setQuery($query);
		return $db->loadObject();
	}
	
	public function actionCreateOrder(&$response, $status){		
		$session_id = AndroidHelper::getSessionId();
		$session = $this->loadSession($session_id);		  	
		if(!$session){
			$response = self::generateError('10');
			return false;
		}else{			
			$customer_id = $session->userid;
			$request = AndroidHelper::getBodyRequest();
			if(!$request){
				$response = self::generateError('100');
				return false;
			}
			$response['data'] = new JObject();
			$data = array();
			$data['from']			= json_encode($request->from->location);
			$data['to']				= json_encode($request->to->location);
			$data['customer_id'] 	= $request->from->sender->user_id;
			$data['recipient_info']	= json_encode($request->to->recipient);
			$data['distance']		= floatval($request->distance/1000);
			$data['is_booked']		= $request->is_booked;
			$data['start_time']		= $request->start_time;
			//$data['vehicle_id']		= $request->vehicle_id;
			$data['vehicle_type_id']= $request->vehicle_type_id;
//			$data['transport_type_id']= $request->transport_type_id;
			$data['delivery_code']	= $request->delivery_code;
			$data['promo_code']		= $request->promo_code;
			$data['packages'] 		= json_encode($request->packages);
			foreach ($request->to->location as $k=>$d){
				if($d == null || $d == ''){
					$response = self::generateError('11',$k);
					return false;
				}
			}
			foreach ($request->from->location as $k=>$d){
				if($d == null || $d == ''){
					$response = self::generateError('11',$k);
					return false;
				}
			}
			foreach ($data as $k=>$d){
				if($d == null || $d == ''){
					if($k != 'start_time' && $k != 'promo_code' && $k != 'delivery_code'){						
						$response = self::generateError('11',$k);
						return false;
					}
					
				}
			}
			
			$order = new BookproOrder();
			$save = $order->save($data);
			if(!$save){
				$response = self::generateError('21');
				return false;
			}
			$response['status'] = 'ok';
    		$response['message'] = array(
        					'en'=>JText::_('EN_BOOKPRO_CREATE_ORDER_SUCCESS'),
        					'fr'=>JText::_('FR_BOOKPRO_CREATE_ORDER_SUCCESS'));
			$response['data'] = (object)array('order_id'=>(string)$order->id);
			return true;
		}
	}
	public function actionGetPrice(&$response, $status){		
		$session_id = AndroidHelper::getSessionId();
		$session = $this->loadSession($session_id);		  	
		if(!$session){
			$response = self::generateError('10');
			return false;
		}else{			
			$customer_id = $session->userid;
			$request = AndroidHelper::getBodyRequest();
			
			$response['data'] = new JObject();
			$data = array();
			$data['from']			= json_encode($request->from->location);
			$data['to']				= json_encode($request->to->location);
			$data['distance']		= floatval($request->distance/1000);
			$data['is_booked']		= $request->is_booked;
			$data['vehicle_id']		= $request->vehicle_id;
			$data['vehicle_type_id']= $request->vehicle_type_id;
//			$data['transport_type_id']= $request->transport_type_id;
			$data['promo_code']		= $request->promo_code;
			$data['delivery_code']	= $request->delivery_code;
			$data['start_time']		= $request->start_time;
			$order = new BookproOrder();
			$order->data = $data;
			$order->setPrice();
			
			if(!empty($data['promo_code'])){
				$check = $order->processCoupon();
				if(!$check){
					$response = self::generateError('21');
					return false;
				}
			}		
			if($order->data['total'] < 0){
				$order->data['total'] = 0;
			}			
			$response['status'] = 'ok';
    		$response['message'] = array(
        					'en'=>JText::_('EN_BOOKPRO_GET_PRICE_SUCCESS'),
        					'fr'=>JText::_('FR_BOOKPRO_GET_PRICE_SUCCESS'));
    		$params = JComponentHelper::getParams('com_bookpro');
			$currency = (object)array(	'name'=>$params->get('main_currency_text','euros'),
												'code'=>$params->get('main_currency','EUR'));
			$price = $order->data['params']->price;
			$price->delivery_validation = CurrencyHelper::formatNumber($price->delivery_validation);
			$price->total = CurrencyHelper::formatNumber($order->data['total']);
			$price->main = CurrencyHelper::formatNumber($price->main);
			
			$response['data'] = (object)array(
				'price'=>AndroidHelper::format($price),
				'currency'=>$currency);
			return true;
		}
	}
	
	//check permission of user to show order
	private function checkOrderPermission($session,$order){
		//order not existed
		if(!$order->order_id){
			return true;
		}
		//driver
		if(isset($session->data->vehicle)){
			//if the driver accept the order
			if($order->driver_id == $session->userid){
				return true;
			}
			if($order->driver_id && $order->driver_id != $session->userid){
				return false;
			}
			$params = json_decode($order->params);
			if(in_array($session->userid, $params->candidate->cancel_list)){
				return false;
			}
		}else{
			//if this order is created by the customer in session
			
			if($order->customer_id == $session->userid){
				return true;
			}else{
				return false;
			}
		}
		
		
		return true;
	}
	
	//get order detail
	public function actionGetDetail(&$response, $status){
			$session_id = AndroidHelper::getSessionId();
			$session = $this->loadSession($session_id);		
			$request = AndroidHelper::getBodyRequest();	
			if(!$session || !$request->order_id){
				$response = self::generateError('10');
				$response['status'] = 'ko';
				return false;
			}else{	
				//check permission of driver to get detail
				$response = self::generateSuccess();
				$order = AndroidHelper::getOrderData($request->order_id,$session->is_driver());
				
				if(!$this->checkOrderPermission($session, $order)){
					$response = self::generateError('100');				
					$response['data'] = new JObject();
					return false;
				}
				
				$result = AndroidHelper::formatDataByOrder($order,true);
				
				$response['status'] = 'ok';
    			$response['message'] = array(
        					'en'=>JText::_('EN_BOOKPRO_GET_DETAIL_SUCCESS'),
        					'fr'=>JText::_('FR_BOOKPRO_GET_DETAIL_SUCCESS'));
				$response['data'] = $result;
				return true;				
			}
	}
	
		
	public function actionAccept(&$response, $status){
		$session_id = AndroidHelper::getSessionId();
		$session = $this->loadSession($session_id);
		
		if(!$session){
			$response = plgJBackendUser::generateError('10');
			$response['status'] = 'ko';
			return false;
		}else{
			$request = AndroidHelper::getBodyRequest();
			$driver_id = $session->userid;
			$order = new BookproOrder();
			$order->id = $request->order_id;
			$order->driver_id = $driver_id;
			$check = $order->accept($session);
			if($check){
				$response['status'] = 'ok';
    			$response['message'] = array(
        				'en'=>JText::_('EN_BOOKPRO_ACCEPT_SUCCESS'),
        				'fr'=>JText::_('FR_BOOKPRO_ACCEPT_SUCCESS'));
				$response['data'] = new JObject();
				return true;
			}
			else{
				$response = self::generateError($order->error_code);
			}
			
		}		
		return false;		
	}
	
	public function actionCancel(&$response, $status){
		$session_id = AndroidHelper::getSessionId();
		$session = $this->loadSession($session_id);						
		if(!$session){
			$response = plgJBackendUser::generateError('10');
			$response['status'] = 'ko';
			return false;
		}else{
			$request = AndroidHelper::getBodyRequest();				
			$order = new BookproOrder();
			$order->id = $request->order_id;
			$customer_id = $session->userid;
			if($this->checkDriver($customer_id)){
				$order->driver_id = $customer_id;						
			}
			$order->comment = $request->comment;
			$check = $order->cancel($session);
			if($check){
				$response['status'] = 'ok';
    			$response['message'] = array(
        					'en'=>JText::_('EN_BOOKPRO_CANCEL_SUCCESS'),
        					'fr'=>JText::_('FR_BOOKPRO_CANCEL_SUCCESS'));
				$response['data'] = new JObject();
				
				return true;
			}else{
				$response = self::generateError($order->error_code);
				return false;
			}
		}
		$response = self::generateError('100');
		return false;
	}
	//--------------------------ignore---------------------------
	public function actionIgnore(&$response, $status){
		$session_id = AndroidHelper::getSessionId();
		$session = $this->loadSession($session_id);
		$response = self::generateError('100');			
		if(!$session){
			$response = plgJBackendUser::generateError('10');
			$response['status'] = 'ko';
			return false;
		}else{
			$request = AndroidHelper::getBodyRequest();				
			$order = new BookproOrder();
			$order->id = $request->order_id;
			$customer_id = $session->userid;
			$order->id = $request->order_id;
			$check = $order->ignore($session);
			if($check){
				$response['status'] = 'ok';
    			$response['message'] = array(
        					'en'=>JText::_('EN_BOOKPRO_IGNORE_SUCCESS'),
        					'fr'=>JText::_('FR_BOOKPRO_IGNORE_SUCCESS'));
				$response['data'] = new JObject();
				return true;
			}else{
				$response = self::generateError($order->error_code);
			}
			
		}	
		//$response = self::generateError('100');
		return false;	
	}
	//------------------------Validate end----------------------
	public function actionValidateEnd(&$response, $status){
		$session_id = AndroidHelper::getSessionId();
		$session = $this->loadSession($session_id);		
		$error_code = "100";			
		if(!$session){
			$response = plgJBackendUser::generateError('10');
			$response['status'] = 'ko';
			return false;
		}else{
			$request = AndroidHelper::getBodyRequest();				
			$order = new BookproOrder();
			$order->id = $request->order_id;
			$customer_id = $session->userid;
			$order->driver_id = $customer_id;
			$check = $order->validateend($request->delivery_code);
			if($check){
				$response['status'] = 'ok';
    			$response['message'] = array(
        					'en'=>JText::_('EN_BOOKPRO_VALIDATE_END_SUCCESS'),
        					'fr'=>JText::_('FR_BOOKPRO_VALIDATE_END_SUCCESS'));
				$response['data'] = new JObject();
				return true;
			}
			$error_code = $order->error_code;
		}	
		$response = self::generateError($error_code);
		return false;	
	}
	//--------------start trip-------------------------
	public function actionStartTrip(&$response, $status){
		$session_id = AndroidHelper::getSessionId();
		$session = $this->loadSession($session_id);		
		$error_code = "100";			
		if(!$session){
			$response = plgJBackendUser::generateError('10');
			$response['status'] = 'ko';
			return false;
		}else{
			$request = AndroidHelper::getBodyRequest();				
			$order = new BookproOrder();
			$order->id = $request->order_id;
			$customer_id = $session->userid;
			$order->driver_id = $customer_id;
			$check = $order->startTrip($session);
			if($check){
				$response['status'] = 'ok';
    			$response['message'] = array(
        					'en'=>JText::_('EN_BOOKPRO_START_TRIP_SUCCESS'),
        					'fr'=>JText::_('FR_BOOKPRO_START_TRIP_SUCCESS'));
				$response['data'] = new JObject();
				return true;
			}
			$error_code = $order->error_code;
		}	
		$response = self::generateError($error_code);
		return false;	
	}
	//--------------Arrive pick-------------------------
	public function actionarrivepick(&$response, $status){
		$session_id = AndroidHelper::getSessionId();
		$session = $this->loadSession($session_id);		
		$error_code = "100";			
		if(!$session){
			$response = plgJBackendUser::generateError('10');
			$response['status'] = 'ko';
			return false;
		}else{
			$request = AndroidHelper::getBodyRequest();				
			$order = new BookproOrder();
			$order->id = $request->order_id;
			$customer_id = $session->userid;
			$order->driver_id = $customer_id;
			$check = $order->arrivePick($session);
			if($check){
				$response['status'] = 'ok';
    			$response['message'] = array(
        					'en'=>JText::_('EN_BOOKPRO_ARRIVE_PICK_SUCCESS'),
        					'fr'=>JText::_('FR_BOOKPRO_ARRIVE_PICK_SUCCESS'));
				$response['data'] = new JObject();
				return true;
			}
			$error_code = $order->error_code;
		}	
		$response = self::generateError($error_code);
		return false;	
	}
	//--------------End trip-------------------------
	public function actionEndTrip(&$response, $status){
		$session_id = AndroidHelper::getSessionId();
		$session = $this->loadSession($session_id);		
		$error_code = "100";			
		if(!$session){
			$response = plgJBackendUser::generateError('10');
			$response['status'] = 'ko';
			return false;
		}else{
			$request = AndroidHelper::getBodyRequest();				
			$order = new BookproOrder();
			$order->id = $request->order_id;
			$customer_id = $session->userid;
			$order->driver_id = $customer_id;
			$order->receiver_name = $request->receiver_name;
			$check = $order->endTrip($session);
			if($check){				
				$response['status'] = 'ok';
    			$response['message'] = array(
        					'en'=>JText::_('EN_BOOKPRO_END_TRIP_SUCCESS'),
        					'fr'=>JText::_('FR_BOOKPRO_END_TRIP_SUCCESS'));
				$response['data'] = new JObject();
				$response['data'] = (object)array('order_id'=>"$order->id");
				return true;
			}
			$error_code = $order->error_code;
		}	
		$response = self::generateError($error_code);
		return false;	
	}
	public function actionPayment(&$response, $status){
		$session_id = AndroidHelper::getSessionId();
		$session = $this->loadSession($session_id);		
		$error_code = "100";			
		if(!$session){
			$response = plgJBackendUser::generateError('10');
			$response['status'] = 'ko';
			return false;
		}else{
			$request = AndroidHelper::getBodyRequest();			
			$order = new BookproOrder();
			$order->id = $request->order_id;
			$check = $order->payment($request);
			
			if($check){
				$response['status'] = 'ok';
    			$response['message'] = array(
        					'en'=>JText::_('EN_BOOKPRO_PAYMENT_SUCCESS'),
        					'fr'=>JText::_('FR_BOOKPRO_PAYMENT_SUCCESS'));
				$response['data'] = new JObject();
				$response['data'] = (object)array('order_id'=>"$order->id");
				
				return true;
			}
			$error_code = $order->error_code;
		}	
		$response = self::generateError($error_code);
		return false;			
	}	
	
	public function actionGetClosest(&$response, $status){		
		$session_id = AndroidHelper::getSessionId();
		$session = $this->loadSession($session_id);		  	
		if(!$session){
			$response = self::generateError('10');
			return false;
		}else{
			
			$order = new BookproOrder();
			$data = $order->getClosest($session);
			if($order->error_code){
				$response = self::generateError($order->error_code);
				return false;
			}
			$response['status'] = 'ok';
			$response['message'] = array(
						'en'=>JText::_('EN_BOOKPRO_GET_CLOSEST_SUCCESS'),
						'fr'=>JText::_('FR_BOOKPRO_GET_CLOSEST_SUCCESS'));
			$response['data'] = new JObject();
			$response['data']->order = AndroidHelper::formatOrder($data);
			return true;
		}
	}
  
	private function loadSession($session_id){
		$session = new BookproSession(array('session_id'=>$session_id));
		if($session->loadSessionById()){
			return $session;
		}
		return false;
	}
  
  
  public function onRequestOrder($module, &$response, &$status = null)
  {
    if ($module !== 'order') return true;

    // Add to module call stack
    jBackendHelper::moduleStack($status, 'order');

    $app = JFactory::getApplication();
    $action = $app->input->getString('action');
    $resource = $app->input->getString('resource');
	try{
		 switch ($resource)
	    {
		   case 'edit':
		          return $this->actionEdit($response, $status);
		        break;
		   case 'getmydata':
		   		  return  $this->actionGetMyData($response, $status);
		   		break;
		   case 'createorder':
		        return $this->actionCreateOrder($response, $status);		        
		        break;
		   case 'price':
		   		return $this->actionGetPrice($response, $status);
		   		break;
		   case 'getclosest':
		   		return $this->actionGetClosest($response, $status);
		   		break;
	       case 'getdetail':
		   		return $this->actionGetDetail($response, $status);
		   		break;
		   case 'caculatePrice':
		   		return $this->getClosest($response, $status);
		   		break;
		   case 'accept':
		   		return $this->actionAccept($response, $status);
		   		break;
		   case 'validateend':
		   		return $this->actionValidateEnd($response, $status);
		   		break;
		   case 'payment':
		   		return $this->actionPayment($response, $status);
		   		break;
		   case 'starttrip':
		   		return $this->actionStartTrip($response, $status);
		   		break;
		    case 'endtrip':
		   		return $this->actionEndTrip($response, $status);
		   		break;
		    case 'gettripdetail':
		   		return $this->getClosest($response, $status);
		   		break;
		   case 'cancel':
		   		return $this->actionCancel($response, $status);
		   		break;
		    case 'ignore':
		   		return $this->actionIgnore($response, $status);
		   		break;
			case 'arrivepick':
		   		return $this->actionarrivepick($response, $status);
		   		break;
		   
		   default:
		   		$response =  self::generateError('0');
		   		return false;
		   		break;
	    }
			
	}catch (Exception $e){
		 $response = self::generateError('Error'); // Login failed
		 $response['message'] = array('en'=>'System error','fr'=>'System error');
		 AndroidHelper::write_log('plg_exeption.txt','function '.$resource.PHP_EOL.' request'.json_encode(AndroidHelper::getBodyRequest()).PHP_EOL.'Msg: '.$e->getMessage());
		 $response['status'] = 'ko';
		 $response['msg_error'] = $e->getMessage();
     	 return false;
	}
    return true;
  }
}
?>
