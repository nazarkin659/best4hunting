<?php

/**
 * @package    	com_vm_soa (WebServices for virtuemart)
 * @author		Mickael Cabanas (cabanas.mickael|at|gmail.com)
 * @link 		http://www.virtuemart-datamanager.com
 * @license    	GNU/GPL
*/

defined('_JEXEC') or die('Restricted access');


 error_reporting(E_ALL);
 ini_set("display_errors", 1);
 ini_set("memory_limit","-1");


/**
 * Fonction d'interception des erreurs utilisée pour la mise au point
 */
function myErrorHandler($errno, $errstr, $errfile, $errline){
//	echo "<br>---> An error occured";
    switch ($errno) {
    case E_ERROR:
		echo "<br>---> E_ERROR";
		echo "<pre>";
		////debug_print_backtrace();
		echo "</pre>";
        break;
    case E_WARNING:
//		echo "<br>---> E_WARNING";
//		echo "<pre>";
//		//debug_print_backtrace();
//		echo "</pre>";
        break;
    case E_PARSE:
		echo "<br>---> E_PARSE";
		echo "<pre>";
		//debug_print_backtrace();
		echo "</pre>";
        break;
    case E_NOTICE:
		echo "<br>---> E_NOTICE";
		echo "<pre>";
		//debug_print_backtrace();
		echo "</pre>";
        break;
    case E_CORE_ERROR:
		echo "<br>---> E_CORE_ERROR";
		echo "<pre>";
		//debug_print_backtrace();
		echo "</pre>";
        break;
    case E_CORE_WARNING:
		echo "<br>---> E_CORE_WARNING";
		echo "<pre>";
		//debug_print_backtrace();
		echo "</pre>";
        break;
    case E_COMPILE_ERROR:
		echo "<br>---> E_COMPILE_ERROR";
		echo "<pre>";
		//debug_print_backtrace();
		echo "</pre>";
        break;
    case E_COMPILE_WARNING:
		echo "<br>---> E_COMPILE_WARNING";
		echo "<pre>";
		//debug_print_backtrace();
		echo "</pre>";
        break;
    case E_USER_ERROR:
		echo "<br>---> E_USER_ERROR";
		echo "<pre>";
		//debug_print_backtrace();
		echo "</pre>";
        if ($errstr == "(SQL)"){
            // handling an sql error
            echo "<b>SQL Error</b> [$errno] " . SQLMESSAGE . "<br />\n";
            echo "Query : " . SQLQUERY . "<br />\n";
            echo "On line " . SQLERRORLINE . " in file " . SQLERRORFILE . " ";
            echo ", PHP " . PHP_VERSION . " (" . PHP_OS . ")<br />\n";
            echo "Aborting...<br />\n";
        } else {
            echo "<b>My ERROR</b> [$errno] $errstr<br />\n";
            echo "  Fatal error on line $errline in file $errfile";
            echo ", PHP " . PHP_VERSION . " (" . PHP_OS . ")<br />\n";
            echo "Aborting...<br />\n";
        }
        exit(1);
        break;

    case E_USER_WARNING:
		echo "<br>---> E_USER_WARNING";
		echo "<pre>";
		//debug_print_backtrace();
		echo "</pre>";
        break;
   case E_USER_NOTICE:
		echo "<br>---> E_USER_NOTICE";
		echo "<pre>";
		//debug_print_backtrace();
		echo "</pre>";
        break;
    }
   /* Don't execute PHP internal error handler */
    return true;
}


require_once (JPATH_COMPONENT.DS.'controller.php');
// On récupère la variable atc (action du menu initialisée dans le xml de configuration) pour aiguiller sur le controller de l'action par défaut on utilise le controller qui porte le nom du composant
// Certains composants récupèrent la variable controller : il semble que c'est une variable gérée par le composant qui la positionne en paramètre dans les url ou les formulaires
$controller = JRequest::getVar('act', 'vm_soa');

//require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'controllers'.DS.$controller.'.php');
require_once(JPATH_COMPONENT.DS.'controllers'.DS.$controller.'.php');
$classname = 'vm_soaController'.$controller;

$controller = new $classname( );

// Ici c'est act qui nous sert d'aiguillage
$controller->execute( JRequest::getVar('act', 'vm_soa'));
$controller->redirect();

?>

