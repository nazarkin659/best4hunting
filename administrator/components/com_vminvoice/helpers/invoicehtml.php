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

class InvoiceHTML extends InvoiceHTMLParent
{
    
    var $db;
    var $images;
    var $params;
    var $currency;
    var $colsCount;
    var $fields;
    var $taxSum;
    var $subtotal = 0;
    var $deliveryNote;
    var $order;
    var $payment;
    var $vendor;
	var $currencyDisplay;
    //TODO: altermative classes: (shell cmd). maybe add like downloadable option?
    //http://code.google.com/p/wkhtmltopdf/

    function replaceTags($match)
    {
    	$brBefore = $match[1]; //http://www.artio.net/cz/support-forums/vm-invoice/customer-support/not-displaying-blank-lines-in-custom-address-tags
    	$tag = $match[2];
    	$colonAfter = isset($match[3]) ? $match[3] : '';

    	$replacement = false; 
    	
		$opening='';
    	$tag = trim($tag,' {}');
    	$closing='';
    	
    	//what?
    	//"You can add HTML tags to {} content and that tags will be applied only if tag is not empty.
    	// For example {shipping_address_2<br>} - <br> will be applied only if shipping_address_2 is presented"
    	if (preg_match('#^\s*(<[^}]*>)?\s*(\w+)\s*(<[^}]*>)?\s*$#Us',$tag, $matches)){ //tag content can be wrapped to html tags (commonly <br>)
	    	$opening = $matches[1];
	    	$tag = strtolower(trim($matches[2]));
			$closing = isset($matches[3]) ? $matches[3] : '';
    	}
		
    	$dateStr = $this->params->get('date_pattern') ? $this->params->get('date_pattern') : 'd.m.Y';
    	
    	//TODO: add order_create_date, modify date

    	//trigger event. if some plugin returns string, it is used as replacement no other replacement is done
        $results = $this->dispatcher->trigger('onTagBeforeReplace', array(&$tag, &$this, $this->params));
    	foreach ($results as $result)
    		if (is_string($result))
    			$replacement = $result;
        
        if ($replacement===false)
        {
    	switch ($tag) {
    		
    		
    		
    		case 'contact':
    	        if ($this->params->get('show_contact', 1)) {
    	        	
    	        	$contact = array();
    	        	if (!empty($this->vendor->company_name)) $contact[] = $this->vendor->company_name;
    	        	if (!empty($this->vendor->address_1)) $contact[] = $this->vendor->address_1;
    	        	if (!empty($this->vendor->address_2)) $contact[] = $this->vendor->address_2;
    	        	if (!empty($this->vendor->zip) OR !empty($this->vendor->city)) $contact[] = @$this->vendor->zip . ' ' . @$this->vendor->city;
    	        	if (!empty($this->vendor->state_name)) $contact[] = $this->vendor->state_name;
    	        	if (!empty($this->vendor->country_name)) $contact[] = $this->vendor->country_name;
		            $replacement = VMI_NL. implode (' | ', $contact);
		        }
        		break;
        		
    		case 'logo':
    			if ($this->params->get('show_logo', 1)) {
    				$replacement = $this->getVendorImage();
    			}
    			break;
    			
    		case 'shipping_date_cpt':
    			if ($this->params->get('show_shipping_date'))
    				$replacement = $this->_('COM_VMINVOICE_SHIPPING_DATE');
    			break; 
    			
    		case 'shipping_date':
    			if ($this->params->get('show_shipping_date')) {
		            if ($shippingDate = InvoiceGetter::getOrderShippingDate($this->order->order_id))
		            	$replacement = $this->formatGMDate($dateStr, $shippingDate);
	            	else
	            		$replacement = $this->_('COM_VMINVOICE_NO_SHIPPING_DATE');
    			}
            	break;
            	
            case 'shipping_cpt':	
            	$replacement = $this->_('COM_VMINVOICE_HANDLING_AND_SHIPPING');
            	break;
            	
            case 'shipping_name':
            	$replacement = $this->order->shipment_name;
            	break;
            	
            case 'shipping_desc':
            	$replacement = nl2br($this->order->shipment_desc);
            	break;

    		case 'payment_type_cpt':
    			if ($this->params->get('show_payment_type'))
    				$replacement = $this->_('COM_VMINVOICE_PAYMENT_TYPE');
    			break; 
    			
    		case 'payment_type':
    		case 'payment_type_desc':
    			if ($this->params->get('show_payment_type')){
    				$info = $this->getPaymentInfo();
    				$replacement = $info[$tag=='payment_type' ? 0 : 1];
    			}	
            	break;
            	
    		case 'variable_symbol_cpt':
    			if ($this->params->get('show_variable_symbol'))
    				$replacement = $this->_('COM_VMINVOICE_VARIABLE_SYMBOL');
    			break;
            	
    		case 'variable_symbol':
    			if ($this->params->get('show_variable_symbol')){
    				$field = $this->params->get('variable_symbol');
    				if ($field=='order_no')
    					$replacement = $this->order->order_id;
    				elseif ($field=='invoice_no')
    					$replacement = $this->invoice_no;
    				elseif ($field=='order_number')
    					$replacement = $this->order->order_number;
    			}
    			break;
    			
    		case 'finnish_index_number_cpt':
    			if ($this->params->get('index_number_fi'))
    				$replacement = $this->_('COM_VMINVOICE_INDEX_NUMBER');
    			break;
    			
    		case 'finnish_index_number':
    			if ($this->params->get('index_number_fi')) {
				    // NOTE: Index number must be atleast 4 chars! (3 + checksum), so own invoice numbering which have enougt digits  must be used
				    // Get only numbers
				    $val = preg_replace('/[^0-9]/', '', $this->invoice_no);
				    $replacement = $this->countReferenceFI($val);
    			}
    			break;
    			
    		case 'customer_number_cpt':
    			if ($this->params->get('show_customer_number'))
    				$replacement = $this->_('COM_VMINVOICE_CUSTOMER_NUMBER');
    			break;
    			
    		case 'customer_number':
    			if ($this->params->get('show_customer_number')) {
		        	$replacement = ($custNo = InvoiceGetter::getCustomerNumber($this->order->user_id)) ? $custNo : '' ;
    			}
    			break;
    			
    		case 'shopper_group_cpt':
    			if ($this->params->get('show_shopper_group'))
    				$replacement = $this->_('COM_VMINVOICE_SHOPPER_GROUP');
    			break;
    			
    		case 'shopper_group':
    			if ($this->params->get('show_shopper_group'))
		        	$replacement = implode(', ', InvoiceGetter::getShopperGroup($this->order->user_id));
    			break;

    		case 'coupon_code_cpt':
    			if ($this->order->coupon_code)
    				$replacement = $this->_('COM_VMINVOICE_COUPON_CODE');
    			break;	
    			
    		case 'coupon_code':
    			if ($this->order->coupon_code)
    				$replacement = $this->order->coupon_code;
    			break;	
    			
    		case 'coupon_discount':
    			$replacement = $this->currencyDisplay->getValue($this->order->coupon_discount, $this->currency);
    			break;	
    		case 'coupon_discount-words':
    			$replacement = $this->toWords($this->order->coupon_discount);
    			break;
    			
    		case 'order_discount':
    			$replacement = $this->currencyDisplay->getValue($this->order->order_discount, $this->currency);
    			break;	
    		case 'order_discount-words':
    			$replacement = $this->toWords($this->order->order_discount);
    			break;
    				
    		case 'subtotal_net':
    			$replacement = $this->currencyDisplay->getValue($this->subtotal_net, $this->currency);
    			break;	
    		case 'subtotal_net-words':
    			$replacement = $this->toWords($this->subtotal_net);
    			break;
    				
    		case 'subtotal_tax':
    			$replacement = $this->currencyDisplay->getValue($this->subtotal_tax, $this->currency);
    			break;	
    		case 'subtotal_tax-words':
    			$replacement = $this->toWords($this->subtotal_tax);
    			break;
	
    		case 'subtotal_gross':
    			$replacement = $this->currencyDisplay->getValue($this->subtotal_gross, $this->currency);
    			break;	
    		case 'subtotal_gross-words':
    			$replacement = $this->toWords($this->subtotal_gross);
    			break;

    		case 'total':
    			$replacement = $this->currencyDisplay->getValue($this->order->order_total, $this->currency);
    			break;
    		case 'total-words':
    			$replacement = $this->toWords($this->order->order_total);
    			break;
    			
    		case 'billing_address':
    		case 'shipping_address':
    			
		        // load addresses        
		        $address = array();
		        $adressType = (! $this->deliveryNote) ? $this->params->get('invoice_address') : $this->params->get('dn_address');
		        $address['BT'] = $this->address['BT'];
		        $address['ST'] = $this->address['ST'];   
		        
		        //determine if shipping address is set
		        $STAddressSet=false;
		        $fields = array('first_name','last_name','company','address_1','address_2','city','zip','state','country_name'); //fields to check in comparing
		        //$ignore = array('order_info_id','order_id','user_id','address_type','bank_account_type','address_type_name'); //fields to ignore in comparing
		        
		        //we must determine, if shipping address is set
		        //that mean if it is not empty
		        //and if it is not if is not the same as billing.
		        
		        if (isset($address['ST']->order_id) AND intval($address['ST']->order_id) > 0){
			        foreach ($address['ST'] as $key => $val)
			        	if (in_array($key,$fields) AND !empty($val) AND trim($val)!='' AND $val!=$address['BT']->$key){
			        		$STAddressSet=true;
			        		break;}}
		
		        // if ST address is empty and should be shown always both
			    if (($adressType == 'both' OR $adressType == 'ST') AND !$STAddressSet) {
		            $address['ST'] = $address['BT'];
		            $STAddressSet = true;
		        }
		        //if show delivery only if differs from billing
		        if ($adressType == 'bothi'){
		        	$adressType = 'BT';
		        	if ($STAddressSet)
				        foreach ($address['BT'] as $key => $val)
				        	if (in_array($key,$fields) AND $val!=$address['ST']->$key AND !empty($address['ST']->$key) AND trim($address['ST']->$key)!=''){
				        		$adressType = 'both';
				        		break;}
		        }
		        
		        if ($tag=='billing_address' AND ($adressType == 'BT' OR $adressType == 'both' OR !$STAddressSet))
		        	$replacement = (array_key_exists('BT', $address) ? $this->generateAddress($address['BT'], 'BT') : '');
		        if ($tag=='shipping_address' AND (($adressType == 'ST' OR $adressType == 'both') AND $STAddressSet))
		        	$replacement = (array_key_exists('ST', $address) ? $this->generateAddress($address['ST'], 'ST') : '');
    			break;
        		
    		case 'customer_note_cpt':
    			if (trim($this->order->customer_note)!='' AND (($this->deliveryNote AND $this->params->get('dn_customer_note')) OR (!$this->deliveryNote AND $this->params->get('in_customer_note'))))
    				$replacement = $this->_('COM_VMINVOICE_CUSTOMER_NOTE');
    			break;
    			
    		case 'customer_note':
    	        if (trim($this->order->customer_note)!='' AND (($this->deliveryNote AND $this->params->get('dn_customer_note')) OR (!$this->deliveryNote AND $this->params->get('in_customer_note'))))
		        	$replacement = nl2br(strip_tags($this->order->customer_note));
    			break;
    			        		
    		case 'order_status_cpt': 
    			$replacement = $this->_('COM_VMINVOICE_ORDER_STATUS');
    			break;
    			
    		case 'order_status': 
    	        $replacement = $this->_($this->order->order_status_name);
    			break;
		
    		case 'order_history':
    		
    			$db = JFactory::getDBO();
				if (COM_VMINVOICE_ISVM2)
					$db->setQuery('SELECT modified_on AS time, order_status_code, comments FROM #__virtuemart_order_histories WHERE virtuemart_order_id='.(int)$this->order->order_id.' ORDER BY time DESC');
				else 
					$db->setQuery('SELECT date_added AS time, order_status_code, comments FROM #__vm_order_history WHERE order_id='.(int)$this->order->order_id.' ORDER BY time DESC');
				$history = $db->loadObjectList();
				
				if ($history){
					
					$states = invoiceGetter::getOrderStates();
					$replacement = '<table>';
					foreach ($history as $action){
						
						$add = strpos($dateStr, '%')===false ? ' G:i' : ' %H:%M'; //add time information
						
						$replacement .= '<tr>';
						$replacement .= '<td>'.$this->formatGMDate($dateStr.$add, InvoiceHelper::gmStrtotime($action->time)).'</td>';
						$replacement .= '<td>'.$this->_($states[$action->order_status_code]->name).'</td>';
						$replacement .= '<td>'.nl2br($action->comments).'</td>';
						$replacement .= '</tr>';
					}
					
					$replacement .= '</table>';
				}

    			break;
	
    		
    			
    		
    			
    		//common: shoudld work also for booking if no error
    		
    		case 'items':
    			$br = '\s*<\s*br\s*\/?\s*>\s*';
    			$colon = '\s*:\s*';
    			$items = $this->generateItems(); //get items table
    			$replacement = preg_replace_callback('#('.$br.')?('.TAG_REGEXP.')('.$colon.')?#is',array( &$this, 'replaceTags'),$items); //call self to replace _cpt tags inside table
    			break;
    			
    		case 'invoice_date_cpt':
    			if ($this->deliveryNote ? $this->params->get('dn_date_label') : $this->params->get('invoice_date_label'))
	    			$replacement = $this->deliveryNote ? $this->_('COM_VMINVOICE_DATE') : $this->_('COM_VMINVOICE_INVOICE_DATE');
    			break;
	    
    		case 'invoice_date':
        		if (empty($this->mailsended->invoice_date)){ //for some reason now row in db, but should be. use default date from config.
        			$dateType = $this->params->get('invoice_date');
        			$invoiceDate = ($dateType == 'cdate' OR $dateType == 'mdate') ? $this->order->$dateType : time();
        		}
        		else
        			$invoiceDate = $this->mailsended->invoice_date;
        			
				$replacement = $this->formatGMDate($dateStr,$invoiceDate);
    			break;
    			
    		case 'taxable_payment_date_cpt':
    			if ($this->params->get('show_taxable_payment_date'))
    				$replacement = $this->_('COM_VMINVOICE_TAXABLE_PAYMENT_DATE');
    			break;
    			
    		case 'maturity_date_cpt':
    			if ($this->params->get('show_maturity_date'))
    				$replacement = $this->_('COM_VMINVOICE_MATURITY_DATE');
    			break;
    			
    		case 'taxable_payment_date':
    		case 'maturity_date':
    			
	           $val = $this->params->get('taxable_payment_date');
	           if (empty($val) OR $val=='invdate') { //same as above. TODO. optimize
        			if ($this->mailsended->invoice_date)
        				$taxable_payment_date = $this->mailsended->invoice_date;
        			else{ //for some reason not in row in db, but should be. use default date from config.
        				$dateType = $this->params->get('invoice_date');
        				$taxable_payment_date = ($dateType == 'cdate' OR $dateType == 'mdate') ? $this->order->$dateType : time();
        			}
	            }
	            else
	            	$taxable_payment_date = $this->order->$val;

	            if ($tag=='taxable_payment_date' AND $this->params->get('show_taxable_payment_date'))
	            	$replacement = $this->formatGMDate($dateStr,$taxable_payment_date);
	            elseif ($tag=='maturity_date' AND $this->params->get('show_maturity_date'))
	            	$replacement = $this->formatGMDate($dateStr,$taxable_payment_date+$this->params->get('maturity') * 86400);
	            	
    			break;
    			
    		case 'extra_fields':
    	        if (($this->template_type=="body" AND $this->params->get('fields_pos', 0) == 1) //template body
    	        	OR
    	        	($this->template_type=="footer" AND 
    	        	($this->params->get('fields_pos', 0) == 0 //every footer 
    	        		OR 
    	        	($this->params->get('fields_pos', 0) == 3 AND $this->lastPage)))) //only last footer
		            $replacement = $this->generateExtraFields();
        		break;
        		
    		case 'signature': 
    			if ($this->params->get('show_signature', 1))
	    			$replacement = $this->getSignature();
    			break;

    		case 'pagination': 
    			$this->onlyOnePage=isset($this->onlyOnePage) ? $this->onlyOnePage : false;
    	        $pagination = $this->params->get('show_pagination', 2);
				if ($pagination == 2 AND !$this->onlyOnePage OR $pagination == 1)
    	        	$replacement = $this->sprintf('COM_VMINVOICE_PAGE_S_OF_S', $this->currPageInGroup, $this->totalGroups);
    			break;
        		
    		case 'order_note_cpt':
    			if ($this->params->get('allow_order_notes', 0))
    				$replacement = $this->_('COM_VMINVOICE_ORDER_NOTES');
    			break;
    				
    		case 'order_note':
    			if ($this->params->get('allow_order_notes', 0)){
    				$orderParams = invoicegetter::getOrderParams($this->order->order_id);
    				if (isset($orderParams->order_note))
    					$replacement = nl2br($orderParams->order_note);
    			}
    			break;
    				
            case 'total_weight':
                $replacement = $this->total_weight[0];
                break;
            
            case 'total_weight_cpt':
                $replacement = $this->_('COM_VMINVOICE_TOTAL_WEIGHT');
                break;
            
            case 'weight_unit':
                $replacement = $this->total_weight[1];
                break;
    				
    		default:
    			if (isset($this->replacementFields[$tag]))
    				$replacement = $this->replacementFields[$tag];
    			elseif (isset($this->replacementFields[strtolower($tag)]))
    				$replacement = $this->replacementFields[strtolower($tag)];
    	} //end: switch
        } //end: if ($replacement===false)

        if ($replacement===false)
        	$replacement = ''; //not supported tag?
        
        //trigger plugin to allow additional changes
        $neighbours = array(&$brBefore, &$opening, &$closing, &$colonAfter); //allow plugin also change enclosing neighbours
        $this->dispatcher->trigger('onTagAfterReplace', array(&$tag, &$replacement, &$neighbours, &$this, $this->params));

    	if (trim($replacement)!='') //proper replacement - involve also opening and closing tags, brs and colons..
    		return $brBefore.$opening.$replacement.$closing.$colonAfter;
    	else //empty replacement - output nothing
    		return '';
    }
    
