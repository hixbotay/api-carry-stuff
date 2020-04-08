<?php
/**
 * @package 	Bookpro
 * @author 		Ngo Van Quan
 * @link 		http://joombooking.com
 * @copyright 	Copyright (C) 2011 - 2012 Ngo Van Quan
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: vehicle.php 56 2012-07-21 07:53:28Z quannv $
 **/

defined('_JEXEC') or die('Restricted access');

class TableVehicle extends JTable
{


	/**
	 * Construct object.
	 *
	 * @param JDatabaseMySQL $db database connector
	 */
	function __construct(& $db)
	{
		parent::__construct('#__bookpro_vehicle', 'id', $db);
	}

	

	function check(){
		return true;
	}
	
	function store($updateNulls = false){
		$result = parent::store($updateNulls);
		//save current vehicle to session
		if($result){
			require_once JPATH_ADMINISTRATOR.'/components/com_bookpro/classes/session.php';
			$session = new BookproSession(array('userid'=>$this->driver_id));
			$check_session = $session->loadSessionByUserId();
			if($check_session){					
				$session->setCurrentVehicle(true);
				$session->saveSession();
			}
		}		
		return $result;
	}
	
	function delete($pk = null){
		$result = parent::delete($pk);
		if($result){
			require_once JPATH_ADMINISTRATOR.'/components/com_bookpro/classes/session.php';
			$session = new BookproSession(array('userid'=>$this->driver_id));
			$check_session = $session->loadSessionByUserId();
			if($check_session){					
				$session->setCurrentVehicle(true);
				$session->saveSession();
			}
		}		
		return $result;
	}
	
	
	
	public function publish($pks = null, $state = 1, $userId = 0) {
		
			$k = $this->_tbl_key;
		
			// Sanitize input.
			JArrayHelper::toInteger ( $pks );
			$userId = ( int ) $userId;
			$state = ( int ) $state;
		
			// If there are no primary keys set check to see if the instance key is set.
			if (empty ( $pks )) {
				if ($this->$k) {
					$pks = array (
							$this->$k
					);
				} 			// Nothing to set publishing state on, return false.
				else {
					$this->setError ( JText::_ ( 'JLIB_DATABASE_ERROR_NO_ROWS_SELECTED' ) );
					return false;
				}
			}
		
			// Build the WHERE clause for the primary keys.
			$where = $k . '=' . implode ( ' OR ' . $k . '=', $pks );
		
			// Determine if there is checkin support for the table.
			if (! property_exists ( $this, 'checked_out' ) || ! property_exists ( $this, 'checked_out_time' )) {
				$checkin = '';
			} else {
				$checkin = ' AND (checked_out = 0 OR checked_out = ' . ( int ) $userId . ')';
			}
		
			// Update the publishing state for rows with the given primary keys.
			$this->_db->setQuery ( 'UPDATE ' . $this->_db->quoteName ( $this->_tbl ) . ' SET ' . $this->_db->quoteName ( 'default' ) . ' = ' . ( int ) $state . ' WHERE (' . $where . ')' . $checkin );
		
			try {
				$this->_db->execute ();
			} catch ( RuntimeException $e ) {
				$this->setError ( $e->getMessage () );
				return false;
			}
		
			// If checkin is supported and all rows were adjusted, check them in.
			if ($checkin && (count ( $pks ) == $this->_db->getAffectedRows ())) {
				// Checkin the rows.
				foreach ( $pks as $pk ) {
					$this->checkin ( $pk );
				}
			}
		
			// If the JTable instance value is in the list of primary keys that were set, set the instance.
			if (in_array ( $this->$k, $pks )) {
				$this->state = $state;
			}
		
			$this->setError ( '' );
			return true;
		}
		
		
		/*public function activate()
		{
		// Check for request forgeries.
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		$ids = $this->input->get('cid', array(), 'array');

		if (empty($ids))
		{
			JError::raiseWarning(500, JText::_('COM_USERS_USERS_NO_ITEM_SELECTED'));
		}
		else
		{
			// Get the model.
			$model = $this->getModel();

			// Change the state of the records.
			if (!$model->activate($ids))
			{
				JError::raiseWarning(500, $model->getError());
			}
			else
			{
				$this->setMessage(JText::plural('COM_USERS_N_USERS_ACTIVATED', count($ids)));
			}
		}

		$this->setRedirect('index.php?option=com_bookpro&view=vehicles');
	}*/
		
}

?>