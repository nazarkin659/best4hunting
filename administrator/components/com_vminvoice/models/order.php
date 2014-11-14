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

defined('_JEXEC') or die('Restrict Access');

invoiceHelper::legacyObjects('model');

class VMInvoiceModelOrder extends JModelLegacy
{
    /**
     * Order ID
     * 
     * @var int
     */
    var $orderID = null;

    /**
     * Complete order store.
     * 
     * @param array $data request data
     * @return int order ID
     */
    function save(&$data)
    {
    	$params = InvoiceHelper::getParams();
    	
    	$dispatcher = JDispatcher::getInstance();
       	$result = $dispatcher->trigger('onVMInvoiceBeforeOrderSave', array(&$this, &$data)); //custom plugins support.
       	if (in_array(false, $result, true)) //some plugin return false
       		return false;
       	
	  	$tPrefix = COM_VMINVOICE_ISVM2 ? 'vm2' : 'vm1';
    	$orderIdCol = COM_VMINVOICE_ISVM2 ? 'virtuemart_order_id' : 'order_id';
    	$userInfoCol = COM_VMINVOICE_ISVM2 ? 'virtuemart_userinfo_id' : 'user_info_id';
    	$newUser = false;
        if (empty($data['vendor'])) //guess vendor id, if not set
			if (count($vendors = InvoiceGetter::getVendors())==1)
				$data['vendor']=$vendors[0]->id;
		
		if (empty($data['billing_is_shipping'])) //checkbox
			$data['billing_is_shipping'] = 0;
			
		$data['as_guest'] = (COM_VMINVOICE_ISVM2 AND !empty($data['as_guest']));
		if ($data['as_guest']) //as guest .. reset user if for all cases
			$data['user_id'] = 0;
			
		//create new Joomla! user, if not selected "as guest". if creation fails, store order anyway, but don't update/insert vm_user_info
    	if (!$data['as_guest'] AND !$data['user_id']){  
    		if (!($data['user_id'] = $this->newJUser($data))) //create Joomla! user
    			$data['user_id'] = null;
    		else
    			$newUser = true; //success
    	}
    	
    	//check if user "registered" also in VM. if create_jusers is disabled but user_id is provided, create vmusers record for it (for consistency i guess)
    	if ($data['user_id']){
	    	$user = JFactory::getUser($data['user_id']);
			if ($this->newVMShopper($data, $user) AND !$newUser){ //if ONLY shopper created, display message about it
				$mainframe = JFactory::getApplication();
				$mainframe->enqueueMessage(JText::_('COM_VMINVOICE_SHOPPER_CREATED'));
			}			
    	}
    	
    	//store order
        $vmorder = $this->getTable($tPrefix.'order');
        $vmorder->bind($data,'order_status');  //IMPORTANT: NOT change status here. instead use updateStatus fnc at bottom of this function
        
        if (!$vmorder->$orderIdCol)
        	$vmorder->order_status = 'P'; //for new orders, add them as PENDING. if selected f.e. confirmed, plugins adding points can be called now (?)
        	
        if (!$vmorder->store()) //store
        	JError::raiseWarning(0, 'Cannot store order. '.$vmorder->getError());
                
 		if (!$data['order_id'] = $vmorder->$orderIdCol){ //if is created new order, write back it's id to data property
 			JError::raiseWarning(0,'Order table not stored');
 			return false;
 		}
 		
 		if ($vmorder->order_number) //for new order
 			$data['order_number'] = $vmorder->order_number;
 			
 		//store order note
 		if ($params->get('allow_order_notes', 0)){
 			$orderParams = invoicegetter::getOrderParams($data['order_id']);
 			$orderParams->order_note = $data['order_note'];
 			
 			$db = JFactory::getDBO();
 			$db->setQuery('SELECT count(*) FROM `#__vminvoice_mailsended` WHERE order_id = '.(int)$data['order_id']);
 			if ($db->loadResult()>0)
 				$db->setQuery('UPDATE `#__vminvoice_mailsended` SET params = '.$db->Quote(json_encode($orderParams)).' WHERE order_id = '.(int)$data['order_id']);
 			else
 				$db->setQuery('INSERT INTO `#__vminvoice_mailsended` (`order_id`, `invoice_no`, `invoice_prefix`, `params`) VALUES (
 			'.(int)$data['order_id'].', null, null, '.$db->Quote(json_encode($orderParams)).')');
 			
 			if (!$db->query())
 				JError::raiseWarning(0,'Cannot save order params. '.$db->getErrorMsg());
 		}
 		
 		

 		//store calculation rules (VM2). for items its called in orderitems table
 		if (COM_VMINVOICE_ISVM2){
 			
 			$tableCalcRules = JTable::getInstance('Vm2OrderCalcRules', 'Table'); //for order general
 			if (!$tableCalcRules->storeRulesPerItem(-1, null, $data))
 				JError::raiseWarning(0, 'Cannot update calculation rules for general order. '.implode("\r\n", $tableCalcRules->getErrors()));
 			
 			$tableCalcRules = JTable::getInstance('Vm2OrderCalcRules', 'Table'); //for shipment
 			if (!$tableCalcRules->storeRulesPerItem(-2, null, $data))
 				JError::raiseWarning(0, 'Cannot update calculation rules for shipment. '.implode("\r\n", $tableCalcRules->getErrors()));
 			
 			$tableCalcRules = JTable::getInstance('Vm2OrderCalcRules', 'Table'); //for payment
 			if (!$tableCalcRules->storeRulesPerItem(-3, null, $data))
 				JError::raiseWarning(0, 'Cannot update calculation rules for payment. '.implode("\r\n", $tableCalcRules->getErrors()));
 		}
 		

        //delete coupon, if set so
        if (!empty($data['coupon_code']) AND !empty($data['coupon_delete'])) 
        	$this->deleteCoupon($data['coupon_code']);

        //store items
        $vmorderitem = $this->getTable($tPrefix.'orderitem');  
        if (!$vmorderitem->store($data))
        	JError::raiseWarning(0, 'Cannot store ordered items. '.$vmorderitem->getError());
 
        if (COM_VMINVOICE_ISVM1){ //store payment (VM1)
	        $vmorderpayment = $this->getTable($tPrefix.'orderpayment');
	        $vmorderpayment->bind($data);
	        if (!$vmorderpayment->store())
	       		JError::raiseWarning(0, 'Cannot store order payment. '.$vmorderpayment->getError());
        }
        
        if (COM_VMINVOICE_ISVM2 && $data['payment_method_id']) { //store payment info (VM2)

        	//get name of plugin table used for store payment info
        	$this->_db->setQuery('SELECT payment_element FROM #__virtuemart_paymentmethods WHERE virtuemart_paymentmethod_id = '.(int)$data['payment_method_id']);

        	if ($element = $this->_db->loadResult()){
        		
        		include_once JPATH_ADMINISTRATOR . '/components/com_vminvoice/tables/vm2orderpayment.php';
	        	$vmorderpayment = new TableVm2OrderPayment($this->_db,$element); //cannot use $this->getTable because of constructor param
	        	if (!$vmorderpayment->store($data, $vmorder))
	        		JError::raiseWarning(0, 'Cannot store order payment. '.$vmorderpayment->getError());
        	}
        }
        
        if (COM_VMINVOICE_ISVM2 && $data['shipment_method_id']) { //store shipment info (VM2)

        	//get name of plugin table used for store payment info
        	$this->_db->setQuery('SELECT shipment_element FROM #__virtuemart_shipmentmethods WHERE virtuemart_shipmentmethod_id = '.(int)$data['shipment_method_id']);

        	if ($element = $this->_db->loadResult()){
        		
        		include_once JPATH_ADMINISTRATOR . '/components/com_vminvoice/tables/vm2ordershipment.php';
	        	$vmordershipment = new TableVm2OrderShipment($this->_db,$element); //cannot use $this->getTable because of constructor param
	        	if (!$vmordershipment->store($data))
	        		JError::raiseWarning(0, 'Cannot store order shipment. '.$vmordershipment->getError());
        	}
        }

        //store default user info / get address type name. only if not guest.
        if ($data['user_id']) {

        	$vmuserinfo = $this->getTable($tPrefix.'userinfo');
        	$addressTypes = array('S_' => 'ST' , 'B_' => 'BT');
        	
        	foreach ($addressTypes as $requestPrefix => $addressTypeCode) {

        		if ($addressTypeCode=='ST' && !empty($data['billing_is_shipping']))
        			continue; //not update default shipping user info or get address type name if billing = shipping
        		
        		//update user info if not checked at js prompt OR user dont have any info yet (OR new user)
 		        if ($data['update_userinfo']==1 || $newUser || !InvoiceGetter::getUserInfo(null, $data['user_id'], 'BT')){ 
			        $vmuserinfo->bind($data,$addressTypeCode);
			        if (!$vmuserinfo->store())
			        	JError::raiseWarning(0, 'Cannot store default user info. '.$vmuserinfo->getError());
		        }
		        else //else bind just primary key to load address_type_name
		        	$vmuserinfo->$userInfoCol = $data[$requestPrefix.'user_info_id']; 

		        if ($vmuserinfo->load()) //get used address_type_name (if user info is set), for adding that vmorderuserinfo field if changing shipping address
		        	$data[$requestPrefix.'address_type_name'] = isset($vmuserinfo->address_type_name) ? $vmuserinfo->address_type_name : null;
        	}      
        }

        //store user info for order (if b = s it is not performed in table fnc)
        $vmorderuserinfo = $this->getTable($tPrefix.'orderuserinfo');
        if (!$vmorderuserinfo->store($data))
        	JError::raiseWarning(0, 'Cannot store order user info. '.$vmorderuserinfo->getError());
        		
 		//update order state by VM function (now, because we need userinfo stored)
        //NOT apply to all items. items table for VM2 will call vm functions and trigger plugins on its own.
        $this->updateState($vmorder->$orderIdCol,$data['status'],isset($data['notify']) ? $data['notify'] : 'N', '', false, false); 
        
        //create Invoice Number if we changed state, have own numbering and record not created yet
  		InvoiceHelper::getInvoiceNo($data['order_id']);
        
  		$result = $dispatcher->trigger('onVMInvoiceAfterOrderSave', array(&$this, &$data, $vmorder->$orderIdCol)); //custom plugins support
  		
        return $vmorder->$orderIdCol;
    }

