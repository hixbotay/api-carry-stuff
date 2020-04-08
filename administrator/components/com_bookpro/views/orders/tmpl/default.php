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

JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('formbehavior.chosen', 'select');

$user = JFactory::getUser();	
$listOrder = $this->escape ( $this->state->get ( 'list.ordering' ) );
$listDirn = $this->escape ( $this->state->get ( 'list.direction' ) );
$loggeduser = JFactory::getUser();

$show = ($this->state->get('filter.accept_status') !='' || $this->state->get('filter.cancel_status') != '' || $this->state->get('filter.payment_status') != '' || $this->state->get('filter.trip_status') != '' || $this->state->get('filter.order_who_cancelled') != '');
JsHelper::advanceSearchBox('#advance_search','#advance_search_desc',$show); 

$itemsCount = count($this->items);

BookProHelper::setSubmenu ( 2 );

$trip_status = BookProHelper::get_trip_status();
$accept_status = BookProHelper::get_accept_status();
$payment_status = BookProHelper::get_payment_status();
$cancel_status = BookProHelper::get_cancel_status();
?>
<script type="text/javascript">
	Joomla!.orderTable = function()
	{
		table = document.getElementById("sortTable");
		direction = document.getElementById("directionTable");
		order = table.options[table.selectedIndex].value;
		if (order != '<?php echo $listOrder; ?>')
		{
			dirn = 'asc';
		}
		else
		{
			dirn = direction.options[direction.selectedIndex].value;
		}
	Joomla!.tableOrdering(order, dirn, '');
	}
</script>


  <body>
