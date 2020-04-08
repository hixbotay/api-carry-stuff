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


class BookProModelOrders extends JModelList
{

	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
					'id', 'a.id',
					'customer_id', 'a.customer_id',
					'driver_id', 'a.driver_id',
					'from', 'a.from',
					'to', 'a.to',
					'recipient_info', 'a.recipient_info',
					'is_paid','a.is_paid',
					'total','a.total',	
					'is_accepted','a.is_accepted',
					'is_cancelled',	'a.is_cancelled',			
					'is_booked','a.is_booked',
					'trip_status','a.trip_status', 
					'is_accepted','a.is_accepted', 			
					'start_time', 'a.start_time',
					'end_time', 'a.end_time',
					'distance', 'a.distance',
					'created_time', 'a.created_time',
					'name','b.name',
					'name','c.name',
			);
		}

		parent::__construct($config);
	}

	protected function populateState($ordering = null, $direction = null)
	{
		$search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
		$this->setState('filter.search', $search);
		
		$accept_status = $this->getUserStateFromRequest($this->context . '.filter.accept_status', 'filter_accept_status');
		$this->setState('filter.accept_status', $accept_status);
		
		$cancel_status = $this->getUserStateFromRequest($this->context . '.filter.cancel_status', 'filter_cancel_status');
		$this->setState('filter.cancel_status', $cancel_status);
		
		$payment_status = $this->getUserStateFromRequest($this->context . '.filter.payment_status', 'filter_payment_status');
		$this->setState('filter.payment_status', $payment_status);
		
		$trip_status = $this->getUserStateFromRequest($this->context . '.filter.trip_status', 'filter_trip_status');
		$this->setState('filter.trip_status', $trip_status);
		
		$vehicle_type = $this->getUserStateFromRequest ( $this->context . '.filter.vehicle_type', 'filter_vehicle_type', null, 'int' );
		$this->setState ( 'filter.vehicle_type', $vehicle_type );
		
		$order_type = $this->getUserStateFromRequest ( $this->context . '.filter.order_type', 'filter_order_type', null, 'string' );
		$this->setState ( 'filter.order_type', $order_type );
		
		$customer_id = $this->getUserStateFromRequest ( $this->context . '.filter.customer_id','filter_customer_id', null, 'int' );
		$this->setState ( 'filter.customer_id', $customer_id );	
		
		$who_cancel = $this->getUserStateFromRequest($this->context . '.filter.order_who_cancelled', 'filter_order_who_cancelled');
		$this->setState('filter.order_who_cancelled', $who_cancel);
			
		
		$driver_id = $this->getUserStateFromRequest ( $this->context . '.filter.driver_id','filter_driver_id', null, 'int' );
		$this->setState ( 'filter.driver_id', $driver_id );	
	//	echo $customer_id; die;
		parent::populateState('a.created_time','DESC');
	}

	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id .= ':' . $this->getState('filter.search');
		$id .= ':' . $this->getState('filter.state');
		$id .= ':' . $this->getState('filter.customer_id');
		$id .= ':' . $this->getState('filter.driver_id');

		return parent::getStoreId($id);
	}

	protected function getListQuery()
	{
		// Create a new query object.
		$db    = $this->getDbo();
		$query = $db->getQuery(true);
		// Select the required fields from the table.
		$query->select ( ' a.*, b.name AS customer_name, d.name AS customer,tp.name AS transport_type');

		$query->from($db->quoteName('#__bookpro_orders') . ' AS a');
		$query->join ( 'LEFT', '#__bookpro_customer AS b ON b.id = a.customer_id' );
		$query->join ( 'LEFT', '#__bookpro_customer AS d ON d.id = a.driver_id' );
		$query->join ( 'LEFT', '#__bookpro_transport_type AS tp ON tp.id = a.transport_type_id' );
		
		$search = (int)$this->getState ( 'filter.search' ); 
		if ($search)
		{			
			$query->where('a.id = '.$search);
		}
		
		//Filter Transport Type
		$type = $this->getState ( 'filter.vehicle_type' ); 
		if ($type) {
			if($type != '')
				$query->where ( 'a.transport_type_id=' . $type );
		}
		
		//filter Order Type
		$ordertype = $this->getState ( 'filter.order_type' );
		if(($ordertype !== '' && $ordertype !== null)){
				$query->where ( 'a.is_booked=' . $ordertype );
		}		
		
		//Filter Customer Name
		$customer = $this->getState ( 'filter.customer_id' ); 
		if ($customer) {
			$query->where ( 'a.customer_id=' . $customer );
		}
		
		//Filter Driver Name
		$driver = $this->getState ( 'filter.driver_id' ); 
		if ($driver) {		
			$query->where ( 'a.driver_id=' . $driver );
		}
		
		$accept = $this->getState ( 'filter.accept_status' );
		if(($accept !== '' && $accept !== null)){
			$query->where ( 'a.is_accepted=' . $accept );
		}
		
		$cancel = $this->getState ( 'filter.cancel_status' );
		if(($cancel !== '' && $cancel !== null)){
			$query->where ( 'a.is_cancelled=' . $cancel );
		}
		
		$payment = $this->getState ( 'filter.payment_status' );
		if(($payment !== '' && $payment !== null)){
			$query->where ( 'a.is_paid=' . $payment );
		}
		
		$trip_status = $this->getState ( 'filter.trip_status' );
		if(($trip_status !== '' && $trip_status !== null)){
			if($trip_status == '-2'){
				$query->where ( 'a.trip_status != 2' );
			}else{
				$query->where ( 'a.trip_status=' . $trip_status );
			}
			
		}
		//who cancel the order
		$who_cancel = $this->getState ( 'filter.order_who_cancelled' );
		if($who_cancel){
			$query->where ( 'a.cancel LIKE '.$db->quote($who_cancel.'%'));
		}
		
		$orderCol = $this->state->get('list.ordering','a.created_time');
		$orderDirn = $this->state->get('list.direction','DESC');
		$query->order($db->escape($orderCol.' '.$orderDirn));	
		
		return $query;
		

	}
}

?>