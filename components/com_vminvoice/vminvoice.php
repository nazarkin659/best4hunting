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

// Component Helper
jimport('joomla.application.component.helper');

// load classes and language
require_once (JPATH_ADMINISTRATOR .'/components/com_vminvoice/helpers/invoicehelper.php');

// Permissions check
$orderUserId = null;
if ($cid = JRequest::getVar('cid'))
	$orderUserId = InvoiceGetter::getOrderUserId($cid);

$user = &JFactory::getUser();
$authorized = false;
// is admin or super admin
if (COM_VMINVOICE_ISJ16){
	if ($user->authorise('core.admin', 'com_vminvoice'))
		$authorized=true;
}
elseif($user->usertype == "Super Administrator" OR $user->usertype == "Administrator")
	$authorized=true;

// user is related to the order
if ($orderUserId AND $orderUserId == $user->get('id'))
	$authorized=true;

//TODO: in booking, what of called with session in url (guest resevation)

// redirect handling
if (($controller = JRequest::getWord('controller')) 
AND JRequest::getVar('controller') == 'invoices' 
AND (JRequest::getVar('task') == 'pdf' OR JRequest::getVar('task') == 'pdf_dn') 
AND $authorized) {
	
	//if enabled in config, generate in CURRENT language (not user default or frontend default)
	$params = invoiceHelper::getParams();
	if (!JRequest::getVar('invoice_language') && $params->get('frontend_current_lang')){
		$language = JFactory::getLanguage();
		JRequest::setVar('invoice_language',$language->get('tag'));
	}
	
    require_once(JPATH_COMPONENT_ADMINISTRATOR.'/controllers/'.$controller.'.php');    
    $classname = 'VMInvoiceController'.$controller;
    $controller = new $classname();
}
else {
    require_once(JPATH_COMPONENT.'/controller.php');
    $controller = new VMInvoiceController();
}

$controller->execute(JRequest::getVar('task'));
$controller->redirect();
