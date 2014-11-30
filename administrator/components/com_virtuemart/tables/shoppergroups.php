<?php
/**
 * Shopper group data access object.
 *
 * @package	VirtueMart
 * @subpackage ShopperGroup
 * @author Markus Öhler
 * @author Max Milbers
 * @copyright Copyright (c) 2009-2014 VirtueMart Team. All rights reserved.
 */

defined('_JEXEC') or die();

if(!class_exists('VmTable'))require(JPATH_VM_ADMINISTRATOR.DS.'helpers'.DS.'vmtable.php');


class TableShoppergroups extends VmTable {

	var $virtuemart_shoppergroup_id	 = 0;
	var $virtuemart_vendor_id = 0;
	var $shopper_group_name  = '';
	var $shopper_group_desc  = '';
	var $custom_price_display = 0;
	var $price_display		= '';
	var $default = 0;
	var $published = 0;

	function __construct(&$db) {
		parent::__construct('#__virtuemart_shoppergroups', 'virtuemart_shoppergroup_id', $db);

		$this->setUniqueName('shopper_group_name');
		$this->setLoggable();
		$this->setTableShortCut('sg');
	}

}
// pure php no closing tag
