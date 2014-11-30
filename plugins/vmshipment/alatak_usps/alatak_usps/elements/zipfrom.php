<?php
defined ('_JEXEC') or die();
/**
 *
 * @package    VirtueMart
 * @subpackage Plugins  - Elements
 * @author Valérie Isaksen
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
class JElementZipfrom extends JElement {

	/**
	 * Element name
	 *
	 * @access    protected
	 * @var        string
	 */
	var $_name = 'zipfrom';

	function fetchElement ($name, $value, &$node, $control_name) {

		if (empty($value)) {
			if (!class_exists ('VirtueMartModelVendor')) {
				require(JPATH_VM_ADMINISTRATOR . DS . 'models' . DS . 'vendor.php');
			}
			$vendorId = 1;
			$vendormodel = VmModel::getModel ('vendor');
			$vendorAdress = $vendormodel->getVendorAdressBT ($vendorId);
			preg_match_all ('#[0-9]+#', $vendorAdress->zip, $zip);
			$value = $zip[0][0];
		}

		return '<input type="text" name="params[' . $name . ']" id="params' . $name . '" value="' . $value . '" class="text_area" size="50">';

	}

}