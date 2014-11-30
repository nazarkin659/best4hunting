<?php

defined ('_JEXEC') or die('Restricted access');

/**
*  This Virtuemart shipping plugin is used with a production USPS Shipping Account
*  to obtain the proper shipping rates and options from USPS using the weight of the
*  products in the cart. 
*
* @version v6.4.1 2014/11/26 by Park Beach Systems, Inc.
* @package VirtueMart
* @subpackage shipping
* @copyright Copyright (C) 2014 Park Beach Systems, Inc. All rights reserved.
* @license GNU General Public License version 3, or later http://www.gnu.org/licenses/gpl.html
*/
if (!class_exists ('vmPSPlugin')) {
	require(JPATH_VM_PLUGINS . DS . 'vmpsplugin.php');
}
define('JPATH_VMPPBSUSPSPLUGIN', JPATH_ROOT . DS . 'plugins' . DS . 'vmshipment' . DS . 'usps_ship');
if (!class_exists ('LAFFPack')) {
	require (JPATH_VMPPBSUSPSPLUGIN . DS . 'api' . DS . 'laff-pack.php');
}

/**
* This is the Shipping class to call the USPS API for shipping costs
*/
class plgVmShipmentUSPS_ship extends vmPSPlugin {
	var $usps_username;
	var $usps_password;
	var $usps_server;
	var $usps_path;
	var $usps_proxyserver;
	var $usps_reporterrors;
	var $usps_machinable;
	var $usps_padding;
	var $usps_packagesize;
	var $ship_service = array();
	var $ship_postage = array();
	var $shipmethod_rate = array(); //stores rates by shipmethodid
	var $usps_errored = false;
	var $usps_flatbox_avail = array();
		
	// instance of class
	public static $_this = FALSE;

	/**
	 * @param object $subject
	 * @param array  $config
	 */
	function __construct (& $subject, $config) {
		//$this->logInfo('__construct');
		parent::__construct ($subject, $config);

		$this->_loggable = TRUE;
		$this->_tablepkey = 'id';
		$this->_tableId = 'id';
		$this->tableFields = array_keys ($this->getTableSQLFields ());
		$varsToPush = $this->getVarsToPush ();
		$this->setConfigParameterable ($this->_configTableFieldName, $varsToPush);
		$this->_debug = $this->params->get( 'debug', 0);
		$this->usps_username = $this->params->get( 'USPS_USERNAME', '' );
		$this->usps_password = $this->params->get( 'USPS_PASSWORD', '' );
		$this->usps_server = $this->params->get( 'USPS_SERVER', '' ); //"http://proxy.shr.secureserver.net:3128";
		//$this->usps_path = $this->params->get( 'USPS_PATH', '' );
		$this->usps_path = '/ShippingAPI.dll';
		$this->usps_proxyserver = $this->params->get( 'USPS_PROXYSERVER', '' );
		$this->usps_reporterrors = $this->params->get( 'USPS_REPORTERRORS', 1 );
		$this->usps_packagesize = $this->params->get( 'USPS_PACKAGESIZE', 'REGULAR' );
		$this->usps_padding = $this->params->get( 'USPS_PADDING', '' );
		$this->usps_machinable = $this->params->get( 'USPS_MACHINABLE', 0 );
		$this->usps_smart_flatrate = $this->params->get( 'USPS_SMART_FLATRATE', 0 );
	}
	/**
	* Create USPS shipment table for this plugin
	*/
	public function getVmPluginCreateTableSQL () {
		
		return $this->createTableSQL ('Shipment USPS Table');
	}

	/**
	 * @return array
	 */
	function getTableSQLFields () {
		$SQLfields = array(
			'id'                           => 'int(1) UNSIGNED NOT NULL AUTO_INCREMENT',
			'virtuemart_order_id'          => 'int(11) UNSIGNED',
			'order_number'                 => 'char(32)',
			'virtuemart_shipmentmethod_id' => 'mediumint(1) UNSIGNED',
			'shipment_name'                => 'varchar(5000)',
			'order_weight'                 => 'decimal(10,4)',
			'shipment_weight_unit'         => 'char(3) DEFAULT \'LB\'',
			'shipment_cost'                => 'decimal(10,2)',
			'shipment_package_fee'         => 'decimal(10,2)',
			'tax_id'                       => 'smallint(1)'
		);
		return $SQLfields;
	}

	/**
	 * This method is fired when showing the order details in the frontend.
	 * It displays the shipment-specific data.
	 */
	public function plgVmOnShowOrderFEShipment ($virtuemart_order_id, $virtuemart_shipmentmethod_id, &$shipment_name) {
		//$this->logInfo('plgVmOnShowOrderFEShipment');
		$this->onShowOrderFE ($virtuemart_order_id, $virtuemart_shipmentmethod_id, $shipment_name);
	}

	/**
	 * This event is fired after the order has been stored; it gets the shipment method-
	 * specific data.
	 */
	function plgVmConfirmedOrder (VirtueMartCart $cart, $order) {
		//$this->logInfo('ConfirmedOrder');
		if (!($method = $this->getVmPluginMethod ($order['details']['BT']->virtuemart_shipmentmethod_id))) {
			return NULL; // Another method was selected, do nothing
		}
		if (!$this->selectedThisElement ($method->shipment_element)) {
			return FALSE;
		}
		
		$methodSalesPrice = $_SESSION['cart']['usps_ship_rate'];
		$methodHandlingFee = $this->_getHandlingCost($method, $methodSalesPrice, $cart->pricesUnformatted['salesPrice']);
		
		$values['virtuemart_order_id'] = $order['details']['BT']->virtuemart_order_id;
		$values['order_number'] = $order['details']['BT']->order_number;
		$values['virtuemart_shipmentmethod_id'] = $order['details']['BT']->virtuemart_shipmentmethod_id;
		$values['shipment_name'] = $this->renderPluginName ($method);
		$values['order_weight'] = $this->getOrderWeight ($cart, 'LB');
		$values['shipment_weight_unit'] = 'LB';
		$values['shipment_cost'] = $methodSalesPrice;
		$values['shipment_package_fee'] = $methodHandlingFee;
		$values['tax_id'] = $method->tax_id;
		$this->storePSPluginInternalData ($values);

		return TRUE;
	}

	/**
	 * This method is fired when showing the order details in the backend.
	 * It displays the shipment-specific data.
	 */
	public function plgVmOnShowOrderBEShipment ($virtuemart_order_id, $virtuemart_shipmentmethod_id) {
		//$this->logInfo('plgVmOnShowOrderBEShipment');
		if (!($this->selectedThisByMethodId ($virtuemart_shipmentmethod_id))) {
			return NULL;
		}
		$html = $this->getOrderShipmentHtml ($virtuemart_order_id);
		return $html;
	}

