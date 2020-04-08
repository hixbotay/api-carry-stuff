<?php
/**
 * @package 	Bookpro
 * @author 		Ngo Van Quan
 * @link 		http://joombooking.com
 * @copyright 	Copyright (C) 2011 - 2012 Ngo Van Quan
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: order.php 56 2012-07-21 07:53:28Z quannv $
 **/
defined('_JEXEC') or die('Restricted access');


AImporter::helper('bookpro');
AImporter::model('passenger');

class BookProModelOrder extends JModelAdmin
{
	protected $text_prefix = 'COM_BOOKPRO';

	public function getTable($type = 'Orders', $prefix = 'Table', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	function populateState(){
		$table = $this->getTable();
		$key = $table->getKeyName();
		$pk = JFactory::getApplication()->input->getInt($key);
		if ($pk) {
			$this->setState($this->getName() . '.id', $pk);
		}
	}

	public function getForm($data = array(), $loadData = true){
		$app = JFactory::getApplication();
		//get data from Form
		$form = $this->loadForm('com_bookpro.order','order', array('control'=> 'jform', 'load_data'=>$loadData));
		if (empty($form)){
			return false;
		}

		return $form;
	}

	protected function loadFormData(){
		$data = JFactory::getApplication()->getUserState('com_bookpro.edit.order.data', array());
		if(empty($data)){
			$data = $this->getItem();
		}
		return $data;
	}
	
	/*public function getItem($pk = null)
        {
            if ($item = parent::getItem($pk))
            {
               $item->assistance = $item->params['assistance'];
            }

            return $item;
        }*/
	//get data from Packages input
        private function getPackagesInfo($app){
        	$data = $app->input->post->get('packages',array(),'array');        	
        	$result = array();
        	foreach ($data as $d){
        		$result[] = (object)$d;
        	}
        	return $result;
        }
	//get data from Recipient input
        private function getRecipientInfo($app){
        	$data = $app->input->post->get('recipient_info',array(),'array'); 
        	return (object)$data;
        }
	//get data from Location From input
        private function getLocationFrom($app){
        	$data = $app->input->post->get('from',array(),'array');        	
        	$result  = new JObject();
        	$count = count($data['address']);
        	for ($i = 0; $i < $count; $i++) {
        			$from = new JObject();   
        			$from->latitude= $data['latitude'][$i];    			      			
        			$from->longitude= $data['longitude'][$i]; 
        			$from->address= $data['address'][$i];
        			$result = $from;
        		}
        	return $result;
        }
	//get data from Location To input
        private function getLocationTo($app){
        	$data = $app->input->post->get('to',array(),'array');        	
        	$result  = new JObject();
        	$count = count($data['address']);
        	for ($i = 0; $i < $count; $i++) {
        			$to = new JObject();
        			$to->latitude= $data['latitude'][$i];
        			$to->longitude= $data['longitude'][$i];
        			$to->address= $data['address'][$i];     			
        			$result = $to;
        		}
        	return $result;
        }
	public function save($data)
        {
        	$app = JFactory::getApplication();
        	if ($app->input->get('task') == 'save' || $app->input->get('task') =="apply"){
        		
        		$packages = $this->getPackagesInfo($app);
        		$recipient = $this->getRecipientInfo($app);
        		$from= $this->getLocationFrom($app);
        		$to= $this->getLocationTo($app);
        		//var_dump(json_encode($packages));
        		//var_dump($from);die;
        		$params = new JObject();
        		//$params->assistance = $data['assistance'];
        		 
        		$data['recipient_info'] = empty($recipient) ? 0 : json_encode($recipient);
        		$data['from']= empty($from) ? 0 : json_encode($from);
        		$data['to']= empty($to) ? 0 : json_encode($to);
        		$data['packages']= empty($packages) ? 0 : json_encode($packages);
        	}
        	
        	return parent::save($data);
        }
		
	public function getTransactions($order_id){
		$this->_db->setQuery("Select * from #__bookpro_transaction where order_id=$order_id");
		return $this->_db->loadObjectList();
	}
}
?>