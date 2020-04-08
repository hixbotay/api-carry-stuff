<?php

/**
 * @package 	Bookpro
* @author 		Ngo Van Quan
* @link 		http://joombooking.com
* @copyright 	Copyright (C) 2011 - 2012 Ngo Van Quan
* @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
* @version 	$Id: airport.php 66 2012-07-31 23:46:01Z quannv $
**/


defined('_JEXEC') or die;

class BookproModelAircraft extends JModelAdmin{
	protected $text_prefix = 'COM_BOOKPRO';
	
	public function getTable($type = 'Aircraft', $prefix = 'Table', $config=array()){
		return JTable::getInstance($type, $prefix, $config);
	}
	
	public function getForm($data = array(), $loadData = true){
		$app = JFactory::getApplication();
		//get data from Form
		$form = $this->loadForm('com_bookpro.aircraft','aircraft', array('control'=> 'jform', 'load_data'=>$loadData));
		if (empty($form)){
			return false;
		}
	
		return $form;
	}
	
	protected function loadFormData(){
		$data = JFactory::getApplication()->getUserState('com_bookpro.edit.aircraft.data', array());
	
		if(empty($data)){
			$data = $this->getItem();
		}
	
		return $data;
	}
}