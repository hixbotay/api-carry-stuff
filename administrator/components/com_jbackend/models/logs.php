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

// import the Joomla modellist library
jimport('joomla.application.component.modellist');

class jBackendModelLogs extends JModelList
{
  public function __construct($config = array())
  {
    if (empty($config['filter_fields'])) {
      $config['filter_fields'] = array(
        'id', 'l.id',
        'endpoint', 'l.endpoint',
        'access_type', 'l.access_type',
        'request_time', 'l.request_time',
        'request_date', 'l.request_date',
        'error', 'l.error',
        'error_code', 'l.error_code',
        'user_id', 'l.user_id',
        'key', 'l.key',
        'action', 'l.action',
        'module', 'l.module',
        'resource', 'l.resource',
        'duration', 'l.duration'
      );
    }

    parent::__construct($config);
  }

  /**
   * Method to auto-populate the model state
   *
   * Note. Calling getState in this method will result in recursion
   */
  protected function populateState($ordering = null, $direction = null)
  {
    $search = $this->getUserStateFromRequest($this->context.'.filter.search', 'filter_search', '', 'string');
    // Convert search to lower case
    $search = JString::strtolower($search);
    $this->setState('filter.search', $search);

    // Load the parameters.
    $params = JComponentHelper::getParams('com_jbackend');
    $this->setState('params', $params);

    // List state information.
    parent::populateState('l.id', 'desc');
  }

  /**
   * Method to build an SQL query to load the list data
   *
   * @return string An SQL query
   */
  protected function getListQuery()
  {
    // Create a new query object
    $db = $this->getDbo();
    $query = $db->getQuery(true);
    // Select required fields
    $query->select(
      $this->getState(
        'list.select',
        'l.id, l.endpoint, l.access_type, l.request_time, l.request_date, l.error, l.error_code, l.user_id, l.key, l.action, l.module, l.resource, l.duration'
      )
    );

    // From the table
    $query->from('#__jbackend_logs AS l');

    // Join with the user
    $query->join('LEFT', $db->quoteName('#__users') . ' lu ON lu.id=l.user_id')
      ->select('lu.name as user_name, lu.username as user_username, lu.email as user_email');

    // Filter by search
    $search = $this->getState('filter.search');
    if (!empty($search)) {
      $query->where('(LOWER(l.error_code) LIKE '.$db->quote('%'.$db->escape($search, true).'%').' OR LOWER(l.key) LIKE '.$db->quote('%'.$db->escape($search, true).'%').')');
    }

    // Add the list ordering clause
    $orderCol = $this->state->get('list.ordering', 'l.id');
    $orderDirn = $this->state->get('list.direction', 'desc');
    $query->order($db->escape($orderCol.' '.$orderDirn));

    return $query;
  }

}
