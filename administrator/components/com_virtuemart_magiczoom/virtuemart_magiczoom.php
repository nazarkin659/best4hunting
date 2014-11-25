<?php

/*------------------------------------------------------------------------
# mod_virtuemart_magiczoom - Magic Zoom for Joomla with VirtueMart
# ------------------------------------------------------------------------
# Magic Toolbox
# Copyright 2011 MagicToolbox.com. All Rights Reserved.
# @license - http://www.opensource.org/licenses/artistic-license-2.0  Artistic License 2.0 (GPL compatible)
# Website: http://www.magictoolbox.com/magiczoom/modules/joomla/
# Technical Support: http://www.magictoolbox.com/contact/
/*-------------------------------------------------------------------------*/

// no direct access
defined('_JEXEC') or die('Restricted access.');

defined('DS') or define('DS', DIRECTORY_SEPARATOR);

require_once JPATH_COMPONENT.DS.'helpers'.DS.'helper.php';

//NOTE: Access check.
if(method_exists('JUser', 'authorise')) {
    if(!JFactory::getUser()->authorise('core.manage', 'com_virtuemart_magiczoom')) {
        return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
    }
} else {
    //NOTE: For Joomla 1.5.x
    $acl = JFactory::getACL();
    $acl->addACL('com_virtuemart_magiczoom', 'manage', 'users', 'super administrator');
    $acl->addACL('com_virtuemart_magiczoom', 'manage', 'users', 'administrator');
    if(!JFactory::getUser()->authorize('com_virtuemart_magiczoom', 'manage')) {
        $mainframe->redirect( 'index.php', JText::_('ALERTNOTAUTH') );
    }
}

//NOTE: Import Joomla controller library
jimport('joomla.application.component.controller');

if(!defined('MAGICTOOLBOX_LEGACY_CONTROLLER_DEFINED')) {
    define('MAGICTOOLBOX_LEGACY_CONTROLLER_DEFINED', true);
    if(JVERSION_256) {
        class MagicToolboxLegacyController extends JControllerLegacy {}
    } else {
        class MagicToolboxLegacyController extends JController {}
    }
}

if(method_exists('MagicToolboxLegacyController', 'getInstance')) {
    //NOTE: Get an instance of the controller
    $controller = MagicToolboxLegacyController::getInstance('Virtuemart_Magiczoom');
} else {
    //NOTE: For Joomla 1.5.x

    //NOTE: Require the base controller
    require_once(JPATH_COMPONENT.DS.'controller.php');

    //NOTE: Require specific controller if requested
    if($controller = JRequest::getCmd('controller', '')) {
        $path = JPATH_COMPONENT.DS.'controllers'.DS.$controller.'.php';
        if(file_exists($path)) {
            require_once $path;
        } else {
            $controller = '';
        }
    }

    //NOTE: Create the controller
    //$controller = new Virtuemart_MagiczoomController(array('default_task' => 'display'));
    $classname  = 'Virtuemart_MagiczoomController'.ucfirst($controller);
    $controller = new $classname();
}

$task = JRequest::getCmd('task' , 'display');
$controller->execute($task);
$controller->redirect();
