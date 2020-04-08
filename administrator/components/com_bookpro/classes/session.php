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


class BookproSession extends JObject
{
	public $session_id;
	public $userid;
	public $data;
	public $time;
	private $_db;
	public $lat;
	public $lng;
	public $free;
	
	public function __construct($array = null){
		$this->_db = JFactory::getDbo();
		$this->data = new stdClass();
		if($array){
			foreach ($array as $key=>$val){
				$this->set($key,$val);
			}
		}
	}
	
	private function setParams($data){
		$this->session_id 	= $data->session_id;
		$this->userid		= $data->userid;
		$this->data 		= json_decode($data->data);
		$this->time 		= $data->time;
		$this->lat 			= $data->lat;
		$this->lng 			= $data->lng;
		$this->free 	= $data->free;
	}
	
	private function getTable(){
		require_once JPATH_ADMINISTRATOR.'/components/com_bookpro/tables/session.php';
		$table = new TableSession($this->_db);
		return $table;
	}
	
	public function getSessionById($id){		
		$query = $this->_db->getQuery(true)->select('s.*')
			->from('#__bookpro_session as s')
			->innerJoin('#__bookpro_customer as c ON c.id = s.userid')
			->innerJoin('#__users as u ON u.id = c.user_id')
			->where('c.state = 1')
			->where('u.block = 0')
			->where('s.session_id LIKE' .$this->_db->quote($id));
		$this->_db->setQuery($query);
		//var_dump($this->_db->loadObject());die;
		return $this->_db->loadObject();
	}
	
	public function loadSessionById(){
		if(empty($this->session_id)){
			return false;
		}
		$data = $this->getSessionById($this->session_id);
		if($data){
			if($data->session_id){
				$this->setParams($data);
				return true;
			}
		}
		
		return false;
	}
	
	public function getSessionByUser($id){
		$query = $this->_db->getQuery(true)
			->select('s.*')
			->from('#__bookpro_session as s')
			->innerJoin('#__bookpro_customer as c ON c.id = s.userid')
			->innerJoin('#__users as u ON u.id = c.user_id')
			->where('c.state = 1')
			->where('u.block = 0')
			->where('s.userid='.$id);
		$this->_db->setQuery($query);
		return  $this->_db->loadObject();		
	}
	
	public function loadSessionByUserId(){
		if(empty($this->userid)){
			return false;
		}
		$data = $this->getSessionByUser($this->userid);		
		if($data->session_id){
			$this->setParams($data);
			return true;
		}
		return false;
	}
	
	private function createSessionId(){
		$session_id = '';
		$chars = "0123456789abcdefghijklmnopqrstwvxyzABCDEFGHIJKLMNOPQRSTWVXYZ";
		srand((double)microtime()*1000000);
		$i = 0;
		$total_length = strlen($chars);
		while ($i <= 26) {
			$num = rand() % $total_length;
			$tmp = substr($chars, $num, 1);
			$session_id .= $tmp;
			$i++;
		}
		return $session_id;
	}
	
	
	public function saveSession(){		
		if(empty($this->userid)){
			return false;
		}
		//$offset = JFactory::getConfig()->get('offset');
		$data = array(
			'userid'=>$this->userid,
			'data'	=>json_encode($this->data),
			'session_id'=>$this->session_id,
			'lat'	=> $this->lat,
			'lng'	=> $this->lng,
			'free'=> $this->free
		);
		if($this->time){			
			$data['time'] = $this->time;
		}
		if(empty($this->session_id)){
			$data['session_id'] = $this->createSessionId();
			$this->session_id = $data['session_id'];
		}
		
		$table = $this->getTable();
		
		$result = $table->save($data);
		if($result){
			return true;
		}else{
			AImporter::helper('android');
			AndroidHelper::write_log('session_save_error.txt', 'data: '.json_encode($data));
		}
		return false;
		
	}
	
	/* Set current vehicle for driver*/
	public function setCurrentVehicle($force = false){
		//if user not free return false
		if(!$force && !$this->free){
			return false;
		}
		$query = $this->_db->getQuery(true);
		$query->select('v.id,v.vehicle_type_id,vt.name as vehicle_type_name,v.plate_number')
			->from('#__bookpro_vehicle as v')
			->leftJoin('#__bookpro_vehicle_type as vt ON vt.id = v.vehicle_type_id')
			->where('driver_id = '.(int)$this->userid)
			->where('current=1');
		$this->_db->setQuery($query);
		$vehicle = $this->_db->loadObject();
		$this->data->vehicle = new stdClass();
		$this->data->vehicle->current = 0;
		$this->data->vehicle->current_type = 0;
		$this->data->vehicle->current_type_name = new stdClass();
		$this->data->vehicle->plate_number = "";
		if($vehicle->id){
			$this->data->vehicle->current = (int)$vehicle->id;
			$this->data->vehicle->current_type = (int)$vehicle->vehicle_type_id;
			$this->data->vehicle->current_type_name = json_decode($vehicle->vehicle_type_name);
			$this->data->vehicle->plate_number = $vehicle->plate_number;
		}
		return true;
	}
	
	public function destroy(){		
		if(empty($this->session_id)){
			return false;
		}	
		$user_mail = $this->_db->setQuery('select u.email FROM #__bookpro_customer as u LEFT JOIN #__bookpro_session as s ON s.userid=u.id 
		WHERE s.session_id = '.$this->_db->quote($this->session_id))->loadResult();
		AndroidHelper::write_log('login.txt',$_SERVER['REQUEST_URI'].PHP_EOL.'SESSION DESTROY '.$user_mail.PHP_EOL.'--------');	 
		$table = $this->getTable();
		$result = $table->delete($this->session_id);
		if($result){
			return true;
		}
		return false;
		
	}
	
	public function is_driver(){
		if(empty($this->session_id)){
			return false;
		}
		
		if(isset($this->data->vehicle)){
			return true;
		}
		return false;
	}
	
}
?>
