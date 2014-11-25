<?php

/**
 * PDF Invoicing Solution for VirtueMart & Joomla!
 * 
 * @package   VM Invoice
 * @version   2.0.31
 * @author    ARTIO http://www.artio.net
 * @copyright Copyright (C) 2011 ARTIO s.r.o. 
 * @license   GNU/GPLv3 http://www.artio.net/license/gnu-general-public-license
 */



// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restrict Access');

// Initialize helper, classes, languages and temp. folder
require_once (JPATH_ADMINISTRATOR . '/components/com_vminvoice/helpers/invoicehelper.php');

// Load the CSS
$cssPath = JPATH_COMPONENT_ADMINISTRATOR.'/assets/css/';
$cssUrl = 'components/com_vminvoice/assets/css/';
$document = JFactory::getDocument();
$document->addStyleSheet($cssUrl.'general.css?v='.@filemtime($cssPath.'general.css'));
if (COM_VMINVOICE_ISJ30)
	$document->addStyleSheet($cssUrl.'joomla3.css?v='.@filemtime($cssPath.'joomla3.css'));

//load controller
if (($controller = JRequest::getWord('controller'))) {
    $controllerFile = JPATH_COMPONENT_ADMINISTRATOR . '/'. 'controllers' . '/'. $controller . '.php';
    if (file_exists($controllerFile)) {
        require_once ($controllerFile);
    } else {
        require_once (JPATH_COMPONENT_ADMINISTRATOR . '/'. 'controller.php');
        $controller = '';
    }
} else {
    require_once (JPATH_COMPONENT_ADMINISTRATOR . '/'. 'controller.php');
}

$classname = 'VMInvoiceController' . ucfirst($controller);

$task = JRequest::getWord('task');
$controller = new $classname();
$controller->execute($task);
$controller->redirect();
