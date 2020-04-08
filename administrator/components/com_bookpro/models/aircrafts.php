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

class BookproModelAircrafts extends JModelList{
	
	public function __construct($config=array()){
		if (empty($config['filter_fields'])){
			$config['filter_fields'] = array(
			'code', 'a.code',
			'title', 'a.title',
			'state', 'a.state',
			'seat', 'a.seat',
			'ordering', 'a.ordering',
			);
		}
		
		parent::__construct($config);
	}
	
	protected function populateState($ordering = null, $direction = null){		
		
		$search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
		$this->setState('filter.search', $search);
		
		parent::populateState('a.title','ASC');
	}
	
	protected function getListQuery(){
		
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		
		$query->select('a.*');
		$query->from($db->quoteName('#__bookpro_aircraft').' AS a');
		
		$search = $this->getState('filter.search');
		if(!empty($search)){
			$search = $db->quote('%'.$db->escape($search, true).'%');
			$query->where('(a.title LIKE '.$search.')');
		}
		
		$orderCol = $this->state->get('list.ordering');
		$orderDirn = $this->state->get('list.direction');
		if (empty($orderCol)){
			$orderCol='a.code';
		}
		if (empty($orderDirn)){
			$orderDirn='ASC';
		}
		
		$query->order($db->escape($orderCol.' '.$orderDirn));
		
		return $query;

	}
}