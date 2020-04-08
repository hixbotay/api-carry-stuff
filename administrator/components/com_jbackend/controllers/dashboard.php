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

// import Joomla controlleradmin library
jimport('joomla.application.component.controlleradmin');

/**
 * jBackend Controller Dashboard
 *
 * @package jBackend
 *
 */
class jBackendControllerDashboard extends JControllerAdmin
{
    public function __construct($config = array())
    {
        parent::__construct($config);
    }
}
