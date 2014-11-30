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

require_once dirname(__FILE__).'/vm2orderpaymentorshipment.php';

class TableVm2OrderPayment extends TableVm2OrderPaymentOrShipment
{
	protected $_tableName = '#__virtuemart_payment_plg_';
	protected $_pluginType = 'VmPayment';
	protected $_methodVarName = 'virtuemart_paymentmethod_id';

    //store info about current payment
    function store($data, $vmorder)
    {
    	//delete previous records from all plugin tables (except current one). becuase can be only one record in one table.
    	$this->_db->setQuery('SHOW TABLES LIKE '.$this->_db->Quote(str_replace('#__',$this->_db->getPrefix(),'#__virtuemart_payment_plg_%')));
    	$pluginTables = invoiceHelper::loadColumn($this->_db);
    	$deleted = false;
    	foreach ($pluginTables as $pluginTable)	{ //is with real db prefix
    		
    		$pluginTable = preg_replace('#^'.preg_quote($this->_db->getPrefix()).'#i','#__',$pluginTable);
    		
    		if ($pluginTable==$this->_tbl) //not delete record from current table (can be edited or new)
    			continue;
    			
    		$this->_db->setQuery('DELETE FROM '.$pluginTable.' WHERE virtuemart_order_id='.(int)$data['order_id']);
    		if (!$this->_db->query())
    			JError::raiseWarning(0,'Could not delete former payment from '.$pluginTable);
    		if ($this->_db->getAffectedRows()>0)
    			$deleted = true;
    	}
    	
    	//check if table for payment plugin exists
		if (!InvoiceHelper::checkTableExists($this->_tbl)){
			$this->setError('Payment table '.$this->_tbl.' does not exist.');
			return false;
		}
			
       	$this->_db->setQuery('SELECT `id`, `virtuemart_paymentmethod_id` FROM `'.$this->_tbl .'` WHERE `virtuemart_order_id` = ' . (int) $data['order_id']);
       	$formerRecord = $this->_db->loadObject();
       	$methodChanged = ($deleted || !$formerRecord || ($data['payment_method_id']!=$formerRecord->virtuemart_paymentmethod_id));
        $now = gmdate('Y-m-d H:i:s');
        $currentUser = JFactory::getUser();
        //better use this, because object is auto-filled since J1.6. not all plugin tables have all fields.
        $fields = InvoiceHelper::getTableColumns($this->_db, $this->_tbl);
        
    	$this->id = $formerRecord ? $formerRecord->id : null;
    	$this->virtuemart_order_id = $data['order_id'];
    	$this->order_number = $data['order_number'];
    	$this->virtuemart_paymentmethod_id = $data['payment_method_id'];

    	//update payment info only when payment method changed
    	if ($methodChanged){
    	
    		$paymentInfo = invoiceGetter::getPaymentMethod($data['payment_method_id'], $data['order_language']);
    		
    		//update "paid" price
	    	if (isset($fields['payment_order_total'])){
	    		$this->payment_order_total = $data['order_total'];
	    		if ($vmorder->user_currency_rate) //multiply by exchnage rate
	    			$this->payment_order_total *= $vmorder->user_currency_rate;
	    	}
	    	
	    	if (isset($fields['payment_currency'])){
	    		
				//TODO: pre-select for new order based on pyamrn method seettings?

	    		$this->payment_currency = $data['user_currency_id'] ? $data['user_currency_id'] : $data['order_currency'];

	    		if (stripos($fields['payment_currency'], 'char')!==false){ //they need code (maybe)
	    			$currencies = invoiceGetter::getCurrencies();
	    			if (isset($currencies[$this->payment_currency]))
	    				$this->payment_currency = $currencies[$this->payment_currency]->currency_code;
	    		}
	    		//ale ty tabulky tam maji bordel.
	    	}
	    	
	    	if (isset($fields['email_currency'])){
	    		
	    		if (@$paymentInfo->email_currency == 'vendor')
	    			$this->email_currency = $data['order_currency'];//what here? vendor is usually currecy of order, so se order one
	    		elseif (is_numeric(@$paymentInfo->email_currency))
	    			$this->email_currency = $paymentInfo->email_currency; //maybe forward compatible
	    		else //"payment" no not specified
	    			$this->email_currency = $data['user_currency_id'] ? $data['user_currency_id'] : $data['order_currency']; //id inside
	    	}

    		if (isset($fields['payment_name']))
	    		$this->payment_name = $this->getMethodName($data);

			if (isset($fields['cost_per_transaction']) && isset($paymentInfo->cost_per_transaction)) 
		    	$this->cost_per_transaction = $paymentInfo->cost_per_transaction;
		    		
		    if (isset($fields['cost_percent_total']) && isset($paymentInfo->cost_percent_total)) 
		    	$this->cost_percent_total = $paymentInfo->cost_percent_total;
		    		
		    if (isset($fields['tax_id']) && isset($paymentInfo->tax_id)) 
		    	$this->tax_id = $paymentInfo->tax_id;
    	}
    	
    	if (isset($fields['modified_on'])) 
    		$this->modified_on = $now;
    	if (isset($fields['modified_by'])) 
	    	$this->modified_by = $currentUser->id;
	    if (!$this->id){
	    	if (isset($fields['created_on'])) 
	        	$this->created_on = $now;
	        if (isset($fields['created_by'])) 
	        	$this->created_by = $currentUser->id;
	    }

    	return parent::store();;
    }
    
    function getMethodName($data)
    {
    	if (!($name = $this->renderPluginNameVM())){
    		$paymentInfo = invoiceGetter::getPaymentMethod($data['payment_method_id'], $data['order_language']);
    		$name = '<span class="vmpayment_name">'.@$paymentInfo->payment_name.'</span><span class="vmpayment_description">'.@$paymentInfo->payment_desc.'</span><br />'.@$paymentInfo->payment_info; //? VM...
    	}

    	return $name;
    }
}

?>