<?php

/**
 * @package 	Bookpro
 * @author 		Ngo Van Quan
 * @link 		http://joombooking.com
 * @copyright 	Copyright (C) 2011 - 2012 Ngo Van Quan
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: customers.php 56 2012-07-21 07:53:28Z quannv $
 **/

defined('_JEXEC') or die('Restricted access');


class BookProModelRegistrations extends JModelList
{

	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
					'id', 'a.id',
					'name', 'a.name',
					'email', 'a.email',
					'address', 'a.address',
					'a.post_code','post_code',
					'state',
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
// 		debug($search);die;

		$type = $this->getUserStateFromRequest ( $this->context . '.filter.user_type','filter_user_type', null, 'int' );
		$this->setState ( 'filter.user_type', $type );
		$active = $this->getUserStateFromRequest($this->context . '.filter.active', 'filter_active', null, 'int');
		$this->setState('filter.active', $active);
		

		
			
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
		$state = 0;
		// Select the required fields from the table.
		$query->select('a.*');

		$query->from($db->quoteName('#__bookpro_customer') . ' AS a');
		$query->where('a.state=' . $state);
		//$query->join('LEFT', '#__bookpro_country AS country ON country.id = a.country_id');
		//$query->join('LEFT', '#__users AS b ON b.id=a.user');	
		// If the model is set to check item state, add to the query.
		$state = $this->getState('filter.state');
	
		
		if ($this->getState('filter.search') !== '' && $this->getState('filter.search') !== null)
		{
			// Escape the search token.
			$search = $db->quote('%' . str_replace(' ', '%', $db->escape(trim($this->getState('filter.search')), true) . '%'));
		
			// Compile the different search clauses.
			$searches   = array();
			$searches[] = 'a.name LIKE ' . $search;
			$searches[] = 'a.email LIKE ' . $search;
			$searches[] = 'a.phone LIKE ' . $search;
			
			// Add the clauses to the query.
			$query->where('(' . implode(' OR ', $searches) . ')');
		}

		$type = $this->getState ( 'filter.user_type' );
		//var_dump($type); die();
		if ($type) {
			if($type != '')
			$query->where ( 'a.user_type=' . $type );
		}
		

		// Filter the items validation_status if set.
		
		$active = $this->getState('filter.active');
		//dump($validation_status);die;
		if ($active){
			if($active != '')
			$query->where('a.active=' .$active);
		}
		// Add the list ordering clause.
		$query->order($db->escape($this->getState('list.ordering', 'a.name')) . ' ' . $db->escape($this->getState('list.direction', 'ASC')));
		//echo $query->dump(); die;
		return $query;
	}

}

?>