    function getPaymentInfo()
    {
    	if (COM_VMINVOICE_ISVM2) //vm2 loads it from plugin table
    		$info = array($this->order->payment_name, $this->order->payment_desc);

    	if (!COM_VMINVOICE_ISVM2 OR empty($info[0])){ //vm1 or some error (?) above
    		$payments = InvoiceGetter::getPayments($this->language);
    		if (isset($payments[$this->order->payment_method_id]))
    			$info = array($payments[$this->order->payment_method_id]->name, $payments[$this->order->payment_method_id]->desc);
    	}

    	return $info;
    }
    
    function initializeReplacements()
    {
    	$this->replacementFields = array();
    	$this->replacementFields['invoice_number'] = $this->invoice_no;
    	
    	
 		$this->replacementFields['order_id'] = $this->order->order_id;
 		$this->replacementFields['order_number'] = $this->order->order_number;

 		$allowedAddrFields = InvoiceGetter::getOrderAddress();
 		
 		foreach ($this->address['BT'] as $key => $val) //billing tags
 			if (in_array($key,$allowedAddrFields))
 				$this->replacementFields['billing_'.strtolower($key)] = $val;
 				
 		foreach ($this->address['ST'] as $key => $val) //shipping tags
 			if (in_array($key,$allowedAddrFields))
 				$this->replacementFields['shipping_'.strtolower($key)] = $val;
 		
 		/*
 		$allowedUserFields = InvoiceGetter::getUserInfo(); //TODO: dodelat tohle a taky aby fungovala napoveda a byly tam vsechny ty pekne adresy
 		$userInfo = InvoiceGetter::getUserInfo(null, $this->order->user_id, 'BT');
 		foreach ($userInfo as $key => $val) //user default BT info
 			if (in_array($key,$allowedUserFields))
 				$this->replacementFields['user_'.$key] = $val;
 		var_dump($this->replacementFields, $userInfo);exit;
 		*/
 		
 		foreach (InvoiceGetter::getVendor() AS $field) //vendor tags
 			if (isset($this->vendor->$field))
 				$this->replacementFields['vendor_'.strtolower($field)] = $this->vendor->$field;

 		foreach ($this->order as $key => $val) //general order tags
 			if (!isset($this->replacementFields['order_'.$key]))
 				$this->replacementFields['order_'.strtolower($key)] = $val;
 		
 		
	    $this->replacementFields['start_note'] = $this->fields->note_start;
	    $this->replacementFields['end_note'] = $this->fields->note_end;
	    
 		
	    
	    //items header language strings
	     $this->replacementFields['qty_cpt'] = $this->_('COM_VMINVOICE_QTY');
	     $this->replacementFields['sku_cpt'] = $this->_('COM_VMINVOICE_SKU');
	     $this->replacementFields['name_cpt'] = $this->_('COM_VMINVOICE_PRODUCT_NAME');
	     $this->replacementFields['price_cpt'] = $this->_('COM_VMINVOICE_PRICE');
	     $this->replacementFields['base_total_cpt'] = $this->_('COM_VMINVOICE_BASE_TOTAL');
	     $this->replacementFields['tax_rate_cpt'] = $this->_('COM_VMINVOICE_TAX_RATE');
	     $this->replacementFields['tax_cpt'] = $this->_('COM_VMINVOICE_TAX');
	     
	     $this->determineOnlyTaxRate();
	     if ($this->params->get('show_total_tax_percent') AND $this->onlyOneTaxRate) // append only tax rate if possible 
	     	$this->replacementFields['tax_cpt'] .= ' (' . $this->onlyOneTaxRate . '%)';
	     
	     $this->replacementFields['discount_cpt'] = $this->_('COM_VMINVOICE_DISCOUNT');
	     $this->replacementFields['subtotal_cpt'] = $this->_('COM_VMINVOICE_SUBTOTAL');
	     $this->replacementFields['order_number_cpt'] = $this->_('COM_VMINVOICE_ORDER_NUMBER');
	     
	     $this->replacementFields['invoice_cpt'] = $this->_('COM_VMINVOICE_INVOICE');
	     $this->replacementFields['dn_cpt'] = $this->_('COM_VMINVOICE_DELIVERY_NOTE');	   
	     
	     
	     $this->replacementFields['shipping_net'] =  $this->currencyDisplay->getValue($this->order->order_shipping, $this->currency);
	     $this->replacementFields['shipping_tax'] = $this->currencyDisplay->getValue($this->order->order_shipping_tax, $this->currency);
	     $this->replacementFields['shipping_tax_rate'] =  $this->shipTaxRate.'%';
	     $this->replacementFields['shipping_gross'] =  $this->currencyDisplay->getValue($this->order->order_shipping + $this->order->order_shipping_tax, $this->currency);
	     
		 $this->replacementFields['payment_net'] =  $this->currencyDisplay->getValue($this->order->order_payment, $this->currency);
		 $this->replacementFields['payment_tax'] = $this->currencyDisplay->getValue($this->order->order_payment_tax, $this->currency);
		 $this->replacementFields['payment_tax_rate'] =  $this->paymentTaxRate.'%';
		 $this->replacementFields['payment_gross'] =  $this->currencyDisplay->getValue($this->order->order_payment + $this->order->order_payment_tax, $this->currency);
		 

	     
	    $this->replacementFields['items_count'] = $this->items_count;
	    $this->replacementFields['items_sum'] = $this->items_sum;
    }
    
    function InvoiceHTML($orderID, $deliveryNote, $language)
    {
    	parent::InvoiceHTML($orderID, $language);
    	
    	$this->deliveryNote = $deliveryNote;
                
        
    	$this->order = InvoiceGetter::getOrder($orderID, $this->language);
    	
    	//now we can create currency converter
    	$this->currencyDisplay = new InvoiceCurrencyDisplay($this->order->order_currency);
    	if (!empty($this->order->user_currency_rate)) //add fixed rate for order -> payment currency. can be 1.
    		$this->currencyDisplay->fixedRates[$this->order->order_currency][$this->order->user_currency_id] = $this->order->user_currency_rate;
    	
    	//get display currency
    	$this->currency = $this->order->order_currency;

    	//use payment currency instead? (echange rate can be 1)
    	if ($this->params->get('display_in_payment_currency'))
    		$this->currency = $this->order->user_currency_id ? $this->order->user_currency_id : $this->currency;
    	
    	//in VM1, compute order payment to make it look like VM2
    	if (COM_VMINVOICE_ISVM1)
    	{
    		$this->paymentTaxRate = round($this->params->get('paymenet_taxrate'),2);
    		
    		if ($this->params->get('payment_amount_source')==1) //use just order discount
    		{
    			$gross = $this->order->order_discount;
    		}
    		else //get payment based on selected method
    		{
    			if ($method = InvoiceGetter::getPaymentMethod($this->order->payment_method_id, $this->language))  //(little inspired by ps_checkout get_payment_discount())
    			{
    				if ($method->payment_method_discount_is_percent==1){
    					$gross = $this->order->order_subtotal * $method->payment_method_discount / 100;
	    				if ($method->payment_method_discount_max_amount*1 AND abs($gross) > $method->payment_method_discount_max_amount*1)
	    					$gross = $method->payment_method_discount_max_amount*1;
	    					
	    				if ($method->payment_method_discount_min_amount*1 AND abs($gross) < $method->payment_method_discount_min_amount*1)
	    					$gross = $method->payment_method_discount_min_amount*1;
    				}
    				else
    					$gross = $method->payment_method_discount*1;
    			}  
    			else
    				$gross = 0;  			
    		}
    		
    		$gross = -$gross;
    		
    		$this->order->order_payment = $gross / (($this->paymentTaxRate/100)+1); //compute net
    		$this->order->order_payment_tax = $gross - $this->order->order_payment; //compute tax
    		
    	}
    	else { //vm2 
    	
    		$this->order->shipCalcRules = InvoiceGetter::getOrderCalcRules($this->order->order_id, -2);
    		$this->order->paymentCalcRules = InvoiceGetter::getOrderCalcRules($this->order->order_id, -3);
    	
    		$this->paymentTaxRate = $this->order->order_payment ? round(InvoiceHelper::guessTaxRate($this->order->order_payment+$this->order->order_payment_tax,$this->order->order_payment, $this->order->paymentCalcRules) * 100,2)*1 : 0;
    	}
    	
        $this->invoice_no =  InvoiceHelper::getInvoiceNo($this->order->order_id);
        $this->address = array();
        $this->address['BT'] = InvoiceGetter::getOrderAddress($this->order->order_id,'BT');
		$this->address['ST'] = InvoiceGetter::getOrderAddress($this->order->order_id,'ST');      
       	
        // load vendor info
        $this->vendor = InvoiceGetter::getVendor($this->order->vendor_id, $this->language);

        //get items and calculate tax summary and subtotals
        $this->taxSum = array();

        //get general calc rules
        if (COM_VMINVOICE_ISVM2)
        	$this->calcRules = InvoiceGetter::getOrderCalcRules($orderID, -1);
        	
        
        
        
        $this->loadItems(); 
        $this->initializeReplacements();
    }

    function generateExtraFields ()
    {
        $code = '';
        $code .= VMI_NL . sprintf('<table style="background-color: %s; font-size: 80%%;width:100%%" cellpadding="8"><tr>',  $this->params->get('fields_bg', '#efefef'));

        // vendor info
        $code .= VMI_NL . '  <td>';
        
        $fields1 = array();
        
        
        $fields1[] = $this->vendor->company_name;
        $fields1[] = $this->vendor->address_1;
        if ($this->vendor->address_2)
            $fields1[] = $this->vendor->address_2;
        
        $format = $this->params->get('address_format');
        if ($format=='usa')
        	$fields1[] =  $this->vendor->city .(!empty($this->vendor->state_2_code) AND !is_numeric($this->vendor->state_2_code) ? ', '.$this->vendor->state_2_code : @$this->vendor->state_name).' '.$this->vendor->zip;
        elseif ($format=='uk') {
        	$fields1[] = $this->vendor->city;
        	if (!empty($this->vendor->state_name))
        		$fields1[] = $this->vendor->state_name;
        	$fields1[] = $this->vendor->zip;
        }
        else {
        	$fields1[] = $this->vendor->zip . ' ' . $this->vendor->city ;
        	if (isset($this->vendor->state_name))
        		$fields1[] = $this->vendor->state_name ;
        }
        
        $fields1[] = $this->vendor->country_name ;
        $fields1[] = $this->vendor->url;
        
        
        $code .=  implode("<br />", $fields1) . VMI_NL . '  </td>';

        // extra fields 1
        $code .= VMI_NL . '  <td>';

        $fields2 = array();
        
        if ($this->fields->show_bank_name == 1) {
            $fields2[] = $this->_('COM_VMINVOICE_BANK_NAME') . ':  ' . $this->fields->bank_name;
        }
        if ($this->fields->show_account_nr == 1) {
            $fields2[] = $this->_('COM_VMINVOICE_ACCOUNT_NUMBER') . ':  ' . $this->fields->account_nr;
        }
        if ($this->fields->show_bank_code_no == 1) {
            $fields2[] = $this->_('COM_VMINVOICE_BANK_CODE') . ':  ' . $this->fields->bank_code_no;
        }
        if ($this->fields->show_bic_swift == 1) {
            $fields2[] = $this->_('COM_VMINVOICE_BIC_SWIFT') . ':  ' . $this->fields->bic_swift;
        }
        if ($this->fields->show_iban == 1) {
            $fields2[] = $this->_('COM_VMINVOICE_IBAN') . ':  ' . $this->fields->iban;
        }
        
        $code .= implode("<br />", $fields2) . VMI_NL . '  </td>';
        
        // extra fields 2
        $code .= VMI_NL . '  <td>';

        $fields3 = array();
        if ($this->fields->show_tax_number == 1) {
            $fields3[] = $this->_('COM_VMINVOICE_TAX_NUMBER') . ':  ' . $this->fields->tax_number;
        }
        if ($this->fields->show_vat_id == 1) {
            $fields3[] = $this->_('COM_VMINVOICE_VAT_ID') . ':  ' . $this->fields->vat_id;
        }
        if ($this->fields->show_registration_court == 1) {
            $fields3[] = $this->_('COM_VMINVOICE_REGISTRATION_COURT') . ':  ' . $this->fields->registration_court;
        }
        if ($this->fields->show_phone == 1) {
            $fields3[] = $this->_('COM_VMINVOICE_PHONE') . ':  ' . $this->fields->phone;
        }
        if ($this->fields->show_email == 1) {
            $fields3[] = $this->_('COM_VMINVOICE_MAIL') . ':  ' . $this->fields->email;
        }
        if ($this->fields->show_web_url == 1) {
        	$webUrl = $this->fields->web_url;
        	
        	if (!$webUrl AND $this->vendor->url)
        		$webUrl = $this->vendor->url;
        	
        	$fields3[] = $this->_('COM_VMINVOICE_WEB_URL') . ':  ' . $webUrl;
        }
        
        $code .= implode("<br />", $fields3) . VMI_NL . '  </td>';
        
        $code .= VMI_NL . '</tr></table>';
        
        if (!$fields1 AND !$fields2 AND !$fields3) //if no content, not display
        	return '';
        
        return $code;
    }
	