	/**
	 * This method displays the shipping data stored in the method's table.
	 */
	function getOrderShipmentHtml ($virtuemart_order_id) {
		//$this->logInfo('getOrderShipmentHtml');
		$db = JFactory::getDBO ();
		$q = 'SELECT * FROM `' . $this->_tablename . '` '
			. 'WHERE `virtuemart_order_id` = ' . $virtuemart_order_id;
		$db->setQuery ($q);
		if (!($shipinfo = $db->loadObject ())) {
			vmWarn (500, $q . " " . $db->getErrorMsg ());
			return '';
		}

		if (!class_exists ('CurrencyDisplay')) {
			require(JPATH_VM_ADMINISTRATOR . DS . 'helpers' . DS . 'currencydisplay.php');
		}

		$currency = CurrencyDisplay::getInstance ();
		$tax = ShopFunctions::getTaxByID ($shipinfo->tax_id);
		$taxDisplay = is_array ($tax) ? $tax['calc_value'] . ' ' . $tax['calc_value_mathop'] : $shipinfo->tax_id;
		$taxDisplay = ($taxDisplay == -1) ? JText::_ ('COM_VIRTUEMART_PRODUCT_TAX_NONE') : $taxDisplay;
			
		$html = '<table class="adminlist">' . "\n";
		$html .= $this->getHtmlHeaderBE ();
		$html .= $this->getHtmlRowBE ('USPS_SHIP_SHIPPING_NAME', $shipinfo->shipment_name);
		$html .= $this->getHtmlRowBE ('USPS_SHIP_WEIGHT', $shipinfo->order_weight . ' ' . ShopFunctions::renderWeightUnit ($shipinfo->shipment_weight_unit));
		$html .= $this->getHtmlRowBE ('USPS_SHIP_HANDLING_FEE_APPLIED', $currency->priceDisplay ($shipinfo->shipment_package_fee));
		$html .= $this->getHtmlRowBE ('USPS_SHIP_COST', $currency->priceDisplay ($shipinfo->shipment_cost));
		$html .= $this->getHtmlRowBE ('USPS_SHIP_TAX', $taxDisplay);
		$html .= '</table>' . "\n";

		return $html;
	}

