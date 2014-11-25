<?php 
/*------------------------------------------------------------------------------------------------------------
# VP One Page Checkout! Joomla 2.5 Plugin for VirtueMart 2.0 / VirtueMart 2.6
# ------------------------------------------------------------------------------------------------------------
# Copyright (C) 2012 - 2014 VirtuePlanet Services LLP. All Rights Reserved.
# License - GNU General Public License version 2. http://www.gnu.org/licenses/gpl-2.0.html
# Author: VirtuePlanet Services LLP
# Email: info@virtueplanet.com
# Websites:  http://www.virtueplanet.com
------------------------------------------------------------------------------------------------------------*/
defined ( '_JEXEC' ) or die ( 'Restricted access' );

/* Cart Page Module - 'cart-promo' */
$attribs['style'] = 'container';
$db = JFactory::getDBO();
$query = $db->getQuery(true);
$query->select('a.*');
$query->from($db->quoteName('#__modules', 'a'));
$query->where('a.published = 1');
$query->where('a.position = '.$db->quote('cart-promo'));
$query->order('a.id');
$db->setQuery($query);
$modulesList = $db->loadObjectList('id');
if(isset($modulesList)) {
	echo '<div class="clear"></div>';
	$cartModCount = count($modulesList);
	$N = 1;
	foreach($modulesList as $module) {
		$module_title = $module->title;
		$module_name = $module->module;
		if (JModuleHelper::isEnabled($module_name)) {
			$mod = JModuleHelper::getModule($module_name, $module_title);
			if($N==$cartModCount) {
				$last = ' last-mod';
			} else {
				$last = '';
			}
			echo '<div class="proopc-row"><div class="cart-promo-mod'.$last.'">'.JModuleHelper::renderModule($mod, $attribs).'</div></div>';
		} 
		$N++;				
	} 
} ?>