<?php
/**
 * @package 	Bookpro
 * @author 		Ngo Van Quan
 * @link 		http://joombooking.com
 * @copyright 	Copyright (C) 2011 - 2012 Ngo Van Quan
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: default.php 47 2012-07-13 09:43:14Z quannv $
 **/

defined('_JEXEC') or die('Restricted access');
AHtml::title('Customer', 'user');
JToolBarHelper::custom('Edit', 'edit', 'edit', 'Edit', false);
$bar = &JToolBar::getInstance('toolbar');
JToolBarHelper::cancel();
echo $this->loadTemplate('customer');
?>

