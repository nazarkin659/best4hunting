<?php
/*<VMINVOICE_FOLDER>*/


/**
 * PDF Invoicing Solution for VirtueMart & Joomla!
 * 
 * @package   VM Invoice
 * @version   2.0.31
 * @author    ARTIO http://www.artio.net
 * @copyright Copyright (C) 2011 ARTIO s.r.o. 
 * @license   GNU/GPLv3 http://www.artio.net/license/gnu-general-public-license
 */

defined('_JEXEC') or ('Restrict Access');

invoiceHelper::legacyObjects('view');

class VMInvoiceViewOrder extends JViewLegacy
{
    /**
     * @var VMInvoiceModelOrder
     */
    var $model = null;
    /**
     * Available order statuses
     * 
     * @var array
     */
    var $orderStatus = null;
    /**
     * Order informations.
     * 
     * @var TableVmOrder
     */
    var $orderData = null;
    /**
     * Order items/products informations.
     * 
     * @var array
     */
    var $productsInfo = null;
    /**
     * Available vendors
     * 
     * @var array
     */
    var $vendors = null;
    /**
     * Available shippings
     * 
     * @var array
     */
    var $shippings = null;
    /**
     * Available currencies
     * 
     * @var array
     */
    var $currencies = null;
    /**
     * Available payments
     * 
     * @var array
     */
    var $payments = null;
    /**
     * Available countries
     * 
     * @var array
     */
    var $countries;
    /**
     * Available order languages (since VM 2.0.22)
     *
     * @var array
     */
    var $orderLanguages;
    /**
     * Application serve ajax user list calling
     * 
     * @var booelan
     */
    var $userajax = null;
    /**
     * Application serve ajax order detail list calling
     * 
     * @var booelan
     */
    var $orderajax = null;
    /**
     * Called by ajax to recompute prices based on current rules for item (VM2)
     *
     * @var int
     */
    var $recomputeByRulesI = null;
    
    var $newproduct_id = '';
    var $newproduct_name;
    var $newproduct_quantity;
    var $newproduct_object;	//for VM2
    var $showQuantitySelect;
	var $vmorder; //vm2 order object. used for passing into vm plugins.
	var $paymentCurrencies;
	
