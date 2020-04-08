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

/**
 * jBackend Helper
 *
 * @package jBackend
 *
 */
class jBackendHelper
{
  /**
   * A persistent cache of jbackend modules
   *
   * @var    array
   */
  protected static $modules = null;

  /**
   * A persistent cache of jbackend stats
   *
   * @var    array
   */
  protected static $stats = null;

  /**
   * Configure the Linkbar
   *
   * @param string The name of the active view
   *
   * @return void
   */
  public static function addSubmenu($vName)
  {
    $document = JFactory::getDocument();
    $document->addStyleDeclaration('.icon-48-jbackend {background-image: url(../administrator/components/com_jbackend/images/icon-48-jbackend.png);}');

    JHtmlSidebar::addEntry(
      JText::_('COM_JBACKEND_MENU_DASHBOARD'),
      'index.php?option=com_jbackend&view=dashboard',
      $vName == 'dashboard'
    );

    JHtmlSidebar::addEntry(
      JText::_('COM_JBACKEND_MENU_KEYS'),
      'index.php?option=com_jbackend&view=keys',
      $vName == 'keys'
    );

    JHtmlSidebar::addEntry(
      JText::_('COM_JBACKEND_MENU_LOGS'),
      'index.php?option=com_jbackend&view=logs',
      $vName == 'logs'
    );

  }

  /**
   * Gets a list of the actions that can be performed
   *
   * @return JObject
   */
  public static function getActions()
  {
    $user = JFactory::getUser();
    $result = new JObject;

    $assetName = 'com_jbackend';

    $actions = array(
      'core.admin', 'core.manage', 'core.create', 'core.edit', 'core.edit.state', 'core.delete'
    );

    foreach ($actions as $action) {
      $result->set($action,  $user->authorise($action, $assetName));
    }

    return $result;
  }

  /**
   * Return utf-8 substrings
   *
   * http://www.php.net/manual/en/function.substr.php#90148
   *
   */
  public static function substru($str, $from, $len)
  {
    return preg_replace('#^(?:[\x00-\x7F]|[\xC0-\xFF][\x80-\xBF]+){0,'. $from .'}'.'((?:[\x00-\x7F]|[\xC0-\xFF][\x80-\xBF]+){0,'. $len .'}).*#s','$1', $str);
  }

  public static function trimText($text_to_trim, $max_chars = '50')
  {
    $to_be_continued = '...';

    if ( (function_exists('mb_strlen')) && (function_exists('mb_substr')) )
    {
      // MultiByte version
      if( mb_strlen( $text_to_trim, 'UTF-8' ) > $max_chars ) {
        return mb_substr( $text_to_trim, 0, $max_chars, 'UTF-8' ) . $to_be_continued;
      } else {
        return $text_to_trim;
      }
    } else {
      // Safe version
      $text_trimmed = self::substru($text_to_trim, 0, $max_chars);
      if ( strlen($text_trimmed) < strlen ($text_to_trim) )
      {
        return $text_trimmed . $to_be_continued;
      } else {
        return $text_to_trim;
      }
    }
  }

  public static function getModules()
  {
    if (self::$modules !== null)
    {
      return self::$modules;
    }

    $user = JFactory::getUser();
    $cache = JFactory::getCache('jbackend_modules', 'output');

    $levels = implode(',', $user->getAuthorisedViewLevels());

    if (!(self::$modules = $cache->get($levels)))
    {
      $db = JFactory::getDbo();
      $query = $db->getQuery(true);

      $query->select('a.*')
        ->from('#__extensions AS a')
        ->where('a.type =' . $db->Quote('plugin'))
        ->where('a.folder =' . $db->Quote('jbackend'))
        ->where('a.access IN (' . $levels . ')')
        ->order('a.ordering');

      // Join over the users for the checked out user
      $query->select('uc.name AS editor');
      $query->join('LEFT', '#__users AS uc ON uc.id = a.checked_out');

      // Join over the asset groups
      $query->select('ag.title AS access_level');
      $query->join('LEFT', '#__viewlevels AS ag ON ag.id = a.access');

      self::$modules = $db->setQuery($query)->loadObjectList();

      if ($error = $db->getErrorMsg())
      {
        JError::raiseWarning(500, $error);
        return false;
      }

      $cache->store(self::$modules, $levels);
    }

    return self::$modules;
  }

