
<?php

/**
 * @package 	Bookpro
 * @author 		Ngo Van Quan
 * @link 		http://joombooking.com
 * @copyright 	Copyright (C) 2011 - 2012 Ngo Van Quan
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: bookpro.php 80 2012-08-10 09:25:35Z quannv $
 **/

defined('_JEXEC') or die('Restricted access');
//jimport('joomla.application.component.modellist');
//jimport('joomla.application.component.helper');
AImporter::helper('date','orderstatus');
//JTable::addIncludePath(JPATH_ROOT.'/administrator/components/com_bookpro/tables');

class BookproModelpassengers extends JModelList
{
	public function __construct($config = array())
	{

		parent::__construct($config);
	}

	protected function populateState($ordering = null, $direction = null)
	{
		parent::populateState();
		$app = JFactory::getApplication();
		$id = JRequest::getVar('id', 0, '', 'int');
		$this->setState('passengerlist.id', $id);
			
		// Load the filter state.
		$search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
		$this->setState('filter.search', $search);
		
		$order_status = $this->getUserStateFromRequest ( $this->context . '.filter.order_status', 'filter_order_status', null, 'string' );
		$this->setState ( 'filter.order_status', $order_status );
			
		$order_id = $this->getUserStateFromRequest($this->context . '.filter.order_id', 'filter_order_id',null,'int');
		$this->setState('filter.order_id', $order_id);
			
		$user_id = $this->getUserStateFromRequest($this->context . '.filter.user_id', 'filter_user_id',null,'int');
		$this->setState('filter.user_id', $user_id);
			
			
		$route_id = $this->getUserStateFromRequest($this->context . '.filter.route_id', 'filter_route_id',null,'int');
		$this->setState('filter.route_id', $route_id);
			
		$depart_date = $this->getUserStateFromRequest($this->context . '.filter.depart_date', 'filter_depart_date',null);		
		$depart_date = DateHelper::createFromFormatYmd($depart_date);
		$this->setState('filter.depart_date', $depart_date);
		

			
	}

	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id	.= ':'.$this->getState('passengerlist.id');
		return parent::getStoreId($id);
	}

	/**
	 * Method to get a JDatabaseQuery object for retrieving the data set from a database.
	 *
	 * @return	object	A JDatabaseQuery object to retrieve the data set.
	 */
	protected function getListQuery()
	{

		$db		= $this->getDbo();
		$query	= $db->getQuery(true);
		$query->select('a.*,  od.order_number, od.created as depart_date, od.order_status as order_status');
		$query->from('#__bookpro_passenger AS a');
		$query->join('LEFT', '#__bookpro_orders AS od ON a.order_id = od.id');

		// Filter by search in Title
		$search = $this->getState('filter.search');
		if (!empty($search))
		{
			if (stripos($search, 'id:') === 0)
			{
				$query->where('a.id = ' . (int) substr($search, 3));
			}
			else
			{
				$search = $db->quote('%' . $db->escape($search, true) . '%');
				$query->where('(od.order_number LIKE ' . $search . ' OR CONCAT(a.firstname," ",a.lastname) LIKE ' . $search . ' OR a.age LIKE ' . $search . ' OR a.passport LIKE ' . $search . ' )');
			}
		}
		
		OrderStatus::init();
		$order_status = $this->getState ( 'filter.order_status' );
		if (!empty($order_status)) {
			$query->where ( 'od.order_status LIKE ' . $db->quote ( '%' . $order_status . '%' ) );
		}
		

		$order_id = $this->getState('filter.order_id');
		if($order_id){
			$query->where('a.order_id='.$order_id);
		}

		// Filter by Customer
		$user_id = $this->getState('filter.user_id');
		if($user_id){
			$query->where('od.user_id='.$user_id);
		}

		// Filter by search in Flight
		$route_id=$this->getState('filter.route_id');

		// Filter by search in Depart date
		$depart_date = trim($this->getState('filter.depart_date'));
		if ($route_id && $depart_date) {
			$query->where('((a.route_id='.$route_id.' AND DATE_FORMAT(a.start,"%Y-%m-%d")='.$db->quote($depart_date).') OR (a.return_route_id='.$route_id.' AND DATE_FORMAT(a.return_start,"%Y-%m-%d")='.$db->quote($depart_date).'))');
		}
		
		// Add the list ordering clause.
		$orderCol = $this->state->get('list.ordering', 'id');
		$orderDirn = $this->state->get('list.direction', 'DESC');
		$query->order($db->escape($orderCol . ' ' . $orderDirn));
		$query->group('a.id');
// 		debug($query->dump());
		return $query;
	}
	

}