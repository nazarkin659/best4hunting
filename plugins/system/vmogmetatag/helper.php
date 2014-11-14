<?php defined ('_JEXEC') or  die('Direct Access to ' . basename (__FILE__) . ' is not allowed.');

# VM OG Meta Tag - System Plugin
# Version	: 1.1
# Copyright (C) 2013 VirtuePlanet Services LLP. All Rights Reserved.
# License	: GNU General Public License version 2. http://www.gnu.org/licenses/gpl-2.0.html
# Author	: VirtuePlanet Services LLP
# Email		: info@virtueplanet.com
# Websites: www.virtueplanet.com

if (!class_exists ('VmConfig')) {
	require(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_virtuemart' . DS . 'helpers' . DS . 'config.php');
}

VmConfig::loadConfig ();
JFactory::getLanguage ()->load ('com_virtuemart');

if (!class_exists ('calculationHelper')) {
	require(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_virtuemart' . DS . 'helpers' . DS . 'calculationh.php');
}
if (!class_exists ('CurrencyDisplay')) {
	require(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_virtuemart' . DS . 'helpers' . DS . 'currencydisplay.php');
}
if (!class_exists ('VirtueMartModelVendor')) {
	require(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_virtuemart' . DS . 'models' . DS . 'vendor.php');
}
if (!class_exists ('VmImage')) {
	require(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_virtuemart' . DS . 'helpers' . DS . 'image.php');
}
if (!class_exists ('shopFunctionsF')) {
	require(JPATH_SITE . DS . 'components' . DS . 'com_virtuemart' . DS . 'helpers' . DS . 'shopfunctionsf.php');
}
if (!class_exists ('calculationHelper')) {
	require(JPATH_COMPONENT_SITE . DS . 'helpers' . DS . 'cart.php');
}
if (!class_exists ('VirtueMartModelProduct')) {
	JLoader::import ('product', JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_virtuemart' . DS . 'models');
}