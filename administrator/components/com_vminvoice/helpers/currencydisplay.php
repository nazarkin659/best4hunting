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

class InvoiceCurrencyDisplay
{
    /*
    function getValue ($nb, $decimals = '')
    {
        $res = "";
        // Warning ! number_format function performs implicit rounding
        // Rounding is not handled in this DISPLAY class
        // that's why you have to use the right decimal value.
        // Workaround :number_format accepts either 1, 2 or 4 parameters.
        // this cause problem when no thousands separator is given : in this
        // case, an unwanted ',' is displayed.
        // That's why we have to do the work ourserlve.
        // Note : when no decimal il given (i.e. 3 parameters), everything works fine
        if ($decimals === '') {
            $decimals = $this->nbDecimal;
        }
        if ($this->thousands != '') {
            $res = number_format($nb, $decimals, $this->decimal, $this->thousands);
        } else {
            // If decimal is equal to defaut thousand separator, apply a trick
            if ($this->decimal == ',') {
                $res = number_format($nb, $decimals, $this->decimal, '|');
                $res = str_replace('|', '', $res);
            } else {
                // Else a simple substitution is enough
                $res = number_format($nb, $decimals, $this->decimal, $this->thousands);
                $res = str_replace(',', '', $res);
            }
        }
        return ($res);
    }
*/
	
    static $vm2Currency;
    static $vm1Vendor;
    static $vm1Style;
    
	static $currencyReplacements;

	var $sourceCurrency;
	var $fixedRates = array(); //own currency rates
	
	function __construct($sourceCurrency = null) //if provided, for every getValue will be done also converting
	{
		$this->sourceCurrency = $sourceCurrency;
	}
	
	
	/**
	 * Get number of decimals for currency
	 * @param unknown_type $currency
	 */
	static function getDecimals($currency=null)
	{
		if (COM_VMINVOICE_ISVM2){
			self::loadVM2Currency($currency);
			return isset(self::$vm2Currency[$currency]->currency_decimal_place) ? self::$vm2Currency[$currency]->currency_decimal_place : 2; //assume 2
		}
		else{
			self::loadVM1Currency();
			return isset(self::$vm1Style[2]) ? self::$vm1Style[2] : 2; //assume 2
		}
	}
	
	static function getSymbol($currency=null)
	{
		if (COM_VMINVOICE_ISVM2){
			self::loadVM2Currency($currency);
			return isset(self::$vm2Currency[$currency]->currency_symbol) ? self::$vm2Currency[$currency]->currency_symbol : false;
		}
		else{
			self::loadVM1Currency();
			
			if (!$currency || ($currency == self::$vm1Vendor->vendor_currency)) //we are displaying vendor currency
	       		$symbol = self::$vm1Style[1]; //symbol take from format settings
	       	else //else symbol will be passed curency code
	       		$symbol = $currency;
	       	   
			return $symbol;
		}
	}
	
	static function loadVM2Currency($currency=null)
	{
	    if (!isset(self::$vm2Currency[$currency]))	{
    		$db = JFactory::getDBO();
    		$db->setQuery('SELECT * FROM #__virtuemart_currencies WHERE virtuemart_currency_id = '.(int)$currency);
			self::$vm2Currency[$currency] = $db->loadObject();
    	}
	}
	 
	static function loadVM1Currency()
	{
	     if (!self::$vm1Vendor){
	       $db = JFactory::getDBO();
	       $db->setQuery('SELECT `vendor_currency`, `vendor_currency_display_style`, `vendor_currency` FROM `#__vm_vendor` WHERE `vendor_id` = 1');
	       self::$vm1Vendor =  $db->loadObject();
	       self::$vm1Style = explode('|',self::$vm1Vendor->vendor_currency_display_style);
	     }
	}
    
