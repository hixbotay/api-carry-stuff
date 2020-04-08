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

class BookproControllerUpdOrders extends JControllerAdmin{
	public function getModel($name = 'Order', $prefix = 'BookproModel', $config =array('ignore_request' => true)){
		$model = parent::getModel($name, $prefix, $config);
		return $model;
	}

	public function saveOrderAjax()
	{

		$input = JFactory::getApplication()->input;
		$pks = $input->post->get('cid', array(), 'array');
		$order = $input->post->get('order',array(), 'array');

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
	
}