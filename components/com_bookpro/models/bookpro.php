<?php
/**
 * @package 	Bookpro
 * @author 		Ngo Van Quan
 * @link 		http://joombooking.com
 * @copyright 	Copyright (C) 2011 - 2012 Ngo Van Quan
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: bookpro.php  23-06-2012 23:33:14
 **/
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla modelitem library
jimport('joomla.application.component.modelitem');

/**
 * BookPro Model
 */
class BookProModelBookPro extends JModelItem
{
	/**
	 * @var string msg
	 */
	protected $msg;

	public function __construct($config=array()){
		parent::__construct($config);

	}

	
	public function getFlightTitle($from,$to){
	
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select('title')->from('#__bookpro_dest')->where('id='.$from);
		$query->union('SELECT title FROM #__bookpro_dest WHERE id='.$to);
		$db->setQuery($query);
		$result = $db->loadColumn();
		$str = $result[0] .'-'. $result[1];
		return $str;
	
	}
	
	public function getFromFlightDepart($country_id,$select=null){
	
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select('d.id,d.title,d.value,d.code');
		$query->from('#__bookpro_dest AS d');
		$query->innerJoin('#__bookpro_flight AS f on f.desfrom=d.id');
		$query->where('f.state=1');
		if($country_id)
			$query->where->append('d.country_id='.$country_id);
		$query->order('d.lft ASC');
		$query->group('f.desfrom');
		$sql = (string)$query;
		$db->setQuery($sql);
		
		$dest = $db->loadObjectList();
		
		$options = array();
		foreach($dest as $des)
		{
			$options[] = JHtml::_('select.option', $des->id, $des->title.'-'.$des->code);
		}
		return $options;
	
	}

		

	public function getFlightWithDest($desto =array(),$country_code=''){
		if(!is_array($desto) || empty($desto)){
			$desto=array(0);
		}
			
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select("d.*");
		$query->from('#__bookpro_dest AS d');
		$query->where("d.id IN (".implode(",",$desto).")");
		$query->order("d.ordering");
		$sql = (string)$query;


		$db->setQuery($sql);
		$dest = $db->loadObjectList();
			
		$items =array();
		for($i=0; $i < count($dest); $i++){
			$row = $dest[$i];

			$query =$db->getQuery(true);
			$query->select("f.*,`d`.`value` AS fromName,`a`.`title` as `airline`");
			$query->from('#__bookpro_flight AS `f`');
			$query->leftJoin("#__bookpro_dest as `d` on `f`.`desfrom` = `d`.`id` ");
			$query->leftJoin("#__bookpro_airline as `a` on `f`.`airline_id` = `a`.`id` ");
			$query->where("f.desto = ".$row->id . " and d.country_id = " . $country_code);
			$query->order("d.lft");
			$query->group("f.desfrom");
				
			$sql = (string)$query;
			$db->setQuery($sql);

			$flight = $db->loadObjectList();
			//echo count($flight).";";
			foreach ($flight as $of)
			{
				$query =$db->getQuery(true);
				$query->select("MIN(eco_price) as min_price");
				$query->from('#__bookpro_flight as f');
				$query->where("f.desto = ".$of->desto . " and f.desfrom=" .$of->desfrom);
				$sql = (string)$query;
				$db->setQuery($sql);
				$min_price = $db->loadResult();
				$of->min_price=$min_price;//$min_price;

				//echo $of->min_price;
			}
				
			$row->flights =$flight;
			$items[]=$row;
		}
		//print_r($items);
		return $items;
	}

	
/**
 * 
 * @param int $country_id
 */

	public function getListDestinationOption($country_id,$select=null)
	{
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select('d.id,d.value');
		$query->from('#__bookpro_dest AS d');
		$query->innerJoin('#__bookpro_bustrip AS t on t.from=d.id');
		$query->where('d.state=1');
		if($country_id)
			$query->where->append('d.country_id='.$country_id);
		$query->order('d.lft ASC');
		$query->group('t.from');
		$sql = (string)$query;
		$db->setQuery($sql);
		$dest = $db->loadObjectList();
		$options = array();
		foreach($dest as $des)
		{
			$options[] = JHtml::_('select.option', $des->id, $des->value);
		}
			
		return $options;
	}
	
	

	
	public function getToAirportByFrom($from){
		$db = JFactory::getDBO();
		$query =$db->getQuery(true);
		$query->select('f.`desto` AS `key` ,`d2`.`value` AS `value`,`d2`.`lft` AS `t_order`');
		$query->select('CONCAT(`d2`.`title`,'.$this->_db->quote('-').',`d2`.`value`) AS title');
		$query->from('#__bookpro_flight AS f');
		$query->leftJoin('#__bookpro_dest AS d2 ON f.desto =d2.id');
		$query->where(array('f.desfrom='.$from,'f.state=1'));
		$query->group('f.desto');
		$query->order('t_order');
		$sql = (string)$query;
		$db->setQuery($sql);
		$flight = $db->loadObjectList();
		return $flight;

	}
	
