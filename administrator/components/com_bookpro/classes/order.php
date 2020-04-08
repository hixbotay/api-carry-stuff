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

require_once JPATH_ADMINISTRATOR.'/components/com_bookpro/helpers/math.php';
require_once JPATH_ADMINISTRATOR.'/components/com_bookpro/helpers/android.php';

class BookproOrder extends JObject
{
	public $id;
	public $data;
	public $promo_code;
	public $price;
	public $_db;
	public $table;
	public $error_code;
	public $error_msg;
	public $driver_id;
	public $user;
	
	
	public function __construct($array = null){
		if(!class_exists('TableOrders')){
			require_once JPATH_ADMINISTRATOR.'/components/com_bookpro/tables/orders.php';
		}
		if(!class_exists('BookproPrice')){
			require_once JPATH_ADMINISTRATOR.'/components/com_bookpro/classes/price.php';
		}
		$this->error_code = 0;
		$this->price = new BookproPrice();
		$this->_db = JFactory::getDbo();
		$this->table = new TableOrders($this->_db);
		if($array){
			foreach ($array as $key=>$val){
				$this->set($key,$val);
			}
		}
	}
	
	
	private function caculatePrice(){
		//$this->price->setVehicleById($this->data['vehicle_id']);
		$this->price->setVehicleTypeById($this->data['vehicle_type_id']);
//		$this->price->setTransportTypeById($this->data['transport_type_id']);
		$this->price->setDeliveryCode($this->data['delivery_code']);
		$this->price->setOrderType($this->data['is_booked']);
		//var_dump($this->data);die;
		if($this->data['is_booked']){
			//echo $this->data['start_time'];die;
			$this->price->setDate($this->data['start_time']);
		}else{
			$this->price->setDate(JHtml::_('date','now','Y-m-d'));
		}
		$this->price->vat = JComponentHelper::getParams('com_bookpro')->get('vat');
		$this->price->distance = $this->data['distance'];
		$this->data['total'] =  $this->price->getTotal();
		return $this->data['total'];
	}
	
	public function setPrice(){
		$this->caculatePrice();
		if(isset($this->data['params'])){
			$params = ($this->data['params']);
		}else{
			$params=new JObject();
		}
		
		$price = new JObject();
		if(!empty($this->data['delivery_code'])){
			$price->delivery_validation = $this->price->price_list->validateend->params;
			$price->main = $this->data['total'] - $price->delivery_validation;
		}else{
			$price->delivery_validation = 0;
			$price->main = $this->data['total'] - $price->delivery_validation;
		}
		$params->price = $price;
		$this->data['params'] = ($params);
		return $this->data['total'];
	}
	
	public function processCoupon(){
		require_once JPATH_ADMINISTRATOR.'/components/com_bookpro/models/coupon.php';
		$coupon = new BookProModelCoupon();
		$this->promo_code = $coupon->getObjectByCode($this->data['promo_code']);
		if($this->promo_code){
			if($this->promo_code->subtract_type==1){
				$discount= ($this->data['total'] * $this->promo_code->value)/100;
				$this->data['total'] -= $discount;
				$this->data['discount'] =$discount;				
			}else {
				$this->data['total'] -= $this->promo_code->value;
				$this->data['discount'] = $this->promo_code->value;
			}
			$this->data['params']->promo_code = $this->data['promo_code'];
			return true;		
		}else {			
			return false;
		}
	}
	
	public function addCoupon(){
		$add = $this->processCoupon();
		if($add){
			$this->promo_code->remain -= 1;
			return $this->promo_code->store();
		}
		$this->error_code = 21;
		return false;
		
	}
	
