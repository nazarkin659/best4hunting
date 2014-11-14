<?php
/**
 * @component AwoCoupon for Virtuemart
 * @copyright Copyright (C) Seyi Awofadeju - All rights reserved.
 * @license : GNU/GPL
 * @Website : http://awodev.com
 **/
 
// Disallow direct access to this file
defined('_JEXEC') or die('Restricted access');

$view	= JRequest::getCmd('view','');

JHTML::_('behavior.switcher');

// Load submenu's
$views	= array(
					'' 				=> JText::_('COM_AWOCOUPON_DH_DASHBOARD'),
					'coupons' 		=> JText::_('COM_AWOCOUPON_CP_COUPONS'),
					'about'			=> JText::_('COM_AWOCOUPON_AT_ABOUT'),
				);	

foreach( $views as $key => $val ) {
	$active	= ( $view == $key );
	$key= $key?'&view='.$key:'';
	JSubMenuHelper::addEntry( $val , 'index.php?option=com_awocoupon' . $key , $active );
}
