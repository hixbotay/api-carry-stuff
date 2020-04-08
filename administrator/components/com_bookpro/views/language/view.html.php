<?php
/**
 * @package 	Bookpro
 * @author 		Ngo Van Quan
 * @link 		http://joombooking.com
 * @copyright 	Copyright (C) 2011 - 2012 Ngo Van Quan
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: view.html.php 26 2012-07-08 16:07:54Z quannv $
 **/

defined ( '_JEXEC' ) or die ();
jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');
class BookProViewLanguage extends JViewLegacy {

	public function display($tpl = null) {
		$app = JFactory::getApplication();
		
		$this->filename = $app->input->get('filename');
		$this->type 	= $app->input->get('type');
		$this->dev 		= $app->input->getString('dev');
		if($this->type=="SITE"){
			$jpath 			= JPATH_SITE;
		}elseif ($this->type=="ADMINISTRATOR"){
			$jpath 			= JPATH_ADMINISTRATOR;
		}
		$folder="language".DS.substr($this->filename, 0,5);
		$this->filedata = JFile::read($jpath .DS.$folder .DS. $this->filename);
		$this->addToolbar();
		parent::display ( $tpl );
	}
	
	protected function addToolbar(){
		JToolBarHelper::apply('language.apply');
		JToolBarHelper::cancel('language.cancel');
		JToolBarHelper::title(JText::_('Edit Language'), 'user.png');
	}
}