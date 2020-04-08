<?php
/**
 * @package 	Bookpro
 * @author 		Ngo Van Quan
 * @link 		http://joombooking.com
 * @copyright 	Copyright (C) 2011 - 2012 Ngo Van Quan
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: airport.php 66 2012-07-31 23:46:01Z quannv $
 **/
defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );
class DateHelper {	
	
	public static function formatSqlDate($date,$format="DATE_FORMAT_LC3"){
		if($date=='0000-00-00 00:00:00'){
			
			return JText::_("COM_BOOKPRO_NOT_AVAILABLE");
		}else{
			return JHTML::_('date', $date, $format);
		}
	}
	
	
	static function dayofweek(){
		$days = array(
				0 => 'Sun',
				1 => 'Mon',
				2 => 'Tue',
				3 => 'Wed',
				4 => 'Thu',
				5 => 'Fri',
				6 => 'Sat'
				);
		return $days;
	
	}
	public static function formatTime12($value){
		
		return date("g:i a", strtotime($value));
		
	}
	
	//use in frontend where display ajax flight list
	public static function getArrayTabDate($date){
		$app		= JFactory::getApplication();
		$tzoffset = $app->getCfg('offset');
		
		$date_arr=array();
		
		$currendate=new JDate($date,$tzoffset);		
		$nowdate = new JDate('now',$tzoffset);
		if($currendate < $nowdate){
			$currendate = $nowdate;			
		}	
	
//		if($roundtrip){
//			$nowdate->modify('+2 day');
//		}	

		$days = $currendate->diff($nowdate);
		
		$range = $days->days + $days->invert;

		if ($range >=3) {
			$int_start = -3;
			$int_end = 4;
		}

		if ($range < 3 && $range >0) {
			$int_start = -$range;

			$int_end = 7 - $range;

		}

		if ($range == 0) {
			$int_start = 0;
			$int_end = 7;
		}
		
		for ($i = $int_start; $i < $int_end; $i++) {

			$sdate=JFactory::getDate($date,$tzoffset);

			if($i<0) {
				$sdate->sub(new DateInterval('P'.abs($i).'D'));
			}else{
				$sdate->add(new DateInterval('P'.abs($i).'D'));
			}

			$date_arr[]=$sdate;
		}
		
		return $date_arr;
	}
	public static function formatMultiDate($date,$joomla_format="DATE_FORMAT_LC3"){
		$datearr=explode(';', $date);
		$format = JText::_($joomla_format);
		$result=array();
		for ($i = 0; $i < count($datearr); $i++) {
			$dateObj=JFactory::getDate($datearr[$i]);
			$result[]= JHTML::_('date', $dateObj, $format);
		}
		return $result;
	
	}
	static function getDateArray($numberofday,$start){
		for ($i = 0; $i < $numberofday; $i++) {
			$sdate=JFactory::getDate($start);
			$sdate->add(new DateInterval('P'.abs($i).'D'));
			$date_arr[]=$sdate;
		}
		return $date_arr;
	}
	function getOffsetDay($count,$start){
			
		$date = $start + $count*24*60*60;
		return $date;
	}
	public static function  getCountDay($start,$end){
		
		$start = strtotime($start);
		$end = strtotime($end);
		$days_between = ceil(abs($end - $start) / 86400);
		return $days_between;
	}
	public static function dateBeginDay($date, $tzoffset = 0)
	{
		$day = date('Y-m-d',$date);
		
		$date = strtotime($day.' 00:00:00');
		return $date;
	}
	public static function dateBeginWeek($date, $tzoffset = 0)
	{
		$date = strtotime('last Monday',$date);
		
		return $date;
	}
	public static function dateEndWeek($date, $tzoffset = 0)
	{
		$date = strtotime('next Sunday',$date);
		return $date;
	}
	function startMonth($m,$y){
		$date = date('Y-m-d H:i:s',mktime(0,0,0,$m,01,$y));
		return $date;
		
	}
	function endMonth($d,$m,$y){
		$date = date('Y-m-d H:i:s',mktime(23,59,59,$m,$d,$y));
		return $date;
	}
	public static function dateBeginMonth($date, $tzoffset = 0)
	{
		
		
		
		$fromdate = date('01-m-Y 00:00:00',$date);
		
		
		
		//$date = strtotime('first day this month',$date);
		$fromdate = strtotime($fromdate);
		return $fromdate;
	}
	public static function dateEndMonth($date, $tzoffset = 0)
	{
		$todate = date('t-m-Y 23:59:59',$date);
		
		$todate = strtotime($todate);
		return $todate;
	}
	
