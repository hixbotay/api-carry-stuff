<?php


/**
 * Support for generating html code
 *
 * @package 	Bookpro
 * @author 		Ngo Van Quan
 * @link 		http://joombooking.com
 * @copyright 	Copyright (C) 2011 - 2012 Ngo Van Quan
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: html.php 82 2012-08-16 15:07:10Z quannv $
 **/
defined('_JEXEC') or die('Restricted access');

class JsHelper
{
	/**
	 * check session by js
	 * @param string $link: back link
	 * @param string $header: header of pop-up
	 * @param string $msg: content of pop-up
	 * @param string $button_text: text of button
	 * @return string
	 */
	static function checkSessionJs($link = NULL,$header = NULL,$msg = NULL, $button_text = NULL){
		$session_timeout = JFactory::getSession()->getExpire()*1000;
		if(is_null($msg)){
			$msg = JText::_('COM_BOOKPRO_SESSION_EXPIRED');
		}
		if(is_null($header)){
			$header = JText::_('JNOTICE');
		}
		if(is_null($button_text)){
			$button_text = JText::_('COM_BOOKPRO_BACK');
		}
		if(is_null($link)){
			$link = JUri::root().'index.php';
		}
		
		echo "<script type='text/javascript'>
			jQuery(document).ready(function($){
				setTimeout(checkSessionExpired, (".$session_timeout."));
				var sessionTimeout = checkSession();
				if(!sessionTimeout)
					alertSessionExpired();
			
			});
			function checkSessionPerTime(){
				var sessionTimeout = checkSession();
				if(!sessionTimeout)
					alertSessionExpired();
			}
			
			function alertSessionExpired(){
				jAlertFocus('".$msg."','".$header."','".JRoute::_($link)."','".$button_text."');
			}
			
			function checkSessionExpired(){
				window.setInterval(checkSessionPerTime, 60000);
			}
		</script>";
		
		}
		
	static function getLoader(){
		echo '<img src="'.JUri::root().'components/com_bookpro/assets/images/loader.gif" style="margin:2px"/>';
	}
	
	/**
	 * Implement toggle button for filter search box
	 * @param string $input_button field of button
	 * @param string $input_box field of search box
	 * @param boolean $show Case show the search box
	 */
	static function advanceSearchBox($input_button, $input_box,$show){
		$doc = JFactory::getDocument();
		$script = 'jQuery(document).ready(function($){
			if('.(int)$show.'){
				$("'.$input_box.'").show();
				$("'.$input_button.'").addClass("btn-primary");
			}
			$("'.$input_button.'").click(function(){
				$("'.$input_button.'").toggleClass("btn-primary");
				$("'.$input_box.'").toggle();
			});
		});';
		$doc->addScriptDeclaration($script);
		return;
	}
			

}

?>