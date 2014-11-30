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

class TableVm2OrderShipment extends TableVm2OrderPaymentOrShipment
{
    var $id = null;
    var $virtuemart_order_id = null;
    var $order_number = null;
    var $virtuemart_shipmentmethod_id = null;
    var $shipment_name = null;
    var $order_weight = null;
    var $shipment_weight_unit = null;
    var $shipment_cost = null;
    var $shipment_package_fee = null;
    var $tax_id = null;
    var $created_on = null;
    var $created_by = null;
    var $modified_on = null;
    var $modified_by = null;

    protected $_tableName = '#__virtuemart_shipment_plg_';
    protected $_pluginType = 'VmShipment';
    protected $_methodVarName = 'virtuemart_shipmentmethod_id';
    
    //store info about current shipment
    function store($data)
    {
    	//delete previous records from all plugin tables. becuase can be only one record in one table.
    	$this->_db->setQuery('SHOW TABLES LIKE '.$this->_db->Quote(str_replace('#__',$this->_db->getPrefix(),'#__virtuemart_shipment_plg_%')));
    	$pluginTables = invoiceHelper::loadColumn($this->_db);
    	$deleted = false;
    	foreach ($pluginTables as $pluginTable)	{ //is with real db prefix
    		
    		$pluginTable = preg_replace('#^'.preg_quote($this->_db->getPrefix()).'#i','#__',$pluginTable);
    		
    		if ($pluginTable==$this->_tbl) //not delete record from current table (can be edited or new)
    			continue;
    			
    		$this->_db->setQuery('DELETE FROM '.$pluginTable.' WHERE virtuemart_order_id='.(int)$data['order_id']);
    		if (!$this->_db->query())
    			JError::raiseWarning(0,'Could not delete former shipment record from '.$pluginTable);
    		if ($this->_db->getAffectedRows()>0)
    			$deleted = true;
    	}
    	
    	//check if table for shipment plugin exists
		if (!InvoiceHelper::checkTableExists($this->_tbl)){
			$this->setError('Shipment table '.$this->_tbl.' does not exist.');
			return false;
		}
		
       	$this->_db->setQuery('SELECT `id`, `virtuemart_shipmentmethod_id` FROM `'.$this->_tbl .'` WHERE `virtuemart_order_id` = ' . (int) $data['order_id']);
       	$formerRecord = $this->_db->loadObject();
       	$methodChanged = ($deleted || !$formerRecord || ($data['shipment_method_id']!=$formerRecord->virtuemart_shipmentmethod_id));	
		$now = gmdate('Y-m-d H:i:s');
        $currentUser = JFactory::getUser();
       	
        //TODO: we cannot use plugin functions directly, because it needs whole cart object now. maybe we will have to simulate it.
    	$this->id = $formerRecord ? $formerRecord->id : null;
    	$this->virtuemart_order_id = $data['order_id'];
    	$this->order_number = $data['order_number'];
    	$this->virtuemart_shipmentmethod_id = $data['shipment_method_id'];

    	$weightUnit = false;
    	
    	//find proper shipment record, than store its info to this order-shipment table. 
    	foreach (invoiceGetter::getShippingsVM2($data['order_language']) as $shipping) 
    		if ($shipping->shipping_rate_id == $this->virtuemart_shipmentmethod_id){
    			if ($methodChanged){
	    			$this->shipment_name = $this->getMethodName($shipping);
			    	$this->shipment_cost = isset($shipping->cost) ? $shipping->cost : null;
			    	$this->shipment_package_fee = isset($shipping->package_fee) ? $shipping->package_fee : null;
			    	$this->tax_id = isset($shipping->tax_id) ? $shipping->tax_id : null;
    			}
		    	$weightUnit = @$shipping->weight_unit;
    		}
    	
    	//update weight and unit. lets hope it will work for other than default plugins also
    	$weight = $this->getOrderWeight($data, $weightUnit);	
    	$this->order_weight = $weight!==false ? $weight : null;
		$this->shipment_weight_unit = $weight!==false ? $weightUnit : null;
		
    	$this->modified_on = $now;
	    $this->modified_by = $currentUser->id;
	    if (!$this->id){
	        $this->created_on = $now;
	        $this->created_by = $currentUser->id;
	    }
	    
    	return parent::store();
    }
    
    //similar to plg_weight_countries function
    function getOrderWeight($data, $toUnit) {

    	if (!$toUnit)
    		return false;
    	
    	$weight = 0;
    	InvoiceHelper::importVMFile('helpers/shopfunctions.php');
    	foreach ($data['order_item_id'] as $i => $orderItemId) {
    		if (($productId = $data['product_id'][$i])){
    			
    			if (!($product = invoiceGetter::getProduct($productId)) OR !isset($data['product_quantity'][$i])) //if error, dont compute! 
    				return false;
    			
    			$quantity = $data['product_quantity'][$i];
    			$weight += (ShopFunctions::convertWeigthUnit($product->product_weight, $product->product_weight_uom, $toUnit) * $quantity);
    		}
    	}

		return $weight;
	}
	
	function getMethodName($shippingInfo)
	{
		if (!($name = $this->renderPluginNameVM()))
			$name = '<span class="vmshipment_name">'.@$shippingInfo->name.'</span><span class="vmshipment_description">'.$shippingInfo->desc.'</span>'; //? VM...

		return $name;
	}
}

?>