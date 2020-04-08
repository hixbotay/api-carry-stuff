<?php

/**
 * @package 	Bookpro
 * @author 		Ngo Van Quan
 * @link 		http://joombooking.com
 * @copyright 	Copyright (C) 2011 - 2012 Ngo Van Quan
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: controller.php 16 2012-06-26 12:45:19Z quannv $
 **/

defined('_JEXEC') or die('Restricted access');

//import needed Joomla! libraries
jimport('joomla.application.component.controller');
//import needed JoomLIB helpers
AImporter::helper('bookpro','request');

class AController extends JControllerLegacy
{
    /**
     * String name of controller usable in request data.
     * 
     * @var string
     */
    var $_controllerName;
    /**
     * Sign if after satisfied task do redirect on another page.
     * 
     * @var boolean
     */
    var $_doRedirect;

    function __construct($config = array())
    {
        parent::__construct($config);
        $this->_doRedirect = true;
    }

    function execute($task)
    {
        parent::execute($task);
        
    
		//echo BookProHelper::get();
		
    }

    /**
     * Add new object.
     */
    function add()
    {
        if (IS_SITE) {
            JRequest::setVar('view', 'reservation');
            JRequest::setVar('layout', 'form');
            parent::display();
        } elseif (IS_ADMIN) {
            $this->editing();
        }
    }

    /**
     * Edit existing object.
     */
    function edit()
    {
        $this->editing();
    }

    /**
     * Copy existing subject
     */
    function copy()
    {
        $this->editing();
    }

    /**
     * Open editing form page.
     * 
     * @param string $view name of view edit form
     */
    function editing($view)
    {
        JRequest::setVar('hidemainmenu', 1);
        JRequest::setVar('layout', 'form');
        JRequest::setVar('view', $view);
        $id = ARequest::getCid();
        $this->_model->setId($id);
        $this->_model->checkout();
        parent::display();
    }

    /**
     * Save object and state on edit page.
     */
    function apply()
    {
        $this->save(true);
    }

    /**
     * Save object.
     * 
     * @param boolean $apply true state on edit page, false return to browse list
     */
    function save($apply = false)
    {
        JRequest::checkToken() or jexit('Invalid Token');
        $post = JRequest::get('post');
        $post['id'] = ARequest::getCid();
        $mainframe = &JFactory::getApplication();
        $id = $this->_model->store($post);
        if ($id !== false) {
            $mainframe->enqueueMessage(JText::_('Successfully saved'), 'message');
            if ($apply) {
                ARequest::redirectEdit($this->_controllerName, $id);
            } else {
                ARequest::redirectList($this->_controllerName);
            }
        }
    }

    /**
     * Cancel edit operation. Check in object and redirect to objects list. 
     */
    function cancel($msg)
    {
        $mainframe = &JFactory::getApplication();
        $id = ARequest::getCid();
        if ($id) {
            $this->_model->setId($id);
            $this->_model->checkin();
        }
        $mainframe->enqueueMessage(JText::_($msg));
        ARequest::redirectList($this->_controllerName);
    }

    /**
     * Set object state by choosen operation.
     * 
     * @param string $operation
     */
    function state($operation, $checkToken = true, $redirect = true)
    {
        if ($checkToken)
            JRequest::checkToken() or jexit('Invalid Token');
        $mainframe = &JFactory::getApplication();
        /* @var $mainframe JApplication */
        if (ARequest::controlCids(($cids = ARequest::getCids()), $operation)) {
            if (($success = $this->_model->$operation($cids)) && $this->_doRedirect)
                $mainframe->enqueueMessage(JText::_('Successfully ' . $operation), 'message');
            elseif (! $success && $this->_doRedirect)
                $mainframe->enqueueMessage(JText::_($operation . ' failed'), 'error');
        }
        if ($this->_doRedirect && $redirect)
            ARequest::redirectList($this->_controllerName);
    }

    /**
     * Remove trashed objects.
     */
    function emptyTrash()
    {
        JRequest::checkToken() or jexit('Invalid Token');
        $mainframe = &JFactory::getApplication();
        if ($this->_model->emptyTrash()) {
            $mainframe->enqueueMessage(JText::_('Successfully emptied trash'), 'message');
        } else {
            $mainframe->enqueueMessage(JText::_('Empty trash failed'), 'error');
        }
        ARequest::redirectList($this->_controllerName);
    }

    
    function setTextProperties(&$object, $text)
    {
        $pattern = '#<hr\s+id=("|\')system-readmore("|\')\s*\/*>#i';
        $tagPos = preg_match($pattern, $text);
        if ($tagPos == 0) {
            $object->introtext = $text;
        } else {
            list ($object->introtext, $object->fulltext) = preg_split($pattern, $text, 2);
        }
    }

    function setEditorProperties(&$object)
    {
        if (JString::strlen($object->fulltext) > 1) {
            $object->text = $object->introtext . '<hr id="system-readmore" />' . $object->fulltext;
        } else {
            $object->text = $object->introtext;
        }
    }
}

?>