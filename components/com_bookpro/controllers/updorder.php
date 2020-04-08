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

class BookProControllerUpdOrder extends JControllerForm
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
	
		protected function allowEdit($data = array(), $key = 'id')
		{
			$recordId = (int) isset($data[$key]) ? $data[$key] : 0;
			$categoryId = 0;
			if ($recordId)
			{
				$categoryId = (int) $this->getModel()->getItem($recordId)->catid;
			}
			if ($categoryId)
			{
				// The category has been set. Check the category permissions.
				return JFactory::getUser()->authorise('core.edit', $this->option . '.category.' . $categoryId);
			}
			else
			{
				// Since there is no asset tracking, revert to the component permissions.
				return parent::allowEdit($data, $key);
			}
	}
}

?>