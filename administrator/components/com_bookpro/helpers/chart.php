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


class ChartHelper{
	
	
	private static function getBaseInfo($id = false, $typeChart = false, $option = false){
		$data = new stdClass();
		
		if($id){
			$data->id = $id;
		}
		if($typeChart){
			$data->typeChart= $typeChart;
		}
		else{
			$data->typeChart = 'LineChart';
		}		
		$data->optionChart = $option;	
				
		return $data;		
	}
	
	//get customer id by user
	private static function getCustomerId(){
		$user =& JFactory::getUser();
		$user_id = (int)$user->id;
		$customer = JFactory::getDbo();
		$cquery = $customer->getQuery(true);
		$cquery->select('id,user');
		$cquery->from($customer->quoteName('#__bookpro_customer'));
		$cquery->where('user = '.$user_id);
		$customer->setQuery($cquery);
		$cObject = $customer->loadObjectlist();
	
		if($cObject){
			$user_id = (int)$cObject[0]->id;
		}
		//prevent if no customer match the user
		else{
			return false;
		}
	
		return $user_id;
	
	}
	
	/*
	 * get revenue of month
	 */
	private static function getRevenueData($range, $fromDate = false, $toDate = false){
		
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('sum(total) as total, DATE_FORMAT(created_time,"%Y-%m-%d") as created_time');
		$query->from($db->quoteName('#__bookpro_orders'));
		$query->where('is_paid =1');
		
		//Check is backend or frontend
		if(JFactory::getApplication()->isAdmin()){
			$isBackend = true;
		}
		else{
			$isBackend = false;
		}
		//filter by user
		if(!$isBackend){
			$user_id = self::getCustomerId();
			if ($user_id){
				$query->where('user_id = '.$user_id);
			}
			else{
				return false;
			}
		}		
		
		//Filter result with date
		$dStart = new JDate('now');
		switch (strtolower($range)){
			case 'lastyear':
				$dStart->modify ( '-1 year' );				
				break;
			case 'lastmonth':
				$dStart->modify ( '-1 month' );				
				break;
			default:
				$dStart->modify ( '-1 month' );
				break;
		
		}
		if ($fromDate && $toDate){
			
			$query->where(' created_time >= '.$db->quote(JFactory::getDate($fromDate)->toSql()));
			$query->where(' created_time < '.$db->quote(JFactory::getDate($toDate.' 23:59:59')->toSql()));
			
		}
		else{
			$dStart = $dStart->toSql();
			$query->where('created_time >= '.$db->quote($dStart));
			
		}
		$query->group('DATE_FORMAT(created_time,"%Y-%m-%d")');
		
		$db->setQuery($query);
		$data = $db->loadObjectList();
		foreach($data as &$item){
			if($item->total < 0){
				$item->total = 0;
			}
		}
		
		return $data;
	}
	
	
	
	//process data with year
	private static function lastYear($data){
		
		foreach($data as $i=>$item){
			$day = new JDate($item->created_time);
			$data[$i]->created_time = $day->format('Y-M');
		}
		
		$dStart = new JDate('now');
		$dStart->modify ( '-1 year' );
		
		$newData = array();
		for($i = 0; $i<12; $i++){
			$dStart->modify('+1 month');
			$newData[$i]['date'] = $dStart->format('Y-M');
			$newData[$i]['total']=0;
			
		}
		
		
		foreach($data as $row) {
			foreach($newData as $i=>$value){
				if ($value['date'] == $row->created_time)
					$newData[$i]['total'] += $row->total;
			}
		}
		
		return $newData;
	}
	
	//process data with month
	private static function lastMonth($data){
		
		foreach($data as $item){
			$day = new JDate($item->created_time);
			$item->created_time = $day->format('M-d');
		}
		
		$dStart = new JDate('now');
		$now = new JDate('now');
		$dStart->modify ( '-1 month' );
		
		$newData = array();
		while($dStart != $now){
			$dStart->modify('+1 day');
			$total = 0;
			$date = $dStart->format('M-d');			
			$newData[] = array('date'=>$date,'total'=>$total);
		}
	
		foreach($data as $row) {
			foreach($newData as $i=>$value){
				if ($value['date'] == $row->created_time)
					$newData[$i]['total'] += $row->total;
			}
		}
		
		return $newData;
	}
	
	private static function specificDate($data, $fromDate, $toDate){
		AImporter::helper('date');
		$from = strtotime($fromDate)/86400;
		$to = strtotime($toDate)/86400;// 86400 = (60*60*24)s = 1 day;
		$range = intval($to-$from);
		if($range <= 0){
			JFactory::getApplication()->enqueueMessage(JText::_('COM_BOOKPRO_FROM_DATE_GREATER_THAN_TO_DATE'), 'error');
			return false;
		}
		if($range > 60){
			JFactory::getApplication()->enqueueMessage(JText::_('COM_BOOKPRO_TIME_IS_TOO_LONG'), 'error');
			return false;
		}
		
		foreach($data as $item){
			$day = new JDate($item->created_time);
			$item->created_time = $day->format('Y-m-d');
		}
		
		$dStart = new JDate($fromDate);
		$dTo = new JDate($toDate);
		$dTo->modify('+1 day');
		$newData = array();		
		$i = 0;
		while($dStart != $dTo){
			$newData[$i]['date'] = $dStart->format('Y-m-d');
			$newData[$i]['total']=0;
			$i++;
			$dStart->modify('+1 day');
		}
		
		foreach($data as $row) {
			foreach($newData as $i=>$value){
				if ($value['date'] == $row->created_time)
					$newData[$i]['total'] += $row->total;
			}
		}
		
		return $newData;
		
		
		
	}
	/*
	 * drawing revenue chart
	 */
	/**
	 * Get standard data to chart
	 * @param string $range: range of date
	 * @param string $fromDate
	 * @param string $toDate
	 * @param string $id: id of chart (use when add a lot of charts to a view)
	 * @param string $typeChart: type of chart (Line,colume,pie,...)
	 * @param string $option: option of chart
	 
	 * @return standard data
	 */
	static function getRevenueChart($range, $fromDate = false, $toDate = false, $id = false, $typeChart = false, $option = false){
		$baseInfo = self::getBaseInfo($id, $typeChart, $option);	
		$preData = self::getRevenueData($range,$fromDate,$toDate);
		
		if(!$fromDate || !$toDate || $fromDate == '' || $toDate == ''){
			switch (strtolower($range)){
				case 'lastyear':
					$newData = self::lastYear($preData);
					break;
				case 'lastmonth':
					$newData = self::lastMonth($preData);
					break;
				default:
					$newData = self::lastMonth($preData);
					break;						
			}			
		}
		else{
			$newData = self::specificDate($preData, $fromDate, $toDate);
		}        
         //write data
		$total_array = count($newData);
        $chart = '';
        $chart = "['".JText::_('COM_BOOKPRO_DATE')."','".JText::_('COM_BOOKPRO_ORDER_TOTAL')." ".JComponentHelper::getParams('com_bookpro')->get('currency_symbol')."']";
               
        for($i = 0;$i<$total_array;$i++){
        	$chart .= ",['".$newData[$i]['date']."',".$newData[$i]['total']."]";
        }
        $data = new stdClass();
        $data->data = $chart;
        $data->id = $baseInfo->id;
        $data->type = $baseInfo->typeChart;
        $data->option = $baseInfo->optionChart;
        
        
        
        return $data;
		
	}
	
	
	static function passengerChart(){
	
	}
	
	static function passengerPerFlightChart(){
	
	}
	
	static function revenuePerAgentChart(){
	
	}

	
	
}