    function generateAddress($address=null, $type=null)
    {
    	$format = $this->params->get('address_format');
        $lines = array();
        
        
        // title
        if (!$address AND !$type)
        	die('Address and type not set');
        
        if ($this->params->get('address_label')) {
            if ($type == 'ST')
                $text = $this->_('COM_VMINVOICE_SHIPPING_ADDRESS');
            if ($type == 'BT')
                $text = $this->_('COM_VMINVOICE_BILLING_ADDRESS');
            $lines[] = '<strong>' . $text . ':</strong>';
        }
		if ($format=='german'){ 
			if ($address->title)
				$lines[] = $address->title;
	        $lines[] =  $address->first_name . ' ' .  ($address->middle_name ? $address->middle_name.' ': '') . $address->last_name;
	        if ($address->company)
           		$lines[] = $address->company;
		}
		else {
	        if ($address->company)
	            $lines[] = $address->company;
	        $lines[] = ($address->title ? $address->title.' ': '') . $address->first_name . ' ' .  ($address->middle_name ? $address->middle_name.' ': '') . $address->last_name;
		}
		
        if ($format=='addr-row') {
        	$lines[] = $address->address_1.($address->address_2 ? ' '.$address->address_2 : '');
        }
        else {
	        $lines[] = $address->address_1;
	        if ($address->address_2)
	            $lines[] = $address->address_2;
        }

		if ($format=='usa')
			$lines[] =  $address->city.($address->state_2_code ? ', '.$address->state_2_code : '').' '.$address->zip;
		elseif ($format=='uk') {
			$lines[] = $address->city;
			if (!empty($address->state) AND $address->state!='-')
				$lines[] = $address->state;
			$lines[] = $address->zip;
		}
		else {
	        $lines[] = $address->zip . '  ' . $address->city;
	        if ($address->state AND $address->state!='-')
	            $lines[] = $address->state;
		}
	    if ($address->country_name)
	   		$lines[] = $address->country_name;

	    // extralines 1 - 4
	    foreach (range(1,4) as $i)
		     if ($this->params->get('extra_field'.$i)) {
	            $field = InvoiceGetter::getVMExtraField($this->params->get('extra_field'.$i));
	            $label = $this->params->get('show_extra_field_label') ? InvoiceGetter::getVMTranslation($field['title']) . ': ' : '';
	            $name = $field['name'];
	            if (isset($address->$name) AND $address->$name)
	                $lines[] = $label . str_replace('|*|',', ',$address->$name);
	        }

        
        
        return VMI_NL . implode('<br />' . VMI_NL, array_map('stripslashes', $lines));
    }
	
    
    function getVendorImage ()
    {
    	$filename = InvoiceGetter::getVendorImage($this->order->vendor_id);
    	$path = JPATH_SITE.'/'.$filename;
    	$uri = $filename;
    	
        if ($filename AND $uri AND file_exists($path)) {
           
            $logoWidth = $this->params->get('logo_width','');
            
            if (!empty($logoWidth) AND is_readable($path)) {
            	list ($width, $height, $type, $attr) = getimagesize($path);
                $height = round(($logoWidth/$width) * $height);
                $width = $logoWidth;
                return '<img src="' . $uri . '" style="width:'.($width*$this->scaleCmToPDF).'pt;height:'.($height*$this->scaleCmToPDF).'pt;" width="' . ($width*$this->scaleCmToPDF) . '" height="' . ($height*$this->scaleCmToPDF) . '" />';

                //return '<img src="' . $uri . '" width="' . ($width*$this->scaleCmToPDF) . '" height="' . ($height*$this->scaleCmToPDF) . '" />';
 
            }
            else
            	return '<img src="' . $uri . '"/>';
        }
        else
            return '';
    }
    
    
	/**
	 * Only for template help. Should stay same as replaceItemRow function below.
	 */
    static function getAvailableTags()
    {
    	$ret = array();
    	
    	$ret[0]['seq']='COM_VMINVOICE_SEQ_HELP';
    	$ret[0]['seq_dot']='COM_VMINVOICE_SEQ_DOT_HELP';
    	$ret[0]['qty']='';
    	$ret[0]['qty_unit']='';
    	$ret[0]['sku']='';
        $ret[0]['item_image']='';
        $ret[0]['item_image_url']='';
    	$ret[0]['name']='';
    	$ret[0]['attributes']='';
    	$ret[0]['price']='COM_VMINVOICE_PRICE_HELP';
    	$ret[0]['price_notax']='COM_VMINVOICE_PRICE_NOTAX_HELP';
    	$ret[0]['price_withtax']='COM_VMINVOICE_PRICE_WITHTAX_HELP';
    	$ret[0]['price_discounted']='COM_VMINVOICE_PRICE_DISCOUNTED_HELP';
    	$ret[0]['tax_rate']='COM_VMINVOICE_TAX_RATE_HELP';
    	$ret[0]['tax_price']='COM_VMINVOICE_TAX_PRICE_HELP';
    	$ret[0]['tax_price_item']='COM_VMINVOICE_TAX_PRICE_ITEM_HELP';
    	$ret[0]['discount']='COM_VMINVOICE_DISCOUNT_HELP';
    	$ret[0]['discount_item']='COM_VMINVOICE_DISCOUNT_ITEM_HELP';
    	$ret[0]['subtotal']='COM_VMINVOICE_SUBTOTAL_HELP';
    	$ret[0]['subtotal_base']='COM_VMINVOICE_SUBTOTAL_BASE_HELP';
    	$ret[0]['subtotal_discounted']='COM_VMINVOICE_SUBTOTAL_DISCOUNTED_HELP';
    	$ret[0]['subtotal_item']='COM_VMINVOICE_SUBTOTAL_ITEM_HELP';
    	
    	$ret[0]['product_s_desc']='';
    	$ret[0]['product_desc']='';
    	$ret[0]['product_weight']='';
    	$ret[0]['product_weight_unit']='';
    	$ret[0]['packages']='COM_VMINVOICE_PACKAGES_ITEM_HELP';
    	$ret[0]['boxes']='COM_VMINVOICE_BOXES_ITEM_HELP';
    	
    	$ret[1]['qty_cpt']=JText::_('COM_VMINVOICE_QTY'); //captions
    	$ret[1]['sku_cpt']=JText::_('COM_VMINVOICE_SKU');
    	$ret[1]['name_cpt']=JText::_('COM_VMINVOICE_PRODUCT_NAME');
    	$ret[1]['price_cpt']=JText::_('COM_VMINVOICE_PRICE');
    	$ret[1]['base_total_cpt']=JText::_('COM_VMINVOICE_BASE_TOTAL');
    	$ret[1]['tax_rate_cpt']=JText::_('COM_VMINVOICE_TAX_RATE');
    	$ret[1]['tax_cpt']=JText::_('COM_VMINVOICE_TAX');
    	$ret[1]['discount_cpt']=JText::_('COM_VMINVOICE_DISCOUNT');
    	$ret[1]['subtotal_cpt']=JText::_('COM_VMINVOICE_SUBTOTAL');
    	
    	
    	
    	
    	return $ret;
    }
    
    function mapTags ( $match ) {
        
        return strtolower(trim($match,' {}'));
        
    }
    
    //one function to remove tags from whole string (because it is used also on shipping and payment in footer)
    function removeNonReplacedTags($string)
    {
    	static $not_replace;
    	
    	if (!isset($not_replace)){
	    	if( $this->params->get('invoice_not_replace') ) {
	    		$not_replace = explode( ',', $this->params->get('invoice_not_replace') );
	    		$not_replace = array_map( array($this, 'mapTags'), $not_replace );
	    	}
	    	else
	    		$not_replace = false;
    	}
    	
    	if ($not_replace)
    		foreach ($not_replace as $tag)
    			$string = preg_replace('#\{\s*'.preg_quote($tag).'(\W[^}]*)?\s*\}#is', '', $string); //for future: can be {tag..parameters}
    	
    	return $string;
    }
    
