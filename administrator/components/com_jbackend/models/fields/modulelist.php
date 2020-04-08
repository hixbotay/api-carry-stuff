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

JFormHelper::loadFieldClass('list');

require_once __DIR__ . '/../../helpers/jbackend.php';

/**
 * ModuleList Field class
 *
 * @package jBackend
 *
 */
class JFormFieldModuleList extends JFormFieldList
{
  protected $type = 'ModuleList';

  /**
   * Method to get the field options
   *
   * @return  array  The field option objects
   *
   */
  public function getOptions()
  {
    $options = jBackendHelper::getModulesOptions();

    return array_merge(parent::getOptions(), $options);
  }

}
