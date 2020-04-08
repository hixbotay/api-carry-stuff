<?php
/**
 * @package 	Bookpro
 * @author 		Ngo Van Quan
 * @link 		http://joombooking.com
 * @copyright 	Copyright (C) 2011 - 2012 Ngo Van Quan
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id$
 **/
class BookProControllerCallBack extends JControllerLegacy{
	private $session;
	private $_db;
	function __construct($config = array())
	{
		AImporter::helper('android');		
		//AndroidHelper::write_log('callback.txt', $_SERVER['QUERY_STRING']);
		AndroidHelper::write_log('callback.txt', $_SERVER['REQUEST_URI']);
		$this->_db = JFactory::getDbo();
		parent::__construct($config);
	}
	
	
	private function getOrder($order_id){
		$table = new TableOrders($this->_db);
		$table->load($order_id);
		return $table;
	}
	
	private function getOrderData($order_id){
		$query = $this->_db->getQuery(true);
		$query->select('*')->from('#__bookpro_orders')->where('id = '.(int)$order_id);
		$this->_db->setQuery($query);
		return $this->_db->loadObject();
	}

	//cancel order
	function cancelorder(){
		$app = JFactory::getApplication();
		$order_id = $app->input->getInt('order_id');
		$timeout = $app->input->getInt('timeout');
		AImporter::table('orders');
		$table = $this->getOrder($order_id);
//		JLog::addLogger(array('text_file' => 'ordercancel.txt','text_file_path'=>'logs','text_file_no_php'=>1,'text_entry_format' => '{DATE} {TIME} {MESSAGE}'),JLog::ALERT);		
//		JLog::add('order_id: '.$order_id.' timeout: '.$timeout,JLog::ALERT,'com_bookpro');
		if(!(!$table->id || $table->is_accepted || $table->is_cancelled)){			
			if($timeout > 25){
				sleep(25);
				$timeout -= 25;
				BookProHelper::pingUrl(JUri::root().'index.php?option=com_bookpro&task=callback.cancelorder&order_id='.$order_id.'&timeout='.$timeout);			
			}else{			
				sleep($timeout);
				$table = new TableOrders($this->_db);
				$table->load($order_id);
				if(!$table->id || $table->is_accepted || $table->is_cancelled){			
					$app->close();	
				}
				$table->is_cancelled = 1;
				$check = $table->store();	
				AImporter::helper('android');
				AndroidHelper::write_log('order.txt', 'Order '.$order_id.' cancelled itself');
					
			}
		}

		$table = null;
		unset($table);
		$app->close();	
		
	}	
	
	
	//remove driver from candidate list in session (apply for order not booking)
	private function session_remove_candidate($driver_id){
		AImporter::classes('session');
		$session = new BookproSession();
		$session->userid = $driver_id;
		$session->loadSessionByUserId();
		 
		if(isset($session->data->candidate)){
			$driver_list = $session->data->candidate->list;
			
			AImporter::helper('android');
			AndroidHelper::write_log('order.txt', 'Order '.$session->data->candidate->order_id.' remove candidate '.$driver_id);
			unset($session->data->candidate);
			$session->data->pn_sent = 1;
			$session->saveSession();
						
			return $driver_list;
		}
		return false;
	}
	
	private function checkOrderToStop($order_id,$driver_id){
		$order = $this->getOrderData($order_id);
		$order_params = json_decode($order->params);
		//check driver ignore, order is accpetd, order is cancelled, order is deleted
		if(!$order->id || $order->is_accepted || $order->is_cancelled || in_array($driver_id,$order_params->candidate->cancel_list)){				
			$this->session_remove_candidate($driver_id);
			JFactory::getApplication()->close();	
		}
	}
	
	private function getClosestDriver($vehicle_type_id,$location, $cancel_list, $offset = 0, $limit = 0){
		$closest_driver_list = MathHelper::googleGetClosest($vehicle_type_id,$location,50,0,50);
		$driver_list = array();									//---driver candidate list
		foreach ($closest_driver_list as $id){
			if(!in_array( $id, $cancel_list )){
				$driver_list[]= $id;
			}
		}
		return $driver_list;
	}
	
	//Do action driver cancel booking
	public function processDriverCancelBooking(){
		AImporter::table('orders');
		AImporter::helper('math');
		$app = JFactory::getApplication();
		$order_id = $app->input->getInt('order_id');
		$driver_id = $app->input->getInt('driver_id');
			
		$table = $this->getOrder($order_id);
		$order_params = json_decode($table->params);		
		//add driver to cancel list
		$order_params->candidate->wait = 0;
		$order_params->candidate->hold = 0;
		if($driver_id){
			$order_params->candidate->cancel_list[] = $driver_id;	
		}
			
		$table->params = json_encode($order_params);
		$check = $table->store();

		AImporter::classes('order');
		$order = new BookproOrder();
		$order->id = $order_id;
		//clear candidate in the driver session
		$this->session_remove_candidate($driver_id);
		
		/* Update driver candidate list */			
		$cancel_list = $order_params->candidate->cancel_list;	//---cancel list of the order
		//get new driver list without cancel list
		$driver_list = $this->getClosestDriver($table->vehicle_type_id, json_decode($table->from), $cancel_list);
		
		$next_driver = $order->saveCandidateDriver($driver_list);
		BookProHelper::pingUrl(JUri::root().'index.php?option=com_bookpro&task=callback.drivercancelbooking&order_id='.$order_id.'&driver_id='.$next_driver.'&timeout='.JComponentHelper::getParams('com_bookpro')->get('timeout_driver',10));
		$app->close();
	}
	
