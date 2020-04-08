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
* jBackend Key Table class
*
* @package JBackend
*
*/
class jBackendTableKey extends JTable
{
  public function __construct(& $db) {
    parent::__construct('#__jbackend_keys', 'id', $db);
  }

  public function check() {
    /** check for unique key */
    $query = 'SELECT `id` FROM `#__jbackend_keys` WHERE `key` = ' . $this->_db->quote($this->key);
    $this->_db->setQuery($query);

    $xid = intval($this->_db->loadResult());
    if ($xid && $xid != intval($this->id)) {
      $this->setError(JText::_('COM_JBACKEND_WARNING_DUPLICATED_KEY'));
      return false;
    }
    return true;
  }

}
