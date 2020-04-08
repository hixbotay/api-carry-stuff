<?php
/**
 * @package 	Bookpro
 * @author 		Ngo Van Quan
 * @link 		http://joombooking.com
 * @copyright 	Copyright (C) 2011 - 2012 Ngo Van Quan
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: customer.php 23 2012-07-08 02:20:56Z quannv $
 **/

defined('_JEXEC') or die('Restricted access');
jimport('joomla.filesystem.file');

class BookProControllerCustomer_doc extends JControllerForm
{
	function delete(){
		$id = JRequest::getInt('id',0);
		$customer_id = JRequest::getInt('customer_id',0);
		$url = JRequest::getString('url');
		
		$file_name = explode("documents", $url);
		$file_name=$file_name[1];
		
		$model = $this->getModel('Customer_doc','BookproModel');
		$model->id = $id;
		$item = $model->getItem($id);
		JFile::delete(JPATH_ADMINISTRATOR.DS.$item->url);
		$model->delete($id);
		$this->setRedirect(JRoute::_('index.php?option=com_bookpro&view=customer&layout=edit&id='.$customer_id.'Itemid='.JRequest::getVar('Itemid'), false));
		$this->setMessage(JText::_('COM_BOOKPRO_N_ITEMS_DELETED'), 'message');
	}
	
	public function uploadDoc(){
			
			$mainframe = JFactory::getApplication();
			$input=$mainframe->input;
			$userfile = $_FILES['fileUpload'];	
			$filePath = explode('.',$userfile["name"]);
			$length = count($filePath);
			$layout = $this->input->get('layout');
			
			$customer_id = $input->get('customer_id');
			
			// Make sure that file uploads are enabled in php
			if (! ( bool ) ini_get ( 'file_uploads' )) {
				$this->setRedirect(JRoute::_('index.php?option=com_bookpro&view=customer&layout='.$layout.'&id='.$customer_id, false),JText::_('UPLOAD_FILE_IS_DISABLE_IN_PHP'),'error');
				return;
			}
				
			// If there is no uploaded file, we have a problem...
			if (! is_array ( $userfile )) {
				
				$this->setRedirect(JRoute::_('index.php?option=com_bookpro&view=customer&layout='.$layout.'&id='.$customer_id, false),JText::_('NO_FILE_IS_UPLOAD'),'error');				
				return;
			}
				
			// Check if there was a problem uploading the file.
			if ($userfile ['error'] || $userfile ['size'] < 1) {
				$this->setRedirect(JRoute::_('index.php?option=com_bookpro&view=customer&layout='.$layout.'&id='.$customer_id, false),JText::_('COM_JBTRACKING_MSG_INSTALL_WARN_INSTALLUPLOADERROR'),'error');			
				return;
			}
			$tmp_dest = "documents/". $customer_id .'/'. preg_replace('/[^A-Za-z0-9.\-]/', '', $userfile ['name']);	
			if(file_exists(JPATH_ADMINISTRATOR.DS.$tmp_dest)){
				$this->setRedirect(JRoute::_('index.php?option=com_bookpro&view=customer&layout='.$layout.'&id='.$customer_id, false),JText::_('The file is existed, please rename the file!'),'error');			
				return;
			}		
			$tmp_src = $userfile ['tmp_name'];
		
			if(JFile::upload ( $tmp_src, JPATH_ADMINISTRATOR.DS.$tmp_dest )){
				AImporter::table('customer_doc');
				$db=JFactory::getDbo();
				$post = array();
				$post['url'] = $tmp_dest;
				$post['name'] = $filePath[$length-2];
				$post['customer_id']=$customer_id;
				//	debug($post); die;
				$upload= new TableCustomer_doc($db);
				if($upload->save($post)){
					$this->setMessage(JText::_('COM_JBTRACKING_N_ITEM_ARE_IMPORT'));
				}else{
					$this->setMessage(JText::_('Save error!'),'error');
				}
			}else{
				$this->setMessage(JText::_('Upload error!'),'error');
			}
			$this->setRedirect(JRoute::_('index.php?option=com_bookpro&view=customer&layout='.$layout.'&id='.$customer_id, false));				
			return;
		}
	
  
}

?>