	//remove driver from candidate list of booking
	function drivercancelbooking(){		
		$app = JFactory::getApplication();
		$order_id = $app->input->getInt('order_id');
		$driver_id = $app->input->getInt('driver_id');
		$timeout = $app->input->getInt('timeout');
//		JLog::addLogger(array('text_file' => 'drivercancelbooking.txt','text_file_path'=>'logs','text_file_no_php'=>1,'text_entry_format' => '{DATE} {TIME} {MESSAGE}'),JLog::ALERT);		
//		JLog::add('order_id: '.$order_id.' timeout: '.$timeout.' driver: '.$driver_id,JLog::ALERT,'com_bookpro');		
		//check driver ignore, order is accpetd, order is cancelled, order is deleted
		$this->checkOrderToStop($order_id, $driver_id);
		if($timeout > 25){
			sleep(25);
			$timeout -= 25;
			BookProHelper::pingUrl(JUri::root().'index.php?option=com_bookpro&task=callback.drivercancelbooking&order_id='.$order_id.'&driver_id='.$driver_id.'&timeout='.$timeout);							
		}else{		
			sleep($timeout);	
			$this->checkOrderToStop($order_id, $driver_id);
			BookProHelper::pingUrl(JUri::root().'index.php?option=com_bookpro&task=callback.processdrivercancelbooking&order_id='.$order_id.'&driver_id='.$driver_id);
		}
		$app->close();	
		
	}
	
	function sendClosestOrderPN(){
		$app = JFactory::getApplication();
		$driver_id = $app->input->getInt('driver_id');
		$order_id = $app->input->getString('order_id');
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('*')->from('#__bookpro_customer_pn')->where('user_id = '.(int)$driver_id)->order('id DESC');
		
		$db->setQuery($query);
		$pn_list = $db->loadObjectList();
		//delete old token if more than 10 token for a user
// 		if(count($pn_list > 20)){
// 			$remove_array = array();
// 			for($i = 20;$i<count($pn_list);$i++){
// 				$remove_array[] = $pn_list[$id]->id;
// 			}
// 		}
		
		AImporter::classes('pn');
		$pn = new BookproPN();
		$message = array('loc-key' => "have_closest_order",'loc-args'=>array($order_id));
		foreach ($pn_list as $item){
			$result = $pn->push_message($item->os, $item->device_token, $item->id, $message, 1, $item->push_sound);
			//debug($result);
		}
		$pn = null;
		$app = null;
		$db = null;
		$app->close();
		
	}
	
	function sendvalidatesms(){
		AImporter::table('orders');
		$app = JFactory::getApplication();
		$order_id = $app->input->getInt('order_id');
		$table = $this->getOrder($order_id);
		if($table->delivery_code){
			$dispatcher    = JDispatcher::getInstance();
			$import 	= JPluginHelper::importPlugin('bookpro','product_sms' );
			require_once JPATH_ADMINISTRATOR.'/components/com_bookpro/models/application.php';
			$applicationModel	= new BookProModelApplication();
  			$msg = $applicationModel->getObjectByCode('DELIVERY_CODE_SMS');
			$receipt = json_decode($table->recipient_info);
			$sms_content = array(
				'sms'=>str_replace('{code}', $table->delivery_code, $msg->email_customer_body),
				'phone'=>$receipt->phone
			);
			$send = $dispatcher->trigger( "onBookproSendSms", array($sms_content));
			//var_dump($send);
		}
		$table = null;
		$app->close();
		
	}
	
	//cancel order after accept if buyer does not pay for a long time
	function cancel_order_after_accept(){
		AImporter::table('orders');
		$app = JFactory::getApplication();
		$order_id = $app->input->getInt('order_id');		
		$timeout = $app->input->getInt('timeout');
		$table = $this->getOrder($order_id);
		if(!(!$table->id || $table->is_cancelled || $table->is_paid)){			
			if($timeout > 25){
				sleep(25);
				$timeout -= 25;
				BookProHelper::pingUrl(JUri::root().'index.php?option=com_bookpro&task=callback.cancel_order_after_accept&order_id='.$order_id.'&timeout='.$timeout);			
			}else{			
				sleep($timeout);
				AImporter::helper('android');
				$table = $this->getOrder($order_id);
				//echo '<pre>';
				//var_dump($table);die;
				if(!$table->id || $table->is_cancelled || $table->is_paid){			
					$app->close();	
				}
				$params = json_decode($table->params);
				if($params->is_paying){
					//order is paying, wait 10s
					BookProHelper::pingUrl(JUri::root().'index.php?option=com_bookpro&task=callback.cancel_order_after_accept&order_id='.$order_id.'&timeout=10');			
					$app->close();	
				}
				$table->is_cancelled = 1;
				//set free for driver
				$check = $table->store();	
				AImporter::classes('session');
				$session = new BookproSession();
				$session->userid = $table->driver_id;
				$session->loadSessionByUserId();
				//check if it is driver
				if($session->data->vehicle){
					$session->free = 1;
					$session->saveSession();
					AndroidHelper::write_log('order.txt', 'Order '.$order_id.' cancelled after accepted set driver '.$session->userid.' free = 1');
				}else{
					AndroidHelper::write_log('order.txt', 'Order '.$order_id.' cancelled after accepted. driver '.$session->userid);
				}
				
					
			}
		}

		$table = null;
		unset($table);
		$app->close();	
		
	}
	
	
	
}