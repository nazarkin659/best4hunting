<?php
/**
 * YjContactUs entry point file for YjContactUs Component
 * 
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

// Access check.
if (!JFactory::getUser()->authorise('core.manage', 'com_yjcontactus')) 
{
	return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
}

// require helper file
JLoader::register('YjContactUSHelper', dirname(__FILE__) . DS . 'helpers' . DS . 'helper.php');
require_once JPATH_COMPONENT.'/helpers/helper.php';

// import joomla controller library
jimport('joomla.application.component.controller');

// Get an instance of the controller prefixed by YjContactUS
$controller = JController::getInstance('YjContactUS');

// Perform the Request task
$controller->execute(JRequest::getCmd('task'));

// Redirect if set by the controller
$controller->redirect();
?>