    /**
     * Returns currency's code (only VM2!)
     * 
     * @param int $currency Currency ID
     * @return string Currency code or empty string if not found
     */
    static function getCode($currency = null)
    {
        if (!$currency)
            return '';
        
        if (!COM_VMINVOICE_ISVM2)
            return $currency;

        self::loadVM2Currency($currency);
        
        if (!self::$vm2Currency[$currency])
            return $currency;
        
        return self::$vm2Currency[$currency]->currency_code_3;
    }
    
    /**
     * Returns currency ID for given currency code (only VM2!)
     * 
     * @param string $code Currency code
     * @return string|null Currency ID or null on error
     */
    static function getCurrencyId($code)
    {
        if (!$code)
            return null;
        
        if (!COM_VMINVOICE_ISVM2) {
            return null;
        }
        
		$db = JFactory::getDBO();
		$db->setQuery('SELECT virtuemart_currency_id FROM #__virtuemart_currencies WHERE currency_code_3 = '.$db->Quote($code));
        return $db->loadResult();
    }
	
    
    //same as getFullValue, but works on instance
    //if $currency will be different than this->sourceCurrency, conversion will be done
    function getValue($nb,$currency=null,$showSymbol=true)
    {
    	$nb = (float)$nb;
    	
    	if ($this->sourceCurrency AND $currency AND $this->sourceCurrency!=$currency){
    		
    		$rate = null;
    		if (isset($this->fixedRates[$this->sourceCurrency][$currency])) //we have fixed rate stored for this source->final
    			$rate = $this->fixedRates[$this->sourceCurrency][$currency]; //else will be used vm cunverter module
    		//TODO: a neslo by to i naopak?
    		if (($nb2 = self::convert($nb, $this->sourceCurrency, $currency, $rate))!==false)
    			$nb = $nb2;
    	}

    	return self::getFullValue($nb, $currency, $showSymbol);
    }
    
