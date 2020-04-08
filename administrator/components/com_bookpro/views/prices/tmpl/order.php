<?php

/**
 * @package 	Bookpro
 * @author 		Ngo Van Quan
 * @link 		http://joombooking.com
 * @copyright 	Copyright (C) 2011 - 2012 Ngo Van Quan
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: bookpro.php 80 2012-08-10 09:25:35Z quannv $
 **/

defined('_JEXEC') or die;

BookProHelper::setSubmenu(1);
$order = BookProHelper::get('order_type');
$params = json_decode($this->order->params);
?>
<div class="span10" id="j-main-container">
<form action="<?php echo JRoute::_('index.php?option=com_bookpro'); ?>" method="post" id="adminForm" name="adminForm">
		<?php echo JHtml::_('bootstrap.startTabSet', 'myTab',array('active'=>'tab1'));?> 
		<?php foreach ($order as $key=>$val){?>
			<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'tab'.$key, JText::_('COM_BOOKPRO_ORDERS_'.strtoupper($val))); ?> 
				<div class="span12">
					<div class="form-horizontal">				
						<div class="control-group">
			                <div class="control-label" style="width:200px"><label class="hasTooltip" title="" data-original-title="<strong>Default Price</strong>">
							<?php echo JText::_('COM_BOOKPRO_HARD_PRICE')?></label></div>
			                <div class="controls"><input type="text" name="data[order][<?php echo $val?>][hard]" value="<?php echo $params->$val->hard?>" /></div>
						</div>
						<div class="control-group">
			                <div class="control-label" style="width:200px"><label class="hasTooltip" title="" data-original-title="<strong>Distance (km) </strong>">
							<?php echo JText::_('COM_BOOKPRO_DISTANCE_PRICE')?>(Km)</label></div>
			                <div class="controls"><input type="text" name="data[order][<?php echo $val?>][distance]" value="<?php echo $params->$val->distance?>" /></div>
						</div>
						</div>
				</div>
			<?php echo JHtml::_('bootstrap.endTab');?>	
		<?php }?>
    	<?php echo JHtml::_('bootstrap.endTabSet');?>

					
	
		<input type="hidden" name="option" value="com_bookpro" />		
		<input type="hidden" name="task" value="" /> 
		<input type="hidden" name="type" value="order" />
		<?php echo JHtml::_('form.token'); ?>
	
</form>
</div>

