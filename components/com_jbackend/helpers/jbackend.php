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

  /*
   * Generate an error object
   *
   * $errorCode      string   Code of error to generate
   * $errorDetails   string   Error details to add to error description (optional)
   *
   * return          array    The error ('status' => 'ko', 'error_code' => <code>, 'error_description' => <description>)
   */
  public static function generateError($errorCode, $errorDetails = '')
  {
    $error = array();
    $error['status'] = 'ko';
    switch($errorCode) {
      // Module related error codes
      case 'REQ_ANS':
        $error['error_code'] = 'REQ_ANS';
        $error['error_description'] = 'Action not specified';
        break;
      case 'REQ_MNS':
        $error['error_code'] = 'REQ_MNS';
        $error['error_description'] = 'Module not specified';
        break;
      case 'REQ_MNF':
        $error['error_code'] = 'REQ_MNF';
        $error['error_description'] = 'Module not found';
        break;
      case 'REQ_RUN':
        $error['error_code'] = 'REQ_RUN';
        $error['error_description'] = 'Request unknown';
        break;
        // API key related error codes
      case 'REQ_AKR':
        $error['error_code'] = 'REQ_AKR';
        $error['error_description'] = 'API key required';
        break;
      case 'REQ_AKL':
        $error['error_code'] = 'REQ_AKL';
        $error['error_description'] = 'API key limit exceeded';
        break;
      case 'REQ_AKE':
        $error['error_code'] = 'REQ_AKE';
        $error['error_description'] = 'API key expired';
        break;
      case 'REQ_AKI':
        $error['error_code'] = 'REQ_AKI';
        $error['error_description'] = 'API key invalid';
        break;
      case 'REQ_AKG':
        $error['error_code'] = 'REQ_AKG';
        $error['error_description'] = 'API key generic error';
        break;
        // User authentication related error codes
      case 'REQ_UCA':
        $error['error_code'] = 'REQ_UCA';
        $error['error_description'] = 'Unable to check authentication';
        break;
      case 'REQ_AUR':
        $error['error_code'] = 'REQ_AUR';
        $error['error_description'] = 'Authentication required';
        break;
    }
    return $error;
  }

  /*
   * Add to module call stack
   *
   * $status      object   The object with status and call stack information
   * $module      string   The module name to add to module call stack
   *
   */
  public static function moduleStack(&$status, $module)
  {
    if (is_null($status)) { $status = new JObject; }
    if (isset($status->module_stack)) {
      if (!is_array($status->module_stack)) { $status->module_stack = array(); }
    } else {
      $status->module_stack = array();
    }
    $status->module_stack[] = (string) $module;
  }

  /*
   * Log request to database
   *
   * $request     array    The array with all the request information
   *
   */
  public static function logRequest($request)
  {
    $fields = array('endpoint' => '', 'access_type' => '', 'request_time' => '', 'request_date' => '', 'error' => '', 'error_code' => '', 'user_id' => '', 'key' => '', 'action' => '', 'module' => '', 'resource' => '', 'duration' => '');

    $request = array_merge($fields, (array)$request);

    $db = JFactory::getDbo();
    $db->setQuery("INSERT INTO #__jbackend_logs (`endpoint`, `access_type`, `request_time`, `request_date`, `error`, `error_code`, `user_id`, `key`, `action`, `module`, `resource`, `duration`) VALUES (" . $db->quote( $request['endpoint'] ) . ", " . $db->quote( $request['access_type'] ) . ", " . $db->quote( $request['request_time'] ) . ", " . $db->quote( $request['request_date'] ) . ", " . $db->quote( $request['error'] ) . ", " . $db->quote( $request['error_code'] ) . ", " . $db->quote( $request['user_id'] ) . ", " . $db->quote( $request['key'] ) . ", " . $db->quote( $request['action'] ) . ", " . $db->quote( $request['module'] ) .  ", " . $db->quote( $request['resource'] ) . ", " . $db->quote( $request['duration'] ) . ")");

    $res = @$db->query();
  }

}
