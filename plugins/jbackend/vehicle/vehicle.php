<?php
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
require_once JPATH_ADMINISTRATOR.'/components/com_bookpro/helpers/android.php';

class plgJBackendVehicle extends JPlugin
{
  public function __construct(& $subject, $config)
  {
    parent::__construct($subject, $config);
    $this->loadLanguage();
	JFactory::getLanguage()->load('com_bookpro_msg_group');
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
      case '16':
        	$error['code'] = '16';
        	$error['message'] = array(
        			'en'=>JText::_('EN_BOOKPRO_INVAILD_DRIVER'),
        			'fr'=>JText::_('FR_BOOKPRO_INVAILD_DRIVER'));
        	break;
      case '17':
        	$error['code'] = '16';
        	$error['message'] = array(
        			'en'=>JText::_('EN_BOOKPRO_VEHICLE_NOT_EXISTED'),
        			'fr'=>JText::_('FR_BOOKPRO_VEHICLE_NOT_EXISTED'));
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
	        					'en'=>JText::_('EN_BOOKPRO_INVALID_ACTION'),
	        					'fr'=>JText::_('FR_BOOKPRO_INVALID_ACTION'));
	        break;
    }
    return $error;
  }
  
	public function actionLoad(&$response, $status){		
		
		$session_id = AndroidHelper::getSessionId();
		//AndroidHelper::write_log('jb_vehicle.txt','request: '.json_encode($_REQUEST).PHP_EOL.'session: '.$session_id);
//		$datarequest = AndroidHelper::getBodyRequest();
		$session = $this->loadSession($session_id);
		if(!$session){
			$response = self::generateError('10');
			$response['status'] = 'ko';
			return false;
		}else{
			$customer_id = $session->userid;
			if(!$this->checkDriver($customer_id)){
				$response = self::generateError('16');
				$response['status'] = 'ko';
				return false;
			}else{
				$response['status'] = 'ok';
				$response['message'] = array(
		        					'en'=>JText::_('EN_BOOKPRO_LOAD_VEHICLE_SUCCESS'),
		        					'fr'=>JText::_('FR_BOOKPRO_LOAD_VEHICLE_SUCCESS'));
				$vehicle = new JObject();
				$vehicle->vehicles = $this->getVehicleByCustomer($customer_id);
				$response['data'] = $vehicle;
				return true;
			}
			
		}		
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
	private function getVehicleByCustomer($user_id){
		$db = JFactory::getDbo();		
		$query = $db->getQuery(true);
		$query->select('a.id, a.name, a.vehicle_type_id, type.name as vehicle_type_name, a.plate_number, a.current, a.capacity, a.desc')
		->leftJoin('#__bookpro_vehicle_type as type ON a.vehicle_type_id = type.id')
		->from('#__bookpro_vehicle as a')
		->where('driver_id='.$user_id);
		$db->setQuery($query);
		$result= $db->loadObjectList();
		foreach($result as &$item){
			$item->vehicle_type_name = json_decode($item->vehicle_type_name);
		}
		
		return $result;
	}
	
	//delete vehicle of a driver with id not exists
	private function deleteVehicleNoExisted($driver_id, $vehicle_exist){
		if(empty($driver_id)){
			return false;
		}
		$db = JFactory::getDbo();		
		$query = $db->getQuery(true);
		$query->delete('#__bookpro_vehicle')
		->where('driver_id='.$driver_id);
		if(!empty($vehicle_exist)){
			$query->where('id NOT IN ('.implode(',', $vehicle_exist).')');
		}
		$db->setQuery($query);
		return $db->execute();
	}
	
	public function actionChange(&$response, $status){		
		$session_id = AndroidHelper::getSessionId();
		$session = $this->loadSession($session_id);
		  	
		if(!$session){
			$response = self::generateError('10');
		}else{			
			$customer_id = $session->userid;
			if(!$this->checkDriver($customer_id)){
				$response = self::generateError('16');
			}else{
				$request = AndroidHelper::getBodyRequest();
				$response['status'] = 'ok';
				
				require_once JPATH_ADMINISTRATOR.'/components/com_bookpro/tables/vehicle.php';
				$db = JFactory::getDbo();
				$vehicle_exist = array();
				$data = array();
								
				foreach ($request->vehicles as $array){
					$array->driver_id = $customer_id;
					$data[] = (array)$array;					
					if(!empty($array->id)){
						$vehicle_exist[] = $array->id;
					}
					
				}
				//delete vehicle that have no id
				$this->deleteVehicleNoExisted($customer_id, $vehicle_exist);
				
				//save vehicle
				foreach ($data as $array){
					$table = new TableVehicle($db);
					$table->save($array);					
				}
				
				$response['message'] = array(
		        					'en'=>JText::_('EN_BOOKPRO_CHANGE_VEHICLE_SUCCESS'),
		        					'fr'=>JText::_('FR_BOOKPRO_CHANGE_VEHICLE_SUCCESS'));
				$response['data'] = (object)array('vehicles'=>$this->getVehicleByCustomer($customer_id));
				
				return true;
			}
		}
		return false;	
	}
	
	/*private function emptyCurrentByDriver($customer_id){
		$db = JFactory::getDbo();		
		$query = $db->getQuery(true);
		$query->update('#__bookpro_vehicle')
		->set('current=0')
		->where('driver_id='.$customer_id);
		$db->setQuery($query);
		return $db->execute();
	}*/
	
	public function actionSetcurrent(&$response, $status){		
		$session_id = AndroidHelper::getSessionId();
		$session = $this->loadSession($session_id);
		
		
		if(!$session){
			$response = plgJBackendUser::generateError('10');
			$response['status'] = 'ko';
			return false;
		}else{
			$request = AndroidHelper::getBodyRequest();
			$customer_id = $session->userid;
			
			if(!$this->checkDriver($customer_id)){
				$response = self::generateError('16');
				$response['data'] = new JObject();
			}else{
				JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_bookpro/tables');
				$vehicleTable= JTable::getInstance('Vehicle', 'Table');
				//check vehicle exists
				$check = $vehicleTable->load($request->id);
				if($vehicleTable->id){
					$response['status'] = 'ok';
					$response['message'] = array(
		        					'en'=>JText::_('EN_BOOKPRO_SET_CURRENT_VEHICLE_SUCCESS'),
		        					'fr'=>JText::_('FR_BOOKPRO_SET_CURRENT_VEHICLE_SUCCESS'));
					//$this->emptyCurrentByDriver($customer_id);//set all vehicle = 0
					$this->customername=BookProHelper::emptyCurrentByDriver($customer_id);
					$vehicleTable->current = 1;
					$vehicleTable->store();
					return true;
				}else{
					$response = self::generateError('17');
					$response['data'] = new JObject();
				}
				
			}
  			
		}
		return false;
	}
    
	private function loadSession($session_id){
		$session = new BookproSession(array('session_id'=>$session_id));
		if($session->loadSessionById()){
			return $session;
		}
		return false;
	}
	
  
  public function onRequestvehicle($module, &$response, &$status = null)
  {
    if ($module !== 'vehicle') return true;

    // Add to module call stack
    jBackendHelper::moduleStack($status, 'vehicle');

    $app = JFactory::getApplication();

    $resource = $app->input->getString('resource');
	try{
		 switch ($resource)
	    {
		   case 'load':
		          return $this->actionLoad($response, $status);
		        break;
		   case 'change':
		   		  return  $this->actionChange($response, $status);
		   		break;
		   case 'setcurrent':
		   		  return  $this->actionSetcurrent($response, $status);
		   		break;
		   default:
		   	 $response = plgJBackendvehicle::generateError('0'); // Action not specified
		      return false;
		      break;
	    }
			
	}catch (Exception $e){
		 $response['message'] = array('en'=>$e->getMessage(),'fr'=>$e->getMessage());
     	 $response['status'] = 'ko';
     	 return false;
	}
    return true;
  }
}