<div class="span10" id='j-main-container'>
<form action="<?php echo JRoute::_('index.php?option=com_bookpro&view=orders');?>" method="post" name="adminForm" id="adminForm">
		
   <div class="btn-group pull-right hidden-phone">
			<label for="limit" class="element-invisible"><?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC');?></label>
			<?php echo $this->pagination->getLimitBox(); ?>
	</div>
	
	<div id="filter-bar" class="btn-toolbar">
		<div class="filter-search btn-group pull-left">
			<input type="text" name="filter_search" id="filter_search" placeholder="<?php echo JText::_('COM_BOOKPRO_SEARCH_BY_ORDER_NUMBER'); ?>" value="<?php echo $this->state->get('filter.search')?>" />
		</div>
		<div class="btn-group pull-left">
			<button class="btn hasTooltip" type="submit" title="<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>" onclick="this.form.submit();"><i class="icon-search"></i></button>
			<button type="button" class="btn hasTooltip" title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_CLEAR'); ?>" 
						onclick="
								this.form.filter_search.value='';
								this.form.filter_vehicle_type.value=''; 
								this.form.filter_order_type.value='';
								this.form.filter_customer_id.value=''; 
								this.form.filter_driver_id.value='';
								this.form.filter_accept_status.value='';
								this.form.filter_payment_status.value=''; 
								this.form.filter_cancel_status.value=''; 
								this.form.filter_trip_status.value=''; 			
								this.form.filter_order_who_cancelled.value='';
								this.form.submit();">
						<i class="icon-remove"></i>
					</button>
		</div>
	
		<div class="clearfix"> </div>
		<div id="filter-bar" class="btn-group pull-left">	
	          <?php //echo $this->vehicletype ?>
		</div>
		<div id="filter-bar" class="btn-group pull-middle">	
	                   <?php echo $this->ordertype ?>
		</div>
		<div id="filter-bar" class="btn-group pull-middle">	
	            <?php echo $this->customername ?>
		</div>	
		<div id="filter-bar" class="btn-group pull-middle">	
	                   <?php echo $this->drivername ?>
		</div>
		<div class="btn-wrapper">
			<button id="advance_search" title="" class="btn hasTooltip js-stools-btn-filter" type="button" data-original-title="Filter the list items.">
				Advance search <i class="caret"></i>
			</button>
		</div>
		<div class="clearfix"></div>
		
		<div id="advance_search_desc" class="btn-toolbar js-stools-container-filters hidden-phone row-fluid" style="display:none">
			<div class="span12">
				<span><?php echo $this->getAcceptBox() ?></span>		
				<span class="btn-wrapper"><?php echo $this->paymentBox() ?></span>			
				<span class="btn-wrapper"><?php echo $this->getCancelBox() ?></span>
				<span class="btn-wrapper"><?php echo $this->getTripStatusBox() ?></span>	
				<span class="btn-wrapper"><?php echo $this->getWhoCanceled() ?></span>	
			</div>		
							
		</div>	
	</div>
	
	<div class="clearfix"></div>
		
		<table class="table-striped table">
			<thead>
				<tr>
					<th width="2%">
							<input type="checkbox" class="inputCheckbox" name="toggle" value="" onclick="Joomla.checkAll(this);" />
					</th>
					<th ><?php echo JText::_('COM_BOOKPRO_ORDER_NUMBER'); ?></th>	
						
					<th class="title">
				        <?php echo JHTML::_('grid.sort',JText::_('COM_BOOKPRO_ORDERS_CUSTOMER_NAME'), 'customer_name', $listDirn, $listOrder); ?>
					</th>
					<th><?php echo JHTML::_('grid.sort',JText::_('COM_BOOKPRO_DRIVER_NAME'), 'customer', $listDirn, $listOrder); ?></th>
																
					<th ><?php echo JText::_('COM_BOOKPRO_ORDERS_START_TIME'); ?></th>
					<th class="nowrap hidden-phone"><?php echo JText::_('COM_BOOKPRO_ORDERS_ORDER_TYPES'); ?></th>
					<th ><?php //echo JText::_('COM_BOOKPRO_TRANSPORT_TYPE'); ?></th>
					<th ><?php echo JHTML::_('grid.sort',JText::_('COM_BOOKPRO_ORDERS_TRIP_STATUS'), 'trip_status', $listDirn, $listOrder); ?></th>					
					<th ><?php echo JHTML::_('grid.sort',JText::_('COM_BOOKPRO_ORDERS_IS_ACCEPT'), 'is_accepted', $listDirn, $listOrder); ?></th>
					<th><?php echo JHTML::_('grid.sort',JText::_('COM_BOOKPRO_ORDERS_IS_CANCELLED'), 'is_cancelled', $listDirn, $listOrder); ?></th>						
					<th>		
				        <?php echo JHTML::_('grid.sort',JText::_('COM_BOOKPRO_ORDERS_PAY_STATUS'),'is_paid', $listDirn, $listOrder); ?></th>
					<th><?php echo JText::_('COM_BOOKPRO_ORDER_DISCOUNT'); ?></th>
					<th><?php echo JHTML::_('grid.sort',JText::_('COM_BOOKPRO_ORDERS_TOTAL'), 'total', $listDirn, $listOrder); ?></th>
					
					<th><?php echo JHTML::_('grid.sort',JText::_('COM_BOOKPRO_ORDERS_CREATED_TIME'), 'created_time', $listDirn, $listOrder); ?></th>	
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
				<?php if (! is_array($this->items) || ! $itemsCount) { ?>
					<tr><td colspan="10"><?php echo JText::_('No items found.'); ?></td></tr>
				<?php } else { ?>
				    <?php for ($i = 0; $i < $itemsCount; $i++) { ?>
				    	<?php $subject = $this->items[$i]; 
						if($subject->total < 0){
							$subject->total = 0;
						}
						?>
				    	<tr class="row<?php echo ($i % 2); ?>" sortable-group-id="1">
				    		<td class="checkboxCell"><?php echo JHTML::_('grid.checkedout', $subject, $i); ?></td>
				    		<td >
				    			<a href="<?php echo JRoute::_('index.php?option=com_bookpro&task=order.edit&id='.(int) $subject->id); ?>" >
				    			<?php echo sprintf('%010d', $subject->id); ?></a>
				    		</td>
				    		<td >	
				    			<a href="<?php echo JRoute::_('index.php?option=com_bookpro&task=customer.edit&id='.(int) $subject->customer_id); ?>" >			                
								<?php echo $this->escape($subject->customer_name); ?></a>			                
				    		</td>
				    		<td >
				    			<a href="<?php echo JRoute::_('index.php?option=com_bookpro&task=customer.edit&id='.(int) $subject->driver_id); ?>" >	
				    			<?php echo $subject->customer; ?>
				    		</td>	
				    		<td class="hidden-phone"><?php if($subject->start_time==0){echo '';} else{echo $subject->start_time;};?></td>			
				    		<td class="hidden-phone"><?php echo JText::_('COM_BOOKPRO_ORDERTYPE_'.$subject->is_booked) ?></a></td>				    					   
				    		<td class="hidden-phone"><?php //echo BookProHelper::formatLang($subject->transport_type); ?></td>				    		
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
		
	
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<?php echo JHtml::_('form.token'); ?>
	<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>"/>
	<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>"/>
</form>
</div>
