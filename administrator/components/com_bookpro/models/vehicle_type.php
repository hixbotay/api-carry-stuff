<?php

/**
 * @package 	Bookpro
 * @author 		Ngo Van Quan
 * @link 		http://joombooking.com
 * @copyright 	Copyright (C) 2011 - 2012 Ngo Van Quan
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: vehicle_type.php 108 2012-09-04 04:53:31Z quannv $
 **/
defined ( '_JEXEC' ) or die ( 'Restricted access' );
class BookProModelVehicle_type extends JModelAdmin {
	function __construct() {
		parent::__construct ();
		if (! class_exists ( 'TableVehicle_type' )) {
			AImporter::table ( 'vehicle_type' );
		}
		$this->_table = $this->getTable ( 'vehicle_type' );
	}
	public function getForm($data = array(), $loadData = true) {
		$form = $this->loadForm ( 'com_bookpro.vehicle_type', 'vehicle_type', array (
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
	

	
	protected function loadFormData() {
		$data = JFactory::getApplication ()->getUserState ( 'com_bookpro.edit.vehicle_type.data', array () );
		if (empty ( $data )) {
			$data = $this->getItem ();
		}
		return $data;
	}
	
	//get data from Name input
       private function getVehicleName($app){
        	$data = $app->input->post->get('name',array(),'array');
        	//debug($data);    	
        	$result = [];
       		$count = count($data['code']);
        	for ($i = 0; $i < $count; $i++) {
        			if(!empty($data['val'][$i]))
        			$result[$data['code'][$i]]= $data['val'][$i]; 
        		}
        	//var_dump($result);die;
        	return $result;
        	
        }
	//get data from Capacity input
       private function getVehicleCapacity($app){
        	$data = $app->input->post->get('capacity',array(),'array');
        	//debug($data);die;     	
        	$result =[];
       		$count = count($data['code']);
       		
        	for ($i = 0; $i < $count; $i++) {
        			if(!empty($data['val'][$i]))
        			$result[$data['code'][$i]]= $data['val'][$i]; 
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
			AImporter::helper('image');
        	//debug($_FILES);die;
        	$app = JFactory::getApplication();
        	if ($app->input->get('task') == 'save' || $app->input->get('task') =="apply"){
        		
        		$name=$this->getVehicleName($app);
        		$capacity=$this->getVehicleCapacity($app);
        		$price = $this->getPrice($app);
        		$params = new JObject();
        		//$params->assistance = $data['assistance'];
				//debug($name);die;
        		$data['name'] = empty($name) ? 0 : json_encode($name);
        		$data['capacity'] = empty($name) ? 0 : json_encode($capacity);
        		$data['params'] = empty($price) ? 0 : json_encode($price);
        		/* Save icon */
        		$file = $this->upload();
				if(!empty($data['id'])){
					$table = $this->getTable();
					$table->load($data['id']);
					$files = json_decode($table->icon);
				}else{
					$files = new JObject();
				}
        		if($file){
        			// thumbnail sizes
					$sizes = AImage::getSize();
        			//file exist, upload file
        			$path = 'images/vehicle_type/';
        			if(!file_exists(JPATH_ROOT.'/'.$path)){
        				jimport(jimport('joomla.filesystem.folder'));
        				JFolder::create(JPATH_ROOT.'/'.$path);        				
        			}
	        		
	        	 	/* resize and save image */
			      	foreach ($sizes as $key=>$size) {
						if(isset($file[$key])){
							$img_name = urlencode(preg_replace('/\s+/', '',$key.'_'.$file[$key]['name']));
							$path_img_abs = JPATH_ROOT.'/'.$path.$img_name;
							//delete old file if existed
							if(file_exists($path_img_abs)){
								jimport(jimport('joomla.filesystem.file'));
								JFile::delete($path_img_abs);
							}
							$path_img_url = $path.$img_name;
							if(!Aimage::resize($file[$key],$size['w'], $size['h'],$path_img_abs)){
								JFactory::getApplication()->enqueueMessage('Resolution '.$size['w'].'X'.$size['h'].'save Failed','error');
							}
							$files->$key = $path_img_url;
						}
			      		
						
			        	
			      	}
					
        		}
				$data['icon'] = json_encode((object)$files);
				
        	}
        	
        	return parent::save($data);
        }
        
	private function upload(){
		$max_file_size = 1024*200; // 200kb
		$valid_exts = array('jpeg', 'jpg', 'png', 'gif');	
		$icon_size = AImage::getSize();
		$icon_file = false;
		if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_FILES['icon']['name'])) {
			$icon_file = array();
			foreach($icon_size as $k=>$v){
				if($_FILES['icon']['name'][$k]){
					foreach($_FILES['icon'] as $key=>$icon){
						$icon_file[$k][$key] = $icon[$k];
						
					}	
				}
				
			}
					
		//debug($_FILES['icon']);
		//debug($icon_file);die;
			foreach($icon_file as $icon){
				if( $icon['size'] <= $max_file_size ){        		
					// get file extension
					$ext = strtolower(pathinfo($icon['name'], PATHINFO_EXTENSION));
					if (!in_array($ext, $valid_exts)) {
						 JFactory::getApplication()->enqueueMessage('Unsupported file!','error');
						 return false;	
					} 
				  } else{
					JFactory::getApplication()->enqueueMessage('Please upload image smaller than 200KB','error');	
					return false;
				  }
			}
			
			return $icon_file;
		  
		}
		return false;
	}
	
}

?>