    function display($tpl = null)
    {
    	//if using date picker, include jquery (?) do it soon, because else VM will include UI before jquery 
    	if (COM_VMINVOICE_ISVM2){
    		invoiceHelper::importVMFile('helpers/config.php',true, true);
    		vmJsApi::jQuery();
    	}
    	
    	InvoiceHelper::setSubmenu(5);
    	
        $this->model = $this->getModel();
        $this->params = InvoiceHelper::getParams();
        

        JRequest::setVar('hidemainmenu', 1);
        
        $this->userajax = JRequest::getString('task') == 'userajax';
        $this->orderajax = JRequest::getString('task') == 'orderajax';
        $this->taxajax = JRequest::getString('task') == 'taxajax';        
		if (JRequest::getString('task') == 'orderajax' AND JRequest::getVar('recompute_rules_i',null)!==null)
			$this->recomputeByRulesI = intval(JRequest::getInt('recompute_rules_i')); // intval makes sure that we reall get an integer, because JRequest::getInt returns string when magic quotes are enabled

        $this->model->orderID = JRequest::getInt('cid');
        
        $this->countries = InvoiceGetter::getCountries();

        //get order data
        $this->orderData = $this->model->getOrderInfo($this->orderajax ? 'orderajax' : ($this->userajax ? 'userajax' : false),JRequest::getInt('override_shipping'),JRequest::getInt('override_payment'));

        if (!$this->orderajax) {
        	
        	$uid = explode(';',JRequest::getVar('uid', '')); //get selected ST/BT address code from ajax
        	if ($uid[0]=='new')	$uid[0]=null;
        	if ($this->orderData->as_guest) $uid = array(null, null, null); //...
        	if (!isset($uid[1])) $uid[1] = null;
        	if (!isset($uid[2])) $uid[2] = null;

            $this->billingData = $this->userajax ? $this->model->getUserInfo('BT', $uid[1], $uid[0]) : $this->model->getOrderUserInfo('BT');
            $this->billingData->userFields = invoiceGetter::getUserFields('B_',$this->billingData);
            
            $this->shippingData = $this->userajax ? $this->model->getUserInfo('ST', $uid[2], $uid[0]) : $this->model->getOrderUserInfo('ST');
            $this->shippingData->userFields = invoiceGetter::getUserFields('S_', $this->shippingData);
            
        	$this->b_states = InvoiceGetter::getStates($this->billingData->country);
        	$this->s_states = InvoiceGetter::getStates($this->shippingData->country);
            
            if ($this->userajax) {
                $this->setLayout('userinfo');
                parent::display($tpl);
                exit();
            }
        }

        //if adding product, get select for new product price (if more prices)
        $this->productPrices = array();
        $this->productPriceSelected = null;
        
        //adding product: VM1
        if (COM_VMINVOICE_ISVM1)
	        if (($pid = JRequest::getInt('pid')) AND is_null(JRequest::getVar('pprice'))){ //passed new product id but no price
	        
	        	 $productPrices = InvoiceGetter::getProductPrices($pid);
	        	 
	        	 if ($productPrices AND count($productPrices)==1){ //only one price - act like it was alerady selected
	        	 	$price = reset($productPrices);
	        	 	JRequest::setVar('pprice', $price->product_price);
	        	 }
	        	 elseif ($productPrices){ //more prices - dont add product, instead display select box
					$groups = InvoiceGetter::getShopperGroups();
					foreach ($productPrices as $price){
		        	 	$group = isset($groups[$price->shopper_group_id]) ? ' ('.$groups[$price->shopper_group_id]->name.')' : null;
		        	 	$this->productPrices[] = JHTML::_('select.option', $price->product_price,  strip_tags(InvoiceCurrencyDisplay::getFullValue($price->product_price, $price->product_currency)).$group);
		        	}
		        	
		        	//reset new product from request (to not add product below)
		        	JRequest::setVar('pid', null); 
		        	JRequest::setVar('pname', null); 
		        	
		        	//for template
		        	$this->newproduct_id = $pid;
		        	if ($product = InvoiceGetter::getProduct($pid))
		        		$this->newproduct_name = $product->product_name;
		        	
		        	//get pre-selected price based on order user id
		        	if (!empty($this->orderData->user_id) && ($groups = InvoiceGetter::getShopperGroup($this->orderData->user_id)))
		        		foreach ($productPrices as $price)
		        			if (isset($groups[$price->shopper_group_id]))
		        				JRequest::setVar('pprice', $price->product_price);
	        	 }
	        	 else { //no prices in VM, very, very strange
	        	 	echo "Notice: No prices for product $pid";
	        	 }
	        }
	        
	    //adding product: VM2
		if (COM_VMINVOICE_ISVM2 AND ($pid = JRequest::getInt('pid'))){

			$notAdd = false;
			$loadProduct = false;
			
			if (is_null(JRequest::getVar('pprice'))){ //no price selected.
				
				$quantityCond = false;
				$productPricesAll = InvoiceGetter::getProductPrices($pid);
				
				if ($productPricesAll AND count($productPricesAll)==1){ //only 1 price globally, no need to ask. put it to request.
					$price = reset($productPricesAll);
					JRequest::setVar('pprice', $price->virtuemart_product_price_id);
				}
				elseif (count($productPricesAll)>1){ //more prices, make select. always make select from all prices to give vendor ability to select price he wants.
					
					$groups = InvoiceGetter::getShopperGroups();
					
					foreach ($productPricesAll as $price){
						$pcs = '';
						$priceTxt = '';
						if ($price->price_quantity_start OR $price->price_quantity_end){
							
							if ($price->override!=0){
								$vendor = invoiceGetter::getVendor(!empty($this->orderData->vendor_id) ? $this->orderData->vendor_id : 1);
								if ($price->override==-1)
									$priceTxt = InvoiceHelper::frontendTranslate('COM_VIRTUEMART_OVERWRITE_PRICE_TAX', null, false, 'com_virtuemart').': '.strip_tags(InvoiceCurrencyDisplay::getFullValue($price->product_override_price, $vendor->vendor_currency));
								elseif ($price->override==1)
									$priceTxt = InvoiceHelper::frontendTranslate('COM_VIRTUEMART_OVERWRITE_FINAL', null, false, 'com_virtuemart').': '.strip_tags(InvoiceCurrencyDisplay::getFullValue($price->product_override_price, $vendor->vendor_currency));
							}
							else 
								$priceTxt = strip_tags(InvoiceCurrencyDisplay::getFullValue($price->product_price, $price->product_currency));
							
							$pcs =  ' ('.$price->price_quantity_start.' - '.$price->price_quantity_end.' '.JText::_('COM_VMINVOICE_PCS').')';
							$quantityCond = true;
						}
						
						$group = isset($groups[$price->virtuemart_shoppergroup_id]) ? ' ('.$groups[$price->virtuemart_shoppergroup_id]->name.')' : null;
						$this->productPrices[] = JHTML::_('select.option', $price->virtuemart_product_price_id,  $priceTxt.$pcs.$group);
					}
					
					$productPricesNow = JRequest::getInt('pquantity') ? InvoiceGetter::getProductPrices($pid, null, JRequest::getInt('pquantity')) : $productPricesAll;
	
					if ($productPricesNow AND count($productPricesNow)==1){ //we have 1 price after knowing quanitty - make it pre-selected in list
						$price = reset($productPricesNow);
						$this->productPriceSelected = $price->virtuemart_product_price_id; //TODO: what about overrides?
					}
					elseif (count($productPricesNow)>1){ //more prices. 
						
						if ($quantityCond AND !JRequest::getInt('pquantity')){ //there is quantity condition and dont know quantity: display quantity box first. before display select.
							$this->showQuantitySelect = true;
							$this->productPrices = array();
						}
						else { //pre-select first or price which fits shopper group
							
							$price = reset($productPricesNow);
							if (!empty($this->orderData->user_id) && ($groups = InvoiceGetter::getShopperGroup($this->orderData->user_id)))
								foreach ($productPricesNow as $priceNow)
									if (isset($groups[$priceNow->virtuemart_shoppergroup_id])){
										$price = $priceNow; break;}
								
							$this->productPriceSelected = $price->virtuemart_product_price_id;
						}
					}
					
					//we will display quantity select or price select now
					$notAdd = true;
					$loadProduct = true;
				}
			}
			
			if (is_null(JRequest::getVar('customPrice')) AND is_null(JRequest::getVar('customPlugin')))
				$loadProduct = true;
			
			if ($loadProduct){
				
				//load product as object with fields. we need it always if not added yet to display name at least.
				$lang = JFactory::getLanguage();
				$lang->load('com_virtuemart', JPATH_ADMINISTRATOR);
				
				InvoiceHelper::importVMFile('models/product.php');
				$model = new VirtueMartModelProduct();
				
				if (is_object($this->newproduct_object = $model->getProduct($pid, TRUE, true, TRUE, 1)))
					$this->newproduct_object = clone $this->newproduct_object; //for all cases
				
				//unset "generic child variant" field. its only making redirect to childs on frontend.
				if (isset($this->newproduct_object->customfieldsCart))
					foreach ($this->newproduct_object->customfieldsCart as $key => $val) 
						if ($val->field_type=='A')
							unset($this->newproduct_object->customfieldsCart[$key]);
			}

			//ok, check if didnt specified custom fields before, if there are some
			if (is_null(JRequest::getVar('customPrice')) AND is_null(JRequest::getVar('customPlugin'))){

				//there are some child variants OR custom fields. dont add, just display that fields.
				if (!empty($this->newproduct_object->customfieldsCart) OR !empty($this->newproduct_object->customsChilds))
					$notAdd = true;
			}
			
			if ($notAdd){ //reset vars to let know prodict will not be added yet
				JRequest::setVar('pid', null);
				JRequest::setVar('pname', null);
				$this->newproduct_id = $pid;
			}
		}
		
        $this->orderStatus = InvoiceGetter::getOrderStates();
        $this->vendors = InvoiceGetter::getVendors();
        $this->taxRates = InvoiceGetter::getTaxRates();
        $this->nbDecimal = InvoiceCurrencyDisplay::getDecimals($this->orderData->order_currency);
        
        $orderItemIds = null; //null !== array()! if deleted all products, must be empty array
        if ($this->orderajax)
        	$orderItemIds = JRequest::getVar('order_item_id', array(), 'default', 'array');
        
        $this->productsInfo = $this->model->getProductsInfo($orderItemIds, JRequest::getInt('pid'), JRequest::getVar('pname'), JRequest::getVar('pprice'), JRequest::getVar('pquantity'),$this->orderData);
        
        $addedProduct = (JRequest::getVar('pid') OR JRequest::getVar('pname')); //we need it for below, to not unser calcRules for new product
        
        //if added product now, reset requests 
        if (JRequest::getVar('pid')){
        	JRequest::setVar('pid', null);
        	JRequest::setVar('pname', null);
        	JRequest::setVar('pquantity', null);
        	JRequest::setVar('pprice', null);
        	$this->newproduct_object = null;
        }
        
        if ($this->orderajax) { //rewrite products parameters from db by request ones

        	$calcVars = array('virtuemart_order_calc_rule_id', 'calc_value', 'calc_mathop', 'calc_currency', 'calc_rule_name', 'calc_kind', 'calc_amount'); //calc_amount no. why? used for general order, where is only amount (?)
        	
            $count = count($this->productsInfo);

            //i: poradove cislo .. 0,1,2,..
            //j: cislo z postu. v pripade, ze jsme nejaky smazali, je tam mezera: 0,1,3,4..

            if ($addedProduct)
            	array_push($orderItemIds, ''); //itr can be blabnk, mportant is there is new element
            
            $i = 0;
            foreach ($orderItemIds as $j => $orderItemId) { //$i is real key of products, but not POŘADOVÉ
            
                $product = &$this->productsInfo[$i];

                //add values from POST. those are with keys J. 
                $params = get_object_vars($product);
                foreach ($params as $param => $value)
                    if (isset($_REQUEST[$param][$j]))
                        $product->$param = htmlspecialchars_decode($_REQUEST[$param][$j]);
                
                //now add also attributes like rows in VM2. parse back to json.
                if (COM_VMINVOICE_ISVM2 AND ($attrs = VMInvoiceModelOrder::addAttributesFromRequestToJson($j, JRequest::get('post')))!==null) //only when presented in post. (can be adding new product)
                	$product->product_attribute = $attrs;
                
                //rewrite calc rules by POST also
                if (COM_VMINVOICE_ISVM2){

                	//dont reset if added new product, because it is not in POST yet,
                	//butr it can be also case if DELETED ALL rules.
                	//new product is always on end.

                	if (!(($addedProduct) AND $i == $count-1))
                		$product->calcRules = array(); //reset

                	foreach ($calcVars as $calcVar){
                		
                		$array = JRequest::getVar($calcVar, array(),'post','array');
                		
                		if (isset($array[$j]))
	                		foreach ($array[$j] as $ruleJ => $value)
		                	{
		                		if (!isset($product->calcRules[$ruleJ]))
		                			$product->calcRules[$ruleJ] = new stdClass();
		                		
		                		$product->calcRules[$ruleJ]->$calcVar = $value;
		                	}
                	}
                }
                
                $i++;
            }
                        
            //rewrite also order general, shipping and payment calculation rules
            if (COM_VMINVOICE_ISVM2){
            	
            	$this->orderData->shippingCalcRules = array();
            	$this->orderData->paymentCalcRules = array();
            	$this->orderData->orderCalcRules = array();
            	
	            foreach ($calcVars as $calcVar){
	            	
	            	$array = JRequest::getVar($calcVar, array(),'post','array');
	            	
	            	if (isset($array[-1]))
	            		foreach ($array[-1] as $ruleJ => $value) {
	            		if (!isset($this->orderData->orderCalcRules[$ruleJ]))
	            			$this->orderData->orderCalcRules[$ruleJ] = new stdClass();
	            		$this->orderData->orderCalcRules[$ruleJ]->$calcVar = $value;
	            	}
	            	
	            	if (isset($array[-2]))
	            		foreach ($array[-2] as $ruleJ => $value) {
		                	if (!isset($this->orderData->shippingCalcRules[$ruleJ]))
		                		$this->orderData->shippingCalcRules[$ruleJ] = new stdClass();
		                	$this->orderData->shippingCalcRules[$ruleJ]->$calcVar = $value;
		                }
		                
		               if (isset($array[-3]))
		               	foreach ($array[-3] as $ruleJ => $value) {
		               		if (!isset($this->orderData->paymentCalcRules[$ruleJ]))
		               			$this->orderData->paymentCalcRules[$ruleJ] = new stdClass();
		               		$this->orderData->paymentCalcRules[$ruleJ]->$calcVar = $value;
		               }
	            } 
            }
        }

        $this->model->recomputeOrder($this->productsInfo, $this->orderData, $this->orderajax, $this->recomputeByRulesI);
        
        if (COM_VMINVOICE_ISVM2){
        	$this->shippings = InvoiceGetter::getShippingsVM2();
        	$this->payments = InvoiceGetter::getPayments();
        	
        	//make array of method -> payment currency. will be used for default pre-select.
        	$this->paymentCurrencies = array();
        	foreach ($this->payments as $payment)
        		$this->paymentCurrencies[(int)$payment->id] = (int)@$payment->payment_params->payment_currency;
        	
        	//select for order language
			if (InvoiceHelper::vmCersionCompare('2.0.22') >= 0){
	        	$this->orderLanguages = InvoiceGetter::getTranslatableLanguages();
	        	array_unshift($this->orderLanguages, (object)array('lang_code' => '', 'title' => '-')); //add empty option
	        	if ($this->orderData->order_language AND !isset($this->orderLanguages[$this->orderData->order_language])) //if current value not in list, add it
	        		array_unshift($this->orderLanguages, (object)array('lang_code' => $this->orderData->order_language, 'title' => $this->orderData->order_language));
			}
		}
        
        $this->currencies = InvoiceGetter::getCurrencies();

        
        foreach ($this->currencies as $currency)
        	$currency->name = JText::sprintf('COM_VMINVOICE_CURRENCY_SHORT_INFO', $currency->name, COM_VMINVOICE_ISVM2 ? $currency->symbol : $currency->id);
        array_unshift($this->currencies, JHTML::_('select.option', '', JText::_('COM_VMINVOICE_SELECT'), 'id', 'name'));
        
        $this->showWeight = intval($this->params->get('show_order_weight', '0')) == 1;
        if ($this->showWeight)
        	$this->orderWeight = InvoiceGetter::getOrderWeight($this->productsInfo);
        
        if ($this->orderajax) {
            $this->setLayout('products');
            parent::display($tpl);
            
            // Add JS created by VM plugins
            $doc = JFactory::getDocument();
            if (isset($doc->_script['text/javascript'])) {
                echo '<script type="text/javascript">';
                echo $doc->_script['text/javascript'];
                echo '</script>';
            }
            exit();
        }

        if (COM_VMINVOICE_ISVM1){
        	$this->shippings = InvoiceGetter::getShippingsVM1($this->orderData->user_info_id, $this->orderData->order_currency, $this->orderData->user_id, $this->model->overal_weight_vm1);
        	$this->payments = InvoiceGetter::getPayments();
    	}
    	
    	if (COM_VMINVOICE_ISVM2 AND $this->orderData->order_id){
	    	InvoiceHelper::importVMFile('helpers/vmmodel.php');
	    	$orderModel = VmModel::getModel('orders');
	    	$this->vmorder = $orderModel->getOrder($this->orderData->order_id);
    	}
    	
    	$this->orderParams = invoicegetter::getOrderParams($this->orderData->order_id);
    	
        // Load VM JavaScript
        if (COM_VMINVOICE_ISVM2) {
            $doc = JFactory::getDocument();
            $doc->addScript(JUri::root().'/components/com_virtuemart/assets/js/vmprices.js');
        }
        
        parent::display($tpl);
    }
    
