<?php

/**
 * @package 	Bookpro
* @author 		Ngo Van Quan
* @link 		http://joombooking.com
* @copyright 	Copyright (C) 2011 - 2012 Ngo Van Quan
* @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
* @version 	$Id: airport.php 66 2012-07-31 23:46:01Z quannv $
**/


defined('_JEXEC') or die('Restricted access');

require_once( dirname(__FILE__).'/calendar.php' );
class FlightCalendar extends PN_CalendarCell{
	public $adult=null;
	public $child=null;
	public $rate_price = null;
	public $flight_id = 0;
	public function __construct($y, $m, $d, $in_current_month = true) {
		parent::__construct($y, $m, $d, $in_current_month );
		
		$flight_id=JFactory::getApplication()->input->get('flight_id');
		$this->flight_id = $flight_id;
		if ($flight_id) {
				
			$db=JFactory::getDbo();
			$query=$db->getQuery(true);
			$query->select('count(id)')->from('#__bookpro_roomrate')->where(array('room_id='.$flight_id,'DATE_FORMAT(date,"%Y-%m-%d")='.$db->quote($dd->format('Y-m-d'))));
			$db->setQuery($query);
			$count=$db->loadResult();
				
		}
		//echo $query;
		//var_dump($roomrate);
		$this->rate_price = false;
		if($count){
			$this->rate_price = true;
			//$this->adult=$roomrate->adult;
			//$this->child=$roomrate->child;
			//$this->id = $roomrate->id;
		}
	}
	function setCount(){
		
	}
	
}
?>