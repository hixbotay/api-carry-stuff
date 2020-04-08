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
jimport('joomla.application.component.modeladmin');

class jBackendModelLog extends JModelAdmin
{
  /**
   * Returns a reference to the a Table object, always creating it
   *
   * @param  type  The table type to instantiate
   * @param  string  A prefix for the table class name. Optional
   * @param  array  Configuration array for model. Optional
   * @return  JTable  A database object
   */
  public function getTable($type = 'Log', $prefix = 'jBackendTable', $config = array())
  {
    return JTable::getInstance($type, $prefix, $config);
  }

  /**
   * Method to get the record form
   *
   * @param  array  $data    Data for the form
   * @param  boolean  $loadData  True if the form is to load its own data (default case), false if not
   * @return  mixed  A JForm object on success, false on failure
   */
  public function getForm($data = array(), $loadData = true)
  {
    return false;
  }

  /**
   * Prepare and sanitise the table data prior to saving
   *
   * @param  JTable  A JTable object
   */
  protected function prepareTable($table)
  {
    parent::prepareTable($table);
  }

}
