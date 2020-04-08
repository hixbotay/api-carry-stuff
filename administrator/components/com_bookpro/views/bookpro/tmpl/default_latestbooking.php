<?php
/**
 * @package 	Bookpro
 * @author 		Ngo Van Quan
 * @link 		http://joombooking.com
 * @copyright 	Copyright (C) 2011 - 2012 Ngo Van Quan
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: default.php 66 2012-07-31 23:46:01Z quannv $
 **/
// No direct access to this file <?php echo $this->loadTemplate('config')?
defined('_JEXEC') or die('Restricted Access');
?>
<div class="row-fuild">
	<legend>
		<?php echo JText::_('COM_BOOKPRO_LATEST_BOOKING'); ?>
	</legend>
	
	<table class="table table-striped">
		<thead>
			<tr>
				<th width="2%"><?php echo JText::_('COM_BOOKPRO_ORDER_NUMBER'); ?></th>				
				<th width="4%"><?php echo JText::_('COM_BOOKPRO_ORDERS_ORDER_TYPES'); ?></th>		
				<th width="5%"><?php echo JText::_('COM_BOOKPRO_ORDERS_TRIP_STATUS'); ?></th>
				<th width="2%"><?php echo JText::_('COM_BOOKPRO_ORDERS_IS_ACCEPT'); ?></th>	
				<th width="5%"><?php echo JText::_('COM_BOOKPRO_ORDERS_PAY_STATUS'); ?></th>
				<th width="2%"><?php echo JText::_('COM_BOOKPRO_ORDERS_TOTAL'); ?></th>
				<th width="8%"><?php echo JText::_('COM_BOOKPRO_ORDERS_CREATED_TIME'); ?></th>			
			</tr>
		</thead>
		
		<tbody>
		<?php if (empty($this->items)) {?>
			<tr>
				<td colspan="13" class="emptyListInfo"><?php echo JText::_('COM_BOOKPRO_NO_BOOKING'); ?></td>
			</tr>
			<?php } 
			 else {
				foreach ($this->items as $i=>$item) { 
			?>		
			<tr class="row<?php echo $i % 2; ?>">
				<td >
					<a href="<?php echo JRoute::_('index.php?option=com_bookpro&task=order.edit&id='.(int) $item->id); ?>" >
					<?php echo sprintf('%010d',$item->id) ?>
					</a>
				</td>
				<td class="hidden-phone"><?php if($item->is_booked==0){ echo 'Order';} else{ echo 'Booking';}; ?></td>	
				<td class="hidden-phone"><?php if($item->trip_status==0){ echo 'Not yet started';} elseif($item->trip_status == 1) {echo 'Running';} else{ echo 'End';}; ?></td>			
				<td class="hidden-phone"><?php if($item->is_accepted==0){ echo 'No';} else{ echo 'Yes';}; ?></td>
				<td class="hidden-phone"><?php if($item->is_paid == 1){ echo 'Paided';} else{ echo 'Not paid';}; ?></td>
				<td class="hidden-phone"><?php echo CurrencyHelper::formatprice($item->total);?></td>
				<td class="hidden-phone"><?php echo $item->created_time;?></td>
			</tr>
			<?php } 
				}?>
		</tbody>
	</table>
</div>