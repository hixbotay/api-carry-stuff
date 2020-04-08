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

class TableOrders extends JTable
{


	/**
	 * Construct object.
	 *
	 * @param JDatabaseMySQL $db database connector
	 */
	function __construct(& $db)
	{
		parent::__construct('#__bookpro_orders', 'id', $db);
	}

	

	function check(){
		if(!$this->created_time){
			$this->created_time	= JFactory::getDate()->toSql();
		}
		return true;
	}
	function create_unique_order_id(){

		$order = '';
		$chars = "0123456789";
		srand((double)microtime()*1000000);
		$i = 0;
		while ($i <= 5) {
			$num = rand() % 10;
			$tmp = substr($chars, $num, 1);
			$order = $order . $tmp;
			$i++;
		}
		return $order;
	}
		public function publish($pks = null, $state = 1, $userId = 0)
		{
			$k = $this->_tbl_key;
			JArrayHelper::toInteger($pks);
			$state = (int) $state;
			if (empty($pks))
			{
				if ($this->$k)
				{
					$pks = array($this->$k);
				}
				else
				{
					$this->setError(JText::_('JLIB_DATABASE_ERROR_NO_ROWS_
					SELECTED'));
					return false;
				}
			}
			$where = $k . '=' . implode(' OR ' . $k . '=', $pks);
			$query = $this->_db->getQuery(true)
			->update($this->_db->quoteName($this->_tbl))
			->set($this->_db->quoteName('state') . ' = ' . (int) $state)
			->where($where);
			$this->_db->setQuery($query);
			try
			{
				$this->_db->execute();
			}
			catch (RuntimeException $e)
			{
				$this->setError($e->getMessage());
				return false;
			}
			if (in_array($this->$k, $pks))
			{
				$this->state = $state;
			}
				$this->setError('');
				return true;
		}
	}

?>