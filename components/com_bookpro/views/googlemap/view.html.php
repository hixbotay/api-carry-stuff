<?php
defined('_JEXEC') or die( 'Restricted access' );
jimport( 'joomla.application.component.view' );
class BookProViewGooglemap extends JView
{
	function display($tpl = null)
	{
		$this->config=AFactory::getConfig();
		$this->_prepare();
		parent::display($tpl);
	}
	private function _prepare(){
		$doc=JFactory::getDocument();
		$doc->setTitle($this->obj->title);
		
	}

	
}
