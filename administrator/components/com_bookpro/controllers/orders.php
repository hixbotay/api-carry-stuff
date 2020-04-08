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

class BookproControllerOrders extends JControllerAdmin{
	public function getModel($name = 'Order', $prefix = 'BookproModel', $config =array('ignore_request' => true)){
		$model = parent::getModel($name, $prefix, $config);
		return $model;
	}

		public function saveOrderAjax()
		{
			$input = JFactory::getApplication()->input;
			$pks = $input->post->get('cid', array(), 'array');
			$order = $input->post->get('order', array(), 'array');
			JArrayHelper::toInteger($pks);
			JArrayHelper::toInteger($order);
			$model = $this->getModel();
			$return = $model->saveorder($pks, $order);
			if ($return)
			{
				echo "1";
			}
			JFactory::getApplication()->close();
		}

	function back(){
		$this->setRedirect(JRoute::_('index.php?option=com_bookpro'));
	}
	
	public function filterOrderCurrentDelivery(){
		AImporter::model('orders');
		$model = new BookProModelOrders();
		$state = $model->getState();
		$state->set('filter.cancel_status','0');
		$state->set('filter.accept_status','1');
		$state->set('filter.trip_status','-2');
		$view = $this->getView('Orders', 'html', 'BookProView' );
		$view->setModel($model,true);
		$view->display();
		return;
	}
	
	public function filterCustomer(){
		$input = JFactory::getApplication ()->input;
		$id = $input->getInt('customer_id');
		
		AImporter::model('orders');
		$model = new BookProModelOrders();
		$state = $model->getState();
		$state->set('filter.customer_id',$id);
		$state->set('filter.driver_id',$id);
		$view = $this->getView('Orders', 'html', 'BookProView' );
		$view->setModel($model,true);
		$view->display();
		return;
	}
	
}