    /**
     * Returns formatted currency
     * 
     * @param float $nb	number
     * @param string/int $currency currency code (vm1) currency id (vm2)
     * @param bool	if show symbol
     */
    static function getFullValue($nb,$currency=null,$showSymbol=true)
    {
    	$nb = (float)$nb;
    	
    	$params = InvoiceHelper::getParams();
    	$rounding = $params->get('rounding',1);
    	
    	if (!$currency)
    		return $nb;

    	
	        
		
    	if (COM_VMINVOICE_ISVM2)
    	{
    		self::loadVM2Currency($currency);
    		
    		if (!self::$vm2Currency[$currency])
    			return (float)$nb;
    		
    		$symbol = self::$vm2Currency[$currency]->currency_symbol;
    		
    		// check for defined currency replacements
	        $symbol = self::replaceSymbol($symbol);

		    if ($rounding==0)
		        $nb = self::roundBetterDown($nb,self::$vm2Currency[$currency]->currency_decimal_place);
		    elseif ($rounding==1)  
	    		$nb = self::roundBetter($nb,self::$vm2Currency[$currency]->currency_decimal_place);
	    	else
	    		$nb = self::roundBetterUp($nb,self::$vm2Currency[$currency]->currency_decimal_place);
	        
    		$number = number_format(abs($nb), (int)self::$vm2Currency[$currency]->currency_decimal_place, self::$vm2Currency[$currency]->currency_decimal_symbol, self::$vm2Currency[$currency]->currency_thousands);
    		$return = self::$vm2Currency[$currency]->{$nb>=0?'currency_positive_style':'currency_negative_style'};
    		$return = str_replace('{sign}','-',$return);
    		$return = str_replace('{number}',$number,$return);
			$return = str_replace('{symbol}',$showSymbol ? $symbol : '',$return);
			//$return = str_replace(' ','&nbsp;', $return); //replace by nbsp to not break lines in invoices
			
			return $return;
    	}
    	else //vm1
    	{
	        self::loadVM1Currency();
	        if (!self::$vm1Vendor)
	        	return $nb;
	        	
	       if ($currency == self::$vm1Vendor->vendor_currency) //we are displaying vendor currency
	       		$symbol = self::$vm1Style[1]; //symbol take from format settings
	       else{ //else symbol will be passed curency code
	       	   $symbol = $currency;
	    	   switch ($symbol) { //converted by this switch
		            case 'USD':
		                $symbol = '$';
		                break;
		            case 'EUR':
		                $symbol = '€';
		                break;
		            case 'GBP':
		                $symbol = '£';
		                break;
		            case 'JPY':
		                $symbol = '¥';
		                break;
		            case 'AUD':
		                $symbol = 'AUD $';
		                break;
		            case 'CAD':
		                $symbol = 'CAD $';
		                break;
		            case 'HKD':
		                $symbol = 'HKD $';
		                break;
		            case 'NZD':
		                $symbol = 'NZD $';
		                break;
		            case 'SGD':
		                $symbol = 'SGD $';
		                break;
		        }
	       	}

    		// check for defined currency replacements
	        $symbol = self::replaceSymbol($symbol);
	        
		    if ($rounding==0)
		        $nb = self::roundBetterDown($nb,self::$vm1Style[2]);
		    elseif ($rounding==1)  
	    		$nb = self::roundBetter($nb,self::$vm1Style[2]);
	    	else
	    		$nb = self::roundBetterUp($nb,self::$vm1Style[2]);

	        $symbol = $showSymbol ? $symbol : '';
		        
	    	// currency symbol position
	        if ($nb == abs($nb)) {
				$number = number_format($nb, (int)self::$vm1Style[2], self::$vm1Style[3], self::$vm1Style[4]);
	            // Positive number
	            switch (self::$vm1Style[5]) {
	                case 0:
	                    // 0 = '00Symb'
	                    $number = $number . $symbol;
	                    break;
	                case 2:
	                    // 2 = 'Symb00'
	                    $number = $symbol . $number;
	                    break;
	                case 3:
	                    // 3 = 'Symb 00'
	                    $number = $symbol . ' ' . $number;
	                    break;
	                case 1:
	                default:
	                    // 1 = '00 Symb'
	                    $number = $number . ' ' . $symbol;
	                    break;
	            }
	        } else {
	        	$number = number_format(abs($nb), (int)self::$vm1Style[2], self::$vm1Style[3], self::$vm1Style[4]);
	            // Negative number

	            switch (self::$vm1Style[6]) {
	                case 0:
	                    // 0 = '(Symb00)'
	                    $number = '(' . $symbol . $number . ')';
	                    break;
	                case 1:
	                    // 1 = '-Symb00'
	                    $number = '-' . $symbol . $number;
	                    break;
	                case 2:
	                    // 2 = 'Symb-00'
	                    $number = $symbol . '-' . $number;
	                    break;
	                case 3:
	                    // 3 = 'Symb00-'
	                    $number = $symbol . $number . '-';
	                    break;
	                case 4:
	                    // 4 = '(00Symb)'
	                    $number = '(' . $number . $symbol . ')';
	                    break;
	                case 5:
	                    // 5 = '-00Symb'
	                    $number = '-' . $number . $symbol;
	                    break;
	                case 6:
	                    // 6 = '00-Symb'
	                    $number = $number . '-' . $symbol;
	                    break;
	                case 7:
	                    // 7 = '00Symb-'
	                    $number = $number . $symbol . '-';
	                    break;
	                case 9:
	                    // 9 = '-Symb 00'
	                    $number = '-' . $symbol . ' ' . $number;
	                    break;
	                case 10:
	                    // 10 = '00 Symb-'
	                    $number = $number . ' ' . $symbol . '-';
	                    break;
	                case 11:
	                    // 11 = 'Symb 00-'
	                    $number = $symbol . ' ' . $number . '-';
	                    break;
	                case 12:
	                    // 12 = 'Symb -00'
	                    $number = $symbol . ' -' . $number;
	                    break;
	                case 13:
	                    // 13 = '00- Symb'
	                    $number = $number . '- ' . $symbol;
	                    break;
	                case 14:
	                    // 14 = '(Symb 00)'
	                    $number = '(' . $symbol . ' ' . $number . ')';
	                    break;
	                case 15:
	                    // 15 = '(00 Symb)'
	                    $number = '(' . $number . ' ' . $symbol . ')';
	                    break;
	                case 8:
	                default:
	                    // 8 = '-00 Symb'
	                    $number = '-' . $number . ' ' . $symbol;
	                    break;
	            }
	        }

	        //$number = str_replace(' ','&nbsp;', $number); //replace by nbsp to not break lines in invoices
	            	        
	        return $number;
    	}
    	
    }
    
