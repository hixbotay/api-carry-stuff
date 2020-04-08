<?php

/**
 * @package 	Bookpro
 * @author 		Ngo Van Quan
 * @link 		http://joombooking.com
 * @copyright 	Copyright (C) 2011 - 2012 Ngo Van Quan
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: orders.php 56 2012-07-21 07:53:28Z quannv $
 **/

defined('_JEXEC') or die('Restricted access');


class BookProModelUpdOrders extends JModelList
{

	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
					'id', 'a.id',
					'type', 'a.type',
					'recipient_name', 'a.recipient_name',
					'total','a.total',
					'pay_status','a.pay_status',
					'order_status','a.order_status',
					'created','a.created', 'name', 'c.name','phone', 'c.phone'
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
		//debug($search); die;
		//$active = $this->getUserStateFromRequest($this->context . '.filter.active', 'filter_active');
		//$this->setState('filter.active', $active);

		//$state = $this->getUserStateFromRequest($this->context . '.filter.state', 'filter_state');
		//$this->setState('filter.state', $state);

// 		$groupId = $this->getUserStateFromRequest($this->context . '.filter.group', 'filter_group_id', null, 'int');
// 		$this->setState('filter.group_id', $groupId);
// 		dump($groupId);die;
		$validationstatus = $this->getUserStateFromRequest($this->context . '.filter.validation_status', 'filter_validation_status', null, 'int');
		$this->setState('filter.validation_status', $validationstatus);

		//$groupId = $this->getUserStateFromRequest($this->context . '.filter.group', 'filter_group_id', null, 'int');
		//$this->setState('filter.group_id', $groupId);
		
	//	dump($validationstatus); die;

		//$groups = json_decode(base64_decode($app->input->get('groups', '', 'BASE64')));

		//if (isset($groups))
		//{
		//	JArrayHelper::toInteger($groups);
		//}

		//$this->setState('filter.groups', $groups);

			
		// List state information.
		parent::populateState('a.customer_id', 'asc');
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
		// Select the required fields from the table.
		$query->select ( ' a.id, a.type, a.recipient_name, a.total, a.pay_status, a.order_status, a.created, c.name, c.phone');

		$query->from($db->quoteName('#__bookpro_orders') . ' AS a');
		$query->leftJoin ( '#__bookpro_customer AS c ON c.id=a.customer_id' );
		//$query->join('LEFT', '#__bookpro_country AS country ON country.id = a.country_id');
		//$query->join('LEFT', '#__users AS b ON b.id=a.user');	
		
		// If the model is set to check item state, add to the query.
		//$state = $this->getState('filter.state');
		
		// Filter the items over the search string if set.
		if ($this->getState('filter.search') !== '' && $this->getState('filter.search') !== null)
		{
			// Escape the search token.
			$search = $db->quote('%' . str_replace(' ', '%', $db->escape(trim($this->getState('filter.search')), true) . '%'));
		
			// Compile the different search clauses.
			$searches   = array();
			$searches[] = 'a.pay_status LIKE ' . $search;
			$searches[] = 'a.order_status LIKE ' . $search;
			$searches[] = 'a.recipient_name LIKE ' . $search;
			$searches[] = 'c.name LIKE ' . $search;
			//$searches[] = 'a.id LIKE '.(int) $search;
			
			
			// Add the clauses to the query.
			$query->where('(' . implode(' OR ', $searches) . ')');
		}
		
		$type = $this->getState ( 'filter.type' );
		//var_dump($type); die();
		if ($type) {
			if($pay_status != 4)
			$query->where ( 'a.type=' . $type );
		}
		

		// Filter the items pay_status if set.
		
		$pay_status = $this->getState('filter.pay_status');
		
		if ($pay_status){
			if($pay_status != '')
			$query->where('a.pay_status=' .$validation_status);
		}
		// Add the list ordering clause.
		$query->order($db->escape($this->getState('list.ordering', 'a.customer_id')) . ' ' . $db->escape($this->getState('list.direction', 'ASC')));

		return $query;
	}
	
	

}

?>