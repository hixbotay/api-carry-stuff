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
 * jBackend Controller Keys
 *
 * @package jBackend
 *
 */
class jBackendControllerKeys extends JControllerAdmin
{
    public function resetstats()
    {
        // Check for request forgeries
        JRequest::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        $model = $this->getModel('key');
        if(!$model->resetstats()) {
          $message = JText::sprintf('COM_JBACKEND_ERROR_RESETSTATS_FAILED', $model->getError());
          $this->setRedirect(JRoute::_('index.php?option=com_jbackend&view=keys', false), $message, 'error');
        } else {
          $this->setRedirect( 'index.php?option=com_jbackend&view=keys' );
        }
    }

    /**
     * Proxy for getModel
     */
    public function getModel($name = 'Key', $prefix = 'jBackendModel', $config = array('ignore_request' => true))
    {
      $model = parent::getModel($name, $prefix, $config);

      return $model;
    }

    /**
     * Method to save the submitted ordering values for records via AJAX.
     *
     * @return  void
     *
     */
    public function saveOrderAjax()
    {
      $pks = $this->input->post->get('cid', array(), 'array');
      $order = $this->input->post->get('order', array(), 'array');

      // Sanitize the input
      JArrayHelper::toInteger($pks);
      JArrayHelper::toInteger($order);

      // Get the model
      $model = $this->getModel();

      // Save the ordering
      $return = $model->saveorder($pks, $order);

      if ($return)
      {
        echo "1";
      }

      // Close the application
      JFactory::getApplication()->close();
    }

}
