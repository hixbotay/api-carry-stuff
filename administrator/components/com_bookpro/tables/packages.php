<?php
/**
 * @package 	Bookpro
 * @author 		Ngo Van Quan
 * @link 		http://joombooking.com
 * @copyright 	Copyright (C) 2011 - 2012 Ngo Van Quan
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: orders.php 56 2012-07-21 07:53:28Z quannv $
 **/

defined('_JEXEC') or die('Restricted access');

class TablePackages extends JTable
{
	/**
	 * Construct object.
	 *
	 * @param JDatabaseMySQL $db database connector
	 */
	function __construct(& $db)
	{
		parent::__construct('#__bookpro_package', 'id', $db);
	}
	
}

?>