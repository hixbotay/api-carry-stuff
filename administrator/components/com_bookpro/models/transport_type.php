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
class BookProModelTransport_type extends JModelAdmin {
	function __construct() {
		parent::__construct ();
		if (! class_exists ( 'TableTransport_type' )) {
			AImporter::table ( 'transport_type' );
		}
		$this->_table = $this->getTable ( 'transport_type' );
	}
	public function getForm($data = array(), $loadData = true) {
		$form = $this->loadForm ( 'com_bookpro.transport_type', 'transport_type', array (
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
		$data = JFactory::getApplication ()->getUserState ( 'com_bookpro.edit.transport_type.data', array () );
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
	
	/**
	 * Save customer.
	 *
	 * @param array $data
	 *        	request data
	 * @return customer id if success, false in unsuccess
	 */
	public function publish(&$pks, $value = 1) {
		$user = JFactory::getUser ();
		$table = $this->getTable ();
		$pks = ( array ) $pks;
		
		// Attempt to change the state of the records.
		if (! $table->publish ( $pks, $value, $user->get ( 'id' ) )) {
			$this->setError ( $table->getError () );
			
			return false;
		}
		
		return true;
	}
	function unpublish($cids) {
		return $this->state ( 'state', $cids, 0, 1 );
	}
	//get data from Name input
       private function getTranportName($app){
        	$data = $app->input->post->get('name',array(),'array');
        	//debug($data);die;     	
        	$result = new JObject();
       		$count = count($data['code']);
        	for ($i = 0; $i < $count; $i++) {
        			if(!empty($data['val'][$i]))
        			$result->$data['code'][$i]= $data['val'][$i]; 
        		}
        	//var_dump($result);die;
        	return $result;
        	
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
        		
        		$name=$this->getTranportName($app);
				$price = $this->getPrice($app);
        		//var_dump(json_encode($packages));
        		//var_dump($name);die;
        		$params = new JObject();
        		//$params->assistance = $data['assistance'];
        		$data['name'] = empty($name) ? 0 : json_encode($name);
        		$data['params'] = empty($price) ? 0 : json_encode($price); 
        		//var_dump($data);die;
        	}
        	
        	return parent::save($data);
        }
}

?>