<?php

defined( '_JEXEC' ) or die( 'Restricted access' );
jimport( 'joomla.application.component.view' );
AImporter::helper('bookpro');
class BookproViewRegistrationenterprise extends JViewLegacy
{
	var $document=null;
	function display($tpl = null)
	{
		$this->document = JFactory::getDocument();
 		$user=JFactory::getUser();
// 		if($user->id){
// 			JFactory::getApplication()->enqueueMessage(JText::_('COM_BOOKPRO_REGISTER_LOGIN_ALREADY'));
// 			JFactory::getApplication()->redirect('index.php?option=com_bookpro&view=mypage');
// 			return;
// 		}
// 		$this->document->setTitle(JText::_('COM_BOOKPRO_REGISTER_VIEW'));
		parent::display($tpl);
	}


}

?>
