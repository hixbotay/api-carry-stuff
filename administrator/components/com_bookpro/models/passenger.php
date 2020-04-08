   <?php
   
   /**
    * @package 	Bookpro
    * @author 		Ngo Van Quan
    * @link 		http://joombooking.com
    * @copyright 	Copyright (C) 2011 - 2012 Ngo Van Quan
    * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
    * @version 	$Id: bookpro.php 80 2012-08-10 09:25:35Z quannv $
    **/
   

 defined('_JEXEC') or die('Restricted access');
/**
* @version		$Id:passenger.php  1 2014-03-15 18:20:26Z Quan $
* @package		Bookpro1
* @subpackage 	Models
* @copyright	Copyright (C) 2014, Ngo Van Quan. All rights reserved.
* @license #http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
*/
 defined('_JEXEC') or die('Restricted access');
/**
 * Bookpro1ModelPassenger 
 * @author Ngo Van Quan
 */
 
class BookproModelPassenger  extends JModelAdmin { 

		
/**
	 * Method to get the record form.
	 *
	 * @param   array    $data      Data for the form. [optional]
	 * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not. [optional]
	 *
	 * @return  mixed  A JForm object on success, false on failure

	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_bookpro.passenger', 'passenger', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form))
		{
			return false;
		}
		return $form;
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return  mixed  The data for the form.
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$app  = JFactory::getApplication();
		$data = $app->getUserState('com_bookpro.edit.passenger.data', array());

		if (empty($data))
		{
			$data = $this->getItem();
		
		}
		
		if(!version_compare(JVERSION,'3','<')){
			$this->preprocessData('com_bookpro.passenger', $data);
		}
		

		return $data;
	}
	function deleteByCustomerID($customeID){
		$query='DELETE FROM ' . $this->_table->getTableName(). ' ';
		$query.= 'WHERE `customer_id` = ' . $customeID;
		$this->_db->setQuery($query);
		if (!$this->_db->query()) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		return true;
	}
	function deleteByOrderID($orderID){
		$query='DELETE FROM ' . $this->_table->getTableName(). ' ';
		$query.= 'WHERE `order_id` = ' . $orderID;
		$this->_db->setQuery($query);
		if (!$this->_db->query()) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		return true;
	}
	
}
?>