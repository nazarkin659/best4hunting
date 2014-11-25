<?php 
/**
 * @version     1.0.0
 * @package     com_vmreporter
 * @copyright   Copyright (C) 2013 VirtuePlanet Services LLP. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      VirtuePlanet Services LLP <info@virtueplanet.com> - http://www.virtueplanet.com
 */

defined('_JEXEC') or die;

class getNames {
	
	public function currency($id) {
		$db = JFactory::getDBO();
		$query = "SELECT currency_code_3 FROM #__virtuemart_currencies WHERE virtuemart_currency_id=".$id;
		$db->setQuery($query);
		$currency_code_3 = $db->loadResult();
		return $currency_code_3;
	}
	
	public function user($id) {
		$db = JFactory::getDBO();
		$query = "SELECT name FROM #__virtuemart_userinfos WHERE virtuemart_user_id=".$id;
		$db->setQuery($query);
		$name = $db->loadResult();
		return $name;
	}	
	
	public function shipment($id) {
		$lang = JFactory::getLanguage(); 
		$lang = str_replace("-","_",strtolower($lang->getTag()));		
		$db = JFactory::getDBO();
		$query  = "SELECT shipment_name FROM #__virtuemart_shipmentmethods_".$lang."\n";
		$query .= "WHERE virtuemart_shipmentmethod_id=".$id;
		$db->setQuery($query);
		$name = $db->loadResult();
		return $name;
	}	
	
	public function payment($id) {
		$lang = JFactory::getLanguage(); 
		$lang = str_replace("-","_",strtolower($lang->getTag()));		
		$db = JFactory::getDBO();
		$query  = "SELECT payment_name FROM #__virtuemart_paymentmethods_".$lang."\n";
		$query .= "WHERE virtuemart_paymentmethod_id=".$id;
		$db->setQuery($query);
		$name = $db->loadResult();
		return $name;
	}	
		
}

?>