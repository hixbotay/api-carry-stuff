<?php
/**
 * jBackend component for Joomla
 *
 * @author selfget.com (info@selfget.com)
 * @package jBackend
 * @copyright Copyright 2014 - 2015
 * @license GNU Public License
 * @link http://www.selfget.com
 * @version 2.1.3
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');

// Load the tooltip behavior
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('behavior.modal');
JHtml::_('formbehavior.chosen', 'select');

// Add tooltip style
$document = JFactory::getDocument();
$document->addStyleDeclaration( '.tip-text {word-wrap: break-word !important;}' );
$document->addStyleDeclaration( '.jrules td {padding: 0 10px 2px 0 !important; border: none !important;}' );

$user = JFactory::getUser();
$userId = $user->get('id');
$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn = $this->escape($this->state->get('list.direction'));
$canOrder = $user->authorise('core.edit.state');
$saveOrder = $listOrder=='k.ordering';
if ($saveOrder)
{
  $saveOrderingUrl = 'index.php?option=com_jbackend&task=keys.saveOrderAjax&tmpl=component';
  JHtml::_('sortablelist.sortable', 'keyList', 'adminForm', strtolower($listDirn), $saveOrderingUrl);
}
$sortFields = $this->getSortFields();
?>
<script type="text/javascript">
  Joomla.orderTable = function() {
    table = document.getElementById("sortTable");
    direction = document.getElementById("directionTable");
    order = table.options[table.selectedIndex].value;
    if (order != '<?php echo $listOrder; ?>') {
      dirn = 'asc';
    } else {
      dirn = direction.options[direction.selectedIndex].value;
    }
    Joomla.tableOrdering(order, dirn, '');
  }
  Joomla.submitbutton = function(pressbutton) {
  var form = document.adminForm;
    if (pressbutton == 'keys.resetstats') {
        if ( confirm("<?php echo JText::_('COM_JBACKEND_RESET_STATS_CONFIRM', false); ?>") ) {
            Joomla.submitform('keys.resetstats');
        }
    } else {
        Joomla.submitform(pressbutton);
    }
  }
</script>

<form action="<?php echo JRoute::_('index.php?option=com_jbackend&view=keys'); ?>" method="post" name="adminForm" id="adminForm">
  <?php if (!empty( $this->sidebar)) : ?>
    <div id="j-sidebar-container" class="span2">
    <?php echo $this->sidebar; ?>
    </div>
    <div id="j-main-container" class="span10">
  <?php else : ?>
    <div id="j-main-container">
  <?php endif;?>
    <?php
    // Search tools bar
    echo JLayoutHelper::render('joomla.searchtools.default', array('view' => $this));
    ?>
    <table class="table table-striped" id="keyList">
      <thead>
        <tr>
          <th width="5%" class="center hidden-phone">
            <?php echo JText::_('COM_JBACKEND_NUM'); ?>
          </th>
          <th width="5%" class="nowrap center hidden-phone">
            <?php echo JHtml::_('searchtools.sort', '', 'k.ordering', $listDirn, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING', 'icon-menu-2'); ?>
          </th>
          <th width="5%" class="center hidden-phone">
            <?php echo JHtml::_('grid.checkall'); ?>
          </th>
          <th width="5%" class="nowrap center">
            <?php echo JHtml::_('searchtools.sort', 'COM_JBACKEND_HEADING_KEYS_PUBLISHED', 'k.published', $listDirn, $listOrder); ?>
          </th>
          <th width="30%" class="center">
            <?php echo JHtml::_('searchtools.sort', 'COM_JBACKEND_HEADING_KEYS_KEY', 'k.key', $listDirn, $listOrder); ?>
            &nbsp;/&nbsp;
            <?php echo JHtml::_('searchtools.sort', 'COM_JBACKEND_HEADING_KEYS_USER_ID', 'k.user_id', $listDirn, $listOrder); ?>
            <br />
            <?php echo JHtml::_('searchtools.sort', 'COM_JBACKEND_HEADING_KEYS_COMMENT', 'k.comment', $listDirn, $listOrder); ?>
          </th>
          <th width="20%" class="center">
            <?php echo JHtml::_('searchtools.sort', 'COM_JBACKEND_HEADING_KEYS_DAILY_REQUESTS', 'k.daily_requests', $listDirn, $listOrder); ?>
            <br />
            <?php echo JHtml::_('searchtools.sort', 'COM_JBACKEND_HEADING_KEYS_EXPIRATION_DATE', 'k.expiration_date', $listDirn, $listOrder); ?>
          </th>
          <th width="20%" class="center">
            <?php echo JHtml::_('searchtools.sort', 'COM_JBACKEND_HEADING_KEYS_HITS', 'k.hits', $listDirn, $listOrder); ?>
            &nbsp;/&nbsp;
            <?php echo JHtml::_('searchtools.sort', 'COM_JBACKEND_HEADING_KEYS_LAST_VISIT', 'k.last_visit', $listDirn, $listOrder); ?>
            <br />
            <?php echo JHtml::_('searchtools.sort', 'COM_JBACKEND_HEADING_KEYS_CURRENT_DAY', 'k.current_day', $listDirn, $listOrder); ?>
            &nbsp;/&nbsp;
            <?php echo JHtml::_('searchtools.sort', 'COM_JBACKEND_HEADING_KEYS_CURRENT_HITS', 'k.current_hits', $listDirn, $listOrder); ?>
          </th>
          <th width="10%" class="nowrap center hidden-phone">
            <?php echo JHtml::_('searchtools.sort', 'COM_JBACKEND_HEADING_KEYS_ID', 'k.id', $listDirn, $listOrder); ?>
          </th>
        </tr>
      </thead>
      <tbody>
      <?php
        if( count( $this->items ) > 0 ) {
          foreach ($this->items as $i => $item) :
            $ordering   = ($listOrder == 'k.ordering');
            $canCreate  = $user->authorise('core.create',     'com_jbackend.key');
            $canEdit    = $user->authorise('core.edit',       'com_jbackend.key.'.$item->id);
            $canCheckin = $user->authorise('core.manage',     'com_checkin') || $item->checked_out == $userId || $item->checked_out == 0;
            $canChange  = $user->authorise('core.edit.state', 'com_jbackend.key.'.$item->id) && $canCheckin;
            $item_link = JRoute::_('index.php?option=com_jbackend&task=key.edit&id='.(int)$item->id);
      ?>
        <tr class="row<?php echo $i % 2; ?>" sortable-group-id="keys">
          <td class="center hidden-phone">
            <?php echo $this->pagination->getRowOffset( $i ); ?>
          </td>
          <td class="order nowrap center hidden-phone">
            <?php
            $iconClass = '';
            if (!$canChange)
            {
              $iconClass = ' inactive';
            }
            elseif (!$saveOrder)
            {
              $iconClass = ' inactive tip-top hasTooltip" title="' . JHtml::tooltipText('JORDERINGDISABLED');
            }
            ?>
            <span class="sortable-handler <?php echo $iconClass ?>">
              <i class="icon-menu"></i>
            </span>
            <?php if ($canChange && $saveOrder) : ?>
              <input type="text" style="display:none" name="order[]" size="5" value="<?php echo $item->ordering; ?>" class="width-20 text-area-order " />
            <?php endif; ?>
          </td>
          <td class="center hidden-phone">
            <?php echo JHtml::_('grid.id', $i, $item->id); ?>
          </td>
          <td class="center">
            <div class="btn-group">
              <?php echo JHtml::_('jgrid.published', $item->published, $i, 'keys.', $canChange, 'cb'); ?>
            </div>
          </td>
          <td class="small">
            <span style="display:block; word-wrap:break-word;">
            <?php if ($item->checked_out) : ?>
              <?php echo JHtml::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'keys.', $canCheckin); ?>
            <?php endif; ?>
            <?php
              if ($canEdit) : ?>
                <a href="<?php echo $item_link; ?>" title="<?php echo JText::_('COM_JBACKEND_EDIT_ITEM'); ?>"><?php echo $item->key; ?></a>
              <?php else : ?>
                <span title="<?php echo JText::_('COM_JBACKEND_HEADING_KEYS_KEY'); ?>"><?php echo $item->key; ?></span>
              <?php endif; ?>
            </span>
            <div><?php echo $item->user_name . ' ('; ?><a class="modal" rel="{handler: 'clone', dopt: 'userdetails_<?php echo $item->user_id; ?>', size: {x: 320, y: 200}}" href="#userdetails_<?php echo $item->user_id; ?>"><?php echo $item->user_id . ')'; ?></a></div>
            <div style="border: 1px dashed silver; min-height: 30px; word-wrap: break-word; margin-top: 5px;" class="editlinktip hasTip" title="<?php echo JText::_('COM_JBACKEND_FIELD_KEY_COMMENT_LABEL')."::".htmlspecialchars($item->comment, ENT_QUOTES); ?>">
              <?php
                $max_chars = 100;
                echo jBackendHelper::trimText(htmlspecialchars($item->comment, ENT_QUOTES), $max_chars);
              ?>
            </div>
            <div class="hide">
              <div id="userdetails_<?php echo $item->user_id; ?>">
                <table class="table">
                  <tr><td><?php echo JText::_('COM_JBACKEND_FIELD_KEY_USER_NAME_LABEL'); ?></td><td><?php echo $item->user_name; ?></td></tr>
                  <tr><td><?php echo JText::_('COM_JBACKEND_FIELD_KEY_USER_USERNAME_LABEL'); ?></td><td><?php echo $item->user_username; ?></td></tr>
                  <tr><td><?php echo JText::_('COM_JBACKEND_FIELD_KEY_USER_EMAIL_LABEL'); ?></td><td><?php echo $item->user_email; ?></td></tr>
                </table>
                <div style="text-align: center;"><button class="btn" type="button" onclick="window.parent.SqueezeBox.close();"><?php echo JText::_('COM_JBACKEND_CLOSE'); ?></button></div>
              </div>
            </div>
          </td>
          <td class="center">
            <?php echo $item->daily_requests; ?>
            <br />
            <?php echo $item->expiration_date; ?>
          </td>
          <td class="center">
            <?php echo $item->hits; ?>
            <br />
            <?php echo $item->last_visit; ?>
            <br />
            <div style="border: 1px dashed silver; margin-top: 5px;">
              <?php echo $item->current_day; ?>
              <br />
              <?php echo $item->current_hits; ?>
            </div>
          </td>
          <td class="center hidden-phone">
            <?php echo $item->id; ?>
          </td>
        </tr>
      <?php
          endforeach;
        } else {
      ?>
        <tr>
          <td colspan="10">
            <?php echo JText::_('COM_JBACKEND_LIST_NO_ITEMS'); ?>
          </td>
        </tr>
      <?php
        }
      ?>
      </tbody>
      <tfoot>
        <tr>
          <td colspan="10">
            <?php echo $this->pagination->getListFooter(); ?>
          </td>
        </tr>
      </tfoot>
    </table>

    <input type="hidden" name="task" value="" />
    <input type="hidden" name="boxchecked" value="0" />
    <?php echo JHtml::_('form.token'); ?>
  </div>
</form>
