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

class jBackendModelKeys extends JModelList
{
  public function __construct($config = array())
  {
    if (empty($config['filter_fields'])) {
      $config['filter_fields'] = array(
        'id', 'k.id',
        'key', 'k.key',
        'user_id', 'k.user_id',
        'user_name', 'ku.user_name',
        'user_username', 'ku.user_username',
        'user_email', 'ku.user_email',
        'daily_requests', 'k.daily_requests',
        'expiration_date', 'k.expiration_date',
        'comment', 'k.comment',
        'hits', 'k.hits',
        'last_visit', 'k.last_visit',
        'current_day', 'k.current_day',
        'current_hits', 'k.current_hits',
        'ordering', 'k.ordering',
        'published', 'k.published',
        'checked_out', 'k.checked_out',
        'checked_out_time', 'k.checked_out_time'
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
    $state = $this->getUserStateFromRequest($this->context.'.filter.state', 'filter_state', '', 'string');
    $this->setState('filter.state', $state);

    $search = $this->getUserStateFromRequest($this->context.'.filter.search', 'filter_search', '', 'string');
    // Convert search to lower case
    $search = JString::strtolower($search);
    $this->setState('filter.search', $search);

    // Load the parameters.
    $params = JComponentHelper::getParams('com_jbackend');
    $this->setState('params', $params);

    // List state information.
    parent::populateState('k.ordering', 'asc');
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
        'k.id, k.key, k.user_id, k.daily_requests, k.expiration_date, k.comment, k.hits, k.last_visit, DATE_FORMAT(k.current_day, "%Y-%m-%d") as current_day, k.current_hits, k.ordering, k.published, k.checked_out, k.checked_out_time'
      )
    );

    // From the table
    $query->from('#__jbackend_keys AS k');

    // Join with the user
    $query->join('LEFT', $db->quoteName('#__users') . ' ku ON ku.id=k.user_id')
      ->select('ku.name as user_name, ku.username as user_username, ku.email as user_email');

    // Join over the users for the checked out user
    $query->select('uc.name AS editor');
    $query->join('LEFT', '#__users AS uc ON uc.id = k.checked_out');

    // Filter by state
    $state = $this->getState('filter.state');
    if (is_numeric($state)) {
      $query->where('(k.published = '.(int) $state.')');
    } else if ($state === '') {
      $query->where('(k.published IN (0, 1))'); // By default only published and unpublished
    }

    // Filter by search
    $search = $this->getState('filter.search');
    if (!empty($search)) {
      $query->where('(LOWER(k.key) LIKE '.$db->quote('%'.$db->escape($search, true).'%').' OR LOWER(k.comment) LIKE '.$db->quote('%'.$db->escape($search, true).'%').')');
    }

    // Add the list ordering clause
    $orderCol = $this->state->get('list.ordering', 'k.ordering');
    $orderDirn = $this->state->get('list.direction', 'asc');
    $query->order($db->escape($orderCol.' '.$orderDirn));

    return $query;
  }

}
