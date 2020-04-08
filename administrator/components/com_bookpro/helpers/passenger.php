<?php



/**

 * @package 	Bookpro

 * @author 		Ngo Van Quan

 * @link 		http://joombooking.com

 * @copyright 	Copyright (C) 2011 - 2012 Ngo Van Quan

 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html

 * @version 	$Id: bookpro.php 80 2012-08-10 09:25:35Z quannv $

 **/



defined('_JEXEC') or die('Restricted access');



class PassengertHelper

{
	static function getListPassengerForBustrip($bustrips,$date){
		AImporter::model('orderinfos','passengers');
		$passengers = array();
		
		foreach ($bustrips as $bustrip){
				 
			$orderinfosModel = new BookProModelOrderInfos();
			$objs = $orderinfosModel->getListsByObj($bustrip->id,$date);
			
			if (count($objs)) {
				foreach ($objs as $obj){
					$passModel = new BookProModelPassengers();
					$lists = array('order_id'=>$obj->order_id);
					$passModel->init($lists);
					$pass = $passModel->getData();
					if (count($pass)) {
						foreach ($pass as $pas){
							$pas->boarding_location = $bustrip->fromName;
							$passengers[] = $pas;
						}
					}
					//$pass= $passModel->getObjectOrderId($obj->order_id);
					
				}
			}	 
		}
		return $passengers;
	}
	 static function getCPassengers($type){
		// Load passenger
		echo "Ty". $type;
		die();
		$db = JFactory::getDbo ();
		$query = $db->getQuery ( true );
		$query->select ( 'count' );
		$query->from ( '#__bookpro_passenger' );
		$query->where ( 'order_id = ' . $order->id.' AND group_id='.$type);
		$db->setQuery ( ( string ) $query );
		return $db->loadResult();
	}
}


?>