	/**
	 * Assigning a driver to a candidate of order and send PN
	 * list: list of driver
	 */
	public function saveCandidateDriver($list){
		require_once JPATH_ADMINISTRATOR.'/components/com_bookpro/classes/session.php';
		if(empty($list)){
			return false;
		}
		
		$driver_list = $list;
		array_shift($driver_list);
		foreach ($list as $id){
			$session = new BookproSession(array('userid'=>$id));
			$session->loadSessionByUserId();
			//TODO check if 2 process save candidate do same time
			if(!isset($session->data->candidate)){			
				$candidate = new JObject();
				$candidate->order_id = $this->id;
				$candidate->list = $driver_list;
				$session->data->candidate = $candidate;
				if($session->saveSession()){
					//send PN
					require_once JPATH_ADMINISTRATOR.'/components/com_bookpro/helpers/bookpro.php';
					BookProHelper::pingUrl(JUri::root().'index.php?option=com_bookpro&task=callback.sendClosestOrderPN&driver_id='.$id.'&order_id='.$this->id);
					return $id;
				}
			}else{ 
				array_shift($driver_list);
			}
			
		}
		return false;
		
	}
	
	
	public function save($data){		
		//delete old order
		if(!$data['is_booked']){
			$query = $this->_db->getQuery(true);
			$query->delete('#__bookpro_orders')->where('is_accepted = 0 AND is_booked=0 AND is_cancelled = 0 AND customer_id='.$data['customer_id']);
			$this->_db->setQuery($query);
			$this->_db->execute();
		}
		//Client will prevent to create order if start time < a period time and reservation is booking, so it not need server
		$data['params'] = new JObject();
		$data['params']->candidate = new JObject();
		$data['params']->candidate->wait = 0;
		$data['params']->candidate->hold = 0;
		$data['params']->candidate->cancel_list = array();		
		
		$this->data = $data;
		$this->data['created_time'] = JHtml::_('date','now','Y-m-d H:i:s');
		$this->data['trip_location'] = '[]';
		$this->data['trip_start_time'] = '';
		//caculate price
		$this->setPrice();
		//process promo code
		if(!empty($data['promo_code'])){	
			if(!$this->addCoupon()){
				return false;
			}
		}
		$save = $this->store();
		if($save){
			$this->id = $this->table->id;
			require_once JPATH_ADMINISTRATOR.'/components/com_bookpro/helpers/bookpro.php';
			$location = json_decode($this->table->from);
			$driver_list = MathHelper::googleGetClosest($this->table->vehicle_type_id,$location, 50, 0, 50);
			AndroidHelper::write_log('order.txt', 'Order '.$this->id.' is created. Driver list: '.implode(',',$driver_list));
	
			$driver_id = $this->saveCandidateDriver($driver_list);
			//set timeout to cancel if it is order
			$timeout_order = JComponentHelper::getParams('com_bookpro')->get('timeout_order',60);
			if(!$driver_id){
				$timeout_order = '60';
			}
			BookProHelper::pingUrl(JUri::root().'index.php?option=com_bookpro&task=callback.cancelorder&order_id='.$this->id.'&timeout='.$timeout_order);				
			
			//set timeout for driver to accept order
			BookProHelper::pingUrl(JUri::root().'index.php?option=com_bookpro&task=callback.drivercancelbooking&order_id='.$this->id.'&driver_id='.$driver_id.'&timeout='.JComponentHelper::getParams('com_bookpro')->get('timeout_driver',10));					
				
					
			
			return true;
		}
		return false;
	}
	
	private function store(){
		$this->data['params'] = json_encode($this->data['params']);		
		if($this->table->save($this->data)){
			return true;
		}else{
			AndroidHelper::write_log('order.txt', 'Order save FAILED: '.json_encode($this->data));
			return false;
		}
	}
	
