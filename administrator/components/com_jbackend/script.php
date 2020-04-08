<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * Script file of HelloWorld component
 */
class com_jBackendInstallerScript
{
  /**
   * Recursively delete a directory
   *
   * @param string $dir Directory name
   * @param boolean $deleteRootToo Delete specified top-level directory as well
   *
   * @return void
   */
  public static function unlinkRecursive($dir, $deleteRootToo = false)
  {
    if(!$dh = @opendir($dir))
    {
      return;
    }
    while (false !== ($obj = readdir($dh)))
    {
      if($obj == '.' || $obj == '..')
      {
        continue;
      }

      if (!@unlink($dir . '/' . $obj))
      {
        self::unlinkRecursive($dir.'/'.$obj, true);
      }
    }

    closedir($dh);

    if ($deleteRootToo)
    {
       @rmdir($dir);
    }

    return;
  }

  /**
   * Method to install the component
   *
   * @return void
   */
  function install($parent)
  {
    // $parent is the class calling this method
    $parent->getParent()->setRedirectURL('index.php?option=com_jbackend');
  }

  /**
   * Method to uninstall the component
   *
   * @return void
   */
  function uninstall($parent)
  {
    // $parent is the class calling this method
    echo '<p>' . JText::_('COM_JBACKEND_UNINSTALL_TEXT') . '</p>';
  }

  /**
   * Method to update the component
   *
   * @return void
   */
  function update($parent)
  {
    // $parent is the class calling this method
    echo '<p>' . JText::sprintf('COM_JBACKEND_UPDATE_TEXT', $parent->get('manifest')->version) . '</p>';
  }

  /**
   * Method to run before an install/update/uninstall method
   *
   * @return void
   */
  function preflight($type, $parent)
  {
    // $parent is the class calling this method
    // $type is the type of change (install, update or discover_install)
    echo '<p>' . JText::_('COM_JBACKEND_PREFLIGHT_' . $type . '_TEXT') . '</p>';
  }

  /**
   * Method to run after an install/update/uninstall method
   *
   * @return void
   */
  function postflight($type, $parent)
  {
    // $parent is the class calling this method
    // $type is the type of change (install, update or discover_install)
    echo '<p>' . JText::_('COM_JBACKEND_POSTFLIGHT_' . $type . '_TEXT') . '</p>';
  }
}