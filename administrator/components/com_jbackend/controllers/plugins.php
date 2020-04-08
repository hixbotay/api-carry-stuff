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

// import Joomla controlleradmin library
jimport('joomla.application.component.controlleradmin');

/**
 * jBackend Controller Plugins
 *
 * @package jBackend
 *
 */
class jBackendControllerPlugins extends JControllerAdmin
{
    public function __construct($config = array())
    {
        parent::__construct($config);
    }

  /**
   * Check in of one or more plugins
   *
   * @return  boolean  True on success
   *
   */
  public function checkin()
  {
    // Check for request forgeries
    JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

    // Initialise variables
    $ids = $this->input->get('cid', array(), 'array');

    JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_plugins/models');
    $model = JModelLegacy::getInstance( 'Plugin', 'PluginsModel' );
    $return = $model->checkin($ids);
    if ($return === false)
    {
      // Checkin failed
      $message = JText::sprintf('JLIB_APPLICATION_ERROR_CHECKIN_FAILED', $model->getError());
      $this->setRedirect(JRoute::_('index.php?option=com_jbackend&view=dashboard', false), $message, 'error');
      return false;
    }
    else
    {
      // Checkin succeeded
      $message = JText::plural($this->text_prefix . '_N_ITEMS_CHECKED_IN', count($ids));
      $this->setRedirect(JRoute::_('index.php?option=com_jbackend&view=dashboard', false), $message);
      return true;
    }
  }

  /**
   * Method to publish a list of items
   *
   * @return  void
   *
   */
  public function publish()
  {
    // Check for request forgeries
    JSession::checkToken() or die(JText::_('JINVALID_TOKEN'));

    // Get items to publish from the request
    $cid = $this->input->get('cid', array(), 'array');
    $data = array('publish' => 1, 'unpublish' => 0, 'archive' => 2, 'trash' => -2, 'report' => -3);
    $task = $this->getTask();
    $value = JArrayHelper::getValue($data, $task, 0, 'int');

    if (empty($cid))
    {
      JError::raiseWarning(500, JText::_($this->text_prefix . '_NO_ITEM_SELECTED'));
    }
    else
    {
      // Get the model
      JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_plugins/models');
      $model = JModelLegacy::getInstance( 'Plugin', 'PluginsModel' );

      // Make sure the item ids are integers
      JArrayHelper::toInteger($cid);

      // Publish the items.
      if (!$model->publish($cid, $value))
      {
        JError::raiseWarning(500, $model->getError());
      }
      else
      {
        if ($value == 1)
        {
          $ntext = $this->text_prefix . '_N_ITEMS_PUBLISHED';
        }
        elseif ($value == 0)
        {
          $ntext = $this->text_prefix . '_N_ITEMS_UNPUBLISHED';
        }
        elseif ($value == 2)
        {
          $ntext = $this->text_prefix . '_N_ITEMS_ARCHIVED';
        }
        else
        {
          $ntext = $this->text_prefix . '_N_ITEMS_TRASHED';
        }
        $this->setMessage(JText::plural($ntext, count($cid)));
      }
    }
    $this->setRedirect(JRoute::_('index.php?option=com_jbackend&view=dashboard', false));
  }

}