	//get closest booking for driver
//	private function getClosestBooking($session){
//		$date = JFactory::getDate('now',JFactory::getConfig()->get('offset'));
//		$date->modify('+ '.JComponentHelper::getParams('com_bookpro')->get('latest_booking_time',30).' minutes');
//		$query = $this->_db->getQuery(true);
//		$query->select('*')
//			->from('#__bookpro_orders')
//			->where('is_accepted = 0 AND  is_booked=1 AND is_cancelled = 0')
//			->where('start_time < '.$this->_db->quote($date->toSql(true)))
//			->where('vehicle_type_id = '.(int)$session->data->vehicle->current_type);
//		$this->_db->setQuery($query);
//		//echo $query;die;
//		$data = $this->_db->loadObjectList();
//		$distance = 50;
//		$result = false;
//
//		if($data){
//			foreach ($data as $d){
//				
//				$from = json_decode($d->from);
//				$params = json_decode($d->params);
//				if($params->candidate->wait && $session->userid==$params->candidate->hold){
//					return $d;
//				}
//				$cancel_driver_list = $params->candidate->cancel_list;
//				//var_dump($cancel_driver_list);
//				if($from){					
//					$d->location_distance = MathHelper::haversineDistance((float)$from->latitude, (float)$from->longitude, (float)$session->lat, (float)$session->lng);
//					
//					if($d->location_distance < $distance && !in_array($session->userid,$cancel_driver_list) && !$params->candidate->wait){
//						$distance = $d->location_distance;
//						$result = $d;
//					}
//				}				
//			}
//		}
//		if($result->id){
//			$this->table->load($result->id);
//			$params = json_decode($this->table->params);
//			$params->candidate->wait = 1;
//			$params->candidate->hold = $session->userid;
//			$this->table->params = json_encode($params);
//			$this->table->store();
//		}
//		return $result;
//	}
	
	private function clearCandidateList($session){
		unset($session->data->candidate);
		unset($session->data->current_order);
		return $session->saveSession();
	}
	public function getClosest($session){
		//check session if it is not free without current delivery order, there is a bug need to fix
		$log_str = "order {$session->data->candidate->order_id} user {$session->userid} ";
		
		if(!$session->free){
			//check if it is driver
			if($session->data->vehicle){
				$query = $this->_db->getQuery(true);
				$query->select('count(*)')
				->from('#__bookpro_orders')
				->where('(driver_id = '.(int)$session->userid.')')
				->where('trip_status !=2 AND is_accepted = 1 AND is_cancelled = 0');
				$this->_db->setQuery($query);
				//			echo $query;die;
				$result = $this->_db->loadResult();
				if(!$result){
					$session->free = 1;
					$session->saveSession();
					AndroidHelper::write_log('order.txt', 'Driver '.$session->userid.' force save free = 1 Data:'.json_encode($session->data));
				}
			}			
		}
		$result = false;
		if(isset($session->data->candidate->order_id)){
			
			$order_id = (int)$session->data->candidate->order_id;
			$result = AndroidHelper::getOrderData($order_id,true);
			$log_str .= 'order: '.json_encode($result);
			//clear driver candidate list (this case will happen when customer create new order and original order has been deleted)
			if(!$result){				
				$this->clearCandidateList($session);
			}else{
				//return false if order is cancelled or accpeted and check if the driver is in cancel list (he has ignored the order)
				$params = json_decode($result->params);
				if(in_array($session->userid, $params->candidate->cancel_list) || $result->is_cancelled || $result->is_accepted){
					$log_str .= PHP_EOL.' ---- the order is remove';
					$this->clearCandidateList($session);
					$result = true;
				}
			}
		}else{
			if(isset($session->data->pn_sent)){
				unset($session->data->pn_sent);
				$session->saveSession();
				$this->error_code  = 100;
				$log_str .=PHP_EOL.'PN is sent';
				$result = false;
			}
		}
		//if have no order, find booking
		/*
		if(!($result)){
			$result = $this->getClosestBooking($session);
			//remove driver from candidate when time exceed
			if($result){
				require_once JPATH_ADMINISTRATOR.'/components/com_bookpro/helpers/bookpro.php';
				BookProHelper::pingUrl(JUri::root().'index.php?option=com_bookpro&task=callback.drivercancelbooking&order_id='.$result->id.'&driver_id='.$session->userid.'&timeout='.JComponentHelper::getParams('com_bookpro')->get('timeout_driver',10));					
			}
		}
		*/
		AndroidHelper::write_log('order_getclosest.txt', $log_str);
		return $result;		
	}
	
