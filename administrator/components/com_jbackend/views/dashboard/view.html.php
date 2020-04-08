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
 * HTML View class for the jBackend Dashboard component
 *
 * @package jBackend
 *
 */
class jBackendViewDashboard extends JViewLegacy
{
  protected $items;
  protected $pagination;
  protected $state;
  protected $modulelist;
  protected $stats;

  public function display($tpl = null)
  {
    if ($this->getLayout() !== 'modal')
    {
      jBackendHelper::addSubmenu('dashboard');
    }

    // Initialise variables
    $this->items = $this->get('Items');
    $this->pagination = $this->get('Pagination');
    $this->state = $this->get('State');
    $this->modulelist = jBackendHelper::getModules();
    $this->stats = jBackendHelper::getStats();
    $this->filterForm = $this->get('FilterForm');
    $this->activeFilters = $this->get('ActiveFilters');

    // Check for errors
    if (count($errors = $this->get('Errors'))) {
      JError::raiseError(500, implode("\n", $errors));
      return false;
    }

    // We don't need toolbar in the modal window.
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

    require_once JPATH_COMPONENT.'/helpers/jbackend.php';
    $canDo = jBackendHelper::getActions();

    if ($canDo->get('core.admin')) {
      JToolBarHelper::preferences('com_jbackend');
    }

    JHtmlSidebar::setAction('index.php?option=com_jbackend&view=dashboard');

  }

}
