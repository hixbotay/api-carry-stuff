<?php
/**
 * 
 * @package 	Bookpro
 * @author 		Ngo Van Quan
 * @link 		http://joombooking.com
 * @copyright 	Copyright (C) 2011 - 2012 Ngo Van Quan
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id$
 **/

defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );
BookProHelper::setSubmenu(1);

JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('formbehavior.chosen', 'select');

$colspan = 10;
$app		= JFactory::getApplication();
$listOrder = $this->escape ( $this->state->get ( 'list.ordering' ) );
$listDirn = $this->escape ( $this->state->get ( 'list.direction' ) );
$saveOrder = $listOrder == 'a.ordering';
if ($saveOrder) {
	$saveOrderingUrl = 'index.php?option=com_bookpro&controller=aircrafts&task=saveOrderAjax&tmpl=component';
	JHtml::_('sortablelist.sortable', 'aircraftList', 'adminForm', strtolower($listDirn), $saveOrderingUrl);
}
$sortFields = $this->getSortFields ();

?>
<script type="text/javascript">
	Joomla.orderTable = function()
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
		Joomla.tableOrdering(order, dirn, '');
	}
</script>

<div class="span10" id="j-main-container">
<form action="<?php echo JRoute::_('index.php?option=com_bookpro&view=aircrafts');?>" method="post" id="adminForm" name="adminForm">
	<div id="filter-bar" class="btn-toolbar">
		<!-- search input -->
		<div class="filter-search btn-group pull-left">
			<label for="filter_search" class="element-invisible">
				<?php echo JText::_('COM_BOOKPRO_SEARCH');?>
			</label>
			<input type="text" name="filter_search" id="filter_search" class="hasTooltip"
				placeholder="<?php echo JText::_('COM_BOOKPRO_SEARCH');?>"
				value="<?php echo $this->escape($this->state->get('filter.search'));?>"
				title="<?php echo JText::_('COM_BOOKPRO_SEARCH');?>"
			/>
			
		</div>
				
		<!-- search button -->
		<div class="btn-group pull-left">
			<button class="btn hasTooltip" type="submit" 
			title="<?php echo JText::_('JSEARCH_FILTER_SUBMIT');?>"	>
				<i class="icon-search"></i>
			</button>
			<button class="btn hasTooltip" type="button"
			title="<?php echo JText::_('JSEARCH_FILTER_CLEAR');?>"
			onclick="document.id('filter_search').value='';				
				this.form.submit();" >
				<i class="icon-remove"></i>
			</button>
		</div>
		
		<div class="btn-group pull-right hidden-phone">
					<label for="limit" class="element-invisible"><?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC');?></label>
					<?php echo $this->pagination->getLimitBox(); ?>
		</div>
		
		<!-- filter sort -->
		<div class="btn-group pull-right hidden-phone">	
			<label for="directionTable" class="element-invisible">
				<?php echo JText::_('JFIELD_ORDERING_DESC');?>
			</label>
			<select name="directionTable" id="directionTable" class="input-medium" onchange="Joomla.orderTable()">
				<option value="">
					<?php echo JText::_('JFIELD_ORDERING_DESC');?>
				</option>
				<option value="asc"	<?php if($listDirn == 'asc') echo 'selected="selected"' ?>>
					<?php echo JText::_('JGLOBAL_ORDER_ASCENDING');?>
				</option>
				<option value="desc" <?php if($listDirn=='desc') echo 'selected="selected"'?>>
					<?php echo JText::_('JGLOBAL_ORDER_DESCENDING');?>
				</option>
			</select>	
		</div>
		
		<div class="btn-group pull-right">
			<label for="sortTable" class="element-invisible">
				<?php echo JText::_('JGLOBAL_SORT_BY');?>
			</label>
			<select name="sortTable" id="sortTable" class="input-medium" onchange="Joomla.orderTable()">
				<option value=""><?php echo JText::_('JGLOBAL_SORT_BY');?></option>
				<?php echo JHtml::_('select.options', $sortFields, 'value', 'text', $listOrder); //option = columes of database?>
			</select>
		</div>
	</div>
	
	<div class="clearfix"></div>
	<div id="editcell">
		<table class= "table table-striped" id="aircraftList">
			<thead>
					<tr>
						<th width="1%" class="nowrap center hidden-phone">
								<?php
								echo JHtml::_( 'grid.sort', '<i class="icon-menu-2"></i>', 'a.ordering', $listDirn, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING' );
								?>
					</th>
					<th width="1%" class="hidden-phone">
							<?php echo JHtml::_('grid.checkall'); ?>							
							
					</th>
					<th width="5%" style="min-width: 55px" class="nowrap center">
				        <?php echo JHTML::_('grid.sort',JText::_('JSTATUS'), 'a.state', $listDirn, $listOrder); ?>
					</th>
					<th width="10%">
				        <?php echo JHTML::_('grid.sort',JText::_('Code'), 'a.code', $listDirn, $listOrder); ?>
					</th>
					<th>
				        <?php echo JHTML::_('grid.sort',JText::_('COM_BOOKPRO_AIRPORT_TITLE'), 'a.title', $listDirn, $listOrder); ?>
					</th>
					<th width="10%">
				        <?php echo JHTML::_('grid.sort',JText::_('COM_BOOKPRO_SEAT'), 'a.seat', $listDirn, $listOrder); ?>
					</th>
					<th width="10%">
				       <?php echo JText::_('COM_BOOKPRO_AGENT_IMAGE');?>
					</th>
					
					
				</tr>
			</thead>
			
			<tfoot>
    			<tr>
    				<td colspan="7">
    				    <?php echo $this->pagination->getListFooter(); ?>
    				</td>
    			</tr>
			</tfoot>
			
			<tbody>
				<?php if(empty ($this->items)){?>
					<tr><td colspan="<?php echo $colspan; ?>" class="emptyListInfo"><?php echo JText::_('No items found.'); ?></td></tr>
				<?php }?>
				<?php foreach ($this->items as $i => $item) :
				$ordering	= $listOrder == 'a.ordering';
				?>
					<tr>
						<td class="order nowrap center hidden-phone">
						<?php 
						$iconClass = '';
						if (!$saveOrder) :
						$iconClass = ' inactive tip-top hasTooltip" title="' . JHtml::tooltipText('JORDERINGDISABLED');
						endif;
						?>
						
						<span class="sortable-handler<?php echo $iconClass ?>">
								<i class="icon-menu"></i>
						</span>
						<input type="text" style="display:none" name="order[]" size="5"
									value="<?php echo $item->ordering; ?>" class="width-20 text-area-order " />
						</td>
						
						<td class="center hidden-phone">
						<?php echo JHtml::_('grid.id', $i, $item->id);?>
						</td>
						
						<td class="center ">						
						<?php
							echo JHtml::_('jgrid.published', $item->state, $i, 'aircrafts.', true, 'cb');
							
						?>					
						</td>
						<td>
							<?php echo $item->code;?>
						</td>
						<td>
							<a href="<?php echo JRoute::_('index.php?option=com_bookpro&view=aircraft&layout=edit&id='.(int)$item->id);?>">
							<?php echo $item->title;?>
							</a>
						</td>
						<td>
							<?php echo $item->seat;?>
						</td>				
						<td>
							<img src="<?php echo "../".$item->image;?>" alt="<?php echo JText::_('COM_BOOKPRO_AGENT_IMAGE');?>" style="max-width: 50px;">
						</td>
					</tr>
				<?php endforeach;?>
			</tbody>
		</table>
	</div>
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="view" value="aircrafts" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
		<?php echo JHtml::_('form.token'); ?>
</form>
</div>
