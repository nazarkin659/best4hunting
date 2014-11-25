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

class TableVm2Order extends JTable
{
	//technically, not needed there J 1.6 and more does this automatically (but in rare cases can be used J1.5 and VM2)
    var $virtuemart_order_id = null;
    var $virtuemart_user_id = null;
    var $virtuemart_vendor_id = null;
    var $order_number = null;
    var $order_pass = null;
    var $order_total = null;
    var $order_salesPrice = null;
    var $order_billTaxAmount = null;
    var $order_billDiscountAmount = null;
    var $order_discountAmount = null;
    var $order_subtotal = null;
    var $order_tax = null;
    var $order_shipment = null;
    var $order_shipment_tax = null;
    var $order_payment = null;
    var $order_payment_tax = null;
    var $coupon_discount = null;
    var $coupon_code = null;
    var $order_discount = null;
    var $order_currency = null;
    var $order_status = null;
    var $user_currency_id = null;
    var $user_currency_rate = null;
    var $virtuemart_paymentmethod_id = null;
    var $virtuemart_shipmentmethod_id = null;
    var $customer_note = null;
    var $ip_address = null;
    var $created_on = null;
    var $created_by = null;
    var $modified_on = null;
    var $modified_by = null;
    
    function __construct(&$db)
    {
        parent::__construct('#__virtuemart_orders', 'virtuemart_order_id', $db);
    }

    function bind($data)
    {
    	$rename = array(  //if we are using other "inner" name than db name
    		'order_id'=>'virtuemart_order_id', 
    		'user_id'=>'virtuemart_user_id', 
    		'vendor_id'=>'virtuemart_vendor_id', 
    		'status'=>'order_status', 
    		'vendor'=>'virtuemart_vendor_id', 
    		'order_shipping'=>'order_shipment', 
    		'order_shipping_tax'=>'order_shipment_tax', 
    		'payment_method_id'=>'virtuemart_paymentmethod_id', 
    		'shipment_method_id'=>'virtuemart_shipmentmethod_id'
    	);
    	
    	foreach ($rename as $postName => $dbName)
    		if (isset($data[$postName]))
    			$data[$dbName] = $data[$postName];
    	
        parent::bind($data);

        $currentUser = JFactory::getUser();
        $this->modified_on = gmdate('Y-m-d H:i:s');
        $this->modified_by = $currentUser->id;
        if (!$this->virtuemart_order_id){
            $this->created_on = gmdate('Y-m-d H:i:s');
            $this->created_by = $currentUser->id;
        }

        if (property_exists($this, 'ip_address') AND !$this->ip_address) //do not rewrite (?)
        	$this->ip_address = $_SERVER['REMOTE_ADDR'];
        
        if (InvoiceHelper::vmCersionCompare('2.0.22') < 0) //since 2.0.22 negative
        	$this->order_discountAmount = $this->order_discountAmount * (-1); 
        
        //since 2.0.22, coupon discount is stored as negative (properly). before it, we must swicth ot to positive
        if (InvoiceHelper::vmCersionCompare('2.0.22') >= 0)
        	$this->coupon_discount = - ($this->coupon_discount);   
             
        $this->order_discount = $this->order_discountAmount; //...
        
        if (empty($this->user_currency_id)) //if empty, set same as order currency
        	$this->user_currency_id = $this->order_currency;

        if ($this->user_currency_id == $this->order_currency) //rate is 1
       		$this->user_currency_rate = 1;
        
        //new order: generate new order number and password
    	//from admin virtuemart/models/orders line 588
    	if (empty($this->virtuemart_order_id)){
    		
    		//do it VM style. first ask plugins for order number+pass, then do it by ourselves
			JPluginHelper::importPlugin('vmshopper');
			$dispatcher = JDispatcher::getInstance();
    		$dispatcher->trigger('plgVmOnUserOrder',array(&$this));

			if(empty($this->order_number))		
				$this->order_number = $this->generateOrderNumber($this->virtuemart_user_id,4, $this->virtuemart_vendor_id);
				
			if(empty($this->order_pass))
				$this->order_pass = 'p_'.substr( md5((string)time().$this->order_number ), 0, 5);
    	}
    }
    
    /**
     * Call VM function or do it on our own.
     * 
     * @param int $uid
     * @param int $length
     * @param int $virtuemart_vendor_id
     * 
     * @return string
     */
	function generateOrderNumber($uid = 0,$length=10, $virtuemart_vendor_id)
	{
		//note: since somewhere between VM 2.0.3 and 2.0.6, generateOrderNumber became public, so we can call it
		invoiceHelper::importVMFile('models/orders.php');
		$modelOrders = new VirtueMartModelOrders();
		if (is_callable(array($modelOrders, 'generateOrderNumber')))
			return $modelOrders->generateOrderNumber($uid, $length, $virtuemart_vendor_id);
		
		//else, this is just replication of this function
		$db = JFactory::getDBO();
		$q = 'SELECT COUNT(1) FROM #__virtuemart_orders WHERE `virtuemart_vendor_id`="'.$virtuemart_vendor_id.'"';
		$db->setQuery($q);

		//We can use that here, because the order_number is free to set, the invoice_number must often follow special rules
		$count = $db->loadResult();
		$count = $count + (int)VM_ORDER_OFFSET;

		$data = substr( md5( session_id().(string)time().(string)$uid )
		,0
		,$length
		).'0'.$count;

		return $data;
	}
}



?>