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

// Include the component HTML helpers
JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');

// Load the tooltip behavior
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');
JHtml::_('formbehavior.chosen', 'select');

// Add pre style
$document = JFactory::getDocument();
$document->addStyleDeclaration( 'pre {font-size: 11px;}' );
?>
<script type="text/javascript">
  function generatecode(size)
  {
    var code = "";
    var alphabet = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";

    for( var i=0; i < size; i++ )
        code += alphabet.charAt(Math.floor(Math.random() * alphabet.length));

    return code;
  }
  Joomla.submitbutton = function(task)
  {
    if (task == 'key.generate_key') {
      document.getElementById('jform_key').value = generatecode(20);
      return false;
    }
    if (task == 'key.cancel' || document.formvalidator.isValid(document.id('key-form'))) {
      Joomla.submitform(task, document.getElementById('key-form'));
    }
  }
</script>

<form action="<?php echo JRoute::_('index.php?option=com_jbackend&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="key-form" class="form-validate form-horizontal">
  <div class="row-fluid">
    <!-- Begin Content -->
    <div class="span10 form-horizontal">

      <ul class="nav nav-tabs">
        <li class="active"><a href="#key" data-toggle="tab"><?php echo empty($this->item->id) ? JText::_('COM_JBACKEND_KEY_NEW_KEY') : JText::_('COM_JBACKEND_KEY_EDIT_KEY'); ?></a></li>
        <li><a href="#stats" data-toggle="tab"><?php echo JText::_('COM_JBACKEND_KEY_STATS'); ?></a></li>
      </ul>

      <fieldset>
      <div class="tab-content">

        <div class="tab-pane active" id="key">
          <div class="control-group">
            <div class="control-label"><?php echo $this->form->getLabel('key'); ?></div>
            <div class="controls"><div class="input-append"><?php echo $this->form->getInput('key'); ?><button type="button" class="btn" id="jform_key_img" onclick="Joomla.submitbutton('key.generate_key')"><i class="icon-wand"></i></button></div></div>
          </div>
          <div class="control-group">
            <div class="control-label"><?php echo $this->form->getLabel('user_id'); ?></div>
            <div class="controls"><?php echo $this->form->getInput('user_id'); ?></div>
          </div>
          <div class="control-group">
            <div class="control-label"><?php echo $this->form->getLabel('daily_requests'); ?></div>
            <div class="controls"><?php echo $this->form->getInput('daily_requests'); ?></div>
          </div>
          <div class="control-group">
            <div class="control-label"><?php echo $this->form->getLabel('expiration_date'); ?></div>
            <div class="controls"><?php echo $this->form->getInput('expiration_date'); ?></div>
          </div>
          <div class="control-group">
            <div class="control-label"><?php echo $this->form->getLabel('comment'); ?></div>
            <div class="controls"><?php echo $this->form->getInput('comment'); ?></div>
          </div>
          <div class="control-group">
            <div class="control-label"><?php echo $this->form->getLabel('id'); ?></div>
            <div class="controls"><?php echo $this->form->getInput('id'); ?></div>
          </div>
        </div>

        <div class="tab-pane" id="stats">
          <div class="control-group">
            <div class="control-label"><?php echo $this->form->getLabel('hits'); ?></div>
            <div class="controls"><?php echo $this->form->getInput('hits'); ?></div>
          </div>
          <div class="control-group">
            <div class="control-label"><?php echo $this->form->getLabel('last_visit'); ?></div>
            <div class="controls"><?php echo $this->form->getInput('last_visit'); ?></div>
          </div>
          <div class="control-group">
            <div class="control-label"><?php echo $this->form->getLabel('current_day'); ?></div>
            <div class="controls"><?php echo $this->form->getInput('current_day'); ?></div>
          </div>
          <div class="control-group">
            <div class="control-label"><?php echo $this->form->getLabel('current_hits'); ?></div>
            <div class="controls"><?php echo $this->form->getInput('current_hits'); ?></div>
          </div>
        </div>

      </div>
      </fieldset>

    </div>
    <!-- End Content -->

    <!-- Begin Sidebar -->
    <div class="span2">

      <h4><?php echo JText::_('COM_JBACKEND_KEY_PUBLISHING_OPTIONS');?></h4>
      <hr />
      <fieldset class="form-vertical">
      <div class="control-group">
        <div class="control-group">
          <div class="control-label"><?php echo $this->form->getLabel('published'); ?></div>
          <div class="controls"><?php echo $this->form->getInput('published'); ?></div>
        </div>
      </div>
      </fieldset>

    </div>
    <!-- End Sidebar -->
  </div> <!-- row-fluid -->
  <input type="hidden" name="task" value="" />
  <?php echo JHtml::_('form.token'); ?>
</form>
 