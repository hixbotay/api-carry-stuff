<?php

/**
 * @package 	Bookpro
 * @author 		Ngo Van Quan
 * @link 		http://joombooking.com
 * @copyright 	Copyright (C) 2011 - 2012 Ngo Van Quan
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: customers.php 56 2012-07-21 07:53:28Z quannv $
 **/
defined ( '_JEXEC' ) or die ( 'Restricted access' );
class BookProModelCustomers extends JModelList {
	public function __construct($config = array()) {
		if (empty ( $config ['filter_fields'] )) {
			$config ['filter_fields'] = array (
					'id',
					'a.id',
					'name',
					'a.name',
					'email',
					'a.email',
					'phone', 'a.phone',
					'company_name', 'a.company_name',
					'user_type', 'a.user_type',
					'registration_date','a.registration_date',
			);
		}
		
		parent::__construct ( $config );
	}
	protected function populateState($ordering = null, $direction = null) {
		$app = JFactory::getApplication ();
		$input=$app->input;
		// Load the filter state.
		$search = $this->getUserStateFromRequest ( $this->context . '.filter.search', 'filter_search' );
		$this->setState ( 'filter.search', $search );
		
		$user_type = $this->getUserStateFromRequest ( $this->context . '.filter.user_type', 'filter_user_type' );
		$this->setState ( 'filter.user_type', $user_type );
		
		$timeout = $this->getUserStateFromRequest ( $this->context . '.filter.timeout', 'filter_timeout' );
		$this->setState ( 'filter.timeout', $timeout );	
			
		$groupId = $this->getUserStateFromRequest ( $this->context . '.filter.group', 'filter_group_id', null, 'int' );
		$this->setState ( 'filter.group_id', $groupId );
		
		$groups = json_decode ( base64_decode ( $app->input->get ( 'groups', '', 'BASE64' ) ) );
		
		if (isset ( $groups )) {
			JArrayHelper::toInteger ( $groups );
		}
		
		$this->setState ( 'filter.groups', $groups );
		
		//filter stats to approve
		$state = JFactory::getApplication()->input->get('state');
		$this->setState ( 'filter.state', $state );
		
		// List state information.
		parent::populateState ( 'a.name', 'asc' );
	}
	protected function getStoreId($id = '') {
		// Compile the store id.
		$id .= ':' . $this->getState ( 'filter.search' );
		$id .= ':' . $this->getState ( 'filter.state' );
		
		return parent::getStoreId ( $id );
	}
	protected function getListQuery() {
		// Create a new query object.
		$db = $this->getDbo ();
		$query = $db->getQuery ( true );
		
		// Select the required fields from the table.
		//$query->select ( $this->getState ( 'list.select', 'a.*, `country`.country_name AS country_name,b.id AS user_id, b.username AS username' ) );
		$query->select ($this->getState('list.select', 'a.*, b.id AS user_id, b.username AS username, b.block'));
		$query->from ( $db->quoteName ( '#__bookpro_customer' ) . ' AS a' );
		$query->join ( 'LEFT', '#__users AS b ON b.id=a.user_id' );		
		
		// Filter the items over the search string if set.
		$search = $this->getState ( 'filter.search' );
		if ($search) {
			// Escape the search token.
			$search = $db->quote ( '%' . trim ($search) . '%' );
			$searches = array ();
			$searches [] = 'a.name LIKE ' . $search;
			$searches [] = 'a.email LIKE ' . $search;
			$searches [] = 'a.phone LIKE' . $search;
			// Add the clauses to the query.
			$query->where ( '(' . implode ( ' OR ', $searches ) . ')' );
		}
		
		//filter User Type
		$user_type = $this->getState ( 'filter.user_type' );
		if ($user_type) {
			if($user_type[0] == '-' || $user_type < 0){
				//find usertype # user_type state
				$query->where ( 'a.user_type !=' . abs((int)$user_type) );
			}else{
				$query->where ( 'a.user_type=' . $user_type );
			}
			
		}
		//filter by timeout of customer
		$timeout = $this->getState ( 'filter.timeout' );
		if ($timeout) {
			$query->innerJoin('left', '#__bookpro_session as s ON s.userid = a.id');
			$current_time = JFactory::getDate()->toUnix();
			//$query->where('s.time > '.($current_time - $timeout));
			//$query->where('s.userid > 0');
		}
		
		$state = $this->getState ( 'filter.state' );
		if (!is_null($state) && $state != '') {
			$query->where ( 'a.state = ' . ( int ) $state );
		}else 
		{
			$state = 1;
			$query->where('a.state =' .$state);
			
		}
		
		$query->group('a.id');
		// Add the list ordering clause.
		$query->order ( $db->escape ( $this->getState ( 'list.ordering', 'a.name' ) ) . ' ' . $db->escape ( $this->getState ( 'list.direction', 'ASC' ) ) );
		//echo $query;
		return $query;
	}
	
	function getItemOrderByCustomer($id){
		$jinput = JFactory::getApplication()->input;
		//debug($id); die;
		if($id){
			$db		= $this->getDbo();
			$query	= $db->getQuery(true);
			$query->select('a.*, u.name');
			$query->from('#__bookpro_orders AS a')->innerJoin('#__bookpro_customer AS u on u.id=a.customer_id' )->where('a.customer_id=' . $id);
			$query->order(id);
			$db->setQuery($query);
			//echo $query->dump();die;
			return $db->loadObjectList();
			//debug($db->loadObject());die;
		} else {
			return null;
		}
	}
}

?>