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

class BookproControllerBookPro extends JControllerAdmin{
	
	function getOnlineCustomer(){
		$user_type = $this->input->getString('type');
		$model = $this->getModel('Customers');
		$state = $model->getState();
		$state->set('filter.user_type',$user_type);
		$state->set('filter.timeout',JComponentHelper::getParams('com_bookpro')->get('timeout_online',5));
		
		echo (int)$model->getTotal();
		die;
	}
	
	function getCurrentOrderDelivery(){
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('count(*)')
			->from('#__bookpro_orders')
			->where('is_cancelled = 0 AND is_accepted = 1 AND trip_status != 2');
		$db->setQuery($query);
		echo (int)$db->loadResult();
		die;
	}
}