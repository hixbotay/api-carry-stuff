<?php

/**
 * @package 	Bookpro
 * @author 		Ngo Van Quan
 * @link 		http://joombooking.com
 * @copyright 	Copyright (C) 2011 - 2012 Ngo Van Quan
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: coupon.php 14 2012-06-26 12:42:05Z quannv $
 **/

defined('_JEXEC') or die('Restricted access');


//import needed JoomLIB helpers


class BookProModelCoupon extends JModelAdmin
{
   
	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_bookpro.coupon', 'coupon', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form))
		{
			return false;
		}
	
		return $form;
	}
	
	public function getTable($type = 'Coupon', $prefix = 'Table', $config=array()){
		JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_bookpro/tables');
		return JTable::getInstance($type, $prefix, $config);
	}
	
	/**
	 * (non-PHPdoc)
	 * 
	 * @see JModelForm::loadFormData()
	 */
	protected function loadFormData() {
		$data = JFactory::getApplication ()->getUserState ( 'com_bookpro.edit.coupon.data', array () );
		if (empty ( $data ))
			$data = $this->getItem ();
		return $data;
	}
	protected function populateState()
	{
		$table = $this->getTable();
		$key = $table->getKeyName();
	
		// Get the pk of the record from the request.
		 
		$pk = JFactory::getApplication()->input->getInt($key);
		
		$this->setState($this->getName() . '.id', $pk);
	
		// Load the parameters.
		
	}
   
	function getObjectByCode($code)
	{
		$table = $this->getTable();
		$table->load(array('code'=>$code));
		if($table->id){
			if($table->publish_date <= JFactory::getDate()->toSql() && $table->unpublish_date >= JFactory::getDate()->toSql() && $table->remain > 0){
				return $table;
			}
		}
		return false;
	}
	//get data from Name input
       private function getDescription($app){
        	$data = $app->input->post->get('description',array(),'array');
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

	public function save($data)
        {
        	$app = JFactory::getApplication();
        	if ($app->input->get('task') == 'save' || $app->input->get('task') =="apply"){
        		
        		$description = $this->getDescription($app);
        		//var_dump($name); die;
        		//$params = new JObject();
        		//$params->assistance = $data['assistance'];
        		 
        		$data['description'] = empty($description) ? 0 : json_encode($description);
        	}
        	
        	return parent::save($data);
        }
      
}

?>