    function hiddenFieldsFromVar($variable, $name)
    {
    	if (is_null($variable))
    		return ;
    	
    	if (is_object($variable))
    		$variable = (array)$variable;
    	
    	if (!is_array($variable))
    		return '<input type="hidden" name="'.$name.'" value="'.htmlspecialchars($variable).'" />'."\r\n";
    	
    	$ret = '';
    	foreach ($variable as $key => $val)
    		$ret.=$this->hiddenFieldsFromVar($val, $name.'['.$key.']');
    	return $ret;
    }
    
    //try to get custom field input for particular attribute
    protected function getAttributeInput($key, $val, $i, $j)
    {
    	static $dispatcher;
    	
    	if (!COM_VMINVOICE_ISVM2)
    		return false;
    	
    	$db = JFactory::getDBO();
    	$db->setQuery('SELECT * FROM #__virtuemart_product_customfields AS CF
			 JOIN #__virtuemart_customs AS C ON (CF.virtuemart_custom_id = C.virtuemart_custom_id)
			  WHERE CF.virtuemart_customfield_id = '.$db->Quote($key));
    	 
    	//TODO: hm, a co klasický dropbox ktery umi vm nativne?
    	//slo by to, jen bychom museli nacist atributy ktere ma nastaveny dany PRODUKT
    	//a pak se k tomu chvovat jako klasickemu selectu.
    	
    	//jak na to?
    	
    	$field = $db->loadObject();
    	if (!$field) //only plugin fields now (?)
    		return false; 

    	if ($field->field_type!='E' AND $field->field_type!='V')
    		return false;
    	 
    	//copy of getProductCustomsFieldCart
    	$db->setQuery('SELECT field.`virtuemart_product_id`, `custom_params`,`custom_element`, field.`virtuemart_custom_id`,
				field.`virtuemart_customfield_id`,field.`custom_value`, field.`custom_price`, field.`custom_param`
				FROM `#__virtuemart_customs` AS C
				LEFT JOIN `#__virtuemart_product_customfields` AS field ON C.`virtuemart_custom_id` = field.`virtuemart_custom_id`
				Where `virtuemart_product_id` =' . (int)$field->virtuemart_product_id.' 
    			and is_cart_attribute = 1 and C.`virtuemart_custom_id`=' . (int)$field->virtuemart_custom_id.' ORDER BY field.`ordering`');
    	 
    	$field->options = array();
    	foreach ($db->loadObjectList () as $option)
    		$field->options[$option->virtuemart_customfield_id] = $option;

    	//plugin field, call plugin (not for options? not. because they are old. yes. i dont know.)
    	if ($field->field_type=='E'){
    		
	    	if (!isset($dispatcher)){
		    	JPluginHelper::importPlugin('vmcustom');
		    	JPluginHelper::importPlugin('vmcalculation');
		    	$dispatcher = JDispatcher::getInstance ();
	    	}

	    	//get display html
	    	$dispatcher->trigger('plgVmOnDisplayProductVariantFE', array($field, &$j, &$field));
    	}
    	elseif ($field->field_type == 'V') {
    		
    		InvoiceHelper::importVMFile('models/customfields.php');
    		InvoiceHelper::importVMFile('helpers/currencydisplay.php');
    		
    		$product = $field->virtuemart_product_id ? InvoiceGetter::getProduct($field->virtuemart_product_id) : null;
    		$calculator = calculationHelper::getInstance ();
    		$calculator->productCurrency = !empty($product->product_currency) ? $product->product_currency:$calculator->productCurrency;
    		$currency = CurrencyDisplay::getInstance ();
    		
    		foreach ($field->options as $productCustom) {
    			$price = VirtueMartModelCustomfields::_getCustomPrice($productCustom->custom_price, $currency, $calculator);
    			$productCustom->text = JText::_($productCustom->custom_value) . ' (' . $price.')';
    		}
    		 
    		$field->display = '<input type="hidden" name="product_attribute_name['.$i.']['.$j.']" value="'.$field->custom_title.'" />'; //need to pass also attribute name
    		$field->display .= JHTML::_('select.genericlist', $field->options, 'customPlugin['.$key.']', '', 'custom_value', 'text'); //name will be replaced below
    	}

    	if (empty($field->display))
    		return false;
    	
    	jimport('joomla.string.string'); //?
    	if (!class_exists('JString') OR !is_callable('JString::str_ireplace'))
    		return false;
    	
    	//if we canot find and properly replace all inputs with current values, do not display this field!
    	//because we cannot lose any saved value
    	foreach ($this->getRequiredInputs($val) as $inputKey => $value){ 
    	
    		$realKey = 'customPlugin['.$key.']'.$inputKey;
    		
    		if ($field->field_type == 'E') //plugins have special name
    			$newKey = 'product_attribute_custom['.$i.']['.$j.']'.$inputKey; 
			else //name for simple VM name+value attr
				$newKey = 'product_attribute_value['.$i.']['.$j.']'.$inputKey; 
			
    		//vm value fix
    		if (preg_match('#^\s*<span class="c[ou]stumTitle">(.*)</span>\s*<span class="c[ou]stumValue" >(.*)</span>\s*$#is', $value, $matches))
    			$value = $matches[2];

    		//input
    		if (preg_match('#<input [^>]*name="'.preg_quote($realKey, '#').'"[^>]*>#isuU', $field->display, $matches))
    		{ 
    			//TODO: radio and checkboxes
    			if (!preg_match('#type\s*=\s*"(text|hidden)"#is', $matches[0])) //only text/hidden now, no radio or checkbox
    				return false; 

    			//replace value
    			$replacedInput = preg_replace('#(\svalue\s*=\s*")[^"]*(")#isU', '${1}'.JString::str_ireplace('"', "'", $value).'${2}', $matches[0], -1, $count);
    			if ($count!=1) //value not replaced!
    				return false;

    			$field->display = JString::str_ireplace($matches[0], $replacedInput, $field->display); //ok, replace input with updated value
    		}
    		//select
    		elseif (preg_match('#(<select [^>]*name="'.preg_quote($realKey, '#').'"[^>]*>)(.+)(<\s*\/\s*select\s*>)#isuU', $field->display, $matches))
    		{
    			//ok, we have select, now select proper value (and un-select default one)
    			
    			//remove selected, if some
    			$options = preg_replace('#\sselected\s*=\s*("selected"|selected)#is', '', $matches[2]);
    			$options = preg_replace('#(<option[^>]\s)selected([\s>])#is', '$1$2', $options);
    			
    			//select proper one
    			$options = preg_replace('#(<option[^>]*\svalue\s*=\s*"'.preg_quote($value, '#').'"[^>]*)>#is', '${1} selected="selected">', $options, -1, $count);
    			if ($count!=1) //option not selected (if not exists for example)!
    				return false;
    			
    			$field->display = JString::str_ireplace($matches[0], $matches[1].$options.$matches[3], $field->display); //replace select with updated options
    		}
    		//textarea
    		elseif (preg_match('#(<textarea [^>]*name="'.preg_quote($realKey, '#').'"[^>]*>)(.+)(<\s*\/\s*textarea\s*>)#isuU', $field->display, $matches))
    			$field->display = JString::str_ireplace($matches[0], $matches[1].$value.$matches[3], $field->display);
    		else
    			return false;

    		//also update name
    		$field->display = JString::str_ireplace($realKey, $newKey, $field->display); 
    		if (JString::strpos($field->display, $newKey)===false) //name not replaced ($count parameter in str_ireplacenot works)
    			return false;
    	}

    	return $field;	
    }
    
    //we have $val from attributes field
    //and we need to extract inputs we must have to fill all these values, so we will not lose any
    private function getRequiredInputs($val, $pathToVal = array())
    {
    	$inputs = array();
    		
    	if (is_array($val) OR is_object($val)){
    		foreach ((array)$val as $key1 => $val1)
    			$inputs = array_merge($inputs, $this->getRequiredInputs($val1, array_merge($pathToVal, array($key1)))); //parse next level, pass updated path
    	}
    	else{ //this var is "final". we need add it to final array
    			
    		$path = array();
    		foreach ($pathToVal as $node)
    			$path[] = '['.$node.']';
    			
    		$inputs[implode('', $path)] = $val; //we rached final value, add it to final array
    	}
    		
    	return $inputs;
    }
}

?>