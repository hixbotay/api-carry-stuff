<?php
	
/**
 * @package 	Bookpro
 * @author 		Ngo Van Quan
 * @link 		http://joombooking.com
 * @copyright 	Copyright (C) 2011 - 2012 Ngo Van Quan
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: currency.php 16 2012-06-26 12:45:19Z quannv $
 **/

defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );

class AndroidHelper{
	static function getPaymentSetting(){
		jimport('joomla.filesystem.file');
		$data = JFile::read(JPATH_ADMINISTRATOR.'/components/com_bookpro/data/payment.json');
		
		
		return json_decode($data);
	}

	static function getBodyRequest(){
	//print_r($_SERVER);die; 
	  $body = file_get_contents('php://input');
	  $data = json_decode($body);
	  if(!$data){
		  return (object)$_REQUEST;
	}
	  return $data; 
  	} 
  	
  	static function getHeader($key_t){
  		$arh = array();
  				$rx_http = '/\AHTTP_/';
  				foreach($_SERVER as $key => $val) {
  					if( preg_match($rx_http, $key) ) {
  						$arh_key = preg_replace($rx_http, '', $key);
  						$rx_matches = array();
  						// do some nasty string manipulations to restore the original letter case
  						// this should work in most cases
  						$rx_matches = explode('_', $arh_key);
  						if( count($rx_matches) > 0 and strlen($arh_key) > 2 ) {
  							foreach($rx_matches as $ak_key => $ak_val) $rx_matches[$ak_key] = ucfirst($ak_val);
  							$arh_key = implode('-', $rx_matches);
  						}
  						$arh[$arh_key] = $val;
  					}
  				}
  			$headers = $arh;
	  	
	  	return $headers[$key_t];
  	}
  	
  	static function getSessionId(){
  		if(isset($_SERVER['HTTP_SESSION_ID'])){
  			return $_SERVER['HTTP_SESSION_ID'];
  		}
  		if(isset($_SERVER['HTTP_SESSION-ID'])){
  			return $_SERVER['HTTP_SESSION-ID'];
  		}
  		if(isset($_SERVER['HTTP_X_SESSION_ID'])){
  			return $_SERVER['HTTP_X_SESSION_ID'];
  		}
  		
  		return self::getHeader('session_id');
  	}
  	
  	private static function formatArray($data){
  		foreach($data as $key=>$var){
  			if(is_numeric($var)){
  				$data[$key] = (string)$var;
  			}
  			elseif (is_null($var) || $var == null){
  				$data[$key] = "";
  			}else{
  				
  			}
  		}
  		return $data;
  	}
  	
	private static function formatObject($data){
  		foreach($data as $key=>$var){
  			if(is_numeric($var)){
  				$data->$key = (string)$var;
  			}
  			elseif (is_null($var) || $var == null){
  				$data->$key = "";
  			}else{
  			}
  		}
  		return $data;
  	}
  	
  	static function format($data){
  		if(is_null($data)){
  			return "";
  		}
  		if(empty($data)){
  			return $data;
  		}
  		foreach($data as &$var){
  			if(is_numeric($var)){
  				$var = (string)$var;
  			}
  			elseif (is_null($var)){
  				$var = "";
  			}else{
  			}
  		}
  		return $data;
  	}
	public static function getCustomerInfo($id){
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('id as user_id, name, company_name,function,phone,mobile,address,city, post_code,user_type')->from('#__bookpro_customer')->where('id='.(int)$id);
		$db->setQuery($query);
		$result = $db->loadObject();
		if(!$result){
			$result =new JObject();
		}
		return $result;
	}
	