    static function replaceSymbol($symbol)
    {
        if (!self::$currencyReplacements){
			$params = InvoiceHelper::getParams();
	        self::$currencyReplacements = $params->get('currency_char');
		}
    	
    	if (strlen(self::$currencyReplacements) > 0) {
	        $curr_c = explode(",", self::$currencyReplacements);
	        foreach ($curr_c as $ccf) {
	            $cc_fields = explode("|", $ccf);
	            if ($symbol == $cc_fields[0] && isset($cc_fields[1]))
	                $symbol = $cc_fields[1];
	    	}
	    }
	    
	    return $symbol;
    }
    
    //http://cz.php.net/manual/en/function.floor.php#105870
	// This function is backwards compatible with round(), 
	// but provides an optional extra argument which, 
	// if set and not null, allows you to override the default 
	// rounding direction. See examples below. 
	static function roundBetter($number, $precision = 0, $mode = 1, $direction = NULL) {    
	    if (!isset($direction) || is_null($direction)) { 
	        return round($number, (int)$precision/*, $mode*/); 
	    } 
	    
	    else { 
	        $factor = pow(10, -1 * $precision); 
	
	        return strtolower(substr($direction, 0, 1)) == 'd' 
	            ? floor($number / $factor) * $factor 
	            : ceil($number / $factor) * $factor; 
	    } 
	} 
	
	// roundBetterUp(1999, -3) => 2000 
	// roundBetterUp(1001, -3) => 2000 
	static function roundBetterUp($number, $precision = 0, $mode = 1) { 
	    return self::roundBetter($number, $precision, $mode, 'up'); 
	} 
	
	// roundBetterDown(1999, -3) => 1000 
	// roundBetterDown(1001, -3) => 1000 
	static function roundBetterDown($number, $precision = 0, $mode = 1) { 
	    return self::roundBetter($number, $precision, $mode, 'down'); 
	} 
	
	//convert price from one currency to another
	static function convert($price, $fromCurrency, $toCurrency, $exRate = null)
	{
		if ($exRate)
			return $price*$exRate;
		 
		if (!COM_VMINVOICE_ISVM2)
			return $price;
		 
		static $conv;
		if (!isset($conv)){
			$conv = false;
			jimport('joomla.filesystem.file');
			InvoiceHelper::importVMFile('helpers/config.php');
			$converterFile = VmConfig::get('currency_converter_module','convertECB.php');
	
			if (JFile::exists( JPATH_VM_ADMINISTRATOR.'/plugins/currency_converter/'.$converterFile )) {
				$moduleClassname=substr($converterFile, 0, -4);
				require_once(JPATH_VM_ADMINISTRATOR.'/plugins/currency_converter/'.$converterFile);
				if( class_exists( $moduleClassname ))
					$conv = new $moduleClassname();
			}
	
			if (!$conv) {
				if(!class_exists('convertECB'))
					require(JPATH_VM_ADMINISTRATOR.'/plugins/currency_converter/convertECB.php');
				if(!class_exists('convertECB'))
					$conv = new convertECB();
			}
		}
		 
		if (!$conv)
			return false;

		return $conv->convert($price, self::getCode($fromCurrency), self::getCode($toCurrency));
	}
}
?>