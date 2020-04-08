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

$user = JFactory::getUser();
//make sure user is logged in
if($user->id == 0)
{
	JError::raiseWarning( 403, JText::_( 'COM_BOOKPRO_ERROR_MUST_LOGIN') 
	);
	$joomlaLoginUrl = 'index.php?option=com_users&view=login';
	echo "<br><a href='".JRoute::_($joomlaLoginUrl)."'>".JText::_( 'COM_BOOKPRO_LOG_IN')."</a><br>";
}
else
{
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

  <body onload="load()">
<div class="span10" id='j-main-container'>
<form action="<?php echo JRoute::_('index.php?option=com_bookpro&view=updorders');?>" method="post" name="adminForm" id="adminForm">
		
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
					<th class="title" width="10%">
				        <?php echo JHTML::_('grid.sort',JText::_('COM_BOOKPRO_ORDERS_RECIPIENT_NAME'), 'name', $listDirn, $listOrder); ?>
					</th>
					<th width="4%"><?php echo JText::_('COM_BOOKPRO_ORDERS_TYPE'); ?></th>
					<th width="4%"><?php echo JText::_('COM_BOOKPRO_ORDERS_TOTAL'); ?></th>
					<th width="4%"><?php echo JText::_('COM_BOOKPRO_ORDERS_PAY_STATUS'); ?></th>
					<th width="4%"><?php echo JText::_('COM_BOOKPRO_ORDERS_ORDER_STATUS'); ?></th>
					<th width="4%"><?php echo JText::_('COM_BOOKPRO_ORDERS_CREATED'); ?></th>
					<th width="4%"><?php echo JText::_('COM_BOOKPRO_ORDERS_CUSTOMER_NAME'); ?></th>	
					<th width="4%"><?php echo JText::_('COM_BOOKPRO_ORDERS_CUSTOMER_PHONE'); ?></th>	
					<th width="4%"><?php echo JText::_('COM_BOOKPRO_ORDERS_ID'); ?></th>					
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
				                
				                <a href="<?php echo JRoute::_('index.php?option=com_bookpro&task=updorder.edit&id='.(int) $subject->id); ?>" title="<?php echo JText::_('COM_BOOKPRO_EDIT_ID'); ?>">
							<?php echo $this->escape($subject->recipient_name); ?></a>
				                
				    		</td>
				    		<td class="hidden-phone"><?php echo $subject->type; ?></td>
				    		<td class="hidden-phone"> <?php echo $subject->total; ?></td>
				    		<td class="hidden-phone"><?php echo $subject->pay_status; ?></td>
				    		<td class="hidden-phone"><?php echo $subject->order_status; ?></td>
				    		<td class="hidden-phone"><?php echo $subject->created; ?></td>
				    		<td class="hidden-phone"><?php echo $subject->name; ?></td>
				    		<td class="hidden-phone"><?php echo $subject->phone; ?></td>
							<td class="hidden-phone"><?php echo $subject->id; ?></td>
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
<?php
}