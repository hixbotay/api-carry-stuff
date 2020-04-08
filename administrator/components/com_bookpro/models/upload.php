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

class BookproModelUpload extends JModelAdmin{
	protected $text_prefix = 'COM_BOOKPRO';
	public $_model;
	
	
	
	public function getTable($type = 'upload', $prefix = 'BookproTable', $config=array()){		
		return JTable::getInstance($type, $prefix, $config);
	}
	
	
	//save data from an array to database
// 	public function save($data){
	
// 		$db = JFactory::getDbo();				
// 		$query = $db->getQuery(true);
		
// 		$table = $this->getTable();	
		
// 		//$table->load(array('code' => $data[code]));
		
// 		$save_stt = $table->save($data);
		
// 		if($save_stt)
// 			return true;
// 		return false;
		

// 	}
	
	
	public function getForm($data = array(), $loadData = true){
		$app = JFactory::getApplication();
		//get data from Form
		$form = $this->loadForm('com_bookpro.upload','upload', array('control'=> 'jform', 'load_data'=>$loadData));
		if (empty($form)){
			return false;
		}
		
		return $form;
	}
}