	/**
	 * This method returns the shipping cost for the selected method.
	 */
	function getCosts (VirtueMartCart $cart, $method, $cart_prices) {
		$pluginmethod_id = $this->_idName;
		$methodSalesPrice = $this->shipmethod_rate[$method->$pluginmethod_id];
		$_SESSION['cart']['usps_ship_rate'] = $methodSalesPrice;
		//$this->logInfo('Setting USPS cost for shipment_rate'.$method->$pluginmethod_id .'='.$this->shipmethod_rate[$method->$pluginmethod_id]);

		return $methodSalesPrice;
	}
	/**
	* This function cleans the service name.
	* @param                $servicename
	* @return string
	*/
	function getCleanServiceName($serviceName){
		$serviceName = str_replace( "&lt;sup&gt;&amp;reg;&lt;/sup&gt;" , "" , $serviceName);
		$serviceName = str_replace( "&lt;sup&gt;&amp;trade;&lt;/sup&gt;" , "" , $serviceName);
		$serviceName = str_replace( "&lt;sup&gt;&#174;&lt;/sup&gt;" , "" , $serviceName); //July 2013
		$serviceName = str_replace( "&lt;sup&gt;&#8482;&lt;/sup&gt;" , "" , $serviceName); //July 2013
		$serviceName = str_replace( " 1-Day" , "" , $serviceName); //July 2013 - remove varying text from servicename
		$serviceName = str_replace( " 2-Day" , "" , $serviceName); //July 2013 - remove varying text from servicename
		$serviceName = str_replace( " 3-Day" , "" , $serviceName); //July 2013 - remove varying text from servicename
		$serviceName = str_replace( " Military" , "" , $serviceName); //July 2013 - remove varying text from servicename
		$serviceName = str_replace( " DPO" , "" , $serviceName); //July 2013 - remove varying text from servicename
		$serviceName = str_replace( " APO/FPO/DPO" , "" , $serviceName); //Sept 2014 - remove varying text from servicename
		
		$serviceName = str_replace( "&lt;sup&gt;&amp;reg;&lt;/sup&gt;" , "" , $serviceName);
		$serviceName = str_replace( "&lt;sup&gt;&amp;trade;&lt;/sup&gt;" , "" , $serviceName);
		$serviceName = str_replace( "&lt;sup&gt;&#174;&lt;/sup&gt;" , "" , $serviceName); //July 2013
		$serviceName = str_replace( "&lt;sup&gt;&#8482;&lt;/sup&gt;" , "" , $serviceName); //July 2013
		$serviceName = str_replace( "**" , "" , $serviceName); //September 2014
		
		return trim($serviceName);
	}
	/**
	* This method determines the handing costs for the shipping method.
	* @param                $method
	* @param                $methodSalesPrice
	* @return currency
	*/
	function _getHandlingCost($method, $methodSalesPrice, $cartvalue) {
		//$this->logInfo('getHandlingCost '.$method->USPS_HANDLINGFEE_TYPE.'= '.$method->USPS_HANDLINGFEE);
		if($method->USPS_HANDLINGFEE_TYPE == 'NONE'){
			//No handling fee applied
			return 0;
		}else{
			if (preg_match('/%$/',$method->USPS_HANDLINGFEE)) {
				if($method->USPS_HANDLINGFEE_TYPE == 'PCTCART'){
					//Add percentage based on cart total
					return $cartvalue * (substr($method->USPS_HANDLINGFEE,0,-1)/100);
				}else{ //Add percentage based on shipping method total
					return $methodSalesPrice * (substr($method->USPS_HANDLINGFEE,0,-1)/100);
				}
			} else {
				return $method->USPS_HANDLINGFEE;
			}
		}
	}
	/**
	* This method determines the vendor's zip code to send to USPS for shipping.
	* @param	$vendorid
	* @return	zipcode
	*/
	function _getVendorZipcode($vendorId) {
		$vendorModel = VmModel::getModel ('vendor');
		$vendorAddress = $vendorModel->getVendorAdressBT($vendorId);
		if ($vendorAddress == NULL){
			vmWarn('Error obtaining vendor zipcode from shop settings.');
			return;
		}
		return $vendorAddress->zip;
	}
	/**
	* This method determines if a call to USPS API is required based on changes in cart.
	* @param VirtueMartCart $cart
	* @param $method
	* @return int
	*/
	function _requireUSPSUpdate($cart) {
		//$this->logInfo('_requireUSPSUpdate');
		$requireupdate = false;
		//Check cart weight
		$orderWeight = $this->getOrderWeight ($cart, 'LB');
		//$this->logInfo('$orderWeight'.$orderWeight.' - '.$_SESSION['cart']['usps_ship_weight']);
		if ($orderWeight !== $_SESSION['cart']['usps_ship_weight']){
			$this->logInfo('Cart weight changed');
			$requireupdate = true;
		}
		//Check source zip
		if ($this->_getVendorZipCode($cart->vendorId) != $_SESSION['cart']['usps_ship_source_zip']){
			$this->logInfo('Shipping destination changed');
			$requireupdate = true;
		}
		//Check destination zip		
		$address = (($cart->ST == 0) ? $cart->BT : $cart->ST);
		if ($address['zip'] != $_SESSION['cart']['usps_ship_dest_zip']){
			$this->logInfo('Shipping source changed');
			$requireupdate = true;
		}

		return $requireupdate;
	}
	/**
	 * @param \VirtueMartCart $cart
	 * @param int             $method
	 * @param array           $cart_prices
	 * @return bool
	 */
	protected function checkConditions ($cart, $method, $cart_prices) {
		//$this->logInfo('checkconditions');
		//7/24/2013 - changes to add condition weight variable and clear selected id if conditions not met
		$usps_cond = false;
		$this->convert ($method);
		$pluginmethod_id = $this->_idName;

		//9/2/2014 - Clean USPS service name to support service names saved with old variations
		$method->USPS_SERVICE = $this->getCleanServiceName($method->USPS_SERVICE);
				
		$orderWeight = $this->getOrderWeight ($cart, 'LB');
		$orderWeightcond = true;
		if ($orderWeight <= 0) {
			$this->logInfo('shipmentmethod '.$method->shipment_name.' = FALSE Reason: Cart weight is '.$orderWeight);
			//return false;
			$orderWeightcond = false;
		}
		if($orderWeight > 70.00 ) {
			vmdebug ('shipmentmethod '.$method->shipment_name.' = FALSE Reason: Cart weight of '.$orderWeight.' pounds exceeds the 70 pound limit.');
			//return false;
			$orderWeightcond = false;
		}
		$address = (($cart->ST == 0) ? $cart->BT : $cart->ST);
		$type = (($cart->ST == 0) ? 'BT' : 'ST');
		$countries = array();
		if (!empty($method->countries)) {
			if (!is_array ($method->countries)) {
				$countries[0] = $method->countries;
			} else {
				$countries = $method->countries;
			}
		}
		// probably did not gave his BT:ST address
		if (!is_array ($address)) {
			$address = array();
			$address['zip'] = 0;
			$address['virtuemart_country_id'] = 0;
		}
		if(isset($cart_prices['salesPrice'])){
			$orderamount_cond = $this->testRange($cart_prices['salesPrice'],$method,'orderamount_start','orderamount_stop','order amount');
		} else {
			$orderamount_cond = FALSE;
		}

		$userFieldsModel =VmModel::getModel('Userfields');
		//if ($userFieldsModel->fieldPublished('zip', $type)){
			if (isset($address['zip'])) {
				$zip_cond = $this->testRange($address['zip'],$method,'zip_start','zip_stop','zip');
			} else {
		
				$zip_cond = true;
			}
		//} else {
			//$zip_cond = true;
		//}
		
		//if ($userFieldsModel->fieldPublished('virtuemart_country_id', $type)){
			if (!isset($address['virtuemart_country_id'])) {
				$address['virtuemart_country_id'] = 0;
			}
			if (in_array ($address['virtuemart_country_id'], $countries) || count ($countries) == 0) {	
				$this->logInfo('shipmentmethod '.$method->shipment_name.' = TRUE for variable virtuemart_country_id = '.implode($countries,', ').', Reason: Country in rule or none set');
				$country_cond = true;
			}
			else{
				$this->logInfo('shipmentmethod '.$method->shipment_name.' = FALSE for variable virtuemart_country_id = '.implode($countries,', ').', Reason: Country does not fit');
				$country_cond = false;
			}
		//} else {
			//$this->logInfo('shipmentmethod '.$method->shipment_name.' = TRUE for variable virtuemart_country_id, Reason: no boundary conditions set');
			//$country_cond = true;
		//}
		
		//7/15/2014 - Add weight check since VM2.6 calls shipping plugin with no items
		if($orderWeightcond){
			//9/12/2013 - Add conditions for flat rate boxes if using 'Smart Flatbox'
			$fitbox_cond = true; //default to fits
			if($this->usps_smart_flatrate){
				$this->logInfo('Smart Flatbox');
				switch (trim($method->USPS_SERVICE)){
					//Priority Mail Express Flat Rate
					case "Priority Mail Express Flat Rate Boxes":
					case "Priority Mail Express Sunday/Holiday Delivery Flat Rate Boxes":
					case "Priority Mail Express Flat Rate Boxes Hold For Pickup":
						$containers[] = array('length' => 11,'width' => 8.5,'height' => 5.5); //11'' x 8 1/2'' x 5 1/2''
						$containers[] = array('length' => 11.875, 'width' => 3.375,'height' => 13.625); //11 7/8" x 3 3/8" x 13 5/8"
						$usps_flatbox_level = 1;
						$usps_flatbox_type = 'PMED'; //priority mail express
						break;
					//Priority Mail Flat Rate
					case "Priority Mail Small Flat Rate Box":
						$containers[] = array('length' => 8.625,'width' => 5.375,'height' => 1.625); //8 5/8'' x 5 3/8'' x 1 5/8
						$usps_flatbox_level = 3;
						$usps_flatbox_type = 'PMD'; //priority mail
						break;
					case "Priority Mail Medium Flat Rate Box":
						$containers[] = array('length' => 11,'width' => 8.5,'height' => 5.5); //11'' x 8 1/2'' x 5 1/2''
						$containers[] = array('length' => 13.625,'width' => 11.875,'height' => 3.375); //13 5/8" x 11 7/8" x 3 3/8"
						$usps_flatbox_level = 2;
						$usps_flatbox_type = 'PMD'; //priority mail
						break;
					case "Priority Mail Large Flat Rate Box":
						$containers[] = array('length' => 12,'width' => 12,'height' => 5.5); //12" x 12" x 5 1/2"
						$usps_flatbox_level = 1;
						$usps_flatbox_type = 'PMD'; //priority mail
						break;
					//Priority Mail Express International Flat Rate
					case "Priority Mail Express International Flat Rate Boxes":
						if ($orderWeight > 20) break; //can not be larger than 20lbs
						$containers[] = array('length' => 11,'width' => 8.5,'height' => 5.5); //11'' x 8 1/2'' x 5 1/2''
						$containers[] = array('length' => 11.875, 'width' => 3.375,'height' => 13.625); //11 7/8" x 3 3/8" x 13 5/8"
						$usps_flatbox_level = 1;
						$usps_flatbox_type = 'PMEI'; //priority mail express intl.
						break;
					//Priority Mail International Flat Rate
					case "Priority Mail International Small Flat Rate Box":
						if ($orderWeight > 4) break; //can not be larger than 4lbs
						$containers[] = array('length' => 8.625,'width' => 5.375,'height' => 1.625); //8 5/8'' x 5 3/8'' x 1 5/8
						$usps_flatbox_level = 3;
						$usps_flatbox_type = 'PMI'; //priority mail intl.
						break;
					case "Priority Mail International Medium Flat Rate Box":
						if ($orderWeight > 20) break; //can not be larger than 20lbs
						$containers[] = array('length' => 11,'width' => 8.5,'height' => 5.5); //11'' x 8 1/2'' x 5 1/2''
						$containers[] = array('length' => 13.625,'width' => 11.875,'height' => 3.375); //13 5/8" x 11 7/8" x 3 3/8"
						$usps_flatbox_level = 2;
						$usps_flatbox_type = 'PMI'; //priority mail intl.
						break;
					case "Priority Mail International Large Flat Rate Box":
						if ($orderWeight > 20) break; //can not be larger than 20lbs
						$containers[] = array('length' => 12,'width' => 12,'height' => 5.5); //12" x 12" x 5 1/2"
						$usps_flatbox_level = 1;
						$usps_flatbox_type = 'PMI'; //priority mail intl.
						break;
				}
				if(isset($containers)){ //Method requires boxes to fit into specific container
					$fitbox_cond = false; //default - products do not fit in box
					//if this method is smaller box (or equal to for OPC) size then last available flatbox then loop through all products to see if fits
					if((!isset($this->usps_flatbox_avail)) || ($this->usps_flatbox_avail[$usps_flatbox_type] <= $usps_flatbox_level)){
						//$b=0;
						foreach ($cart->products as $product) {
							for ($q = 0; $q < $product->quantity; $q++) {
								$length = ShopFunctions::convertDimensionUnit($product->product_length, $product->product_lwh_uom, 'IN');
								$width = ShopFunctions::convertDimensionUnit($product->product_width, $product->product_lwh_uom, 'IN');
								$height = ShopFunctions::convertDimensionUnit($product->product_height, $product->product_lwh_uom, 'IN');
								//$boxes[$b] = array('length' => $length,'width' => $width,'height' => $height);
								$boxes[] = array('length' => $length,'width' => $width,'height' => $height);
								//$this->logInfo('product '.$length.'x'.$width.'x'.$height);
								//$b++;
							}
						}
						foreach ($containers as $k => $container) {
							//For each potential container size check if boxes fit
							$this->logInfo(' Determining if boxes fit into container '.$k);
							$lp = new LAFFPack();
							$lp->pack($boxes, $container);
							$boxesNotPacked = $lp->get_remaining_number_boxes();
							if($boxesNotPacked == 0){
								$this->logInfo('shipmentmethod '.$method->shipment_name.' = TRUE, Reason: Products do fit into box.');
								$this->usps_flatbox_avail[$usps_flatbox_type] = $usps_flatbox_level;
								$fitbox_cond = true; //all boxes fit in container
								break;
							}else{
								$this->logInfo('shipmentmethod '.$method->shipment_name.' = FALSE, Reason: Products do not fit into box.');
								$fitbox_cond = false; //products do not fit in box
							}
						}
					}
				}
			}
		}
		//$allconditions = (int)$zip_cond + (int)$country_cond + (int)$orderamount_cond;
		//$allconditions = (int)$orderWeightcond + (int)$zip_cond + (int)$country_cond + (int)$orderamount_cond;
		$allconditions = (int)$orderWeightcond + (int)$zip_cond + (int)$country_cond + (int)$orderamount_cond + (int)$fitbox_cond;
		//if($allconditions === 3){
		//if($allconditions === 4){
		if($allconditions === 5){
			//9/12/2013 END - Add conditions for flat rate boxes
			//$pluginmethod_id = $this->_idName;		
			//Continue with USPS API call
			if (($this->ship_service == null) && ($this->usps_errored == false)){ //do not call if already called
				$this->_getUSPSRates($orderWeight, $this->_getVendorZipcode($cart->vendorId), $address['zip'], $address['virtuemart_country_id']);
			}					
			$count = count($this->ship_service);
			if ($count < 1){
				$this->logInfo('No USPS options returned from USPS service.');
			}else{
				$this->logInfo('USPS service returned '.$count.' possible mail service options.');
				$i = 0;
				while ($i < $count) {
					// USPS returns Charges in USD.
					if (trim($this->ship_service[$i]) == trim($method->USPS_SERVICE)){
						//set rate of method as usps_ship_rate to support autoselect when one shipping method exists
						//7-24-2013 - two decimals
						//$methodSalesPrice = $this->ship_postage[$i];
						$methodSalesPrice = floatval($this->ship_postage[$i]);
						$methodSalesPrice = $methodSalesPrice + $this->_getHandlingCost($method, $methodSalesPrice, $cart_prices['salesPrice']);
						$this->shipmethod_rate[$method->$pluginmethod_id] = $methodSalesPrice;
						$this->logInfo('Setting USPS cost for shipment_rate'.$method->$pluginmethod_id.' = '.$this->shipmethod_rate[$method->$pluginmethod_id]);
						$usps_cond = true;
						break;
					}
					$i++;
				}
				if(!$usps_cond){
					$this->logInfo('checkConditions '.$method->shipment_name.' not matched with returned services.');
				}
			}
		}
		if($usps_cond){
			$this->logInfo('checkConditions '.$method->shipment_name.' DOES apply for this cart.');
			return TRUE;
		}else{
			$this->logInfo('checkConditions '.$method->shipment_name.' DOES NOT apply for this cart.');
			//7-22-2013- if this shipping method is currently selected but fails conditions we want to remove it.
			//this is required because we do not recall 'checkconditions' when setting price on CART page.
			if($method->$pluginmethod_id == $cart->virtuemart_shipmentmethod_id){
				//$this->logInfo(' MATCH: method_id='.$method->$pluginmethod_id.' virtuemart_shipmentmethod_id='.$cart->virtuemart_shipmentmethod_id);
				$cart->virtuemart_shipmentmethod_id = null;
			}
			return FALSE;
		}
	}

