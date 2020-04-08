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

class jBackendModelRequest extends JModelItem
{
  /**
   * Method to get response data
   *
   * @return  array  An array with the response data or the error data
   */
  public function getItem()
  {
    $app = JFactory::getApplication('site');
    $dispatcher = JEventDispatcher::getInstance();

    JPluginHelper::importPlugin('jbackend');

    // Before check module
    $dispatcher->trigger('onBeforeCheckModule');

    $module = $app->input->getString('module');
    if (is_null($module))
    {
      return jBackendHelper::generateError('REQ_MNS'); // Module not specified
    }

    // Check request session
    $session_data = null;
    $dispatcher->trigger('onCheckSession', array($module, &$session_data));

    // Get base path and API key
    $session = JFactory::getSession();
    $access_type = $session->get('access_type');
    $enabled_modules = $session->get('enabled_modules');
    $selected_modules = $session->get('selected_modules');

    if (($enabled_modules !== '1') && (!in_array($module, $selected_modules)))
    {
      return jBackendHelper::generateError('REQ_MNF'); // Module not found
    }

    if ($access_type === 'key')
    {
      // Check API key
      $api_key = $app->input->getString('api_key');
      if (is_null($api_key))
      {
        return jBackendHelper::generateError('REQ_AKR'); // API key required
      }
      $key_status = $this->checkAPIKey($api_key); // 0 = Valid, 1 = Limit exceeded, 2 = Expired, 3 = Invalid, 4 = Generic error
      if ($key_status > 0)
      {
        switch ($key_status)
        {
          case 1: return jBackendHelper::generateError('REQ_AKL'); // API key limit exceeded
          case 2: return jBackendHelper::generateError('REQ_AKE'); // API key expired
          case 3: return jBackendHelper::generateError('REQ_AKI'); // API key invalid
          default: return jBackendHelper::generateError('REQ_AKG'); // API key generic error
        }
      }
    } else if ($access_type === 'user') {
      // Check user authentication
      if (is_null($session_data))
      {
        return jBackendHelper::generateError('REQ_UCA'); // Unable to check authentication
      }
      if ( ($session_data['is_guest']) && !($session_data['is_auth_request']) )
      {
        return jBackendHelper::generateError('REQ_AUR'); // Authentication required
      }
    }

    $response = null;
    $status = null;
    $dispatcher->trigger('onRequest'.$module, array($module, &$response, &$status));
    if (is_null($response))
    {
      if (isset($status->module_stack))
      {
        return jBackendHelper::generateError('REQ_RUN'); // Request unknown
      } else {
        return jBackendHelper::generateError('REQ_MNF'); // Module not found
      }

    }

    return $response;
  }

  public function checkAPIKey($key)
  {
    // 0 = Valid, 1 = Limit exceeded, 2 = Expired, 3 = Invalid, 4 = Generic error
    $check_result = 4; // Generic error

    $db = $this->getDbo();
    $db->setQuery("SELECT * FROM `#__jbackend_keys` WHERE `published` = '1' AND `key` = " . $db->quote($key));
    $keys = $db->loadAssocList();
    if (is_array($keys))
    {
      if (isset($keys[0]))
      {
        $now = time();
        // Check expired
        $expiration_date_timestamp = (int) strtotime($keys[0]['expiration_date']);
        if ( ($expiration_date_timestamp <= $now) && ($keys[0]['expiration_date'] != '0000-00-00 00:00:00') )
        {
          $check_result = 2; // Expired
        } else {
          // Check limit exceeded
          $today = gmdate("Y-m-d", $now);
          $current_day_time = strtotime($keys[0]['current_day']);
          $current_day = gmdate("Y-m-d", (int) $current_day_time);
          $current_hits = $keys[0]['current_hits'];

          if (($current_day_time === false) || ($current_day !== $today))
          {
            // Not valid date (e.g. 0000-00-00 00:00:00) || Restart on different day
            $current_day = $today;
            $current_hits = 0;
          }

          if ($keys[0]['daily_requests'] > 0)
          {
            // Check hits
            $check_result = ($current_hits >= $keys[0]['daily_requests']) ? 1 : 0; // 1 = Limit exceeded, 0 = Valid
          } else {
            // Unlimited
            $check_result = 0; // Valid
          }

          if ($check_result == 0)
          {
            // Update key stats
            $last_visit = date("Y-m-d H:i:s", $now);
            $current_hits++;
            $db->setQuery("UPDATE `#__jbackend_keys` SET `hits` = `hits` + 1, `last_visit` = " . $db->quote($last_visit) . ", `current_day` = " . $db->quote($current_day) . ", `current_hits` = " . $db->quote($current_hits) . " WHERE `key` = " . $db->quote($key));
            $res = @$db->query();
          }

        }
      } else {
        $check_result =3; // Invalid
      }
    }
    return $check_result;
  }

}
