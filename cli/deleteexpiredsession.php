<?php
/**
 * @package    Joomla.Cli
 *
 * @copyright  Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

// Initialize Joomla framework
const _JEXEC = 1;
error_reporting(E_ALL);
ini_set('display_errors', 1);
// Load system defines
if (file_exists(dirname(__DIR__) . '/defines.php'))
{
	require_once dirname(__DIR__) . '/defines.php';
}

if (!defined('_JDEFINES'))
{
	define('JPATH_BASE', dirname(__DIR__));
	require_once JPATH_BASE . '/includes/defines.php';
}

// Import the configuration.
require_once JPATH_CONFIGURATION.'/configuration.php';
// Get the framework.
require_once JPATH_LIBRARIES . '/import.legacy.php';

// Bootstrap the CMS libraries.
require_once JPATH_LIBRARIES . '/cms.php';

/**
 * Cron job to trash expired cache data.
 *
 * @since  2.5
 */
$day = 3600*24;
$time = (int)( JFactory::getDate()->toUnix() -  $day );
$db = JFactory::getDbo();
$query = $db->getQuery(true);
//delete session expired
$query->delete('#__bookpro_session')
	->where('time < '.$time);
$db->setQuery($query);
$db->execute();
$query->clear();
//delete pn token expired
$db->setQuery('delete pn.* from #__bookpro_customer_pn as pn left join #__bookpro_session as s ON s.userid=pn.user_id where s.userid IS NULL');
$db->execute();
exit;	
