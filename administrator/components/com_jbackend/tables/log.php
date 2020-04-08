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

// import Joomla table library
jimport('joomla.database.table');

/**
* jBackend Log Table class
*
* @package JBackend
*
*/
class jBackendTableLog extends JTable
{
  public function __construct(& $db) {
    parent::__construct('#__jbackend_logs', 'id', $db);
  }
}