	/**
	 * @param $method
	 */
	function convert (&$method) {

		$method->orderamount_start = (float)$method->orderamount_start;
		$method->orderamount_stop = (float)$method->orderamount_stop;
		$method->zip_start = (int)$method->zip_start;
		$method->zip_stop = (int)$method->zip_stop;
	
	}

	private function testRange($value, $method, $floor, $ceiling,$name){

		$cond = true;
		if(!empty($method->$floor) and !empty($method->$ceiling)){
			$cond = (($value >= $method->$floor AND $value <= $method->$ceiling));
			if(!$cond){
				$result = 'FALSE';
				$reason = 'is NOT within Range of the condition from '.$method->$floor.' to '.$method->$ceiling;
			} else {
				$result = 'TRUE';
				$reason = 'is within Range of the condition from '.$method->$floor.' to '.$method->$ceiling;
			}
		} else if(!empty($method->$floor)){
			$cond = ($value >= $method->$floor);
			if(!$cond){
				$result = 'FALSE';
				$reason = 'is not at least '.$method->$floor;
			} else {
				$result = 'TRUE';
				$reason = 'is over min limit '.$method->$floor;
			}
		} else if(!empty($method->$ceiling)){
			$cond = ($value <= $method->$ceiling);
			if(!$cond){
				$result = 'FALSE';
				$reason = 'is over '.$method->$ceiling;
			} else {
				$result = 'TRUE';
				$reason = 'is lower than the set '.$method->$ceiling;
			}
		} else {
			$result = 'TRUE';
			$reason = 'no boundary conditions set';
		}

		$this->logInfo('shipmentmethod '.$method->shipment_name.' = '.$result.' for variable '.$name.' = '.$value.' Reason: '.$reason);
		return $cond;
	}

