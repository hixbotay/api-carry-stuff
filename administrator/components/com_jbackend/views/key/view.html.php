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

require_once JPATH_COMPONENT.'/helpers/jbackend.php';

/**
 * HTML View class for the jBackend Key component
 *
 * @package jBackend
 *
 */
class jBackendViewKey extends JViewLegacy
{
  protected $form;
  protected $item;
  protected $state;

  /**
   * Display the view
   */
  public function display($tpl = null)
  {
    // Initialiase variables.
    $this->form = $this->get('Form');
    $this->item = $this->get('Item');
    $this->state = $this->get('State');

    // Check for errors.
    if (count($errors = $this->get('Errors'))) {
      JError::raiseError(500, implode("\n", $errors));
      return false;
    }

    $this->addToolbar();
    parent::display($tpl);
  }

  /**
   * Add the page title and toolbar
   *
   */
  protected function addToolbar()
  {
    JFactory::getApplication()->input->set('hidemainmenu', true);

    $user = JFactory::getUser();
    $userId = $user->get('id');
    $isNew = ($this->item->id == 0);
    $checkedOut = !($this->item->checked_out == 0 || $this->item->checked_out == $userId);
    $canDo = jBackendHelper::getActions();

    JToolBarHelper::title($isNew ? JText::_('COM_JBACKEND_KEY_NEW') : JText::_('COM_JBACKEND_KEY_EDIT'), 'jbackend.png');

    // If not checked out, can save the item
    if (!$checkedOut && ($canDo->get('core.edit') || count($user->getAuthorisedCategories('com_jbackend', 'core.create')) > 0)) {
      JToolBarHelper::apply('key.apply');
      JToolBarHelper::save('key.save');

      if ($canDo->get('core.create')) {
        //JToolBarHelper::save2new('key.save2new');
        JToolBarHelper::custom('key.save2new', 'save-new', '', 'JTOOLBAR_SAVE_AND_NEW', false);
      }
    }

    // If an existing item, can save to a copy
    if (!$isNew && $canDo->get('core.create')) {
      //JToolBarHelper::save2copy('key.save2copy');
      JToolBarHelper::custom('key.save2copy', 'save-copy', '', 'JTOOLBAR_SAVE_AS_COPY', false);
    }

    if (empty($this->item->id)) {
      JToolBarHelper::cancel('key.cancel');
    } else {
      JToolBarHelper::cancel('key.cancel', 'JTOOLBAR_CLOSE');
    }

  }

}