 /**
     * Convert date into given format with given time zone offset.
     * 
     * @param $date string date to convert
     * @param $format string datetime format
     * @param $tzoffset int time zone offset
     * @return BookProDate
     */
    public static function convertDate($date, $format = '%Y-%m-%d %H:%M:%S', $tzoffset = false)
    {
        static $cache;
        $key = $date . $format . $tzoffset;
        if (! isset($cache[$key])) {
        	if ($tzoffset){
        		$mainframe = JFactory::getApplication();
        		/* @var $mainframe JApplication */
        		$jdate = JFactory::getDate($date, $mainframe->getCfg('config'));
        		/* @var $date JDate */
        		$jdate->setOffset($mainframe->getCfg('config')); 
        	} else {
        		$jdate = JFactory::getDate($date);
        		/* @var $jdate JDate */
        	}
            $output = new BookProDate();
            $output->orig = $date;
            $output->uts = $jdate->toUnix();
            $output->dts = $jdate->toFormat($format, $tzoffset);
            $cache[$key] = $output;
        }
        return $cache[$key];
    }
    public static function dateEndDay($date, $tzoffset = 0)
    {
    	$date = date('Y-m-d',$date);
    	$date = strtotime($date.' 23:59:59');
    	return $date;
    }
    static function formatTime($value){
    	$config=JComponentHelper::getParams('com_bookpro');
    	$value=JFactory::getDate()->format('Y-m-d').' '.$value;
    	return JFactory::getDate($value)->format(trim($config->get('timespace')));
    }
   
    
    	static function getConvertDateFormat($type='P')
	{
		$type = strtoupper($type);
		$configJ=JComponentHelper::getParams('com_bookpro');
		$php_format = $configJ->get('date_format_type','Y-m-d');
		if($type=="P"){
			return $php_format;
		}else{

			// $type is param for PHP and Mooltoos and Javascript
			if($type=="M"){
				$SYMBOLS_MATCHING = array(
				// Day
	        'd' => '%d',
	        'D' => '%D',
	        'j' => '%j',
	        'l' => '%l',
	        'N' => '%N',
	        'S' => '%S',
	        'w' => '%w',
	        'z' => '%z',
				// Week
	        'W' => '%W',
				// Month
	        'F' => '%F',
	        'm' => '%m',
	        'M' => '%M',
	        'n' => '%n',
	        't' => '%t',
				// Year
	        'L' => '%L',
	        'o' => '%o',
	        'Y' => '%Y',
	        'y' => '%y',
				// Time
	        'a' => '%a',
	        'A' => '%A',
	        'B' => '%B',
	        'g' => '%g',
	        'G' => '%G',
	        'h' => '%h',
	        'H' => '%H',
	        'i' => '%i',
	        's' => '%s',
	        'u' => '%u'
	        );
			}elseif($type=="J"){
				$SYMBOLS_MATCHING = array(
				// Day
	        'd' => 'dd',
	        'D' => 'D',
	        'j' => 'd',
	        'l' => 'DD',
	        'N' => '',
	        'S' => '',
	        'w' => '',
	        'z' => 'o',
				// Week
	        'W' => '',
				// Month
	        'F' => 'MM',
	        'm' => 'mm',
	        'M' => 'M',
	        'n' => 'm',
	        't' => '',
				// Year
	        'L' => '',
	        'o' => '',
	        'Y' => 'yy',
	        'y' => 'y',
				// Time
	        'a' => '',
	        'A' => '',
	        'B' => '',
	        'g' => '',
	        'G' => '',
	        'h' => '',
	        'H' => '',
	        'i' => '',
	        's' => '',
	        'u' => ''
	        );
			}
			$jqueryui_format = "";
			$escaping = false;
			for($i = 0; $i < strlen($php_format); $i++)
			{
				$char = $php_format[$i];
				if($char === '\\') // PHP date format escaping character
				{
					$i++;
					if($escaping) $jqueryui_format .= $php_format[$i];
					else $jqueryui_format .= '\'' . $php_format[$i];
					$escaping = true;
				}
				else
				{
					if($escaping) { $jqueryui_format .= "'"; $escaping = false; }
					if(isset($SYMBOLS_MATCHING[$char]))
					$jqueryui_format .= $SYMBOLS_MATCHING[$char];
					else
					$jqueryui_format .= $char;
				}
			}
			return $jqueryui_format;
		}
	}
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $string_date
	 */
	static function createFromFormat($string_date){
		
		if(empty($string_date))
			return '';
		$configJ=JComponentHelper::getParams('com_bookpro');
		$php_format = $configJ->get('date_format_type','Y-m-d');
		$date = DateTime::createFromFormat($php_format, $string_date);
		return  $date;
		
	}
	
	static function createFromFormatYmd($string_date){		
		if(empty($string_date))
			return '';
		$configJ=JComponentHelper::getParams('com_bookpro');
		$php_format = $configJ->get('date_format_type','Y-m-d');
		$date = DateTime::createFromFormat($php_format, $string_date);
		if($date){
			return  $date->format('Y-m-d');
		}
		return false;
		
	
	}
	
	static function formatHours($string_hours){
		$pattern = '/([0-1][0-9]|2[0-3])[:]([0-5][0-9])/';
		if(!preg_match($pattern, $string_hours)){
			return $string_hours;
		}
		$params = JComponentHelper::getParams('com_bookpro');
		$config = $params->get('formatHours','12');
		if($config == 12){
			$hour = explode(':', $string_hours);
			$compare = $hour[0] - 12;
			if($compare > 0){				
				return (int)($hour[0] - 12).':'.$hour[1].' PM';				
			}	
			else if($compare == 0){
				return (int)($hour[0]).':'.$hour[1].' PM';
			}
			else
				return (int)($hour[0]).':'.$hour[1].' AM';
			
		}
		else
			return $string_hours;
	}
	
}