	/**
	 * Create the table for this plugin if it does not yet exist.
	 */
	function plgVmOnStoreInstallShipmentPluginTable ($jplugin_id) {

		return $this->onStoreInstallPluginTable ($jplugin_id);
	}

	/**
	 */
	public function plgVmOnSelectCheckShipment (VirtueMartCart &$cart) {
		//$this->logInfo('plgVmOnSelectCheckShipment');
		$_SESSION['cart']['usps_ship_rate'] = str_replace (" ", "", JRequest::getVar ('shipment_id_' . $cart->virtuemart_shipmentmethod_id.'_rate', ''));					
		return $this->OnSelectCheck ($cart);
	}

	/**
	 * This event is fired to display the pluginmethods in the cart (edit shipment/payment) for example
	 */
	public function plgVmDisplayListFEShipment (VirtueMartCart $cart, $selected = 0, &$htmlIn) {

		return $this->displayListFE ($cart, $selected, $htmlIn);
	}
	/**
	* displayListFE
	* This event handles all USPS options
	*
	* @param object  $cart Cart object
	* @param integer $selected ID of the method selected
	* @return boolean True on success, false on failures, null when this plugin was not selected.
	*/
	function displayListFE (VirtueMartCart $cart, $selected = 0, &$htmlIn) {
		//$this->logInfo('displayListFE');
		if ($this->getPluginMethods ($cart->vendorId) === 0) {
			if (empty($this->_name)) {
				vmAdminInfo ('displayListFE cartVendorId=' . $cart->vendorId);
				$app = JFactory::getApplication ();
				$app->enqueueMessage (JText::_ ('COM_VIRTUEMART_CART_NO_' . strtoupper ($this->_psType)));
				return FALSE;
			} else {
				return FALSE;
			}
		}
		
		$html = array();
		$method_name = $this->_psType . '_name';
		//clear usps session variables to trigger USPS API call
		$_SESSION['cart']['usps_ship_weight'] = 0;

		//Loop through each USPS shipping option in store to find any matches
		foreach ($this->methods as $method) {
			//9/2/2014 - Clean USPS service name to support service names saved with old variations
			$method->USPS_SERVICE = $this->getCleanServiceName($method->USPS_SERVICE);
			
			if ($this->checkConditions ($cart, $method, $cart->pricesUnformatted)) {
				$method->$method_name = $this->renderPluginName ($method);
				//Write out the shipping options
				$count = count($this->ship_service);
				if ($count < 1){
					$this->logInfo('No USPS Options');
				}else{
					$i = 0;
					while ($i < $count) {
						// USPS returns Charges in USD.
						if ($this->ship_service[$i] == $method->USPS_SERVICE){
							//7-24-2013 - two decimals
							//$methodSalesPrice = $this->ship_postage[$i];
							$methodSalesPrice = floatval($this->ship_postage[$i]);
							$this->logInfo('USPS service cost = '.$methodSalesPrice);					
							$methodSalesPrice = $methodSalesPrice + $this->_getHandlingCost($method, $methodSalesPrice, $cart->pricesUnformatted['salesPrice']);
							$this->logInfo('USPS service cost after handling fee = '.$methodSalesPrice);
							$html [] = $this->getPluginHtml ($method, $selected, $methodSalesPrice);
							break; 
						}
						$i++;
					}
				}
			}
		}

		if (!empty($html)) {
			$htmlIn[] = $html;
			return TRUE;
		}
	
		return FALSE;
	}
	/**
	* Override in writing HTML for USPS shipping options
	*/
	protected function getPluginHtml ($plugin, $selectedPlugin, $pluginSalesPrice) {
		//$this->logInfo('getPluginHtml');
		$pluginmethod_id = $this->_idName;
		$pluginName = $this->_psType . '_name';
				
		if ($selectedPlugin == $plugin->$pluginmethod_id) {
			$checked = 'checked="checked"';
		} else {
			$checked = '';
		}
		
		if (!class_exists ('CurrencyDisplay')) {
			require(JPATH_VM_ADMINISTRATOR . DS . 'helpers' . DS . 'currencydisplay.php');
		}
		$currency = CurrencyDisplay::getInstance ();
		$costDisplay = "";
		if ($pluginSalesPrice) {
			$costDisplay = $currency->priceDisplay ($pluginSalesPrice);
			$costDisplay = '<span class="' . $this->_type . '_cost"> (' . JText::_ ('COM_VIRTUEMART_PLUGIN_COST_DISPLAY') . $costDisplay . ")</span>";
		}

		$html = '<input type="radio" name="' . $pluginmethod_id . '" id="' . $this->_psType . '_id_' . $plugin->$pluginmethod_id . '"   value="' . $plugin->$pluginmethod_id . '" ' . $checked . ">\n"
		. '<label for="' . $this->_psType . '_id_' . $plugin->$pluginmethod_id . '">' . '<span class="' . $this->_type . '">' . $plugin->$pluginName . $costDisplay . "</span></label>\n"
		. '<input type="hidden" name="' . $this->_psType . '_id_' . $plugin->$pluginmethod_id . '_rate"   value="' . $pluginSalesPrice . '">';
		
		return $html;
	}
	/**
	 * @param VirtueMartCart $cart
	 * @param array          $cart_prices
	 * @param                $cart_prices_name
	 * @return bool|null
	 */
	public function plgVmOnSelectedCalculatePriceShipment (VirtueMartCart $cart, array &$cart_prices, &$cart_prices_name) {
		//$this->logInfo('plgVmOnSelectedCalculatePriceShipment');
		//7/24/2013 this check was not proper and caused selected shipping option not to clear if method was no longer valid
		//if (!($method = $this->getVmPluginMethod ($cart->virtuemart_shipmentmethod_id))) {
		//	return NULL; // Another method was selected, do nothing
		//}
		$id = $this->_idName;
		if (!($method = $this->selectedThisByMethodId ($cart->$id))) {
			return NULL; // Another method was selected, do nothing
		}
		if (!($method = $this->getVmPluginMethod ($cart->$id))) {
			return NULL;
		}
		//7/24/2013 end
		
		$cart_prices_name = '';
		//$cart_prices[$this->_psType . '_tax_id'] = 0;
		$cart_prices['cost'] = 0;
		
		//If there is no change to the cart that would require a USPS rate change then do not call 'checkConditions'
		if(!$this->_requireUSPSUpdate($cart) && !empty($_SESSION['cart']['usps_ship_rate'])){
			$pluginmethod_id = $this->_idName;
			//If there has not been a rate set from this procedure then set rate to current cart USPS rate
			if(empty($this->shipmethod_rate[$method->$pluginmethod_id])){
				$this->shipmethod_rate[$method->$pluginmethod_id] = $_SESSION['cart']['usps_ship_rate'];				
			}
		}else{
			if (!$this->checkConditions ($cart, $method, $cart_prices)) {
				return FALSE;
			}			
		}
		
		$paramsName = $this->_psType . '_params';
		$cart_prices_name = $this->renderPluginName ($method);
		
		$this->setCartPrices ($cart, $cart_prices, $method);
		
		return TRUE;
		//return $this->onSelectedCalculatePrice ($cart, $cart_prices, $cart_prices_name);
	}
	

