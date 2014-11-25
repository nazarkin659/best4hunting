<?php

if (!defined('_JEXEC')) {
    die('Direct Access to ' . basename(__FILE__) . ' is not allowed.');
}
/*
 * @version $Id:
 * @package VirtueMart
 * @subpackage Plugins - shipment
 * @author Val?rie Isaksen
 * @copyright Copyright (C) 2011 alatak.net - All rights reserved.
 * @license license.txt Proprietary Lisence. This code belongs to alatak.net
 * You are not allowed to distribute or sell this code. You bought only a license to use it for one virtuemart installation.
 * You are not allowed to modify this code.
 * https://www.usps.com/webtools/htm/Rate-Calculators-v1-3.htm
 * https://www.usps.com/webtools/htm/Address-Information-v3-1a.htm#_Toc131231399
 * https://www.usps.com/business/web-tools-apis/technical-documentation.htm
 * https://www.usps.com/business/web-tools-apis/rate-calculators-v1-7a.pdf
 * */

if (!class_exists('vmPSPlugin')) {
    require(JPATH_VM_PLUGINS . DS . 'vmpsplugin.php');
}

class plgVmShipmentAlatak_USPS extends vmPSPlugin {

    public static $_this = false;
    private $_usps_id = '';
    private $_usps_name = '';
    private $_usps_rate = '';
    private $_usps_length = 0;
    private $_usps_width = 0;
    private $_usps_height = 0;
    private $_usps_girth = 0;
    private $valueOfContents = 0;
    private $_usps_packageCount = 0;
    var $from_zip = 0;
    var $to_zip = 0;
    var $shipping_pounds = '';
    var $shipping_ounces = '';
    var $is_domestic;

    function __construct(& $subject, $config) {

        //if (self::$_this)
        //   return self::$_this;
        parent::__construct($subject, $config);

        $this->_loggable = true;
        $this->tableFields = array_keys($this->getTableSQLFields());
        $varsToPush = array('username' => array('', 'char'),
            'shipment_logos' => array('', 'char'),
            'server' => array('', 'char'),
            'secure' => array('', 'int'),
            'proxy_server' => array('', 'char'),
            'dimension_unit' => array('', 'int'),
            'machinable' => array('', 'int'),
            'commercial' => array('', 'char'),

            'package_size' => array('', 'int'),
            'send_dimensions' => array('', 'int'),
            'weigth_padding' => array('', 'char'),
            'weight_unit' => array('LB', 'char'),
            'dimensions_padding' => array('', 'char'),
            'shipment_strategy' => array('', 'char'),

            'domestic_enable' => array('', 'int'),
            'domestic' => array('', 'int'),
            'countries_domestic' => array('', 'int'),
            'domestic_fee' => array('', 'float'),
            'domestic_free_shipment' => array('', 'float'),
            'domestic_packaging_dimension' => array('', 'char'),

            'intl_enable' => array('', 'int'),
            'intl' => array('', 'int'),
            'countries_intl' => array('', 'char'),
            'extraServices' => array('', 'int'),
            'intl_packaging_dimension' => array('', 'char'),
            'intl_fee' => array('', 'float'),
            'intl_free_shipment' => array('', 'float'),
            'tax_id' => array('', 'int'),
            'show_debug' => array('', 'int'),
            'report_usps_error' => array('', 'int'),
        );

        $this->setConfigParameterable($this->_configTableFieldName, $varsToPush);

        // 		self::$_this
        //$this->createPluginTable($this->_tablename);
        //self::$_this = $this;
    }

    /**
     * Create the table for this plugin if it does not yet exist.
     *
     * @author Valérie Isaksen
     */
    protected function getVmPluginCreateTableSQL() {

        return $this->createTableSQL('Shipment USPS Table');
    }

    function getTableSQLFields() {

        $SQLfields = array(
            'id' => ' int(1) unsigned NOT NULL AUTO_INCREMENT',
            'virtuemart_order_id' => 'int(11) UNSIGNED DEFAULT NULL',
            'order_number' => 'char(32) DEFAULT NULL',
            'virtuemart_shipmentmethod_id' => 'mediumint(1) UNSIGNED DEFAULT NULL',
            'shipment_name' => 'varchar(5000)',
            'weight' => 'mediumint(1) UNSIGNED DEFAULT NULL',
            'weight_unit' => 'char(3) DEFAULT \'KG\' ',
            'shipment_cost' => 'decimal(10,2) DEFAULT NULL',
            'tax_id' => 'smallint(1) DEFAULT NULL'
        );
        return $SQLfields;
    }

    public function plgVmDisplayListFEShipment(VirtueMartCart $cart, $selected = 0, &$htmlIn) {

        if ($this->getPluginMethods($cart->vendorId) === 0) {
            if (empty($this->_name)) {
                $app = JFactory::getApplication();
                $app->enqueueMessage(JText::_('COM_VIRTUEMART_CART_NO_' . strtoupper($this->_psType)));
                return false;
            } else {
                return false;
            }
        }
        $weight = $this->getOrderWeight($cart);
        if ($weight == 0) {
            vmDebug('USPS: plgVmDisplayListFEShipment', 'weigth = 0');
            return;
        }
        $vmhtml = '';
        $to_address = (($cart->ST == 0) ? $cart->BT : $cart->ST);
        if (empty($to_address['zip'])) {
            $mainframe = JFactory::getApplication();
            $redirectMsg = JText::_('VMSHIPMENT_ALATAK_USPS_NO_ZIP_DEFAULT');
            //vmWarn($redirectMsg);
            $mainframe->redirect(JRoute::_('index.php?option=com_virtuemart&view=user&task=editaddresscheckout&addrtype=BT'), $redirectMsg);
        } else {
            $pluginmethod_id = $this->_idName;
            $pluginName = $this->_psType . '_name';
            foreach ($this->methods as $method) {
                if (empty($to_address['zip'])) {
                    $vmhtml = $method->no_zip_msg ? $method->no_zip_msg : JText::_('VMSHIPMENT_ALATAK_USPS_NO_ZIP_DEFAULT');
                    $html[] = $vmhtml;
                } else {
                    $response = $this->_getUSPSrates($cart, $method, $cart->pricesUnformatted);
                    if ($response) {
                        $vmhtml = '';
                        //$vmhtml = '<input type="radio" name="' . $pluginmethod_id . '" id="' . $this->_psType . '_id_' . $method->$pluginmethod_id . '"   value="' . $method->$pluginmethod_id . '" ' . $checked . '>';
                        //$vmhtml .= '<label for="' . $this->_psType . '_id_' . $method->$pluginmethod_id . '">' . '<span class="' . $this->_type . '">' . $method->$pluginName . "</span></label>\n";
                        $usps_server = $this->getServer($method);
                        $vmhtml .= $this->_getResponseUSPSHtml($method, $response, $selected, $cart);

                        if ($method->show_debug) {
                            // vmdebug('plgVmDisplayListFEShipment', $response);
                            //$vmhtml .= "Cart Contents: " . $order_weight . "<br><br>\n";
                        }

                        $html[] = $vmhtml;
                    }
                }
            }
        }
        $htmlIn[] = $html;
        return $htmlIn;
    }

    /**
     * Get the total weight for the order, based on which the proper shipping rate
     * can be selected.
     *
     * @param object $cart Cart object
     * @return float Total weight for the order
     */
    protected function getOrderWeight(VirtueMartCart $cart, $method = '') {

        $weight = 0;
        foreach ($cart->products as $product) {
            if ($method == '') {
                $weight += $product->product_weight; // just to find if downloadable products
            } else {
                $weight += (ShopFunctions::convertWeigthUnit($product->product_weight, $product->product_weight_uom, $method->weight_unit) * $product->quantity);
            }
        }
        if ($method != '') {
            $padding = $method->weigth_padding;
            if (preg_match('/%$/', $padding)) {
                $weight += ($weight * substr($padding, 0, -1) * 0.01);
            } else {
                $weight = $weight + $padding;
            }
        }
        return $weight;
    }

    /**
     * This event is fired after the shipping method has been selected. It can be used to store
     * additional shipper info in the cart.
     *
     * @param object $cart Cart object
     * @param integer $selected ID of the shipper selected
     * @return boolean True on succes, false on failures, null when this plugin was not selected.
     * On errors, JError::raiseWarning (or JError::raiseError) must be used to set a message.
     * @author ValУЉrie Isaksen
     */
    public function plgVmOnSelectCheckShipment(VirtueMartCart $cart) {

        if (!$this->selectedThisByMethodId($cart->virtuemart_shipmentmethod_id)) {
            return NULL; // Another method was selected, do nothing
        }

        $this->_usps_id = JRequest::getInt('usps');
        $this->_usps_name = base64_decode(JRequest::getVar('usps_name-' . $cart->virtuemart_shipmentmethod_id, ''));
        $this->_usps_rate = JRequest::getVar('usps_rate-' . $cart->virtuemart_shipmentmethod_id, '');
        vmdebug('USPS plgVmOnSelectCheckShipment ', $this->_usps_name);

        $this->_setUspsIntoSession();
        return true;
    }

