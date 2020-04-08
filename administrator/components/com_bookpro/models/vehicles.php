
<?php

/**
 * @package 	Bookpro
 * @author 		Ngo Van Quan
 * @link 		http://joombooking.com
 * @copyright 	Copyright (C) 2011 - 2012 Ngo Van Quan
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: vehicles.php 56 2012-07-21 07:53:28Z quannv $
 **/

defined('_JEXEC') or die('Restricted access');


class BookProModelVehicles extends JModelList
{

	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
					'id', 'a.id',
					'name', 'a.name',
					'vehicle_type_id', 'a.vehicle_type_id',
					'driver_id', 'a.driver_id',
					'capacity','a.capacity',
					'default','a.default',
					'current','a.current',
					'plate_number', 'a.plate_number',	
					'name', 'b.name',	
					'title', 'c.name',	
			);
		}

		parent::__construct($config);
	}

	protected function populateState($ordering = null, $direction = null)
	{
		$app = JFactory::getApplication('administrator');

		// Load the filter state.
		$search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
		$this->setState('filter.search', $search);
		
		$type = $this->getUserStateFromRequest ( $this->context . '.filter.type','filter_type', null, 'int' );
		$this->setState ( 'filter.type', $type );
		
		$driver_id = $this->getUserStateFromRequest ( $this->context . '.filter.driver_id','filter_driver_id', null, 'int' );
		$this->setState ( 'filter.driver_id', $driver_id );
			
		// List state information.
		parent::populateState('a.name', 'asc');
	}

	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id .= ':' . $this->getState('filter.search');
		$id .= ':' . $this->getState('filter.state');

		return parent::getStoreId($id);
	}

	protected function getListQuery()
	{
		// Create a new query object.
		$db    = $this->getDbo();
		$query = $db->getQuery(true);
		//$state = 1;
		// Select the required fields from the table.
		$query->select('a.*, b.name AS customer_name,c.name AS vehicle_type_name');

		$query->from($db->quoteName('#__bookpro_vehicle') . ' AS a');
		$query->join ( 'LEFT', '#__bookpro_customer AS b ON b.id = a.driver_id' );
		$query->join ( 'LEFT', '#__bookpro_vehicle_type AS c ON c.id = a.vehicle_type_id' );

		//Filter the items over the search string if set.
		if ($this->getState('filter.search') !== '' && $this->getState('filter.search') !== null)
		{
			// Escape the search token.
			$search = $db->quote('%' . str_replace(' ', '%', $db->escape(trim($this->getState('filter.search')), true) . '%'));

			// Compile the different search clauses.
			$searches   = array();
			$searches[] = 'a.name LIKE ' . $search;
			$searches[] = 'a.capacity LIKE ' . $search;

			// Add the clauses to the query.
			$query->where('(' . implode(' OR ', $searches) . ')');
		}
		
		$driver_id = $this->getState('filter.driver_id');
		if($driver_id){
			$query->where('a.driver_id = '.$driver_id);
		}
		
		$type = $this->getState('filter.type');
		if($type){
			$query->where('a.vehicle_type_id = '.$type);
		}

		$orderCol  = $this->state->get('list.ordering');
		$orderDirn  = $this->state->get('list.direction');
		$query->order($db->escape($orderCol.' '.$orderDirn));
		
		// Add the list ordering clause.
		$query->order($db->escape($this->getState('list.ordering', 'a.name')) . ' ' . $db->escape($this->getState('list.direction', 'ASC')));

		return $query;
	}
	
	public function getTypes(){
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		
		$query->select('a.*');
		$query->from($db->quoteName('#__bookpro_vehicle_type').' AS a');
		$db->setQuery($query);
		return $db->loadObjectList();
	}
	
	public function getItemByIds($ids = null){
		
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		
		$query->select('a.*,type.title as type_title, type.id as type_id');
		$query->from($db->quoteName('#__bookpro_vehicle').' AS a');
		$query->join('left', '#__bookpro_vehicle_type as type ON type.id = a.type');
		if(!empty($ids)){
			if(is_array($ids)){
				$where = implode(',', $ids);
			}
			else{
				$where = $ids;
			}
			$query->where('a.id IN ('.$where.')');
		}
		
		$db->setQuery($query);
		return $db->loadObjectList();
	}
	
 	
	
	

}


?>