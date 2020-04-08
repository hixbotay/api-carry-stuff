<?php

/**
 * @package 	Bookpro
 * @author 		Ngo Van Quan
 * @link 		http://joombooking.com
 * @copyright 	Copyright (C) 2011 - 2012 Ngo Van Quan
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: view.html.php 47 2012-07-13 09:43:14Z quannv $
 **/
defined ( '_JEXEC' ) or die ( 'Restricted access' );
AImporter::helper('currency','paystatus','js');
class BookProViewOrders extends JViewLegacy {
	
	protected $items;	
	protected $pagination;	
	protected $state;
	public function display($tpl = null) {
		
		$this->state = $this->get ( 'State' );
		$this->pagination	= $this->get ( 'Pagination' );
		$this->items		= $this->get ( 'items' );
		
		if (count ( $errors = $this->get ( 'Errors' ) )) {
			JError::raiseError ( 500, implode ( "\n", $errors ) );
			
			return false;
		}
		// Include the component HTML helpers.
		JHtml::addIncludePath ( JPATH_COMPONENT . '/helpers/html' );
		$this->vehicletype = BookProHelper::getTypeVehicleSelect ( $this->state->get ( 'filter.vehicle_type' ) );
		$this->ordertype = BookProHelper::getTypeOrderSelect ( $this->state->get ( 'filter.order_type' ) );
		$this->customername=BookProHelper::getCustomerName ( $this->state->get ( 'filter.customer_id' ) );
		$this->drivername=BookProHelper::getDriverName ( $this->state->get ( 'filter.driver_id' ) );
		
		$this->addToolbar ();
		
		parent::display ( $tpl );
	}
	protected function addToolbar() {
		JToolbarHelper::title ( JText::_ ( 'COM_BOOKPRO_ORDERS_MANAGER' ),'list-2');
		JToolbarHelper::editList ( 'order.edit' );
		JToolbarHelper::divider ();
		JToolbarHelper::deleteList ( '', 'orders.delete' );
	}
	
	protected function getAcceptBox(){
		$options = BookProHelper::get('accept_status','arrayObject');
		$option = JHtml:: _('select.option', '', JText::_("COM_BOOKPRO_SELECT_ACCEPT_STATUS"));
		array_unshift($options , $option);
		return JHtml::_('select.genericlist',$options,'filter_accept_status','class="input input-medium"','value','text',$this->state->get('filter.accept_status'));
	}
	
	protected function getTripStatusBox(){
		$options = BookProHelper::get('trip_status','arrayObject');
		$option = JHtml:: _('select.option', '', JText::_("COM_BOOKPRO_SELECT_TRIP_STATUS"));
		array_unshift($options , $option);
		return JHtml::_('select.genericlist',$options,'filter_trip_status','class="input input-medium"','value','text',$this->state->get('filter.trip_status'));
	}
	
	protected function getCancelBox(){
		$options = BookProHelper::get('cancel_status','arrayObject');
		$option = JHtml:: _('select.option', '', JText::_("COM_BOOKPRO_SELECT_CANCEL_STATUS"));
		array_unshift($options , $option);
		return JHtml::_('select.genericlist',$options,'filter_cancel_status','class="input input-medium"','value','text',$this->state->get('filter.cancel_status'));
	}
	
	protected function paymentBox(){
		$options = BookProHelper::get('payment_status','arrayObject');
		$option = JHtml:: _('select.option','', JText::_("COM_BOOKPRO_SELECT_PAYMENT_STATUS"));
		array_unshift($options , $option);
		return JHtml::_('select.genericlist',$options,'filter_payment_status','class="input input-medium"','value','text',$this->state->get('filter.payment_status'));
	}
	
	protected function getWhoCanceled(){
		$options = array();
		$options[] = JHtml:: _('select.option','', JText::_("COM_BOOKPRO_SELECT_WHO_CANCELLED"));
		$options[] = JHtml:: _('select.option','driver', JText::_("COM_BOOKPRO_DRIVER"));
		$options[] = JHtml:: _('select.option','customer', JText::_("COM_BOOKPRO_CUSTOMER"));
		$options[] = JHtml:: _('select.option','admin', JText::_("JADMINISTRATOR"));
		//array_unshift($options , $option);
		return JHtml::_('select.genericlist',$options,'filter_order_who_cancelled','class="input input-medium"','value','text',$this->state->get('filter.order_who_cancelled'));
	}
	
	
}
?>