    function _setUspsIntoSession() {

        $session = JFactory::getSession();
        $sessionUsps = new stdClass();
        // card information
        $sessionUsps->_usps_id = $this->_usps_id;
        $sessionUsps->_usps_name = $this->_usps_name;
        $sessionUsps->_usps_rate = $this->_usps_rate;
        vmdebug('_setUspsIntoSession', $sessionUsps);
        $session->set('usps', serialize($sessionUsps), 'vm');
    }

    function _getUspsFromSession() {

        $session = JFactory::getSession();
        $sessionUsps = $session->get('usps', 0, 'vm');

        if (!empty($sessionUsps)) {
            $sessionUspsData = unserialize($sessionUsps);
            $this->_usps_id = $sessionUspsData->_usps_id;
            $this->_usps_name = $sessionUspsData->_usps_name;
            $this->_usps_rate = $sessionUspsData->_usps_rate;
            return true;
        }
        return false;
    }

    /**
     * This is for checking the input data of the payment method within the checkout
     *
     * @author Valerie Cartan Isaksen
     */
    function plgVmOnCheckoutCheckDataShipment(VirtueMartCart $cart) {

        if (!$this->selectedThisByMethodId($cart->virtuemart_shipmentmethod_id)) {
            return NULL; // Another method was selected, do nothing
        }
        return $this->_getUspsFromSession();

    }

    /*
     * @param $plugin plugin
     */

    function renderPluginName($plugin) {

        $return = '';
        $plugin_name = $this->_psType . '_name';
        $plugin_desc = $this->_psType . '_desc';
        $description = '';
        // 		$params = new JParameter($plugin->$plugin_params);
        // 		$logo = $params->get($this->_psType . '_logos');
        $logosFieldName = $this->_psType . '_logos';
        $logos = $plugin->$logosFieldName;
        if (!empty($logos)) {
            $return = $this->displayLogos($logos) . ' ';
        }
        if (!empty($plugin->$plugin_desc)) {
            $description = '<span class="' . $this->_type . '_description">' . $plugin->$plugin_desc . '</span>';
        }

        $pluginName = $return . '<span class="' . $this->_type . '_name">' . $plugin->$plugin_name . '</span>' . $description;
        $this->_getUspsFromSession();
        $serviceName = html_entity_decode($this->_usps_name, ENT_COMPAT, 'UTF-8');

        $usps_info = '<span class="' . $this->_type . '_usps">' . $serviceName . '</span>';
        $pluginName = $return . '<span class="' . $this->_type . '_name">' . $plugin->$plugin_name . '</span>' . $description . $usps_info;

        return $pluginName;
    }

    function plgVmOnCheckAutomaticSelectedShipment(VirtueMartCart $cart, array $cart_prices = array(), &$shipCounter) {

        if ($this->getPluginMethods($cart->vendorId) === 0) {
            return NULL;
        }


        $nbPlugin = 0;
        $virtuemart_pluginmethod_id = 0;
        $nbMethod = 0;

        foreach ($this->methods as $method) {
            vmdebug('USPS plgVmOnCheckAutomaticSelectedShipment', $cart->pricesUnformatted, $cart_prices, "nnn");
            $response = $this->_getUSPSrates($cart, $method, $cart_prices);
            $nb = count($response);
            $nbMethod = $nbMethod + $nb;
            $shipCounter += $nb;
            $idName = $this->_idName;
            $virtuemart_pluginmethod_id = $method->$idName;
        }
        if ($nbMethod == 1) {
            if (is_array($response)) {
                $this->_usps_id = 0;
                $service_name = array_keys($response);
// Intl Priority Mail&lt;sup&gt;&amp;reg;&lt;/sup&gt; International (6 - 10 business days)
                if ($this->is_domestic) {
                    $this->_usps_name = $service_name[0];
                } else {
                    $this->_usps_name = $this->getServiceName($response [$service_name[0]]);
                }
                $this->_usps_rate = $response[$this->_usps_name]['rate'];
                $this->_setUspsIntoSession();
                $return = $virtuemart_pluginmethod_id;
            } else {
                return 0;
            }
        } else {
            $return = 0;
        }
        return $return;
    }

    /*
     * plgVmonSelectedCalculatePrice
     * Calculate the price (value, tax_id) of the selected method
     * It is called by the calculator
     * This function does NOT to be reimplemented. If not reimplemented, then the default values from this function are taken.
     * @author Valerie Isaksen
     * @cart: VirtueMartCart the current cart
     * @cart_prices: array the new cart prices
     * @return null if the method was not selected, false if the shiiping rate is not valid any more, true otherwise
     *
     *
     */

    public function plgVmonSelectedCalculatePriceShipment(VirtueMartCart $cart, array &$cart_prices, &$cart_prices_name) {

        if (!($method = $this->getVmPluginMethod($cart->virtuemart_shipmentmethod_id))) {
            return NULL; // Another method was selected, do nothing
        }
        if (!$this->selectedThisElement($method->shipment_element)) {
            return false;
        }

        $this->_getUspsFromSession();
        $cart_prices['shipmentTax'] = 0;
        $cart_prices['shipmentValue'] = 0;

        if (!$this->checkConditions($cart, $method, $cart_prices)) {
            return false;
        }

        $cart_prices_name = $this->renderPluginName($method);

        $this->setCartPrices($cart, $cart_prices, $method);

        return true;
    }

    /*
     * update the plugin cart_prices
     *
     * @author Valérie Isaksen
     *
     * @param $cart_prices: $cart_prices['salesPricePayment'] and $cart_prices['paymentTax'] updated. Displayed in the cart.
     * @param $value :   fee
     * @param $tax_id :  tax id
     */

    function setCartPrices(VirtueMartCart $cart, &$cart_prices, $method) {

        if (!class_exists('calculationHelper')) {
            require(JPATH_VM_ADMINISTRATOR . DS . 'helpers' . DS . 'calculationh.php');
        }
        if ($this->is_domestic) {
            $free_shipment = (float)$method->domestic_free_shipment;
        } else {
            $free_shipment = (float)$method->intl_free_shipment;
        }
        if (($free_shipment > 0.00) && $cart_prices['salesPrice'] >= $free_shipment) {
            return;
        }
        $price = self::convertUSPSPrice($this->_usps_rate, $cart->pricesCurrency);

        $cart_prices['shipmentValue'] = $price;
        $cart_prices['shipmentTax'] = 0;
        $taxrules = array();
        $calculator = calculationHelper::getInstance();
        // tax_id=-1 no rules
        if (isset($method->tax_id) and (int)$method->tax_id === -1) {

        } else {
            if (!empty($method->tax_id)) {
                $cart_prices[$this->_psType . '_calc_id'] = $method->tax_id;

                $db = JFactory::getDBO();
                $q = 'SELECT * FROM #__virtuemart_calcs WHERE `virtuemart_calc_id`="' . $method->tax_id . '" ';
                $db->setQuery($q);
                $taxrules = $db->loadAssocList();
            } else {
                //This construction makes trouble, if there are products with different vats in the cart
                //on the other side, it is very unlikely to have different vats in the cart and simultan it is not possible to use a fixed tax rule for the shipment
                if (!empty($calculator->_cartData['VatTax']) and count($calculator->_cartData['VatTax']) == 1) {
                    $taxrules = $calculator->_cartData['VatTax'];
                    foreach ($taxrules as &$rule) {
                        $rule['subTotal'] = $cart_prices[$this->_psType . 'Value'];
                    }

                } else {
                    $taxrules = $calculator->_cartData['taxRulesBill'];
                    if (!empty($taxrules)) {
                        foreach ($taxrules as &$rule) {
                            unset($rule['subTotal']);
                        }
                    }

                }
            }
        }
        if (!class_exists('calculationHelper')) {
            require(JPATH_VM_ADMINISTRATOR . DS . 'helpers' . DS . 'calculationh.php');
        }
        if (count($taxrules) > 0) {
            $cart_prices['salesPriceShipment'] = round($calculator->executeCalculation($taxrules, $cart_prices['shipmentValue']), 4);
            $cart_prices['shipmentTax'] = round($cart_prices['salesPriceShipment'] - $cart_prices['shipmentValue'], 4);

        } else {
            $cart_prices['salesPriceShipment'] = $cart_prices['shipmentValue'];
            $cart_prices['shipmentTax'] = 0;
        }
    }

    /**
     * This method is fired when showing the order details in the frontend.
     * It displays the shipper-specific data.
     *
     * @param integer $order_number The order Number
     * @return mixed Null for shippers that aren't active, text (HTML) otherwise
     * @author ValУЉrie Isaksen
     */
    public function plgVmOnShowOrderFEShipment($virtuemart_order_id, $virtuemart_shipmentmethod_id, &$shipment_name) {

        $this->onShowOrderFE($virtuemart_order_id, $virtuemart_shipmentmethod_id, $shipment_name);
    }


