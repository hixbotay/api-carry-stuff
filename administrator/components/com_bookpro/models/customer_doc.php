<?php
/**
 * @package 	Bookpro
 * @author 		Ngo Van Quan
 * @link 		http://joombooking.com
 * @copyright 	Copyright (C) 2011 - 2012 Ngo Van Quan
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: bookpro.php 27 2012-07-08 17:15:11Z quannv $
 **/
defined ('_JEXEC') or die;

class BookproModelCustomer_doc extends JModelAdmin{
	protected $text_prefix = 'COM_BOOKPRO';
	
	public function getTable($type = 'Customer_doc', $prefix = 'Table', $config=array()){
		return JTable::getInstance($type, $prefix, $config);
	}
	
	public function getForm($data = array(), $loadData = true){
		$app = JFactory::getApplication();
		//get data from Form
		$form = $this->loadForm('com_bookpro.customer_doc','customer_doc', array('control'=> 'jform', 'load_data'=>$loadData));
		if (empty($form)){
			return false;
		}
		
		return $form;
	}
	
	protected function loadFormData(){
		$data = JFactory::getApplication()->getUserState('com_bookpro.edit.customer_doc.data', array());
		
		if(empty($data)){
			$data = $this->getItem();
		}
		
		
		
		return $data;
	}
	
	function getItemCustomer_doc($customer_id){
		$user = JFactory::getUser ();
		if($user->id){
			$db		= $this->getDbo();
			$query	= $db->getQuery(true);
			$query->select('a.*');
			$query->from('#__bookpro_customer_doc AS a')->innerJoin('#__bookpro_customer AS b on b.id=a.customer_id' )->where('b.id=' . $customer_id);
			$db->setQuery($query);
			//echo $query->dump();
			return $db->loadObjectList();
			 
		} else {
			return null;
		}
	}
	

	
}