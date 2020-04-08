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
AImporter::model('vehicle');

class BookProControllerVehicle extends JControllerForm
{
	/*
	 * old method in Joomla 2.5
	 */
	var $_model;

	function __construct($config = array())
	{
		parent::__construct($config);
		$this->_model = $this->getModel('vehicle');
	}
	public function changecurrent(){
		$input = JFactory::getApplication ()->input;
		$vehicle_id = $input->getInt('vehicle_id');
		//debug($vehicle_id);die;
		//$driver_id = $input->getInt('driver_id');
		JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_bookpro/tables');
  		$vehicle = JTable::getInstance('vehicle', 'table');
  		$vehicle->load(array('id'=> $vehicle_id));
		$customer_id=$vehicle->driver_id;	
		$check = BookProHelper::setCurrentVehicle($vehicle_id,$customer_id,$vehicle->current);
		if($check){
			JFactory::getApplication ()->enqueueMessage ( 'Update successful' );
		}else{
			JFactory::getApplication ()->enqueueMessage ( 'Update error','error' );
		}
		
		$this->setRedirect ( 'index.php?option=com_bookpro&view=vehicles' );
		return;
		}

}

?>