    /**
     * This event is fired after the order has been stored; it gets the shipping method-
     * specific data.
     *
     * @param int $order_id The order_id being processed
     * @param object $cart  the cart
     * @param array $priceData Price information for this order
     * @return mixed Null when this method was not selected, otherwise true
     * @author Valerie Isaksen
     */
    function plgVmConfirmedOrder(VirtueMartCart $cart, $order) {

        if (!($method = $this->getVmPluginMethod($order['details']['BT']->virtuemart_shipmentmethod_id))) {
            return NULL; // Another method was selected, do nothing
        }
        if (!$this->selectedThisElement($method->shipment_element)) {
            return false;
        }
        $currency = CurrencyDisplay::getInstance();
        $vendor_id = 1;
        if (!class_exists('VirtueMartModelVendor')) {
            JLoader::import('vendor', JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_virtuemart' . DS . 'models');
        }
        $vendorCurrency = VirtueMartModelVendor::getVendorCurrency($vendor_id)->vendor_currency;
        $this->_getUspsFromSession();
        $price = self::convertUSPSPrice($this->_usps_rate, $vendorCurrency);

        $values['order_number'] = $order['details']['BT']->order_number;
        $values['shipment_id'] = $order['details']['BT']->virtuemart_shipmentmethod_id;
        $values['shipment_name'] = $this->renderPluginName($method);
        $values['weight'] = $this->getOrderWeight($cart, $method);
        $values['weight_unit'] = $method->weight_unit;
        $values['shipment_cost'] = $price;
        $values['tax_id'] = $method->tax_id;
        $this->storePSPluginInternalData($values);

        return true;
    }

    /**
     * This method is fired when showing the order details in the backend.
     * It displays the shipper-specific data.
     * NOTE, this plugin should NOT be used to display form fields, since it's called outside
     * a form! Use plgVmOnUpdateOrderBE() instead!
     *
     * @param integer $virtuemart_order_id The order ID
     * @param integer $vendorId Vendor ID
     * @param object $_shipInfo Object with the properties 'carrier' and 'name'
     * @return mixed Null for shippers that aren't active, text (HTML) otherwise
     * @author Valerie Isaksen
     */
    public function plgVmOnShowOrderBEShipment($virtuemart_order_id, $virtuemart_shipmentmethod_id) {

        if (!($this->selectedThisByMethodId($virtuemart_shipmentmethod_id))) {
            return NULL;
        }
        $html = $this->getOrderShipmentHtml($virtuemart_order_id);
        return $html;
    }

    function getOrderShipmentHtml($virtuemart_order_id) {

        $db = JFactory::getDBO();
        $q = 'SELECT * FROM `' . $this->_tablename . '` '
            . 'WHERE `virtuemart_order_id` = ' . $virtuemart_order_id;
        $db->setQuery($q);
        if (!($shipinfo = $db->loadObject())) {
            vmWarn(500, $q . " " . $db->getErrorMsg());
            return '';
        }

        if (!class_exists('CurrencyDisplay')) {
            require(JPATH_VM_ADMINISTRATOR . DS . 'helpers' . DS . 'currencydisplay.php');
        }

        $currency = CurrencyDisplay::getInstance();
        $tax = ShopFunctions::getTaxByID($shipinfo->tax_id);
        $taxDisplay = is_array($tax) ? $tax['calc_value'] . ' ' . $tax['calc_value_mathop'] : $shipinfo->tax_id;
        $taxDisplay = ($taxDisplay == -1) ? JText::_('COM_VIRTUEMART_PRODUCT_TAX_NONE') : $taxDisplay;

        $html = '<table class="adminlist">' . "\n";
        $html .= $this->getHtmlHeaderBE();
        $html .= $this->getHtmlRowBE('ALATAK_USPS_SHIPPING_NAME', $shipinfo->shipment_name);
        $html .= $this->getHtmlRowBE('ALATAK_USPS_WEIGHT', $shipinfo->weight . ' ' . ShopFunctions::renderWeightUnit($shipinfo->weight_unit));
        $html .= $this->getHtmlRowBE('ALATAK_USPS_COST', $currency->priceDisplay($shipinfo->shipment_cost, 1, false));
        vmdebug('xx', $shipinfo->shipment_cost);
        //$html .= $this->getHtmlRowBE('WEIGHT_COUNTRIES_PACKAGE_FEE', $currency->priceDisplay($shipinfo->shipment_package_fee, '', false));
        $html .= $this->getHtmlRowBE('ALATAK_USPS_TAX', $taxDisplay);
        $html .= '</table>' . "\n";

        return $html;
    }

    function _getShippingCost($method, VirtueMartCart $cart) {

        $value = $this->getShippingValue($method, $cart->pricesUnformatted);
        $shipping_tax_id = $this->getShippingTaxId($method, $cart);
        $tax = ShopFunctions::getTaxByID($shipping_tax_id);
        $taxDisplay = is_array($tax) ? $tax['calc_value'] . ' ' . $tax['calc_value_mathop'] : $shipping_tax_id;
        $taxDisplay = ($taxDisplay == -1) ? JText::_('COM_VIRTUEMART_PRODUCT_TAX_NONE') : $taxDisplay;
    }

    function getShippingValue($method, $cart_prices) {

        $free_shipping = $method->free_shipping;
        if ($free_shipping && $cart_prices['salesPrice'] >= $free_shipping) {
            return 0;
        } else {
            return $method->rate_value + $method->package_fee;
        }
    }

    function getShippingTaxId($method) {

        return $method->shipping_tax_id;
    }

    function checkShippingConditions($cart, $method, $cart_prices) {

        $response = $this->_getUSPSrates($cart, $method, $cart_prices);
        vmdebug('checkShippingConditions', $response);
        return count($response);
    }


    function checkConditions($cart, $method, $cart_prices) {
        $orderWeight = $this->getOrderWeight($cart, $method);
        $address = (($cart->ST == 0) ? $cart->BT : $cart->ST);

        $nbShipment = 0;
        $countries = array();
        if (!empty($method->countries_domestic)) {
            if (!is_array($method->countries_domestic)) {
                $method->countries_domestic = (array)$method->countries_domestic;
            }
        }
        if (!empty($method->countries_intl)) {
            if (!is_array($method->countries_intl)) {
                $method->countries_intl = (array)$method->countries_intl;
            }
        } else {
            $method->countries_intl = array();
        }
        $countries = array_merge($method->countries_domestic, $method->countries_intl);

        $db = JFactory::getDBO();
        $query = 'SELECT virtuemart_country_id';
        $query .= ' FROM `#__virtuemart_countries`';
        $query .= ' WHERE  ';
        $or = ' OR ';
        foreach ($countries as $country) {
            $query .= '`country_2_code` =\'' . $country . '\'' . $or;
        }
        $query = substr($query, 0, -strlen($or));
        $db->setQuery($query);
        $countries_id = $db->loadResultArray();

        // probably did not gave his BT:ST address
        if (!is_array($address)) {
            $address = array();
            $address['zip'] = 0;
            $address['virtuemart_country_id'] = 0;
        }

        if (!isset($address['virtuemart_country_id'])) {
            $address['virtuemart_country_id'] = 0;
        }
        //if (in_array($address['virtuemart_country_id'], $countries_id) || count($countries_id) == 0) {
        $responses = $this->_getUSPSrates($cart, $method, $cart_prices);
        if ($responses) {
            if ($this->_getUspsFromSession()) {
                if (is_array($responses)) {
                    foreach ($responses as $service => $response) {
                        if ($this->is_domestic) {
                            $serviceNameToCompare = $service;
                        } else {
                            $serviceNameToCompare = $this->getIntlServiceName($response);
                        }
                        vmdebug('serviceNameToCompare', $serviceNameToCompare, $this->_usps_name, html_entity_decode($serviceNameToCompare, ENT_COMPAT, 'UTF-8'), $response);
                        $serviceNameToCompare = html_entity_decode($serviceNameToCompare, ENT_COMPAT, 'UTF-8');
                        $ups_name = html_entity_decode($this->_usps_name, ENT_COMPAT, 'UTF-8');

                        if (($serviceNameToCompare == $ups_name) OR (html_entity_decode($serviceNameToCompare, ENT_COMPAT, 'UTF-8') == $ups_name)) {
                            if ($this->_usps_rate != $response['rate']) {
                                VmInfo('USPS price updated');
                            }
                            $this->_usps_rate = $response['rate'];
                            $this->_setUspsIntoSession();
                            return true;
                        }
                    }
                }
            }
        }
        // return false;
        //}

        return false;
    }

    function _weightCond($orderWeight, $method) {

        return true;
    }

    function _getUSPSrates($cart, $method, $cart_prices) {

        $usps_to_weight_unit = 'LB'; //$method->weight_unit'); // must be in pounds
        $to_address = (($cart->ST == 0) ? $cart->BT : $cart->ST);
        if (empty($to_address['zip'])) {
            return NULL;
        }
        $this->from_zip = $this->getVendorZip();

        $order_weight = 0;

        //$packages = array();


        $this->to_zip = substr($to_address['zip'], 0, 5);
        $this->to_country = ShopFunctions::getCountryByID($to_address['virtuemart_country_id'], 'country_2_code');
        $this->to_country_name = ShopFunctions::getCountryByID($to_address['virtuemart_country_id'], 'country_name');

        vmdebug('cart', $cart);
        $this->valueOfContents = $cart_prices['salesPrice'];

        vmdebug('_getUSPSrates', $cart_prices);
        $this->is_domestic = false;
        $domestic_countries = $method->countries_domestic;
        $domestic = in_array($this->to_country, $domestic_countries);
        if ($domestic and $method->domestic_enable) {
            $this->is_domestic = true;
            $xmlPost = $this->_getDomesticRequest($method, $cart, $cart_prices);
        } elseif ($method->intl_enable == true and !$domestic) {
            $intl_countries = $method->countries_intl;
            if (empty($intl_countries) or in_array($this->to_country, $intl_countries)) { //domestic if US, PR or VI
                $xmlPost = $this->_getIntlRequest($method, $cart, $cart_prices);
            } else {
                return NULL;
            }
        } else {
            return NULL;
        }
        if (empty($xmlPost)) {
            return NULL;
        }
        $xmlResult = "";
        if (!$this->_sendRequest($method, $xmlPost, $xmlResult)) {
            return $xmlResult; // contains error message
        }

        $response = $this->_handleResponse($method, $xmlResult, $cart);
        if (is_array($response)) {
            uasort($response, array($this, 'uspsCompareRates'));
        }

        return $response;
    }

    function createPackage($method, $products, $cart, $cart_prices, $prefix = 'domestic_') {
        vmdebug("USPS createPackage XXX", $method->shipment_strategy, $cart->pricesUnformatted, $cart_prices);

        if ($method->shipment_strategy == 'volume') {
            return $this->createPackageVolume($method, $products, $cart, $prefix);
        } elseif ($method->shipment_strategy == 'weight') {
            return $this->createPackageWeight($method, $products, $cart, $cart_prices);
        } else {
            return $this->createPackageIndividual($method, $products, $cart, $cart_prices);
        }
    }

    function createPackageWeight($method, $products, $cart, $cart_prices) {
        vmdebug("createPackageWeight");
        $i = 0;
        $order_weight = 0;
        $order_volume = 0;
        $to_weight_unit = substr($method->weight_unit, 0, 2);
        $order_weight = $this->getOrderWeight($cart, $method);

        if ($order_weight == 0) {
            vmdebug('UPS: Total weight is 0.');
            return NULL;
        }
        if ($order_weight < 0.1) {
            $order_weight = 0.1;
        }

        $order_weight = round($order_weight, 1);
        vmdebug("order_weight after rounding", $order_weight);

        $maxUSPSWeight = $this->getMaxUSPSWeight($method);
        vmdebug("createPackageWeight maxUSPSWeight", $maxUSPSWeight);

        $nb_box = ceil($order_weight / $maxUSPSWeight);
        $order_weight_per_box = $order_weight / $nb_box;
        $order_weight_per_box = round($order_weight_per_box, 1);
        $package_value = $cart_prices['salesPrice'] / $nb_box;
        vmdebug('USPS createPackageWeight $package_value', $package_value, $cart->pricesUnformatted);
        $packages = array();
        $nb = 0;
        $max_length = 0;
        $max_height = 0;
        $max_width = 0;
        // getMax dimensions. It is not the best method..
        foreach ($products as $product) {
            $dimensions[$i] = $this->getDimensions($product->product_length, $product->product_height, $product->product_width, $product->product_lwh_uom, $method);
            $max_length = max($dimensions[$i]['length'], $max_length);
            $max_height = max($dimensions[$i]['height'], $max_height);
            $max_width = max($dimensions[$i]['width'], $max_width);
            $i++;

        }
        for ($i = 0; $i < $nb_box; $i++) {
            $package['packaging_dimension_unit'] = 'IN'; //$method->packaging_dimension_unit;
            $package['length'] = $max_length;
            $package['width'] = $max_width;
            $package['height'] = $max_height;
            $package['weight'] = $order_weight_per_box;
            $package['volume'] = 0;
            $package['girth'] = 0;
            $package['value'] = $package_value;
            $packages[$i++] = $package;

        }

        return $packages;
    }

    function createPackageVolume($method, $products, $cart, $prefix) {
        vmdebug("createPackageVolume");
        $i = 0;
        $order_weight = 0;
        $order_volume = 0;
        $to_weight_unit = substr($method->weight_unit, 0, 2);

        $dimensions_padding = $method->dimensions_padding;
        $max_length = 0;
        $max_height = 0;
        $max_width = 0;
        foreach ($products as $product) {
            // getWeigth adds thepadding
            $order_weight = $this->getWeight(ShopFunctions::convertWeigthUnit($product->product_weight, $product->product_weight_uom, $to_weight_unit), $product->quantity, $method) + $order_weight;
            // getDimensions adds the padding
            $dimensions[$i] = $this->getDimensions($product->product_length, $product->product_height, $product->product_width, $product->product_lwh_uom, $method);
            $volume[$i] = $dimensions[$i]['width'] * $dimensions[$i]['height'] * $dimensions[$i]['length'] * $product->quantity;
            $order_volume = $volume[$i] + $order_volume;
            $i++;

        }
        if ($order_volume == 0) {
            $msg = 'USPS: You choose the volume shipment strategy, but the order volume calculated is 0. (' . $prefix . ')';
            vmError($msg, $msg);
            return NULL;
        }
        if ($order_weight == 0) {
            vmdebug('USPS: Total weight is 0.');
            return NULL;
        }
        if ($order_weight < 0.1) {
            $order_weight = 0.1;
        }

        $order_weight = round($order_weight, 2);
        vmdebug("order_weight after rounding", $order_weight);
        $boxes = $this->getBoxes($method, $prefix);
        if (empty($boxes)) {
            vmError('No boxes are defined for ' . $prefix, 'No boxes are defined for ' . $prefix);
            return NULL;
        }
        $maxBoxVolume = $this->getMaxBoxVolume($boxes);

        $nb_box = ceil($order_volume / $maxBoxVolume);
        if ($nb_box == 0) {
            vmError('Nb of boxes is 0 for ' . $prefix, 'Nb of boxes is 0 for ' . $prefix);
            return NULL;
        }
        vmdebug('USPS createPackage', "order volume " . $order_volume, "maxvolume " . $maxBoxVolume, "nb box", $nb_box);
        $order_weight_per_box = $order_weight / $nb_box;

        $order_weight_per_box = round($order_weight_per_box, 1);
        $package_value = $cart->pricesUnformatted['salesPrice'] / $nb_box;

        for ($i = 0; $i < $nb_box; $i++) {
            for ($i = 0; $i < $nb_box; $i++) {
                $package['packaging_dimension_unit'] = 'IN'; // $method->packaging_dimension_unit;
                $package['length'] = $boxes[$maxBoxVolume]['length'];
                $package['width'] = $boxes[$maxBoxVolume]['width'];
                $package['height'] = $boxes[$maxBoxVolume]['height'];
                $package['weight'] = $order_weight_per_box;
                $package['volume'] = $boxes[$maxBoxVolume]['box'];
                $package['girth'] = 0;
                $package['value'] = $package_value;
                $packages[$i++] = $package;

            }
        }

        return $packages;
    }

    function createPackageIndividual($method, $products, $cart, $cart_prices) {
        vmdebug("createPackageIndividual");

        //$to_weight_unit = substr ($method->weight_unit, 0, 2);
        $to_weight_unit = 'LB';
        $xml = "";
        $packages = array();
        $nb = 0;

        $dimensions_padding = $method->dimensions_padding;
        foreach ($products as $pkey => $product) {
            $product_weight = ShopFunctions::convertWeigthUnit($product->product_weight, $product->product_weight_uom, $to_weight_unit);
            // getWeight adds the padding
            $product_weight = $this->getWeight($product_weight, 1, $method);

            $padding = $method->weigth_padding;
            if (preg_match('/%$/', $padding)) {
                $product_weight += ($product_weight * substr($padding, 0, -1) * 0.01);
            } else {
                $product_weight = $product_weight + $padding;
            }

            if ($product_weight == 0) {
                vmdebug('USPS: Product weight is 0');
                continue;
            }
            if ($product_weight < 0.1) {
                $product_weight = 0.1;
            }

            $product_weight = round($product_weight, 2);
            $package_value = $product->prices['salesPrice'];
            // getDimensions adds the padding
            $dimensions = $this->getDimensions($product->product_length, $product->product_height, $product->product_width, $product->product_lwh_uom, $method);
            $girth = ($dimensions ['width'] * 2) + ($dimensions ['height'] * 2);
            $volume = $dimensions ['width'] * $dimensions ['height'] * $dimensions ['length'];
            for ($i = 0; $i < $product->quantity; $i++) {
                $package['dimension_unit'] = 'IN';
                $package['length'] = $dimensions ['length'];
                $package['width'] = $dimensions ['width'];
                $package['height'] = $dimensions ['height'];
                $package['weight'] = $product_weight;
                $package['volume'] = $volume;
                $package['girth'] = $girth;
                $package['value'] = $package_value;

                $packages[$nb++] = $package;
            }

        }

        return $packages;
    }

    function getMaxBoxVolume($boxes) {

        // the boxes are already sorted by volume
        //$count = count ($boxes);
        return max(array_keys($boxes));
        //return $boxes[$count - 1];

    }

    function getBoxes($method, $prefix) {

        $i = 0;
        $param = $prefix . 'packaging_dimension';
        $confBoxes = explode(PHP_EOL, $method->$param);
        foreach ($confBoxes as $tempBox) {
            $dimensions = explode(";", $tempBox);
            foreach ($dimensions as $tempDimension) {
                $temp = explode("=", $tempDimension);
                $boxes[$i] [$temp[0]] = $temp[1];
            }
            $i++;
        }
        foreach ($boxes as $key => $box) {
            $volume = $box['length'] * $box['width'] * $box['height'];
            $sorted_boxes[$volume] = $box;
            $sorted_boxes[$volume]['volume'] = $box['length'] * $box['width'] * $box['height'];
        }
        asort($sorted_boxes);

        return $sorted_boxes;
    }

    function getDimensions($length, $height, $width, $lwh_uom, $method) {

        if (empty($method->dimensions_padding)) {
            $dimensions_padding = 0;
        } else {
            $dimensions_padding = $method->dimensions_padding;
        }
        $length = ShopFunctions::convertDimensionUnit($length, $lwh_uom, 'IN');
        $height = ShopFunctions::convertDimensionUnit($height, $lwh_uom, 'IN');
        $width = ShopFunctions::convertDimensionUnit($width, $lwh_uom, 'IN');

        if (preg_match('/%$/', $dimensions_padding)) {
            $length += ($length * substr($dimensions_padding, 0, -1) * 0.01);
            $height += ($height * substr($dimensions_padding, 0, -1) * 0.01);
            $width += ($width * substr($dimensions_padding, 0, -1) * 0.01);
        } else {
            $length = $length + $dimensions_padding;
            $height = $height + $dimensions_padding;
            $width = $width + $dimensions_padding;
        }

        $dimensions['length'] = max($length, $height, $width);
        $dimensions['width'] = min($length, $height, $width);
        if ($dimensions['length'] == $length) {
            if ($dimensions['width'] == $width) {
                $dimensions['height'] = $height;
            } else {
                $dimensions['height'] = $width;
            }
        } elseif ($dimensions['length'] == $width) {
            if ($dimensions['width'] == $length) {
                $dimensions['height'] = $height;
            } else {
                $dimensions['height'] = $height;
            }
        } else {
            if ($dimensions['width'] == $length) {
                $dimensions['height'] = $width;
            } else {
                $dimensions['height'] = $length;
            }
        }

        return $dimensions;
    }

    function getValue($method, $priceValue, $pricesCurrency) {

        $insured_value = '';
        if ($method->insured_value) {


        }
        return $insured_value;
    }

    function getMaxUSPSWeight($method) {

        /* Packages can be up to 150 lbs (70 kg). */

        return 70;


    }

    function getWeight($product_weight, $quantity, $method) {

        if (empty($method->weight_padding)) {
            $weight_padding = 0;
        } else {
            $weight_padding = $method->weight_padding;
        }
        //Pad the shipping weight to allow weight for shipping materials
        if (preg_match('/%$/', $weight_padding)) {
            $product_weight += ($product_weight * substr($weight_padding, 0, -1) * 0.01);
        } else {
            $product_weight = $product_weight + $weight_padding;
        }
        return $product_weight * $quantity;
    }

    /**
     *
     * https://www.usps.com/webtools/htm/Rate-Calculators-v1-3.htm
     *
     * @param type $uspsParams
     * @param type $method
     */
    function _getDomesticRequest($method, $cart, $cart_prices) {

        $usps_username = $this->getUsername($method);
        $usps_password = $this->getPassword($method);
        $shpService = 'All';
        $fcmailtype = "PARCEL";

        //$str = "<Service>FIRST CLASS</Service>";
        $str .= "<FirstClassMailType>" . $fcmailtype . "</FirstClassMailType>";
        $packages = $this->createPackage($method, $cart->products, $cart, $cart_prices, 'domestic_');
        if (empty($packages)) {
            return NULL;
        }
        //the xml that will be posted to usps for domestic rates
        $xmlPost = 'API=RateV4&XML=<RateV4Request USERID="' . $usps_username . '">'; // Password is not required anymore
        foreach ($packages as $key => $package) {
            $xmlPost .= '<Package ID="' . $key . '">';
            $xmlPost .= "<Service>" . $shpService . "</Service>";
            $xmlPost .= $str;
            $xmlPost .= "<ZipOrigination>" . $this->from_zip . "</ZipOrigination>";
            $xmlPost .= "<ZipDestination>" . $this->to_zip . "</ZipDestination>";
            $xmlPost .= "<Pounds>" . floor($package['weight']) . "</Pounds>";
            $xmlPost .= "<Ounces>" . ceil(16 * ($package['weight'] - floor($package['weight']))) . "</Ounces>";

            // Container type
            if ($method->package_size == "LARGE") {
                $xmlPost .= "<Container>RECTANGULAR</Container>"; // TODO: NONRECTANGULAR/GIRTH support
            } else {
                $xmlPost .= "<Container />";
            }
            // If the dimensions are big, we may have to force to LARGE, otherwise USPS returns all kind of sizes
            $package_size = $this->getPackageSize($package, $method);

            $xmlPost .= "<Size>" . $package_size . "</Size>";

            if ($package_size == "LARGE" or $method->send_dimensions == 1) {
                $xmlPost .= "<Width>" . round($package['width'], 2) . "</Width>";
                $xmlPost .= "<Length>" . round($package['length'], 2) . "</Length>";
                $xmlPost .= "<Height>" . round($package['height'], 2) . "</Height>";
            }
            $xmlPost .= "<Machinable>" . $method->machinable . "</Machinable>";
            $xmlPost .= "</Package>";
        }
        $xmlPost .= "</RateV4Request>";
        //build array holding domestic service names that are active in VM
        vmdebug('USPS: _getDomesticRequest', "<textarea cols='80' rows='15'>" . $xmlPost . "</textarea>");
        return $xmlPost;
    }

    /**
     *
     * @param type $uspsParams
     * @param type $method
     */
    function _getIntlRequest($method, $cart, $cart_prices) {

        $usps_username = $this->getUsername($method);
        $usps_password = $this->getPassword($method);
        $extraServices = $method->extraServices;
        $usps_packageid = 0;

        $packages = $this->createPackage($method, $cart->products, $cart, $cart_prices, 'intl_');
        if (empty($packages)) {
            return NULL;
        }
        //the xml that will be posted to usps for international rates
        $xmlPost = 'API=IntlRateV2&XML=<IntlRateV2Request USERID="' . $usps_username . '">';
        //$xmlPost .= '<Revision>2</Revision>';
        foreach ($packages as $key => $package) {
            $xmlPost .= '<Package ID="' . $key . '">';
            $xmlPost .= "<Pounds>" . floor($package['weight']) . "</Pounds>";
            $xmlPost .= "<Ounces>" . ceil(16 * ($package['weight'] - floor($package['weight']))) . "</Ounces>";
            $xmlPost .= "<Machinable>" . $method->machinable . "</Machinable>";
            $xmlPost .= "<MailType>Package</MailType>";
            /*
            $xmlPost .= "<GXG>" .
                '<POBoxFlag>N</POBoxFlag>' . //Specify as "Y" if the destination is a post office box.
                '<GiftFlag>N</GiftFlag>' . // Specify as "Y" if the package contains a gift.
                '</GXG>';*/
            if ($package['value']) {
                $xmlPost .= '<ValueOfContents>' . $package['value'] . '</ValueOfContents>'; //If specified, used to compute Insurance fee (if insurance is available for service and destination) and indemnity coverage.
            }

            $xmlPost .= "<Country>" . $this->to_country_name . "</Country>";

            // Container type
            /*
            if ($method->package_size == "LARGE") {
                $xmlPost .= "<Container>RECTANGULAR</Container>"; // TODO: NONRECTANGULAR/GIRTH support
            } else {
                $xmlPost .= "<Container />";
            }
            */
            // If the dimensions are big, we may have to force to LARGE, otherwise USPS returns all kind of sizes
            $package_size = $this->getPackageSize($package, $method);

            $xmlPost .= "<Container>RECTANGULAR</Container>"; // TODO: NONRECTANGULAR/GIRTH support
            $xmlPost .= "<Size>" . $package_size . "</Size>";
            if ($package_size == "LARGE" or $method->send_dimensions == 1) {
                $xmlPost .= "<Width>" . round($package['width'], 2) . "</Width>";
                $xmlPost .= "<Length>" . round($package['length'], 2) . "</Length>";
                $xmlPost .= "<Height>" . round($package['height'], 2) . "</Height>";
                $xmlPost .= '<Girth>' . round($package['girth'], 2) . '</Girth>';
            } else {
                $xmlPost .= "<Width>0</Width>";
                $xmlPost .= "<Length>0</Length>";
                $xmlPost .= "<Height>0</Height>";
                $xmlPost .= '<Girth>0</Girth>';
            }
            //$xmlPost .= "<OriginZip>" . $this->from_zip . "</OriginZip>";
            //$xmlPost .= '<CommercialFlag>N</CommercialFlag>';

            if ($extraServices) {
                $xmlPost .= '<ExtraServices>';
                foreach ($extraServices as $extraService) {
                    $xmlPost .= '<ExtraService>' . $extraService . '</ExtraService>';
                }
                $xmlPost .= '</ExtraServices>';
            }

            $xmlPost .= "</Package>";
        }
        $xmlPost .= "</IntlRateV2Request>";

        /*
                $xmlPost = 	'API=IntlRateV2&XML=<IntlRateV2Request USERID="534RCSKY4839">' .
                                '<Revision>2</Revision>' .
                                '<Package ID="0">' .
                                '<Pounds>0</Pounds>' .
                                '<Ounces>6</Ounces>' .
                    '<Machinable>True</Machinable>'.
                                '<MailType>Package</MailType>' .
                                '<GXG>' .
                                    '<POBoxFlag>N</POBoxFlag>' .
                                    '<GiftFlag>N</GiftFlag>' .
                                '</GXG>' .
                                '<ValueOfContents>10</ValueOfContents>' .
                                '<Country>Australia</Country>' .
                                '<Container>RECTANGULAR</Container>' .
                                '<Size>LARGE</Size>' .
                                '<Width>2</Width>' .
                                '<Length>10</Length>' .
                                '<Height>6</Height>' .
                                '<Girth>0</Girth>' .
                                '<OriginZip>90805</OriginZip>' .
                                '<CommercialFlag>N</CommercialFlag>' .
        '<ExtraServices>' .
                                '<ExtraService>1</ExtraService>' .
                            '</ExtraServices>' .
                                '</Package>' .
                                '</IntlRateV2Request>';
        */

        vmdebug('USPS: _getIntlRequest', "<textarea cols='80' rows='15'>" . $xmlPost . "</textarea>");

        return $xmlPost;
    }

    static $uspsCache;

    function _sendRequest($method, $xmlPost, &$xmlResult) {

        if (!empty(self::$uspsCache)) {
            if (isset(self::$uspsCache[$xmlPost])) {
                if (isset(self::$uspsCache[$xmlPost]['method'])) {
                    if (self::$uspsCache[$xmlPost]['method'] == $method) {
                        $xmlResult = self::$uspsCache[$xmlPost]['result'];
                        vmdebug('USPS: FROM CACHE $xmlPost', "<textarea cols='80' rows='15'>" . $xmlPost . "</textarea>");
                        vmdebug('USPS: FROM CACHE xmlResult', "<textarea cols='80' rows='15'>" . $xmlResult . "</textarea>");
                        return true;
                    }
                }
            }
        }


        // Using cURL is Up-To-Date and easier!!
        $usps_server = $this->getServer($method);
        $proxy_server = $method->proxy_server;
        $curl_request = curl_init();
        curl_setopt($curl_request, CURLOPT_URL, $usps_server);
        curl_setopt($curl_request, CURLOPT_POST, 1);
        curl_setopt($curl_request, CURLOPT_FAILONERROR, true);
        curl_setopt($curl_request, CURLOPT_POSTFIELDS, $xmlPost);
        curl_setopt($curl_request, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl_request, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl_request, CURLOPT_CONNECTTIMEOUT, 20);
        curl_setopt($curl_request, CURLOPT_TIMEOUT, 30);
        if (!empty($proxy_server)) {
            curl_setopt($curl_request, CURLOPT_HTTPPROXYTUNNEL, TRUE);
            curl_setopt($curl_request, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);
            curl_setopt($curl_request, CURLOPT_PROXY, $proxy_server);
        }
        $xmlResult = curl_exec($curl_request);
        vmdebug('USPS: xmlResult', $usps_server, "<textarea cols='80' rows='15'>" . $xmlResult . "</textarea>");

        $error = curl_error($curl_request);
        curl_close($curl_request);

        if (!empty($error)) {
            if ($method->report_usps_error) {
                $xmlResult = $error . " " . $usps_server; //JText::_ ('VMSHIPMENT_ALATAK_USPS_ERROR_USPS');
                return false;
            }
        }
        if (empty(self::$uspsCache)) {
            self::$uspsCache = array();
        }
        if (empty(self::$uspsCache[$xmlPost])) {
            self::$uspsCache[$xmlPost] = array();
        }
        self::$uspsCache[$xmlPost]['method'] = $method;
        self::$uspsCache[$xmlPost]['result'] = $xmlResult;

        return true;
    }


    function _handleResponse($method, $xmlResult, $cart) {

        //vmdebug('_handleResponse', $xmlResult);
        //echo $xmlResult;
        if (empty($xmlResult)) {
            return NULL;
        }
        $document_xml = new DomDocument; // Instanciation d'un DOMDocument
        $document_xml->loadXML($xmlResult); // On charge

        if (strstr($xmlResult, "Error")) {
            $error = true;
            $html = "<span class=\"message\">" . JText::_('VMSHIPMENT_ALATAK_USPS_REPONSE_ERROR') . "</span><br/>";
            /*
             <?xml version="1.0"?>
             <RateV4Response><Package ID="0">
            * <Error><Number>-2147219487</Number>
            * <Source>DomesticRatesV4;clsRateV4.ValidateMachinable;RateEngineV4.ProcessRequest</Source>
            * <Description>Machinable value must be 'True' or 'False' for service type Parcel Post and service type All.</Description>
            * <HelpFile></HelpFile><HelpContext>1000440</HelpContext></Error>
            * </Package></RateV4Response>
            */

            $error_code = $document_xml->getElementsByTagName("Number");
            $error_code = $error_code->item(0);
            $error_code = $error_code->nodeValue;
            $html .= "<strong>" . JText::_('VMSHIPMENT_ALATAK_USPS_REPONSE_ERROR_CODE') . '</strong> ' . $error_code . "<br/>";
            $error_desc = $document_xml->getElementsByTagName("Description");
            $error_desc = $error_desc->item(0);
            $error_desc = $error_desc->nodeValue;
            $html .= "<strong>" . JText::_('VMSHIPMENT_ALATAK_USPS_REPONSE_ERROR_DESCRIPTION') . '</strong> ' . $error_desc . "<br/>";
            return $html;
        }

        if ($this->is_domestic) {
            return $this->_handleResponseDomestic($method, $document_xml, $cart);
        } else {
            return $this->_handleResponseIntl($method, $document_xml, $cart);
        }
    }

    function _handleResponseDomestic($method, $document_xml, $cart) {

        //vmdebug('USPS: _handleResponseDomestic',"<textarea cols='80' rows='15'>".$document_xml."</textarea>");

        $services_active = $this->_getDomesticServices($method->domestic);
        $ship_postage = '';
        $fee = $method->domestic_fee;
        vmdebug('_handleResponseDomestic', $fee);
        $matchingNodes = & $document_xml->getElementsByTagName("Postage");
        $count = 0;
        $servicesNotConfigurated = array();
        $isFreeShipment = $this->isDomesticFreeShipment($method, $cart->pricesUnformatted);
        vmdebug('isFreeShipment', (int)$isFreeShipment);
        vmdebug('Active services', $services_active);

        if ($matchingNodes != NULL) {
            for ($i = 0; $i < $matchingNodes->length; $i++) {
                $currNode = & $matchingNodes->item($i);
                $serviceName = $currNode->getElementsByTagName("MailService");
                $serviceName = $serviceName->item(0);
                //v4.2.1 1/2/2011 - remove special characters
                //$serviceName = $serviceName->getText();
                $serviceNameKey = $this->filterServiceName($serviceName->nodeValue);
                $serviceNameKey = $this->removeDay($serviceNameKey);

                //vmDebug('_handleResponseDomestic ServiceName returned', $serviceName);
                //if service in XML is contained in active service array add as option
                if (empty($services_active) or in_array($serviceNameKey, $services_active)) {
                    if (!isset($ship_postage[$serviceNameKey])) {
                        $ship_postage[$serviceNameKey] ['rate'] = 0;
                    }
                    $this_rate = $currNode->getElementsByTagName("Rate");
                    $this_rate = $this_rate->item(0);
                    $this_rate = $this_rate->nodeValue;

                    if ($isFreeShipment) {
                        $ship_postage[$serviceNameKey] ['rate'] = 0;
                    } else {
                        if (preg_match('/%$/', $fee)) {
                            $ship_postage[$serviceNameKey]['rate'] += $this_rate * (1 + substr($fee, 0, -1) / 100);
                        } else {
                            $ship_postage[$serviceNameKey] ['rate'] += $this_rate + (float)$fee;
                        }
                    }


                } else {
                    $servicesNotConfigurated[$i] = $serviceNameKey;
                }
            }
        }
        vmDebug('_handleResponseDomestic $ship_postage', $ship_postage);
        if (empty($ship_postage)) {
            $this->printVmAdminInfo($servicesNotConfigurated, $services_active);
        }
        return $ship_postage;
    }

    function _handleResponseIntl($method, $document_xml, $cart) {

        $services_active = $this->_getIntlServices($method->intl);
        vmdebug('_handleResponseIntl', $services_active);
        $fee = $method->intl_fee;
        $isFreeShipment = $this->isIntlFreeShipment($method, $cart->pricesUnformatted);

        $ship_postage = array();
        $servicesNotConfigurated = array();
        $matchingNodes = & $document_xml->getElementsByTagName("Service");
        if ($matchingNodes != NULL) {
            for ($i = 0; $i < $matchingNodes->length; $i++) {
                $currNode = & $matchingNodes->item($i);
                $serviceName = $currNode->getElementsByTagName("SvcDescription");
                $serviceName = $serviceName->item(0);
                //v4.2.1 1/2/2011 - remove special characters
                //$serviceName = $serviceName->getText();
                $serviceNameKey = $this->filterServiceName($serviceName->nodeValue);
                $serviceNameKey = $this->removeDay($serviceNameKey);
                //if service in XML is contained in active service array add as option
                if (in_array($serviceNameKey, $services_active) or empty($services_active)) {
                    if (!isset($ship_postage[$serviceNameKey])) {
                        $ship_postage[$serviceNameKey] ['rate'] = 0;
                    }
                    $this_rate = $currNode->getElementsByTagName("Postage");
                    $this_rate = $this_rate->item(0);
                    $this_rate = $this_rate->nodeValue;

                    if ($isFreeShipment) {
                        $ship_postage[$serviceNameKey] ['rate'] = 0;
                    } else {
                        if (preg_match('/%$/', $fee)) {
                            $ship_postage[$serviceNameKey]['rate'] += $this_rate * (1 + substr($fee, 0, -1) / 100);
                        } else {
                            $ship_postage[$serviceNameKey]['rate'] += $this_rate + (float)$fee;
                        }
                    }

                    $mailType = $currNode->getElementsByTagName("MailType");
                    $mailType = $mailType->item(0);
                    $ship_postage[$serviceNameKey]['MailType'] = $mailType->nodeValue;
                    $SvcCommitments = $currNode->getElementsByTagName("SvcCommitments");
                    $SvcCommitments = $SvcCommitments->item(0);
                    $ship_postage[$serviceNameKey]['SvcCommitments'] = $SvcCommitments->nodeValue;
                    $SvcDescription = $currNode->getElementsByTagName("SvcDescription");
                    $SvcDescription = $SvcDescription->item(0);
                    $ship_postage[$serviceNameKey]['SvcDescription'] = $SvcDescription->nodeValue;
                    $MaxDimensions = $currNode->getElementsByTagName("MaxDimensions");
                    $MaxDimensions = $MaxDimensions->item(0);
                    $ship_postage[$serviceNameKey]['MaxDimensions'] = $MaxDimensions->nodeValue;
                    $MaxWeight = $currNode->getElementsByTagName("MaxWeight");
                    $MaxWeight = $MaxWeight->item(0);
                    $ship_postage[$serviceNameKey]['MaxWeight'] = $MaxWeight->nodeValue;
                    //$temp_shipping[$count][$ship_postage[$count]['MaxWeight']]=$ship_postage[$count]['SvcCommitments'];
                } else {
                    $servicesNotConfigurated[$i] = $serviceNameKey;
                }
            }
        }
        //echo "<pre>"; print_r($ship_postage);echo "</pre>";
        if (empty($ship_postage)) {
            $this->printVmAdminInfo($servicesNotConfigurated, $services_active);
        }
        vmdebug('_handleResponseIntl', $ship_postage);

        return $ship_postage;
    }

    function getPackageSize($package, $method) {
        $package_size = $method->package_size;
        if (($package['length'] > 12) or ($package['width'] > 12)  or  ($package['height'] > 12)) {
            $package_size = 'LARGE';
            vmDebug('USPS getPackageSize forcing to LARGE');
        }
        return $package_size;
    }

    function filterServiceName($serviceName) {
        $serviceNameKey = str_replace("&lt;sup&gt;&amp;reg;&lt;/sup&gt;", "", $serviceName);
        $serviceNameKey = str_replace("&lt;sup&gt;&amp;trade;&lt;/sup&gt;", "", $serviceNameKey);
        $serviceNameKey = str_replace("&lt;sup&gt;&#8482;&lt;/sup&gt;", "", $serviceNameKey);
        $serviceNameKey = str_replace("&lt;sup&gt;&#174;&lt;/sup&gt;", "", $serviceNameKey);
        $serviceNameKey = str_replace("l&lt;sup&gt;&#174;&lt;/sup&gt;", "", $serviceNameKey);

        return $serviceNameKey;
    }

    function removeDay($serviceName) {

        $serviceName = str_replace(array(' 1-Day', ' 2-Day', ' 3-Day', ' Military', ' DPO'), '', $serviceName);

        return $serviceName;
    }

    function printVmAdminInfo($servicesNotConfigurated, $services_active) {

        $servicesNotConfiguratedHtml = "<ul>";

        foreach ($servicesNotConfigurated as $value) {
            $servicesNotConfiguratedHtml .= "<li>" . $value . "</li>";
        }
        $servicesNotConfiguratedHtml .= "</ul>";
        $servicesConfiguratedHtml = "<ul>";
        foreach ($services_active as $value) {
            $servicesConfiguratedHtml .= "<li>" . $value . "</li>";
        }
        $servicesConfiguratedHtml .= "</ul>";

        vmdebug('USPS: The delivery services configurated in the shipment method do no match the Delivery Services returned by USPS.<br />The Delivery Services and Service codes returned by USPS are:' . $servicesNotConfiguratedHtml . 'Delivery Services and Service codes configurated  are:' . $servicesConfiguratedHtml);

    }

    function _getDomesticServices($services) {

        $prefix = 'VMSHIPMENT_ALATAK_USPS_DOMESTIC_';
        return $this->_getServices($prefix, $services);
    }

    function _getIntlServices($services) {

        $prefix = 'VMSHIPMENT_ALATAK_USPS_INTL_';
        return $this->_getServices($prefix, $services);
    }

    function _getServices($prefix, $services) {

        $i = 0;
        $key = $prefix . $i;
        $fields = array();
        if (empty($services)) {
            return $fields;
        }
        if (!is_array($services)) {
            $services = (array)$services;
        }
        foreach ($services as $key => $value) {
            $lang_key = $prefix . $value;
            $fields[$i] = JText::_($lang_key);
            $i++;
            $lang_key = $prefix . $i;
        }
        return $fields;
    }

    function _getResponseUSPSHtml($method, $responses, $selectedShipment, $cart) {

        if (JVM_VERSION > 1) {
            $folder = 'alatak_usps' . DS;
        } else {
            $folder = '';
        }
        if (!is_array($responses)) {
            return $responses;
        }

        if (!class_exists('CurrencyDisplay')) {
            require(JPATH_VM_ADMINISTRATOR . DS . 'helpers' . DS . 'currencydisplay.php');
        }
        $currency = CurrencyDisplay::getInstance();

        $pluginName = $this->_psType . '_name';
        //$html = '<div id="usps" data-shipment="' . $method->$pluginmethod_id . '">';
        if ($selectedShipment == $method->virtuemart_shipmentmethod_id) {
            $checked = 'checked';
        } else {
            $checked = '';
        }
        $checked = '';
        $html = '';
        $i = 0;

        //$html .= '<input type="radio" name="' . $pluginmethod_id . '" id="shipment_id_' . $method->$pluginmethod_id . '"   value="' . $method->$pluginmethod_id . '" ' . ">USPS";
        //$html.='<input type="hidden" name="virtuemart_shipmentmethod_id" id="usps-shipment-method" value="" >';
        foreach ($responses as $service => $response) {

            $rate = self::convertUSPSPrice($response['rate'], $cart->pricesCurrency);

            $shipmentCostDisplay = $currency->priceDisplay($rate);
            if ($this->is_domestic) {
                //$service = $response['service'];
            } else {
                $service = $this->getIntlServiceName($response); //' '.$this->shipping_pounds. ' '.$response['MaxWeight'];
            }
            vmdebug('USPS converterRate', $service, $response['rate'], $rate, $cart->pricesCurrency, $shipmentCostDisplay);
            $service = htmlspecialchars_decode($service);
            $json['rate'] = $response['rate'];
            $json['service'] = base64_encode($service);
            $name = json_encode($json);

            $html .= '<input type="radio" name="virtuemart_shipmentmethod_id" class="js-change-usps-' . $method->virtuemart_shipmentmethod_id . '" data-usps=\'' . $name . '\' id="usps_id-' . $method->virtuemart_shipmentmethod_id . '_' . $i . '"   value="' . $method->virtuemart_shipmentmethod_id . '" ' . $checked . '>
	     <label  for="usps_id-' . $method->virtuemart_shipmentmethod_id . '_' . $i . '" >';
            if (!empty($method->shipment_logos)) {
                $html .= $this->displayLogos($method->shipment_logos) . ' ';
            }
            $html .= '<span class="' . $this->_type . '">' . $method->$pluginName . ' ' . $service . ' (' . $shipmentCostDisplay . ')
		     </span>
	     </label><br />

		 ';
            $i++;
        }
        $html .= '<input type="hidden" name="usps_name-' . $method->virtuemart_shipmentmethod_id . '" id="usps_name-' . $method->virtuemart_shipmentmethod_id . '" value="0" />
	     <input type="hidden" name="usps_rate-' . $method->virtuemart_shipmentmethod_id . '" id="usps_rate-' . $method->virtuemart_shipmentmethod_id . '" value="0" />';
        $js = '
 jQuery(document).ready(function( $ ) {
   	jQuery("input.js-change-usps-' . $method->virtuemart_shipmentmethod_id . '").click( function(){
     usps = jQuery(this).data("usps");
   if (usps !== undefined ) {
    jQuery("#usps_name-' . $method->virtuemart_shipmentmethod_id . '").val(usps.service) ;
    jQuery("#usps_rate-' . $method->virtuemart_shipmentmethod_id . '").val(usps.rate) ;
   }
     });
 });
 ';
        /* In some cases the prevuious JQuery does not work
        $js = '
 jQuery(document).ready(function( $ ) {
  jQuery("input[name=\'virtuemart_shipmentmethod_id\']").live("click( function(){
     usps = jQuery(this).data("usps");
   if (usps !== undefined ) {
    jQuery("#usps_name").val(usps.service) ;
    jQuery("#usps_rate").val(usps.rate) ;
    update_form();
   }
     });
 });
 ';
*/
        $doc = JFactory::getDocument();
        $doc->addScriptDeclaration($js);
        return $html;
    }

    function getUserName($method) {

        return $method->username;
        /*
        if ($method->usps_test) {
        return $method->test_username;
        } else {
        return $method->username;
        }
       * */
    }

    function getPassword($method) {

        return;
        /*
        if ($method->usps_test) {
        return $method->test_password;
        } else {
        return $method->password;
        }
       */
    }

    function getServer($method) {

        //return $server = "http://production.shippingapis.com/ShippingAPI.dll";
        if ($method->server == 'production') {
            if ($method->secure) {
                $server = "https://production.shippingapis.com/ShippingAPI.dll";
            } else {
                $server = "http://production.shippingapis.com/ShippingAPI.dll";
            }
        } else {
            if ($method->secure) {
                $server = "https://secure.shippingapis.com/ShippingAPITest.dll";
            } else {
                $server = "http://secure.shippingapis.com/ShippingAPITest.dll";
            }
        }
        return $server;

    }

    function getVendorZip() {

        $db = & JFactory::getDBO();
        if (!class_exists('VirtueMartModelVendor')) {
            require(JPATH_VM_ADMINISTRATOR . DS . 'models' . DS . 'vendor.php');
        }
        $userId = VirtueMartModelVendor::getUserIdByVendorId(1);
        if (!class_exists('VirtueMartModelUser')) {
            require(JPATH_VM_ADMINISTRATOR . DS . 'models' . DS . 'user.php');
        }
        $userModel = new VirtueMartModelUser();
        $old = false;
        if ($old) {

            //$userModel->setUserId($userId);
            $userDetails = $userModel->getUser();
            $q = 'SELECT `virtuemart_userinfo_id` FROM `#__virtuemart_userinfos` WHERE `virtuemart_user_id` = "' . (int)$userId . '"';
            $db->setQuery($q);
            $userInfo_id = $db->loadResult();
            $userInfo = $userDetails->userInfo;
            if ($userInfo_id) {
                return $userDetails->userInfo[$userInfo_id]->zip;
            } else {
                return false;
            }
        } else {
            $vendorId = 1;
            $vendorModel = VmModel::getModel('vendor');
            $vendor = $vendorModel->getVendor($vendorId);
            $userId = $vendorModel->getUserIdByVendorId($vendorId);
            $usermodel = VmModel::getModel('user');
            $virtuemart_userinfo_id = $usermodel->getBTuserinfo_id($userId);

            $userFields = $usermodel->getUserInfoInUserFields('', 'BT', $virtuemart_userinfo_id, true, true);
            vmdebug('userFields', $userFields[$virtuemart_userinfo_id]['fields']['zip']['value']);

            /*
             $virtuemart_vendor_id = 1;
             $vendorModel = new VirtueMartModelVendor();
             $userId = $vendorModel->getUserIdByVendorId($virtuemart_vendor_id);
             $virtuemart_userinfo_id = $userModel->getBTuserinfo_id($userId);
             $userFields = $userModel->getUserInfoInUserFields('', 'BT', $virtuemart_userinfo_id);
            */
            return substr($userFields[$virtuemart_userinfo_id]['fields']['zip']['value'], 0, 5);

        }
    }

    function getServiceName($response) {

        if ($this->is_domestic) {
            $service = $response['service'];
        } else {
            $service = $this->getIntlServiceName($response);
        }
        return $service;
    }

    function getIntlServiceName($response) {
// SvcDescription =  Priority Mail&lt;sup&gt;&amp;reg;&lt;/sup&gt; International
        return $response['SvcDescription'] . " (" . $response['SvcCommitments'] . ")";
    }

    function plgVmDeclarePluginParamsShipment($name, $id, &$data) {

        return $this->declarePluginParams('shipment', $name, $id, $data);
    }

    function plgVmSetOnTablePluginParamsShipment($name, $id, &$table) {

        return $this->setOnTablePluginParams($name, $id, $table);
    }

    /**
     * Create the table for this plugin if it does not yet exist.
     * This functions checks if the called plugin is active one.
     * When yes it is calling the standard method to create the tables
     *
     * @author Valérie Isaksen
     *
     */
    function plgVmOnStoreInstallShipmentPluginTable($jplugin_id) {

        return $this->onStoreInstallPluginTable($jplugin_id);
    }

    static function convertUSPSPrice($price, $toCurrency) {

        // $fromCurrency is always USD
        $currency = CurrencyDisplay::getInstance();
        $toCurrency_code_3 = $currency->ensureUsingCurrencyCode($toCurrency);
        $usd_currency_code_3 = 'USD';

        if ($toCurrency_code_3 != $usd_currency_code_3) {
            $currencyId = $currency->getCurrencyIdByField($usd_currency_code_3);
            $converterRate = $currency->convertCurrencyTo($currencyId, 1.0, FALSE); //TODO false or true ???
        } else {
            $converterRate = 1;
        }
        $priceInNewCurrency = $price / $converterRate;
        return $priceInNewCurrency;
    }

    public function uspsCompareRates($a, $b) {

        return $a['rate'] > $b['rate'];
    }

    function isDomesticFreeShipment($method, $cart_prices) {

        return $this->isFreeShipment($method, $cart_prices, $method->domestic_free_shipment);
    }

    function isIntlFreeShipment($method, $cart_prices) {

        return $this->isFreeShipment($method, $cart_prices, $method->intl_free_shipment);
    }

    function isFreeShipment($method, $cart_prices, $free_shipping) {

        vmdebug('isFreeShipment', $free_shipping, $cart_prices['salesPrice']);
        $free_shipping = (float)$free_shipping;
        if ($free_shipping > 0.00 && $cart_prices['salesPrice'] >= $free_shipping) {
            return true;
        } else {
            return false;
        }
    }
}



// No closing tag



