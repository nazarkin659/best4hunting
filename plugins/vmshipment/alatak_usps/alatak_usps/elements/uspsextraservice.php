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
class JElementUspsExtraService extends JElement {

	/**
	 * Element name
	 *
	 * @access    protected
	 * @var        string
	 */
	var $_name = 'uspsExtraService';

	function fetchElement ($name, $value, &$node, $control_name) {

		$jlang = JFactory::getLanguage ();
		$jlang->load ('plg_vmshipment_alatak_usps', JPATH_ADMINISTRATOR);

		$i = 0;

		$prefix = 'VMSHIPMENT_ALATAK_USPS_EXTRASERVICE_';
		$fields = array();
		// service = 4 does not exist
		for ($i = 0; $i < 7; $i++) {
			$key = $prefix . $i;
			if ($jlang->hasKey ($key)) {
				$fields[$i]['value'] = $i;
				$fields[$i]['text'] = JText::_ ($key);
			}
		}

		$class = 'multiple="true" size="50"';
		return JHTML::_ ('select.genericlist', $fields, $control_name . '[' . $name . '][]', $class, 'value', 'text', $value, $control_name . $name);

	}

}