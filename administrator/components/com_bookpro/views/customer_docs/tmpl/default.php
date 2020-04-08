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
	
$listOrder  = $this->escape($this->state->get('list.ordering'));
$listDirn   = $this->escape($this->state->get('list.direction'));
$loggeduser = JFactory::getUser();
$sortFields = $this->getSortFields();

$itemsCount = count($this->items);

BookProHelper::setSubmenu ( 2 );
?>
<div class="span12" id="j-main-container">
<form action="<?php echo JRoute::_('index.php?option=com_bookpro&view=customer_docs');?>" method="post" name="adminForm" id="adminForm">

	<div class="filter-search fltlft">
				<div class="pull-left hidden-phone fltlft form-inline">					
					
                    <input type="text" name="filter_search" id="filter_search" class="" onchange="this.form.submit();" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" placeholder="<?php echo JText::_('COM_BOOKPRO_SEARCH_EMAIL')?>"/>
                    <?php echo BookProHelper::getCustomer('filter_customer_id',$this->state->get('filter.customer_id'),'onchange="this.form.submit();"'); ?>
				</div>
				<div class="pull-left hidden-phone fltlft">
					<button onclick="this.form.submit();" class="btn">
						<i class="icon-search"></i>
					</button>
					<button type="button" class="btn hasTooltip" title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_CLEAR'); ?>" 
						onclick="
							//	document.id('filter_search').value='';
							//	this.form.filter_user_type.value='';
							//	this.form.filter_active.value='';
								this.form.filter_search.value='';
								this.form.submit();
						">
						<i class="icon-remove"></i>
					</button>
				</div>
				
			</div>
			
		
	<div id="filter-bar" class="btn-toolbar">
			
		<div class="btn-group pull-right hidden-phone">
			<label for="limit" class="element-invisible"><?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC');?>
			</label>
			<?php echo $this->pagination->getLimitBox(); ?>
		</div>
	</div>
		<table class="table-striped table">
			<thead>
				<tr>
					<th width="2%">
						<input type="checkbox" class="inputCheckbox" name="toggle" value="" onclick="Joomla.checkAll(this);" />
					</th>
					
					<th class="title" width="10%">
				        <?php echo JHTML::_('grid.sort',JText::_('Name doc'), 'name', $listDirn, $listOrder); ?>
					</th>
					<th width="4%"><?php echo JText::_('url');?></th>		
					<th width="4%"><?php echo JText::_('customer'); ?></th>
					<th width="4%"><?php echo JText::_('dowload'); ?></th>
					
										
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
				    	<?php $subject = &$this->items[$i]; ?>
				   		
				    	<tr class="row<?php echo ($i % 2); ?>">
				    		
				    		<td class="checkboxCell"><?php echo JHTML::_('grid.checkedout', $subject, $i); ?></td>
				    		<td>
				                
							<?php echo $this->escape($subject->name); ?></a>
				                
				    		</td>
				    		<td><?php echo $subject->url; ?></td>
				    		<td><?php echo $subject->namecustomer; ?></td>
				    		<td>
				    		<!-- <input type="button" value="<?php echo $subject->name;?>" class="button" onclick="location.href='<?php echo $subject->url; ?>'"> -->
				    		<a target="_blank" href="<?php echo $subject->url;?>"> <?php echo $subject->name;?></a></td>
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