	public static function getOrderData($order_id,$is_driver =false){		
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('o.id as order_id, o.receiver_name, o.is_pick_arrived, o.created_time,o.trip_start_time, 
					o.from, o.to, o.customer_id, o.driver_id, o.recipient_info, o.distance, o.start_time, 
					o.end_time, o.trip_status, o.packages, o.vehicle_type_id, o.vehicle_id, o.total as price, 
					o.currency, o.recipient_validate, o.note as note, o.delivery_code, o.is_booked, 
					o.is_paid, o.is_accepted, o.is_cancelled, o.params')
			->from('#__bookpro_orders as o')
			->where('o.id='.(int)$order_id);
		if($is_driver){
			$query->select('o.total+o.discount as price');
		}
		
		$db->setQuery($query);
		$result = $db->loadObject();
		if(!$result){
			$result = new JObject();
		}
		
		return $result;
	}
	
	public static function formatOrder($order,$receiver_name = false){
		if(empty($order)){
			$order = new JObject();
			return $order;
		}
		if($order){
			if($order->price < 0){
				$order->price = 0;
			}
			$from = new JObject();
			$from->location = json_decode($order->from);
			$from->sender = self::getCustomerInfo($order->customer_id);
			$order->from = $from;
			$to = new JObject();
			$to->location = json_decode($order->to);
			$to->recipient = json_decode($order->recipient_info);	
			$order->to = $to;		
			$order->packages = json_decode($order->packages);
			$params = JComponentHelper::getParams('com_bookpro');
			$order->currency = (object)array(	'name'=>$params->get('main_currency_text','euros'),
												'code'=>$params->get('main_currency','EUR'));
			$order_params =  json_decode($order->params);
			$price = new stdClass();
			$price->delivery_validation = CurrencyHelper::formatNumber($order_params->price->delivery_validation);
			$price->total = CurrencyHelper::formatNumber($order->price);
			$price->main = CurrencyHelper::formatNumber($order->price - $order_params->price->delivery_validation);
			$order->price = self::format($price);
			if(isset($order_params->cancel_comment)){
				$order->cancel_comment = $order_params->cancel_comment;
			}else{
				$order->cancel_comment = "";
			}
						
			
			
			unset($order->recipient_info);
			unset($order->params);
			unset($order->customer_id);
			unset($order->driver_id);
			unset($order->end_time);
			unset($order->trip_status);
			unset($order->trip_start_time);
			if(!$receiver_name){
				unset($order->receiver_name);
			}
		}
		return self::format($order);
	}
	
	public static function formatDataByOrder($order,$receiver_name = false){
		$result = new JObject();
		$db = JFactory::getDbo();
		if(!$order->order_id){
			return $result;
		}
				
		//vehicle
		$vehicle = new JObject();
		if($order->vehicle_id){
			$vehicle = json_decode($order->vehicle_id);
			if(!is_object($vehicle)){
				$vehicle = new JObject();
			}
		}
		//driver
		$driver = new JObject();
		if($order->driver_id){
			$query = $db->getQuery(true);
			$query->select('d.id as user_id, d.name, d.mobile')
				->from('#__bookpro_customer as d')
				->where('d.id='.$order->driver_id);
			$db->setQuery($query);
			$driver = $db->loadObject();
			$driver->plate_number = $vehicle->plate_number;
		}	
		
		$trip = new JObject();
		$trip->start_time = $order->trip_start_time;
		$trip->end_time = $order->end_time;
		$trip->trip_status = $order->trip_status;
				
		$result->order = self::format(self::formatOrder($order,$receiver_name));
		$result->driver = self::format($driver);				
		$result->vehicle = self::format($vehicle);
		$result->trip = self::format($trip);
		$order = null;$driver = null;$vehicle = null;$trip = null;
		
		return $result;
	}
	
	public static function getOrderDetail($order_id,$receiver_name = false,$is_driver=false){
		$order = self::getOrderData($order_id,$is_driver);
		return self::formatDataByOrder($order,$receiver_name);
	}
	
	static function getPriceSetting($params = null){
		if(empty($params)){
			$params = JComponentHelper::getParams('com_bookpro');
		}
		$result = new JObject();
		$result->currency_name = $params->get('main_currency_text','euros');
		//$result->currency_code = $params->get('main_currency','EUR');
		return $result;
	}
	
	static function write_log($log_file, $error, $type = E_USER_NOTICE){
		date_default_timezone_set('Asia/Ho_Chi_Minh');
		$date = date('d/m/Y H:i:s');
		$error = $date.": ".$error."\n";
		
		$log_file = JPATH_ROOT."/logs/".$log_file;
		if(filesize($log_file) > 1048576 || !file_exists($log_file)){
			$fh = fopen($log_file, 'w');
		}
		else{
			//echo "Append log to log file ".$log_file;
			$fh = fopen($log_file, 'a');
		}
		
		fwrite($fh, $error);
		fclose($fh);
	}
  	
  	
}
?>