    /**
     * Create new Joomla! user from post data
     * 
     * @param array $postData
     */
    function newJUser($postData)
    {			
    	//load language to get user errors translated
		$language = JFactory::getLanguage();
		$language->load('com_users', JPATH_ADMINISTRATOR); 
		$language->load('com_users', JPATH_SITE);
		$language->load('joomla', JPATH_ADMINISTRATOR);
		$language->load('joomla', JPATH_SITE);
		
		//http://blog.amdaris.com/how-to-create-users-programatically-in-joomla-1-6-and-1-5/
		if (COM_VMINVOICE_ISJ16){ //J1.6 and more
			
			jimport('joomla.application.component.helper');
			$config = JComponentHelper::getParams('com_users');
			// Default to Registered.
			$defaultUserGroup = $config->get('new_usertype', 2);
			
			$user = new JUser();
			$user->name = $postData['B_first_name'].' '.$postData['B_last_name'];

			//get username
			$defusername = JFilterOutput::stringURLUnicodeSlug($postData['B_first_name'].''.$postData['B_last_name']);
			$user->username = $defusername;
			$i=1;
			while(1){ //johnsmith1,2,3...
				$this->_db->setQuery('SELECT count(*) FROM #__users WHERE username = '.$this->_db->Quote($user->username));
				if ($this->_db->loadResult()>0) $user->username = $defusername.($i++); else break;
			}
			
			$user->email = $postData['B_email'];
			$user->guest = false;
			$user->password = md5($postData['B_email']);
			$user->password_clean = $postData['B_email'];
			$user->groups = array($defaultUserGroup);
			
			$result = false;
			$userId = null;
			
			//now, INSTEAD firing save() method (which would invoke plugins to send e-mails to new users and we CANt simulate editing since we need add..
			//simulate save() method. See original JUser::save method.
				
			// Create the user table object
			$table			= $user->getTable();
			//$user->params	= '';
			$table->bind($user->getProperties());
				
			// Allow an exception to be thrown.
			try
			{
				// Check and store the object.
				if (!$table->check()) {
					$user->setError($table->getError());
				}
				else
				{
					$my = JFactory::getUser();
	
					// Get the old user
					$oldUser = new JUser();
	
					$iAmSuperAdmin	= $my->authorise('core.admin');
		
					// We are only worried about edits to this account if I am not a Super Admin.
					if ($iAmSuperAdmin != true) {
						// Check if the new user is being put into a Super Admin group.
						foreach ($user->groups as $key => $groupId)
						{
							if (JAccess::checkGroup($groupId, 'core.admin')) {
								throw new Exception(JText::_('JLIB_USER_ERROR_NOT_SUPERADMIN'));
							}
						}
					}
		
					// Fire the onUserBeforeSave event.
					JPluginHelper::importPlugin('user');
					$dispatcher = JDispatcher::getInstance();
					$result = $dispatcher->trigger('onUserBeforeSave', array($oldUser->getProperties(), false, $user->getProperties()));
					
					if (!in_array(false, $result, true)) {
						
						// Store the user data in the database
						if (!($result = $table->store())) {
							throw new Exception($table->getError());
						}
						
						$userId = $table->get('id');
		
						// Fire the onAftereStoreUser event
						$dispatcher->trigger('onUserAfterSave', array($user->getProperties(), false, $result, $user->getError()));
					}
				}
			}
			catch (Exception $e)
			{
				JError::raiseWarning(0,$e->getMessage());
				$result = false;
			}
		}
		else{ //J! 1.5
			$user = new JUser();
			$user->name = $postData['B_first_name'].' '.$postData['B_last_name'];
			
			//get username
			$defusername = strtolower(preg_replace('#[ ;:?!"\']#','',$postData['B_first_name'].''.$postData['B_last_name'])); //1.5 dont have stringURLUnicodeSlug
			$user->username = $defusername;
			$i=1;
			while(1){ //johnsmith1,2,3...
				$this->_db->setQuery('SELECT count(*) FROM #__users WHERE username = '.$this->_db->Quote($user->username));
				if ($this->_db->loadResult()>0) $user->username = $defusername.($i++); else break;
			}
			
			$user->email = $postData['B_email'];
			$user->usertype = 'Registered';
			$user->guest = 0;
			$user->password = md5($postData['B_email']);	
			$acl =& JFactory::getACL();
			$user->gid = $acl->get_group_id( '', $user->usertype, 'ARO' );
			$user->registerDate = JFactory::getDate()->toMySQL();
			
			$result = $user->save();
			$userId = $user->id;
		}
		
    	if ($result && $userId){
			$mainframe = JFactory::getApplication();
			$mainframe->enqueueMessage(JText::sprintf('COM_VMINVOICE_USER_CREATED',$user->username, $postData['B_email']));
    		return $userId;}
		else {
			JError::raiseWarning(500,JText::_('COM_VMINVOICE_USER_NOT_CREATED').': '.JText::_($user->getError()),$user->getError());
			return false;
		}
    }
    
