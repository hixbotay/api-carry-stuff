
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
BookProHelper::setSubmenu ( 3 );
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('formbehavior.chosen', 'select');
$user = JFactory::getUser();		
$listOrder  = $this->escape($this->state->get('list.ordering'));
$listDirn   = $this->escape($this->state->get('list.direction'));
$loggeduser = JFactory::getUser();
$sortFields = $this->getSortFields();

$saveOrder = $listOrder == 'a.ordering';
if ($saveOrder)
{
	$saveOrderingUrl = 'index.php?option=com_bookpro&task=vehicles.saveOrderAjax&tmpl=component';
	JHtml::_('sortablelist.sortable', 'vehicleList', 'adminForm', strtolower($listDirn), $saveOrderingUrl);
}
$sortFields = $this->getSortFields();
$itemsCount = count($this->items);

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
<div class="span10" id="j-main-container">
<form action="<?php echo JRoute::_('index.php?option=com_bookpro&view=vehicles');?>" method="post" name="adminForm" id="adminForm">
	
		<div class="btn-group pull-right hidden-phone">
			<label for="limit" class="element-invisible"><?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC');?>
			</label>
			<?php echo $this->pagination->getLimitBox(); ?>
		</div>
	<div class="filter-search fltlft">
			<div class="pull-left hidden-phone fltlft form-inline">					
					
                    <input type="text" name="filter_search" id="filter_search" class="" onchange="this.form.submit();" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" placeholder="<?php echo JText::_('COM_BOOKPRO_VEHICLES_SEARCH')?>"/>
                    <?php echo BookProHelper::getDriverName ( $this->state->get ( 'filter.driver_id' ) ); ?>
                    <?php echo $this->getVehicleType(); ?>
                    <?php //echo $this->validation; ?>
				</div>
				<div class="btn-group pull-left">
					<button onclick="this.form.submit();" class="btn">
						<i class="icon-search"></i>
					</button>
					<button type="button" class="btn hasTooltip" title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_CLEAR'); ?>" 
						onclick="
								jQuery('#filter_search').val('');
								jQuery('#filter_driver_id').val('');
								jQuery('#filter_type').val('');
								this.form.filter_search.value='';
								this.form.submit();">
						<i class="icon-remove"></i>
					</button>
				</div>			
			</div>
			<div class="clearfix"> </div>
		<table class="table-striped table">
			<thead>
				<tr>
					<th width="2%">
						<input type="checkbox" class="inputCheckbox" name="toggle" value="" onclick="Joomla.checkAll(this);" />
					</th>					
					<th class="title" width="5%">
				        <?php echo JHTML::_('grid.sort',JText::_('COM_BOOKPRO_VEHICLES_NAME'), 'name', $listDirn, $listOrder); ?>
					</th>
					<th width="5%"><?php echo JHTML::_('grid.sort',JText::_('COM_BOOKPRO_DRIVER_NAME'), 'b.name', $listDirn, $listOrder);?></th>
					<th width="4%"><?php echo JText::_('COM_BOOKPRO_VEHICLES_TYPE_TITLE');?></th>		
					<th width="4%"><?php echo JHTML::_('grid.sort','COM_BOOKPRO_VEHICLES_CAPACITY', 'a.capacity', $listDirn, $listOrder);?></th>		
					<th width="4%"><?php echo JText::_('COM_BOOKPRO_VEHICLES_PLATE_NUMBER'); ?></th>
					<th width="1%" style="min-width:55px" class="nowrap center">
						<?php echo JText::_('COM_BOOKPRO_VEHICLES_CURRENT'); ?>
					</th>
					<!--<th width="1%" style="min-width:55px" class="nowrap center">
						<?php echo JText::_('COM_BOOKPRO_VEHICLES_DEFAULT'); ?>
					</th>-->
					<th width="1%"><?php  echo JHtml::_('grid.sort', 'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?></th>					
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
				    	<?php $subject = &$this->items[$i]; 
				    	$canCheckin = $user->authorise('core.manage', 'com_checkin') || $item->checked_out == $user->get('id') || $item->checked_out == 0;
						$canChange = $user->authorise('core.edit.state', 'com_bookpro') && $canCheckin;
				    	?>
				   		
				    	<tr class="row<?php echo ($i % 2); ?>" sortable-group-id="1">
				    		<td class="checkboxCell"><?php echo JHTML::_('grid.checkedout', $subject, $i); ?></td>
				    		<td>				                
				                <a href="<?php echo JRoute::_('index.php?option=com_bookpro&task=vehicle.edit&id='.(int) $subject->id); ?>" title="<?php echo JText::_('COM_BOOKPRO_EDIT_USER'); ?>">
							<?php echo $this->escape($subject->name); ?></a>				                
				    		</td>	
				    		<td> <a href="<?php echo JRoute::_('index.php?option=com_bookpro&task=customer.edit&id='.(int) $subject->driver_id); ?>" title="<?php echo JText::_('COM_BOOKPRO_EDIT_USER'); ?>">
				    		<?php echo $subject->customer_name; ?></td>		 
				    		<td><?php echo BookProHelper::formatLang($subject->vehicle_type_name); ?></td>	   						
				    		<td><?php echo $subject->capacity; ?></td>
				    		<td><?php echo $subject->plate_number; ?></td>
				    		<td class="center">
								<?php 
								if ($subject->current == 1) {
									?>												
									<a class="btn btn-micro active hasTooltip" onclick="location.href='<?php echo JRoute::_('index.php?option=com_bookpro&controller=vehicle&task=changecurrent&vehicle_id='.$subject->id); ?>'" title="" data-original-title="Disable">
									<span class="icon-publish"></span></a>
									<?php						
								} else {
									?>
									<a class="btn btn-micro hasTooltip" onclick="location.href='<?php echo JRoute::_('index.php?option=com_bookpro&controller=vehicle&task=changecurrent&vehicle_id='.$subject->id); ?>'" title="" data-original-title="Enable">
									<span class="icon-unpublish"></span></a>
									<?php }?>		
							</td>
							<!--<
							<td class="center">
								<?php echo JHtml::_('jgrid.published', $subject->default, $i, 'vehicles.', $canChange, 'cb'); ?>
													
							</td>-->						
				    		<td><?php echo $subject->id; ?></td>
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