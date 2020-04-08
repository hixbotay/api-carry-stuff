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
JHtml::_('bootstrap.framework');
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('formbehavior.chosen', 'select');

// Add styles
$document = JFactory::getDocument();
$document->addStyleDeclaration( '#quick-icons { clear:both; margin:-1px 0 8px; padding:8px 0; position:relative; z-index:8; }' );
$document->addStyleDeclaration( '#quick-icons.k2NoLogo { margin:0 0 8px; padding:0; border:none; }' );
$document->addStyleDeclaration( '#quick-icons div.icon-wrapper { float:left; display: block !important; width: auto !important; height :auto!important; line-height:12px !important; background: none; }' );
$document->addStyleDeclaration( '#quick-icons div.icon { text-align:center; margin-right:15px; float:left; margin-bottom:15px; }' );
$document->addStyleDeclaration( '#quick-icons div.icon a { background-color:#fff; background-position:-30px; display:block; float:left; height:97px; width:108px; color:#565656; vertical-align:middle; text-decoration:none; border:1px solid #CCC; -webkit-border-radius:5px; -moz-border-radius:5px; border-radius:5px; -webkit-transition-property:background-position, 0 0; -moz-transition-property:background-position, 0 0; -webkit-transition-duration:.8s; -moz-transition-duration:.8s; }' );
$document->addStyleDeclaration( '#quick-icons div.icon a:hover { background-position:0; -webkit-border-bottom-left-radius:50% 20px; -moz-border-radius-bottomleft:50% 20px; border-bottom-left-radius:50% 20px; -webkit-box-shadow:-5px 10px 15px rgba(0,0,0,0.25); -moz-box-shadow:-5px 10px 15px rgba(0,0,0,0.25); box-shadow:-5px 10px 15px rgba(0,0,0,0.25); position:relative; z-index:10; }' );
$document->addStyleDeclaration( '#quick-icons div.icon a img { padding:10px 0; margin:0 auto; }' );
$document->addStyleDeclaration( '#quick-icons div.icon a span { display:block; text-align:center; }' );
$document->addStyleDeclaration( '.pane-toggler, .pane-toggler-down { background-color: #d9edf7; border-color: #bce8f1; color: #3a87ad; text-shadow: 0 1px 0 rgba(255,255,255,0.5); border: 1px solid #fbeed5; -webkit-border-radius: 4px; -moz-border-radius: 4px; border-radius: 4px; padding: 8px 35px 8px 14px; }' );

$modulelist = $this->modulelist;
$stats = $this->stats;
$user = JFactory::getUser();
?>
<?php if (!empty( $this->sidebar)): ?>
  <div id="j-sidebar-container" class="span2">
    <?php echo $this->sidebar; ?>
  </div>
  <div id="j-main-container" class="span10">
<?php else : ?>
  <div id="j-main-container">
