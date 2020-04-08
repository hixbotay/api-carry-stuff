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

class BookProModelPackage extends JModelAdmin
{
	protected $text_prefix = 'COM_BOOKPRO';

	public function getTable($type = 'Packages', $prefix = 'Table', $config = array())
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
		$form = $this->loadForm('com_bookpro.package','package', array('control'=> 'jform', 'load_data'=>$loadData));
		if (empty($form)){
			return false;
		}

		return $form;
	}

	protected function loadFormData(){
		$data = JFactory::getApplication()->getUserState('com_bookpro.edit.package.data', array());
		if(empty($data)){
			$data = $this->getItem();
		}
		return $data;
	}
	//get data from Name input
       private function getPackageName($app){
        	$data = $app->input->post->get('name',array(),'array');
        	//debug($data);
        	$result = [];
       		$count = count($data['code']);
        	for ($i = 0; $i < $count; $i++) {
				$result[$data['code'][$i]]= $data['val'][$i]; 
			}
        	return $result;
        	
        }

	public function save($data)
        {
        	$app = JFactory::getApplication();
        	if ($app->input->get('task') == 'save' || $app->input->get('task') =="apply"){
        		
        		$name = $this->getPackageName($app);
        		//var_dump($name); die;
        		//$params = new JObject();
        		//$params->assistance = $data['assistance'];
        		 
        		$data['name'] = empty($name) ? 0 : json_encode($name);
        	}
        	
        	return parent::save($data);
        }
}
?>