<?php

/**
 * @package 	Bookpro
* @author 		Ngo Van Quan
* @link 		http://joombooking.com
* @copyright 	Copyright (C) 2011 - 2012 Ngo Van Quan
* @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
* @version 	$Id: airport.php 66 2012-07-31 23:46:01Z quannv $
**/


defined('_JEXEC') or die;

class BookproModelPrices extends JModelList{
	public function __construct($config=array()){
		if (empty($config['filter_fields'])){
			$config['filter_fields'] = array(
			'code', 'a.code'
			);
		}
		
		parent::__construct($config);
	}
	
	protected function populateState($ordering = null, $direction = null){		
		
		$code = $this->getUserStateFromRequest($this->context . '.filter.code', 'filter_code');
		$this->setState('filter.code', $code);
		
		parent::populateState('a.id','DESC');
	}
	
	protected function getListQuery(){
		
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		
		$query->select('a.*');
		$query->from($db->quoteName('#__bookpro_price').' AS a');
		
		$code = $this->getState('filter.code');
		if(!empty($code)){
			$query->where('(a.code LIKE '.$code.')');
		}
		
		$orderCol = $this->state->get('list.ordering');
		$orderDirn = $this->state->get('list.direction');
		if (empty($orderCol)){
			$orderCol='a.id';
		}
		if (empty($orderDirn)){
			$orderDirn='DESC';
		}
		
		$query->order($db->escape($orderCol.' '.$orderDirn));
		
		return $query;

	}
	
	public function getPriceList(){
		if(!class_exists('MathHelper')){
			require_once JPATH_ADMINISTRATOR.'/components/com_bookpro/helpers/math.php';
		}
		$db = $this->getDbo();
		$query = $db->getQuery(true);		
		$query->select('*');
		$query->from($db->quoteName('#__bookpro_price'));
		$db->setQuery($query);
		$data = $db->loadObjectList();
		$result = new JObject();
		$result->base = MathHelper::filterArrayObject($data, 'code', 'BASE');
		$result->validateend = MathHelper::filterArrayObject($data, 'code', 'VALIDATE_END');
		$result->date = MathHelper::filterArrayObjects($data, 'code', 'DATE');
		$result->week = MathHelper::filterArrayObjects($data, 'code', 'WEEK');
		$result->order = MathHelper::filterArrayObject($data, 'code', 'ORDER');
		return $result;
	}
}
?>