	public function ignore($session){
		if($this->loadTable()){
			//cancel ignore if order is paid
			if($this->table->is_paid){
				$this->error_code = "45";
				return false;
			}
			if($this->table->is_accepted){
				$this->error_code = "98";
				return false;
			}
			
			$params = json_decode($this->table->params);
			if(in_array($session->userid, $params->candidate->cancel_list)){
				$this->error_code = "99";
				return false;//driver ignored the order already
			}
			$list = $session->data->candidate->list;
			//move the order to next driver
			$this->saveCandidateDriver($list);
			//remove user from candidate list in session
			$this->clearCandidateList($session);	
			$params->candidate->wait = 0;
			$params->candidate->hold = 0;
			$params->candidate->cancel_list[] = $session->userid;
			$this->table->driver_id = 0;
			$this->table->is_accepted = 0;
			$this->table->params = json_encode($params);
			AndroidHelper::write_log('order.txt', 'Order '.$this->table->id.' is ignore by user '.$session->userid.'');
			return $this->table->store();
		}
		AndroidHelper::write_log('order.txt', 'Order null is ignore by user '.$session->userid.' error 40 order not exist');
		$this->error_code = "40";		 //order not existed
		return false;
	}
	
	private function loadTable($config = array()){
		if($this->table->id){
			$check = true;
		}
		if($this->id){
			$check = $this->table->load($this->id);			
		}
		if($check){
			foreach ($config as $key=>$val){
				if($this->table->$key != $val){
					return false;
				}
			}
			return true;
		}
		return false;
	}
	
	public function accept($session){
		if(!$this->validateOrder()){
			return false;
		}
		if($this->driver_id){
			//exception: order is accept before
			//			 driver is invalid
			if($this->table->is_accepted == 1){
				$this->error_code = "99";
				return false;
			}
			if(!$session->free){
				$this->error_code = "33";
				return false;
			}
			//check the driver in cancel list of the order
			$order_params = json_decode($this->table->params);
			if(in_array($this->driver_id, $order_params->candidate->cancel_list)){
				$this->error_code = "98";
				return false;
			}
			
			$query = $this->_db->getQuery(true);
			//get vehicle info
			$query->select('v.id, v.name, v.vehicle_type_id, type.name as vehicle_type_name, v.plate_number,v.capacity,v.desc')
				->leftJoin('#__bookpro_vehicle_type as type ON v.vehicle_type_id = type.id')
				->from('#__bookpro_vehicle as v')
				->where('v.driver_id='.$this->driver_id.' AND v.current=1');
			$this->_db->setQuery($query);
			$vehicle = $this->_db->loadObject();
			if($vehicle){
				$vehicle->vehicle_type_name = json_decode($vehicle->vehicle_type_name);
				$this->table->driver_id = $this->driver_id;
				$this->table->is_accepted = 1;
				$this->table->vehicle_id = json_encode($vehicle);
				$this->table->store();				
				
				if($this->table->is_booked){
					//if order type is booking still leave driver free
				}else{
					$session->free = 0;
				}
				
				$this->clearCandidateList($session);
				//auto cancel order if order not pay for a long time
				require_once JPATH_ADMINISTRATOR.'/components/com_bookpro/helpers/bookpro.php';
				BookProHelper::pingUrl(JUri::root().'index.php?option=com_bookpro&controller=callback&task=cancel_order_after_accept&order_id='.$this->table->id.'&timeout='.JComponentHelper::getParams('com_bookpro')->get('timeout_order_after_accept','120'));
				
				AndroidHelper::write_log('order.txt', 'Order '.$this->table->id.' is accepted by driver '.$session->userid);
				return true;
			}else{
				$this->error_code = "34";
			}			
		}	
		$this->error_code = "100";		 //order not existed
		return false;
		
	}
	
