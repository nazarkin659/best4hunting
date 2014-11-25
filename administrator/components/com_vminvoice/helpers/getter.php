<?php

/**
 * PDF Invoicing Solution for VirtueMart & Joomla!
 * 
 * @package   VM Invoice
 * @version   2.0.31
 * @author    ARTIO http://www.artio.net
 * @copyright Copyright (C) 2011 ARTIO s.r.o. 
 * @license   GNU/GPLv3 http://www.artio.net/license/gnu-general-public-license
 */

// check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restrict Access');

class InvoiceGetter
{
//TODO: kašovat vše

	static function checkOrderExists($orderId)
	{
		$db = JFactory::getDBO();
		
		if (COM_VMINVOICE_ISVM2)
			$db->setQuery("SELECT virtuemart_order_id FROM #__virtuemart_orders WHERE virtuemart_order_id=".(int)$orderId);
		else
			$db->setQuery("SELECT order_id FROM #__vm_orders WHERE order_id=".(int)$orderId);
		
		
		return ($db->loadResult()>0);
	}
	
	static function getOrderNumberAndPass($orderId)
	{
		$db = JFactory::getDBO();
		if (COM_VMINVOICE_ISVM2)
			$db->setQuery('SELECT order_number, order_pass FROM #__virtuemart_orders WHERE virtuemart_order_id='.(int)$orderId);
		else
			$db->setQuery('SELECT order_number, NULL as order_pass FROM #__vm_orders WHERE order_id='.(int)$orderId);
			
		return $db->loadAssoc();
	}
	
	
	static function getOrderLanguage($orderId)
	{
		if (COM_VMINVOICE_ISVM2 AND InvoiceHelper::vmCersionCompare('2.0.22') >= 0){
			$db = JFactory::getDBO();
			$db->setQuery('SELECT order_language FROM `#__virtuemart_orders` WHERE virtuemart_order_id = '.(int)$orderId);
			return $db->loadResult();
		}
		
		return null;
	}
	
	
	static function getOrder($orderId=null, $lang = null)
	{
		static $orders = array();
		if (!$lang)
			$lang = JFactory::getLanguage()->get('tag');

		if (!isset($orders[$lang][$orderId])) //cache
		{
	        $db = JFactory::getDBO();
	        
	        if (empty($orderId))
	        	return array(); //fields list	
	        //should produce most same results (if not eixists, null)
	        if (COM_VMINVOICE_ISVM2)
	        {
	        	$langPayments = self::getVm2LanguageTable('#__virtuemart_paymentmethods', $lang);
	        	$langShipments = self::getVm2LanguageTable('#__virtuemart_shipmentmethods', $lang);

	        	//since 2.0.22, coupon discount and order discount are is stored as negative
	        	$discountMinus = InvoiceHelper::vmCersionCompare('2.0.22');
	        	//BUT WILL VM UPDATE CHANGE ALSO ITS DATABASE???????? seems not. at least with update by joomla install page (vm does not work)

		        $db->setQuery('SELECT 
		        `orders`.*,
		        `orders`.virtuemart_order_id AS order_id, 
		        `orders`.virtuemart_vendor_id AS vendor_id, 
		        `orders`.virtuemart_user_id AS user_id, 
		        `orders`.order_shipment AS order_shipping, 
		        `orders`.order_shipment_tax AS order_shipping_tax, 
		        '.($discountMinus ? '`orders`.`order_discount`' : '(-1 * `orders`.`order_discount`) AS `order_discount`').', 
		        '.($discountMinus ? '`orders`.`order_discountAmount`' : '(-1 * `orders`.`order_discountAmount`) AS `order_discountAmount`').', 
		        '.($discountMinus ? '`orders`.`coupon_discount`' : '(-1 * `orders`.`coupon_discount`) AS `coupon_discount`').', 
		        `orders`.created_on, 
		        `orders`.modified_on, 
		        `orders`.`virtuemart_paymentmethod_id` AS `payment_method_id`, 
		        `orders`.`virtuemart_shipmentmethod_id` AS `shipment_method_id`, 
		        `#__virtuemart_orderstates`.`order_status_name`, 
		        `vmp`.payment_element, `vms`.shipment_element, 
		        `vmplang`.`payment_name`, `vmplang`.`payment_desc`, 
		        `vmslang`.`shipment_name`, `vmslang`.`shipment_desc`
		        FROM `#__virtuemart_orders` AS `orders`
		        LEFT JOIN `#__virtuemart_orderstates` ON `orders`.`order_status`=`#__virtuemart_orderstates`.`order_status_code` 
		        LEFT JOIN `#__virtuemart_paymentmethods` `vmp` ON `orders`.`virtuemart_paymentmethod_id` = `vmp`.`virtuemart_paymentmethod_id`
		        LEFT JOIN `'.$langPayments.'` vmplang ON `orders`.`virtuemart_paymentmethod_id` = `vmplang`.`virtuemart_paymentmethod_id`
		        LEFT JOIN `#__virtuemart_shipmentmethods` `vms` ON `orders`.`virtuemart_shipmentmethod_id` = `vms`.`virtuemart_shipmentmethod_id`
		        LEFT JOIN `'.$langShipments.'` vmslang ON `orders`.`virtuemart_shipmentmethod_id` = `vmslang`.`virtuemart_shipmentmethod_id`
		        WHERE `virtuemart_order_id` = '.(int)$orderId.' GROUP BY `orders`.`virtuemart_order_id`');
		        if ($order = $db->loadObject()){
		        	$order->cdate = InvoiceHelper::gmStrtotime($order->created_on);
		        	$order->mdate = InvoiceHelper::gmStrtotime($order->modified_on);
		        	
		        	//try to load payment and shipment names and desc from plugin tables stored in time of order
		        	//only if current not exists (deleted), or when order was in same language as requested or when old vm (no lang flag)

		        	if (($lang==$order->order_language OR !$order->order_language OR !$order->payment_name) AND
		        		($payment = InvoiceGetter::getOrderPaymentRecord($orderId, $order->payment_element))){
		        		if (preg_match('#<span\s+class="vmpayment_name"\s*>(.+)<\/span>#iUs', $payment->payment_name, $matches))
		        			$order->payment_name = $matches[1];
		        		if (preg_match('#<span\s+class="vmpayment_description"\s*>(.+)<\/span>#iUs', $payment->payment_name, $matches))
		        			$order->payment_name = $matches[1];
		        	}

		        	if (($lang==$order->order_language OR !$order->order_language OR !$order->shipment_name) AND 
		        		($shipment = InvoiceGetter::getOrderShipmentRecord($orderId, $order->shipment_element))){
		        		if (preg_match('#<span\s+class="vmshipment_name"\s*>(.+)<\/span>#iUs', $shipment->shipment_name, $matches))
		        			$order->shipment_name = $matches[1];
		        		if (preg_match('#<span\s+class="vmshipment_description"\s*>(.+)<\/span>#iUs', $shipment->shipment_name, $matches))
		        			$order->shipment_desc = $matches[1];
		        	}
		        }
		        
		        $orders[$lang][$orderId] = $order;
		        
	        }
	        else
	        {
		        $query = '
		        SELECT `order`.`order_id`,`order`.`user_id`, `order`.`order_number`, 
		        `order`.`user_info_id`, `cdate`, `order`.`mdate`, `order_status`, 
		        `order`.`vendor_id`, `order`.`ship_method_id`, `order_currency`, 
		        `method`.`payment_method_id`, `order`.`order_shipping`, 
		        `order`.`order_shipping_tax`,
		        `order_discount`, 
		        (-1 * `order`.`coupon_discount`) AS `coupon_discount`, 
		        NULL AS `order_payment`, 
		        NULL AS `order_payment_tax`, 
		        `coupon_code` , `order_total`, `order_subtotal`, `order_tax`, `customer_note`, 
		        `payment_method_discount`, `payment_method_discount_is_percent`, 
		        `#__vm_order_status`.`order_status_name`
		        FROM `#__vm_orders` AS `order` 
		        LEFT JOIN `#__vm_order_status` ON `order`.`order_status` = `#__vm_order_status`.`order_status_code` 
		        LEFT JOIN `#__vm_order_payment` AS `payment` ON `order`.`order_id` = `payment`.`order_id` 
		        LEFT JOIN `#__vm_payment_method` AS `method` ON  `method`.`payment_method_id` = `payment`.`payment_method_id`
		        LEFT JOIN `#__vm_shipping_rate` AS `shipping` ON `shipping`.`shipping_rate_id` = `order`.`ship_method_id`
		        LEFT JOIN `#__vm_tax_rate` AS `tax` ON `tax`.`tax_rate_id` = `shipping`.`shipping_rate_vat_id` 
		        WHERE `order`.`order_id`='.(int)$orderId.' GROUP BY `order`.`order_id`';
		        
		        $db->setQuery($query);
		        if ($orders[$lang][$orderId] = $db->loadObject()){
			        $shipInfo = explode('|', $orders[$lang][$orderId]->ship_method_id);
			        $orders[$lang][$orderId]->shipment_name = isset($shipInfo[1]) ? $shipInfo[1] : '';
			        $orders[$lang][$orderId]->shipment_desc = isset($shipInfo[2]) ? $shipInfo[2] : '';
			        $orders[$lang][$orderId]->user_currency_id = null; //?
			       	$orders[$lang][$orderId]->user_currency_rate = 1;
		        }
	        }

	        
	        
		}
		
		return $orders[$lang][$orderId];
	}
	
	
	static function getOrderVendor($orderId)
	{
		$db = JFactory::getDBO();
		if (COM_VMINVOICE_ISVM2)
			$db->setQuery('SELECT virtuemart_vendor_id FROM #__virtuemart_orders WHERE virtuemart_order_id='.(int)$orderId);
		else
			$db->setQuery('SELECT vendor_id FROM #__vm_orders WHERE order_id='.(int)$orderId);
		return $db->loadResult();
	}
	
	static function getOrderManufacturers($orderId, $lang)
	{
		$db = JFactory::getDBO();
		$orderId = $db->quote($orderId);
		
		if (COM_VMINVOICE_ISVM2) {
			$manufacturer = self::getVm2LanguageTable('#__virtuemart_manufacturers', $lang);
			$db->setQuery("SELECT m.* FROM $manufacturer AS m
				           LEFT JOIN #__virtuemart_product_manufacturers AS pm ON pm.virtuemart_manufacturer_id = m.virtuemart_manufacturer_id
				           LEFT JOIN #__virtuemart_order_items AS oi ON oi.virtuemart_product_id = pm.virtuemart_product_id
				           WHERE oi.virtuemart_order_id = $orderId");
		} else {
			$db->setQuery("SELECT m.* FROM #__vm_manufacturer AS m
					       LEFT JOIN #__vm_product_mf_xref AS pm ON pm.manufacturer_id = m.manufacturer_id
						   LEFT JOIN #__vm_order_item AS oi ON oi.product_id = pm.product_id
						   WHERE oi.order_id = $orderId");
		}
		
		$orderManufacturers = $db->loadObjectList();
		return $orderManufacturers;
	}
	
	
	
	static function getOrderItems($orderId=null,$orderItemsIds=null, $ordering = null, $lang = null)
	{
		if (!$orderId && !$orderItemsIds)
			return array();
		
		if (!$ordering)
			$ordering = 'id ASC';
			
		if (!preg_match('#^(\w+)(?: (asc|desc))?$#i',trim($ordering), $match)){
			JError::raiseWarning(0,'Invoice: getOrderItems: Bad ordering param');
			$match = array('id asc', 'id', 'asc');}
	
		$orderCol = trim($match[1]);
		$orderDir = (!empty($match[2]) AND in_array(strtolower(trim($match[2])), array('asc','desc'))) ? $match[2] : 'asc';
			
		
		
		//translate order col name
		if ($orderCol=='name')	$orderCol='order_item_name';
		elseif ($orderCol=='sku')	$orderCol='order_item_sku';
	
		if (COM_VMINVOICE_ISVM2){ //all prices convert for one item
	        	
			//translate order col name (VM2)
			if ($orderCol=='id')
				$orderCol='virtuemart_order_item_id';
			elseif ($orderCol=='price')
				$orderCol='product_subtotal_with_tax';
			
			//since VM 2.0.11, product_tax is stored per-item (in older versions it was for item*quantity)
			InvoiceHelper::importVMFile('version.php');
			$taxPerItem = InvoiceHelper::vmCersionCompare('2.0.11', true) >= 0; 
			
			//from 2.0.16 to 2.0.20 discount was stored as positive		
			$discountPositive = ((InvoiceHelper::vmCersionCompare('2.0.15') > 0) AND (InvoiceHelper::vmCersionCompare('2.0.22') < 0));
			
			$sql = 'SELECT item.*,item.`virtuemart_order_item_id` AS `order_item_id`, 
				item.`virtuemart_product_id` AS `product_id`,
				item.`virtuemart_vendor_id` AS `vendor_id`, 
				item.product_item_price, 
				item.product_basePriceWithTax AS product_price_with_tax, 
				(item.product_subtotal_discount/product_quantity)'.($discountPositive ? '*(-1)' : '').' AS product_price_discount, 
				'.($taxPerItem ? 'item.product_tax' : 'item.product_tax/product_quantity AS product_tax').', 
				item.product_subtotal_with_tax/product_quantity AS product_subtotal_with_tax, 
				lang.product_name, lang.product_s_desc, lang.product_desc, product.product_weight, product.product_weight_uom, product.product_packaging,
				product.product_params, 
                medias.file_url_thumb AS item_image
		        FROM `#__virtuemart_order_items` AS item
		        LEFT JOIN `#__virtuemart_products` AS product ON item.virtuemart_product_id = product.virtuemart_product_id
		        LEFT JOIN '.self::getVm2LanguageTable('#__virtuemart_products', $lang).' AS lang ON item.virtuemart_product_id = lang.virtuemart_product_id 
                LEFT JOIN `#__virtuemart_product_medias` AS pmedias ON (
                    item.virtuemart_product_id = pmedias.virtuemart_product_id
                    AND pmedias.ordering = (SELECT MIN(pm.ordering) FROM `#__virtuemart_product_medias` AS pm WHERE pm.virtuemart_product_id = item.virtuemart_product_id)
                )
                LEFT JOIN `#__virtuemart_medias` AS medias ON pmedias.virtuemart_media_id = medias.virtuemart_media_id
		        WHERE '.
				($orderId ? '`virtuemart_order_id` = ' . (int)$orderId : ' `virtuemart_order_item_id` IN ('.implode(',',(array)$orderItemsIds).')').
				' GROUP BY item.`virtuemart_order_item_id`'. /*group for all cases */
				' ORDER BY item.`'.$orderCol.'` '.$orderDir;
		}
		else {
			
			//translate order col name (VM1)
			if ($orderCol=='id')	
				$orderCol='order_item_id';
			elseif ($orderCol=='price')	
				$orderCol='product_final_price';
			
			$sql = 'SELECT item.*, 
			item.product_item_price, 
			item.product_final_price AS product_price_with_tax,
			NULL AS product_price_discount, 
			(item.product_final_price - item.product_item_price) AS product_tax, 
			item.product_final_price AS product_subtotal_with_tax, 
			product.product_s_desc, product.product_desc, product.product_weight, product.product_weight_uom, product.product_packaging, 
			NULL AS product_params, 
			product.product_full_image AS item_image
			FROM `#__vm_order_item` AS item 
			LEFT JOIN #__vm_product AS product ON item.product_id = product.product_id 
			WHERE '.($orderId ? 'item.`order_id` = ' 
			. (int)$orderId : ' item.`order_item_id` IN ('.implode(',',(array)$orderItemsIds).')').
			' ORDER BY item.`'.$orderCol.'` '.$orderDir;
		}
		
		
		$db = JFactory::getDBO();
		$db->setQuery($sql);
		if ($res = $db->loadObjectList())
		{
			
			if (!COM_VMINVOICE_ISVM2) //prepend image path relative to joomla root
				foreach ($res as &$row)
					if ($row->item_image)
						$row->item_image = 'components'.'/'.'com_virtuemart'.'/'.'shop_image'.'/'.'product'.'/'.$row->item_image;
			
            // 16.9.2013 dajo: fix the product discount to always contain the correct sign (in DB discounts applied after tax are positive,
            //                 but discounts applied before tax are negative - VM logic)
            if (COM_VMINVOICE_ISVM2) { //pama: better use version check as above?
                foreach ($res as &$row) {
                    if ($row->product_price_discount != 0) {
                        if ($row->product_subtotal_with_tax < $row->product_price_with_tax) {
                            // Discount -> negative
                            if ($row->product_price_discount > 0)
                                $row->product_price_discount = -$row->product_price_discount;
                        }
                        else {
                            // Surcharge -> positive
                            if ($row->product_price_discount < 0)
                                $row->product_price_discount = -$row->product_price_discount;
                        }
                    }
                }
            }
            
            //since 2.0.22
	        if (!isset($row->product_discountedPriceWithoutTax))
	        	$row->product_discountedPriceWithoutTax = null; 
	        if (!isset($row->product_priceWithoutTax)) 
	           	$row->product_priceWithoutTax = null;

            
            
			//unset images that dont exists to not break pdf generate completly
			foreach ($res as &$row)
				if (!empty($row->item_image) AND !file_exists(JPATH_SITE.'/'.ltrim($row->item_image, '/')))
					$row->item_image = null;
		}
		
		return $res;
	}
	
	
	static function getOrderPaymentRecord($orderId, $plugin)
	{
		static $cache;
		$orderId = (int)$orderId;
		if (!COM_VMINVOICE_ISVM2)
			return false;
		if (!isset($cache[$orderId.'.'.$plugin])){
			$table = '#__virtuemart_payment_plg_'.$plugin;
			if (!invoiceHelper::checkTableExists($table))
				$cache[$orderId.'.'.$plugin] = false;
			else {
				$db = JFactory::getDBO();
				$db->setQuery('SELECT * FROM '.$table.' WHERE virtuemart_order_id='.(int)$orderId);
				$cache[$orderId.'.'.$plugin] = $db->loadObject();
			}
		}
		return $cache[$orderId.'.'.$plugin];
	}
	
	static function getOrderShipmentRecord($orderId, $plugin)
	{
		static $cache;
		$orderId = (int)$orderId;
		if (!COM_VMINVOICE_ISVM2)
			return false;
		if (!isset($cache[$orderId.'.'.$plugin])){
			$table = '#__virtuemart_shipment_plg_'.$plugin;
			if (!invoiceHelper::checkTableExists($table))
				$cache[$orderId.'.'.$plugin] = false;
			else {
				$db = JFactory::getDBO();
				$db->setQuery('SELECT * FROM '.$table.' WHERE virtuemart_order_id='.(int)$orderId);
				$cache[$orderId.'.'.$plugin] = $db->loadObject();
			}
		}
		return $cache[$orderId.'.'.$plugin];
	}	
	
	
	
	
	

	/**
	 * Only for VM2.
	 * WARNING: return can differ based on version of VM2
	 * 
	 * @param int $orderId		order id
	 * @param int $orderItemId	get payment rules for specific item. -1: rules per bill, -2: shipment rules, -3: payment rules
	 * @return array
	 */

	static function getOrderCalcRules($orderId,$orderItemId)
	{
		$db = JFactory::getDBO();
		$where = array();
		$where[] = 'virtuemart_order_id = '.(int)$orderId;
		
		if ($orderItemId>0) //rules for specific item
			$where[] = 'virtuemart_order_item_id = '.(int)$orderItemId;
		elseif ($orderItemId==-1) //rules for general order (bill)
			$where[] = '(virtuemart_order_item_id IS NULL OR virtuemart_order_item_id=0) AND calc_kind != '.$db->Quote('shipment').' AND calc_kind != '.$db->Quote('payment');
		elseif ($orderItemId==-2) //rules for shipment
			$where[] = 'calc_kind = '.$db->Quote('shipment');
		elseif ($orderItemId==-3) //rules for payment
			$where[] = 'calc_kind = '.$db->Quote('payment');
		
		$db->setQuery('SELECT * FROM #__virtuemart_order_calc_rules WHERE ('.implode(') AND (', $where).') ORDER BY virtuemart_order_calc_rule_id ASC');	
		$res =  $db->loadObjectList();

		return $res;
	}
	
	/**
	 * Get fields from order calc rules to retermine if using extended table (probably from 2.0.12, but better check manually)
	 * 
	 * @param	$checkField string, optional	check existence of one field
	 * @return	array/bool
	 */
	static function getOrderCalcRulesFields($checkField = null)
	{
		if (!COM_VMINVOICE_ISVM2)
			return false;
		
		static $fields; //cache
		
		if (!isset($fields))
		{
			$db = JFactory::getDBO();
			$fields = InvoiceHelper::getTableColumns($db, '#__virtuemart_order_calc_rules');
			$fields = array_combine(array_keys($fields), array_keys($fields));
		}
		
		return $checkField ? isset($fields[$checkField]) : $fields;
	}
	
	static function getProduct($productId, $lang = null)
	{
		//TODO: použít na počítání discountu a daní nějakou vm funkci?
		
		$db = JFactory::getDBO();
		if (COM_VMINVOICE_ISVM2){
			$langProducts = self::getVm2LanguageTable('#__virtuemart_products', $lang);
        	$sql = 'SELECT 
        			`p`.`virtuemart_product_id` AS product_id, 
        			l.product_name AS product_name, 
        			`price`.`product_currency` AS `product_currency`,
            		IF (`price`.override=1,`product_override_price`,`price`.`product_price`) AS `product_price`, 
            		`p`.`product_sku` AS `product_sku`, 
            		`p`.`virtuemart_vendor_id` AS vendor_id, 
            		`p`.product_weight, `p`.product_weight_uom,
        			`p`.product_availability
		        	FROM `#__virtuemart_products` AS p
		        	LEFT JOIN '.$langProducts.' AS l ON p.virtuemart_product_id = l.virtuemart_product_id
            		LEFT JOIN `#__virtuemart_product_prices` AS `price` ON `p`.`virtuemart_product_id` = `price`.`virtuemart_product_id` 
            		WHERE `p`.`virtuemart_product_id`='.(int)$productId.' 
        			GROUP BY `p`.`virtuemart_product_id`';
		}
		else
        	$sql = 'SELECT  
        			`p`.`product_id`, 
        			`p`.`product_name` AS `product_name`, 
            		`price`.`product_price` AS `product_price`, 
            		`price`.`product_currency` AS `product_currency`,
            		`p`.`product_sku` AS `product_sku`, 
            		`p`.`vendor_id` AS vendor_id, 
            		`p`.product_weight, `p`.product_weight_uom, 
            		`tax_rate`, `discount`.*, NULL AS product_availability 
            		FROM `#__vm_product` AS `p` 
            		LEFT JOIN `#__vm_product_price` AS `price` ON `p`.`product_id` = `price`.`product_id` 
            		LEFT JOIN `#__vm_tax_rate` AS `tax` ON `tax`.`tax_rate_id` = `p`.`product_tax_id` 
            		LEFT JOIN `#__vm_product_discount` AS `discount` ON `discount`.`discount_id` = `p`.`product_discount_id` 
            		WHERE `p`.`product_id`='.(int)$productId.' 
					GROUP BY `p`.`product_id`';
        	
        $db->setQuery($sql);
        return $db->loadObject();
	}
	
	static function getProductPrices($productId, $priceId = null, $quantity = null)
	{
		static $cache;
		$cacheId = "$productId;$priceId;$quantity";
		
		if (!isset($cache[$cacheId])){
			
			$db = JFactory::getDBO();
			
			if (COM_VMINVOICE_ISVM2){
				
				$db = JFactory::getDbo();
				$nullDate = $db->getNullDate();
				$jnow = JFactory::getDate();
				$method = method_exists($jnow, 'toMySQL') ? 'toMySQL' : 'toSql';
				$now = $jnow->$method();
				
				$db->setQuery('SELECT * FROM #__virtuemart_product_prices WHERE virtuemart_product_id = '.(int)$productId.'
				AND ( (`product_price_publish_up` IS NULL OR `product_price_publish_up` = "' . invoiceHelper::escape($db, $nullDate) . '" OR `product_price_publish_up` <= "' .invoiceHelper::escape($db, $now) . '" )
			    AND (`product_price_publish_down` IS NULL OR `product_price_publish_down` = "' .invoiceHelper::escape($db, $nullDate) . '" OR product_price_publish_down >= "' . invoiceHelper::escape($db, $now) . '" ) )'.
			    ($quantity ? ' AND( (`price_quantity_start` IS NULL OR `price_quantity_start`="0" OR `price_quantity_start` <= '.$quantity.') AND (`price_quantity_end` IS NULL OR `price_quantity_end`="0" OR `price_quantity_end` >= '.$quantity.') )' : '').
				($priceId ? ' AND virtuemart_product_price_id = '.(int)$priceId : '').'
				ORDER BY price_quantity_start, virtuemart_shoppergroup_id, product_price'); 
			}
			else
				$db->setQuery('SELECT product_price, product_currency, shopper_group_id FROM #__vm_product_price WHERE product_id = '.(int)$productId.($priceId ? ' AND product_price_id = '.(int)$priceId : ''));
			
			 $cache[$cacheId] = $db->loadObjectList();
		}
		
		return $cache[$cacheId];
	}
	
	/**
	 * Limit 100
	 * @param unknown_type $filter
	 */
    static function getAjaxProductList($filter, $lang = null)
    {
        $db = JFactory::getDBO();
        
        //build chain of languages priority (now, because $db :))
        if (COM_VMINVOICE_ISVM2){
        	$possibleLangs = array();
        	$possibleLangs[] = JFactory::getLanguage()->get('tag');
        	$langParams = JComponentHelper::getParams('com_languages');
        	$possibleLangs[] = $langParams->get('admin');
        	$possibleLangs[] = $langParams->get('site');
        	$possibleLangs[] = 'en-GB';
        	$possibleLangs = array_unique($possibleLangs);
        }
        
        if (COM_VMINVOICE_ISVM2){

        	//join all language tables, because we search in all languages and we need at least one translation
        	$joins = $wheres = $selects = array();
        	foreach (self::getVm2LanguageTable('#__virtuemart_products', -1) as $o => $tableName){
        		$selects[] = '`'.$tableName.'`.product_name AS product_name_'.substr($tableName, -5);
        		$joins[] = 'LEFT JOIN `'.$tableName.'` ON (p.virtuemart_product_id = `'.$tableName.'`.virtuemart_product_id)';
        		$wheres[] = '`'.$tableName.'`.`product_name` LIKE '.$db->Quote('%' . $filter . '%').'';
        	}
        	
        	$db->setQuery('SELECT p.`virtuemart_product_id` AS `id`, p.`product_sku`, 
        	p.product_in_stock, p.product_ordered, '.implode(', ', $selects).'
        	FROM `#__virtuemart_products` AS p '.implode(' ', $joins).' 
        	WHERE '.implode(' OR ', $wheres).' OR p.`product_sku` LIKE ' . $db->Quote('%'.$filter.'%'),0,50);
        }
        else
        	$db->setQuery('SELECT `product_id` AS `id`,`product_name`, `product_sku`, product_in_stock
        	FROM `#__vm_product` 
        	WHERE `product_name` LIKE ' . $db->Quote('%' . $filter . '%').' OR `product_sku` LIKE ' . $db->Quote('%' . $filter . '%'),0,50);

        if (count($res = $db->loadObjectList())) foreach ($res as $product)
        {
        	if (COM_VMINVOICE_ISVM2){
	        	$product->product_name = null;
	        	foreach ($possibleLangs as $langTag) //pick based on priority
	        		if ($langTag AND !empty($product->{'product_name_'.$langTag})){
	        			$product->product_name = $product->{'product_name_'.$langTag}; break;}
	        	if (!$product->product_name) //non of them? pick first
	        		foreach ($product as $key => $val)
	        			if ($val AND strpos($key, 'product_name_')===0){
	        				$product->product_name = $val; break;}
	        	if (!$product->product_name)
	        		$product->product_name = 'cannot get name';
        	}
        	
        	$product->name = $product->product_sku.' - '.$product->product_name;
        	$product->name.=' ('.JText::_('COM_VMINVOICE_IN_STOCK').': '.$product->product_in_stock;
        	if (COM_VMINVOICE_ISVM2)
        		$product->name.=', '.JText::_('COM_VMINVOICE_ORDERED').': '.$product->product_ordered;
        	$product->name.=')';
        }
        	
        return $res;
    }
    
    //create search indexes for searching function getAjaxUserList
    static function checkUserSearchIndexes($create = false)
    {
    	$db = JFactory::getDBO();
    	
    	//get indexes for table
    	if (COM_VMINVOICE_ISVM2){
    		$table = '`#__virtuemart_userinfos`';
    		$missing = array('last_name', 'first_name', 'company', 'city', 'address_type_name', 'phone_1');
    	}
    	else{
    		$table = '`#__vm_user_info`';
    		$missing = array('last_name', 'first_name', 'user_email', 'company', 'city', 'address_type_name', 'phone_1');
    	}
    	
    	$missing = array_combine($missing, $missing);
    	
    	$db->setQuery('SHOW INDEXES FROM '.$table);
    	$result = $db->loadObjectList();
    	
    	//jinak. musi to být index o JEDINE polozce. nebo ne. ta, co hledame musi byt v inexu prvni, ze ano?
    	//to zajistime tak, ze si to grupneme
    	//a seradime podle Seq_in_index;
    	
    	$userIdName = COM_VMINVOICE_ISVM2 ? 'virtuemart_user_id' : 'user_id';
    	
    	//prepare two dimensional array
    	$indexes = array();
    	foreach ($result as $index)
    		$indexes[$index->Key_name][(int)$index->Seq_in_index] = $index;

    	//ok, now we can check if we have indexes
    	foreach ($indexes as $keyName => $childs)
    		foreach ($childs as $Seq_in_index => $index)
    			if (isset($missing[$index->Column_name]) AND $Seq_in_index==1) //we have index for our column AND it is first one (can be)
    				unset($missing[$index->Column_name]);
    		
    	//https://pm.artio.net/issues/7211#change-37388
    	//now also we want index for (`virtuemart_user_id`, `address_type`)
    	$haveJoinIndex = false;
    	foreach ($indexes as $keyName => $childs)
    		if ((isset($childs[1]) AND $childs[1]->Column_name==$userIdName) AND (isset($childs[2]) AND $childs[2]->Column_name=='address_type')){
    			$haveJoinIndex = true; break;}

    	if (!$haveJoinIndex)
    		$missing[] = array($userIdName, 'address_type');

    	//another check: relevant only if we have >100 customer address records
    	if ($missing){ 
    		$db->setQuery('SELECT count(*) FROM '.$table);
    		if ($db->loadResult()<100)
    			$missing = array();
    	}
    	
    	//only check, return resuls
    	if (!$create)
    		return $missing;
    	
    	//we will create!
    	if ($missing){
    		
    		$start = time();
    		foreach ($missing as $colNames)
    		{
    			if ((time()-$start)>min(30, max(20, ini_get('max_execution_time')-10))){ //longer than 30s or max exec. time -10
    				JError::raiseNotice(0, 'Due to PHP time limit, creating indexes ended prematuraly before all indexes were created. Click to create indexes again to continue.');
    				continue;
    			}
    			
    			$colNames = (array)$colNames;

    			$dbNames = array();
    			foreach ($colNames as $colName)
    				$dbNames[] = InvoiceHelper::nameQuote($db, $colName);
    			
    			$db->setQuery('ALTER TABLE '.$table.' ADD INDEX ('.implode(',', $dbNames).')');
    			if (!$db->query())
    				JError::raiseWarning(0, 'Cannot create search index for column(s) '.implode(', ', $colNames).'. '.$db->getErrorMsg());
    			else
    				JFactory::getApplication()->enqueueMessage(JText::sprintf('COM_VMINVOICE_CREATED_SEARCH_INDEX', implode(', ', $colNames)));
    		}
    	}
    }
    
    static function getAjaxUserList($filterOrig)
    {
		$db = JFactory::getDBO();
		
		//if entered only number, try search by exact user id OR userinfo id
		//"NEEED to be able 2 search: customer_id OR phone_1 OR virtuemart_userinfo_id"
		if (COM_VMINVOICE_ISVM2)
			$searchId = is_numeric($filterOrig) ? ' OR BT.virtuemart_user_id = '.(int)$filterOrig.' OR BT.virtuemart_userinfo_id = '.(int)$filterOrig.' OR ST.virtuemart_userinfo_id = '.(int)$filterOrig : '';
		else
			$searchId = is_numeric($filterOrig) ? ' OR BT.user_id = '.(int)$filterOrig.' OR BT.user_info_id='.(int)$filterOrig.' OR ST.user_info_id='.(int)$filterOrig : '';

		$words = preg_split('#[ ,;-]#', $filterOrig);
		
		$conditions = array();
		foreach ($words as $key => &$word){
			
			if (!($word = JString::strtolower(JString::trim($word, ' _-;,.')))){
				unset($words[$key]);
				continue;}
			
			if (JString::strlen($word)<=1 AND !is_numeric($word)){
				unset($words[$key]);
				continue;}
			
				
			$isPhone = preg_match('#^[0-9 +/()]+$#', $word);
				
			$word = $db->Quote('%'.invoiceHelper::escape($db, $word, true).'%');
			
			//NOTE: if modifying, modify also createUserSearchIndexes()!
			
			if (COM_VMINVOICE_ISVM2){
				
				$conditions[] = '(BT.`last_name` LIKE ' . $word . ' 
			        OR BT.`first_name` LIKE ' . $word . ' 
			        OR U.`email` LIKE ' . $word . ' 
			        OR BT.`company` LIKE ' . $word . ' 
			        OR BT.`city` LIKE ' . $word . ' 
			        OR ST.`address_type_name` LIKE ' . $word . '
			        '.($isPhone ? ' OR BT.phone_1 LIKE '.$word  : '').')';
			}
			else {
				
				$conditions[] = '(BT.`last_name` LIKE ' . $word . ' 
			        OR BT.`first_name` LIKE ' . $word . ' 
			        OR BT.`user_email` LIKE ' . $word . ' 
			        OR BT.`company` LIKE ' . $word . ' 
			        OR BT.`city` LIKE ' . $word . ' 
			        OR ST.`address_type_name` LIKE ' . $word . '
			        '.($isPhone ? ' OR BT.phone_1 LIKE '.$word  : '').')';
			}
		}
		
		if (!$conditions)
			return array();
				
	    //get user list with all shipping adresses   
		if (COM_VMINVOICE_ISVM2)
		{
	        $db->setQuery('SELECT ST.`address_type_name`, ST.`virtuemart_userinfo_id` AS st_user_info_id,
	        BT.`virtuemart_user_id` AS user_id,  BT.`virtuemart_userinfo_id` AS bt_user_info_id,
	        BT.`last_name`, BT.`first_name`, BT.`title`, BT.`middle_name`, BT.`company`, BT.`city` 
	        FROM `#__virtuemart_userinfos` AS BT
	        LEFT JOIN `#__virtuemart_userinfos` AS ST ON (BT.virtuemart_user_id = ST.virtuemart_user_id AND ST.`address_type` = "ST")
	        LEFT JOIN `#__users` AS U ON BT.virtuemart_user_id=U.id
	        WHERE BT.`address_type` = "BT" AND (('.implode(' AND ', $conditions).')'.$searchId.')
	        ORDER BY BT.`last_name`', 0, 50);
		}
		else 
		{
	        $db->setQuery('SELECT ST.`address_type_name`, ST.`user_info_id` AS st_user_info_id,
	        BT.`user_id` AS user_id,  BT.`user_info_id` AS bt_user_info_id,
	        BT.`last_name`, BT.`first_name`, BT.`title`, BT.`middle_name`, BT.`company`, BT.`city` 
	        FROM `#__vm_user_info` AS BT
	        LEFT JOIN `#__vm_user_info` AS ST ON (BT.user_id = ST.user_id AND ST.`address_type` = "ST")
	        WHERE BT.`address_type` = "BT" AND (('.implode(' AND ', $conditions).')'.$searchId.')
	        ORDER BY BT.`last_name`', 0, 50);
		}
		
		//HM... really? jak je to s indexy? jsou k načemu, když se joinují tabulky?
		
	    $users = $db->loadObjectList();
	    
	    //TODO: each user have to have option to select only billing address
	    //scenario: user have some shipping address
	    
	    $usersOnlyBilling = array(); //element with only billing address (= billing same as shipping)
	    $usersWithShipping = array();
	    
        foreach ($users as $key => $user) {
        	
            $user->id = $user->user_id.';'.$user->bt_user_info_id.';';
            $user->name = $user->title . ' ' . $user->first_name . ' ' . $user->middle_name . ' ' . $user->last_name;
            if (($company = JString::trim($user->company)))
                $user->name .= ', ' . $company;
            if (($city = JString::trim($user->city)))
                $user->name .= ', ' . $city; 
            
            if (!isset($usersOnlyBilling[$user->user_id])) //store only "pure" billing infio record (for billing same as shipping)
            	$usersOnlyBilling[$user->user_id] = clone $user; //to break reference
            
            $user->id .= $user->st_user_info_id; //add shipping info id
            	
            if ($user->st_user_info_id && !empty($user->address_type_name) AND $user->address_type_name!='-default-') //append ST adress type name, if not default
            	$user->name .= ' (' .$user->address_type_name.')' ;
            	
            if ($user->st_user_info_id)
            	$usersWithShipping[$user->user_id] = true; //if this is shipping address also, store info about it
        }
        
        foreach ($users as $key => $user) { //add user only billing info before ones with shipping assigned
        	if (isset($usersWithShipping[$user->user_id])){
        		array_splice($users, $key, 0, array($usersOnlyBilling[$user->user_id]));
        		unset($usersWithShipping[$user->user_id]);
        	}
        }
        
        //+ now join users which dont have VM account. for example when someone imported database. hope this is now slowing query much.
        $conditions = array();
        
        $searchId = is_numeric($filterOrig) ? ' OR U.id = '.(int)$filterOrig : '';
        
        foreach ($words as $key => $word){

        	if (COM_VMINVOICE_ISVM2)
        		$conditions[] = '(U.name LIKE ' . $word . ' OR 
		       	 U.username LIKE ' . $word . ' OR 
		       	 U.email LIKE ' . $word . ' OR 
		       	 U.name LIKE ' . $word . ')';
        	else 
        		$conditions[] = '(U.name LIKE ' . $word . ' OR 
	       		 U.username LIKE ' . $word . ' OR 
	       		 U.email LIKE ' . $word . ' OR 
	       		 U.name LIKE ' . $word . ')';
        	
        	//NOTE: name field does not have indexes :-/
        }
        
       	if (COM_VMINVOICE_ISVM2)
       		 $db->setQuery('SELECT U.id,U.name,U.username FROM #__users AS U
       		 LEFT JOIN `#__virtuemart_userinfos` AS ui ON ui.virtuemart_user_id=U.id
       		 WHERE ui.virtuemart_user_id IS NULL AND (('.implode(' AND ', $conditions).')'.$searchId.')');
       	else
       		 $db->setQuery('SELECT U.id,U.name,U.username FROM #__users AS U
       		 LEFT JOIN `#__vm_user_info` AS ui ON ui.user_id=U.id
       		 WHERE ui.user_id IS NULL AND (('.implode(' AND ', $conditions).')'.$searchId.')');

       	if ($users2 = $db->loadObjectList()) foreach($users2 as $user2)
       	{
	       	$newUser = new stdClass();
	        $newUser->id = $user2->id.';;';
	        $newUser->name = $user2->name.' ('.$user2->username.') - '.JText::_('COM_VMINVOICE_ONLY_JOOMLA_USER');
	        
       		array_push($users,$newUser);
       	}

        return $users;
    }
    
    
    /**
     * Get available order statuses.
     * 
     * @return array
     */
    static function getOrderStates()
    {    	
        $db = JFactory::getDBO();
        
        if (COM_VMINVOICE_ISVM2)
        	$db->setQuery('SELECT order_status_code AS \'id\', order_status_name AS \'name\' FROM `#__virtuemart_orderstates`');
        else
        	$db->setQuery('SELECT order_status_code AS \'id\', order_status_name AS \'name\' FROM `#__vm_order_status`');
        
        
        $orderStatus =  $db->loadObjectList('id');
        foreach ($orderStatus as $key => $status){ //translate status
        	
        	$orderStatus[$key]->name= self::getVMTranslation($status->name);
	        
	        
        }
        
        return $orderStatus;	
    }
    
    
    static function getTaxRates()
    {
    	$db = JFactory::getDBO();
    	if (COM_VMINVOICE_ISVM2) //in VM2, taxes are not explicitely marked as taxes, just select taxes for product or bill with +%
    	 	$db->setQuery('SELECT `virtuemart_calc_id` AS id, (`calc_value`/100) AS value FROM `#__virtuemart_calcs` 
    	 	WHERE (calc_kind=\'Tax\' OR calc_kind=\'TaxBill\' OR calc_kind=\'VatTax\' OR calc_kind=\'DATax\') AND calc_value_mathop=\'+%\' ORDER BY `value` ASC');
    	else
    		$db->setQuery('SELECT `tax_rate` AS id, `tax_rate` AS value FROM `#__vm_tax_rate` ORDER BY `value` ASC');
    		
    	$already = array(); //prevent duplicates
    	$taxRates = $db->loadObjectList('id');
    	$result = array();
    	if (count($taxRates)) foreach ($taxRates as $key => &$taxRate){
    		
    		$taxRate->value = (float)$taxRate->value;
    		$taxRate->name = ($taxRate->value*100).'%';

    		if (in_array($taxRate->value,$already))
    			unset($taxRates[$key]);
    		else
    			$already[] = $taxRate->value;
    	}
    	
	   	$zeroTaxRate = new StdClass(); //add 0% tax rate
	    $zeroTaxRate->id = 0;
	    $zeroTaxRate->value = 0;
	    $zeroTaxRate->name = '0%';
	    array_unshift($taxRates, $zeroTaxRate);
	    
    	return $taxRates;
    }
    
    static function getPaymentMethod($methodId, $lang = null)
    {
    	$db = JFactory::getDBO();
    	if (COM_VMINVOICE_ISVM2){
    		$langPayments = self::getVm2LanguageTable('#__virtuemart_paymentmethods', $lang);
    		$db->setQuery('SELECT *
		        FROM `#__virtuemart_paymentmethods` `vmp` 
		        LEFT JOIN `'.$langPayments.'` vmplang ON `vmp`.`virtuemart_paymentmethod_id` = `vmplang`.`virtuemart_paymentmethod_id`
		        WHERE `vmp`.`virtuemart_paymentmethod_id` = '.(int)$methodId);
    		
    		if ($method = $db->loadObject()){ //parse also params string
				$params = explode('|',$method->payment_params);
    			foreach ($params as $param){
    				$param = explode('=',$param,2);
    				if (count($param)>1)
    					$method->{$param[0]} = trim($param[1],'"');
    			}
    		}

    		return $method;
    	}
    	else{
    		$db->setQuery('SELECT * FROM `#__vm_payment_method` 
    		WHERE `payment_method_id` = '.(int)$methodId);
    		return $db->loadObject();
    	}
    }
	
    static function getVendors()
    {
        $db = JFactory::getDBO();
        
        if (COM_VMINVOICE_ISVM2)
        	$db->setQuery('SELECT virtuemart_vendor_id AS `id`, `vendor_name` AS `name` FROM `#__virtuemart_vendors` ORDER BY `name` ASC');
		else
        	$db->setQuery('SELECT `vendor_id` AS id, `vendor_name` AS name FROM `#__vm_vendor` ORDER BY `name` ASC');
        
        return $db->loadObjectList();
    }
    
    static function getCurrencies()
    {
    	static $cache;
    	if (!isset($cache)){
	        $db = JFactory::getDBO();
	        if (COM_VMINVOICE_ISVM2)
	        	$db->setQuery('SELECT `currency_name`, `currency_name` AS name, `virtuemart_currency_id`, `virtuemart_currency_id` AS id, `currency_symbol` AS `symbol`, currency_code_3 AS currency_code FROM `#__virtuemart_currencies` ORDER BY `currency_name` ASC');
	        else
	        	$db->setQuery('SELECT `currency_name`, `currency_name` AS name, `currency_code`, `currency_code` AS id FROM `#__vm_currency` ORDER BY `currency_name` ASC');
    		$cache = $db->loadObjectList('id');
    	}
    	
    	return $cache;
    }
    
    static function getCountriesDB()
    {
        $db = JFactory::getDBO();
        if (COM_VMINVOICE_ISVM2)
        	$db->setQuery('SELECT `country_name`, `country_name` AS name, `country_3_code`, `country_2_code`, `virtuemart_country_id` AS id, `virtuemart_country_id` AS `country_id` FROM `#__virtuemart_countries` ORDER BY `country_name` ASC');
        else
        	$db->setQuery('SELECT `country_name`, `country_name` AS name, `country_3_code`, `country_2_code`, `country_3_code` AS id, `country_id` FROM `#__vm_country` ORDER BY `country_name` ASC');
    
    	return $db->loadObjectList();;
    }
    
    static function getCountries()
    {
        $countries = self::getCountriesDB();

         foreach ($countries as $i => $country) //translate
          	$countries[$i]->name = JText::sprintf('COM_VMINVOICE_COUNTRY_SHORT_INFO', $country->country_name, $country->country_3_code);
          									
        $newCountry = new stdClass();
        $newCountry->id = COM_VMINVOICE_ISVM2 ? 0 : '-';
        $newCountry->name =  JText::_('COM_VMINVOICE_SELECT');
        array_unshift($countries,$newCountry);

        return $countries;
    }
    
    static function getStatesDB($country = null)
    {
    	$db = JFactory::getDBO();
        if (COM_VMINVOICE_ISVM2)
        	$db->setQuery('SELECT `state_name`, `state_name` AS name, `state_2_code`, `state_3_code`, `virtuemart_state_id` AS id FROM `#__virtuemart_states` 
        	'.($country ? 'WHERE virtuemart_country_id = '.(int)$country : '').' ORDER BY `state_name` ASC');
        else{
        	
        	if ($country && !is_numeric($country)) {//country code
        		$db->setQuery('SELECT country_id FROM `#__vm_country` WHERE country_3_code LIKE '.$db->Quote($country));
        		$country = $db->loadResult();
        	}
        	
        	$db->setQuery('SELECT `state_name`, `state_name` AS name, `state_2_code`, `state_3_code`, state_2_code AS id FROM `#__vm_state` 
        	'.($country ? 'WHERE country_id = '.(int)$country : '').' ORDER BY `state_name` ASC');
        }
        return $db->loadObjectList();
    }
    
    /**
     * Get states list
     * 
     * @param unknown_type (optional) $country	country id (VM2) OR country_3_code (VM1)
     */
    static function getStates($country = null)
    {
        $states = self::getStatesDB($country);
        
        $newState = new stdClass();
        $newState->id = COM_VMINVOICE_ISVM2 ? 0 : '-';
        $newState->name =  JText::_('COM_VMINVOICE_SELECT');
        array_unshift($states,$newState);
        
        return $states;
    }
    
    static function getPayments($lang = null)
    {
    	static $cache = array();
    	if (isset($cache[$lang]))
    		return $cache[$lang];
    	
        $db = JFactory::getDBO();
        if (COM_VMINVOICE_ISVM2)
        {
            $langPayments = InvoiceGetter::getVm2LanguageTable('#__virtuemart_paymentmethods', $lang);
    		$db->setQuery('SELECT *, 
    			`#__virtuemart_paymentmethods`.virtuemart_paymentmethod_id AS `id`,
    			`'.$langPayments.'`.`payment_name` AS `name`, 
    			`'.$langPayments.'`.`payment_desc` AS `desc`
    			FROM `#__virtuemart_paymentmethods` LEFT JOIN `'.$langPayments.'` ON  
    			#__virtuemart_paymentmethods.virtuemart_paymentmethod_id = `'.$langPayments.'`.virtuemart_paymentmethod_id');
        }
        else
        	$db->setQuery('SELECT 
        	`payment_method_id` AS id, 
        	`payment_method_name` AS name, 
        	NULL AS `desc`,  
        	`payment_method_discount`, 
        	`payment_method_discount_is_percent` 
        	FROM `#__vm_payment_method` WHERE `payment_enabled`=\'Y\' ORDER BY `payment_method_name` ASC');
        $cache[$lang] = $db->loadObjectList('id');
        
        if (COM_VMINVOICE_ISVM2)
	        foreach ($cache[$lang] as $method){
	        	$params = explode('|',$method->payment_params);
	        	$method->payment_params = new stdClass();
	        	foreach ($params as $param){
	        		$param = explode('=',$param,2);
	        		if (count($param)>1)
	        			$method->payment_params->{$param[0]} = trim($param[1],'"');
	        	}
	        }
        
        return $cache[$lang];
    }
    
    static function getShippingsVM2($lang = null)
    {
    	static $shippings = array();
    	
    	if (empty($shippings[$lang]))
    	{
    		$shippings[$lang] = array();
	    	//TODO: we need to "fake" whole virtue matrt cart object to pass it to shipping plugins to get available shippings ?
	    	$langShippings = InvoiceGetter::getVm2LanguageTable('#__virtuemart_shipmentmethods', $lang);
	    	
	    	$db = JFactory::getDBO();
	    	$db->setQuery('SELECT s.virtuemart_shipmentmethod_id, 
	    	l.shipment_name, l.shipment_desc, s.shipment_params
	    	FROM #__virtuemart_shipmentmethods s
	    	LEFT JOIN '.$langShippings.' l ON  
	    	s.virtuemart_shipmentmethod_id = l.virtuemart_shipmentmethod_id');
	    	$dbshippings = $db->loadObjectList();

	    	foreach ($dbshippings as $dbshipping)
	    	{
	    		$shipping = new stdClass();
	    		$shipping->shipping_rate_id = $dbshipping->virtuemart_shipmentmethod_id;
	    		$shipping->name = $dbshipping->shipment_name;
	    		$shipping->desc = $dbshipping->shipment_desc;
	    		foreach (explode('|', $dbshipping->shipment_params) as $param){
	    			$paramVal = explode('=',$param,2);
	    			if (count($paramVal)>1)
	    				$shipping->{$paramVal[0]} = trim($paramVal[1],'"');
	    		}
	    		$shippings[$lang][$shipping->shipping_rate_id] = $shipping;
	    	}
    	}
    	return $shippings[$lang];
    }
    
    /**
     * Get aviable shippings array. Standard shippings from db, other from shipping modules. 
     * But it is not very reliable, shipping modules are little moody (currency rates change, ...)
     * 
     * @param	string	used by shipping module; from view->orderData->user_info_id  (to get adress)
     * @param 	string	used by shipping module; currency to count proper prices
     * @param	int		used by shipping module; overal weight of order
     */
    static function getShippingsVM1($user_info_id, $currency, $userId, $weight = 0)
    {
    	static $shippings;
    	
    	if (!empty($shippings[$user_info_id.$currency.$weight])) //cache
    		return $shippings[$user_info_id.$currency.$weight];
    	
    	$decimals = InvoiceCurrencyDisplay::getDecimals();
    	
    	//get VirtueMart framework
        global $mosConfig_absolute_path;
        
        InvoiceHelper::importVMFile('virtuemart_parser.php',false);
      	
    	//1. Get all values for standard_shipping from db    	
        $db = JFactory::getDBO();
        $db->setQuery(
        	'SELECT `shipping_rate_id`, `shipping_carrier_name`, `shipping_rate_name`, `currency_code`, `shipping_rate_value`, 
        		`shipping_rate_package_fee`, `shipping_rate_vat_id`, `tax_rate` 
        		FROM `#__vm_shipping_rate` AS `rate` 
        		LEFT JOIN `#__vm_shipping_carrier` AS `carrier` ON `carrier`.`shipping_carrier_id` = `rate`.`shipping_rate_carrier_id`
        		LEFT JOIN `#__vm_currency` AS `currency` ON `currency`.`currency_id` = `rate`.`shipping_rate_currency_id` 
        		LEFT JOIN `#__vm_tax_rate` AS `tax` ON `tax`.`tax_rate_id` = `rate`.`shipping_rate_vat_id` 
        		ORDER BY `shipping_carrier_name` ASC, `shipping_rate_name` ASC');
  
        $items = $db->loadObjectList();
        // standard shipping
        foreach ($items as $item)
        {
        	// convert to current currency
        	$item->shipping_rate_value = $GLOBALS['CURRENCY']->convert($item->shipping_rate_value, $item->currency_code, $currency) ;
        	$item->shipping_rate_package_fee = $GLOBALS['CURRENCY']->convert($item->shipping_rate_package_fee, $item->currency_code, $currency) ;
        	        	
        	$shippingRateString = implode('|', array(
        	    'standard_shipping',
        	    $item->shipping_carrier_name,
        	    $item->shipping_rate_name,
        	    number_format($item->shipping_rate_value + $item->shipping_rate_package_fee, $decimals, '.', ''),
        	    $item->shipping_rate_id
        	));

        	$shipping = new stdClass();
        	$shipping->shipping_rate_id = $shippingRateString;
        	$shipping->name = array('',
        		$item->shipping_carrier_name,
        		(strlen($item->shipping_rate_name) > 50 ? substr($item->shipping_rate_name, 0, 50)."..." : $item->shipping_rate_name),
        		round($item->shipping_rate_value,$decimals).'+'.round($item->shipping_rate_package_fee, $decimals),
        		$currency . ', VAT '. round($item->tax_rate * 100, 2).'%');
        	$shipping->tax_rate = $item->tax_rate*1;
        	$shippings[$user_info_id.$currency.$weight][] = $shipping;
        }
        
        // 2. Get other shippings from custom shipping modules. 
        $GLOBALS['product_currency'] = $currency;
        global $weight_total;
		$weight_total = (is_numeric($weight)) ?  $weight : 0;  //store total weight for purpose of finding shippings
		ob_start();
		ps_checkout::list_shipping_methods($user_info_id, null); //get methods html. note first paraneter, that is user info id from vm_user_info table, from which is token his country and zip in modules. so doesen't matter which country you select in VM Invoice form!!!
		$shippingsHTML = ob_get_clean(); //catch thrown html shipping form from buffer

		if (preg_match_all('/value=["\'](.+)["\']/iU', $shippingsHTML, $matches)) //find radio inputs in html
		{
			foreach ($matches[1] as $shippingRateString)
			{
				$shippingRateString = urldecode($shippingRateString);
				$shippingValues = explode('|', $shippingRateString);

				if (count($shippingValues) > 3 AND trim($shippingValues[0]) != 'standard_shipping') { //not involve standard_shipping, that is handled above
				
					//substract shipping tax from price
					$tax = InvoiceGetter::getVM1ShippingTax($shippingValues[0], isset($shippingValues[4]) ? $shippingValues[4] : '', $userId);
					if ($tax > 0)
						$shippingValues[3] = round($shippingValues[3] - ($shippingValues[3] * $tax), $decimals);
						
					if (!isset($shippingValues[4]))
						$shippingValues[4]= 0; //shipping rate id
						
					$shipping = new stdClass();
					$shipping->shipping_rate_id = implode('|', $shippingValues);
					$shipping->name = array(
    					$shippingValues[0],
    					$shippingValues[1],
    					strlen($shippingValues[2]) > 50 ? substr($shippingValues[2], 0, 50) . "..." : $shippingValues[2],
    					round($shippingValues[3], $decimals),
    					$currency .', VAT '.round($tax * 100, 2).'%');
    				$shipping->tax_rate = $tax*1;
					$shippings[$user_info_id.$currency.$weight][] = $shipping;
				}
			}
		}
        
        return $shippings[$user_info_id.$currency.$weight];
    }
    
    /**
     * Gets shipping tax from VM's shipping module.
     * 
     * @param	string	name of shipping module
     * @param	int		shipping method id (=last, optional parameter in shipping method string)
     * @param	id		id of user who does the order (it will be computed by his country maybe)
     */
    function getVM1ShippingTax($shippingClass, $shippMethodId=null, $userId=null)
    {
    	InvoiceHelper::importVMFile('classes/ps_ini.php');
    	if (!InvoiceHelper::importVMFile('classes/shipping/'.$shippingClass.'.php'))
    		return false;

	    $shipping = new $shippingClass();
	    $_REQUEST["shipping_rate_id"]="||||".$shippMethodId; //ship method sting...
	    if (!is_null($userId)) $_SESSION['auth']['user_id']=$userId; //user id to determine address...
	    $shipTaxRate = !is_null($shippMethodId) ? $shipping->get_tax_rate($shippMethodId) : $shipping->get_tax_rate();
		return $shipTaxRate;
    }
    
	static function getVendor($vendorId=null, $lang = null)
    {
    	if (empty($vendorId)) //keys for template helper. should be same as returned values below
    	return array('company_name', 'title', 'first_name', 'middle_name', 'last_name', 'phone_1', 'phone_2', 'phone_vendor', 'fax', 'email',
    	 'address_1', 'address_2', 'city', 'state_name', 'state_2_code', 'state_3_code', 'country_name', 'country_2_code', 'country_3_code', 
    	 'zip', 'store_name', 'store_desc', 'url', 'currency_name', 'currency_3_code');
    	
    	static $cache = array();
    	if (isset($cache[$lang]))
    		return $cache[$lang];
    	
    	$db = JFactory::getDBO();
    	if (COM_VMINVOICE_ISVM2){
    		$langVendors = InvoiceGetter::getVm2LanguageTable('#__virtuemart_vendors', $lang);
	        $db->setQuery(
	        'SELECT i.*,
	        v.vendor_name AS company_name, 
	        i.title AS title, 
	        i.first_name AS first_name , 
	        i.middle_name AS middle_name, 
	        i.last_name AS last_name, 
	        i.phone_1 AS phone_1, 
	        i.phone_2 AS phone_2, 
	        vlang.vendor_phone AS phone_vendor,
	        i.fax AS fax, 
	        u.email AS email, 
	        i.address_1 AS address_1, 
	        i.address_2 AS address_2, 
	        i.city AS city,
	        s.state_name,
	        s.state_2_code,
	        s.state_3_code,
	        c.country_name,
	        c.country_2_code,
	        c.country_3_code, 
	        i.zip AS zip, 
	        vlang.vendor_store_name AS store_name, 
	        vlang.vendor_store_desc AS store_desc, 
	        vlang.vendor_url AS url, 
	        v.vendor_currency, 
	        IF (curr.currency_name IS NULL, CONCAT(\'Currency \',v.vendor_currency), curr.currency_name) AS currency_name, 
	        IF (curr.currency_code_3 IS NULL, CONCAT(\'Currency \',v.vendor_currency), curr.currency_code_3) AS currency_3_code
	        FROM `#__virtuemart_vendors` AS v
	        LEFT JOIN `#__virtuemart_vmusers` AS vmusers ON v.virtuemart_vendor_id = vmusers.virtuemart_vendor_id
	        LEFT JOIN `#__virtuemart_userinfos` AS i ON (vmusers.virtuemart_user_id=i.virtuemart_user_id AND i.address_type=\'BT\')
	        LEFT JOIN `#__virtuemart_countries` AS c ON (i.virtuemart_country_id = c.virtuemart_country_id)
	        LEFT JOIN `#__virtuemart_states` AS s ON (i.virtuemart_state_id = s.virtuemart_state_id)
	        LEFT JOIN '.$langVendors.' AS vlang ON vmusers.virtuemart_vendor_id = .vlang.virtuemart_vendor_id
	        LEFT JOIN `#__users` AS u ON vmusers.virtuemart_user_id = u.id
	        LEFT JOIN `#__virtuemart_currencies` AS curr ON v.vendor_currency = curr.virtuemart_currency_id
	        WHERE v.virtuemart_vendor_id='.(int)$vendorId.' AND vmusers.user_is_vendor=1 GROUP BY v.virtuemart_vendor_id LIMIT 1');
	        //https://pm.artio.net/issues/8173!
	        /*
	        IF (s.state_name IS NULL, CONCAT(\'State \',i.virtuemart_state_id), s.state_name) AS state_name,
	        IF (s.state_2_code IS NULL, CONCAT(\'State \',i.virtuemart_state_id),s.state_2_code) AS state_2_code,
	        IF (s.state_3_code IS NULL, CONCAT(\'State \',i.virtuemart_state_id), s.state_3_code) AS state_3_code,
	        IF (c.country_name IS NULL, CONCAT(\'Country \',i.virtuemart_country_id), c.country_name) AS country_name,
	        IF (c.country_2_code IS NULL, CONCAT(\'Country \',i.virtuemart_country_id), c.country_2_code) AS country_2_code,
	        IF (c.country_3_code IS NULL, CONCAT(\'Country \',i.virtuemart_country_id), c.country_3_code) AS country_3_code, 
	         */
    	}
    	else
	        $db->setQuery(
	        "SELECT v.*, 
	        v.vendor_name AS company_name, 
	        v.contact_title AS title, 
	        v.contact_first_name AS first_name , 
	        v.contact_middle_name AS middle_name, 
	        v.contact_last_name AS last_name, 
	        v.contact_phone_1 AS phone_1, 
	        v.contact_phone_2 AS phone_2, 
	        v.vendor_phone AS phone_vendor, 
	        v.contact_fax AS fax, 
	        v.contact_email AS email, 
	        v.vendor_address_1 AS address_1, 
	        v.vendor_address_2 AS address_2, 
	        v.vendor_city AS city,
	        IF (s.state_name IS NULL, v.vendor_state, s.state_name) AS state_name,
	        IF (s.state_2_code IS NULL, v.vendor_state, s.state_2_code) AS state_2_code,
	        IF (s.state_3_code IS NULL, v.vendor_state, s.state_3_code) AS state_3_code,
	        IF (c.country_name IS NULL, v.vendor_country, c.country_name) AS country_name,
	        IF (c.country_2_code IS NULL, v.vendor_country, c.country_2_code) AS country_2_code,
	        IF (c.country_3_code IS NULL, v.vendor_country, c.country_3_code) AS country_3_code, 
	        v.vendor_zip AS zip,
	        v.vendor_store_name AS store_name,
	        v.vendor_store_desc AS store_desc,
	        v.vendor_url AS url, 
	        v.vendor_currency, 
	        IF (curr.currency_name IS NULL, v.vendor_currency, curr.currency_name) AS currency_name, 
	        v.vendor_currency AS currency_3_code
	        	FROM `#__vm_vendor` AS v
	          	LEFT JOIN `#__vm_country` AS c ON (v.`vendor_country` = c.`country_3_code` OR v.`vendor_country` = c.`country_2_code`)
	          	LEFT JOIN `#__vm_state` AS s ON ((v.`vendor_state` = s.`state_3_code` OR v.`vendor_state` = s.`state_2_code`) AND s.country_id = c.country_id)
	          	LEFT JOIN `#__vm_currency` AS curr ON (v.`vendor_currency` = curr.`currency_code`)
	      		WHERE v.`vendor_id` = " . (int)$vendorId.' GROUP BY v.vendor_id LIMIT 1');

        $cache[$lang] = $db->loadObject();
        return $cache[$lang];
    }
    
    static function getVendorMailAndName($vendorId)
    {
    	static $cache;
    	
    	if (!isset($cache[$vendorId])){
    		$db = JFactory::getDBO();
    		if (COM_VMINVOICE_ISVM2)
    			$db->setQuery('
    			SELECT v.vendor_name AS name, i.email
    			FROM `#__virtuemart_vendors` AS v
	        	LEFT JOIN `#__virtuemart_vmusers` AS vmusers ON v.virtuemart_vendor_id = vmusers.virtuemart_vendor_id
	        	WHERE virtuemart_vendor_id = '.(int)$vendorId);
    		else
            	$db->setQuery("SELECT v.`contact_email` AS email, v.`vendor_name` AS name FROM `#__vm_vendor` AS v WHERE v.`vendor_id` = ".(int)$vendorId);
            $cache[$vendorId] = $db->loadObject();
    	}
    	return $cache[$vendorId];
    }
    
    /**
     * Get vendor title image relative to joomla root.
     * 
     * @param unknown_type $vendorId
     */
    static function getVendorImage($vendorId)
    {
    	$db = JFactory::getDBO();
    	if (COM_VMINVOICE_ISVM2){ //get first vendor image in vm2
    	
    		$db->setQuery('SELECT m.file_url 
    		FROM #__virtuemart_vendor_medias vm 
    		JOIN #__virtuemart_medias m ON vm.virtuemart_media_id = m.virtuemart_media_id
    		WHERE vm.virtuemart_vendor_id = '.(int)$vendorId.'
    		ORDER BY vm.ordering ASC LIMIT 1');
    		if ($filename = ltrim($db->loadResult(),' /\\'))
    			return $filename;
			else
				return false;
    	}
    	else //vm1
    	{
    		$vendor = self::getVendor($vendorId);
			if ($vendor && $vendor->vendor_full_image)
				return "components/com_virtuemart/shop_image/vendor/" . $vendor->vendor_full_image;
			else
				return false;
    	}
    }
    
    /**
     * Get order address. If called ST and not presented (= when BT = ST), returned is ST.
     * If called with no parameter, returned is list of available fields (for replacables tags help)
     */
    static function getOrderAddress($orderId=null,$type=null)
    {
    	$db = JFactory::getDBO();
    	
    	if (!$orderId){ //keys for template helper. should be same as returned values below
    		
    		static $return;
    		
    		if (empty($return))
    		{
	    		$return = array();
	    		$exclude = array('virtuemart_order_userinfo_id','virtuemart_order_id','virtuemart_user_id',
	    		'order_info_id','order_id','address_type',
	    		'created_on','created_by','modified_on','modified_by','locked_on','locked_by',
	    		'virtuemart_state_id', 'virtuemart_country_id'); //ugly fields
	    		
	    		$db->setQuery('SHOW COLUMNS FROM '.(COM_VMINVOICE_ISVM2 ? '#__virtuemart_order_userinfos' : '#__vm_order_user_info'));
	
	    		foreach ( $db->loadObjectList() as $column)
	    			if (!in_array($column->Field,$exclude))
	    				$return[] = $column->Field;
	
	    		$return[]='country_name';
	    		$return[]='state';
	    		$return[]='state_2_code';
	    		if (!in_array('user_id',$return))
	    			$return[] = 'user_id';
	    		$return[]='username';
	    		
	    		$return = array_unique($return);
    		}
    		return $return;
    	}

    	if (COM_VMINVOICE_ISVM2)
	        $db->setQuery(
	        "SELECT i.*, i.`virtuemart_order_id` AS order_id, i.virtuemart_user_id AS user_id, u.username, c.`country_name`, s.`state_name` AS `state`, s.`state_2_code`
	        	FROM `#__virtuemart_order_userinfos` AS i
	        	LEFT JOIN `#__virtuemart_states` AS s ON (i.`virtuemart_state_id` = s.`virtuemart_state_id`)
	          	LEFT JOIN `#__virtuemart_countries` AS c ON (i.`virtuemart_country_id` = c.`virtuemart_country_id`)
	          	LEFT JOIN `#__users` AS u ON i.virtuemart_user_id=u.id
	      		WHERE i.`virtuemart_order_id` = $orderId AND i.`address_type` = '$type'");
    	else
	        $db->setQuery(
	        "SELECT i.*, u.username, c.`country_name`, i.`state` AS `state_2_code`, IFNULL(s.`state_name`, i.`state`) AS `state`
	        	FROM `#__vm_orders` AS o 
	          	INNER JOIN `#__vm_order_user_info` AS i ON (o.`order_id` = i.`order_id` AND i.`address_type` = '$type')
	          	LEFT JOIN `#__vm_country` AS c ON (i.`country` = c.`country_3_code`)
	        	LEFT JOIN `#__vm_state` AS s ON ((i.`state` = s.`state_2_code` OR i.`state` = s.`state_id`) AND s.country_id = c.country_id)
	          	LEFT JOIN `#__users` AS u ON i.user_id=u.id
	      		WHERE o.`order_id` = " .(int)$orderId);
	        
	    $address  = $db->loadObject();
	    
	    if (!$address AND $type=='ST') //no shipping address stored = use billing
	    	$address = self::getOrderAddress($orderId,'BT');
	        
	   	if (is_object($address) && (!$address->address_type_name OR $address->address_type_name=='-default-'))
	    	$address->address_type_name = null; //form VM1
	        	
        return $address;
    }
    
    static function getVMExtraField($fieldID)
    {
    	$db = JFactory::getDBO();
    	if (COM_VMINVOICE_ISVM2)
    		$db->setQuery("SELECT `name`, `title` FROM `#__virtuemart_userfields` WHERE `virtuemart_userfield_id` = ".(int)$fieldID);
    	else
        	$db->setQuery("SELECT `name`, `title` FROM `#__vm_userfield` WHERE `fieldid` = ".(int)$fieldID);
        return $db->loadAssoc();
    }
    
    /**
     * Get (last) shipping date in timestamp
     * 
     * @param int $orderId
     */
    static function getOrderShippingDate($orderId)
    {
    	$db = JFactory::getDBO();
    	if (COM_VMINVOICE_ISVM2)
    		$db->setQuery("SELECT `created_on` FROM `#__virtuemart_order_histories` WHERE `virtuemart_order_id` = ".(int)$orderId.
	            	" AND `order_status_code`='S' ORDER BY `created_on` DESC LIMIT 1");
    	else
    		$db->setQuery("SELECT `date_added` FROM `#__vm_order_history` WHERE `order_id` = ".(int)$orderId.
	            	" AND `order_status_code`='S' ORDER BY `date_added` DESC LIMIT 1");
    	$res = 	$db->loadResult();
    	return $res ? InvoiceHelper::gmStrtotime($res) : null;
    }
    
    static function getCustomerNumber($userId)
    {
    	$db = JFactory::getDBO();
    	if (COM_VMINVOICE_ISVM2)
    		$db->setQuery('SELECT `customer_number` FROM `#__virtuemart_vmusers` WHERE `virtuemart_user_id` = '.(int)$userId);
    	else
    		$db->setQuery('SELECT `customer_number` FROM `#__vm_shopper_vendor_xref` WHERE `user_id` = '.(int)$userId);
    		
		return $db->loadResult();	        
    }
    
    /**
     * Get array of groups of user.
     */
    static function getShopperGroup($userId, $getId = false)
    {
    	$db = JFactory::getDBO();
    	if (COM_VMINVOICE_ISVM2)
    		$db->setQuery('SELECT `xref`.`virtuemart_shoppergroup_id` AS `grid`, `group`.`shopper_group_name` AS `grname`
		        	FROM `#__virtuemart_vmuser_shoppergroups` `xref` 
		        	LEFT JOIN `#__virtuemart_shoppergroups` `group` ON `xref`.`virtuemart_shoppergroup_id` = `group`.`virtuemart_shoppergroup_id`
		        	WHERE `xref`.`virtuemart_user_id` = '.(int)$userId);
    	else
    		$db->setQuery('SELECT `xref`.`shopper_group_id` AS `grid`, `group`.`shopper_group_name` AS `grname`
		        	FROM `#__vm_shopper_vendor_xref` `xref` 
		        	LEFT JOIN `#__vm_shopper_group` `group` ON `xref`.`shopper_group_id` = `group`.`shopper_group_id`
		        	WHERE `xref`.`user_id` = '.(int)$userId);
    			        	
    	$groups = $db->loadObjectList();

    	$ret = array();
    	foreach ($groups as $record)
    		$ret[$record->grid] = $getId ? $record->grid : ($record->grname ? $record->grname : $record->grid);
    	
    	return $ret;	        	
    }
    
    static function getShopperGroups($vendorId = null)
    {
    	$db = JFactory::getDBO();
    	if (COM_VMINVOICE_ISVM2)
    		$db->setQuery('SELECT virtuemart_shoppergroup_id AS id, shopper_group_name AS name FROM #__virtuemart_shoppergroups '.($vendorId ? 'WHERE virtuemart_vendor_id='.(int)$vendorId : ''));
    	else
    		$db->setQuery('SELECT shopper_group_id AS id, shopper_group_name AS name FROM #__vm_shopper_group '.($vendorId ? 'WHERE vendor_id='.(int)$vendorId : ''));
    	
		return $db->loadObjectList('id');
    }
    
    static function getDefaultShopperGroup($vendorId = null)
    {
    	$db = JFactory::getDBO();
    	if (COM_VMINVOICE_ISVM2)
    		$db->setQuery('SELECT virtuemart_shoppergroup_id FROM #__virtuemart_shoppergroups WHERE `default` = 1'.($vendorId ? ' AND virtuemart_vendor_id='.(int)$vendorId : ''));
    	else
    		$db->setQuery('SELECT shopper_group_id FROM #__vm_shopper_group WHERE `default` = 1'.($vendorId ? ' AND vendor_id='.(int)$vendorId : ''));
    	return $db->loadResult();
    }

    /**
     * Get default currency. Returned currency for vendor, if not set, currency used by most products.
     */
    static function getDefaultCurrency($vendorId=null)
    {
    	$db = JFactory::getDBO();	
    	if ($vendorId){
    		if (COM_VMINVOICE_ISVM2)
    			$db->setQuery('SELECT vendor_currency FROM `#__virtuemart_vendors` WHERE virtuemart_vendor_id = '.(int)$vendorId);
    		else
    			$db->setQuery('SELECT vendor_currency FROM `#__vm_vendor` WHERE vendor_id = '.(int)$vendorId);
    			
    		return $db->loadResult();
    	}	
		if (COM_VMINVOICE_ISVM2)
			$db->setQuery('SELECT product_currency,count(virtuemart_product_price_id) AS records FROM `#__virtuemart_product_prices` GROUP BY product_currency ORDER BY records DESC LIMIT 1');
		else
			$db->setQuery('SELECT product_currency,count(product_price_id) AS records FROM `#__vm_product_price` GROUP BY product_currency ORDER BY records DESC LIMIT 1');
    	return $db->loadResult();
    }
    
    /**
     * Get user info eigther by user info id OR by combination of user_id and address type
     * 
     * @param unknown_type $userInfoId	VM1: some long hash string, VM2: int
     * @param unknown_type $userId		Joomla user id
     * @param unknown_type $addressType	BT or ST
     */
    static function getUserInfo($userInfoId=null,$userId=null,$addressType=null)
    {
    	$db = JFactory::getDBO();	
    	
    	if (!$userInfoId && !$userId){ //keys for template helper. should be same as returned values below
    	
    		static $return;
    	
    		if (empty($return))
    		{
    			$return = array();
    			$exclude = array('virtuemart_order_userinfo_id','virtuemart_order_id','virtuemart_user_id',
    					'order_info_id','order_id','address_type',
    					'created_on','created_by','modified_on','modified_by','locked_on','locked_by',
    					'virtuemart_state_id', 'virtuemart_country_id'); //ugly fields
    			 
    			$db->setQuery('SHOW COLUMNS FROM '.(COM_VMINVOICE_ISVM2 ? '#__virtuemart_userinfos' : '#__vm_user_info'));
    	
    			foreach ( $db->loadObjectList() as $column)
    				if (!in_array($column->Field,$exclude))
    					$return[] = $column->Field;
    	
    			$return[]='user_id';
    			$return[]='country';
    			$return[]='state';
	 
    			$return = array_unique($return);
    		}
    		return $return;
    	}
    	
    	
        if ($userInfoId) {
			if (COM_VMINVOICE_ISVM2)
				$where = 'UI.`virtuemart_userinfo_id` = ' . (int)$userInfoId;
			else
        		$where = '`user_info_id` = ' . $db->Quote($userInfoId);
        }
        elseif ($userId && $addressType){
        	if (COM_VMINVOICE_ISVM2)
        		$where = 'UI.`virtuemart_user_id` = ' . (int) $userId . ' AND UI.`address_type` = ' . $db->Quote($addressType);
        	else
        		$where = '`user_id` = ' . (int) $userId . ' AND `address_type` = ' . $db->Quote($addressType);
        }
        else
        	return false;
        
        if (COM_VMINVOICE_ISVM2)
        	$db->setQuery('SELECT UI.*,    
        		UI.virtuemart_userinfo_id AS user_info_id,
        		UI.virtuemart_user_id AS user_id, 
	        	UI.virtuemart_country_id AS country, 
	        	UI.virtuemart_state_id AS state, 
	        	U.email AS email
	        	FROM `#__virtuemart_userinfos` UI
	        	LEFT JOIN #__users U ON UI.virtuemart_user_id=U.id WHERE '.$where);
        else
        	$db->setQuery('SELECT *, user_email AS email FROM `#__vm_user_info` WHERE '.$where);
         	
        $info = $db->loadObject();

        if (is_object($info) && (!$info->address_type_name OR $info->address_type_name=='-default-'))
	    	$info->address_type_name = null;
	        	
        return $info;
    }
    
    
    
    static function getOrderUserId($orderId)
    {
    	static $cache = array();
    	if (!array_key_exists($orderId, $cache)){
	    	$db = JFactory::getDBO();
	    	
	       	if (COM_VMINVOICE_ISVM2)
	        	$db->setQuery("SELECT U.`id` FROM `#__virtuemart_orders` AS O JOIN `#__users` AS U ON O.virtuemart_user_id=U.id WHERE O.`virtuemart_order_id` = ".(int)$orderId);
	        else
				$db->setQuery("SELECT U.`id` FROM `#__vm_orders` AS O JOIN `#__users` AS U ON O.user_id=U.id WHERE O.`order_id` = ".(int)$orderId);
	    	
				
			
			$cache[$orderId] = $db->loadResult();
    	}	
    	
    	return $cache[$orderId];
    }

    
    /**
     * Get user info of order. If address not presented, returned empty. (which is different from getOrderAddress behaivor)
     */
    static function getOrderUserInfo($orderId,$addressType)
    {
    	static $cache;
    	if (!isset($cache[$orderId.$addressType])){
	        $db = JFactory::getDBO();
	        if (COM_VMINVOICE_ISVM2)
	        	$db->setQuery('SELECT *,
	        	virtuemart_order_userinfo_id AS order_info_id, 
	        	virtuemart_order_id AS order_id, 
	        	virtuemart_user_id AS user_id, 
	        	virtuemart_country_id AS country, 
	        	virtuemart_state_id AS state
	        	FROM `#__virtuemart_order_userinfos` WHERE `virtuemart_order_id` = ' . (int) $orderId . ' AND `address_type` = ' . $db->Quote($addressType));
	        else
	        	$db->setQuery('SELECT *, 
	        	`user_email` AS `email`
	        	FROM `#__vm_order_user_info` WHERE `order_id` = ' . (int) $orderId . ' AND `address_type` = ' . $db->Quote($addressType));
	        
	        $info = $db->loadObject();

	        if (is_object($info) && (!$info->address_type_name OR $info->address_type_name=='-default-'))
	        	$info->address_type_name = null; //for VM1
	        		
	        $cache[$orderId.$addressType] = $info;
    	}

    	return is_object($cache[$orderId.$addressType]) ? clone $cache[$orderId.$addressType] : $cache[$orderId.$addressType];
    }
    
    
    /**
     * Returns order numbers:
     * 1) which are unsent (or delivery note unsent if send both)
     * 2) are in defined status
     * 3) were modified on last 24 hours (only at VM)
     */
    static function getUnsentOrderIDs ()
    {
    	 $params = InvoiceHelper::getParams();

         $dnCond = InvoiceHelper::getSendBoth() ? " OR ms.`dn_mailed` = '0'" : "";
           
         $order_status = (array)$params->get('order_status');
         
        
        
        if (COM_VMINVOICE_ISVM2) //vm2 stores mdate and cdate in gmt also (only in datetime format)
        	$sql = "SELECT o.`virtuemart_order_id` FROM `#__virtuemart_orders` AS o
        		LEFT JOIN `#__vminvoice_mailsended` AS ms ON (o.`virtuemart_order_id` = ms.`order_id`)
				WHERE (ms.`order_id` IS NULL OR ms.`invoice_mailed` = '0' $dnCond)
				AND o.`modified_on` > '".gmdate('Y-m-d H:i:s',time()-86400)."'";
        else //vm1 stores cdate and mdate in gmt
        	$sql = "SELECT o.`order_id` 
        		FROM `#__vm_orders` AS o
        		LEFT JOIN `#__vminvoice_mailsended` AS ms ON (o.`order_id` = ms.`order_id`)
				WHERE (ms.`order_id` IS NULL OR ms.`invoice_mailed` = '0' $dnCond)
				AND o.`mdate` > UNIX_TIMESTAMP() - 86400";
        if (count($order_status))
        	$sql .= " AND o.`order_status` IN ('".implode("','",$order_status)."')";
        
        // make sure orders are sorted from oldest to newest by order ID
		if (COM_VMINVOICE_ISVM2)
			$sql .= " ORDER BY o.`virtuemart_order_id` ASC";
		else
		    $sql .= " ORDER BY o.`order_id` ASC";
		    
        
        $db = JFactory::getDBO();
        $db->setQuery($sql);
        return invoiceHelper::loadColumn($db);
    }
    
    /**
     * Get params for each order from mailsended table. Its objext stored as json.
     * 
     * @param int $orderId
     */
    static function getOrderParams($orderId)
    {
    	if (!$orderId)
    		return new stdClass();
    	
    	$db = JFactory::getDBO();
    	$db->setQuery('SELECT `params` FROM `#__vminvoice_mailsended` WHERE order_id = '.(int)$orderId);
    	
    	$params = json_decode($db->loadResult());
    	if (!is_object($params))
    		$params = new stdClass();
    	return $params;
    }
    
    
    
    /**
     * Get order IDS created after specified time
     * 
     * @param int $time
     */
    static function getOrdersFromTime($time)
    {
    	if (COM_VMINVOICE_ISVM2)
    		$sql = 'SELECT `virtuemart_order_id` FROM `#__virtuemart_orders` WHERE `created_on > \''.gmdate('Y-m-d H:i:s',$time)."'";
    	else
    		$sql = 'SELECT `order_id` FROM `#__vm_orders` WHERE `o`.`cdate`>'.$time;
        $db = JFactory::getDBO();
        $db->setQuery($sql);
        return invoiceHelper::loadColumn($db);
    }
    
    static function getCoupon($code)
    {
		$db = JFactory::getDBO();
		if (COM_VMINVOICE_ISVM2)
			$db->setQuery('SELECT * FROM `#__virtuemart_coupons` WHERE `coupon_code`='.$db->Quote($code));
		else
        	$db->setQuery('SELECT * FROM `#__vm_coupons` WHERE `coupon_code`='.$db->Quote($code));
        return $db->loadObject();
    }
    
   	
    
	/**
	 * Gets specific SQL string to config file.
	 * 
	 * @param string $field		config field name
	 * @param string $defalt	default sql query
	 */
    static function getConfigSQL($field,$defalt=null)
    {
    	switch ($field){
    	
    		case 'order_status':
    			
		    	if (COM_VMINVOICE_ISVM2)
		    		return 'SELECT order_status_code AS value, order_status_name AS title FROM `#__virtuemart_orderstates`';
		    	else
		    		return 'SELECT order_status_code AS value, order_status_name AS title FROM `#__vm_order_status`';
		    	
		    	
				break;
				
			
			case 'extra_field1':
			case 'extra_field2':
			case 'extra_field3':
			case 'extra_field4':
						
		    	if (COM_VMINVOICE_ISVM2)
		    		return 'SELECT \'\' AS fieldid,\'\' AS name UNION SELECT virtuemart_userfield_id AS fieldid, name FROM #__virtuemart_userfields';
		    	else
		    		return 'SELECT \'\' AS fieldid,\'\' AS name UNION SELECT fieldid, name FROM #__vm_userfield`';
	    		break;
	    	
			case 'default_vendor':
		    	if (COM_VMINVOICE_ISVM2)
		    		return 'SELECT `virtuemart_vendor_id` AS `vendor_id`, `vendor_name` FROM `#__virtuemart_vendors` ORDER BY `vendor_name` ASC';
		    	else
		    		return 'SELECT `vendor_id`, `vendor_name` FROM `#__vm_vendor` ORDER BY `vendor_name` ASC';
	    		break;
	    	
			case 'default_currency':
		    	if (COM_VMINVOICE_ISVM2)
		    		return 'SELECT `virtuemart_currency_id` AS id, CONCAT(`currency_name`,\', \',`currency_code_3`) AS `name` FROM `#__virtuemart_currencies` ORDER BY `virtuemart_currency_id` ASC';
		    	else
		    		return 'SELECT `currency_code` AS id, CONCAT(`currency_name`,\', \',`currency_code`) AS `name` FROM `#__vm_currency` ORDER BY `currency_id` ASC';
	    		break;
	    		
	    	case 'default_status':
	    		if (COM_VMINVOICE_ISVM2)
		        	return 'SELECT order_status_code AS \'id\', order_status_name AS \'name\' FROM `#__virtuemart_orderstates`';
		        else
		        	return 'SELECT order_status_code AS \'id\', order_status_name AS \'name\' FROM `#__vm_order_status`';
	    		break;
	    	
	    		
			default:
				return $defalt;
    	}
    }

	
	/**
	 * Get VM language localised table name prior to current language.
	 * @param string $tableName
	 * @param string $langTag	required language code, null for current, -1 for returning array of all available lang tables
	 * @uses current JLanguage setting
	 * @return string	table name with language suffix, with db #__ prefix
	 */
	static function getVm2LanguageTable($tableName, $langTag = null)
	{
		static $langTables = array();
		
		$db = JFactory::getDBO();
		$tableName = str_replace('#__',$db->getPrefix(),$tableName);
		
		if (!array_key_exists($tableName, $langTables)){
			$db = JFactory::getDBO();
			$db->setQuery('SHOW TABLES LIKE \''.invoiceHelper::escape($db, $tableName).'_%\'');
			$langTables[$tableName] = invoiceHelper::loadColumn($db);
		}

		if ($langTag==-1)
			return $langTables[$tableName];

		if (!count($langTables[$tableName]))
			return false;
		
		//if exact tag not provided, use current language
		if (!$langTag){
			$lang = JFactory::getLanguage();
			$langTag = $lang->get('tag');
		}

		$langTable = $tableName.'_'.strtolower(str_replace('-','_',$langTag));
		
		if (!in_array($langTable,$langTables[$tableName])){ //no table for desired language
			$langTable = $tableName.'_en_gb';				//use en (? not current lang?)
			if (!in_array($langTable,$langTables[$tableName])) //try en_gb
				$langTable = reset($langTables[$tableName]);	 //else use first table
		}
		
		return preg_replace('#^'.preg_quote($db->getPrefix()).'#i','#__',$langTable);	
	}

    /**
     * Get VM language string
     * @param string $string
     * @param string $module optional for VM1
     */
    static function getVMTranslation($string,$module='common')
    {
    	$language =  JFactory::getLanguage();
    	$currTag = $language->get('tag');
    	
    	if (COM_VMINVOICE_ISVM2)
    	{
    		static $langLoaded;
    		
    		if (empty($langLoaded)){ //load english as base
    			
    			$language->load('com_virtuemart',JPATH_SITE,'en-GB');
    			$language->load('com_virtuemart_orders',JPATH_SITE,'en-GB');
    			$language->load('com_virtuemart_shoppers',JPATH_SITE,'en-GB');
    			$language->load('com_virtuemart',JPATH_ADMINISTRATOR,'en-GB');
    			$langLoaded = 'en-GB';
    		}
    		
    		if ($langLoaded!=$currTag){
    			
    			$language->load('com_virtuemart',JPATH_SITE,$currTag,true);
    			$language->load('com_virtuemart_orders',JPATH_SITE,$currTag,true);
    			$language->load('com_virtuemart_shoppers',JPATH_SITE,$currTag,true);
    			$language->load('com_virtuemart',JPATH_ADMINISTRATOR,$currTag,true);
    	    	$langLoaded = $currTag;
    		}

    		return JText::_($string);
    	}
    	elseif (COM_VMINVOICE_ISVM1)
    	{
			global $mosConfig_lang, $VM_LANG, $modulename;
			
			static $vm1Language;
			
			if (empty($vm1Language[$currTag]))
			{
				
				$backwardLang = is_callable(array($language, 'getBackwardLang')) ? strtolower($language->getBackwardLang()) : $currTag;
				
				
				$loadLang = $backwardLang;
				$langPath = JPATH_ADMINISTRATOR. '/components/com_virtuemart/languages/'.$module.'/';
				
				//try alternative names, if not, english
				if (!file_exists( $langPath.$loadLang.'.php' ))
					$loadLang = $backwardLang.'iso';
				if (!file_exists( $langPath.$loadLang.'.php' ))
					$loadLang = $backwardLang.'1250';
				if (!file_exists( $langPath.$loadLang.'.php' ))
					$loadLang = 'english';

				$mosConfig_lang = $loadLang;
				$GLOBALS['mosConfig_lang'] = $mosConfig_lang;
					
				InvoiceHelper::importVMFile('classes/language.class.php'); 
				$vm1Language[$currTag] = new vmLanguage();;
				$GLOBALS['VM_LANG'] = &$vm1Language[$currTag];				
				$vm1Language[$currTag]->_debug = false; //disable VM debug if translation not found
				
				//DONT! use vm_lang->load() function, because it use require_once - in more instances doesnt load language again
				//instead, use own load here
				if (file_exists($langPath.strtolower($loadLang).'.php')) 
					include( $langPath.strtolower($loadLang).'.php' );
			}
			
			$modulename = $module; //global variable used in _ function

			$vmTrans = $vm1Language[$currTag]->_($string); //get translation

			if (isset($vm1Language[$currTag]->modules[$module]['CHARSET']) and function_exists('iconv')) //convert to utf 8 based on encoding STATED in language file
				$vmTrans = iconv ($vm1Language[$currTag]->modules[$module]['CHARSET'] ,'utf-8' , $vmTrans );
			

			return ($vmTrans && $vmTrans!=$string) ? $vmTrans : JText::_($string);
    	}
			
    }
    
    /**
     * Gets "custom" user fields from Virtue Mart
     * TODO: get also only names (to use it in table sabe funsions to not sanitise chekcboxes not presented on form) (see tables)
     * 
     * @param	string	B_ or S_
     * @param	object	billingData or shippingData object
     *
     * @return	array	user fields labels and inputs
     */
    static function getUserFields($prefix,&$data)
    {
    	if (COM_VMINVOICE_ISVM2)
    	{
    		InvoiceHelper::importVMFile('models/userfields.php');
    		InvoiceHelper::importVMFile('tables/userfields.php');
    		InvoiceHelper::importVMFile('tables/countries.php');
    
    		//load VM language to inputs get translated
    		$language = JFactory::getLanguage();
    		$language->load('com_virtuemart',JPATH_SITE);
    		$language->load('com_virtuemart_orders',JPATH_SITE);
    		$language->load('com_virtuemart_shoppers',JPATH_SITE);
    		$language->load('com_virtuemart',JPATH_ADMINISTRATOR);
    		 
    		$model = new VirtueMartModelUserfields();
    
    		$skip = array('username', 'password', 'password2','agreed',
    				'first_name','last_name','middle_name','title','company','address_1','address_2',
    				'city','zip','virtuemart_country_id','virtuemart_state_id',
    				'email','phone_1','phone_2','fax','address_type_name','address_type');
    
    
    		$selection = $model->getUserFields('shipment',array('delimiters'=>false,'captcha'=>false),$skip);
    
    		//now add extra fields, if defined and not there yet (for example if that field is allowed only for registration form
    		//find, if there is extra fields 1 - 4
    		$extraFields = array();
    		$params = InvoiceHelper::getParams();
    		foreach (range(1,4) as $i) //put extra fields we need to get additionaly to speical array
    			if ($extra_field = $params->get('extra_field'.$i)){
    			$extraFields[$extra_field] = false; //add to array
    			foreach ($selection as $field)
    				if ($field->virtuemart_userfield_id == $extra_field) //if this is already presented in selection, remove
    				unset($extraFields[$extra_field]);
    		}
    		 
    		 
    		if ($extraFields){ //there are fields to get
    			foreach (array('registration','account') as $area) //find fields for registration or account form
    				foreach ($model->getUserFields($area,array('delimiters'=>false,'captcha'=>false),$skip) as $field)  //go and check if extra field is inside
    				if (isset($extraFields[$field->virtuemart_userfield_id]))
    				$extraFields[$field->virtuemart_userfield_id] = $field; //yes, add it to result
    			 
    			foreach ($extraFields as $field) //go through results
    				if ($field) //not false
    				array_push($selection, $field); //add to result array
    		}
    
    		$fields = $model->getUserFieldsFilled($selection,$data,$prefix);
    
    		$return = array();
    
    		if (count($fields['fields'])) foreach ($fields['fields'] as $field)
    			if(!empty($field['formcode']))
    			$return[] = array('title' => $field['title'],'input' => $field['formcode'],'desc' => $field['title']);
    
    		return $return;
    	}
    	else
    	{
    		//load VM1 framework
    		global $mosConfig_absolute_path;
    			
    		InvoiceHelper::importVMFile('virtuemart_parser.php',false);
    		InvoiceHelper::importVMFile('classes/ps_userfield.php');
    			
    		//get all non-system user fields for shipping or/and fields that are defined in VMInvoice config
    		$params = InvoiceHelper::getParams();
    		$db = JFactory::getDBO();
    		$extras='';
    		foreach (range(1,4) as $i)
    			if ($extra_field = $params->get('extra_field'.$i))
    			$extras .= " OR fieldid=".$db->Quote($extra_field);
    
    		$db->setQuery('SELECT * FROM `#__vm_userfield` WHERE (`type`!=\'delimiter\' AND `shipping`=1 AND `sys`=0 AND `published`=1)'.$extras.' ORDER BY ordering');
    		$userFields = $db->loadObjectList();
    			
    		$skipFields = array();
    		$db = new ps_DB();
    
    		foreach ($userFields as & $field){
    
    			if (property_exists($data, $field->name)){ //if we have default value set
    				$field->default=$data->{$field->name};  //set "fake" default value to VM
    				$db->record[0]->{$prefix.$field->name} = $data->{$field->name}; //set "fake" database record to VM
    			}
    			$field->name = $prefix.$field->name; //append B_ or S_ prefix to field name
    			$field->required = 0; //empty requied info
    
    			if ($field->type=='delimiter') //unset delimiters
    				unset($field);
    		}
    			
    		//call VM and catch thrown html
    		ob_start();
    		ps_userfield::listUserFields( $userFields, $skipFields, $db, false );
    		$userInfo = ob_get_clean();
    			
    		//parse title, help hint and inputs from VM's gibbrish
    		$regExp = 'class="formLabel.*"\s*>\s*<\s*label.*>(.*)<\s*\/\s*label.*
			class="formField.*"\s*>(.*)\s*(?:<\s*span.*onmouseover="Tip\s*\(\s*\'(.*)\'.*<\s*\/\s*span\s*>)?\s*(?:<\s*br\s*\/?s*>)?\s*<\s*\/\s*div\s*>';
    		$userInfo = preg_match_all('/'.$regExp.'/iUsx',$userInfo,$matches,PREG_SET_ORDER);
    			
    		//store them to nice array
    		$return = array();
    		if (!empty($userInfo)){
    
    			foreach ($matches as $key => $match){
    					
    				$return[$key]['title'] = $match[1];
    				$return[$key]['input'] = $match[2];
    				if (isset($match[3]))
    					$return[$key]['desc'] = $match[3];
    			}
    		}
    	}
    	 
    	return $return;
    }
    
    
    //$products is array of objects with properties: product_id(for VM1), product_weight, product_weight_uom, product_quantity
    static function getOrderWeight($products, $toUnit = null)
    {
    	$weight = 0;
    	
    	if (!$products)
    		return array(0, $toUnit);
    	
    	if (!$toUnit){ //determine proper final unit
    		 
    		$usedUnits = array();
    		foreach ($products as $product) //make list of used units at products
    			if ($product->product_weight)
    				$usedUnits[$product->product_weight_uom] = true;
    		 
    		if (count($usedUnits)==1) //only one unit used, pick it
    			$toUnit = key($usedUnits);
    		else{ //used more units
    	
    			if (COM_VMINVOICE_ISVM2){ //in VM2, try pick shop default unit
    				InvoiceHelper::importVMFile('helpers/config.php');
    				$def = VmConfig::get('weight_unit_default', '', true); //pick default weight unit
    				if (isset($usedUnits[$def]))
    					$toUnit = $def;
    			}
    	
    			if (!$toUnit) //if default not used, pick one from priority list, dont mess with it
    				foreach (array('KG', 'LB', 'G', 'OZ', 'MG') as $unit)
    					if (isset($usedUnits[$unit])){
    						$toUnit = $unit;break;}
    	
    			if (!$toUnit) //else pick first (vm1 - they have it as text area)
    				$toUnit = key($usedUnits);
    		}
    		 
    		if (!$toUnit)
    			$toUnit = 'KG'; //weird
    	}
    	
    	if (COM_VMINVOICE_ISVM2){
    		
    		if (!InvoiceHelper::importVMFile('helpers/shopfunctions.php')) //some earlier VM functions does not have it
    			return array('N/A', '');

	    	foreach ($products as $product){  //compute and convert
	    		if (is_callable('ShopFunctions::convertWeightUnit'))
	    			$weight += ShopFunctions::convertWeightUnit ($product->product_weight, $product->product_weight_uom, $toUnit) * $product->product_quantity;
    			elseif (is_callable('ShopFunctions::convertWeigthUnit')) //typo
    				$weight += ShopFunctions::convertWeigthUnit($product->product_weight, $product->product_weight_uom, $toUnit) * $product->product_quantity;
	    		else
	    			return array('N/A', '');
	    	}	
	    }
    	else { //vm1
    		
    		if (!InvoiceHelper::importVMFile('classes/ps_shipping_method.php'))
    			return array('N/A', '');
    		
    		if (preg_match('#^(g|grams?|m(ili)?g(rams?))?$#i', $toUnit)) //...
    			$toUnit = 'KG'; //i kdyz.. slo by to..
    		
    		//determine final
    		foreach ($products as $product){  //compute and convert
    			if ($product->product_weight){
	    			if (preg_match('#(kg|kilo)#i', $toUnit))
	    				$itemWeight = ps_shipping_method::get_weight_KG($product->product_id) * $product->product_quantity;
	    			elseif (preg_match('#(lb|po)#i', $toUnit))
	    				$itemWeight = ps_shipping_method::get_weight_LB($product->product_id) * $product->product_quantity;
	    			elseif (preg_match('#(ou|oz)#i', $toUnit))
	    				$itemWeight = ps_shipping_method::get_weight_OZ($product->product_id) * $product->product_quantity;
	    			else //no proper weight unit defined
	    				$itemWeight = 0;
	    			
	    			if (!$itemWeight) //no proper weight unit OR bad result of functions above
	    				return array('N/A', '');
	    			
	    			$weight += $itemWeight;
    			}
    		}
    	}

    	return array(round($weight, 3), $toUnit); //round to 3 decimal places
    }
        
    
    
    static function getTranslatableLanguages()
    {
    	static $languages;
    	
    	if (!isset($languages)){
    		$languages = array();
    
    		if (COM_VMINVOICE_ISJ16){
    			foreach (JLanguageHelper::getLanguages() as $language)
    				$languages[$language->lang_code] = $language;
    		}
    		else{
    			foreach (JLanguage::getKnownLanguages(JPATH_SITE) as $tag => $metadata)
    				$languages[$tag] = (object)array('lang_code' => $tag, 'title' => $metadata['name']);
    		}
    	}
    	 
    	return $languages;
    }
}

?>