    function replaceItemRow( $match ) {

    	$item = $this->currentItem;    	
    	$replacement = false;
    	$tag = strtolower(trim($match[0],' {}'));
    	
    	//trigger event. if some plugin returns string, it is used as replacement and no other replacement is done
        $results = $this->dispatcher->trigger('onTagItemBeforeReplace', array(&$tag, &$item, &$this, $this->params));
    	foreach ($results as $result)
    		if (is_string($result))
    			$replacement = $result;
    	
    	if ($replacement===false)
	   	switch ($tag) {
	   		
	   		case 'seq':
	   			$replacement = $this->seqNo;
	   			break;
	   			
	   		case 'seq_dot':
	   			$replacement = $this->seqNo.'.';
	   			break;
	   				
	    	
	    	case 'qty':
	    		$replacement = $item->product_quantity  * 1;
	    		break;
	    		
	    	case 'qty_unit':
	    		$replacement = ($this->deliveryNote AND $this->params->get('show_quantity_unit_dn') OR !$this->deliveryNote AND $this->params->get('show_quantity_unit')) ? ' '.$this->_('COM_VMINVOICE_PCS') : '';
	    		break;
	        
	    	case 'sku':
	    		if (($this->params->get('show_sku') AND ! $this->deliveryNote) OR ($this->params->get('show_sku_dn') AND $this->deliveryNote))
                	$replacement = $item->order_item_sku;
                else
	    			$replacement = '-remove-';
	        	break;
	        
            case 'item_image':    
				if ($this->params->get('show_product_image_in_invoice') AND $item->item_image) {  
                    $width = (int)$this->params->get('invoice_image_width');
                    $height = (int)$this->params->get('invoice_image_height');    
                	$replacement = '<img src="'.$item->item_image . '" style="width:'.$width.'px;height:'.$height.'px;" width="' . $width. '" height="' .$height. '" />';
                }
                break;     

            case 'item_image_url':
            	$replacement = $item->item_image;
            	break;
                        
	    	case 'name':
	    		$replacement = $item->order_item_name;
	        	break;
	        	
	    	case 'attributes':
	    		if ($this->params->get('show_attributes')==1){
	    			
	    			$decoded = json_decode($item->product_attribute, true);
	    			$itemTemp = false; //populated by copy of $item suitable to pass to VM to get attributes HTML directly from it
	    			
	    			//try to use directly VM2 plugins for display
	    			if (COM_VMINVOICE_ISVM2 AND in_array($this->params->get('attributes_use_vm', 'FE'), array('FE','BE'))){
	    				InvoiceHelper::importVMFile('models/customfields.php', true);
	    				if (class_exists('VirtueMartModelCustomfields')){
	    					
	    					//load class for Donation plugin.. 
	    					//thing is we cannot know what plugins wants to load because they usualy operates inside VM
	    					//maybe we can somehow scan plugins source we will use and guess which VM classes they need... 
	    					InvoiceHelper::importVMFile('helpers/currencydisplay.php');
	    					
	    					$itemTemp = clone $item; //for all cases...
	    					if (!empty($item->virtuemart_product_id)){
	    						$product = InvoiceGetter::getProduct($item->virtuemart_product_id, $this->language);
	    						//TODO: aby objekt byl uplne stejny jako se pouziva ve vm ... intnotes,, categorie navazat... eeejjgh. to by slo delat leta.
	    						if ($product)
	    							$itemTemp = (object)array_merge((array)$product, (array)$itemTemp);
	    					}
	    					InvoiceGetter::getVMTranslation(''); //just to load all VM language files
	    				}
	    			}
	    			
	    			if ($decoded===null) //string attributes (VM1 style or written into textarea). no nl2br, because can be already with brs(?)
	    				$replacement =  preg_replace('#(\s*<\s*p\s[^>]*>\s*|\s*<\s*\/\s*p\s*>\s*|<\s*br\s*\/?\s*>\s*$|^\s*<\s*br\s*\/?\s*>)#i','',$item->product_attribute); //remove all <p>s and enclosing <br>s
	    			else{ //JSON atrributes (VM2)

	    				if ($itemTemp){ //if we will use VM plugins to display attributes, we need to switch to inv. language, to translate properly
	    					
	    					$lang = JFactory::getLanguage();
	    					$origLang = $lang->get('tag');
	    					
	    					if ($origLang!=$this->language){
	    						$lang->setLanguage($this->language);
	    						$lang->load('com_virtuemart', JPATH_SITE, $this->language, true);
	    						$lang->load('com_virtuemart_orders', JPATH_SITE, $this->language, true);
	    						$lang->load('com_virtuemart_shoppers', JPATH_SITE, $this->language, true);
	    						$lang->load('com_virtuemart', JPATH_ADMINISTRATOR, $this->language, true);
	    						//reload? i dont know. and i dont care.
	    					}
	    				}
	    				
	    				$attributes = array();
	    				$db = JFactory::getDBO();
	    				
	    				foreach ($decoded as $key => $val){
	    					
	    					//ok, NYNI to zkusime prohnat pres VM pluginy. jen pokud to jde a pokud to uzivatel povoli (?)
	    					if ($itemTemp AND is_numeric($key) AND $key>0){ //we CAN try to call VM plugins. key must be int>0, else it will silently return nothing.
	    							    						
	    						$itemTemp->param = array($key => $val); //simuate only one parameter. let them think this is only one.
	    						$attributeshtml = VirtueMartModelCustomfields::customFieldDisplay($itemTemp, $itemTemp->param, '<div>', 'plgVmDisplayInOrder' . $this->params->get('attributes_use_vm', 'FE'));
	    						
	    						//remove enclosing <div> and <br>s. dont do mess here!
	    						$attributeshtml = preg_replace('#(^<div>|<\/div>$)#i', '', trim($attributeshtml));
	    						$attributeshtml = trim(preg_replace('#(^(\s*<\s*br\s*\/?\s*>\s*)+|(\s*<\s*br\s*\/?\s*>\s*)+$)#i', '', trim($attributeshtml)));
	    						if (preg_match('#^<span class="product-field-type-."><\/span>$#isU', $attributeshtml)) //empty attribute
	    							$attributeshtml ='';
	    						
	    						//remove emtpy <div>s? and <spam>s?
	    						
	    						if ($attributeshtml){ //we have some output from VM plugin functions - add it. 
	    							
	    							//NOW we must add colon after title, because VM puts only space there (defaultly)
	    							//we can do it surely only for non-plugin attribites and simple plugin ones
	    							//else keep output as is (we dont know what plugins displays)
	    							//@see VirtueMartModelCustomfields::customFieldDisplay
	    							if ($productCustom = VirtueMartModelCustomfields::getProductCustomField($key)){
	    							
	    								if ($productCustom->custom_title AND (($productCustom->field_type != "E" AND $productCustom->show_title) OR $productCustom->field_type == "E")) {
	    									$spanStart = '<span class="product-field-type-' . $productCustom->field_type . '">'; //@see function
	    									$translated = JText::_($productCustom->custom_title);

	    									//hm, ale treba u Custom Field Dropbox to muze delat neplechu...
	    									//IF it is like <span something>Title Value ...
	    									//OR possibly <span something><span possibly something>Title Value ...
	    									if (preg_match('#^('.preg_quote($spanStart, '#').'\s*(?:<span[^>]*>)?\s*)'.preg_quote($translated, '#').' (.+)$#i', $attributeshtml, $matches))
	    										$attributeshtml = $matches[1].$translated.': '.$matches[2];
	    								}
	    							}

	    							$attributes[] = $attributeshtml;
	    						}
	    						
	    						//if we dont have output, it (probably!) means output should be seen here (e.g. file upload element)
	    						//and ... look on VM versions what they do there

	    						continue;
	    					}
	    					
	    					//ok, no native VM plugins, parse our own way
	    					if (is_array($val) OR is_object($val)){
	    						
	    						foreach ((array)$val as $key2 => $val2){

	    							if (is_array($val2) OR is_object($val2)){ //another nested object :)
	    								foreach ((array)$val2 as $key3 => $val3){ 

	    									$title = false;
	    									
	    									//support for VMCustom - textinput plugin
	    									//{"48":{"textinput":{"comment":"10"}}}
	    									if (COM_VMINVOICE_ISVM2){ 
	    										$db->setQuery('SELECT C.custom_title FROM #__virtuemart_product_customfields AS CF
		    									JOIN #__virtuemart_customs AS C ON (CF.virtuemart_custom_id = C.virtuemart_custom_id)
		    									WHERE CF.custom_value = '.$db->Quote($key2).' AND CF.virtuemart_customfield_id = '.(int)$key);
	    										$title = $db->loadResult();
	    									}

	    									//support for Custom Field Dropbox Plugin (or more) - replace title from custom field title
	    									//{"49":{"drop":{"custom_drop":"B"}}
	    									if (!$title AND COM_VMINVOICE_ISVM2 AND !is_numeric($key3)){
	    										$db->setQuery('SELECT custom_param FROM #__virtuemart_product_customfields WHERE custom_value = '.$db->Quote($key2).' AND virtuemart_customfield_id = '.(int)$key);
	    										
	    										if (($param = $db->loadResult()) AND ($params = json_decode($param, true)))
	    											foreach ($params as $key4 => $val4)
	    												if (substr($key4, -5)=='_name' AND is_string($val4))
	    													$title = $val4;
	    									}
	    									
                                            // Support for Custom Fields For All plugin
                                            if (COM_VMINVOICE_ISVM2 AND $key2 == 'customfieldsforall' AND is_numeric($val3)) {
                                                $query = "SELECT `customsforall_value_name`"
                                                        ." FROM `#__virtuemart_custom_plg_customsforall_values`"
                                                        ." WHERE `customsforall_value_id` = ".(int)$val3;
                                                $db->setQuery($query);
                                                $val4 = $db->loadResult();
                                                if ($val4) {
                                                    $val3 = $val4;
                                                }
                                            }
                                            
	    									if (!$title)
	    										$title = $key3;
	    										
	    									if ($title AND !empty($val3))
												$attributes[] = $this->_($title, 'com_virtuemart').': '.$this->_($val3, 'com_virtuemart');
	    								}
	    							}
	    							elseif (!empty($key2) AND !empty($val2)){

	    								$title = false;
	    								
	    								if (COM_VMINVOICE_ISVM2){ //try to find custom field title 
		    								$db->setQuery('SELECT C.custom_title FROM #__virtuemart_product_customfields AS CF
		    									JOIN #__virtuemart_customs AS C ON (CF.virtuemart_custom_id = C.virtuemart_custom_id)
		    									WHERE CF.custom_value = '.$db->Quote($key2).' AND CF.virtuemart_customfield_id = '.(int)$key);
		    								$title = $db->loadResult();
	    								}

										$attributes[] = $this->_($title ? $title : $key2, 'com_virtuemart').': '.$this->_($val2, 'com_virtuemart');
	    							}
	    						}
	    					}
	    					else{
	    						//usual way..
		    					if (preg_match('#^\s*<\s*span.*>(.*)<\s*\\/\s*span\s*>\s*<\s*span.*>(.*)<\s*\\/\s*span\s*>\s*$#iU', (string)$val, $matches)) { //two spans
		    					//if (preg_match('#^\s*<\s*span\s+class\s*=\s*\\"costumTitle\\"\s*>(.*)<\s*\\?\s*/\s*span\s*>\s*<\s*span\s+class\s*=\s*\\"costumValue\\"\s*>(.*)<\s*\\?\s*/\s*span\s*>\s*$#iU', $val, $matches))
		    						// Check the field type
                                    $val = $matches[2];
                                    if (COM_VMINVOICE_ISVM2 && is_numeric($val)) {
                                        $query = "SELECT C.field_type FROM #__virtuemart_product_customfields AS CF
		    									JOIN #__virtuemart_customs AS C ON (CF.virtuemart_custom_id = C.virtuemart_custom_id)
		    									WHERE CF.virtuemart_customfield_id = ".(int)$key;
                                        $db->setQuery($query);
                                        $fieldType = $db->loadResult();
                                        
                                        // Handle Image field type
                                        if ($fieldType == 'M') { // Image
                                            $query = "SELECT file_title FROM #__virtuemart_medias WHERE virtuemart_media_id = ".(int)$val;
                                            $db->setQuery($query);
                                            $fileTitle = $db->loadResult();
                                            if ($fileTitle) {
                                                $val = $fileTitle;
                                            }
                                        }
                                    }
                                    
		    						$attributes[] =  $this->_($matches[1], 'com_virtuemart').': '. $this->_($val, 'com_virtuemart');
                                }
		    					elseif (($val = trim((string)$val)) AND !preg_match('#^\d+$#',$val)){ //http://www.artio.net/support-forums/vm-invoice/customer-support/custom-plugin-attribute-title ?
		    						$attributes[] = $this->_((string)$val, 'com_virtuemart');} //only when not empty and not integer
	    					}
	    				}
	    				
	    				$replacement = implode("<br />\r\n", $attributes);

	    				//if we used VM plugings, switch back to current language
	    				if ($itemTemp AND $origLang!=$this->language){ //reload back. yeah.
	    					$lang->setLanguage($origLang);
	    					$lang->load('com_virtuemart', JPATH_SITE, $origLang, true);
	    					$lang->load('com_virtuemart_orders', JPATH_SITE, $origLang, true);
	    					$lang->load('com_virtuemart_shoppers', JPATH_SITE, $origLang, true);
	    					$lang->load('com_virtuemart', JPATH_ADMINISTRATOR, $origLang, true);
	    					//reload? i dont know. and i dont care.
	    				}
	    			}
	    		}
	    		else 
	    			$replacement = '-remove-';
	        	break;
	        
	    	case 'price': //1 item price without tax
	    		//display as vm
	    		if ($this->params->get('show_price_discounts') AND !empty($item->product_priceWithoutTax) AND $item->product_discountedPriceWithoutTax*1 != $item->product_priceWithoutTax*1) {
	    			$replacement =  '<span style="text-decoration:line-through">'.$this->currencyDisplay->getValue($item->product_item_price, $this->currency, $this->showCurrency) .'</span><br />';
	    			$replacement .= $this->currencyDisplay->getValue($item->product_discountedPriceWithoutTax, $this->currency, $this->showCurrency);
	    		} else
	    			$replacement =  $this->currencyDisplay->getValue($item->product_item_price, $this->currency, $this->showCurrency) ;
	        	break;
	      
				// price_notax is older version for subtotal_base, it should be not removed because of compatibility with old settings  	
	    	case 'subtotal_base': // new format (item_price * qty)  
	    	case 'price_notax': //quantity * price without tax
	    		if ($this->params->get('show_price_notax')){
	    			//display as vm
	    			if ($this->params->get('show_price_discounts')	AND !empty($item->product_priceWithoutTax) AND $item->product_discountedPriceWithoutTax*1 != $item->product_priceWithoutTax*1) { 
	    				$replacement =  '<span style="text-decoration:line-through">'.$this->currencyDisplay->getValue($item->item_price_notax, $this->currency, $this->showCurrency) .'</span><br />';
	    				$replacement .= $this->currencyDisplay->getValue($item->product_discountedPriceWithoutTax*$item->product_quantity, $this->currency, $this->showCurrency);
	    			} else
	    				$replacement =  $this->currencyDisplay->getValue($item->item_price_notax, $this->currency, $this->showCurrency);
	    		}else 
	    			$replacement = '-remove-';
	        	break;

	    	case 'price_discounted':
	        	$replacement = $this->currencyDisplay->getValue($item->product_item_price + ($item->discount/$item->product_quantity), $this->currency, $this->showCurrency);
	        	break;
	        
	    	case 'price_withtax': //1 item with tax
				$replacement = $this->currencyDisplay->getValue($item->product_item_price+($item->item_tax_amount/$item->product_quantity), $this->currency, $this->showCurrency);
	        	break;
	        	
	    	case 'tax_rate':
	    		if ($this->params->get('show_tax_rate'))
	    			$replacement =  $item->tax_item.'%';
	    		else 
	    			$replacement = '-remove-';
	        	break;

	    	case 'tax_price':
	    		if ($this->params->get('show_tax_price'))
	    			$replacement =  $this->currencyDisplay->getValue($item->item_tax_amount, $this->currency, $this->showCurrency);
	    		else 
	    			$replacement = '-remove-';
	        	break;
	        	
	    	case 'tax_price_item':
	    			$replacement =  $this->currencyDisplay->getValue($item->item_tax_amount/$item->product_quantity, $this->currency, $this->showCurrency);
	        	break;
	        	
	    	case 'discount':
	    		if (COM_VMINVOICE_ISVM2 AND (($this->params->get('show_discount')==2 AND $this->isItemsDiscount) OR $this->params->get('show_discount')==1))
	    			$replacement =  $this->currencyDisplay->getValue($item->discount, $this->currency, $this->showCurrency);
	    		else
	    			$replacement = '-remove-';
	        	break;
	        	
	    	case 'discount_item':
	    		if (COM_VMINVOICE_ISVM2 AND $item->discount)
	    			$replacement =  $this->currencyDisplay->getValue($item->discount/$item->product_quantity, $this->currency, $this->showCurrency);
	        	break;
	      	
            case 'discount_notax':
	    		if (COM_VMINVOICE_ISVM2 AND (($this->params->get('show_discount')==2 AND $this->isItemsDiscount) OR $this->params->get('show_discount')==1))
	    			$replacement =  $this->currencyDisplay->getValue($item->discount * 100 / (100 + $item->tax_item), $this->currency, $this->showCurrency);
	    		else
	    			$replacement = '-remove-';
	        	break;
	        	
	    	case 'discount_item_notax':
	    		if (COM_VMINVOICE_ISVM2 AND $item->discount)
	    			$replacement =  $this->currencyDisplay->getValue(($item->discount * 100 / (100 + $item->tax_item)) / $item->product_quantity, $this->currency, $this->showCurrency);
	        	break;
	      	
	    	case 'subtotal_item':
	    	case 'subtotal':

	    		$replacement = '';
	    		//display as vm
	    		if (!empty($item->product_priceWithoutTax) AND $this->params->get('show_price_discounts') AND $item->product_discountedPriceWithoutTax*1 != $item->product_priceWithoutTax*1){  
	    			if ($tag=='subtotal_item')
	    				$replacement =  '<span style="text-decoration:line-through">'.$this->currencyDisplay->getValue($item->product_basePriceWithTax, $this->currency, $this->showCurrency) .'</span><br />';
	    			else
	    				$replacement =  '<span style="text-decoration:line-through">'.$this->currencyDisplay->getValue($item->product_basePriceWithTax*$item->product_quantity, $this->currency) .'</span><br />';
	    		}
	    		
	    		if (COM_VMINVOICE_ISVM2 AND !$this->params->get('item_subtotal_with_discount', 1)) //no discount!
	    			$subtotal = $item->subtotal - $item->discount;
	    		else
	    			$subtotal = $item->subtotal;
	    		
	    		if ($tag=='subtotal_item')
	    			$replacement .= $this->currencyDisplay->getValue($subtotal/$item->product_quantity, $this->currency, $this->showCurrency);
	    		else
	    			$replacement .= $this->currencyDisplay->getValue($subtotal, $this->currency);
	        break;
	      
	       	case 'subtotal_discounted':
	    		$replacement = $this->currencyDisplay->getValue($item->subtotal - $item->item_tax_amount, $this->currency, $this->showCurrency);
	    		break;
	
	    	case 'product_s_desc':
	    		$replacement =  preg_replace('#(\s*<\s*p\s*[^>]*>\s*|\s*<\s*\/\s*p\s*>\s*|<\s*br\s*\/?\s*>\s*$|^\s*<\s*br\s*\/?\s*>)#i','',$item->product_s_desc); //remove all <p>s and enclosing <br>s
	        	break;
	    	case 'product_desc':
	    		$replacement =  preg_replace('#(\s*<\s*p\s*[^>]*>\s*|\s*<\s*\/\s*p\s*>\s*|<\s*br\s*\/?\s*>\s*$|^\s*<\s*br\s*\/?\s*>)#i','',$item->product_desc); //remove all <p>s and enclosing <br>s
	        	break;
	    	case 'product_weight':
	    		$replacement = $item->product_weight ? $item->product_weight*1 : '';
	        	break;
	    	case 'product_weight_unit':
	    		$replacement =  $item->product_weight ? $item->product_weight_uom : '';
	        	break;
	        	    	
	        case 'packages':
	        	$product_packaging = COM_VMINVOICE_ISVM1 ? ($item->product_packaging & 0xFFFF) : $item->product_packaging;
                if (isset($product_packaging) AND is_numeric($product_packaging) AND $product_packaging*1>0)
                    $replacement = ceil($item->product_quantity / $product_packaging);
                else
                    $replacement = '';
                break;
	        	
            case 'boxes':
            	$box = 0;
            	if (COM_VMINVOICE_ISVM2){
	            	foreach (explode('|', $item->product_params) as $prodParam){
	            		$prodParam = explode('=', $prodParam, 2);
	            		if ($prodParam[0]=='product_box')
	            			$box = trim($prodParam[1], ' "');
	            	}
            	}
            	else //vm1
            		$box = ($item->product_packaging>>16)&0xFFFF;

               	if ($box && is_numeric($box))
               		$replacement = ceil($item->product_quantity / $box);
               	else
               		$replacement = '';
               	break;
                	
	        
	        	
	        
	   	}
	   	
	   	$this->dispatcher->trigger('onTagItemAfterReplace', array(&$tag, &$replacement, &$item, &$this, $this->params));
	   	
    	return $replacement;
    }
        
    
    
    /**
     * Loads items and computes tax summary. Also loads shipping and its tax rates.
     */
    function loadItems()
    {
    	$this->items_count = 0;
    	$this->items_sum = 0;
    	
    	
    	$decimals = InvoiceCurrencyDisplay::getDecimals($this->currency);
    	$sumRounded = ($this->params->get('tax_sums_rounded',1) AND ($decimals!==false)); //whether calculate tax summary with already rounded values (default yes)
    	
        // load items
        $this->items = InvoiceGetter::getOrderItems($this->order->order_id, null, $this->params->get('items_ordering'), $this->language);
        $this->subtotal_net = 0;
        $this->subtotal_tax = 0;
        $this->subtotal_gross = 0;
        $this->isItemsDiscount = false;
        $this->subtotal_discount = 0;
        $this->total_weight = InvoiceGetter::getOrderWeight($this->items);
        
        foreach ($this->items as &$orderItem) {
        	
        	//if set in config and translation is available, replace product name by current one. 
        	if ($this->params->get('translate_product_name',0) AND $orderItem->product_name)
        		$orderItem->order_item_name = $orderItem->product_name;
        	
        	$q = $orderItem->product_quantity;
        	
        	$this->items_count++;
        	$this->items_sum+= $q;
        	
        	//get calc rules for item. we will need it for guessing tax rate that was used
        	if (COM_VMINVOICE_ISVM2)
        		$orderItem->calcRules = InvoiceGetter::getOrderCalcRules($this->order->order_id, $orderItem->virtuemart_order_item_id);
        	
        	// calculates for footer and replace fnc
			$guessedRate = InvoiceHelper::guessTaxRate($orderItem->product_price_with_tax,$orderItem->product_item_price, COM_VMINVOICE_ISVM2 ? $orderItem->calcRules : array());
			
			$orderItem->tax_item = round($guessedRate * 100,2)*1;
			$orderItem->item_price_notax = $q * $orderItem->product_item_price;
			if (COM_VMINVOICE_ISVM2){ //vm2
				$orderItem->item_tax_amount = $orderItem->product_tax*$q;
				$orderItem->item_price_tax = $orderItem->product_price_with_tax * $q;
				$orderItem->subtotal = $orderItem->product_subtotal_with_tax*$q; //should be also with discount
				$itemTaxValue = $orderItem->product_price_with_tax - $orderItem->product_item_price;
				$itemTaxPercent = $orderItem->product_item_price ? ($itemTaxValue / ($orderItem->product_item_price / 100)) : 0;
                // 16.9.2013 dajo: don't subtract the tax from discount - sometimes the discount is applied before tax,
                //                 sometimes after tax
				//$orderItem->discount = (($orderItem->product_price_discount / (100 + $itemTaxPercent)) * 100) * $q;
                $orderItem->discount = $orderItem->product_price_discount * $q;
				if ($orderItem->discount)
					$this->isItemsDiscount = true;
			}
			else{ //vm1
				if ($this->params->get('product_price_calculation','vm')=='tax'){ //compute from base x tax
					$orderItem->item_tax_amount = $orderItem->item_price_notax * $guessedRate;
					$orderItem->item_price_tax = $orderItem->item_price_notax + $orderItem->item_tax_amount;}
				else { //take from VM
					$orderItem->item_tax_amount = $q * ($orderItem->product_price_with_tax - $orderItem->product_item_price);
					$orderItem->item_price_tax = $q * $orderItem->product_price_with_tax;}
				$orderItem->subtotal = $orderItem->item_price_tax;
				$orderItem->discount = null;
			}
			if (! isset($this->taxSum[(string) $orderItem->tax_item])) {
			    $this->taxSum[(string) $orderItem->tax_item]['notax'] = 0;
			    $this->taxSum[(string) $orderItem->tax_item]['taxa'] = 0;
			    $this->taxSum[(string) $orderItem->tax_item]['total'] = 0;
			}
			
			$this->subtotal_discount+=$orderItem->discount;
			
			$this->taxSum[(string) $orderItem->tax_item]['notax'] += $sumRounded ? round($orderItem->item_price_notax, $decimals) : $orderItem->item_price_notax;
			$this->taxSum[(string) $orderItem->tax_item]['taxa'] +=  $sumRounded ? round($orderItem->item_tax_amount, $decimals) : $orderItem->item_tax_amount;
			
			
			$priceTax = $sumRounded ? round($orderItem->item_price_tax, $decimals) : $orderItem->item_price_tax;
			if (COM_VMINVOICE_ISVM2 AND $this->params->get('take_discount_into_summary',0)==1) //deduct discount from at summary GROSS
				$priceTax += $sumRounded ? round($orderItem->discount, $decimals) : $orderItem->discount;
				
			$this->taxSum[(string) $orderItem->tax_item]['total'] += $priceTax;
			
	        $this->subtotal_net += $orderItem->item_price_notax;
	        $this->subtotal_tax += $orderItem->item_tax_amount;
            // 16.9.2013 dajo: gross order subtotal should be just sum of lines subtotals
			//$this->subtotal_gross += $orderItem->item_price_tax;
            $this->subtotal_gross += $orderItem->subtotal;
        }
 
        //compute shipping
		if ($this->order->order_shipping OR $this->order->order_shipping_tax){
						
			// tax rate (guessed)
			$this->shipTaxRate=  round(InvoiceHelper::guessTaxRate($this->order->order_shipping+$this->order->order_shipping_tax,$this->order->order_shipping, COM_VMINVOICE_ISVM2 ? $this->order->shipCalcRules : array()) * 100,2)*1;
				
			//add shipping to tax summary
	        if (! isset($this->taxSum[(string) $this->shipTaxRate])) {
	            $this->taxSum[(string) $this->shipTaxRate]['notax'] = 0;
	            $this->taxSum[(string) $this->shipTaxRate]['taxa'] = 0;
	            $this->taxSum[(string) $this->shipTaxRate]['total'] = 0;
	        }
	        $this->taxSum[(string) $this->shipTaxRate]['notax'] += $sumRounded ? round($this->order->order_shipping, $decimals) : $this->order->order_shipping;
	        $this->taxSum[(string) $this->shipTaxRate]['taxa'] += $sumRounded ? round($this->order->order_shipping_tax, $decimals) : $this->order->order_shipping_tax;
	        $this->taxSum[(string) $this->shipTaxRate]['total'] += $sumRounded ? round($this->order->order_shipping + $this->order->order_shipping_tax, $decimals) : ($this->order->order_shipping + $this->order->order_shipping_tax);
	        
	        $this->subtotal_net += $this->order->order_shipping;
	        $this->subtotal_tax += $this->order->order_shipping_tax;
	        $this->subtotal_gross += $this->order->order_shipping + $this->order->order_shipping_tax;
		}
		
        //compute payment
		if (($this->order->order_payment!=0 OR $this->order->order_payment_tax!=0) AND 
			(COM_VMINVOICE_ISVM2 OR (COM_VMINVOICE_ISVM1 AND $this->params->get('show_payment_row')!=2))){ //in vm1 only if selected to show in separate rpw

			//add payment to tax summary
	        if (! isset($this->taxSum[(string) $this->paymentTaxRate])) {
	            $this->taxSum[(string) $this->paymentTaxRate]['notax'] = 0;
	            $this->taxSum[(string) $this->paymentTaxRate]['taxa'] = 0;
	            $this->taxSum[(string) $this->paymentTaxRate]['total'] = 0;
	        }
	        $this->taxSum[(string) $this->paymentTaxRate]['notax'] += $sumRounded ? round($this->order->order_payment, $decimals) : $this->order->order_payment;
	        $this->taxSum[(string) $this->paymentTaxRate]['taxa'] += $sumRounded ? round($this->order->order_payment_tax, $decimals) : $this->order->order_payment_tax;
	        $this->taxSum[(string) $this->paymentTaxRate]['total'] += $sumRounded ? round($this->order->order_payment + $this->order->order_payment_tax, $decimals) : ($this->order->order_payment + $this->order->order_payment_tax);
	        
	        $this->subtotal_net += $this->order->order_payment;
	        $this->subtotal_tax += $this->order->order_payment_tax;
	        $this->subtotal_gross += $this->order->order_payment + $this->order->order_payment_tax;
		}

        //unset empty tax summary (like free shipping)
        foreach ($this->taxSum as $taxRate => $taxSum)
			if (!$taxSum['notax'] AND !$taxSum['taxa'] AND !$taxSum['total']){
		       unset($this->taxSum[$taxRate]);
		       continue;}
		       
	    if (false) { //take from vm databse (but prices can be misfitting http://www.artio.net/cz/support-forums/vm-invoice/customer-support/some-wrong-shown-things-tax-title-country)

	        $this->subtotal_net = $this->order->order_subtotal;
	        $this->subtotal_tax = $this->order->order_tax;
	        	
	        //add shipping value to subtotal
	        $this->subtotal_net += $this->order->order_shipping;
		    $this->subtotal_tax += $this->order->order_shipping_tax;
		    if (!(COM_VMINVOICE_ISVM1 AND $this->params->get('show_payment_row')==2)) { //add payment value to subtotals
		       $this->subtotal_net += $this->order->order_payment;
		       $this->subtotal_tax += $this->order->order_payment_tax;
		    }
	    }
	    
	    //http://www.artio.net/cz/support-forums/vm-invoice/customer-support/total-mismatch
	    //now this is tricky: when order total misfits calculated order (sub)total
	    //change it to original order (sub)total (total minus discounts), else prices on invoice wont fit
	    //if showing tax summary, it must have only one tax rate! (because else we canot know what subtotal is misfitting)
		//only in VM1, becaue VM2 stores prices with higher precision, so they shouldnt misfit
	    if (COM_VMINVOICE_ISVM1 AND (count($this->taxSum)==1 OR !$this->params->get('show_tax_summary'))){

	    	$computedSubTotalGrossRounded = $this->currencyDisplay->getValue($this->subtotal_gross, $this->currency, false);
	    	$orderSubtotalGross = $this->order->order_total - $this->order->coupon_discount;
	    	
	    	//now this is very sophisticated.
	    	if ($this->params->get('show_payment_row')==2) //add (decuduct) order disocunt only if not shpwing payment row above
	    		$orderSubtotalGross += $this->order->order_discount; //+!!!!!!!!!!!!!!
	    	
	    	$orderSubtotalGrossRounded = $this->currencyDisplay->getValue($orderSubtotalGross, $this->currency, false);

	    	if ($orderSubtotalGrossRounded!=$computedSubTotalGrossRounded){ //computed subtotal and order subtotal misfits
	    		$this->subtotal_gross = $orderSubtotalGross;
	    		if (count($this->taxSum)==1){
	    			reset($this->taxSum);
	    			$this->taxSum[key($this->taxSum)]['total'] = $sumRounded ? round($orderSubtotalGross, $decimals) : $orderSubtotalGross;
	    		}
	    	}
	    }
	    
        // 16.9.2013 dajo: the discount is already included in the subtotal_gross
	    //if (COM_VMINVOICE_ISVM2 AND $this->params->get('take_discount_into_summary',0)==1) //deduct discount from 
	    	//$this->subtotal_gross += $this->order->order_discount;
	    
		
		
    }
    
    function generateItems()
    {
    	$code = '';
    	
    	//get items template
	    $dn = $this->deliveryNote ? 'dn_' : '';
	    $db = JFactory::getDBO();
	    $db->setQuery('SELECT `template_'.strtolower($dn).'items` FROM `#__vminvoice_config`');
	    if (count($template = explode('_TEMPLATE_ITEMS_SEPARATOR_',$db->loadResult()))!=2) 
	    	die('Not defined items block template for '.(!$this->deliveryNote ? 'invoice':'delivery note'));

        if (!is_numeric(InvoiceHelper::getNumCols($template[0])))
        	die('Not proper header template for items block ');
	
        // Load custom tax summary templates
        $template[2] = $template[3] = '';
        if (!$this->deliveryNote) {
            $db->setQuery("SELECT `template_tax_header`, `template_tax_row` FROM `#__vminvoice_config`");
            $tmps = $db->loadObject();
            if ($tmps) {
                $template[2] = $tmps->template_tax_header;
                $template[3] = $tmps->template_tax_row;
            }
        }
        
        foreach ($template as &$templatePart)	//remove table tags to get only row
        	$templatePart = preg_replace('#<\s*\/?\s*(table|tbody|thead)[^>]*>#is','',$templatePart);
        	
        //non-empty first cell - add 1% width cell on start with empty <span> inside. it is because TCPDF bug putting content on next page. (http://sourceforge.net/projects/tcpdf/forums/forum/435311/topic/5096409)
        $columnsItem = InvoiceHelper::getColumns($template[1]);
        if (trim(strip_tags($columnsItem[0][2]))!='') {
        	$td = '<td width="1%"><span></span></td>';
        	$template[0] = InvoiceHelper::addColumn($template[0],0, $td);
        	$template[1] = InvoiceHelper::addColumn($template[1],0, $td);
            
            if (!empty($template[2]))
                $template[2] = InvoiceHelper::addColumn($template[2],0, $td);
            if (!empty($template[3]))
                $template[3] = InvoiceHelper::addColumn($template[3],0, $td);
        }
        	
        // generate item lines
        $itemLines = array();
        $i = 0;
        
        
        
        
        $lineTemplateForItem = $this->removeNonReplacedTags($template[1]); //remove non-replaced tags from line-template
       
        $colsToDelete = null;
        
        foreach ($this->items as $orderItem) {

        	$this->seqNo = ++$i;
			$this->currentItem = $orderItem; //to pass item obj to function

			$itemLine = preg_replace_callback("#\{[\w ]+\}#is",array( &$this, 'replaceItemRow'),$lineTemplateForItem); //replace tags

			if (!isset($colsToDelete)){ //determine which columns will be removed
				$colsToDelete = array();
				$columns = InvoiceHelper::getColumns($itemLine);

				foreach ($columns as $key => $column){

					$colspan = 1; 
					if (preg_match('#colspan\s*=\s*["\']?\s*(\d+)#i',$column[1],$tdColspan))
						$colspan = $tdColspan[1];
						
					if (preg_match('#^(\s*-remove-\s*)+$#is',strip_tags($column[2]))){ //if all content of column was removed..., remove column 
						for ($j=0;$j<$colspan;$j++) //if column has rowspan, mark also "non existing" columns
							$colsToDelete[]=$key+$j;
					}
				}
			}
			
			$itemLine = InvoiceHelper::removeColumns($itemLine,$colsToDelete);
			$itemLine = str_replace('-remove-','',$itemLine); //remove "to-remove" marks
			$itemLines[] = $itemLine;
        }
        
	    $templateLine = InvoiceHelper::removeColumns($template[1],$colsToDelete);
	    $colsNo = InvoiceHelper::getNumCols($templateLine);
	        
        // Different currencies block
        $currencyLines = array();
        
        
        //VM1 pocitani = subtotal + tax  + shipment + shipment tax + payment + payment tax - coupon discount - order discount (jeste se jinak jmenujou)
        //VM2 pocitani = subtotal + tax  + shipment + shipment tax + payment + payment tax + coupon discount + order discount
        //VM2 order_subtotal = souscet cen bez dane a modifikatoru.
        
	    //determine last "subtotal" tag, sometimes there can be subtotal, sometimes subtotal_item
        // 25.11.2013 dajo: and sometimes only the {subtotal_discounted} is present
        if (strpos($templateLine, '{subtotal}') !== false) {
            $subtotalTag = '{subtotal}';
            $subtotalTag2 = '{subtotal}';
        }
        else if (strpos($templateLine, '{subtotal_item}') !== false) {
            $subtotalTag = '{subtotal_item}';
            $subtotalTag2 = '{subtotal_item}';
        }
        else if (strpos($templateLine, '{subtotal_discounted}') !== false) {
            $subtotalTag = '{subtotal_discounted}';
            $subtotalTag2 = (strpos($templateLine, '{subtotal}') !== false) ? '{subtotal}' : '{subtotal_discounted}'; //?
        }
        else {
            $subtotalTag = '{subtotal}';
            $subtotalTag2 = '{subtotal}';
        }

        if (!$this->deliveryNote)
        {	
	        //generate line for shipping
        	if ($this->params->get('show_shipping_row')==0 OR ($this->params->get('show_shipping_row')==1 AND $this->order->order_shipping>0)) {
        	
		        $shippingLine = $this->removeNonReplacedTags($template[1]); //remove also non-replaced tags. treat as product line.
		        $shippingLine = InvoiceHelper::removeColumns($shippingLine,$colsToDelete);
			    $shippingLine = str_replace('{name}',$this->_('COM_VMINVOICE_HANDLING_AND_SHIPPING'),$shippingLine);
				$shippingLine = str_replace('{attributes}',$this->params->get('show_shipping_carrier') ? trim($this->order->shipment_name.': '.$this->order->shipment_desc,' :') : '',$shippingLine);
				
				if ($this->params->get('show_shipping_prices')==1 OR (!$this->params->get('show_shipping_prices') AND $this->order->order_shipping>0)) //display prices
			    {
					$shippingLine = str_replace('{price}',$this->currencyDisplay->getValue($this->order->order_shipping, $this->currency, $this->showCurrency),$shippingLine);
					$shippingLine = str_replace('{price_notax}',$this->currencyDisplay->getValue($this->order->order_shipping, $this->currency, $this->showCurrency),$shippingLine);
					$shippingLine = str_replace('{tax_rate}',$this->shipTaxRate.'%',$shippingLine);
					$shippingLine = str_replace('{tax_price}',$this->currencyDisplay->getValue($this->order->order_shipping_tax, $this->currency, $this->showCurrency),$shippingLine);
					$shippingLine = str_replace('{tax_price_item}',$this->currencyDisplay->getValue($this->order->order_shipping_tax, $this->currency, $this->showCurrency),$shippingLine);
					$shippingLine = str_replace($subtotalTag,$this->currencyDisplay->getValue($this->order->order_shipping + $this->order->order_shipping_tax, $this->currency),$shippingLine);
		        }
		        
		        $shippingLine = preg_replace('#\{[\w ]+\}#Us','',$shippingLine); //remove rest of tags
				$itemLines[] = $shippingLine;
        	}
        	
			//generate line for payment fee/discount (in VM2 always if not empty, in VM1 based on config)
			if ($this->params->get('show_payment_row')==0 OR
				($this->params->get('show_payment_row')==1 AND ($this->order->order_payment + $this->order->order_payment_tax)!=0))
			{
				if (($this->order->order_payment + $this->order->order_payment_tax)>=0)
					$label = $this->_('COM_VMINVOICE_PAYMENT_FEE');
				else
					$label = $this->_('COM_VMINVOICE_PAYMENT_DISCOUNT');
				
				$paymentLine = $this->removeNonReplacedTags($template[1]); //remove also non-replaced tags. treat as product line.
		        $paymentLine = InvoiceHelper::removeColumns($paymentLine,$colsToDelete);
			    $paymentLine = str_replace('{name}',$label,$paymentLine);
				
			    if ($this->params->get('show_payment_row_type')) { //show payment type in line also
			    	$info = $this->getPaymentInfo();
    				if (!empty($info[0]))
    					$paymentLine = str_replace('{attributes}',trim($info[0].': '.$info[1],' :'),$paymentLine);
    			}
    						
				if (COM_VMINVOICE_ISVM1 AND empty($this->paymentTaxRate)) //if vm1 and not entered payment tax rate, dont display zero tax and base prices, only subtotal
					$paymentLine = str_replace($subtotalTag,$this->currencyDisplay->getValue($this->order->order_payment + $this->order->order_payment_tax, $this->currency),$paymentLine);
				else{
					$paymentLine = str_replace('{price}',$this->currencyDisplay->getValue($this->order->order_payment, $this->currency, $this->showCurrency),$paymentLine);
					$paymentLine = str_replace('{price_notax}',$this->currencyDisplay->getValue($this->order->order_payment, $this->currency, $this->showCurrency),$paymentLine);
					$paymentLine = str_replace('{tax_rate}',$this->paymentTaxRate.'%',$paymentLine);
					$paymentLine = str_replace('{tax_price}',$this->currencyDisplay->getValue($this->order->order_payment_tax, $this->currency, $this->showCurrency),$paymentLine);
					$paymentLine = str_replace('{tax_price_item}',$this->currencyDisplay->getValue($this->order->order_payment_tax, $this->currency, $this->showCurrency),$paymentLine);
					$paymentLine = str_replace($subtotalTag,$this->currencyDisplay->getValue($this->order->order_payment + $this->order->order_payment_tax, $this->currency),$paymentLine);
				}
				
		        $paymentLine = preg_replace('#\{[\w ]+\}#Us','',$paymentLine); //remove rest of tags
				$itemLines[] = $paymentLine;
			}

			//generate items footer		
			
	        //determine measures of footer block
	        // 15.11.2013 maju - {subtotal_base} is same as {price_notax}. The second one is older version and we need it there for compatibility with old settings
	        $relevantCols = array('{price_notax}','{subtotal_base}','{subtotal_discounted}','{tax_rate}','{tax_price}',$subtotalTag); //columns we use in footer block. footer block must contain all of them + 1 to left for labels.
	        $columns = InvoiceHelper::getColumns($templateLine);
	        $leftOffset = null; //left column no of items block
	        $rightOffset = $colsNo-1; //right column no of items block
	        $subtotalOffset = 0;
	        
	        foreach ($columns as $key => $column)
	        	foreach ($relevantCols as $relevantCol)
	        		if (strpos($column[2],$relevantCol)!==false){

	        			$leftOffset = is_null($leftOffset) ? $key : min($leftOffset,$key);
	        			$rightOffset = max($rightOffset,$key);
	        			if ($relevantCol==$subtotalTag)
	        				$subtotalOffset = $key;
	        		}
	        		

	        $rightOffset++;
	       
	        //TODO: poslední nemusí být subtotral?. to bohužel není možné. Subtotal je vždy s VAT. Vyžadovalo by další hodiny supportu to doprogramovat. 
	        //(poznámka: řádek se součty bude vždy poslední v tabulce a co si zobrazí jako poslední v řádku je jejich věc)
	        
	        //univeral code for footer line with or without hr
	       	$hrCode = VMI_NL.'<tr>';
	        if ($leftOffset-1>0)
	        	$hrCode.='<td colspan="'.($leftOffset-1).'"></td>';
	        $hrCode.='<td colspan="'.($rightOffset-$leftOffset+1).'"><hr style="color:black"></td>';
	        if ($colsNo-$rightOffset>0)
	        	$hrCode.='<td colspan="'.($colsNo-$rightOffset).'"></td>';
	        $hrCode .= '</tr>';
	        
	       	$emptyCode = VMI_NL.'<tr>';
	        if ($leftOffset-1>0)
	        	$emptyCode.='<td colspan="'.($leftOffset-1).'"></td>';
	        $emptyCode.='<td colspan="'.($rightOffset-$leftOffset+1).'"></td>';
	        if ($colsNo-$rightOffset>0)
	        	$emptyCode.='<td colspan="'.($colsNo-$rightOffset).'"></td>';
	        $emptyCode .= '</tr>';
	        
	        //just prepare vars
	        $showingTaxPrice = (strpos($template[1], '{tax_price}')!==false AND $this->params->get('show_tax_price'));
	        $showingTaxPriceItem = (strpos($template[1], '{tax_price_item}')!==false);
	        
	       	//pre-set content of footer lines
	        $footerLines = array();
	        
	        //add tax summary
	        if ($this->params->get('show_tax_summary')) {

	        	if ($this->params->get('show_tax_summary_label')){ //add tax summary label
			        $headerCode = VMI_NL.'<tr>';
			        if ($leftOffset-1>0)
			        	$headerCode.='<td colspan="'.($leftOffset-1).'"></td>';
			        $headerCode.='<td colspan="'.($rightOffset-$leftOffset+1).'">'.$this->_('COM_VMINVOICE_TAX_SUMMARY').'</td>';
			        if ($colsNo-$rightOffset>0)
			        	$headerCode.='<td colspan="'.($colsNo-$rightOffset).'"></td>';
			        $headerCode .= '</tr>';
			        
			        $footerLines['tax_summary'][] = $headerCode;
		        }
		        
		        $footerLines['tax_summary'][] = $hrCode; //add HR line before
		        
                // Add custom headers
                if ($this->params->get('use_tax_template') && $this->params->get('show_tax_header'))
                    $footerLines['tax_summary'][] = $template[2];

		        ksort($this->taxSum); 	// sort tax groups
                $firstLine = true;
		        foreach ($this->taxSum as $taxRate => $taxSum) {
		        	
                    // Use custom or default template?
		            $taxLine = ($this->params->get('use_tax_template') ? $template[3] : $template[1]);
		            $taxLine = InvoiceHelper::removeColumns($taxLine,$colsToDelete);
		            $taxLine = str_replace($subtotalTag2,$this->currencyDisplay->getValue($taxSum['total'], $this->currency),$taxLine);
		            
                    // Add total label if set to
                	if ($firstLine && ($this->params->get('show_total_label', 1) == 2)) {
                        $firstLine = false;
                        if ($leftOffset > 0){  //add total label
                			$td = '<td align="left"><b>'.$this->_('COM_VMINVOICE_TOTAL').':</b></td>';
                			$taxLine = InvoiceHelper::replaceColumn($taxLine, $leftOffset-1, $td);
                        }
                    }
                    
		            // 15.11.2013 maju - both, {price_notax} and {subtotal_base} are same - {price_notax} is older version for compatibility with old settings
		            $taxLine = str_replace('{price_notax}',$this->currencyDisplay->getValue($taxSum['notax'], $this->currency, $this->showCurrency),$taxLine);
		            $taxLine = str_replace('{subtotal_base}',$this->currencyDisplay->getValue($taxSum['notax'], $this->currency, $this->showCurrency),$taxLine);
		            
		            // 15.11.2013 maju - there is possible to remove discount calculation from tax summary so without discount it will work as {subtotal_base} or {price_notax}
		            $taxLine = str_replace('{subtotal_discounted}',$this->currencyDisplay->getValue($taxSum['total'] - $taxSum['taxa'], $this->currency, $this->showCurrency),$taxLine);
		            
		            $taxLine = str_replace('{tax_rate}',$taxRate.'%',$taxLine);

		            if ($showingTaxPrice AND $showingTaxPriceItem) //if showing both taxes, replace only "overall	
		            	$taxLine = str_replace('{tax_price}',$this->currencyDisplay->getValue($taxSum['taxa'], $this->currency, $this->showCurrency),$taxLine);
		            else //else replace one or other
		            	$taxLine = str_replace(array('{tax_price}', '{tax_price_item}'),$this->currencyDisplay->getValue($taxSum['taxa'], $this->currency, $this->showCurrency),$taxLine);
		            
		        	$taxLine = preg_replace('#\{[\w ]+\}#Us','',$taxLine); //remove rest of tags
					$footerLines['tax_summary'][]= $taxLine; //add tax line
		        }
		        
		   		$footerLines['tax_summary'][] = $hrCode; //add HR line after
	        }
	        
	        $couponDiscount = $this->order->coupon_discount;	//real coupon discount (gross). note by pama (21.2.2014): here is always negative.
            
            // 16.9.2013 dajo: fixed coupon discount taxes calculation - coupon discount should be applied evenly to all product lines
	        //$couponTaxRate = $this->params->get('coupon_vat')*1;	//coupon % VAT
	        //$couponNoTax = $this->order->coupon_discount/(($couponTaxRate/100)+1);	//coupon discount net
	        //$couponTaxAmount = $this->order->coupon_discount - $couponNoTax;	//coupon tax amount
            $couponRate = $this->subtotal_gross ? ($couponDiscount / $this->subtotal_gross) : 0;
            $couponTaxAmount = $this->subtotal_tax * $couponRate;
            $couponNoTax = $couponDiscount - $couponTaxAmount;
		    
		    $totalNet = $this->subtotal_net;	//total net price, without discount
		    $totalTax = $this->subtotal_tax;	//total tax, without coupon
		    $totalDiscount = $this->order->order_total - $this->subtotal_net - $this->subtotal_tax - $couponDiscount; //total discount, without coupon

		    if (COM_VMINVOICE_ISVM2 AND $this->calcRules)
	        	foreach ($this->calcRules as $rule)
		    		$totalDiscount -= (float)(string)$rule->calc_amount;
        	
		    $this->determineOnlyTaxRate();
		    //subtotal discount jsou všechny discounty BEZ COUPONU (ptz ty jsou pod nim)

        	//subtotals are shown without coupon
        	//(overall coupon discount is only taken to {discount} field
        	// 16.9.2013 dajo: subtotal shouldn't include the coupon discount! (it isn't included in VM order detail either)
            $subtotalDiscount = $totalDiscount; // + $couponDiscount;
        	
	        if ($this->params->get('show_subtotal')){ //takze subtotal je jeste bez couponu, dal se coupon odecte
	        	
	        	$subtotalLine = $template[1];
	        	$subtotalLine = InvoiceHelper::removeColumns($subtotalLine,$colsToDelete);
	        	if ($leftOffset>0)
	        		$subtotalLine = InvoiceHelper::replaceColumn($subtotalLine,$leftOffset-1, '<td align="left">'.$this->_('COM_VMINVOICE_SUBTOTAL').':</td>');
	        	
	        	$subtotalLine = str_replace($subtotalTag,$this->currencyDisplay->getValue($this->subtotal_gross, $this->currency),$subtotalLine);
	        	
	        	// 15.11.2013 maju - both, {price_notax} and {subtotal_base} are same - {price_notax} is older version for compatibility with old settings
		        $subtotalLine = str_replace('{price_notax}',$this->currencyDisplay->getValue($totalNet, $this->currency, $this->showCurrency),$subtotalLine);
		        $subtotalLine = str_replace('{subtotal_base}',$this->currencyDisplay->getValue($totalNet, $this->currency, $this->showCurrency),$subtotalLine);
		            
		        if ($showingTaxPrice AND $showingTaxPriceItem) //if showing both taxes, replace only "overall
		        	$subtotalLine = str_replace('{tax_price}',$this->currencyDisplay->getValue($this->subtotal_tax, $this->currency, $this->showCurrency),$subtotalLine);
		        else //else replace one or other
		        	$subtotalLine = str_replace(array('{tax_price}', '{tax_price_item}'),$this->currencyDisplay->getValue($this->subtotal_tax, $this->currency, $this->showCurrency),$subtotalLine);

		        $subtotalLine = str_replace('{discount}',$this->currencyDisplay->getValue($subtotalDiscount, $this->currency, $this->showCurrency),$subtotalLine);
		        
		        $subtotalLine = str_replace('{subtotal_discounted}',$this->currencyDisplay->getValue($this->subtotal_gross - $this->subtotal_tax, $this->currency, $this->showCurrency),$subtotalLine);
		        
		        $subtotalLine = preg_replace('#\{[\w ]+\}#Us','',$subtotalLine); //remove rest of tags
				$footerLines['subtotal'][] = $subtotalLine;
	        }

	        //now generate total lines 
	        
	        $labelColspan = $subtotalOffset - $leftOffset + 1; //space for total labels
	        
	        $couponLine = false;

        	if ($couponDiscount != 0 AND !$this->deliveryNote){

	        	$couponLine = $template[1];
	        	$couponLine = InvoiceHelper::removeColumns($couponLine,$colsToDelete);
	        	$couponLine = str_replace($subtotalTag,$this->currencyDisplay->getValue($couponDiscount, $this->currency),$couponLine);
	        	
	        	if ($this->params->get('coupon_extended')>0){ //broke coupon discount price to net + tax + price

		        	if ($leftOffset>0)
		        		$couponLine = InvoiceHelper::replaceColumn($couponLine,$leftOffset-1, '<td align="left">'.$this->_('COM_VMINVOICE_COUPON').':</td>'); //use shorter string version
		            $couponLine = str_replace('{price_notax}',$this->currencyDisplay->getValue($couponNoTax, $this->currency, $this->showCurrency),$couponLine);
		            $couponLine = str_replace('{tax_rate}',(($this->params->get('coupon_vat') == '') ? '' : ($this->params->get('coupon_vat') . '%')),$couponLine);
		            
		            if ($showingTaxPrice AND $showingTaxPriceItem) //if showing both taxes, replace only "overall
		            	$couponLine = str_replace('{tax_price}',($this->params->get('show_coupon_tax_amount') ? $this->currencyDisplay->getValue($couponTaxAmount, $this->currency, $this->showCurrency) : ''),$couponLine);
		          	else //else replace one or other
		            	$couponLine = str_replace(array('{tax_price}', '{tax_price_item}'),($this->params->get('show_coupon_tax_amount') ? $this->currencyDisplay->getValue($couponTaxAmount, $this->currency, $this->showCurrency) : ''),$couponLine);
	        	}
	        	else{ //else just coupon discount
	        		
		        	if ($leftOffset>0 AND $labelColspan>0){ //make column wider straight to subtotal
		        		$couponLine = InvoiceHelper::removeColumns($couponLine,range($leftOffset-1, $subtotalOffset -1));
		        		$td = '<td align="left" colspan="'.$labelColspan.'">'.$this->_('COM_VMINVOICE_COUPON_DISCOUNT').':</td>';
		        		$couponLine = InvoiceHelper::addColumn($couponLine,$leftOffset-1, $td);
		        	}
	        	}
	        	     	
	        	$couponLine = preg_replace('#\{[\w ]+\}#Us','',$couponLine); //remove rest of tags
	        }
	        
	        if ($couponLine AND $this->params->get('coupon_extended')) //add "extended" coupon line
				$footerLines['coupon_extended'][] = $couponLine;
     	        
	        //VM2: display lines with used calcaultion rules for general order
	        if (COM_VMINVOICE_ISVM2 AND $this->calcRules)
	        	foreach ($this->calcRules as $rule){
	        		
	        		if ((string)$rule->calc_amount==0) //skip rules with no amount
	        			continue;
	        		
	        		$ruleCurrency = !empty($rule->calc_currency) ? $rule->calc_currency : $this->currency; //get currency (VM 2.0.12 stores it)
	        		//$ruleValue = 
	        		
		        	$ruleLine = $template[1];
		        	$ruleLine = InvoiceHelper::removeColumns($ruleLine,$colsToDelete);
		        	$ruleLine = str_replace($subtotalTag,$this->currencyDisplay->getValue($rule->calc_amount, $ruleCurrency),$ruleLine);
		        	
		        	if ($leftOffset>0 AND $labelColspan>0){ //make column wider straight to subtotal
		        		$ruleLine = InvoiceHelper::removeColumns($ruleLine,range($leftOffset-1, $subtotalOffset -1));
		        		$td = '<td align="left" colspan="'.$labelColspan.'">'.($rule->calc_rule_name ? $rule->calc_rule_name.':' : '').'</td>';
		        		$ruleLine = InvoiceHelper::addColumn($ruleLine,$leftOffset-1, $td);
		        	}
		        	
					$ruleLine = preg_replace('#\{[\w ]+\}#Us','',$ruleLine); //remove rest of tags
					$footerLines['calc_rules'][] = $ruleLine;
	        	}
	        
	   		$orderDiscount =  $this->order->order_discount;
	        if (COM_VMINVOICE_ISVM1 AND $this->params->get('show_payment_row')!=2) //in VM1 and we already deducted payment discount from order discount...
	        	$orderDiscount += $this->order->order_payment + $this->order->order_payment_tax;
	   		
	        if ((int)$this->currencyDisplay->getValue(-$orderDiscount, $this->currency,false) != 0
	        	AND !(COM_VMINVOICE_ISVM2 AND $this->params->get('take_discount_into_summary',0)==1)){
	        	$discountLine = $template[1];
	        	$discountLine = InvoiceHelper::removeColumns($discountLine,$colsToDelete);
	        	$discountLine = str_replace($subtotalTag,$this->currencyDisplay->getValue(-$orderDiscount, $this->currency),$discountLine);
	        	
	        	if ($leftOffset>0 AND $labelColspan>0){  //make column wider straight to subtotal
	        		$discountLine = InvoiceHelper::removeColumns($discountLine,range($leftOffset-1, $subtotalOffset -1));
	        		$td = '<td align="left" colspan="'.$labelColspan.'">'.$this->_($orderDiscount> 0 ? 'COM_VMINVOICE_DISCOUNT' : 'COM_VMINVOICE_FEE').':</td>';
	        		$discountLine = InvoiceHelper::addColumn($discountLine,$leftOffset-1, $td);
	        	}
	        	
				$discountLine = preg_replace('#\{[\w ]+\}#Us','',$discountLine); //remove rest of tags
				$footerLines['discount'][] = $discountLine;
	        }

	        if ($couponLine AND !$this->params->get('coupon_extended')) //add "simple" coupon line
				$footerLines['coupon_simple'][] = $couponLine;
	        
	        //CUSTOMIZATION: (Photogem s.r.l.) - show totals with coupon discounts
	        /*
	        "Total order deduced the coupon net value without tax (euro 5,25)
			Total tax deduced the coupon tax (euro 1,25)
			Total order, net value + tax (euro 7,20)"
			*/

	        //show totals
	        
	        if ($this->params->get('show_total_net',0)>0) //add "Total net" line
	        {
	        	$totalNetLine = $template[1];
	        	$totalNetLine = InvoiceHelper::removeColumns($totalNetLine,$colsToDelete);
	        	if ($leftOffset>0 AND $labelColspan>0){  //make column wider straight to subtotal
	        		$totalNetLine = InvoiceHelper::removeColumns($totalNetLine,range($leftOffset-1, $subtotalOffset -1));
	        		$td = '<td align="left" colspan="'.$labelColspan.'">'.$this->_('COM_VMINVOICE_TOTAL_NET').':</td>';
	        		$totalNetLine = InvoiceHelper::addColumn($totalNetLine,$leftOffset-1, $td);
	        	}

	        	//show without (other, not coupon) discount (that is in "disocunts"), unless...
	        	$totalNetShow = $totalNet; //2: witout coupon discount
	        	if ($this->params->get('show_total_net',0)==1) //1: deduct coupon
	        		$totalNetShow += $couponNoTax;
	        	elseif ($this->params->get('show_total_net',0)==4) //4: deduct coupon and items discounts
	        		$totalNetShow += $couponNoTax + $totalDiscount;
	        	elseif ($this->params->get('show_total_net',0)==3) //...if set, deduct from net now (andrea.coppolecchia@gmail.com)
	        		$totalNetShow += $totalDiscount;

	        	$totalNetLine = str_replace($subtotalTag,$this->currencyDisplay->getValue($totalNetShow, $this->currency),$totalNetLine);
				$totalNetLine = preg_replace('#\{[\w ]+\}#Us','',$totalNetLine); //remove rest of tags
				$footerLines['total_net'][] = $totalNetLine;
	        }
	        
	        if ($this->params->get('show_total_tax',0)>0) //add "Total tax" line
	        {
	        	$totalTaxLine = $template[1];
	        	$totalTaxLine = InvoiceHelper::removeColumns($totalTaxLine,$colsToDelete);
	        	if ($leftOffset>0 AND $labelColspan>0){  //make column wider straight to subtotal
	        		$totalTaxLine = InvoiceHelper::removeColumns($totalTaxLine,range($leftOffset-1, $subtotalOffset -1));
	        		$td = '<td align="left" colspan="'.$labelColspan.'">'.$this->_('COM_VMINVOICE_TOTAL_TAX').(($this->params->get('show_total_tax_percent') AND $this->onlyOneTaxRate!==false) ? ' ('.($this->onlyOneTaxRate*1).'%)' : '').':</td>';
	        		$totalTaxLine = InvoiceHelper::addColumn($totalTaxLine,$leftOffset-1, $td);
	        	}
	        	
	        	$totalTaxShow = $totalTax; //2: witout coupon discount
	        	if ($this->params->get('show_total_tax',1)==1) //1: deduct coupon
	        		$totalTaxShow += $couponTaxAmount;
	        		        	
	        	//without coupon discount
	        	$totalTaxLine = str_replace($subtotalTag,$this->currencyDisplay->getValue($totalTaxShow, $this->currency),$totalTaxLine);
	        	$totalTaxLine = preg_replace('#\{[\w ]+\}#Us','',$totalTaxLine); //remove rest of tags
	        	$footerLines['total_tax'][] = $totalTaxLine;
	        }
	        
	        if (($totalDiscount) AND $this->params->get('show_total_discount',0)>0) //add "Total discount" line
	        {
	        	$totalDiscountLine = $template[1];
	        	$totalDiscountLine = InvoiceHelper::removeColumns($totalDiscountLine,$colsToDelete);
	        	if ($leftOffset>0 AND $labelColspan>0){  //make column wider straight to subtotal
	        		$totalDiscountLine = InvoiceHelper::removeColumns($totalDiscountLine,range($leftOffset-1, $subtotalOffset -1));
	        		$td = '<td align="left" colspan="'.$labelColspan.'">'.$this->_('COM_VMINVOICE_TOTAL_DISCOUNT').':</td>';
	        		$totalDiscountLine = InvoiceHelper::addColumn($totalDiscountLine,$leftOffset-1, $td);
	        	}
	        	
	        	
	        	$totalDiscountShow = $totalDiscount; //2: witout coupon discount
	        	if ($this->params->get('show_total_discount',1)==1) //1: deduct coupon
	        		$totalDiscountShow += $couponDiscount;

	        	$totalDiscountLine = str_replace($subtotalTag,$this->currencyDisplay->getValue($totalDiscountShow, $this->currency),$totalDiscountLine);
	        	$totalDiscountLine = preg_replace('#\{[\w ]+\}#Us','',$totalDiscountLine); //remove rest of tags
	        	$footerLines['total_discount'][] = $totalDiscountLine;
	        }
	        
	        //show total line
            $totalLine = ($this->params->get('use_tax_template', 0) == 0) ? $template[1] : $template[3];
            $totalLine = InvoiceHelper::removeColumns($totalLine,$colsToDelete);
            $totalLine = str_replace($subtotalTag,'<b>'.$this->currencyDisplay->getValue($this->order->order_total, $this->currency).'</b>',$totalLine); //now, to make sure it will not be replaced by e.g. subtotal_discounted
	        
	        if ($this->params->get('total_extended',0)>0){

	        	$totalNetShow = $totalNet; //2: not deduct coupon discount
	        	$totalTaxShow = $totalTax;
	        	$totalDiscountShow = $totalDiscount + $couponDiscount; //(so we must add it to discount field to prices fit)
	        	if ($this->params->get('total_extended',0)==1){ //1: deduct coupon discount
	        		$totalNetShow += $couponNoTax;
	        		$totalTaxShow += $couponTaxAmount;
	        		$totalDiscountShow -= $couponDiscount; //not add to disocunt fields to prices fit
	        	}
	        
			    // 15.11.2013 maju - both, {price_notax} and {subtotal_base} are same - {price_notax} is older version for compatibility with old settings 
			    $totalLine = str_replace('{price_notax}',$this->currencyDisplay->getValue($totalNetShow, $this->currency, $this->showCurrency),$totalLine);
			    $totalLine = str_replace('{subtotal_base}',$this->currencyDisplay->getValue($totalNetShow, $this->currency, $this->showCurrency),$totalLine);
			    
			    $totalLine = str_replace('{subtotal_discounted}',$this->currencyDisplay->getValue($this->order->order_total - $totalTaxShow, $this->currency, $this->showCurrency),$totalLine);
						
			    if ($this->onlyOneTaxRate)//if is used one tax for whole order, display it
			    	$totalLine = str_replace('{tax_rate}',$this->onlyOneTaxRate.'%',$totalLine);

			    if ($showingTaxPrice AND $showingTaxPriceItem) //if showing both taxes, replace only "overall
			   	 	$totalLine = str_replace('{tax_price}',$this->currencyDisplay->getValue($totalTaxShow, $this->currency, $this->showCurrency),$totalLine);
			    else //else replace one or other
			    	$totalLine = str_replace(array('{tax_price}', '{tax_price_item}'),$this->currencyDisplay->getValue($totalTaxShow, $this->currency, $this->showCurrency),$totalLine);

			    $totalLine = str_replace('{discount}',$this->currencyDisplay->getValue($totalDiscountShow, $this->currency, $this->showCurrency),$totalLine);
	        }            
        
     	    // Add total label if set to
            if ($this->params->get('show_total_label', 1) == 1) {
            	if ($leftOffset>0 AND $labelColspan>0){
            		if ($this->params->get('total_extended')){
            			$td = '<td align="left"><b>'.$this->_('COM_VMINVOICE_TOTAL').':</b></td>';
            			$totalLine = InvoiceHelper::removeColumns($totalLine,$leftOffset-1);
            			$totalLine = InvoiceHelper::addColumn($totalLine,$leftOffset-1, $td);
            		}
            		else { //make column wider straight to subtotal
            			$td = '<td align="left" colspan="'.$labelColspan.'"><b>'.$this->_('COM_VMINVOICE_TOTAL').':</b></td>';
    	        		$totalLine = InvoiceHelper::removeColumns($totalLine,range($leftOffset-1, $subtotalOffset -1));
    	        		$totalLine = InvoiceHelper::addColumn($totalLine,$leftOffset-1, $td);
            		}
    	        }
            }

	        $totalLine = preg_replace('#\{[\w ]+\}#Us','',$totalLine); //remove rest of tags
	        
	        $footerLines['total'][] = $totalLine; 
            
            // Display block with converted currencies if set to
            if (COM_VMINVOICE_ISVM2 && ($this->params->get('show_total_currencies', 0) > 0)) {
                $codes = trim($this->params->get('show_total_currencies_list', ''), ', ');

                if (!empty($codes)) {
                    $codes = array_unique(array_map('trim', explode(',', $codes)));
                    $mainCode = InvoiceCurrencyDisplay::getCode($this->order->order_currency); //not use $this->currency
					foreach ( $codes as $code ) {
						
						if ($code == 'PAYMENT') { // display payment currency
							if (! $this->order->user_currency_id or $this->order->user_currency_id == $this->currency)
								continue; //no need to display it
							
							$currencyId = $this->order->user_currency_id; 
							$currencies = InvoiceGetter::getCurrencies();
							//var_dump($currencies, $currencyId);exit;
							if (!isset($currencies[$currencyId]))
								continue; // weird
							
							$code = $currencies [$currencyId]->currency_code;
							$exRate = $this->order->user_currency_rate; // use fixed rate
						} else {
							$currencyId = InvoiceCurrencyDisplay::getCurrencyId ( $code );
							if (! $currencyId)
								$currencyId = $this->currency;
							$exRate = null; // use converter
						}
						
						if (isset ( $currencyLines [$code] ))
							continue; // we already have this line
						
						if (InvoiceCurrencyDisplay::convert ( $this->order->order_total, $mainCode, $code, $exRate ) === false) // for some reason cannot convert, skip
							continue;
						
						$line = $template [1];
						$line = InvoiceHelper::removeColumns ( $line, $colsToDelete );
						if ($leftOffset > 0)
							$line = InvoiceHelper::replaceColumn ( $line, $leftOffset - 1, '<td align="left">' . $code . ':</td>' );
						
						//
						
						if ($this->params->get ( 'show_total_currencies', 0 ) == 2) {
							// Subtotal
							$line = str_replace ( $subtotalTag, InvoiceCurrencyDisplay::getFullValue ( InvoiceCurrencyDisplay::convert ( $this->subtotal_gross, $mainCode, $code, $exRate ), $currencyId ), $line );
							$line = str_replace ( '{price_notax}', InvoiceCurrencyDisplay::getFullValue ( InvoiceCurrencyDisplay::convert ( $totalNet, $mainCode, $code, $exRate ), $currencyId, $this->showCurrency ), $line );
							$line = str_replace ( '{subtotal_base}', InvoiceCurrencyDisplay::getFullValue ( InvoiceCurrencyDisplay::convert ( $totalNet, $mainCode, $code, $exRate ), $currencyId, $this->showCurrency ), $line );
							
							if ($showingTaxPrice and $showingTaxPriceItem) // if showing both taxes, replace only "overall
								$line = str_replace ( '{tax_price}', InvoiceCurrencyDisplay::getFullValue ( InvoiceCurrencyDisplay::convert ( $this->subtotal_tax, $mainCode, $code, $exRate ), $currencyId, $this->showCurrency ), $line );
							else
								$line = str_replace ( array ('{tax_price}','{tax_price_item}' ), InvoiceCurrencyDisplay::getFullValue ( InvoiceCurrencyDisplay::convert ( $this->subtotal_tax, $mainCode, $code, $exRate ), $currencyId, $this->showCurrency ), $line );
							
							$line = str_replace ( '{discount}', InvoiceCurrencyDisplay::getFullValue ( InvoiceCurrencyDisplay::convert ( $subtotalDiscount, $mainCode, $code, $exRate ), $currencyId, $this->showCurrency ), $line );
							$line = str_replace ( '{subtotal_discounted}', InvoiceCurrencyDisplay::getFullValue ( InvoiceCurrencyDisplay::convert ( $this->subtotal_gross - $this->subtotal_tax, $mainCode, $code, $exRate ), $currencyId, $this->showCurrency ), $line );
						} else {
							// Total
							if ($leftOffset > 0 and $labelColspan > 0) { // add total label
								if ($this->params->get ( 'total_extended' )) {
									$td = '<td align="left">' . $code . ':</td>';
									$line = InvoiceHelper::removeColumns ( $line, $leftOffset - 1 );
									$line = InvoiceHelper::addColumn ( $line, $leftOffset - 1, $td );
								} else { // make column wider straight to subtotal
									$td = '<td align="left" colspan="' . $labelColspan . '">' . $code . ':</td>';
									$line = InvoiceHelper::removeColumns ( $line, range ( $leftOffset - 1, $subtotalOffset - 1 ) );
									$line = InvoiceHelper::addColumn ( $line, $leftOffset - 1, $td );
								}
							}
							
							$line = str_replace ( $subtotalTag, InvoiceCurrencyDisplay::getFullValue ( InvoiceCurrencyDisplay::convert ( $this->order->order_total, $mainCode, $code, $exRate ), $currencyId ), $line );
						}
						
						$line = preg_replace ( '#\{[\w ]+\}#Us', '', $line ); // remove rest of tags
						$currencyLines [$code] = $line;
					}
				}
            }
        }
        
        
        $headerLine = InvoiceHelper::removeColumns($template[0],$colsToDelete); //language contstants are at template and are replaced by [] replace in method getHTML
		
        //now compute columns width from header columns
        $colWidths = array();
        $headColumns = InvoiceHelper::getColumns($headerLine);
        foreach ($headColumns as $key => $headColumn)
        {
        	if (preg_match('#style\s*=\s*"[^"]*width\s*:\s*(\d+)\s*%#is',$headColumn[1],$width)) //width set by style
        		$colWidths[$key]=(int)$width[1]; 
        	if (preg_match('#width\s*=\s*"?\s*(\d+)\s*%#is',$headColumn[1],$width)) //width set by attribute
        		$colWidths[$key]=(int)$width[1]; 
        }

        if ($colsNoWidth = count($headColumns) - count($colWidths)) //some columns dont have width specified
        {
        	$oneColumn = (100 - array_sum($colWidths)) / $colsNoWidth; //split remaning % (if any) width between them
        	
        	if ($oneColumn<0)
        		$oneColumn = 0;
        		
	        foreach ($headColumns as $key => $headColumn) 
	        	if (!isset($colWidths[$key]))
	        		$colWidths[$key] = $oneColumn;	
        }

        if (($widthSum = array_sum($colWidths))!=100) //overall width is bigger or lower than 100%
        {
        	$ratio = 100 / $widthSum; //ratio to reduce/enlarge them to 100
        	foreach ($colWidths as &$width)
        		$width *= $ratio;
        }
        //TODO: get column widths array real (like without colspan). when is colspan, devide by it
		//TODO: when setting column widths, counts also with colspan (sum them)
        //set new widths
        $headerLine = InvoiceHelper::setColumnWidths($headerLine,$colWidths);
		$itemLines = InvoiceHelper::setColumnWidths($itemLines,$colWidths);
        $currencyLines = InvoiceHelper::setColumnWidths($currencyLines,$colWidths);
			
		//start generating table
        $code = VMI_NL.'<table style="table-layout:fixed;width:100%;" >';
        if ($this->params->get('repeat_header')) //TCPDF and mPDF will auto repeat then
        	$code.= VMI_NL.'<thead>';
		$code.= VMI_NL.$headerLine;
        // 18.12.2013 dajo: removed width="100%" as a quick fix, because due to rounding error column widths may exceed 100% and the line was then moved to the left
		$code.= VMI_NL.'<tr><td colspan="'.$colsNo.'"><hr style="color:black"></td></tr>'; //TODO: tohle dÄ›lat v tamplatu. poÄŤĂ­tat s rowspanem. au. :D a proÄŤ je to tak velkĂ©?
        if ($this->params->get('repeat_header'))
        	$code.= VMI_NL.'</thead>';
        $code.= VMI_NL.'<tbody>';
        $code.= VMI_NL.implode(VMI_NL,$itemLines);
        $code.= VMI_NL.'</tbody>';
        
        
        //booing does not have footer?

        if (!$this->deliveryNote)
        {
        	if ($this->params->get('pdf_library', 'tcpdf')!='mpdf') //mPDF auto-repeats tfoot on every page
	        	$code.= VMI_NL.'<tfoot>'; 

	        //re-sort footer lines based on config
			$ordering = invoiceHelper::getItemsFooterOrdering($this->deliveryNote);
			$footerLinesOrdered = array();
			foreach ($ordering as $key => $type){
				if (isset($footerLines[$type]))
					$footerLinesOrdered[$type] = $footerLines[$type];
				elseif ($type=='empty') // add empty line
					$footerLinesOrdered[$type.'_'.$key] = $emptyCode; //must be unique
				elseif ($type=='hr') // add hr line
					$footerLinesOrdered[$type.'_'.$key] = $hrCode; //must be unique
			}
			
	        //make one-dimensional array from it
	        $footerLinesNew = array();
	        foreach ($footerLinesOrdered as $type => $lines) {
                if (!is_array($lines))
        			$footerLinesNew[$type] = InvoiceHelper::setColumnWidths($lines,$colWidths);
        		else
        			foreach ($lines as $key => $line)
	        			$footerLinesNew[$type.'_'.$key] = InvoiceHelper::setColumnWidths($line,$colWidths);
	        }
	        
	        //delete duplicated rows (like hr-rows next to each other)
	        $lastLine = false;
	        foreach ($footerLinesNew as $key => $line){
	        	if ($lastLine==$line AND !preg_match('#^empty_#', $key)) //not apply for empty line (can be intentional)
	        		unset($footerLinesNew[$key]);
	        	$lastLine = $line;
	        }
	        
	        //trigger event
	        $this->dispatcher->trigger('onItemsFooterWrite', array(&$this, &$footerLinesNew, &$code, $this->params));
	        
	        $code.= implode(VMI_NL, $footerLinesNew);
	        
	        if ($this->params->get('pdf_library', 'tcpdf')!='mpdf')
	       		$code.= VMI_NL.'</tfoot>';
        }
        
        $code.= VMI_NL.'</table>';

        // Add the currencies block
        if (count($currencyLines) > 0) {
            $code .= VMI_NL.'<br/><br/>'.VMI_NL;
            $code .= '<table style="table-layout:fixed;width:100%" >'.VMI_NL;
            $code .= '<tbody>'.VMI_NL;
            $code .= implode(VMI_NL, $currencyLines);
            $code .= '</tbody>'.VMI_NL;
            $code .= '</table>'.VMI_NL;
        }
        
        //insert special tag fro TCPDF to non-break table rows (but its not working)
        $code = preg_replace('#(<\s*tr.*)>#Uis','$1 nobr="true">',$code);
 
        //trigger event
	    $this->dispatcher->trigger('onItemsWrite', array(&$this, &$code, $this->params));
     	   
        return $code;
    }
    
    
    /**
     * Determine if using only one tax rate
     */
    function determineOnlyTaxRate() {
    	$this->onlyOneTaxRate = false;
    	$taxRates = array_keys($this->taxSum);
    	foreach ($taxRates as $key => $val) // unset 0%, but only if no base amount (else total line will have misfitting calculation)
	    	if (empty($val) AND empty($this->taxSum[(string)$val]['notax']))
    			unset($taxRates[$key]);
    	if (count($taxRates)==1)  {
    		$taxRate = reset($taxRates);
            // 16.9.2013 dajo: coupon discount doesn't have its own tax rate, it's evenly distributed to all lines
    		//if ($couponTaxRate == $taxRate OR !$couponDiscount OR !$couponTaxRate)
   			$this->onlyOneTaxRate = $taxRate;
    	}
    }
    
    
    function getSignature ()
    {
    	$code='';
    	
    	
        $url = 'http://www.artio.net/virtuemart-tools/vm-invoice-generator';
        $link = ' <a href="' . $url . '">ARTIO VM Invoice</a> ';
        $url = '<a href="' . $url . '">' . $url . '</a>';
        $code = VMI_NL . '<div style="font-size: 70%; text-align: center;">' 
            . $this->_('COM_VMINVOICE_THIS_DOCUMENT_WAS_GENERATED_USING') . $link 
            . ' - ' . $this->_('COM_VMINVOICE_VIRTUEMART_PDF_INVOICING_SOLUTION') . '.<br />' . $url 
            . '</div>';
        
        
    	
        return $code;
    }
}
?>