	/**
	 * Checks how many plugins are available. If only one, the user will not have the choice. Enter edit_xxx page
	 * The plugin must check first if it is the correct type
	 */
	function plgVmOnCheckAutomaticSelectedShipment (VirtueMartCart $cart, array $cart_prices = array(), &$shipCounter) {
		//$this->logInfo('OnCheckAutomaticSelected'.$shipCounter);
		if ($shipCounter > 1) {
			return 0;
		}

		return $this->onCheckAutomaticSelected ($cart, $cart_prices, $shipCounter);
	}

	function plgVmonShowOrderPrint ($order_number, $method_id) {
		//$this->logInfo('plgVmonShowOrderPrint');

		return $this->onShowOrderPrint ($order_number, $method_id);
	}

	function plgVmDeclarePluginParamsShipment ($name, $id, &$data) {
		//$this->logInfo('plgVmDeclarePluginParamsShipment');

		return $this->declarePluginParams ('shipment', $name, $id, $data);
	}
	
	function plgVmDeclarePluginParamsShipmentVM3 (&$data) {
		return $this->declarePluginParams ('shipment', $data);
	}

	/**
	 *  Validate data for plugin shipment in BE
	 */
	function plgVmSetOnTablePluginShipment(&$data,&$table){
		//$this->logInfo('plgVmSetOnTablePluginShipment');	
		$name = $data['shipment_element'];
		$id = $data['shipment_jplugin_id'];

		$handlingType = $data['USPS_HANDLINGFEE_TYPE'];
		
		if (!empty($this->_psType) and !$this->selectedThis ($this->_psType, $name, $id)) {
			return FALSE;
		} else {
			if(empty($this->usps_username)){
				vmError('USPS username is not set in plugin settings.');
			}
			if(empty($this->usps_server)){
				vmError('USPS server type is not set in plugin settings.');
			}
			if(empty($this->usps_path)){
				vmError('USPS path is not set in plugin settings.');
			}
			if($this->usps_reporterrors == 1) vmWarn('VMSHIPMENT_USPS_SHIP_REPORTERRORS_NOTICE');
			
			$toConvert = array('weight_start','weight_stop','orderamount_start','orderamount_stop');
			foreach($toConvert as $field){
				if(!empty($data[$field])){
					$data[$field] = str_replace(array(',',' '),array('.',''),$data[$field]);
				} else {
					unset($data[$field]);
				}
			}

			//Test to ensure conditions proper		
			if(!empty($data['zip_start']) and !empty($data['zip_stop']) and (int)$data['zip_start']>=(int)$data['zip_stop']){
				vmWarn('VMSHIPMENT_USPS_SHIP_ZIP_CONDITION_WRONG');
			}
			if(!empty($data['weight_start']) and !empty($data['weight_stop']) and (float)$data['weight_start']>=(float)$data['weight_stop']){
				vmWarn('VMSHIPMENT_USPS_SHIP_WEIGHT_CONDITION_WRONG');
			}

			if(!empty($data['orderamount_start']) and !empty($data['orderamount_stop']) and (float)$data['orderamount_start']>=(float)$data['orderamount_stop']){
				vmWarn('VMSHIPMENT_USPS_SHIP_AMOUNT_CONDITION_WRONG');
			}
			
			if ($handlingType == 'PCTCART' || $handlingType == 'PCTSHIP' ){
				if (!preg_match('/%$/',$data['USPS_HANDLINGFEE'])){
					vmError('A percentage type handling fee was selected but a percentage value was not entered. Example: "5%".');
				}
			}

			return $this->setOnTablePluginParams ($name, $id, $table);
		}
	}
	/**
	*  Validate data for plugin shipment in BE older VM versions
	*/
	function plgVmSetOnTablePluginParamsShipment ($name, $id, &$table) {
		//$this->logInfo('plgVmSetOnTablePluginShipment');
		if (!empty($this->_psType) and !$this->selectedThis ($this->_psType, $name, $id)) {
			return FALSE;
		} else {	
			return $this->setOnTablePluginParams ($name, $id, $table);
		}
	}
	

