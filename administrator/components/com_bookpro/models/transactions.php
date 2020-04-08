
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


class BookProModelTransactions extends JModelList
{

	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
					'id', 'a.id',
					'order_id', 'a.order_id','a.created','created','tx_id','a.tx_id',
					'total', 'a.total',
					'params', 'a.params',
					'success','a.success',					
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
		
		$cancel_status = $this->getUserStateFromRequest($this->context . '.filter.cancel_status', 'filter_cancel_status');
		$this->setState('filter.cancel_status', $cancel_status);
		
		$who_cancel = $this->getUserStateFromRequest($this->context . '.filter.order_who_cancelled', 'filter_order_who_cancelled');
		$this->setState('filter.order_who_cancelled', $who_cancel);
		
		$type = $this->getUserStateFromRequest ( $this->context . '.filter.type','filter_type', null, 'int' );
		$this->setState ( 'filter.type', $type );
			
		// List state information.
		parent::populateState('a.created', 'DESC');
	}

	protected function getListQuery()
	{
		// Create a new query object.
		$db    = $this->getDbo();
		$query = $db->getQuery(true);
		//$state = 1;
		// Select the required fields from the table.
		$query->select('a.*');

		$query->from($db->quoteName('#__bookpro_transaction') . ' AS a');
		$query->join ( 'LEFT', '#__bookpro_orders AS b ON b.id = a.order_id' );

		//Filter the items over the search string if set.
		$search = $this->getState('filter.search');
		if ( $search !== '' && $search !== null)
		{
			// Escape the search token.
			$search = $db->quote('%' . trim($search). '%');

			// Compile the different search clauses.
			$searches   = array();
			$searches[] = 'a.order_id LIKE ' . $search;
			$searches[] = 'a.tx_id LIKE ' . $search;
			
			$query->where('(' . implode(' OR ', $searches) . ')');
		}
		
		$cancel = $this->getState ( 'filter.cancel_status' );
		if(($cancel !== '' && $cancel !== null)){
			$query->where ( 'b.is_cancelled=' . $cancel );
		}
		
		$who_cancel = $this->getState ( 'filter.order_who_cancelled' );
		if($who_cancel){
			$query->where ( 'b.cancel LIKE '.$db->quote($who_cancel.'%'));
		}
		
		
		$orderCol  = $this->state->get('list.ordering','created');
		$orderDirn  = $this->state->get('list.direction','DESC');
		$query->order($db->escape($orderCol.' '.$orderDirn));

		return $query;
	}
	
 	
	
	

}


?>