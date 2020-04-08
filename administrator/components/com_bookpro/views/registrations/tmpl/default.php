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

$listOrder = $this->escape ( $this->state->get ( 'list.ordering' ) );
$listDirn = $this->escape ( $this->state->get ( 'list.direction' ) );
$loggeduser = JFactory::getUser ();
$sortFields = $this->getSortFields ();

$itemsCount = count ( $this->items );

BookProHelper::setSubmenu ( 12 );
?>
<div class="span10" id="j-main-container">
	<form
		action="<?php echo JRoute::_('index.php?option=com_bookpro&view=customers&state=0&layout=registration');?>"
		method="post" name="adminForm" id="adminForm">

		<fieldset id="filter-bar">
			<div class="filter-search fltlft">
				<div class="btn-group pull-left">
					<input type="text" name="filter_search" id="filter_search"
						value="<?php echo $this->state->get('filter.search')?>" class=""
						onchange="this.form.submit();" value="<?php ?>"
						placeholder="<?php echo JText::_('COM_BOOKPRO_KEYWORD')?>" />
				</div>
				<div class="btn-group pull-left">	
                   <?php echo $this->customergroup?>
				</div>
				<div class="btn-group pull-left">
					<button onclick="this.form.submit();" class="btn">
						<?php echo JText::_('COM_BOOKPRO_SEARCH'); ?>
					</button>
					<button class="btn hasTooltip" type="button"
						title="<?php echo JText::_('JSEARCH_FILTER_CLEAR');?>"
						onclick="
					//	document.id('filter_type_id').value='0';						
					//	document.id('filter_search').value='';
						this.form.filter_search.value='';
						this.form.filter_user_type.value='';
						this.form.submit();">
						<i class="icon-remove"></i>
					</button>
				</div>
			</div>
			<div class="btn-group pull-right hidden-phone">
				<label for="limit" class="element-invisible"><?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC');?></label>
					<?php echo $this->pagination->getLimitBox(); ?>
			</div>

		</fieldset>
		<table class="table table-striped ">
			<thead>
				<tr>
					<th width="1%"><input type="checkbox" class="inputCheckbox"
						name="toggle" value="" onclick="Joomla.checkAll(this);" /></th>

					<th class="title" width="10%">
				        <?php echo JHTML::_('grid.sort',JText::_('COM_BOOKPRO_CUSTOMER_NAME'), 'name', $listDirn, $listOrder); ?>
					</th>

					<th><?php echo JText::_('COM_BOOKPRO_CUSTOMER_USERNAME'); ?></th>
					<th width="5%" class="nowrap center">
							<?php echo JHtml::_('searchtools.sort', 'COM_BOOKPRO_CUSTOMER_APPROVE', 'a.active', $listDirn, $listOrder); ?>
						</th>
					<th class="title" width="10%">
				        <?php echo JHTML::_('grid.sort',JText::_('COM_BOOKPRO_CUSTOMER_TYPE'), 'user_type', $listDirn, $listOrder); ?>
					</th>
					<th class="title" width="10%">
				        <?php echo JHTML::_('grid.sort',JText::_('COM_BOOKPRO_CUSTOMER_SOCIALREASON'), 'company_name', $listDirn, $listOrder); ?>
					</th>
					<th width="8%"><?php echo JText::_('COM_BOOKPRO_CUSTOMER_PHONE'); ?></th>

					<th><?php echo JText::_('COM_BOOKPRO_CUSTOMER_CITY'); ?>
					</th>
					<!-- <th><?php echo JText::_('COM_BOOKPRO_COUNTRY'); ?></th> -->
					<th>
					 <?php echo JHTML::_('grid.sort',JText::_('COM_BOOKPRO_CUSTOMER_REGISTRATION_DATE'), 'registration_date', $listDirn, $listOrder); ?>
					</th>
					<th style="text-align: right" width="4%">
				        <?php echo JHTML::_('grid.sort', 'ID', 'id', $listDirn, $listOrder); ?>
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
				    	<?php $subject = &$this->items[$i]; ?>
				   		
				    	<tr class="row<?php echo ($i % 2); ?>">

					<td class="checkboxCell"><?php echo JHTML::_('grid.checkedout', $subject, $i); ?></td>
					<!-- 
				    		<td class="center">
								<?php echo JHtml::_('jgrid.published', $subject->state, $i, 'customers.', true, 'cb', null, null); ?>
							</td>
							 -->

					<td><a
						href="<?php echo JRoute::_('index.php?option=com_bookpro&view=customer&layout=registration&id='.(int) $subject->id); ?>">
								<?php echo $this->escape($subject->name); ?></a></td>

					<td><a
						href="<?php echo JRoute::_('index.php?option=com_users&task=user.edit&id='.$subject->user_id)?>"><?php echo $subject->username;?> </a></td>
					<td class="center"><input type="button" value="Approve"
						class="btn btn-success"
						onclick="location.href='<?php echo JRoute::_('index.php?option=com_bookpro&controller=customer&task=approve&id='.$subject->id); ?>'">
					</td>

					<td class="left"><?php if($subject->user_type == 1){ echo 'Particular';} elseif($subject->user_type == 2) {echo 'Enterprise';} else{ echo 'Driver';}; ?></td>
					<td class="left"><?php echo $subject->company_name;?></td>
					<td class="right"><?php echo $subject->phone; ?></td>

					<td class="justify"><?php echo $subject->city; ?></td>
					<!-- <td><?php echo $subject->country_name; ?></td>-->
					<td><?php echo JHtml::_('date',$subject->registration_date,'d-m-Y'); ?></td>
					<td style="text-align: right; white-space: nowrap;"><?php echo number_format($subject->id, 0, '', ' '); ?></td>
				</tr>
				    <?php } ?>
				<?php } ?>
			</tbody>
		</table>


		<input type="hidden" name="task" value="" /> 
		<input type="hidden" name="boxchecked" value="0" />
		<?php echo JHtml::_('form.token'); ?>
		<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" /> 
		<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
	</form>
</div>