<?php

/**
 * @package 	Bookpro
 * @author 		Ngo Van Quan
 * @link 		http://joombooking.com
 * @copyright 	Copyright (C) 2011 - 2012 Ngo Van Quan
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: tours.php 21 2012-07-06 04:06:17Z quannv $
 **/

defined('_JEXEC') or die;
jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');
AImporter::helper('xml');

class BookproViewLanguages extends JViewLegacy
{
	protected $items;
	protected $pagination;
	protected $state;
	var $lists;

	/**
	 * (non-PHPdoc)
	 * @see JViewLegacy::display()
	 */
	public function display($tpl = null)
	{
		$input = JFactory::getApplication()->input;
		
		$this->language = $input->getString('language'); 
		$this->addToolbar();
		
		parent::display($tpl);
	}
	
	protected function addToolbar(){
		JToolBarHelper::title(JText::_('Manager Language'), 'user.png');
	}

	
}
