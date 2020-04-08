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
jimport('joomla.filesystem.file');

class BookproControllerUpload extends JControllerLegacy{
	
		public function getModel($name = 'upload', $prefix = 'BookproModel', $config =array('ignore_request' => true)){
			$model = parent::getModel($name, $prefix, $config);
			return $model;
		}
	
		public function cancel(){
			header('location:index.php?option=com_bookpro');
		}
		
		public function save(){
			JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_bookpro/tables');
			$db=JFactory::getDbo();
			//echo "phuongdinh"; die;
		//	require_once JPATH_COMPONENT.'/helpers/csvfilehelper.php';			
			$app = JFactory::getApplication();
			//$model = $this->getModel();
			$mainframe = JFactory::getApplication();
			$input=$mainframe->input;
				
			//$customer = $input->getArray($_POST);
			$customer_id = JFactory::getApplication()->input->get('customer_id');
			//$A=$customer['id'];
			//debug($customer_id);die;
			//$table = $model->getTable();
			// Get the uploaded file information
			$userfile = JRequest::getVar ( 'fileUpload', null, 'files', 'array' );
			//debug($userfile); die;
			//check file is csv file
			$filePath = explode('.',$userfile["name"]);
			//debug($filePath);die;
			$length = count($filePath);
			/*if (($filePath[$length-1] != 'docx') && ($filePath[$length-1] != 'doc') && $length > 0){
				$app->enqueueMessage(JText::_('UPLOAD_NOT_TRUE_DOC'));
				//JError::raiseWarning('', JText::_('UPLOAD_NOT_TRUE_DOC'));
				return false;
			}*/
			
			// Make sure that file uploads are enabled in php
			if (! ( bool ) ini_get ( 'file_uploads' )) {
				$app->enqueueMessage(JText::_('UPLOAD_FILE_IS_DISABLE_IN_PHP'));
				//JError::raiseWarning ( '', JText::_ ( 'UPLOAD_FILE_IS_DISABLE_IN_PHP' ) );
				return false;
			}
			
			// If there is no uploaded file, we have a problem...
			if (! is_array ( $userfile )) {
				$app->enqueueMessage(JText::_('NO_FILE_IS_UPLOAD'));
			//	JError::raiseWarning ( '', JText::_ ( 'NO_FILE_IS_UPLOAD' ) );
				return false;
			}
			
			// Check if there was a problem uploading the file.
			if ($userfile ['error'] || $userfile ['size'] < 1) {
				$app->enqueueMessage(JText::_('COM_JBTRACKING_MSG_INSTALL_WARN_INSTALLUPLOADERROR'));
				//JError::raiseWarning ( '', JText::_ ( 'COM_JBTRACKING_MSG_INSTALL_WARN_INSTALLUPLOADERROR' ) );
				return false;
			}	

			$config = JFactory::getConfig ();
			
			$tmp_dest = "documents" . DS . $customer_id .DS.  $userfile ['name'];
			debug($tmp_dest);
			//$tmp_dest = $config->get ( 'documents' ) . '/' . $userfile ['name'];
			$tmp_src = $userfile ['tmp_name'];
//			debug($tmp_src); die;
			JFile::upload ( $tmp_src, $tmp_dest );
			//debug($tmp_dest); die;
			$post = array();
			$post['url'] = $tmp_dest;
			$post['name'] = $filePath[$length-2];
			$post['customer_id']=$customer_id;
//	debug($post); die;
		//	$success = '';
			require_once JPATH_COMPONENT_ADMINISTRATOR.'/tables/upload.php';
			$upload= new TableUpload($db);
			$upload->save($post);
		//	debug($upload);die;
			//$app->enqueueMessage(JText::_('COM_JBTRACKING_N_ITEM_ARE_IMPORT'), 'message');
			$this->setRedirect(JRoute::_('index.php?option=com_bookpro&view=customer&layout=registration&id='.$customer_id.'Itemid='.JRequest::getVar('Itemid'), false));
			$this->setMessage(JText::_('COM_JBTRACKING_N_ITEM_ARE_IMPORT'), 'message');
			//$this->setRedirect(JUri::base().'index.php?option=com_jbchatonline&view=postpayment&order_id='.$bookingTable->id.'Itemid='.JRequest::getVar('Itemid'));
					
			return;
		}
		
		public function savecustomer(){
			JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_bookpro/tables');
			$db=JFactory::getDbo();
			$app = JFactory::getApplication();
			$mainframe = JFactory::getApplication();
			$input=$mainframe->input;
		
			$customer_id = JFactory::getApplication()->input->get('customer_id');
			$userfile = JRequest::getVar ( 'fileUpload', null, 'files', 'array' );
			
			//debug($userfile); die;
			//check file is csv file
			$filePath = explode('.',$userfile["name"]);
			//debug($filePath);die;
			$length = count($filePath);
			/*if (($filePath[$length-1] != 'jpg') && ($filePath[$length-1] != 'png') && $length > 0){
				$app->enqueueMessage(JText::_('UPLOAD_NOT_TRUE_DOC'));
				return false;
			}*/
				
			// Make sure that file uploads are enabled in php
			if (! ( bool ) ini_get ( 'file_uploads' )) {
				$app->enqueueMessage(JText::_('UPLOAD_FILE_IS_DISABLE_IN_PHP'));
				return false;
			}
				
			// If there is no uploaded file, we have a problem...
			if (! is_array ( $userfile )) {
				$app->enqueueMessage(JText::_('NO_FILE_IS_UPLOAD'));
				return false;
			}
				
			// Check if there was a problem uploading the file.
			if ($userfile ['error'] || $userfile ['size'] < 1) {
				$app->enqueueMessage(JText::_('COM_JBTRACKING_MSG_INSTALL_WARN_INSTALLUPLOADERROR'));
				return false;
			}
		
			$config = JFactory::getConfig ();
				
			$tmp_dest = "documents" . DS . $customer_id .DS.  $userfile ['name'];
			debug($tmp_dest);
			$tmp_src = $userfile ['tmp_name'];
			//			debug($tmp_src); die;
			JFile::upload ( $tmp_src, $tmp_dest );
			//debug($tmp_dest); die;
			$post = array();
			$post['url'] = $tmp_dest;
			$post['name'] = $filePath[$length-2];
			$post['customer_id']=$customer_id;
			//	debug($post); die;
			require_once JPATH_COMPONENT_ADMINISTRATOR.'/tables/upload.php';
			$upload= new TableUpload($db);
			$upload->save($post);
			//	debug($upload);die;
			$this->setRedirect(JRoute::_('index.php?option=com_bookpro&view=customer&layout=edit&id='.$customer_id.'Itemid='.JRequest::getVar('Itemid'), false));
			$this->setMessage(JText::_('COM_JBTRACKING_N_ITEM_ARE_IMPORT'), 'message');
				
			return;
		}
		
		
}