    /**
     * "Register" new shopper to VM, if not "registered" yet. Not billing/shipping info, that is saved with order.
     * 
     * @param array $postData	post array
     * @param object $user		user object
     */
    function newVMShopper($postData, $user)
	{
		$shopperCreated = null;
		
		if (!empty($postData['vendor']))
		{
			$db = JFactory::getDBO();

			//add user to default shopper group, if not having group yet
			if (COM_VMINVOICE_ISVM2)
				$db->setQuery('SELECT count(*) FROM #__virtuemart_vmuser_shoppergroups WHERE virtuemart_user_id='.(int)$user->id);
			else
				$db->setQuery('SELECT count(*) FROM #__vm_shopper_vendor_xref WHERE user_id='.(int)$user->id).' AND vendor_id='.(int)$postData['vendor'];
			
			if ($db->loadResult()==0)
			{
				$groupId = null;
				if ($postData['shopper_group']){ //passed group id: check if is in vendor's groups
					$groups = invoiceGetter::getShopperGroups($postData['vendor']);
					if (isset($groups[$postData['shopper_group']]))
						$groupId = $postData['shopper_group'];
				}
				
				if (!$groupId) //not - search default group
					$groupId = invoiceGetter::getDefaultShopperGroup($postData['vendor']);
				
				if ($groupId){
					if (COM_VMINVOICE_ISVM2)
						$db->setQuery('INSERT INTO #__virtuemart_vmuser_shoppergroups (virtuemart_user_id,virtuemart_shoppergroup_id) VALUES ('.(int)$user->id.','.(int)$groupId.')');
					else
						$db->setQuery('INSERT INTO #__vm_shopper_vendor_xref (user_id,vendor_id,shopper_group_id,customer_number) VALUES ('.(int)$user->id.','.(int)$postData['vendor'].','.(int)$groupId.',\'\')');
				
					if (!$db->query()){
						JError::raiseWarning(0,'User was not assigned to default shopper group '.$groupId, $db->getErrorMsg());}
					else
						$shopperCreated = true;
				}
			}

			//vm2: create also new vmusers record, if not exists yet
			if (COM_VMINVOICE_ISVM2)
			{
				$db->setQuery('SELECT count(*) FROM #__virtuemart_vmusers WHERE virtuemart_user_id='.(int)$user->id);
				if ($db->loadResult()==0)
				{
					$currentUser = JFactory::getUser();
					
					$data = array(
						'virtuemart_user_id' => (int)$user->id, 
						'virtuemart_vendor_id' => 0, //VM stores here 0
						'customer_number' => md5($user->username),  
						'perms' => 'shopper', 
						'created_on' => gmdate('Y-m-d H:i:s'), 
						'created_by' => (int)$currentUser->id, 
						'customer_number_bycore' => 1 //don't know what this is, but it is required by plgVmShopperIstraxx_snumbers
					);

					JPluginHelper::importPlugin('vmshopper');
					$dispatcher = JDispatcher::getInstance();
					$dispatcher->trigger('plgVmOnUserStore',array(&$data));
						
					$db->setQuery('INSERT INTO #__virtuemart_vmusers (virtuemart_user_id, virtuemart_vendor_id, customer_number, perms, created_on, created_by)
					VALUES ('.(int)$data['virtuemart_user_id'].','.(int)$data['virtuemart_vendor_id'].','.$db->Quote($data['customer_number']).
						','.$db->Quote($data['perms']).','.$db->Quote($data['created_on']).','.(int)$data['created_by'].')');
	
					if (!$db->query()){
						JError::raiseWarning(0,'Not created new record in virtuemart_vmusers table ', $db->getErrorMsg());}	
					else{
						$shopperCreated = true;
						$dispatcher->trigger('plgVmAfterUserStore',array($data));
					}
				}
			}
		}

		return $shopperCreated;
	}
	
    function deleteCoupon($code)
    {
    	$db = JFactory::getDBO();
    	if (COM_VMINVOICE_ISVM2)
    		$db->setQuery('DELETE FROM `#__virtuemart_coupons` WHERE `coupon_code` = '.$db->Quote($code).' LIMIT 1');
    	else
        	$db->setQuery('DELETE FROM `#__vm_coupons` WHERE `coupon_code` = '.$db->Quote($code).' LIMIT 1');
        return $db->query();
    }
    
    function getAjaxList($filter, $type)
    {
        return call_user_func(array($this , 'getAjax' . ucfirst($type) . 'List'), $filter);
    }

    function getAjaxNewproductList($filter)
    {
    	//TODO: vracet az po case, ne po zmacknuti!!! takhle moc requestu. taky omezit pocet vysledku.
    	return InvoiceGetter::getAjaxProductList($filter);
    }

    function getAjaxUserList($filter)
    {
    	if (trim($filter)=='')
    		$users = array();
    	else
    		$users = invoiceGetter::getAjaxUserList($filter);
        
        $newUser = new stdClass();
        $newUser->id = 'new;;';
        $newUser->name = JText::_('COM_VMINVOICE_NEW_CUSTOMER');
        
        array_push($users,$newUser);
        
        return $users;
    }
    
    /**
     * Get current items array
     * 
     * @param array 	$orderItemIds		array of posted order_item_id
     * @param int		$productId			new item id		(eigter one or other is posted)
     * @param string	$productName		new item name	(eigter one or other is posted)
     * @param int		$productPrice		new item price	
     * @param int		$productQuantity	new item quantity	
     * @param object	$order				current order object
     */
    function getProductsInfo($orderItemIds = null, $productId = null, $productName = null, $productPrice = null, $productQuantity = null, &$order = null)
    {
    	if (COM_VMINVOICE_ISVM2){
    		InvoiceHelper::importVMfile('helpers/calculationh.php'); //NOTE: přepočítávání se liší! třeba o 4 desetiny! asi proto, že exchange rate se v čase mění.
    		InvoiceHelper::importVMfile('helpers/vmfilter.php');
    		InvoiceHelper::importVMFile('models/product.php');
    		InvoiceHelper::importVMFile('models/customfields.php');
    		InvoiceHelper::importVMFile('helpers/currencydisplay.php');
    	}
    		
        $db = JFactory::getDBO();
        $app = JFactory::getApplication();

        if ($orderItemIds===null) //initial call
        	$items = InvoiceGetter::getOrderItems($this->orderID); //note: initial ordering
        else { //ajax
        	$items = array();
        	foreach ($orderItemIds as $orderItemId){ //load by order item id. its rewrited by POST in view class
        		if ($orderItemId>0){
        			$item = InvoiceGetter::getOrderItems(null,$orderItemId); //note: initial ordering
		        	$items = array_merge($items,!empty($item) ? $item : $this->getEmptyProduct('',$order));
        		}
        		else //new added products
        			$items[]=$this->getEmptyProduct('',$order);
        	}
        }

        if (COM_VMINVOICE_ISVM2)
        	$vmCurrencyDisplay = CurrencyDisplay::getInstance();
        		
        foreach ($items as &$item){
        	
     		//load also calc rules for items
     		if (COM_VMINVOICE_ISVM2) 
     			$item->calcRules = InvoiceGetter::getOrderCalcRules($this->orderID, $item->order_item_id);
     		
     		//guess tax rate for ordered items (we can't get it from VM prod., because 1) can be own product 2) tax rate could changed). 
     		$item->tax_rate_guessed = InvoiceHelper::guessTaxRate($item->product_price_with_tax, $item->product_item_price, COM_VMINVOICE_ISVM2 ? $item->calcRules : array());
     		$item->tax_rate = null; //will be entered in recomputeOrder //? - used only by VM1. VM2 uses only guessed
     		    
     		if (COM_VMINVOICE_ISVM2){
     			$item->product_item_price_init = $item->product_item_price; //for rules recompute, should stay init always
     			$item->product_currency_init = $item->order_item_currency ? $item->order_item_currency : $order->order_currency_init; //assume product currency is same as order currency. order_item_currency is NULL now (VM 2.0.18). used for re-calculation based on rules to specify currency of base price.
     		}
     		
     		//vm2: try to guess price that was used for computing price. if found, we can assign proper initial properties (price override) for form.
     		if (COM_VMINVOICE_ISVM2 AND $item->product_id){
     			$prices = invoiceGetter::getProductPrices($item->product_id);
     			$model = new VirtueMartModelProduct();

     			if ($product = $model->getProduct($item->product_id, TRUE, FALSE, TRUE, $item->product_quantity)){
     				$product = (object)array_merge((array)$item, (array)$product);
	     			foreach ($prices as $price){
	     				
	     				$product = (object)array_merge((array)$product, (array)$price); //?. join with each price.
	     				$calculator = calculationHelper::getInstance();	     	
	     				$calculator->_roundindig = true;
	     				if (!isset($product->categories))
	     					$product->categories = array();
	     				
	     				$prices = $calculator->getProductPrices($product, 0, $item->product_quantity, false);

	     				if (round($prices['salesPrice'],2)==round($item->product_subtotal_with_tax, 2)){ //we have probably winner

	     					//important: bacause we need to incluce marge, add it as $prices['basePrice'], but convert back to "initial currency". what a hell.
	     					$item->product_item_price_init = $vmCurrencyDisplay->convertCurrencyTo($product->product_currency, $prices['basePrice'], false);
	     					$item->product_currency_init = $product->product_currency;

	     					$item->override = $price->override;
	     					$item->product_override_price = $price->product_override_price;
	     				}
	     			}
     			}
     		}
     		
     		//if ($app->isAdmin() AND $item->product_name) // in the back-end show product names in the native language - if exists
     		//	$item->order_item_name = $item->product_name;
        }
    	
        if (!empty($productId)) //add new product from vm ($productId parameter)
        {
        	$product = InvoiceGetter::getProduct($productId);
        	
        	$newItem = $this->getEmptyProduct('',$order);
        	$newItem->product_id=$product->product_id;
        	$newItem->order_item_name=$product->product_name;
        	$newItem->product_quantity= $productQuantity ? $productQuantity : 1;
        	$newItem->order_item_currency=$product->product_currency; //note: in VM currently empty
        	$newItem->order_item_sku=$product->product_sku;
        	$newItem->tax_rate = null;
        	$newItem->vendor_id = $product->vendor_id;
            $newItem->product_weight = $product->product_weight;
            $newItem->product_weight_uom = $product->product_weight_uom;
        	
            $newItem->product_discountedPriceWithoutTax = 0;
            $newItem->product_priceWithoutTax = 0;
            
        	if (COM_VMINVOICE_ISVM2)
        	{
        		//ok, we must calculate price for new prouduct + add calc rules

        		$model = new VirtueMartModelProduct();
        		$product = $model->getProduct($newItem->product_id, TRUE, FALSE, TRUE);
        		
        		$calculator = calculationHelper::getInstance();
        		$calculator->_roundindig = true;

        		//create variant mods array based on POSTed values. its (cleaned) copy of how VM does it.
        		$variantmods = array();
        		if ($customPrices = JRequest::getVar('customPrice', array(), 'default', 'array'))
        			foreach ($customPrices as $customPrice)
        				foreach ($customPrice as $customId => $custom_fieldId){
        					if (is_array($custom_fieldId))
        						foreach ($custom_fieldId as $userfieldId => $userfield)
        							$variantmods[(int)$custom_fieldId] = $customId; //seems like bug from VM? $custom_fieldId is array
        					else
        						$variantmods[(int)$custom_fieldId] = $customId;
        				}

        		if ($customPluginPost = JRequest::getVar('customPlugin', array(), 'default', 'array')){
        			foreach($customPluginPost as &$customPlugin)
        				if(is_array($customPlugin))
        					foreach($customPlugin as &$customPlug)
        						if(is_array($customPlug))
        							foreach($customPlug as &$customPl){
        								$value = vmFilter::hl( $customPl,array('deny_attribute'=>'*'));
        								$value = preg_replace('@<[\/\!]*?[^<>]*?>@si','',$value);//remove all html tags
        								$value = (string)preg_replace('#on[a-z](.+?)\)#si','',$value);//replace start of script onclick() onload()...
        								$value = trim(str_replace('"', ' ', $value),"'") ;
        								$customPl = (string)preg_replace('#^\'#si','',$value);
        							}
    
        		
        			$product->customPlugin = json_encode($customPluginPost);
        			
        			InvoiceHelper::importVMFile('models/customfields.php');
        			$product = VirtueMartModelCustomfields::addParam($product);//add plugin params for product (?)
        		}
        		
        		//put attributes also to attributes
        		$product_attribute = array();
        		foreach($variantmods as $selected => $variant)
        			if ($selected) {
        				$productCustom = VirtueMartModelCustomfields::getProductCustomField ($selected);
        				
        				if ($productCustom->field_type == "E") {
        				
        					if(!class_exists('vmCustomPlugin')) require(JPATH_VM_PLUGINS.'/vmcustomplugin.php');
        				
        					$product_attribute[$selected] = $productCustom->virtuemart_custom_id;

        					if(!empty($product->param)) //? params are just same as merge of custom_param and customPlugins
        						foreach($product->param as $k => $plg)
        							if ($k == $selected)
        								$product_attribute[$selected] = $plg ;

        				} 
        				else 
        					$product_attribute[$selected] = ' <span class="costumTitle">'.$productCustom->custom_title.'</span><span class="costumValue" >'.$productCustom->custom_value.'</span>';
        			}
        				
        			
        			
        		$newItem->product_attribute = $product_attribute ? json_encode($product_attribute) : '';
        		
        		//base price is determined by price currency, price amount and overrides (-1, 0, 1) 

        		//merge object with selected price object.
        		//do it like in vm product model->getProductPrices();
        		if ($productPrice AND ($productPrices = InvoiceGetter::getProductPrices($newItem->product_id, $productPrice)) AND count($productPrices)==1) //selected price
        			$product = (object)array_merge((array)$product, (array)reset($productPrices));

                if ($productPrice) {
                    // Override the shopper group according to selected price
                    $db->setQuery("SELECT virtuemart_shoppergroup_id FROM #__virtuemart_product_prices WHERE virtuemart_product_price_id = ".(int)$productPrice);
                    $sh = $db->loadResult();
                    if (!is_null($sh)) {
        				$usermodel = VmModel::getModel('user');
        				$usermodel->getCurrentUser();
                        $usermodel->_data->shopper_groups = array($sh);
                    }
                }

        		// store for calc recompute
        		$newItem->override = $product->override;
        		$newItem->product_override_price = $product->product_override_price;

        		if (!isset($product->categories))
        			$product->categories = array();
        		
        		//now compute prices with given variant
        		$variantmod = $calculator->calculateModificators($product, $variantmods);

        		//http://www.artio.net/support-forums/vm-invoice/pre-sale-questions/incompatibility-vm-invoice-2-0-28-and-vm-2-0-14
        		if (InvoiceHelper::vmCersionCompare('2.0.14') > 0)
        			$prices = $calculator->getProductPrices($product ? $product : $newItem->product_id, $variantmod, $newItem->product_quantity, false); //since VM 2.0.7 must be passed object
				else
					$prices = $calculator->getProductPrices($product ? $product : $newItem->product_id, 0, $variantmod, $newItem->product_quantity, false);
				
                // 10.9.2013 dajo: fix for VM returning zero price with tax if no tax is applicable
                if ($prices['basePriceWithTax'] == 0) {
                    $prices['basePriceWithTax'] = $prices['basePrice'];
                }

        		$newItem->product_currency_init = $product->product_currency; // store for calc recompute
        		
        		InvoiceHelper::importVMFile('helpers/currencydisplay.php');
        		$vmCurrencyDisplay = CurrencyDisplay::getInstance();
        		
        		// store for calc recompute
        		$newItem->product_item_price_init = $vmCurrencyDisplay->convertCurrencyTo($product->product_currency, $prices['basePriceVariant'], false); //its with variant mod + converted to product_currency. 

        		$newItem->product_item_price = $prices['basePriceVariant'];
        		$newItem->product_price_with_tax = $prices['basePriceWithTax'];
        		$newItem->product_tax =  $prices['taxAmount']; //not used anywhere in VM2..
        		
        		$newItem->product_subtotal_with_tax = $prices['salesPrice'];
        		
        		$discountAmountMinus = InvoiceHelper::vmCersionCompare('2.0.20') > 0;
        		
        		$newItem->product_price_discount = ($discountAmountMinus ? 1 : -1) * $prices['discountAmount'];
        		
        		if (isset($prices['discountedPriceWithoutTax'])) //since 2.0.22
        			$newItem->product_discountedPriceWithoutTax = $prices['discountedPriceWithoutTax'];
        		if (isset($prices['priceWithoutTax'])) //since 2.0.22
        			$newItem->product_priceWithoutTax = $prices['priceWithoutTax'];
        		
        		//add used calc rules just like for ordered product
        		$newItem->calcRules = array();
        		foreach ($calculator->rules as $kind => $rules)
        			if ($kind!='Marge') //do not store Marge. VM does not do it also. (2.0.20b)
	        			foreach ($rules as $rule){
	        				$rule = (object)$rule;
	        				$rule->virtuemart_order_calc_rule_id = null; //will be new
	        				if (!isset($rule->calc_mathop) AND isset($rule->calc_value_mathop)) //object is named differently
	        					$rule->calc_mathop = $rule->calc_value_mathop;
	        				if (!isset($rule->calc_rule_name) AND isset($rule->calc_name))
	        					$rule->calc_rule_name = $rule->calc_name;
	        				$rule->calc_amount = null; //what is use of this field..
	        				$newItem->calcRules[] = $rule;
	        			}	

        		$newItem->tax_rate_guessed = InvoiceHelper::guessTaxRate($prices['basePriceWithTax'], $prices['basePrice'], $newItem->calcRules);
        	}
        	else //vm1
        	{
        		//TODO: what about currency.
        		$newItem->product_item_price = !is_null($productPrice) ? $productPrice : $product->product_price; //price manually selected, if not, price from db 
        		$newItem->tax_rate_guessed = $product->tax_rate; //assigned tax rate for product
        		$newItem->product_tax = $product->tax_rate * $newItem->product_item_price;
			    $newItem->product_price_with_tax = $newItem->product_item_price + $newItem->product_tax;
			    
			    //apply product specific discount
			    if ($product->amount>0) {
			    	if (($product->start_date<=time() OR empty($product->start_date)) AND ($product->end_date>=time() OR empty($product->end_date))) {
				    	if ($product->is_percent)
				    		$newItem->product_price_with_tax = ($newItem->product_price_with_tax * (100 - $product->amount)) / 100;
				    	else
				    		$newItem->product_price_with_tax = $newItem->product_price_with_tax - $product->amount;
				    		
				    	$newItem->product_item_price = $newItem->product_price_with_tax / (1 + $newItem->tax_rate_guessed);
				    	$newItem->product_tax = $newItem->product_price_with_tax - $newItem->product_item_price;
			    	}
			    }
        	}

			$items[] = $newItem;
        }

		//add new product, that is not in vm ($productName parameter). calc rules not applied?
        if (empty($productId) AND !empty($productName))
        	$items[]=$this->getEmptyProduct($productName,$order);

        // count overall weight 
        // (for purpose of passing it into shipping modules). note this works still with original quantity from db, not post. (why?)
        // only for VM1, because VM1 shipping moduels needs it, VM2 is bit more complex (handled in TableVm2OrderShipment)
        if (COM_VMINVOICE_ISVM1){
        	$this->overal_weight_vm1 = 0;
	        foreach ($items as &$item)
	        	if (!empty($item->product_id)){ //it is VM product
	        		$db->setQuery('SELECT p.`product_weight` FROM `#__vm_product` AS p WHERE p.`product_id` = '. (int) $item->product_id);
			        if ($weight = $db->loadResult())
			        	$this->overal_weight_vm1 += $item->product_quantity * $weight;
	        	}
        }
        
        return $items;
    }
    
    /**
     * Load order information
     * 
     * @param bool $fromPost 		if override loaded values by these from post ( = ajax refresh)
     * @param bool $updateShipping	if override shipping cost + tax by these from ship_method_id (only VM2)
     * @param bool $updatePayment	if override payment cost + tax by these from payment_method_id (only VM2)
     */
    function getOrderInfo($fromPost=false,$updateShipping=false,$updatePayment=false)
    {
        if (!($order = InvoiceGetter::getOrder($this->orderID))) { //new order
        	
        	$params = InvoiceHelper::getParams(); 
        	
        	//get default vendor. if not set and there only one in VM, use him
			if (!$defaultVendor = $params->get('default_vendor')){
				$vendors = InvoiceGetter::getVendors();
				$defaultVendor = count($vendors)==1 ? $vendors[0]->id : 0;
			}
			
			//get default currency. if not set, get default vendor currency, if not set, guess VM most used currency
			if (!$defaultCurrnecy = $params->get('default_currency'))
				$defaultCurrnecy = ($curr = InvoiceGetter::getDefaultCurrency($defaultVendor)) ? $curr : null;

			//get default status
			if (!$defaultStatus = $params->get('default_status'))
				$defaultStatus = '';
			
            $order = new stdClass();
            $order->as_guest = (int)$params->get('default_as_guest', 0);
            $order->user_id = '';
            $order->order_id = '';
            $order->order_number = '';
            $order->user_info_id = '';
            $order->cdate = '';
            $order->mdate = '';
            $order->order_status = $defaultStatus;
            $order->vendor_id = $defaultVendor;
            $order->ship_method_id = '||||';
            $order->order_currency = $defaultCurrnecy;
            $order->user_currency_id = 0; //VM2
            $order->user_currency_rate  = 1.0; //VM2
            $order->payment_method_id = null;
            $order->shipment_method_id = null; //VM2
            $order->order_shipping = 0;
            $order->order_shipping_tax = 0;
            $order->order_shipping_taxrate = 0;
            $order->order_payment = 0;
            $order->order_payment_tax = 0;
            $order->order_payment_taxrate = 0;
            $order->coupon_discount = 0;
            $order->coupon_code = '';
            $order->order_discount = 0;
            $order->order_total = '';
            $order->order_subtotal = '';
            $order->order_tax = '';
            $order->customer_note = ''; 
            $order->order_salesPrice = 0;
            $order->order_discountAmount = 0; //VM2
            $order->orderCalcRules = array();//VM2
            $order->shippingCalcRules = array();//VM2
            $order->paymentCalcRules = array();//VM2
        }
        elseif (COM_VMINVOICE_ISVM2){ //order from db: get calculation rules applied on whole order from db
        	$order->orderCalcRules = InvoiceGetter::getOrderCalcRules($this->orderID, -1);
        	$order->shippingCalcRules = InvoiceGetter::getOrderCalcRules($this->orderID, -2);
        	$order->paymentCalcRules = InvoiceGetter::getOrderCalcRules($this->orderID, -3);
        	
        	$order->as_guest = $order->user_id == 0;
        }
        elseif (COM_VMINVOICE_ISVM1)  //order from db
        	$order->as_guest = 0; //vm1 has no guest checkout
        
        if (!isset($order->order_language))
        	$order->order_language = null; //VM>2.0.22
        
        //TODO: move to more propriate place
        $order->order_shipping_taxrate = InvoiceHelper::guessTaxRate($order->order_shipping+$order->order_shipping_tax, $order->order_shipping, COM_VMINVOICE_ISVM2 ? $order->shippingCalcRules : null);
        if (COM_VMINVOICE_ISVM2)
        	$order->order_payment_taxrate = InvoiceHelper::guessTaxRate($order->order_payment+$order->order_payment_tax, $order->order_payment, $order->paymentCalcRules);
        	
       // fill custom shipping vars from ship_method_id
        if (COM_VMINVOICE_ISVM1)
        {
	        $customShipping = explode('|', $order->ship_method_id);
			$order->custom_shipping_class = $customShipping[0];
			$order->custom_shipping_carrier = isset($customShipping[1]) ? $customShipping[1] : '';
			$order->custom_shipping_ratename = isset($customShipping[2]) ? $customShipping[2] : '';
			$order->custom_shipping_costs = isset($customShipping[3]) ? $customShipping[3] : '';
			if (isset($order->order_shipping_taxrate)) {
			    $order->custom_shipping_taxrate = $order->order_shipping_taxrate;
			} else {
			    if ($order->order_shipping_tax != $customShipping[3] AND $order->order_shipping_tax != 0 AND $customShipping[3] != 0) {
	                $order->custom_shipping_taxrate = InvoiceHelper::guessTaxRate($order->order_shipping + $order->order_shipping_tax, $order->order_shipping);
			    } 
			    else 
			    	$order->custom_shipping_taxrate = 0;
			}
			
			$order->custom_shipping_id = isset($customShipping[4]) ? $customShipping[4] : '';	
			$order->order_shipping_taxrate = $order->custom_shipping_taxrate;
        	//they are glued back from POST at vm1tableorder
        }
        	
        $orderSubtotal = null;
        
        
        if ($fromPost=='userajax'){
        	$order->as_guest = JRequest::getInt('as_guest', 0) ;
        	if (COM_VMINVOICE_ISVM1)
        		$order->as_guest = 0;
        	 
        }
        
        if ($fromPost=='orderajax'){
        
         	//override loaded status by post
        	$order->order_status = JRequest::getVar('status', null);
        
			//override coupon discount by post
        	$order->coupon_discount = JRequest::getVar('coupon_discount', null)*1;
        	
        	//override shipping costs from post
        	$order->order_shipping = JRequest::getVar('order_shipping', null)*1;
        	$order->order_shipping_tax = JRequest::getVar('order_shipping_tax', null)*1;
        	
        	if (COM_VMINVOICE_ISVM1) //VM2 have ship -have tax rate only for display. in VM1 it is relevant.
        		$order->order_shipping_taxrate = JRequest::getVar('order_shipping_taxrate', null)*1;
        	
        	if (COM_VMINVOICE_ISVM2){
        		
        		//override shipping method from post
        		$order->shipment_method_id = JRequest::getVar('shipment_method_id', null);
        		
        		//override payment costs from post
	        	$order->order_payment = JRequest::getVar('order_payment', null)*1;
	        	$order->order_payment_tax = JRequest::getVar('order_payment_tax', null)*1;
        	}
        	
        	//compute current order's subtotal (for payment method discount - VM1)
	        if (COM_VMINVOICE_ISVM1){
	        	$orderSubtotal = 0;
	        	$nets = JRequest::getVar('product_item_price', array(),'default','array');
	        	$quantities = JRequest::getVar('product_quantity', array(),'default','array');
	        	foreach ($nets as $key => $val)
	        		$orderSubtotal += $val * $quantities[$key];
        	}

        	$order->payment_method_id = JRequest::getInt('payment_method_id');
            
            $order->user_id = $order->as_guest ? 0 : JRequest::getInt('user_id');
        }

        if (COM_VMINVOICE_ISVM2 AND $updateShipping)
        	$this->overrideShipment($order);
        
        if (COM_VMINVOICE_ISVM1 OR $updatePayment)
        	$this->overridePayment($order, $orderSubtotal);
        	
	    if (COM_VMINVOICE_ISVM1){
        	//get discount minus payment discount. if is not passed by GET, compute it by substracting payment discount
			$order->order_discount = ($fromPost=='orderajax') ? JRequest::getVar('order_discount')*1 : - $order->order_discount - $order->order_payment;
	    }
	    
	    if (COM_VMINVOICE_ISVM2){ //for calc rules recompute. should stay initial.
	   		$order->order_shipping_init = $order->order_shipping;
	   		$order->order_payment_init = $order->order_payment;
	   		$order->order_currency_init = $order->order_currency;
	    }
	    
        return $order;
    }

    /**
     * Write selected payment method fee's to order
     * 
     * @param unknown_type $order
     * @param unknown_type $orderSubtotal only for VM1
     */
    function overridePayment(&$order, $orderSubtotal=null)
    {
    	$order->order_payment = 0;
    	$order->order_payment_taxrate = 0;
    	$order->order_payment_tax = 0;
    	
    	if (!($method = InvoiceGetter::getPaymentMethod($order->payment_method_id)))
    		return ;
    	
    	if (COM_VMINVOICE_ISVM2){
    		
	    	 $order->order_payment = @$method->cost_per_transaction;
	    	 if (@$method->cost_percent_total) //selected percent of total items amount
	    	 	$order->order_payment = $order->order_payment + ((@$method->cost_percent_total/100)*$order->order_salesPrice); //TODO: jakto ze tady je to order_salesPrice a tam order_subtotal ? 

	    	 if (@$method->tax_id>0){ //apply specific tax set to payment, BUT NOT 100% RELIABLE (they could select apply default rules?)
	    	 	$db = JFactory::getDBO();
	    	 	$db->setQuery('SELECT * FROM #__virtuemart_calcs WHERE virtuemart_calc_id='.(int)$method->tax_id);
	    	 	if ($calcRule = $db->loadObject()){
	    	 		if ($calcRule->calc_value_mathop=='+%')
	    	 			$order->order_payment_taxrate=$calcRule->calc_value/100;
	    	 		elseif ($calcRule->calc_value_mathop=='-%')
	    	 			$order->order_payment_taxrate=-($calcRule->calc_value/100);	
	    	 				
	    	 		$order->order_payment_tax = $order->order_payment * $order->order_payment_taxrate;
	    	 		
	    	 		if ($calcRule->calc_value_mathop=='+')
	    	 			$order->order_payment_tax=$order->order_payment + $calcRule->calc_value;
	    	 		elseif ($calcRule->calc_value_mathop=='-')
	    	 			$order->order_payment_tax=$order->order_payment - $calcRule->calc_value;
	    	 	}
	    	 }	
	    	 
	    	 //TODO: check all other things. grr. (min, max order?)
    	}
	    else{ //vm1

	    	if (is_null($orderSubtotal))
	    		$orderSubtotal = $order->order_subtotal;
	    	
	    	if ($method->payment_method_discount_is_percent==1){ //(little inspired by ps_checkout get_payment_discount())
    			$order->order_payment = $orderSubtotal * $method->payment_method_discount / 100;
    			
	    		if ($method->payment_method_discount_max_amount*1 && abs($order->order_payment) > $method->payment_method_discount_max_amount*1)
	    			$order->order_payment = - $method->payment_method_discount_max_amount*1;
	    					
	    		if ($method->payment_method_discount_min_amount*1 && abs($order->order_payment) < $method->payment_method_discount_min_amount*1)
	    			$order->order_payment = - $method->payment_method_discount_min_amount*1;
    		}
    		else
    			$order->order_payment = $method->payment_method_discount*1;
    					
	    	$order->order_payment = - $order->order_payment;
	    }
    }
    
    /**
     * Write selected shipping method fee's to order
     * 
     * @param order object $order
     */
    function overrideShipment(&$order)
    {
    	if (COM_VMINVOICE_ISVM2 AND ($shippings = InvoiceGetter::getShippingsVM2()))
    	{
    	    foreach ($shippings as $shipping)
        	{
        		if ($shipping->shipping_rate_id == $order->shipment_method_id){
        			
        			$order->order_shipping = $shipping->cost + $shipping->package_fee;
			    	$order->order_shipping_taxrate = 0;
			    	$order->order_shipping_tax = 0;
		        			
			    	 if ($shipping->tax_id>0){ //apply specific tax set to payment, BUT NOT 100% RELIABLE (they could select apply default rules?)
			    	 	$db = JFactory::getDBO();
			    	 	$db->setQuery('SELECT * FROM #__virtuemart_calcs WHERE virtuemart_calc_id='.(int)$shipping->tax_id);
			    	 	if ($calcRule = $db->loadObject()){
			    	 		if ($calcRule->calc_value_mathop=='+%')
			    	 			$order->order_shipping_taxrate=$calcRule->calc_value/100;
			    	 		elseif ($calcRule->calc_value_mathop=='-%')
			    	 			$order->order_shipping_taxrate=-($calcRule->calc_value/100);	
			    	 				
			    	 		$order->order_shipping_tax = $order->order_shipping * $order->order_shipping_taxrate;
			    	 		
			    	 		if ($calcRule->calc_value_mathop=='+')
			    	 			$order->order_shipping_tax=$order->order_shipping + $calcRule->calc_value;
			    	 		elseif ($calcRule->calc_value_mathop=='-')
			    	 			$order->order_shipping_tax=$order->order_shipping - $calcRule->calc_value;
			    	 	}
			    	}	
			    	//TODO: check also other conditions...?
        		}
        	}
    	}
    }
    
    function getUserInfo($addressType,$userInfoId=null, $userId = null)
    {
    	$userInfo = null;
    	if ($userInfoId)
    		if ($userInfo = InvoiceGetter::getUserInfo($userInfoId)) //user info found
    			$userInfo->billing_is_shipping = 0;
    	
    	if (!$userInfo){
    		$userInfo = $this->getEmptyUser();	
        	if ($addressType=='ST') //no ST
        		$userInfo->billing_is_shipping = 1;
        		
    		//if no id passed, maybe there is joomla! user which is not registered in VM yet. 
    		//in that case, fill new user with joomla! register info
    		if ($userId && ($user = JFactory::getUser($userId)))
    		{
    			$userInfo->user_id = $userId;
    			
    			//guess parts of name
    			$name = explode(' ',$user->name);
    			if (count($name)==2){
    				$userInfo->first_name = $name[0];
    				$userInfo->last_name = $name[1];
    			}
    		    elseif (count($name)==3){
    				$userInfo->first_name = $name[0];
    				$userInfo->middle_name = $name[1];
    				$userInfo->last_name = $name[2];
    			}
    			else { //more parts, no matter, just put all to names
    				$userInfo->first_name = array_shift($name);
    				$userInfo->last_name = count($name) ? implode(' ',$name) : '';
    			}

    			$userInfo->email = $user->email;
    			
    			//try to load info from user profile plugin
    			$db = JFactory::getDBO();
    			$db->setQuery('SHOW TABLES LIKE \'%_user_profiles\'');
    			$tables = invoiceHelper::loadColumn($db);
    			if ($tables && count($tables)>0)
    			{
    				$db->setQuery('SELECT profile_key, profile_value FROM #__user_profiles WHERE user_id='.(int)$userId);
    				$profileInfo = $db->loadObjectList('profile_key');
    				if (isset($profileInfo['profile.address1'])) $userInfo->address_1 = trim($profileInfo['profile.address1']->profile_value,' "');
    				if (isset($profileInfo['profile.address2'])) $userInfo->address_2 = trim($profileInfo['profile.address2']->profile_value,' "');
    				if (isset($profileInfo['profile.city'])) $userInfo->city = trim($profileInfo['profile.city']->profile_value,' "');
    				
    				if (isset($profileInfo['profile.region'])){ //find state id by name
    					$userInfo->state = trim($profileInfo['profile.region']->profile_value,' "');	
    				    $searchState = strtolower(preg_replace('#\W#','',$profileInfo['profile.region']->profile_value));
    					foreach (invoiceGetter::getStatesDB() as $region){
    						if (strtolower(preg_replace('#\W#','',$region->state_name)) == $searchState ||
    						strtolower(preg_replace('#\W#','',$region->state_3_code)) == $searchState ||
    						strtolower(preg_replace('#\W#','',$region->state_2_code)) == $searchState ||
    						$region->id == $profileInfo['profile.region']->profile_value){
    							$userInfo->state = $region->id; break;}
    					}
    				}
    				if (isset($profileInfo['profile.country'])){ //find country id by name
    					$userInfo->country = trim($profileInfo['profile.country']->profile_value,' "');
    					$searchCountry = strtolower(preg_replace('#\W#','',$profileInfo['profile.country']->profile_value));
    					foreach (invoiceGetter::getCountriesDB() as $country){
    						if (strtolower(preg_replace('#\W#','',$country->country_name)) == $searchCountry ||
    						strtolower(preg_replace('#\W#','',$country->country_3_code)) == $searchCountry ||
    						strtolower(preg_replace('#\W#','',$country->country_2_code)) == $searchCountry ||
    						$country->id == $profileInfo['profile.country']->profile_value){
    							$userInfo->country = $country->id; break;}
    					}
    				}
    				
    				if (isset($profileInfo['profile.postal_code'])) $userInfo->zip = trim($profileInfo['profile.postal_code']->profile_value,' "');
    				if (isset($profileInfo['profile.phone'])) $userInfo->phone_1 = trim($profileInfo['profile.phone']->profile_value,' "');
    			}
    			
    		}
    	}

        return $userInfo;
    }

    function getOrderUserInfo($addressType)
    {
        if ($user = InvoiceGetter::getOrderUserInfo($this->orderID, $addressType))
        {
        	$db = JFactory::getDBO();
        	
        	//try to also get global user_info_id for given address (we need it for updating user standard address)
        	if (COM_VMINVOICE_ISVM2)
        		$query = 'SELECT virtuemart_userinfo_id FROM #__virtuemart_userinfos WHERE virtuemart_user_id= '.(int)$user->user_id.' AND address_type = '.$db->Quote($addressType);
       		else
       			$query = 'SELECT user_info_id FROM #__vm_user_info WHERE user_id= '.(int)$user->user_id.' AND address_type = '.$db->Quote($addressType);
       			
        	$db->setQuery($query);
       		$db->query();

       		if ($db->getNumRows()>1){ //more addresses (f.e. ST), try to find it by type name
       			if (!$user->address_type_name) //default ST address
       				$append = ' AND ((address_type_name IS NULL) OR address_type_name=\'-default-\')'; //NULL==-default- in VM1
       			else
       				$append = ' AND address_type_name = '.$db->Quote($user->address_type_name);
       			
       			$db->setQuery($query.$append);
        		$db->query();

        		if (COM_VMINVOICE_ISVM1 && $db->getNumRows()>1 AND !$user->address_type_name){ //else try just -default- (vm1)
        			$db->setQuery($query.' AND address_type_name=\'-default-\''); 
        			$db->query(); 
        		}
       		}
        	$user->user_info_id = $db->getNumRows()==1 ? $db->loadResult() : '';
        	$user->billing_is_shipping = 0;
        }
        else {
        	$user = $this->getEmptyUser();
        	if ($addressType=='ST') //no shipping address is found
        		$user->billing_is_shipping = 1;
        }
        	
        	
        return $user;
    }

    function getEmptyUser()
    {
        $user = new stdClass();
        $user->user_id = '';
        $user->address_type = '';
        $user->address_type_name = null;
        $user->company = '';
        $user->title = '';
        $user->last_name = '';
        $user->first_name = '';
        $user->middle_name = '';
        $user->phone_1 = '';
        $user->phone_2 = '';
        $user->fax = '';
        $user->address_1 = '';
        $user->address_2 = '';
        $user->city = '';
        $user->state = '';
        $user->country = '';
        $user->zip = '';
        $user->email = '';
   		$user->user_info_id='';
   		
   		$user->billing_is_shipping = 0;
   		
        return $user;
    }
    
    function getEmptyProduct($name='',$order=null)
    {
    	$product = new stdClass();
    	$product->order_item_id = 0;
    	$product->product_id = 0;
    	$product->order_item_name = $name;
    	$product->product_attribute = '';
    	$product->order_status = isset($order->order_status) ? $order->order_status : 'P';
    	$product->product_quantity = 1;
    	$product->product_price_with_tax = 0;
    	$product->product_item_price = 0;
    	$product->product_item_price_init = 0;
    	$product->product_tax = 0;
    	$product->product_subtotal_with_tax = 0;
    	$product->order_item_currency = isset($order->order_currency) ? $order->order_currency : null; //note: in VM currently empty
    	$product->product_currency_init = $product->order_item_currency;
    	$product->order_item_sku = '';
    	$product->vendor_id = isset($order->vendor_id) ? $order->vendor_id : null;
    	$product->tax_rate_guessed = 0;
    	$product->tax_rate = null;
    	$product->product_price_discount = null;
    	$product->calcRules = array();
    	$product->product_weight = 0;
    	$product->product_weight_uom = '';
    	$product->product_discountedPriceWithoutTax = 0; //since VM 2.0.22..
    	$product->product_priceWithoutTax = 0; //since VM 2.0.22..
    
    	return $product;
    }
      
    /**
     * Recompute order and products.
     * 
     * @param array $products
     * @param object $order
     * @param boolean $recomputeOrder 	whether called by ajax recompute. 
     * @param TableVmOrder $order
     */
    function recomputeOrder(&$products, &$order, $recomputeOrder = false, $recomuteByRulesI = null)
    {
        $orderTax = $orderSubtotal = $order_salesPrice = $order_discountAmount = 0;
        $count = count($products);
        $i=0;
        
        if (COM_VMINVOICE_ISVM2 AND !is_null($recomuteByRulesI)){
        	$basePrices = JRequest::getVar('calc_baseprice', array(), 'default', 'array');
        	$baseCurrencies = JRequest::getVar('calc_basecurrency', array(), 'default', 'array');
        	$overrides = JRequest::getVar('calc_override', array(), 'default', 'array');
        	$overridesPrices = JRequest::getVar('calc_override_price', array(), 'default', 'array');
        	
        	InvoiceHelper::importVMFile('helpers/currencydisplay.php');
        	$vmCurrencyDisplay = CurrencyDisplay::getInstance();

        	//http://www.artio.net/support-forums/vm-invoice/pre-sale-questions/incompatibility-vm-invoice-2-0-28-and-vm-2-0-14
        	$getProductPricesNew = InvoiceHelper::vmCersionCompare('2.0.14') > 0;
        	
        	$discountAmountMinus = InvoiceHelper::vmCersionCompare('2.0.20') > 0;
        }
        
        foreach ($products as &$product) {

        	$productSubtotal = null;
        	$itemRecomputedFromRules = false;
        	
        	if (COM_VMINVOICE_ISVM2 AND $i===$recomuteByRulesI){ //ohoho, user clicked "refresh item prices based on calculation rules"

        		$itemRecomputedFromRules = true;
        		
        		//don't pass whole product object. because 1. product could not exists now 2. we can add custom product
        		$productPass = new stdClass();
        		
        		//re/set some base values, to work mostly 
        		//TODO: product_override_price ? 
        		
        		//if base price specified in post (should be!), convert to selected currency before.
        		//$productPass->product_price = isset($basePrices[$i]) ? $vmCurrencyDisplay->convertCurrencyTo($baseCurrencies[$i], $basePrices[$i], true) : $product->product_item_price;
        		
        		
        		$productPass->product_price =  isset($basePrices[$i]) ? $basePrices[$i] : $product->product_item_price;
        		
        		$productPass->product_tax_id = 0;
        		$productPass->product_discount_id = 0;
        		//$productPass->product_currency = $order->order_currency;
        		$productPass->product_currency = isset($baseCurrencies[$i]) ? $baseCurrencies[$i] : $order->order_currency;
        		
        		$productPass->virtuemart_vendor_id = $product->vendor_id ? $product->vendor_id : $order->vendor_id;
        		$productPass->categories = array();

        		$productPass->override =  isset($overrides[$i]) ? (int)$overrides[$i] : 0;
        		$productPass->product_override_price = isset($overridesPrices[$i]) ? $overridesPrices[$i]*1 : 0;
 
        		//VM 2.0.18
        		//first, pass "fake" rules
        		//build "fake" allrules property that will contain not all rules as usual, but all rules defined per this product
        		//than make sure all these rules will be added by gatherEffectingRulesForProductPrice function. done it by specifying empty arrays in cats, groups, states, countries... 
        		
        		//create calculaor
        		InvoiceHelper::importVMfile('helpers/calculationh.php');
        		$calculator = calculationHelper::getInstance();
        		$calculator->_roundindig = true;
        		
        		//build fake "allrules" array with rules we have obtained from POST (set in view)
        		$calculator->allrules = $this->buildFakeRules($product->calcRules, $productPass->virtuemart_vendor_id, $order->order_currency, true);

        		if ($getProductPricesNew)
        			$prices = $calculator->getProductPrices($productPass, 0.0, $product->product_quantity); 
				else
					$prices = $calculator->getProductPrices($productPass, 0, 0.0, $product->product_quantity);

        		$product->product_item_price = $prices['basePriceVariant']; //basePriceVariant or basePriceWithTax?
        		$product->product_price_with_tax = $prices['basePriceWithTax'];
        		$product->product_tax =  $prices['taxAmount'];
        		$product->product_price_discount = ($discountAmountMinus ? 1 : -1) * $prices['discountAmount'];
        		$product->product_subtotal_with_tax = $prices['salesPrice'];

        		$product->tax_rate_guessed = InvoiceHelper::guessTaxRate($product->product_price_with_tax, $product->product_item_price, $product->calcRules);
        		$productSubtotal = $prices['priceBeforeTax']*$product->product_quantity;

        		//since 2.0.22
        		if (isset($prices['discountedPriceWithoutTax']))
        			$product->product_discountedPriceWithoutTax = $prices['discountedPriceWithoutTax'];
        		if (isset($prices['priceWithoutTax']))
        			$product->product_priceWithoutTax = $prices['priceWithoutTax'];
        		
        		//important: delete Marge from calculation rules. why? because it would apply it on base price always in every recompute
        		//and Marge is not part of order calc rules by VM
        		//we just must reset base price to new item price, else clicking recompute again would restore original price
        		foreach ($product->calcRules as $i => $rule)
        			if ($rule->calc_kind=='Marge'){
        				unset($product->calcRules[$i]);
        				$_POST['calc_baseprice'][$recomuteByRulesI] = $product->product_item_price;
        			}
        	}
        	
        	if (COM_VMINVOICE_ISVM1){ //VM1 relevant. VM2 changes prices only by rules
        	
	        	if ($recomputeOrder)// ok, when called by ajax, recompute guessed tax rate based on form data (which is in $products by now). only vm1.
	        		$product->tax_rate_guessed = InvoiceHelper::guessTaxRate($product->product_tax + $product->product_item_price, $product->product_item_price);
	        		
	        	//recompute tax rates. but only if they changded since last time (TODO: ?)
	        	            
	        	if (is_null($product->tax_rate)) //initial call. else we get it from select box. 
	        		$product->tax_rate = $product->tax_rate_guessed;
	        	
	        	if ($recomputeOrder AND !is_null($product->tax_rate)) //should always be at this point
	        		if ($product->tax_rate>=0 AND $product->tax_rate!=$product->tax_rate_guessed) //tax rate changed by user, else stay initial
	        			$product->product_tax = $product->tax_rate * $product->product_item_price;	
        	}
        	
        	if ($recomputeOrder AND COM_VMINVOICE_ISVM1) { //recompute "final prices". only for vm1, because VM2 does it only when $recomuteByRulesI

        		//base + tax
        		$product->product_price_with_tax = $product->product_item_price + $product->product_tax;
        		
        		//end price
        		$product->product_subtotal_with_tax = $product->product_price_with_tax + $product->product_price_discount;
        	}
        	
            if (COM_VMINVOICE_ISVM2)
            	$order_discountAmount += $product->product_price_discount * $product->product_quantity;
            
            $product->overall_tax = $product->product_tax * $product->product_quantity; //simple, only for display
           	$product->subtotal = $product->product_item_price * $product->product_quantity; //simple, only for display
           	$product->total = $product->product_subtotal_with_tax * $product->product_quantity; //simple, only for display


           	//vm2: $orderSubtotal JE součet $this->productPrices['priceWithoutTax'] = $salesPrice - $this->productPrices['taxAmount'];
           	if (COM_VMINVOICE_ISVM2){ //'priceWithoutTax'
           		//var_dump($product);
           		//$base = !empty($product->product_discountedPriceWithoutTax) ? $product->product_discountedPriceWithoutTax : $product->product_subtotal_with_tax;
           		
           		
           		if (!empty($product->product_priceWithoutTax))
           			$orderSubtotal += $product->product_priceWithoutTax*$product->product_quantity;
           		else
           			$orderSubtotal += ($product->product_subtotal_with_tax - $product->product_tax)*$product->product_quantity;
           	}
           	else
            	$orderSubtotal += $productSubtotal!==null ? $productSubtotal : $product->subtotal; //ok, here cna be problem when using override price to be taxed. when recompouted productm its ok, but if not, there is itenm price without discount!
            $orderTax += $product->overall_tax;
            $order_salesPrice += $product->total;
            
            $i++;
        }
        
        $shipmentRecomputedFromRules = false;
        $paymentRecomputedFromRules = false;
        
        if (COM_VMINVOICE_ISVM2 AND $recomuteByRulesI===-2){ //recompute shipping prices with VM2 calc rules. only simple rules, no plugins (...)
        	InvoiceHelper::importVMfile('helpers/calculationh.php');
        	$calculator = calculationHelper::getInstance();
        	$calculator->_roundindig = true;
        	$rules = $this->buildFakeRules($order->shippingCalcRules, $order->vendor_id, $order->order_currency);
        	$order->order_shipping = isset($basePrices[-2]) ? $vmCurrencyDisplay->convertCurrencyTo($baseCurrencies[-2], $basePrices[-2], true) : $order->order_shipping;
        	$order->order_shipping_tax = $calculator->executeCalculation($rules, $order->order_shipping) - $order->order_shipping;
        	$order->order_shipping_taxrate = InvoiceHelper::guessTaxRate($order->order_shipping+$order->order_shipping_tax, $order->order_shipping, $order->shippingCalcRules); //only guess for display
        	$shipmentRecomputedFromRules = true;
        }
        
        if (COM_VMINVOICE_ISVM2 AND $recomuteByRulesI===-3){ //recompute payment prices with VM2 calc rules. only simple rules, no plugins (...)
        	InvoiceHelper::importVMfile('helpers/calculationh.php');
        	$calculator = calculationHelper::getInstance();
        	$calculator->_roundindig = true;
        	$rules = $this->buildFakeRules($order->paymentCalcRules, $order->vendor_id, $order->order_currency);
        	$order->order_payment = isset($basePrices[-3]) ? $vmCurrencyDisplay->convertCurrencyTo($baseCurrencies[-3], $basePrices[-3], true) : $order->order_payment;
        	$order->order_payment_tax = $calculator->executeCalculation($rules, $order->order_payment) - $order->order_payment;
        	$order->order_payment_taxrate = InvoiceHelper::guessTaxRate($order->order_payment+$order->order_payment_tax, $order->order_payment, $order->paymentCalcRules); //only guess for display
        	$paymentRecomputedFromRules = true;
        }
        
        //same as order subtotal (we use VM2 name even for VM1). but its with discount (in DB it is witout it!)
        if (COM_VMINVOICE_ISVM1) //eh. for VM2 its messy. lets dont use it.
        	$order->order_salesPrice = $order->order_subtotal + $order->order_tax + (COM_VMINVOICE_ISVM2 ? $order->order_discountAmount : 0);
        
        if ($recomputeOrder) {
        	
            $order->order_tax = $orderTax; //recomputed
            $order->order_subtotal = $orderSubtotal;  //recomputed
            $order->order_salesPrice = $order_salesPrice;  //recomputed
            
            if (COM_VMINVOICE_ISVM2)
            	$order->order_discountAmount = $order_discountAmount; //recomputed
            
            if (COM_VMINVOICE_ISVM1 AND $order->order_shipping_taxrate>=0) //if not "-other-", recompute shipping tax bsed on selected one (VM1)
           		$order->order_shipping_tax = $order->order_shipping * $order->order_shipping_taxrate;

            $order->order_total = $order->order_salesPrice; //price of all items featuring tax and discounts
            $order->order_total += $order->order_shipping + $order->order_shipping_tax; //add shipping + tax
            $order->order_total += $order->order_payment + $order->order_payment_tax; //add payment + tax
            $order->order_total += $order->coupon_discount; //add coupon discount
            
            if (COM_VMINVOICE_ISVM1)
            	$order->order_total += $order->order_discount; //vm 1: add "order other discount" (in db it is stored togehter with payment discount, but here we have it separated)
            else
	            foreach ($order->orderCalcRules as $rule)
	            	$order->order_total += $rule->calc_amount; //vm2: add order calculation rules (?)
        }

        //no, stejne se to v poslednich verzich asi zase x krat zmenilo.
        //produkty:
        //VM2:
        //product_final_price - cena jedne polozky s dani a po sleve
        //product_subtotal_with_tax = cena vsech polozek po dani a slevach
        
        //objednavka: 
        //VM2:
        //order_salesPrice - celkova cena objednych veci s danemi a vsim (slevy)
        //order_subtotal - soucet pocatecnich cen produktu product_item_price 
        //order_tax = 1 - slevy - 2
        //order_billTaxAmount = soucet VSECH dani. tedy shipment tax, payment tax, order_tax + dalsi dane (z tabulky calc_rules ovsem jen TAXY ne price modifiery)
        //order_billDiscountAmount = měl by to byt soucet discountu za objednavku? ale chyba ve VM asi. kazdop zaporne cislo (spravne)
        //order_discountAmount = soucet discountu za produkty. kladne cislo, od 2.0.22 zaporne.
        //order_discount = nevim, co to je (asi stejne jako order_discountAmount), kladne cislo stejne jako order_discountAmount
        //note: tohle nahure uz vubec nemusi platit :)
        
        //VM1: 
        //order_subtotal - soucet pocatecnich cen produktu product_item_price  (stejne)
        //order_tax = 1 - 2 vsech produktu (ovsem pokud jsou nejak slevy tak nevim co)
    }
    
    private function buildFakeRules($postRules, $vendorId, $currency, $forProduct = false)
    {
    	//there must be at least one rule (generic) else calculator will load them from db in setVendorId
    	if (!$postRules) 
    		$postRules = array(0=> (object)array('calc_kind' => 'Tax', 'calc_rule_name' => '','calc_mathop' => '+','calc_value' => 0, 'calc_currency' => $currency));
    	
    	$rules = array();
    	foreach ($postRules as $ruleJ => $rule){
        			
        	$rulePassed = array();
        			
        	$rulePassed['virtuemart_calc_id'] = $ruleJ; //just fake..
        	$rulePassed['ordering'] = $ruleJ;
        	
        	$rulePassed['calc_name'] = $rule->calc_rule_name;
        	$rulePassed['calc_value_mathop'] = $rule->calc_mathop;
        	$rulePassed['calc_value'] = $rule->calc_value;
        	$rulePassed['calc_currency'] = $rule->calc_currency;
        	$rulePassed['calc_kind'] = $rule->calc_kind;

        	$rulePassed['cats'] = array();
        	$rulePassed['shoppergrps'] = array();
        	$rulePassed['countries'] = array();
        	$rulePassed['states'] = array();
        			
        	$rulePassed['calc_shopper_published'] = 1;
        	$rulePassed['calc_params'] = null;
        	$rulePassed['virtuemart_vendor_id'] = $vendorId;
        			
        	if ($forProduct)
        		$rules[$vendorId][$rule->calc_kind][] = $rulePassed;
        	else
        		$rules[] = $rulePassed;
        }
        
        return $rules;
    }
    
    /**
     * Update order's state using VM's functions.
     * Only if state is different then present OR is passed force notify (YF)
     * VM code (should) update also mdate. 
     * 
     * @param	object	order id
     * @param	string	new status
     * @param	string	"Y" if notify user if state changed, "YF" if force notify even state stays the same (resend notify), "N" or other for no notify
     * @param 	string	$comments			applies only for VM2! (now)
     * @param	bool	$includeComment		applies only for VM2! (now)
     * @param 	bool	$updateAllLines		applies only for VM2!
     */
	public function updateState($orderId,$newStatus,$notify,$comments='',$includeComment=false, $updateAllLines= false)
    {
		$order = InvoiceGetter::getOrder($orderId);
		
    	//if was changed state or checked notify
		if ((isset($newStatus) AND $newStatus!=$order->order_status) OR ($notify=='YF'))
		{
	    	if (COM_VMINVOICE_ISVM2)
	    	{
	    		//copy of admin/controllers/orders.php updatestatus()
	    		InvoiceHelper::importVMFile('controllers/orders.php');
	    		InvoiceHelper::importVMFile('views/orders/view.html.php');
	    		InvoiceHelper::importVMFile('helpers/shopfunctions.php');
	    		InvoiceHelper::importVMFile('models/orders.php');
	    		InvoiceHelper::importVMFile('tables/orders.php');
	    		InvoiceHelper::importVMFile('tables/vendors.php');
	    		InvoiceHelper::importVMFile('tables/vendor_medias.php');
	    		InvoiceHelper::importVMFile('tables/order_items.php');
	    		InvoiceHelper::importVMFile('tables/userfields.php');
	    		InvoiceHelper::importVMFile('tables/countries.php');
	    		InvoiceHelper::importVMFile('tables/orderstates.php');
	    		InvoiceHelper::importVMFile('tables/medias.php');
	    		InvoiceHelper::importVMFile('tables/vmusers.php');
	    		InvoiceHelper::importVMFile('tables/userinfos.php');
	    		InvoiceHelper::importVMFile('tables/order_histories.php');

				$controller = new VirtuemartControllerOrders();
				
	    		/* Load the view object */
				$view = $controller->getView('orders', 'html');
		
				/* Load the helper */
				$view->loadHelper('shopFunctions');
				$view->loadHelper('vendorHelper');
		
				/* Update the statuses */
				//$model = $controller->getModel('orders');
				
				
	    		$model = VmModel::getModel('orders');
	    		
				// single order is in POST but we need an array
				$orderPass = array();
				$orderPass['order_id']=(int)$order->order_id;
				$orderPass['virtuemart_order_id']=$order->order_id;
				$orderPass['current_order_status']=$order->order_status;
				$orderPass['order_status']=$newStatus;
				$orderPass['comments']=$comments;
				$orderPass['customer_notified']=($notify=='Y' OR $notify=='YF') ? 1 : 0;
				$orderPass['customer_send_comment']= $includeComment ? 1 : 0;
				$orderPass['update_lines']= $updateAllLines ? 1 : 0;

				$formerView = JRequest::getVar('view');
				JRequest::setVar('view','orders'); //this is also for VM checkFilterDir function
				
                // 16.9.2013 dajo: Make sure that e-mail is sent for all the statuses, not just the predefined ones
                //                 (this is also used in VM's updateOrderStatus function
                $model->useDefaultEmailOrderStatus = false;
                
				if (!($result = $model->updateStatusForOneOrder($order->order_id,$orderPass)))
					JError::raiseWarning(0,'Order state not changed by VirtueMart (mail probably not sent). ',print_r($orderPass,true));
				
				JRequest::setVar('view',$formerView);
				
				return $result;
	    	}
	    	else //VM1
	    	{
		    	//get VirtueMart framework
		        global $mosConfig_absolute_path;
		        
		        InvoiceHelper::importVMFile('virtuemart_parser.php',false);
		        InvoiceHelper::importVMFile('classes/ps_order.php');

				//build "fake" array
		    	$d=JArrayHelper::fromObject($order);
		    		
		    	$d['order_id']=$order->order_id;
		    	$d["current_order_status"]=$order->order_status;
		    	$d["order_status"]=$d['new_order_status']=$newStatus;
		    	$d['notify_customer']= (isset($notify) AND ($notify=='Y' OR $notify=='YF')) ? 'Y' : 'N';
		    	$d["order_comment"]='';
		    		    	
		    	//pass it to VM function
		    	$vmOrder = new vm_ps_order;	
		    	$return = $vmOrder->order_status_update($d);
		    	if (!$return)
		    		JError::raiseWarning(0,'Order state not changed by VirtueMart (mail probably not sent). ',print_r($d,true));
	
		    	return $return;
    		}
		}
		else
			return 'no change';
	}
	
	/**
	 * VM2: parse back attributes set in form into original JSON array. its backward process of how it is done in tmpl/products.php
	 */
	static function addAttributesFromRequestToJson($i, $post)
	{
		$attributesTextareas =  (array)@$post['product_attribute']; //normal attributes (VM1 or as fallback)
		$default = isset($attributesTextareas[$i]) ? $attributesTextareas[$i] : null;
		$attributes = array();
		
		$keys = (array)@$post['product_attribute_key'];
		$names = (array)@$post['product_attribute_name'];
		$values = (array)@$post['product_attribute_value'];
		$pluginKeys = (array)@$post['product_attribute_plugin_key'];
		$pluginKeys2 = (array)@$post['product_attribute_plugin_key2'];
		$pluginVals2 = (array)@$post['product_attribute_plugin_val2'];
		$textValues = (array)@$post['product_attribute_textvalue'];
		$customValues = (array)@$post['product_attribute_custom'];
		
		if (!isset($keys[$i]))
			return $default;
		
		foreach ($keys[$i] as $j => $key){

			if ($key=='')
				$key = null;
			
			if (isset($customValues[$i][$j])) //we have custom plugins output
				$attributes[$key] = $customValues[$i][$j];
			elseif (isset($names[$i][$j], $values[$i][$j])) //simple VM name: value
				$attributes[$key] = '<span class="costumTitle">'.$names[$i][$j].'</span><span class="costumValue" >'.$values[$i][$j].'</span>';
			elseif (isset($pluginKeys[$i][$j], $pluginKeys2[$i][$j], $pluginVals2[$i][$j])) //simple plugin format (probably not needed any more)
				$attributes[$key] = array($pluginKeys[$i][$j] => array($pluginKeys2[$i][$j] => $pluginVals2[$i][$j]));
			elseif (isset($textValues[$i][$j]))//json in text
				$attributes[$key] = (($decoded = json_decode($textValues[$i][$j], true))!==null) ? $decoded : $textValues[$i][$j];
			else
				$attributes[$key] = null;
			//else error? 
		}

		return $attributes ? json_encode($attributes) : $default;
	}
}
?>