	public function cancel($session){				
		if($this->loadTable()){
			//check the trip of order is started
			if($this->table->trip_status){
				$this->error_code = "98";
				return false;
			}
			//check the order is cancelled already
			if($this->table->is_cancelled){
				$this->error_code = "99";
				return false;
			}
			if($this->driver_id){
				//driver cancel
				if($this->table->driver_id != $this->driver_id){
					$this->error_code = "42";
					return false;	
				}
				//order is not accepted
				if(!$this->table->is_accepted){
					$this->error_code = "42";
					return false;
				}
				
				//not allow if the order is paid but removed because driver can be broken vehicle or have some accident happen
				/* 
				if($this->table->is_paid){
					$this->error_code = "45";
					return false;
				}
				*/
				
				$session->free = 1;
				$this->clearCandidateList($session);
				AndroidHelper::write_log('order.txt', 'Driver '.$session->userid.' cancelled set free = 1');				
			}else{
				//user is not the user create the order
				if($session->userid != $this->table->customer_id){
					$this->error_code = "42";
					return false;
				}
				//user cancel. Set free for driver
				if($this->table->driver_id){
					require_once JPATH_ADMINISTRATOR.'/components/com_bookpro/classes/session.php';
					$driver_session = new BookproSession();
					$driver_session->userid = $this->table->driver_id;
					$driver_session->loadSessionByUserId();
					$driver_session->free = 1;
					if($this->clearCandidateList($driver_session)){
						AndroidHelper::write_log('order.txt', 'Driver '.$driver_session->userid.' is cancelled order by customer. Set free = 1');
					}
				}
			}
			$params = json_decode($this->table->params);
			$params->cancel_comment = $this->comment;
			//set user cancel
			if($this->driver_id){
				$this->table->cancel = "driver:{$session->userid}";
			}else{
				$this->table->cancel = "customer:{$session->userid}";
			}					
			$this->table->params = json_encode($params);
			$this->table->is_cancelled = 1;
			
			AndroidHelper::write_log('order.txt', 'Order '.$this->table->id.' cancelled by user '.$session->userid);
			return $this->table->store();
		}
		$this->error_code = "40";		 //order not existed
		return false;
	}
	
	public function startTrip($session){
		if(!$this->validateOrder()){
			return false;
		}
		//start trip already
		if($this->table->trip_status == 1){
			$this->error_code = "99";
			return false;
		}
		//the order is executed by other function that change status of trip
		if($this->table->trip_status != 0){
			$this->error_code = "98";
			return false;
		}
		
		if($this->driver_id){	
			if($this->table->driver_id != $this->driver_id){
				$this->error_code = "42";
				return false;	
			}
			$this->table->trip_start_time = JHtml::_('date','now','Y-m-d H:i:s');
			
			$this->table->trip_status = 1;
			//add current order for session
			$session->data->current_order = new JObject();
			$session->data->current_order->lastupdate = 0;
			$session->data->current_order->id = $this->table->id;
			if($session->saveSession()){
				if($this->table->store()){
					AndroidHelper::write_log('order.txt', 'Order '.$this->table->id.' start by driver '.$session->userid);
					return true;
				}
			}
			$this->error_code = '100';	
		}else{
			$this->error_code = "33"; //not driver
		}		
				 //order not existed
		return false;
	}
	
