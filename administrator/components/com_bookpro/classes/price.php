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

require_once JPATH_ADMINISTRATOR.'/components/com_bookpro/helpers/parameter.php';

class BookproPrice extends JObject
{	
	public $vehicle;
//	public $transport_type;
	public $vehicle_type;
	public $distance;
	public $total;
	public $price_list;
	public $vat;
	public $date;
	public $validateend;
	public $order_type;
	
	
	public function __construct($array = null){
		$this->price_list = AParameter::getPrice();
		
		if($array){
			foreach ($array as $key=>$val){
				$this->set($key,$val);
			}
		}
	}
	
	public function getTotal(){
		$total = 0;
		/*
		if($this->vehicle){
			$params = json_decode($this->vehicle->params);
			$total += $params->prices->hard+ $params->prices->distance* $this->distance;
		}
		*/
		if($this->vehicle_type){
			$params = json_decode($this->vehicle_type->params);
			$total += $params->prices->hard + $params->prices->distance*$this->distance;
		}
		//echo $total;
		if($this->validateend){
			$total += $this->price_list->validateend->params;
		}
		//echo ' vldend: '.$total;
		//caculate price by (order/booking)
		$order_type = json_decode($this->price_list->order->params);
		if($order_type){
			$type = $this->order_type;		
			$total += $order_type->$type->hard + $order_type->$type->distance * $this->distance;
		}
		//echo ' od type: '.$total;
		//distance
		//echo $this->distance;die;
		$total += $this->price_list->base->params * $this->distance;
		//echo ' base: '.$total;
		$date = $this->date;
		//echo $date->format('D');
		if($this->price_list->week){
			$price = MathHelper::filterArrayObject($this->price_list->week, 'date', strtoupper($date->format('D')));			
		}
		//echo ' week: '.$total;
		if($this->price_list->date){
			$date_price = MathHelper::filterArrayObject($this->price_list->date, 'date', $date->format('d-m'));	
			if($date_price){
				$price = $date_price;
			}	
		}
		//echo ' date: '.$total;
		//var_dump($price);
		if(($price)){
			$total += $total*$price->params/100;
			//echo $total;
		}
		//echo ' rate: '.$total;
		//echo $this->vat;
		$vat = $total * $this->vat/100;
		//echo $vat;
		
		$total += $vat;
		//echo ' vat: '.$total;die;
		$this->total = $total;
		return $this->total;		
	}
	
	public function setDate($date){
		$this->date = JFactory::getDate($date);
	}
	
	public function setVehicleById($vehicle_id){
		if(!empty($vehicle_id)){
			return false;
		}
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);		
		$query->select('*');
		$query->from($db->quoteName('#__bookpro_vehicle'))
			->where('id = '.$vehicle_id);
		$db->setQuery($query);
		$this->vehicle = $db->loadObject();
	}
	public function setVehicleTypeById($vehicle_type_id){
		if(empty($vehicle_type_id)){
			return false;
		}
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);		
		$query->select('*');
		$query->from($db->quoteName('#__bookpro_vehicle_type'))->where('id='.$vehicle_type_id);
		$db->setQuery($query);
		$this->vehicle_type = $db->loadObject();
	}
	public function setTransportTypeById($transport_type_id){
		if(empty($transport_type_id)){
			return false;
		}
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);		
		$query->select('*');
		$query->from($db->quoteName('#__bookpro_transport_type'))->where('id='.$transport_type_id);
		$db->setQuery($query);
		$this->transport_type = $db->loadObject();
	}
	public function setDeliveryCode($code){
		if(empty($code)){
			$this->validateend = 0;
		}else{
			$this->validateend = 1;
		}
		
	}
	
	public function setOrderType($type){		
		require_once JPATH_ADMINISTRATOR.'/components/com_bookpro/helpers/bookpro.php';
		$order_type = BookProHelper::get('order_type');
		$this->order_type = $order_type[$type];
		return;
	}
}
?>
