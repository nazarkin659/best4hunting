<?php

/**
 *
 * @package    VirtueMart
 * @subpackage Plugins  - Elements
 * @author Val?rie Isaksen
 * @link http://www.virtuemart.net
 * @copyright Copyright (c) 2004 - 2011 VirtueMart Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * @version $Id: $
 */

/*
 * This class is used by VirtueMart Payment or Shipping Plugins
 * which uses JParameter
 * So It should be an extension of JElement
 * Those plugins cannot be configured througth the Plugin Manager anyway.
 */
class JElementUspsCurl extends JElement {

	/**
	 * Element name
	 *
	 * @access    protected
	 * @var        string
	 */
	var $_name = 'uspscurl';

	function fetchElement ($name, $value, &$node, $control_name) {

		$jlang = JFactory::getLanguage ();
		$jlang->load ('com_virtuemart', JPATH_ADMINISTRATOR);

		if (!function_exists ('curl_init')) {
			return JTExt::_ ('VMSHIPMENT_ALATAK_USPS_CURL_LIBRARY_NOT_INSTALLED');
		} else {
			return JTExt::_ ('VMSHIPMENT_ALATAK_USPS_CURL_LIBRARY_INSTALLED');
		}
	}

}