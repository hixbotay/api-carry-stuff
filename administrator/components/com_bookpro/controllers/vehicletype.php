<?php
/**
 * @package 	Bookpro
 * @author 		Ngo Van Quan
 * @link 		http://joombooking.com
 * @copyright 	Copyright (C) 2011 - 2012 Ngo Van Quan
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: vehicle_types.php 56 2012-07-21 07:53:28Z quannv $
 **/

defined('_JEXEC') or die('Restricted access');

class BookproControllerVehicleType extends JControllerAdmin{
	
	
	public function directView(){
		$this->setRedirect('index.php?option=com_bookpro&view=vehicletype');
		return;
	}

	public function save(){
		AImporter::table('vehicletype');
		$app = JFactory::getApplication();
		$input = $app->input;
		$id = $input->get('id',array(),'array');
		$title = $input->get('title',array(),'array');
		$db = JFactory::getDbo();
		foreach ($title as $i=>$item){
			if(!empty($item)){
				$data = array();
				$data['id'] = $id[$i];
				$data['title'] = $title[$i];
				$table = new TableVehicleType($db);
				$table->save($data);
			}			
		}
		$app->enqueueMessage(JText::_('JLIB_APPLICATION_SAVE_SUCCESS'));
		$this->directView();
	}
	
	public function delete()
	{
		JSession::checkToken() or die(JText::_('JINVALID_TOKEN'));
		$cid = JFactory::getApplication()->input->getInt('cid');

		if (!$cid)
		{
			$this->setMessage('Delete failed!');
		}
		else
		{
			AImporter::table('vehicletype');
			$db = JFactory::getDbo();
			$table = new TableVehicleType($db);
			$table->load($cid);
			$result = $table->delete();
		}
		if($result){
			$this->setMessage('Delete successfull!');
		}else{
			$this->setMessage('Delete failed!');
		}
		
		$this->directView();
	}
	
	
	
}