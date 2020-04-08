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
 * HTML View class for the jBackend Keys component
 *
 * @package jBackend
 *
 */
class jBackendViewKeys extends JViewLegacy
{
  protected $items;
  protected $pagination;
  protected $state;

  public function display($tpl = null)
  {
    if ($this->getLayout() !== 'modal')
    {
      jBackendHelper::addSubmenu('keys');
    }

    // Initialise variables
    $this->items = $this->get('Items');
    $this->pagination = $this->get('Pagination');
    $this->state = $this->get('State');
    $this->filterForm = $this->get('FilterForm');
    $this->activeFilters = $this->get('ActiveFilters');

    // Check for errors
    if (count($errors = $this->get('Errors'))) {
      JError::raiseError(500, implode("\n", $errors));
      return false;
    }

    // We don't need toolbar in the modal window
    if ($this->getLayout() !== 'modal') {
      $this->addToolbar();
      $this->sidebar = JHtmlSidebar::render();
    }

    parent::display($tpl);
  }

  /**
   * Add the page title and toolbar
   *
   */
  protected function addToolbar()
  {
    JToolBarHelper::title(JText::_('COM_JBACKEND_MANAGER'), 'jbackend.png');

    $canDo = jBackendHelper::getActions();

    if ($canDo->get('core.create')) {
      JToolBarHelper::addNew('key.add');
    }

    if ($canDo->get('core.edit')) {
      JToolBarHelper::editList('key.edit');
    }

    if (($canDo->get('core.create')) || ($canDo->get('core.edit'))) {
      JToolBarHelper::divider();
    }

    if ($canDo->get('core.edit.state')) {
      if ($this->state->get('filter.state') != 2){
        JToolBarHelper::publish('keys.publish', 'JTOOLBAR_PUBLISH', true);
        JToolBarHelper::unpublish('keys.unpublish', 'JTOOLBAR_UNPUBLISH', true);
        JToolBarHelper::divider();
      }

      if ($this->state->get('filter.state') != -1 ) {
        if ($this->state->get('filter.state') != 2) {
          JToolBarHelper::archiveList('keys.archive');
        }
        else if ($this->state->get('filter.state') == 2) {
          JToolBarHelper::unarchiveList('keys.publish');
        }
      }

      //JToolBarHelper::checkin('keys.checkin');
      JToolBarHelper::custom('keys.checkin', 'checkin', '', 'JTOOLBAR_CHECKIN', true);
    }

    if ($this->state->get('filter.state') == -2 && $canDo->get('core.delete'))
    {
      JToolBarHelper::deleteList('', 'keys.delete', 'JTOOLBAR_EMPTY_TRASH');
      JToolBarHelper::divider();
    }
    elseif ($canDo->get('core.edit.state'))
    {
      JToolBarHelper::trash('keys.trash');
      JToolBarHelper::divider();
    }

    if ($canDo->get('core.delete'))
    {
      JToolBarHelper::custom('keys.resetstats', 'bars', '', 'COM_JBACKEND_TOOLBAR_RESET_STATS', false);
    }

    if ($canDo->get('core.admin')) {
      JToolBarHelper::preferences('com_jbackend');
    }

    JHtmlSidebar::setAction('index.php?option=com_jbackend&view=keys');

  }

  /**
   * Returns an array of fields the table can be sorted by
   *
   * @return  array  Array containing the field name to sort by as the key and display text as value
   *
   */
  protected function getSortFields()
  {
    return array(
      'k.id' => JText::_('COM_JBACKEND_HEADING_KEYS_ID'),
      'k.user_id' => JText::_('COM_JBACKEND_HEADING_KEYS_USER_ID'),
      'k.key' => JText::_('COM_JBACKEND_HEADING_KEYS_KEY'),
      'k.daily_requests' => JText::_('COM_JBACKEND_HEADING_KEYS_DAILY_REQUESTS'),
      'k.expiration_date' => JText::_('COM_JBACKEND_HEADING_KEYS_EXPIRATION_DATE'),
      'k.comment' => JText::_('COM_JBACKEND_HEADING_KEYS_COMMENT'),
      'k.hits' => JText::_('COM_JBACKEND_HEADING_KEYS_HITS'),
      'k.last_visit' => JText::_('COM_JBACKEND_HEADING_KEYS_LAST_VISIT'),
      'k.current_day' => JText::_('COM_JBACKEND_HEADING_KEYS_CURRENT_DAY'),
      'k.current_hits' => JText::_('COM_JBACKEND_HEADING_KEYS_CURRENT_HITS'),
      'k.ordering' => JText::_('JGRID_HEADING_ORDERING'),
      'k.published' => JText::_('COM_JBACKEND_HEADING_KEYS_PUBLISHED')
    );
  }

}
