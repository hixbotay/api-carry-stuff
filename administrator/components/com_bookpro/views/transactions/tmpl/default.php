
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
AImporter::helper('currency','js','date');
$user = JFactory::getUser();		
$listOrder  = $this->escape($this->state->get('list.ordering'));
$listDirn   = $this->escape($this->state->get('list.direction'));
$itemsCount = count($this->items);

?>

<div class="span10" id="j-main-container">
<form action="<?php echo JRoute::_('index.php?option=com_bookpro&view=transactions');?>" method="post" name="adminForm" id="adminForm">
	
		<div class="btn-group pull-right hidden-phone">
			<label for="limit" class="element-invisible"><?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC');?>
			</label>
			<?php echo $this->pagination->getLimitBox(); ?>
		</div>
	<div class="filter-search fltlft">
			<div class="pull-left hidden-phone fltlft form-inline">	
				<input type="text" name="filter_search" id="filter_search" class="" onchange="this.form.submit();" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" placeholder="<?php echo JText::_('COM_BOOKPRO_SEARCH_BY_NUMBER')?>"/>			   
			</div>
			<div class="pull-left hidden-phone fltlft form-inline">	
				<?php echo $this->getCancelBox();?>
			</div>
			<div class="pull-left hidden-phone fltlft form-inline">	
				<?php echo $this->getWhoCanceled();?>
			</div>
				<div class="btn-group pull-left">
					<button onclick="this.form.submit();" class="btn">
						<i class="icon-search"></i>
					</button>
					<button type="button" class="btn hasTooltip" title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_CLEAR'); ?>" 
						onclick="
							//	document.id('filter_search').value='';
								this.form.filter_search.value='';
								this.form.filter_cancel_status.value='';
								this.form.filter_order_who_cancelled.value='';
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
					<th width="5%">
				        <?php echo JHTML::_('grid.sort',JText::_('COM_BOOKPRO_TRANSACTION_ID'), 'tx_id', $listDirn, $listOrder); ?>
					</th>					
					<th width="5%">
				        <?php echo JHTML::_('grid.sort',JText::_('COM_BOOKPRO_ORDER_NUMBER'), 'order_id', $listDirn, $listOrder); ?>
					</th>
					<th width="5%"><?php echo JHTML::_('grid.sort',JText::_('COM_BOOKPRO_ORDERS_TOTAL'), 'b.total', $listDirn, $listOrder);?></th>
							
					<th width="1%"><?php echo JHtml::_('grid.sort', JText::_('COM_BOOKPRO_ORDERS_CREATED_TIME'), 'id', $listDirn, $listOrder); ?></th>					
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
						$params = json_decode($subject->params);
						$currency = strtoupper($params->currency);
						$currency = str_replace('USD','$',$currency);
						$currency = str_replace('EUR','€',$currency);
				    	?>
				   		
				    	<tr class="row<?php echo ($i % 2); ?>" sortable-group-id="1">
				    		<td class="checkboxCell"><?php echo JHTML::_('grid.checkedout', $subject, $i); ?></td>
				    		<td>				                
				                <a href="<?php echo JRoute::_('index.php?option=com_bookpro&task=transaction.edit&id='.(int) $subject->id); ?>">
								<?php echo $subject->tx_id?></a>				                
				    		</td>
				    		<td>				                
				                <a href="<?php echo JRoute::_('index.php?option=com_bookpro&task=order.edit&id='.(int) $subject->order_id); ?>" title="<?php echo JText::_('COM_BOOKPRO_EDIT_USER'); ?>">
								<?php echo sprintf('%010d', $subject->order_id); ?></a>				                
				    		</td>					 	 
				    		<td><?php echo CurrencyHelper::displayPrice($subject->total); ?></td>	 					
				    		<td><?php echo JFactory::getDate($subject->created)->format(DateHelper::getConvertDateFormat().' H:i:s'); ?></td>
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