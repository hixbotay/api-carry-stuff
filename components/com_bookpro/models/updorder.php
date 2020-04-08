<?php
/**
 * @package 	Bookpro
 * @author 		Ngo Van Quan
 * @link 		http://joombooking.com
 * @copyright 	Copyright (C) 2011 - 2012 Ngo Van Quan
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: order.php 56 2012-07-21 07:53:28Z quannv $
 **/
defined('_JEXEC') or die('Restricted access');


AImporter::helper('bookpro');
AImporter::model('passenger');

class BookProModelUpdOrder extends JModelAdmin
{
	protected $text_prefix = 'COM_BOOKPRO';

	public function getTable($type = 'Orders', $prefix = 'Table', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	function populateState(){
		$table = $this->getTable();
		$key = $table->getKeyName();
		$pk = JFactory::getApplication()->input->getInt($key);
		if ($pk) {
			$this->setState($this->getName() . '.id', $pk);
		}
	}

	public function getForm($data = array(), $loadData = true){
		$app = JFactory::getApplication();
		//get data from Form
		$form = $this->loadForm('com_bookpro.order','order', array('control'=> 'jform', 'load_data'=>$loadData));
		if (empty($form)){
			return false;
		}

		return $form;
	}

	protected function loadFormData(){
		$data = JFactory::getApplication()->getUserState('com_bookpro.edit.order.data', array());
		if(empty($data)){
			$data = $this->getItem();
		}
		return $data;
	}



	function getObjectByID($id)
	{
		AImporter::model('customer');
		
		$db = JFactory::getDbo();
		$query = $db->getQuery(true)->select('obj.*,c.mobile,c.firstname,c.email')
						->from('#__bookpro_orders as obj')
						->leftJoin('#__bookpro_customer as c ON c.id = obj.user_id')
						->where('obj.id = '.$id);
		$db->setQuery($query);
		$obj= $db->loadObject();
		return $obj;

	}

	public function getComplexItem($pk){
		$object 	= new JObject();

		$db = JFactory::getDBO();
		$query = $db->getQuery(true);

		// get Order
		$query->select('orders.*');
		$query->from('#__bookpro_orders AS orders');
		$query->where('orders.id = '. (int) $pk);
		$db->setQuery($query);
		$order =  $db->loadObject();
		// get Customer and Country name
		if($order){
			$query = $db->getQuery(true);
			$query->select('customer.*, country.country_name AS country_name');
			$query->from('#__bookpro_customer AS customer');
			$query->leftJoin('#__bookpro_country AS country ON country.id = customer.country_id');
			$query->where('customer.id = '. (int) $order->user_id);
			$db->setQuery($query);
			$customer =  $db->loadObject();
			
			$query = $db->getQuery(true);
			$query->select('passenger.*, country.country_name AS country');
			$query->from('#__bookpro_passenger AS passenger');
			$query->leftJoin('#__bookpro_country AS country ON country.id = passenger.country_id');
			$query->where('passenger.order_id = '. (int) $order->id);
			$db->setQuery($query);
			$passengers =  $db->loadObjectList();
		}
		
		if($order->agent_id){
			$query = $db->getQuery(true);
			$query->select('customer.*, country.country_name AS country_name');
			$query->from('#__bookpro_customer AS customer');
			$query->leftJoin('#__bookpro_country AS country ON country.id = customer.country_id');
			$query->where('customer.id = '. (int) $order->agent_id);
			$db->setQuery($query);
			$agent =  $db->loadObject();
		}

		$object->order 		= $order;
		$object->customer 	= $customer;
		$object->passengers = $passengers;
		$object->agent		= $agent;
		
		return $object;
	}
	function getAddon($order_id){
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$result = new stdClass();
	
		//get depart addon
		$query->select('addon_id');
		$query->from('#__bookpro_orders_addon');
		$query->where($db->quoteName('order_id').' = '. (int) $order_id);
		$query->where($db->quoteName('return').' = 0');
	
		$db->setQuery($query);
		$result->depart = $db->loadColumn();
	
		//get return addon
		$query = $db->getQuery(true);
		$query->select('addon_id');
		$query->from('#__bookpro_orders_addon');
		$query->where($db->quoteName('order_id').' = '. (int) $order_id);
		$query->where($db->quoteName('return').' = 1');
		$db->setQuery($query);
		$result->return = $db->loadColumn();
	
		return $result;
	}
	function getAddonDetails($order_id){
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$result = new stdClass();
	
		//get depart addon
		$query->select('op.addon_id, op.title, op.price, op.child_price, p.start as date');
		$query->from('#__bookpro_orders_addon op');
		$query->join('inner','#__bookpro_passenger as p ON op.order_id = p.order_id');
		$query->where($db->quoteName('op.order_id').' = '. (int) $order_id);
		$query->where($db->quoteName('op.return').' = 0');
		$query->group('op.addon_id');
	
		$db->setQuery($query);
		$result->depart_addon = $db->loadObjectlist();
	
		//get return addon
		$query = $db->getQuery(true);
		$query->select('op.addon_id, op.title, op.price, op.child_price, p.return_start as date');
		$query->from('#__bookpro_orders_addon op');
		$query->join('inner','#__bookpro_passenger as p ON op.order_id = p.order_id');
		$query->where($db->quoteName('op.order_id').' = '. (int) $order_id);
		$query->where($db->quoteName('op.return').' = 1');
		$query->group('op.addon_id');
		$db->setQuery($query);
		$result->return_addon = $db->loadObjectlist();
	
		return $result;
	}

	function saveOrderstt($id,$value){
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->update($db->quoteName('#__bookpro_orders'));
		$query->set($db->quoteName('order_status').' = '.$db->quote($value));
		$query->where($db->quoteName('id').' = '.$id);
		$db->setQuery($query);

		try{
			$db->execute();
		}
		catch(RuntimeException $e){
			$this->setError($e->getMessage());
			return false;
		}
	}
	
	
	
}
?>