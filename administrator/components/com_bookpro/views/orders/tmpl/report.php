<?php
/**
 * @package 	Bookpro
 * @author 		Ngo Van Quan
 * @link 		http://joombooking.com
 * @copyright 	Copyright (C) 2011 - 2012 Ngo Van Quan
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: default.php 63 2012-07-29 10:43:08Z quannv $
 **/

defined('_JEXEC') or die('Restricted access');



$trip_status = BookProHelper::get_trip_status();
$accept_status = BookProHelper::get_accept_status();
$payment_status = BookProHelper::get_payment_status();
$cancel_status = BookProHelper::get_cancel_status();
?>
		
<table class="table-striped table">
<thead>
	<tr>
		<th ><?php echo JText::_('COM_BOOKPRO_ORDER_NUMBER'); ?></th>	
		<th ><?php echo JText::_('COM_BOOKPRO_ORDERS_CUSTOMER_NAME'); ?></th>		
		<th ><?php echo JText::_('COM_BOOKPRO_DRIVER_NAME'); ?></th>	
		<th ><?php echo JText::_('COM_BOOKPRO_ORDERS_START_TIME'); ?></th>	
		<th ><?php echo JText::_('COM_BOOKPRO_ORDERS_ORDER_TYPES'); ?></th>	
		<th ><?php echo JText::_('COM_BOOKPRO_ORDERS_TRIP_STATUS'); ?></th>	
		<th ><?php echo JText::_('COM_BOOKPRO_ORDERS_IS_ACCEPT'); ?></th>
		<th ><?php echo JText::_('COM_BOOKPRO_ORDERS_IS_CANCELLED'); ?></th>	
		<th ><?php echo JText::_('COM_BOOKPRO_ORDERS_PAY_STATUS'); ?></th>	
		<th><?php echo JText::_('COM_BOOKPRO_ORDER_DISCOUNT'); ?></th>
		<th ><?php echo JText::_('COM_BOOKPRO_ORDERS_TOTAL'); ?></th>			
		<th ><?php echo JText::_('COM_BOOKPRO_ORDERS_CREATED_TIME'); ?></th>		
	</tr>
</thead>
<tbody>
	<?php if (! is_array($this->items)) { ?>
		<tr><td colspan="10"><?php echo JText::_('No items found.'); ?></td></tr>
	<?php } else { ?>
	    <?php foreach ($this->items as $i=>$subject) { 
						if($subject->total < 0){
							$subject->total = 0;
						}
						?>
	    	<tr class="row<?php echo ($i % 2); ?>" sortable-group-id="1">
	    		
	    		<td >
	    			<a target="_blank" href="<?php echo JRoute::_('index.php?option=com_bookpro&task=order.edit&id='.(int) $subject->id); ?>" >
	    			<?php echo sprintf('%010d', $subject->id); ?></a>
	    		</td>
	    		<td >	
	    			<a target="_blank" href="<?php echo JRoute::_('index.php?option=com_bookpro&task=customer.edit&id='.(int) $subject->customer_id); ?>" >			                
					<?php echo $this->escape($subject->customer_name); ?></a>			                
	    		</td>
	    		<td >
	    			<a target="_blank" href="<?php echo JRoute::_('index.php?option=com_bookpro&task=customer.edit&id='.(int) $subject->driver_id); ?>" >	
	    			<?php echo $subject->customer; ?>
	    		</td>	
	    		<td class="hidden-phone"><?php if($subject->start_time==0){echo '';} else{echo $subject->start_time;};?></td>			
	    		<td class="hidden-phone"><?php echo JText::_('COM_BOOKPRO_ORDERTYPE_'.$subject->is_booked) ?></a></td>				    					   
	    						    		
	    		<td class="hidden-phone"><?php echo JText::_('COM_BOOKPRO_TRIP_STATUS_'.strtoupper($trip_status[$subject->trip_status])); ?></td>		
	    		<td class="hidden-phone"><?php echo JText::_('COM_BOOKPRO_ACCEPT_STATUS_'.strtoupper($accept_status[$subject->is_accepted]));?></td>
	    		<td class="hidden-phone"><?php echo JText::_('COM_BOOKPRO_CANCEL_STATUS_'.strtoupper($cancel_status[$subject->is_cancelled])); ?></td>
	    		<td class="hidden-phone"><?php echo JText::_('COM_BOOKPRO_PAYMENT_STATUS_'.strtoupper($payment_status[$subject->is_paid])); ?></td>
				<td class="hidden-phone"><?php echo CurrencyHelper::formatprice($subject->discount);?></td>
	    		<td class="hidden-phone"><?php echo CurrencyHelper::formatprice($subject->total);?></td>				
	    		<td class="hidden-phone"><?php echo $subject->created_time;?></td>
	    	</tr>
	    <?php } ?>
	<?php } ?>
</tbody>
</table>