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
jimport('joomla.filesystem.file');
class BookproControllerPayment extends JControllerLegacy
{
	public function add(){
		$this->setRedirect('index.php?option=com_bookpro&view=payment&layout=gateway&type=gateways&dev=1');
		return;
	}
	public function changegateway(){
		$data = JFile::read(JPATH_ADMINISTRATOR.'/components/com_bookpro/data/payment.json');		
		$this->items = json_decode($data);
		$gateway = $this->items->gateways;
		//$gate=(object)$gateway;
		//var_dump($gateway); die;
		
		if($gateway->enabled !=0)
		{
			//JFile::write(JPATH_ADMINISTRATOR.'/components/com_bookpro/data/payment.json', $gateway->enabled=0);
		}
		else 
		{
			//JFile::write(JPATH_ADMINISTRATOR.'/components/com_bookpro/data/payment.json', $gateway->enabled=1);
		}

		$this->setRedirect ( 'index.php?option=com_bookpro&view=payments' );
		}
	//get data from Method
       private function getPayment($app){
        	$data = $app->input->post->get('name',array(),'array');
        	//debug($data);die;     	
        	$result = new JObject();
       		$count = count($data['code']);
       		$input = JFactory::getApplication()->input;
      		 for ($i = 0; $i < $count; $i++) {
      		 		$result->code=$data['cd'];
        			if(!empty($data['val'][$i])){        			
        			$result->name->$data['code'][$i]= $data['val'][$i];}
        			$result->enabled=$data['enable']; 
        		}
			//debug($result->code);die;    	   	
        	return $result;
        }
        
	

	public function savemethod()
        {
        	$app = JFactory::getApplication();
				
			$method = $this->getPayment($app);	         			        	
            $data = JFile::read(JPATH_ADMINISTRATOR.'/components/com_bookpro/data/payment.json');		
			$data = json_decode($data);
			
        	foreach ($data->methods as &$item){
					if($item->code == $method->code){
						$item = $method;
					}else{
						$item->enabled=0;}
				}
        	//debug($data);die;
        	JFactory::getApplication ()->enqueueMessage ( 'Update successful' );
        	JFile::write(JPATH_ADMINISTRATOR.'/components/com_bookpro/data/payment.json',json_encode($data));
        	$this->setRedirect('index.php?option=com_bookpro&view=payment&type=methods&layout=edit&code='.$method->code);
        	return ;
        }
        
	public function savegateway()
        {
        	$app = JFactory::getApplication();
				
			$gateway = (object)$app->input->post->get('gateway',array(),'array');
            $data = JFile::read(JPATH_ADMINISTRATOR.'/components/com_bookpro/data/payment.json');		
			$data = json_decode($data);
			//check the the gateway is existed
			$add_new = 1;
        	foreach ($data->gateways as &$item){
				if($item->code == $gateway->code){
					$item = $gateway;
					$add_new = 0;
				}else{
					//allow only one gateway in same time
					if($gateway->enabled){
						$item->enabled=0;
					}
				}
				
			}
			//add new gateway if the gateway is not existed
			if($add_new){
				$data->gateways[]=$gateway;
			}
			
			
        	//debug($data);die;
        	JFactory::getApplication ()->enqueueMessage ( 'Update successful' );
        	JFile::write(JPATH_ADMINISTRATOR.'/components/com_bookpro/data/payment.json',json_encode($data));
        	$this->setRedirect('index.php?option=com_bookpro&view=payment&type=gateways&layout=gateway&code='.$gateway->code);
			return;
        }
        
	function directview(){
		$this->setRedirect('index.php?option=com_bookpro&view=payment');
	}
	function cancel(){
		$this->setRedirect('index.php?option=com_bookpro&view=payments');
	}
}