<?php

/**
 * @package 	Bookpro
 * @author 		Ngo Van Quan
 * @link 		http://joombooking.com
 * @copyright 	Copyright (C) 2011 - 2012 Ngo Van Quan
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: customer.php 108 2012-09-04 04:53:31Z quannv $
 **/
defined ( '_JEXEC' ) or die ( 'Restricted access' );
class BookProModelVehicle extends JModelAdmin {
	function __construct() {
		parent::__construct ();
		if (! class_exists ( 'TableVehicle' )) {
			AImporter::table ( 'vehicle' );
		}
		$this->_table = $this->getTable ( 'vehicle' );
	}
	public function getForm($data = array(), $loadData = true) {
		$form = $this->loadForm ( 'com_bookpro.vehicle', 'vehicle', array (
				'control' => 'jform',
				'load_data' => $loadData 
		) );
		
		if (empty ( $form ))
			return false;
		return $form;
	}
	
	public function getItem($pk = null)
	{
		if ($item = parent::getItem($pk))
		{
		//	$item->agent_payment = $item->params['payment'];
		//	$item->order_manager = $item->params['order_manager'];
		}
	
		return $item;
	}
	

	/*
	 * function getIdByUserId() { $user = &JFactory::getUser(); @var $user JUser $customer_id=null; if ($user->id) { $query = 'SELECT `customer`.`id` '; $query .= 'FROM `' . $this->_table->getTableName() . '` AS `customer` '; $query .= 'LEFT JOIN `#__users` AS `user` ON `customer`.`user` = `user`.`id` '; // is active customer $query .= 'WHERE `customer`.`user` = ' . $user->id; // juser is active //$query .= ' AND `user`.`block` = 0'; $this->_db->setQuery($query); $customer_id = (int) $this->_db->loadResult(); } return $customer_id; }
	 */
	protected function loadFormData() {
		$data = JFactory::getApplication ()->getUserState ( 'com_bookpro.edit.vehicle.data', array () );
		if (empty ( $data )) {
			$data = $this->getItem ();
		}
		return $data;
	}
	public function getComplexItem($pk) {
		$db = & JFactory::getDBO ();
		$query = $db->getQuery ( true );
		$query->select ( 'a.*,c.country_name,u.username' );
		$query->from ( '#__bookpro_customer AS a' );
		$query->leftJoin ( '#__bookpro_country AS c ON a.country_id = c.id' );
		$query->leftJoin ( '#__users AS u ON a.user = u.id' );
		$query->where ( 'a.id = ' . ( int ) $pk );
		$db->setQuery ( $query );
		$item = $db->loadObject ();
		return $item;
	}
	
	/*function getItemByUser() {
		$user = JFactory::getUser ();
		if ($user->id) {
			$db = JFactory::getDBO ();
			$query = $db->getQuery ( true );
			$query->select ( 'c.*,u.username' )->from ( '#__bookpro_customer AS c' )->innerJoin ( '#__users AS u ON u.id=c.user' )->where ( 'u.id=' . $user->id );
			$db->setQuery ( $query );
			return $db->loadObject ();
		} else {
			return null;
		}
	}*/
	public function getTable($type = 'Vehicle', $prefix = 'Table', $config = array()) {
		return JTable::getInstance ( $type, $prefix, $config );
	}
	

	//get data from Price input
        private function getPrice($app){
        	$data = $app->input->post->get('params',array(),'array');        	
        	$result  = new JObject();
        	$count = count($data['hard']);
        	for ($i = 0; $i < $count; $i++) {
        			$price = new JObject();
        			$price->prices->hard= $data['hard'][$i];
        			$price->prices->distance = $data['distance'][$i];     			
        			$result = $price;
        		}
        	return $result;
        }
	public function save($data)
        {
        	$app = JFactory::getApplication();
        	if ($app->input->get('task') == 'save' || $app->input->get('task') =="apply"){
        		
        		$price = $this->getPrice($app);

        		//var_dump(json_encode($packages));
        		//var_dump($price);die;
        		$params = new JObject();
        		//$params->assistance = $data['assistance'];
        		 
        		$data['params'] = empty($price) ? 0 : json_encode($price);
        	}
        	
        	return parent::save($data);
        }
	
	
	
}

?>