  public static function getStats($refresh = false)
  {
    if ( (self::$stats !== null) && (!$refresh) )
    {
      return self::$stats;
    }

    $cache = JFactory::getCache('jbackend_modules', 'output');

    if ( ($refresh) || !(self::$stats = $cache->get('stats')) )
    {
      self::$stats = array(
        'total_endpoints' => 0,
        'active_endpoints' => 0,
        'total_requests' => 0,
        'total_requests_ok' => 0,
        'total_requests_ko' => 0,
        'last_30_days_requests' => 0,
        'last_7_days_requests' => 0,
        'last_24_hours_requests' => 0,
        'average_response_time' => 0,
        'min_response_time' => 0,
        'max_response_time' => 0
      );

      $db = JFactory::getDbo();

      // Endpoints
      $query = $db->getQuery(true);
      $query->select('published, COUNT(*) AS total')
        ->from('#__menu')
        ->where('link = \'index.php?option=com_jbackend&view=request\'')
        ->group('published');

      $endpoints = $db->setQuery($query)->loadObjectList();
      foreach ($endpoints as $e) {
        self::$stats['total_endpoints'] += $e->total;
        if ($e->published == 1) {
          self::$stats['active_endpoints'] += $e->total;
        }
      }

      // Requests
      $query = $db->getQuery(true);
      $query->select('error, COUNT(*) AS total')
        ->from('#__jbackend_logs')
        ->group('error');

      $requests = $db->setQuery($query)->loadObjectList();
      foreach ($requests as $r) {
        self::$stats['total_requests'] += $r->total;
        if ($r->error == 0) {
          self::$stats['total_requests_ok'] += $r->total;
        }
      }
      self::$stats['total_requests_ko'] = self::$stats['total_requests'] - self::$stats['total_requests_ok'];

      // Last 30 days requests
      $query = $db->getQuery(true);
      $query->select('COUNT(*) AS total')
        ->from('#__jbackend_logs')
        ->where('request_time >= ( NOW() - INTERVAL 30 DAY )')
        ->where('error = 0');

      $last_30 = $db->setQuery($query)->loadObject();
      self::$stats['last_30_days_requests'] = $last_30->total;

      // Last 7 days requests
      $query = $db->getQuery(true);
      $query->select('COUNT(*) AS total')
        ->from('#__jbackend_logs')
        ->where('request_time >= ( NOW() - INTERVAL 7 DAY )')
        ->where('error = 0');

      $last_7 = $db->setQuery($query)->loadObject();
      self::$stats['last_7_days_requests'] = $last_7->total;

      // Last 24 hours requests
      $query = $db->getQuery(true);
      $query->select('COUNT(*) AS total')
        ->from('#__jbackend_logs')
        ->where('request_time >= ( NOW() - INTERVAL 1 DAY )')
        ->where('error = 0');

      $last_24 = $db->setQuery($query)->loadObject();
      self::$stats['last_24_hours_requests'] = $last_24->total;

      // Response time
      $query = $db->getQuery(true);
      $query->select('MIN(duration) AS rtmin, MAX(duration) AS rtmax, AVG(duration) AS rtavg')
        ->from('#__jbackend_logs')
        ->where('error = 0');

      $response_time = $db->setQuery($query)->loadObject();
      self::$stats['average_response_time'] = $response_time->rtavg;
      self::$stats['min_response_time'] = $response_time->rtmin;
      self::$stats['max_response_time'] = $response_time->rtmax;

      $cache->store(self::$stats, 'stats');
    }

    return self::$stats;
  }

  /**
   * Get installed module list in text/value format for a select field
   *
   * @return  array
   */
  public static function getModulesOptions()
  {
    $options = array();

    $db = JFactory::getDbo();
    $query = $db->getQuery(true)
      ->select('element AS value, name AS text')
      ->from('#__extensions AS e')
      ->where('e.type = "plugin"')
      ->where('e.folder = "jbackend"')
      ->order('e.name');

    // Get the options.
    $db->setQuery($query);

    try
    {
      $options = $db->loadObjectList();
    }
    catch (RuntimeException $e)
    {
      JError::raiseWarning(500, $e->getMessage());
    }

    return $options;
  }

}