	/**
	* This method executed to API call to USPS.
	* @param                $order_weight - The actual weight in the cart (prior to any padding)
	* @param                $source_zip - The zip that you are shipping from
	* @param                $dest_zip - The destination zip that you are shipping to
	* @param                $dest_countryid - The destination country id that you are shipping to
	* @return currency
	*/
	private function _getUSPSRates($order_weight, $source_zip, $dest_zip, $dest_countryid) {
		$this->logInfo('**USPS API CALL: Cart weight='.$order_weight.'lbs zipcode from='.$source_zip.' zipcode to='.$dest_zip.' countryid='.$dest_countryid);
		//Store variables to cart which are used to determine if another call to USPS API is required on future requests.
		$_SESSION['cart']['usps_ship_weight'] = $order_weight;
		$_SESSION['cart']['usps_ship_source_zip'] = $source_zip;
		$_SESSION['cart']['usps_ship_dest_zip'] = $dest_zip;
		//Require that zip codes are only first 5 digits. USPS api requirement. this is done after storing in SESSION so that a change in extended zip code will trigger update.
		$source_zip = substr($source_zip, 0, 5);
		$dest_zip = substr($dest_zip, 0, 5);
		
		$this->usps_errored = true; //default to false
		
		if($this->usps_reporterrors == 1) $usps_reporterrors = 1;
		else $usps_reporterrors = 0;
		
		if($order_weight > 0) {
			$usps_packagesize = $this->usps_packagesize;
			$usps_packageid = 0;				
			//Pad the shipping weight to allow weight for shipping materials
			$usps_padding = $this->usps_padding;
			$usps_padding = $usps_padding * 0.01;
			$order_weight = ($order_weight * $usps_padding) + $order_weight;
			$this->logInfo('Cart weight with padding='.$order_weight);
							
			//USPS Machinable for Parcel Post
			$usps_machinable = $this->usps_machinable;
			if ($usps_machinable == '1') $usps_machinable = 'TRUE';
			else $usps_machinable = 'FALSE';

			$shpService = 'All';
			
			if(empty($dest_countryid)) {
				if($usps_reporterrors) vmWarn('VMSHIPMENT_USPS_SHIP_COUNTRY_ID_EMPTY');
				return;
			}
			if(empty($source_zip)) {
				if($usps_reporterrors) vmWarn('VMSHIPMENT_USPS_SHIP_SOURCE_ZIP_EMPTY');
				return;
			}
			if(empty($dest_zip)) {
				if($usps_reporterrors) vmWarn('VMSHIPMENT_USPS_SHIP_DEST_ZIP_EMPTY');
				return;
			}
			
			$dest_country = ShopFunctions::getCountryByID ($dest_countryid, 'country_2_code');
			$dest_country_name = ShopFunctions::getCountryByID ($dest_countryid);

			//Determine weight in pounds and ounces (USPS service will round lbs up when needed)
			$shipping_pounds = floor ($order_weight); //send integer rounded down
			$shipping_ounces = ceil(16 * ($order_weight - floor($order_weight))); //send integer rounded up
				
			//If weight is over 70 pounds, can not send USPS so write notice (update in the future to be able to split the package)
			if( $order_weight > 70.00 ) {
				if($usps_reporterrors) vmWarn( JText::sprintf('VMSHIPMENT_USPS_SHIP_WEIGHT_GT70', $order_weight));
				return;
			}else{
				$domestic = 0; //default to international
				if( ( $dest_country == "US") || ( $dest_country == "PR") || ( $dest_country == "VI")){
					//domestic if US, PR or VI
					$domestic = 1;
				}
				//Build XML string based on service request
				if ($domestic){
					//the xml that will be posted to usps for domestic rates
					$xmlPost = 'API=RateV4&XML=<RateV4Request USERID="'.$this->usps_username.'" PASSWORD="'.$this->usps_password.'">';
					$xmlPost .= '<Revision/>';
					$xmlPost .= '<Package ID="'.$usps_packageid.'">';
					$xmlPost .= "<Service>".$shpService."</Service>";
					$xmlPost .= "<ZipOrigination>".$source_zip."</ZipOrigination>";
					$xmlPost .= "<ZipDestination>".$dest_zip."</ZipDestination>";
					$xmlPost .= "<Pounds>".$shipping_pounds."</Pounds>";
					$xmlPost .= "<Ounces>".$shipping_ounces."</Ounces>";
					$xmlPost .= '<Container/>';
					$xmlPost .= "<Size>".$usps_packagesize."</Size>";
					$xmlPost .= "<Machinable>".$usps_machinable."</Machinable>";
					$xmlPost .= "</Package></RateV4Request>";
				}else{
					//the xml that will be posted to usps for international rates
					$xmlPost = 'API=IntlRateV2&XML=<IntlRateV2Request USERID="'.$this->usps_username.'" PASSWORD="'.$this->usps_password.'">';
					$xmlPost .= '<Package ID="'.$usps_packageid.'">';
					$xmlPost .= "<Pounds>".$shipping_pounds."</Pounds>";
					$xmlPost .= "<Ounces>".$shipping_ounces."</Ounces>";
					$xmlPost .= "<MailType>".$shpService."</MailType>";
					$xmlPost .= "<ValueOfContents>0.0</ValueOfContents>"; //no insurance functionality at this time
					$xmlPost .= "<Country>".$dest_country_name."</Country>";
					$xmlPost .= "<Container>RECTANGULAR</Container>";
					$xmlPost .= "<Size>$usps_packagesize</Size>";
					$xmlPost .= "<Width>0</Width>";
					$xmlPost .= "<Length>0</Length>";
					$xmlPost .= "<Height>0</Height>";
					$xmlPost .= "<Girth>0</Girth>";
					$xmlPost .= "</Package></IntlRateV2Request>";
				}

				//$host = $this->usps_server;
				if($this->usps_server == "STAGING"){
					$host = 'stg-production.shippingapis.com';
				}else{
					$host = 'production.shippingapis.com';
				}
				
				$path = $this->usps_path;
				$port = 80;
				$protocol = "http";
				$html = "";

				// Using cURL is Up-To-Date and easier!!
				if( function_exists( "curl_init" )) {
					$CR = curl_init();
					curl_setopt($CR, CURLOPT_URL, $protocol."://".$host.$path);
					//curl_setopt($CR, CURLOPT_URL, 'http://stg-production.shippingapis.com/ShippingAPI.dll');
					curl_setopt($CR, CURLOPT_POST, 1);
					curl_setopt($CR, CURLOPT_FAILONERROR, true);
					curl_setopt($CR, CURLOPT_POSTFIELDS, $xmlPost);
					curl_setopt($CR, CURLOPT_RETURNTRANSFER, 1);
					curl_setopt ($CR, CURLOPT_SSL_VERIFYPEER, FALSE);
					curl_setopt ($CR, CURLOPT_CONNECTTIMEOUT, 20);
					curl_setopt ($CR, CURLOPT_TIMEOUT, 30);
					if (!empty($this->usps_proxyserver)){
						curl_setopt ($CR, CURLOPT_HTTPPROXYTUNNEL, TRUE);
						curl_setopt ($CR, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);
						curl_setopt ($CR, CURLOPT_PROXY, $this->usps_proxyserver);
					}
					$xmlResult = curl_exec( $CR );
					$error = curl_error( $CR );
					curl_close( $CR );
				
				//HTTP Post
				}else{
					$fp = fsockopen($host, $port, $errno, $errstr, $timeout = 60);
					if( !$fp ) {
						$error = _USPS_RESPONSE_ERROR.": $errstr ($errno)";
					}else{
						//send the server request
						fputs($fp, "POST $path HTTP/1.1\r\n");
						fputs($fp, "Host: $host\r\n");
						fputs($fp, "Content-type: application/x-www-form-urlencoded\r\n");
						fputs($fp, "Content-length: ".strlen($xmlPost)."\r\n");
						fputs($fp, "Connection: close\r\n\r\n");
						fputs($fp, $xmlPost . "\r\n\r\n");

						$xmlResult = '';
						//get the response
						$lineNum = 0;
						while ( !feof($fp) ) {
							$lineNum++;
							if ( $lineNum>500 ) {
								//don't let it run forever, line limit here
								break;
							}
							$line = fgets($fp, 512);
							$line = trim($line);
							if ( strpos( $line ,"HTTP/1")!==false) {
								$inHead = true;
							} elseif ( trim($line)=='') {
								//empty line marks the end of head
								$inHead = false;
								continue;
							}
							if ( $inHead ) {
								//skip
							} else {
								$xmlResult.= trim($line);
							}
							if ( strpos($line,"</RateV4Response>")!==false) {
								//this way we forcefully end the reading not waiting for the eof which may never come
								break;
							}
						}
					}
				}
				
				//Display textarea fields when in debug mode
				if(VMConfig::showDebug()  ){
					echo 'XML Post: <br /><textarea cols="120" rows="5">'.$protocol.'://'.$host.$path.'?'.$xmlPost.'</textarea><br />XML Result:<br /><textarea cols="120" rows="12">'.$xmlResult.'</textarea><br />Cart Contents: '.$order_weight.'<br /><br/>';
				}
				$this->logInfo('HOST:'.$host.' PATH:'.$path);
				$this->logInfo($xmlPost);
				$this->logInfo($xmlResult);
				//Check for error from response from USPS
				if(!empty($error)) {
					if ($usps_reporterrors) vmWarn('USPS Error: '.$error);
					vmDebug($error);
					$this->logInfo($error);
					return;
				}
				//Parse XML response from USPS
				$xmlDoc = new SimpleXMLElement($xmlResult);
				
				//Check for error in response
				if( strstr( $xmlResult, "Error" ) ) {
					$error = $xmlDoc->Package->Error->Description;
					if(empty($error)) $error = $xmlResult; //USPS API can return non-XML on error
					$error = 'USPS Response Error '.$xmlDoc->Package->Error->Number.': '.$error;
					if ($usps_reporterrors) vmWarn($error);
					vmDebug($error);
					$this->logInfo($error);
					return;
				}
				
				//Get shipping options that are selected as available in VM from XML response
				$i = 0;
				$count = 0;
				if ($domestic){ //domestic shipping response
					foreach ($xmlDoc->Package->Postage as $postage) {
						$serviceName = $postage->MailService;
						//remove special characters					
						//$serviceName = str_replace( "&lt;sup&gt;&amp;reg;&lt;/sup&gt;" , "" , $serviceName);
						//$serviceName = str_replace( "&lt;sup&gt;&amp;trade;&lt;/sup&gt;" , "" , $serviceName);
						//$serviceName = str_replace( "&lt;sup&gt;&#174;&lt;/sup&gt;" , "" , $serviceName); //July 2013
						//$serviceName = str_replace( "&lt;sup&gt;&#8482;&lt;/sup&gt;" , "" , $serviceName); //July 2013
						//$serviceName = str_replace( " 1-Day" , "" , $serviceName); //July 2013 - remove varying text from servicename
						//$serviceName = str_replace( " 2-Day" , "" , $serviceName); //July 2013 - remove varying text from servicename
						//$serviceName = str_replace( " 3-Day" , "" , $serviceName); //July 2013 - remove varying text from servicename
						//$serviceName = str_replace( " Military" , "" , $serviceName); //July 2013 - remove varying text from servicename
						//$serviceName = str_replace( " DPO" , "" , $serviceName); //July 2013 - remove varying text from servicename
						//$serviceName = str_replace( " APO/FPO/DPO" , "" , $serviceName); //Sept 2014 - remove varying text from servicename
						$serviceName = $this->getCleanServiceName($serviceName);
						$this->ship_service[$count] = trim($serviceName);
						$this->ship_postage[$count] = $postage->Rate;
						$count++;
					}
													
				}else{ //international shipping response
					foreach ($xmlDoc->Package->Service as $postage) {
						$serviceName = $postage->SvcDescription;
						//remove special characters
						//$serviceName = str_replace( "&lt;sup&gt;&amp;reg;&lt;/sup&gt;" , "" , $serviceName);
						//$serviceName = str_replace( "&lt;sup&gt;&amp;trade;&lt;/sup&gt;" , "" , $serviceName);
						//$serviceName = str_replace( "&lt;sup&gt;&#174;&lt;/sup&gt;" , "" , $serviceName); //July 2013
						//$serviceName = str_replace( "&lt;sup&gt;&#8482;&lt;/sup&gt;" , "" , $serviceName); //July 2013
						//$serviceName = str_replace( "**" , "" , $serviceName); //September 2014
						$serviceName = $this->getCleanServiceName($serviceName);
						$this->ship_service[$count] = $serviceName;
						$this->ship_postage[$count] = $postage->Postage;
						$count++;
					}
				}
			}
		}else{
			vmWarn ('VMSHIPMENT_USPS_SHIP_WEIGHT_LT0');
			return;
		}

		$usps_errored = false;
		return;
	} //end function getUSPSRates

}
?>
