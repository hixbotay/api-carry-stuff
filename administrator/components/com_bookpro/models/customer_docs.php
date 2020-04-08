<?php
/**
 * @package 	Bookpro
 * @author 		Ngo Van Quan
 * @link 		http://joombooking.com
 * @copyright 	Copyright (C) 2011 - 2012 Ngo Van Quan
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: bookpro.php 27 2012-07-08 17:15:11Z quannv $
 **/
defined ('_JEXEC') or die;

class BookproModelCustomer_docs extends JModelList{
	public function __construct($config =array()){
		if (empty($config['filter_fields'])){
			$config['filter_fields'] = array(
				'id', 'a.id',
			);
		}
		
		parent::__construct($config);
	}
	
	protected function populateState($ordering = null, $direction =	null)
	{
			
		$search = $this->getUserStateFromRequest($this->context.'.filter.search','filter_search');
		$this->setState('filter.search', $search);
		
		$customer_id = $this->getUserStateFromRequest($this->context . '.filter.customer_id', 'filter_customer_id');
		$this->setState('filter.customer_id', $customer_id);
		parent::populateState('a.id', 'desc');
		
	}
	
	protected function getListQuery(){
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		
		
		$query->select('a.id, a.url,a.name, a.customer_id, b.name as namecustomer');
		
		$query->from($db->quoteName('#__bookpro_customer_doc') . ' AS a');
		$query->leftJoin('#__bookpro_customer AS b ON b.id=a.customer_id');
		
		$state = $this->getState('filter.state');
		
		// Filter the items over the search string if set.
		if ($this->getState('filter.search') !== '' && $this->getState('filter.search') !== null)
		{
			// Escape the search token.
			$search = $db->quote('%' . str_replace(' ', '%', $db->escape(trim($this->getState('filter.search')), true) . '%'));
		
		
		
			// Add the clauses to the query.
			$query->where('a.name=' .$search);
		}
		
		

		$customer_id = $this->getState ( 'filter.customer_id' );
		//var_dump($type); die();
		if ($customer_id) {
				$query->where ( 'a.customer_id=' . $customer_id );
		}
		
		return $query;
		
	}
}
?>