	public function getAirportOptionsByCountry($id,$from=true,$revert=false)
	{
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select('d.id,d.value');
		$query->from('#__bookpro_dest AS d');
		$query->order('d.lft ASC');

		if ($revert){
			$query->where("d.country_id  !=  $id ");
		}else {
			$query->where("d.country_id  =  $id ");
		}
		$sql = (string)$query;
		$db->setQuery($sql);
		$dest = $db->loadObjectList();
		if($db->getErrorMsg()){
			$this->setError( $db->getErrorMsg());
		}
		$options = array();
		if($from){
			$title ="From";
		}else{
			$title ="To";
		}
		$options2 = array();
		$options2[] = JHtml::_('select.option',0, $title);

		$options2[] = JHtml::_('select.optgroup','');//open group

		$top =array(19,21,16,23,15,24);//hardcode for top destination
		//TODO: Need to get top destination from config

		if ($dest)
		{
			foreach($dest as $des)
			{
				if(in_array((int)$des->id,$top,true) ){
					$options2[] = JHtml::_('select.option', $des->id, $des->value);
						
				}else{
					$options[] = JHtml::_('select.option', $des->id, $des->value);
				}
			}
		}
		$options2[] = JHtml::_('select.optgroup','');//close group

		$options = array_merge($options2,$options);
		return $options;
	}
	/*
	 Create Select airport with group enable
	*/
	public static function createAiportSelectBox($field,$from=true,$selected){

		$app	= JFactory::getApplication();

		$db	= JFactory::getDbo();

		$query = "SELECT r.* from #__bookpro_region as r";

		$db->setQuery($query);

		$ret= $db->loadObjectList();
		$options = array();
		$attribute='';

		if($from){

			$attribute = 'class="inputbox" style="width:150px;" onchange="changeDesfrom(this)" size="1"';
			$options[] = JHtml:: _('select.option', '', 'From');

		}else {
			$attribute='class="inputbox" style="width:150px;" size="1"';
			$options[] = JHtml:: _('select.option', '', 'To');

		}



		for($i=0; $i < count($ret); $i++){

			$row = $ret[$i];

			$options[] = JHtml::_('select.optgroup', $row->name);

				

			//echo $row->name;

			$query =$db->getQuery(true);

				

			$query->select("d.*");

			$query->from('#__bookpro_dest AS d');

			$query->where("d.region = ".$row->id);

				

			//echo $row->id . ",";

			$query->order("d.lft");

				

			$sql = (string)$query;

			$db->setQuery($sql);

			$airports = $db->loadObjectList();

				

			//$options[] = JHtml::_('select.option', "1", "HN");

				

			for($j=0; $j < count($airports); $j++){

				$airport=$airports[$j];

				$options[] = JHtml::_('select.option', $airport->id, $airport->value);


			}

			$options[] = JHtml::_('select.optgroup','');

		}



		$select=JHtml::_('select.genericlist',$options,$field, $attribute ,'value','text',$selected,false);

		return $select;



	}
	
	
	function getCountrySelectBox($field,$selected){
		$app	= JFactory::getApplication();
		$db	= JFactory::getDbo();
		$query = "SELECT r.* from #__bookpro_region as r";
		$db->setQuery($query);
		$ret= $db->loadObjectList();
			
		$options = array();
		$options[] = JHtml:: _('select.option', '', 'Select Country');
			
		for($i=0; $i < count($ret); $i++){
			$row = $ret[$i];
			$options[] = JHtml::_('select.optgroup', $row->name);
			$query =$db->getQuery(true);
			$query->select("d.*");
			$query->from('#__bookpro_country AS d');
			$query->where("d.region_id = ".(int) $row->id);
			$sql = (string)$query;
			$db->setQuery($sql);
			$countries = $db->loadObjectList();

			for($j=0; $j < count($countries); $j++){
				$country=$countries[$j];
				$options[] = JHtml::_('select.option', $country->id, $country->country_name);
			}
			$options[] = JHtml::_('select.optgroup','');
		}
			
		$select=JHtml::_('select.genericlist',$options,$field,'class="inputbox" size="1"','value','text',$selected,false);

		return $select;

	}


}