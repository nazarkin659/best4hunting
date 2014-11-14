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
if (!class_exists( 'VmConfig' )) require(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_virtuemart'.DS.'helpers'.DS.'config.php');
if (!class_exists('ShopFunctions'))
    require(JPATH_VM_ADMINISTRATOR . DS . 'helpers' . DS . 'shopfunctions.php');
class JElementCountryfrom extends JElement {

	/**
	 * Element name
	 *
	 * @access    protected
	 * @var        string
	 */
	var $_name = 'Countryfrom';

	function fetchElement ($name, $value, &$node, $control_name) {

		if (empty($value)) {
			if (!class_exists ('VirtueMartModelVendor')) {
				require(JPATH_VM_ADMINISTRATOR . DS . 'models' . DS . 'vendor.php');
			}
			$vendorId = 1;
			$vendormodel = VmModel::getModel ('vendor');
			$vendorAdress = $vendormodel->getVendorAdressBT ($vendorId);
			$value = shopFunctions::getCountryByID ($vendorAdress->virtuemart_country_id, 'country_2_code');

		}

		$db = JFactory::getDBO ();
		$query = 'SELECT `country_2_code` AS value, `country_name` AS text FROM `#__virtuemart_countries`
               		WHERE `published` = 1 ORDER BY `country_name` ASC ';

		$db->setQuery ($query);
		$fields = $db->loadObjectList ();

		$class = '';
		return JHTML::_ ('select.genericlist', $fields, $control_name . '[' . $name . '][]', $class, 'value', 'text', $value, $control_name . $name);

	}

}