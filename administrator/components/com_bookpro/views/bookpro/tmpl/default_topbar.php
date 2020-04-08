
<?php 
/**
 * @package 	Bookpro
 * @author 		Ngo Van Quan
 * @link 		http://joombooking.com
 * @copyright 	Copyright (C) 2011 - 2012 Ngo Van Quan
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: bookpro.php 80 2012-08-10 09:25:35Z quannv $
 **/

defined('_JEXEC') or die('Restricted access');
$user_group = BookProHelper::get('customer_type','array');
?>
<div class="row-fluid">
		
	<div class="span4 well well-small" style="color:#FFBB00">
		<h2><?php echo JTExt::_('COM_BOOKPRO_VIEW_CURRENT_ORDER')?></h2>
		<b id="current_delivery_order">...</b>
		<br>
		<a class="btn btn-primary" href="index.php?option=com_bookpro&task=orders.filterordercurrentdelivery"><?php echo JText::_('COM_BOOKPRO_VIEW')?></a>
	</div>
	
	<div class="span4 well well-small" style="color:#FFBB00">
		<h2><?php echo JTExt::_('COM_BOOKPRO_CUSTOMER_ONLINE')?></h2>
		<b id="online_customer">...</b>
		<br>
		<a class="btn btn-primary" href="index.php?option=com_bookpro&task=customers.filteronlinecustomer&user_type=-<?php echo $user_group['driver']?>"><?php echo JText::_('COM_BOOKPRO_VIEW')?></a>
	</div>
	
	<div class="span4 well well-small" style="color:#FFBB00">
		<h2><?php echo JTExt::_('COM_BOOKPRO_DRIVER_ONLINE')?></h2>
		<b id="online_driver">...</b>
		<br>
		<a class="btn btn-primary" href="index.php?option=com_bookpro&task=customers.filteronlinecustomer&user_type=<?php echo $user_group['driver']?>"><?php echo JText::_('COM_BOOKPRO_VIEW')?></a>
	</div>
		
</div>

