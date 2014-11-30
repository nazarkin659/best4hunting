<?php
/**
 * YjContactUS entry point file for YjContactUS Component
 * 
 */

// no direct access
defined('_JEXEC') or die;

// Include dependancies
jimport('joomla.application.component.controller');
//require_once JPATH_COMPONENT.'/helpers/mail.php';
//require_once JPATH_COMPONENT.'/helpers/query.php';

$controller = JController::getInstance('YjContactUS');
$controller->execute(JRequest::getCmd('task'));
$controller->redirect();

?>