	public function arrivePick($session){
		if(!$this->validateOrder()){
			return false;
		}
		$this->error_code = "40";
		//arrive pick already
		if($this->table->is_pick_arrived){
			$this->error_code = "99";
			return false;
		}
		//only allow driver
		if($this->driver_id){
			if($this->table->driver_id != $this->driver_id){
				$this->error_code = "42";
				return false;	
			}
			$this->table->is_pick_arrived = 1;
			
			if($this->table->store()){
				//set driver not free if order type is booking
				if($this->table->is_booked){
					$session->free = 0;
					$session->saveSession();
				}
				AndroidHelper::write_log('order.txt', 'Order '.$this->table->id.' arrive pick by driver '.$session->userid);
				return true;
			}
			$this->error_code = '100';	
		}else{
			$this->error_code = "33"; //not driver
		}		
				 //order not existed
		return false;
	}
	
	public function endTrip($session){
		if(!$this->validateOrder()){
			return false;
		}
		
		//end trip already
		if($this->table->trip_status == 2){
			$this->error_code = "99";
			return false;
		}
		//only allow if trip is started
		if($this->table->trip_status != 1){
			$this->error_code = "98";
			return false;
		}
		
		
		$this->error_code = "40";	
		if($this->driver_id){	
			if($this->table->driver_id != $this->driver_id){
				$this->error_code = "42";//driver incorrect
				return false;
			}
			$this->table->end_time = JFactory::getDate('now',JFactory::getConfig()->get('offset'))->toSql(true);
			$this->table->trip_status = 2;
			$this->table->receiver_name = $this->receiver_name;
			//remove current order for session
			unset($session->data->current_order);
			unset($session->data->candidate);
			$session->free = 1;
			if($session->saveSession()){
				AndroidHelper::write_log('order.txt', 'Driver '.$session->userid.' free = 1, unset candidate, unset current order');
				if($this->table->store()){					
					AndroidHelper::write_log('order.txt', 'Order '.$this->table->id.' end by Driver '.$session->userid);		
					return true;
				}
			}
			
			$this->error_code = '100';
				
		}else{
			$this->error_code = "33"; //not driver
		}
			 //order not existed
		return false;
	}
	
	public function validateend($code){
		$this->error_code = "100";
		if(!$this->validateOrder()){
			return false;
		}
		
		//validate already
		if($this->table->recipient_validate){
			$this->error_code = "99";
			return false;
		}
		
		if($this->driver_id){	
			if($this->table->driver_id != $this->driver_id){
				$this->error_code = "42";
				return false;	
			}
			if($this->table->delivery_code != trim($code)){
				$this->error_code = "22";
				return false;	
			}
			$this->table->recipient_validate = 1;
			if($this->table->store()){
				AndroidHelper::write_log('order.txt', 'Order '.$this->table->id.' validate end');
				return true;
			}
		}	
		$this->error_code = "40";		 //order not existed		
		return false;
	}
	
	public function getDetail(){
		$query = $this->_db->getQuery(true)
			->select('*')
			->from('#__bookpro_orders')
			->where('id='.$this->id);
		$this->_db->setQuery($query);
		return $this->_db->loadObject();
	}
	
	public function payment($data){
		
		require_once JPATH_ADMINISTRATOR.'/components/com_bookpro/helpers/currency.php';
		if($this->loadTable()){
			//return if the order is paid
			if($this->table->is_paid){
				$this->error_code = "99";
				return false;
			}
			//return if the order is cancelled
			if($this->table->is_cancelled){
				$this->error_code = "43";
				return false;
			}
			
			
			//set order is paying
			$this->table->params = json_decode($this->table->params);
			$this->table->params->is_paying = 1;
			$this->table->params = json_encode($this->table->params);
			$this->table->store();
			//process payment
			$check = $this->processPayment($data);
			
			//set order is done paying
			$this->table->params = json_decode($this->table->params);
			$this->table->params->is_paying = 0;
			$this->table->params = json_encode($this->table->params);
			$this->table->store();
			
			if($check){
				//change status
				$this->table->is_paid = 1;
				//send sms
				require_once JPATH_ADMINISTRATOR.'/components/com_bookpro/helpers/bookpro.php';
				BookProHelper::pingUrl(JUri::root().'index.php?option=com_bookpro&task=callback.sendvalidatesms&order_id='.$this->table->id);
				AndroidHelper::write_log('order.txt', 'Order '.$this->table->id.' is paid');
				return $this->table->store();
			}else{
				return false;
			}								
		}else{
			$this->error_code="40"; //order not existed
		}		 
		return false;
	}
	
