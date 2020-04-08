<?php
/**
 * @version $Id: script.php 10 2012-06-26 12:37:51Z quannv $
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
class com_bookproInstallerScript
{
	/**
	 * method to install the component
	 *
	 * @return void
	 */
	function install($parent) 
	{
		// $parent is the class calling this method
		$parent->getParent()->setRedirectURL('index.php?option=com_bookpro');
	}
 
	/**
	 * method to uninstall the component
	 *
	 * @return void
	 */
	function uninstall($parent) 
	{
		// $parent is the class calling this method
		echo '<p>' . JText::_('COM_BOOKPRO_UNINSTALL_TEXT') . '</p>';
	}
 
	/**
	 * method to update the component
	 *
	 * @return void
	 */
	function update($parent) 
	{
		// $parent is the class calling this method
		//echo '<p>' . JText::_('COM_BOOKPRO_UPDATE_TEXT') . '</p>';
	}
 
	/**
	 * method to run before an install/update/uninstall method
	 *
	 * @return void
	 */
	function preflight($type, $parent)
	{
		$jversion = new JVersion();

		// Installing component manifest file version
		$this->release = $parent->get( "manifest" )->version;

		// Manifest file minimum Joomla version
		$this->minimum_joomla_release = $parent->get( "manifest" )->attributes()->version;

		// Show the essential information at the install/update back-end
		echo '<p>Installing component manifest file version = ' . $this->release;
		//echo '<br />Current manifest cache commponent version = ' . $this->getParam('version');
		echo '<br />Installing component manifest file minimum Joomla version = ' . $this->minimum_joomla_release;
		echo '<br />Current Joomla version = ' . $jversion->getShortVersion();

		// abort if the current Joomla release is older
		if( version_compare( $jversion->getShortVersion(), $this->minimum_joomla_release, 'lt' ) ) {
			Jerror::raiseWarning(null, 'Cannot install in a Joomla release prior to '.$this->minimum_joomla_release);
			return false;
		}
		/*
		 // abort if the component being installed is not newer than the currently installed version
		 if ( $type == 'update' ) {
			$oldRelease = $this->getParam('version');
			$rel = $oldRelease . ' to ' . $this->release;
			if ( version_compare( $this->release, $oldRelease, 'le' ) ) {
			Jerror::raiseWarning(null, 'Incorrect version sequence. Cannot upgrade ' . $rel);
			return false;
			}
			}
			else { $rel = $this->release; }

			echo '<p>' . JText::_('COM_BOOKPRO_PREFLIGHT_' . $type . ' ' . $rel) . '</p>';
			*/
		if(in_array($type, array('install','discover_install'))) {
			$this->_bugfixDBFunctionReturnedNoError();
		} else {
			$this->_bugfixCantBuildAdminMenus();
		}
	}
 
	/**
	 * method to run after an install/update/uninstall method
	 *
	 * @return void
	 */
	function postflight($type, $parent) 
	{
		$db = JFactory::getDBO();
		$app = JFactory::getApplication('site');
		
		$status = new stdClass;
		$status->modules = array();
		$status->plugins = array();
		
		$src = $parent->getParent()->getPath('source');
		$manifest = $parent->getParent()->manifest;
		$plugins = $manifest->xpath('plugins/plugin');
		//install module
		$modules = $manifest->xpath('modules/module');
		foreach ($modules as $module)
		{
			$name = (string)$module->attributes()->module;
			$client = (string)$module->attributes()->client;
			if (is_null($client))
			{
				$client = 'site';
			}
			($client == 'administrator') ? $path = $src.'/administrator/modules/'.$name : $path = $src.'/modules/'.$name;
			$installer = new JInstaller;
			$result = $installer->install($path);
			$status->modules[] = array('name' => $name, 'client' => $client, 'result' => $result);
		}
		//install plugin
		foreach ($plugins as $plugin)
		{
			$name = (string)$plugin->attributes()->plugin;
			$group = (string)$plugin->attributes()->group;
			$path = $src.'/plugins/'.$group;
			
			if (JFolder::exists($src.'/plugins/'.$group.'/'.$name))
			{
				$path = $src.'/plugins/'.$group.'/'.$name;
			}
			
			$installer = new JInstaller;
			$result = $installer->install($path);
			$query = "UPDATE #__extensions SET enabled=1 WHERE type='plugin' AND element=".$db->Quote($name)." AND folder=".$db->Quote($group);
			$db->setQuery($query);
			$db->query();
			$status->plugins[] = array('name' => $name, 'group' => $group, 'result' => $result);
		}
		$this->printstatus($status);
	}
	
	private function _bugfixCantBuildAdminMenus()
	{
		$db = JFactory::getDbo();

		// If there are multiple #__extensions record, keep one of them
		$query = $db->getQuery(true);
		$query->select('extension_id')
		->from('#__extensions')
		->where($db->qn('element').' = '.$db->q($this->_extension_name));
		$db->setQuery($query);
		$ids = $db->loadColumn();
		if(count($ids) > 1) {
			asort($ids);
			$extension_id = array_shift($ids); // Keep the oldest id

			foreach($ids as $id) {
				$query = $db->getQuery(true);
				$query->delete('#__extensions')
				->where($db->qn('extension_id').' = '.$db->q($id));
				$db->setQuery($query);
				$db->query();
			}
		}

		// @todo

		// If there are multiple assets records, delete all except the oldest one
		$query = $db->getQuery(true);
		$query->select('id')
		->from('#__assets')
		->where($db->qn('name').' = '.$db->q($this->_extension_name));
		$db->setQuery($query);
		$ids = $db->loadObjectList();
		if(count($ids) > 1) {
			asort($ids);
			$asset_id = array_shift($ids); // Keep the oldest id

			foreach($ids as $id) {
				$query = $db->getQuery(true);
				$query->delete('#__assets')
				->where($db->qn('id').' = '.$db->q($id));
				$db->setQuery($query);
				$db->query();
			}
		}

		// Remove #__menu records for good measure!
		$query = $db->getQuery(true);
		$query->select('id')
		->from('#__menu')
		->where($db->qn('type').' = '.$db->q('component'))
		->where($db->qn('menutype').' = '.$db->q('main'))
		->where($db->qn('link').' LIKE '.$db->q('index.php?option='.$this->_extension_name));
		$db->setQuery($query);
		$ids1 = $db->loadColumn();
		if(empty($ids1)) $ids1 = array();
		$query = $db->getQuery(true);
		$query->select('id')
		->from('#__menu')
		->where($db->qn('type').' = '.$db->q('component'))
		->where($db->qn('menutype').' = '.$db->q('main'))
		->where($db->qn('link').' LIKE '.$db->q('index.php?option='.$this->_extension_name.'&%'));
		$db->setQuery($query);
		$ids2 = $db->loadColumn();
		if(empty($ids2)) $ids2 = array();
		$ids = array_merge($ids1, $ids2);
		if(!empty($ids)) foreach($ids as $id) {
			$query = $db->getQuery(true);
			$query->delete('#__menu')
			->where($db->qn('id').' = '.$db->q($id));
			$db->setQuery($query);
			$db->query();
		}
	}
	private function _bugfixDBFunctionReturnedNoError()
	{
		$db = JFactory::getDbo();

		// Fix broken #__assets records
		$query = $db->getQuery(true);
		$query->select('id')
		->from('#__assets')
		->where($db->qn('name').' = '.$db->q($this->_extension_name));
		$db->setQuery($query);
		$ids = $db->loadColumn();
		if(!empty($ids)) foreach($ids as $id) {
			$query = $db->getQuery(true);
			$query->delete('#__assets')
			->where($db->qn('id').' = '.$db->q($id));
			$db->setQuery($query);
			$db->query();
		}

		// Fix broken #__extensions records
		$query = $db->getQuery(true);
		$query->select('extension_id')
		->from('#__extensions')
		->where($db->qn('element').' = '.$db->q($this->_extension_name));
		$db->setQuery($query);
		$ids = $db->loadColumn();
		if(!empty($ids)) foreach($ids as $id) {
			$query = $db->getQuery(true);
			$query->delete('#__extensions')
			->where($db->qn('extension_id').' = '.$db->q($id));
			$db->setQuery($query);
			$db->query();
		}

		// Fix broken #__menu records
		$query = $db->getQuery(true);
		$query->select('id')
		->from('#__menu')
		->where($db->qn('type').' = '.$db->q('component'))
		->where($db->qn('menutype').' = '.$db->q('main'))
		->where($db->qn('link').' LIKE '.$db->q('index.php?option='.$this->_extension_name));
		$db->setQuery($query);
		$ids = $db->loadColumn();
		if(!empty($ids)) foreach($ids as $id) {
			$query = $db->getQuery(true);
			$query->delete('#__menu')
			->where($db->qn('id').' = '.$db->q($id));
			$db->setQuery($query);
			$db->query();
		}
	}
	
	function printstatus($status){
		
		?>
		<table class="adminlist table table-striped">
		<thead>
		<tr>
		<th class="title" colspan="2"><?php echo JText::_('Joombooking extension'); ?></th>
					                    <th width="30%"><?php echo JText::_('Status'); ?></th>
					                </tr>
					            </thead>
					            <tfoot>
					                <tr>
					                    <td colspan="3"></td>
					                </tr>
					            </tfoot>
					            <tbody>
					                <tr class="row0">
					                    <td class="key" colspan="2"><?php echo 'Joombooking '.JText::_('JB Flight'); ?></td>
					                    <td><strong><?php echo JText::_('INSTALLED'); ?></strong></td>
					                </tr>
					                <?php if (count($status->modules)): ?>
					                <tr>
					                    <th><?php echo JText::_('Modules'); ?></th>
					                    <th><?php echo JText::_('Client'); ?></th>
					                    <th></th>
					                </tr>
					                <?php foreach ($status->modules as $module): ?>
					                <tr class="row<?php echo(++$rows % 2); ?>">
					                    <td class="key"><?php echo $module['name']; ?></td>
					                    <td class="key"><?php echo ucfirst($module['client']); ?></td>
					                    <td><strong><?php echo ($module['result'])?JText::_('INSTALLED'):JText::_('NOT INSTALLED'); ?></strong></td>
					                </tr>
					                <?php endforeach; ?>
					                <?php endif; ?>
					                <?php if (count($status->plugins)): ?>
					                <tr>
					                    <th><?php echo JText::_('Plugins'); ?></th>
					                    <th><?php echo JText::_('Group'); ?></th>
					                    <th></th>
					                </tr>
					                <?php foreach ($status->plugins as $plugin): ?>
					                <tr class="row<?php echo(++$rows % 2); ?>">
					                    <td class="key"><?php echo ucfirst($plugin['name']); ?></td>
					                    <td class="key"><?php echo ucfirst($plugin['group']); ?></td>
					                    <td><strong><?php echo ($module['result'])?JText::_('INSTALLED'):JText::_('NOT INSTALLED'); ?></strong></td>
					                </tr>
					                <?php endforeach; ?>
					                <?php endif; ?>
					            </tbody>
					        </table>
		<?php 
		
	}
}