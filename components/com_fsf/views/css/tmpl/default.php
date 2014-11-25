<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;

ob_end_clean();
header('Content-type: text/css');
header("Expires: Sat, 26 Jul 2020 05:00:00 GMT");

$path = JPATH_SITE.DS.'components'.DS.'com_fsf'.DS.'assets'.DS.'css'.DS.'fsf.css';
require_once($path);

exit;