<?php endif;?>

    <div class="row-fluid">
      <div class="well well-small span6">
        <div id="quick-icons">
          <div class="icon-wrapper">
            <div class="icon">
              <a href="<?php echo JRoute::_('index.php?option=com_jbackend&view=keys'); ?>">
                <img alt="<?php echo JText::_('COM_JBACKEND_MENU_KEYS'); ?>" src="<?php echo JUri::root(true).'/administrator/components/com_jbackend/images/icon-48-keys.png'; ?>">
                <span><?php echo JText::_('COM_JBACKEND_MENU_KEYS'); ?></span>
              </a>
            </div>
          </div>
          <div class="icon-wrapper">
            <div class="icon">
              <a href="<?php echo JRoute::_('index.php?option=com_jbackend&view=logs'); ?>">
                <img alt="<?php echo JText::_('COM_JBACKEND_MENU_LOGS'); ?>" src="<?php echo JUri::root(true).'/administrator/components/com_jbackend/images/icon-48-logs.png'; ?>">
                <span><?php echo JText::_('COM_JBACKEND_MENU_LOGS'); ?></span>
              </a>
            </div>
          </div>
        </div>
      </div>
      <div class="well well-small span6">

        <?php
          echo JHtml::_('sliders.start', 'panel-sliders', array('useCookie' => 1));

          echo JHtml::_('sliders.panel', JText::_('COM_JBACKEND_DASHBOARD_SUMMARY_LABEL'), 'panel-summary');
        ?>

        <div class="row-striped">

          <div class="row-fluid">
            <div class="span7">
              <strong class="row-title">
                <a href="javascript:void(0)"><?php echo JText::_('COM_JBACKEND_DASHBOARD_SUMMARY_TOTAL_ENDPOINTS') . '&nbsp;/&nbsp;' . JText::_('COM_JBACKEND_DASHBOARD_SUMMARY_ACTIVE_ENDPOINTS'); ?></a>
              </strong>
            </div>
            <div class="span5">
              <span class="badge badge-info"><?php echo $stats['total_endpoints']; ?></span>&nbsp;/&nbsp;<span class="badge badge-success"><?php echo $stats['active_endpoints']; ?></span>
            </div>
          </div>

          <div class="row-fluid">
            <div class="span7">
              <strong class="row-title">
                <a href="javascript:void(0)"><?php echo JText::_('COM_JBACKEND_DASHBOARD_SUMMARY_TOTAL_REQUESTS') . '&nbsp;/&nbsp;' . JText::_('COM_JBACKEND_DASHBOARD_SUMMARY_TOTAL_REQUESTS_OK') . '&nbsp;/&nbsp;' . JText::_('COM_JBACKEND_DASHBOARD_SUMMARY_TOTAL_REQUESTS_KO'); ?></a>
              </strong>
            </div>
            <div class="span5">
              <span class="badge badge-info"><?php echo $stats['total_requests']; ?></span>&nbsp;/&nbsp;<span class="badge badge-success"><?php echo $stats['total_requests_ok']; ?></span>&nbsp;/&nbsp;<span class="badge badge-warning"><?php echo $stats['total_requests_ko']; ?></span>
            </div>
          </div>

          <div class="row-fluid">
            <div class="span7">
              <strong class="row-title">
                <a href="javascript:void(0)"><?php echo JText::_('COM_JBACKEND_DASHBOARD_SUMMARY_LAST_30_DAYS_REQUESTS') . '&nbsp;/&nbsp;' . JText::_('COM_JBACKEND_DASHBOARD_SUMMARY_LAST_7_DAYS_REQUESTS') . '&nbsp;/&nbsp;' . JText::_('COM_JBACKEND_DASHBOARD_SUMMARY_LAST_24_HOURS_REQUESTS'); ?></a>
              </strong>
            </div>
            <div class="span5">
              <span class="badge badge-info"><?php echo $stats['last_30_days_requests']; ?></span>&nbsp;/&nbsp;<span class="badge badge-info"><?php echo $stats['last_7_days_requests']; ?></span>&nbsp;/&nbsp;<span class="badge badge-info"><?php echo $stats['last_24_hours_requests']; ?></span>
            </div>
          </div>

          <div class="row-fluid">
            <div class="span7">
              <strong class="row-title">
                <a href="javascript:void(0)"><?php echo JText::_('COM_JBACKEND_DASHBOARD_SUMMARY_AVERAGE_RESPONSE_TIME') . '&nbsp;/&nbsp;' . JText::_('COM_JBACKEND_DASHBOARD_SUMMARY_MIN_RESPONSE_TIME') . '&nbsp;/&nbsp;' . JText::_('COM_JBACKEND_DASHBOARD_SUMMARY_MAX_RESPONSE_TIME'); ?></a>
              </strong>
            </div>
            <div class="span5">
              <span class="badge badge-info"><?php echo printf("%.3f", $stats['average_response_time']); ?></span>&nbsp;/&nbsp;<span class="badge badge-success"><?php echo printf("%.3f", $stats['min_response_time']); ?></span>&nbsp;/&nbsp;<span class="badge badge-danger"><?php echo printf("%.3f", $stats['max_response_time']); ?></span>
            </div>
          </div>

        </div>

        <?php
          echo JHtml::_('sliders.panel', JText::_('COM_JBACKEND_DASHBOARD_MODULES_LABEL'), 'panel-modules');
        ?>

          <form action="<?php echo JRoute::_('index.php?option=com_jbackend&view=dashboard'); ?>" method="post" name="adminForm" id="adminForm">
            <table class="table table-striped">
              <thead>
                <tr>
                  <th width="1%">
                    <input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
                  </th>
                  <th>
                    <?php echo JText::_('COM_JBACKEND_DASHBOARD_MODULES_MODULE_NAME'); ?>
                  </th>
                  <th>
                    <strong><?php echo JText::_('JSTATUS'); ?></strong>
                  </th>
                  <th>
                    <?php echo JText::_('COM_JBACKEND_DASHBOARD_MODULES_MODULE_ID'); ?>
                  </th>
                </tr>
              </thead>
            <?php if (count($modulelist)) : ?>
              <tbody>
              <?php foreach ($modulelist as $i=>$item) :
                $canEdit  = $user->authorise('core.edit', 'com_plugins');
                $canCheckin = $user->authorise('core.manage', 'com_checkin') || $item->checked_out==$user->get('id') || $item->checked_out==0;
                $canChange  = $user->authorise('core.edit.state', 'com_plugins') && $canCheckin;
              ?>
                <tr class="row<?php echo $i % 2; ?>">
                  <td class="center">
                    <?php echo JHtml::_('grid.id', $i, $item->extension_id); ?>
                  </td>
                  <td>
                    <?php if ($item->checked_out) : ?>
                      <?php echo JHtml::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'plugins.', $canCheckin); ?>
                    <?php endif; ?>
                    <?php if ($canEdit) : ?>
                      <a href="<?php echo JRoute::_('index.php?option=com_plugins&task=plugin.edit&extension_id='.(int) $item->extension_id); ?>">
                        <?php echo $item->name; ?></a>
                    <?php else : ?>
                        <?php echo $item->name; ?>
                    <?php endif; ?>
                  </td>
                  <td class="center">
                    <?php echo JHtml::_('jgrid.published', $item->enabled, $i, 'plugins.', $canChange); ?>
                  </td>
                  <td class="center">
                    <?php echo $item->extension_id;?>
                  </td>
                </tr>
                <?php endforeach; ?>
              </tbody>
            <?php else : ?>
              <tbody>
                <tr>
                  <td colspan="4">
                    <p class="noresults"><?php echo JText::_('COM_JBACKEND_LIST_NO_ITEMS');?></p>
                  </td>
                </tr>
              </tbody>
            <?php endif; ?>
            </table>
            <input type="hidden" name="task" value="" />
            <input type="hidden" name="boxchecked" value="0" />
            <?php echo JHtml::_('form.token'); ?>
          </form>

        <?php
          echo JHtml::_('sliders.panel', JText::_('COM_JBACKEND_DASHBOARD_ABOUT_LABEL'), 'panel-about');
        ?>

          <div style="float: left; margin-right: 20px;"><img src="<?php echo JUri::root().'administrator/components/com_jbackend/images/logo.png'; ?>" alt="jBackend"/></div>;
          <div style="float: none;"><?php echo JText::_('COM_JBACKEND_COPYRIGHT'); ?></div>;
          <div><?php echo JText::_('COM_JBACKEND_ABOUT_DESC'); ?></div>;

        <?php
          echo JHtml::_('sliders.end');
        ?>

      </div>
    </div>

  </div>