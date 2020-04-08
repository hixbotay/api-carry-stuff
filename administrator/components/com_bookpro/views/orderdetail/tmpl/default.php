<?php
/**
 * @package 	Bookpro
 * @author 		Ngo Van Quan
 * @link 		http://joombooking.com
 * @copyright 	Copyright (C) 2011 - 2012 Ngo Van Quan
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: default.php 63 2012-07-29 10:43:08Z quannv $
 **/
defined ( '_JEXEC' ) or die ( 'Restricted access' );

JHtml::_ ( 'bootstrap.tooltip' );


BookProHelper::setSubmenu ( 12 );
//JToolBarHelper::save('timeslot.saveupdate');
JToolBarHelper::title('View Orders');
JToolbarHelper::back ( 'Cancel', 'index.php?option=com_bookpro&view=customers' );
$itemsCount = count($this->orders);

?>
<div class="span10" id='j-main-container'>
<form action="<?php echo JRoute::_('index.php?option=com_bookpro&view=orderdetail');?>" method="post" name="adminForm" id="adminForm">
		
   <div class="btn-group pull-right hidden-phone">
					<label for="limit" class="element-invisible"><?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC');?></label>
					<?php echo $this->pagination->getLimitBox(); ?>
			</div>
	

	<div class="clearfix"> </div>
		<table class="table-striped table">
			<thead>
				<tr>
					<th width="2%">
							<input type="checkbox" class="inputCheckbox" name="toggle" value="" onclick="Joomla.checkAll(this);" />
					</th>
					<th class="title" width="8%">
				        <?php echo JText::_('COM_BOOKPRO_ORDERS_CUSTOMER_NAME'); ?>
					</th>
					<th width="7%"> <?php echo JText::_('COM_BOOKPRO_ORDERS_RECIPIENT_INFO'); ?></th>
					<th width="4%"><?php echo JText::_('COM_BOOKPRO_TRANSPORT_TYPE'); ?></th>
					<th width="1%" class="nowrap hidden-phone">		
				        <?php echo JText::_('COM_BOOKPRO_ORDERS_PAY_STATUS'); ?></th>					
					<th width="1%" class="nowrap hidden-phone">
					<?php echo JText::_('COM_BOOKPRO_ORDERS_ORDER_TYPES'); ?></th>				
					<th width="6%"><?php echo JText::_('COM_BOOKPRO_ORDERS_START_TIME'); ?></th>
					<th width="6%"><?php echo JText::_('COM_BOOKPRO_ORDERS_END_TIME'); ?></th>
					<th width="5%"><?php echo JText::_('COM_BOOKPRO_ORDERS_TRIP_STATUS'); ?></th>
													
				</tr>
			</thead>
			<tfoot>
    			<tr>
    				<td colspan="10">
    				    <?php echo $this->pagination->getListFooter(); ?>
    				</td>
    			</tr>
			</tfoot>
			<tbody>
				<?php if (! is_array($this->orders) || ! $itemsCount) { ?>
					<tr><td colspan="10"><?php echo JText::_('No items found.'); ?></td></tr>
				<?php } else { ?>
				    <?php for ($i = 0; $i < $itemsCount; $i++) { ?>
				    	<?php $subject = &$this->orders[$i]; 
				   		
						?>
				    	<tr class="row<?php echo ($i % 2); ?>" sortable-group-id="1">
				    		<td class="checkboxCell"><?php echo JHTML::_('grid.checkedout', $subject, $i); ?></td>
				    		<td >				                
								<?php echo $this->escape($subject->name); ?></a>			                
				    		</td>
				    		<td class="hidden-phone">
				    			<?php echo $subject->recipient_info; ?></td>
				    		<td class="center hidden-phone"><?php echo $subject->transport_type; ?></td>
				    		<td class="center hidden-phone"><?php if($subject->pay_status == 1){ echo 'Paided';} else{ echo 'Not paid';}; ?></td>
				    		<td class="center hidden-phone"><?php if($subject->is_booked==1){ echo 'Order';} else{ echo 'Booking';}; ?></td>
				    		<td class="hidden-phone"><?php echo JHtml::_('date',$subject->start_time,'d-m-Y H:i');?></td>
				    		<td class="hidden-phone"><?php echo JHtml::_('date',$subject->end_time,'d-m-Y H:i');?></td>
				    		<td class="hidden-phone"><?php if($subject->trip_status==0){ echo 'Not yet started';} elseif($subject->trip_status == 1) {echo 'Running';} else{ echo 'End';}; ?></td>		
				    	</tr>
				    <?php } ?>
				<?php } ?>
			</tbody>
		</table>
		
	
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<?php echo JHtml::_('form.token'); ?>
</form>
</div>