<?php
/**
 * @package 	Bookpro
 * @author 		Ngo Van Quan
 * @link 		http://joombooking.com
 * @copyright 	Copyright (C) 2011 - 2012 Ngo Van Quan
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id$
 **/

jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');
AImporter::helper('math');
class BookProControllerLanguage extends JControllerLegacy{

	function apply()
	{
		

		$input 		= JFactory::$application->input;
		$filename 	= $input->getString('filename');
		$type 		= $input->getString('type');
		$filedata 	= $_POST['filedata'];
		$dev 		= $input->getString('dev');
		$folder="language".DS.substr($filename, 0,5);
		$urldev='';
		if($dev){
			$urldev = "&dev=".$dev;
		}
		if($type=="SITE"){
			$jpath 			= JPATH_SITE;
		}elseif ($type=="ADMINISTRATOR"){
			$jpath 			= JPATH_ADMINISTRATOR;
		}

		$openfiledata 		= JFile::read($jpath .DS. $folder .DS. $filename);
		$checkfiledata 	= JFile::write($jpath .DS. $folder .DS. $filename, $filedata);
		JFile::write($jpath .DS. $folder .DS. $filename.'.backup', $filedata);
		
		$app=JFactory::getApplication();
		if($checkfiledata){
			$app->enqueueMessage('Saved successful');
		}else{
			$app->enqueueMessage('Saved failed', 'error');
		}
			
		$this->setRedirect('index.php?option=com_bookpro&view=language&filename='.$filename.'&type='.$type.$urldev);

	}

	function cancel()
	{
		$this->setRedirect('index.php?option=com_bookpro&view=languages');
	}
	
	public function addLanguage(){
		AImporter::helper('xml');
		$file_filter = JPATH_ADMINISTRATOR.'/components/com_bookpro/data/language_filter.xml';
		$filter = JFactory::getXML($file_filter);
		$main_lang = 'en-GB';
		$clone_lang = JFactory::getApplication()->input->getString('lang');
		$folder			= JPATH_SITE .DS."language".DS;
		$folderadmin	= JPATH_ADMINISTRATOR .DS."language".DS;
		//admin language file
		$adminArray = XmlHelper::getAttribute($filter->admin->file, 'name');
		//site language file
		$siteArray	= XmlHelper::getAttribute($filter->site->file, 'name');
		
		
		$en_admin_list = preg_filter('/^/', $main_lang.'.', $adminArray);
		$en_site_list = preg_filter('/^/', $main_lang.'.', $siteArray);
		
		$clone_admin_list = preg_filter('/^/', $clone_lang.'.', $adminArray);
		$clone_site_list = preg_filter('/^/', $clone_lang.'.', $siteArray);
		
		foreach ($adminArray as $value) {
			JFile::copy($folderadmin.DS.$main_lang.DS.$main_lang.'.'.$value, $folderadmin.DS.$clone_lang.DS.$clone_lang.'.'.$value);
		}
		foreach ($siteArray as $value) {
			JFile::copy($folder.DS.$main_lang.DS.$main_lang.'.'.$value, $folder.DS.$clone_lang.DS.$clone_lang.'.'.$value);
		}
		$this->setRedirect('index.php?option=com_bookpro&view=languages&layout=list&language='.$clone_lang,'Add language success');
		return;
		
		$site_list_file = JFolder::files($folder);
		$itemsSite = MathHelper::filterArray($site_list_file, $siteArray);
		
		
		
		$admin_list_file = JFolder::files($folderadmin);
		$itemsAdmin = MathHelper::filterArray($admin_list_file, $adminArray);
	}

}