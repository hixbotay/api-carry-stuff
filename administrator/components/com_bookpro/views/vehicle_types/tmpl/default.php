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
JHtml::_ ( 'behavior.multiselect' );
JHtml::_ ( 'formbehavior.chosen', 'select' );
BookProHelper::setSubmenu ( 3 );
$listOrder = $this->escape ( $this->state->get ( 'list.ordering' ) );
$listDirn = $this->escape ( $this->state->get ( 'list.direction' ) );
$loggeduser = JFactory::getUser ();
$sortFields = $this->getSortFields ();

$itemsCount = count ( $this->items );
$icon=(object)$this->items[0];
$a=json_decode($icon->icon);

//var_dump($a->medium);die;
?>
<div class="span12" id="j-main-container">
	<form
		action="<?php echo JRoute::_('index.php?option=com_bookpro&view=vehicle_types');?>"
		method="post" name="adminForm" id="adminForm">

		<div class="filter-search fltlft">
			<div class="pull-left hidden-phone fltlft form-inline">

				<input type="text" name="filter_search" id="filter_search" class=""
					onchange="this.form.submit();"
					value="<?php echo $this->escape($this->state->get('filter.search')); ?>"
					placeholder="<?php echo JText::_('COM_BOOKPRO_SEARCH_EMAIL')?>" />
			</div>
			<div class="pull-left hidden-phone fltlft">
				<button onclick="this.form.submit();" class="btn">
					<i class="icon-search"></i>
				</button>
				<button type="button" class="btn hasTooltip"
					title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_CLEAR'); ?>"
					onclick="
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
					<th width="5%"><input type="checkbox" class="inputCheckbox"
						name="toggle" value="" onclick="Joomla.checkAll(this);" /></th>
					<th width="2%">
				        <?php echo JHTML::_('grid.sort',JText::_('JSTATUS'), 'a.state', $listDirn, $listOrder); ?>
					</th>
					<th class="title" width="20%">
				        <?php echo JHTML::_('grid.sort',JText::_('COM_BOOKPRO_VEHICLE_TYPE_NAME'), 'a.name', $listDirn, $listOrder); ?>
					</th>
					<th><?php echo JText::_('COM_BOOKPRO_VEHICLE_TYPE_DESCRIPTION'); ?></th>
					<th><?php echo JText::_('COM_BOOKPRO_VEHICLE_TYPE_ICON'); ?></th>
					<th width="2%"><?php echo JHTML::_('grid.sort', 'ID', 'a.id', $listDirn, $listOrder); ?>
					</th>
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
					<tr>
					<td colspan="10"><?php echo JText::_('No items found.'); ?></td>
				</tr>
				<?php } else { ?>
				    <?php for ($i = 0; $i < $itemsCount; $i++) { ?>
				    	<?php $subject = $this->items[$i]; 
				    		
							$icon=json_decode($subject->icon);
				    	?>
				    	<tr class="row<?php echo ($i % 2); ?>">
							<td class="checkboxCell"><?php echo JHTML::_('grid.checkedout', $subject, $i); ?></td>
							<td>
							<?php echo JHtml::_('jgrid.published', $subject->state, $i, 'vehicle_types.', true, 'cb'); ?>
							</td>
							<td><a
								href="<?php echo JRoute::_('index.php?option=com_bookpro&task=vehicle_type.edit&id='.(int) $subject->id); ?>"
								title="<?php echo JText::_('COM_BOOKPRO_EDIT_NAME'); ?>">
									<?php echo BookProHelper::formatLang($subject->name); ?></a></td>
							<td class="hidden-phone"><?php echo $subject->desc; ?></td>
							<td>
								<img class="thumbnail" style="width:50px" src="<?php echo JUri::root().reset($icon);?>" alt="<?php echo JText::_('COM_BOOKPRO_VEHICLE_TYPE_ICON');?>" />
							</td>		
							<td class="hidden-phone"><?php echo $subject->id; ?></td>
						</tr>
				    <?php } ?>
				<?php } ?>
			</tbody>
		</table>


		<input type="hidden" name="task" value="" /> <input type="hidden"
			name="boxchecked" value="0" />
	<?php echo JHtml::_('form.token'); ?>
	<input type="hidden" name="filter_order"
			value="<?php echo $listOrder; ?>" /> <input type="hidden"
			name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
	</form>
</div>