	private function processPayment($request){
		$dispatcher    = JDispatcher::getInstance();
		$import 	= JPluginHelper::importPlugin('bookpro','payment_'.$request->payment_gateway );	
		
		if ($import){		
			$request->order = $this->table;
			//get customer info
			$this->_db->setQuery('select * from #__bookpro_customer where id = '.(int)$this->table->customer_id);
			$customer = $this->_db->loadObject();
			$request->customer = $customer;
			//get card token of customer
			$card = $this->getCustomerCard($customer,$request->payment_gateway);
// 			var_dump($card);die;
			if(!$card){
				//invalid card
				$this->error_code = '48';
				return false;
			}
			
			$request->card = $card->data;
			
			$request->total = CurrencyHelper::formatNumber($this->table->total);
			$desc = "Paiement de l'utilisateur '%s' au service Allo&Go pour la commande/réservation No: %s";
			$request->desc = sprintf($desc,$customer->email, $this->table->id);
			 
			
			if($request->order->total<=0){
				$config = JComponentHelper::getParams('com_bookpro');
				$currency = $config->get('main_currency');
				$data[0] = array(
						'status' => 1,
						'tx_id' => "NONE",
						'desc' => $desc,
						'total' => 0,
						'currency' => $currency,
						'created' => JHtml::_('date','now','Y-m-d H:i:s'),
						'method' => $request->payment_gateway
				);
			}else{
				$data = $dispatcher->trigger( "restpayment", array($request ));
			}
			if($data[0]){
				
				if(!$data[0]['status']){
					//payment failed, maybe it is invalid card, check log
					$default_error = array("48","2000","2001","2002","2003","2004","2005","2006","2007","2008","2009","2010");
					if(in_array($data[0]['error'], $default_error)){	
						$this->error_code = $data[0]['error'];
						$this->error_text = null;
					}else{
						$this->error_code = "39";
						$this->error_text = $data[0]['error_message'];
					}
					
					return false;
				}
				//payment success
				$data[0]['gateway']=$request->payment_gateway;			
				require_once JPATH_ADMINISTRATOR.'/components/com_bookpro/tables/transaction.php';
				$transaction_table = new TableTransaction($this->_db);
				$transaction_table->load(array('tx_id'=>$data[0]['tx_id']));				
				 
				$transaction = array(
					'order_id'=>$this->table->id,
					'created'=>JHtml::_('date',$data[0]['created'],'Y-m-d H:i:s'),
					'total'=>$data[0]['total'],
					'tx_id'=>$data[0]['tx_id'],
					'params'=>json_encode($data[0]));		
				if(!$transaction_table->save($transaction)){
					//add log
					AndroidHelper::write_log('transaction_error.txt', 'data: '.json_encode($transaction));
				}
				
				return true;
			}
			$this->error_code="39";
		
		}else{
			$this->error_code="0";
		}
		return $results;
	}
	
	//get card info of customer
	private function getCustomerCard($customer,$gateway=null){	
		//var_dump($customer);die;
		$cards = json_decode($customer->params);
		foreach ($cards->payment_config as $c){
			if($c->default){
				if($gateway && $gateway == $c->payment_gateway){
					return $c;
				}else{
					return $c;
				}
				
			}
		}
		return false;
	}
	
	private function validateOrder(){
		if(!$this->loadTable()){
			$this->error_code = "40";
			return false;
		}
		if($this->table->is_cancelled){
			$this->error_code = "43";
			return false;
		}
		return true;
	}
}
?>
