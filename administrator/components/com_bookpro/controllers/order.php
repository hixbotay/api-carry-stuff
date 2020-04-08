<?php
/**
 * @package 	Bookpro
 * @author 		Ngo Van Quan
 * @link 		http://joombooking.com
 * @copyright 	Copyright (C) 2011 - 2012 Ngo Van Quan
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: order.php 66 2012-07-31 23:46:01Z quannv $
 **/


defined('_JEXEC') or die('Restricted access');

//import needed JoomLIB helpers
AImporter::helper('request', 'controller');
AImporter::model('passenger');

class BookProControllerOrder extends JControllerForm
{
	/*
	 * old method in Joomla 2.5
	 */
	var $_model;

	function __construct($config = array())
	{
		parent::__construct($config);
		$this->_model = $this->getModel('order');
		//$this->_controllerName = CONTROLLER_ORDER;
	}
	
	function cancelOrder(){
		$user = JFactory::getUser();
		AImporter::table('orders');
		$db = JFactory::getDbo();
		$table = new TableOrders($db);
		$table->load($this->input->getInt('id'));
		if($table->id){
			if($table->is_cancelled){
				$table->is_cancelled = 0;
				$table->cancel = "";
				$this->setMessage('Restore order success');
			}else{
				$this->setMessage('Cancelled order success');
				$table->is_cancelled = 1;
				$table->cancel = "admin:{$user->id}";
			}
			$table->store();
		}
		
		$this->setRedirect('index.php?option=com_bookpro&view=order&layout=edit&id='.$table->id);
		return;
	}
	
}

?>