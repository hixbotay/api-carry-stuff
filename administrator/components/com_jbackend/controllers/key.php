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

jimport('joomla.application.component.controllerform');

/**
 * jBackend Controller Key
 *
 * @package jBackend
 *
 */
class jBackendControllerKey extends JControllerForm
{
  public function save($key = null, $urlVar = null)
  {
    // Check for request forgeries.
    JRequest::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

    $data = $this->input->post->get('jform', array(), 'array');
    // Trim blanks from string fields
    $data['key'] = trim($data['key']);
    $data = $this->input->post->set('jform', $data);

    return parent::save($key, $urlVar);
  }

}
