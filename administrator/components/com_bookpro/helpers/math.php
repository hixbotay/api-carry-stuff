<?php
/**
 * @package 	Bookpro
 * @author 		Ngo Van Quan
 * @link 		http://joombooking.com
 * @copyright 	Copyright (C) 2011 - 2012 Ngo Van Quan
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: airport.php 66 2012-07-31 23:46:01Z quannv $
 **/


// Check to ensure this file is included in Joomla!
defined ( '_JEXEC' ) or die ();
class MathHelper {
	static function getSumOfArray($array,$column){		
		$result = array_reduce($array, function($i, $obj) use($column){
			return $i += $obj->$column;
		});
		return $result;
	}
	
	static function filterArrayObject($array,$column,$value){
		if(empty($array)){
			return false;
		}
		$result = array_filter($array, function ($e) use ($column,$value){
			return $e->$column == $value;
		});
		return reset($result);
	}
	static function filterArrayObjects($array,$column,$value){
		if(empty($array)){
			return false;
		}
		$result = array_filter($array, function ($e) use ($column,$value){
			return $e->$column == $value;
		});
		return $result;
	}
	
	/**
	 * Get item of an array with filter by an array
	 * @param array $arrayList: List array to filter
	 * @param array $arrayFilter: filter list
	 */
	static function filterArray($arrayList,$arrayFilter){
		$result = array();
		foreach ($arrayList as $item){
			if(in_array($item,$arrayFilter)){
				$result[] = $item;
			}
		}
		return $result;
	}
	
	
	function haversineDistance($latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo, $earthRadius = 6378)
	{
//		echo $latitudeFrom.' '.$longitudeFrom.' '.$latitudeTo.' '.$longitudeTo;
	  // convert from degrees to radians
	  $latFrom = deg2rad($latitudeFrom);
	  $lonFrom = deg2rad($longitudeFrom);
	  $latTo = deg2rad($latitudeTo);
	  $lonTo = deg2rad($longitudeTo);
	
	  $latDelta = $latTo - $latFrom;
	  $lonDelta = $lonTo - $lonFrom;
	
	  $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
	    cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));
	  return $angle * $earthRadius;
	}
	
	static function getClosestDriver($vehicle_type_id = null,$location,$distance = 50, $offset = 0,$limit = 0){
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$current_time = JFactory::getDate()->toUnix() - JComponentHelper::getParams('com_bookpro')->get('timeout_online',5);
		$distance_sql="( 6378 * acos( cos( radians('%s') ) * cos( radians( s.lat ) ) * cos( radians( s.lng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( s.lat ) ) ) ) AS distance";
		$query->select('s.lat, s.lng, s.data,'.sprintf($distance_sql,$location->latitude,$location->longitude,$location->latitude).' , 
						d.company_name,d.name,d.mobile,d.id');
		$query->from('#__bookpro_session as s')
			->innerJoin('#__bookpro_customer as d ON d.id = s.userid')
			->where('s.free = 1');
			//->where('s.time > '.$current_time);
		if($vehicle_type_id){
			$query->where('s.data LIKE '.$db->quote('%current_type":'.$vehicle_type_id.',%'));
		}
			
		if(!empty($distance)){
			$query->having('distance < '.(int)$distance);
			$query->order('distance ASC');
		}
		//echo $query;
		$db->setQuery($query,$offset,$limit);		
		//echo $query->dump();
		return $db->loadObjectList();
		
	}
	
	static function googleGetClosest($vehicle_type_id,$location, $distance = 50, $offset = 0 ,$limit = 0){
		$t = microtime(true);
		$data = MathHelper::getClosestDriver($vehicle_type_id,$location, $distance, $offset, $limit);
		//var_dump($data);die;
		$driver_list = array();
		$ggkey = JComponentHelper::getParams('com_bookpro')->get('google_key');
		foreach ($data as $d){
			$params = json_decode($d->data);
			if(!isset($params->candidate)){			
				$driver_list[$d->id]= self::googleDistance($d->lat, $d->lng, $location->latitude, $location->longitude, $ggkey, $d->distance);
				//stop get driverlist if timeout over 10s
				if((microtime(true)-$t) > 10){
					break;
				}
					
			}			
		}
		asort($driver_list);
		$result = array();
		foreach($driver_list as $k=>$v){
			$result[]=$k;
		}
		return $result;
	}
	
	static function googleDistance($from_lat, $from_lng, $to_lat, $to_lng, $ggkey, $default){
		$url = "https://maps.googleapis.com/maps/api/distancematrix/json?origins=".$from_lat.",".$from_lng."&destinations=".$to_lat.",".$to_lng."&mode=driving&language=en-GB&key=".$ggkey;	
		
		$distance = json_decode(file_get_contents($url));
		if(isset($distance->rows[0]->elements[0]->distance->text)){			
			return (float)$distance->rows[0]->elements[0]->distance->text;
		}
		return $default;
	}

}

?>