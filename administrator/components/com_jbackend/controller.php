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

// import Joomla controller library
jimport('joomla.application.component.controller');

/**
 * General Controller of jBackend component
 */
class jBackendController extends JControllerLegacy
{
  /**
   * @var    string  The default view.
   */
  protected $default_view = 'dashboard';

  /**
   * Display view
   *
   * @return void
   */
  public function display($cachable = false, $urlparams = false)
  {
    $view = $this->input->get('view', 'dashboard');
    $layout = $this->input->get('layout', 'default');
    $id = $this->input->getInt('id');

    // Check for edit form
    if ($view == 'key' && $layout == 'edit' && !$this->checkEditId('com_jbackend.edit.key', $id)) {
      // Somehow the person just went to the form - we don't allow that
      $this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_UNHELD_ID', $id));
      $this->setMessage($this->getError(), 'error');
      $this->setRedirect(JRoute::_('index.php?option=com_jbackend&view=keys', false));

      return false;
    }

    // Call parent behavior
    parent::display();

    return $this;
  }

}
