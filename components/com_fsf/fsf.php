<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;

if (!function_exists("print_p"))
{
	function print_p($var)
	{
		echo "<pre>";
		print_r($var);
		echo "</pre>";	
	}
}

if (!defined("DS")) define('DS', DIRECTORY_SEPARATOR);

if (!JDEBUG)
{
	error_reporting(E_ALL & ~E_STRICT & ~E_NOTICE & ~E_WARNING);
}

require_once( JPATH_COMPONENT.DS.'helper'.DS.'j3helper.php' );

// Require the base controller
 
require_once( JPATH_COMPONENT.DS.'controller.php' );
require_once( JPATH_COMPONENT.DS.'helper'.DS.'helper.php' );
require_once( JPATH_COMPONENT.DS.'helper'.DS.'settings.php' );

// Require specific controller if requested
if($controller = JRequest::getWord('controller')) {
    $path = JPATH_COMPONENT.DS.'controllers'.DS.$controller.'.php';
    if (file_exists($path)) {
        require_once $path;
    } else {
        $controller = '';
    }
}

// Create the controller
$classname    = 'FsfController'.$controller;
$controller   = new $classname( );

$css = JURI::root(true) . "/index.php?option=com_fsf&view=css&layout=default";
$document = JFactory::getDocument();
$document->addStyleSheet($css); 
FSF_Helper::IncludeJQuery();

// Perform the Request task
$task = JRequest::getVar( 'task' );
if ($task == "captcha_image")
{
	ob_clean();
	require_once (JPATH_SITE.DS.'components'.DS.'com_fsf'.DS.'helper'.DS.'captcha.php');
	$cap = new FSF_Captcha();
	$cap->GetImage();
	exit;
} else {
	$controller->execute( $task );

	// Redirect if set by the controller
	$controller->redirect();
}
?>
