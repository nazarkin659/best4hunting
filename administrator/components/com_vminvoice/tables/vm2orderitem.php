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

class TableVm2OrderItem extends JTable
{
    var $virtuemart_order_item_id = null;
    var $virtuemart_order_id = null;
    var $virtuemart_vendor_id = null;
    var $virtuemart_product_id = null;
    var $order_item_sku = null;
    var $order_item_name = null;
    var $product_quantity = null;
    var $product_item_price = null;
    var $product_tax = null;
    var $product_basePriceWithTax = null;
    var $product_final_price = null;
    var $product_subtotal_discount = null;
    var $product_subtotal_with_tax = null;
    var $order_item_currency = null;
    var $order_status = null;
    var $product_attribute = null;
    var $created_on = null;
    var $created_by = null;
    var $modified_on = null;
    var $modified_by = null;
    
    function __construct(&$db)
    {
        parent::__construct('#__virtuemart_order_items', 'virtuemart_order_item_id', $db);
    }

    function store(&$data)
    {
    	$rename = array(  //if we are using other "inner" name than db name
    		'virtuemart_order_item_id'=>'order_item_id',
    		'virtuemart_vendor_id'=>'vendor_id',
    		'virtuemart_product_id' => 'product_id', 
    		'product_subtotal_discount' => 'product_price_discount', 
    		'product_basePriceWithTax' => 'product_price_with_tax'
    	);
    	
        $now = gmdate('Y-m-d H:i:s');
        $currentUser = JFactory::getUser();
        
        $vars = get_object_vars($this);
        
        //order item status. DON't SAVE it here (only on initial??). Instead use VM function for update item status.
        //but only if not checked "update for all items" (?) not needed. if it is same status, vm should not not do anything.
        
        //SCENARIO: if adding or deleting product, bind order status and use own function to adjust stock
        //if not, DONT change status and adjust stock, instead save first and than call VM function to change status separately
        $newStatuses = array();
        $stored = array();
        
        foreach ($data['order_item_id'] as $i => $orderItemId) { //items to update/add
        	
            foreach ($vars as $param => $value)
                if ($param[0] != '_'){
                	$name = (isset($rename[$param]) ? $rename[$param] : $param);
                	if (isset($data[$name]))
                    	$this->$param = htmlspecialchars_decode(@reset(array_slice($data[$name], $i, 1)));
             	}
             	
             	
            $this->product_attribute = VMInvoiceModelOrder::addAttributesFromRequestToJson($i, $data);
             			
            $this->order_status = null; //NOT update status here. use always VM functions (to adjust stock)
            if (!$this->virtuemart_order_item_id)
                $this->order_status = 'N';	//if ADDING product, set as NEW.
            
            //if changed quantity, set status to "N" to neutralize stock, than (after save) set new status with new quantity. thats only way to use VM functions.
            if ($this->virtuemart_order_item_id){
            	$class  = get_class($this);
            	$oldTable = new $class($this->_db);
            	if (!($oldTable->load($this->virtuemart_order_item_id)))
            		JError::raiseWarning(0, 'Cannit load old row for ordered item '.$this->virtuemart_order_item_id.'. '.$oldTable->getError());
            	elseif ($oldTable->product_quantity!=$this->product_quantity) //changed quantity!
            		$this->changeItemStatus((int)$data['order_id'], $this->virtuemart_order_item_id, 'N'); //change status to "N" (reset stock) with old quantity
            	unset($oldTable);
            }
            
            $this->virtuemart_order_id = $data['order_id'];
	        $this->modified_on = $now;
	        $this->modified_by = $currentUser->id;
	        if (!$this->virtuemart_order_item_id){
	            $this->created_on = $now;
	            $this->created_by = $currentUser->id;
	        }

	        //some price tweaks
	        //basically reversed what is done in invoiceGetter::getOrderItems
	        
	        //frm 2.0.16 to 2.0.20, discount was stored as positive value, so we need to convert
	        $discountPositive = (InvoiceHelper::vmCersionCompare('2.0.15') > 0 AND InvoiceHelper::vmCersionCompare('2.0.22') < 0);
	        $disocuntNegative = $this->product_subtotal_discount;
	        if ($discountPositive) //-1 discount will become 1
	        	$this->product_subtotal_discount = -1*$disocuntNegative;
	        $this->product_subtotal_discount = $this->product_subtotal_discount * $this->product_quantity; //stored for all, we use it form item (:-/)
	        
	        //since VM 2.0.11, product_tax is stored per-item (in older versions it was for item*quantity)
			$taxPerItem = InvoiceHelper::vmCersionCompare('2.0.11') >= 0;
	        $this->product_tax = $taxPerItem ? $this->product_tax : ($this->product_tax * $this->product_quantity); 

	        $this->product_final_price = $this->product_subtotal_with_tax; //its simple totoal price /quantity :)
	        $this->product_subtotal_with_tax = $this->product_subtotal_with_tax * $this->product_quantity; //in database it is for all quantity, we use ut for 1 item.. (:-/)
	        
	        $this->order_item_currency = null;
	        if (!trim($this->product_attribute)) //? not change?
	        	$this->product_attribute = null;

	        //non existing fields yet
	        if (InvoiceHelper::vmCersionCompare('2.0.22') < 0){
	        	unset($this->product_discountedPriceWithoutTax);
	        	unset($this->product_priceWithoutTax);
	        }
	        
        	//store item
        	parent::store();
        	
            $stored[$i] = $this->virtuemart_order_item_id;
            $newStatuses[$this->virtuemart_order_item_id] = !empty($data['order_status'][$i]) ? $data['order_status'][$i] : (!empty($data['status']) ? $data['status'] : 'P'); //status not selected: use order status, if not selected, use pending
        }
        
        //items to delete:

        //change item status to "Cancelled" (to adjust stock) and than delete products
        $this->_db->setQuery('SELECT virtuemart_product_id, virtuemart_order_item_id FROM `#__virtuemart_order_items` WHERE `virtuemart_order_id` = ' .(int) $data['order_id'] . ($stored ? ' AND `virtuemart_order_item_id` NOT IN (' . implode(',', $stored) . ')' : ''));
        if ($itemsDelete = $this->_db->loadObjectList())
        	foreach ($itemsDelete as $itemDelete)
        		$this->changeItemStatus((int)$data['order_id'], $itemDelete->virtuemart_order_item_id, 'X');
        		
        //delete items
        $this->_db->setQuery('DELETE FROM `#__virtuemart_order_items` WHERE `virtuemart_order_id` = ' . (int)$data['order_id'] . ($stored ? ' AND `virtuemart_order_item_id` NOT IN (' . implode(',', $stored) . ')' : ''));
        $this->_db->query();

        //now update order status for edited/added items
        $this->_db->setQuery('SELECT virtuemart_order_item_id FROM `#__virtuemart_order_items` WHERE `virtuemart_order_id` = ' . (int)$data['order_id']);
        
        foreach (invoiceHelper::loadColumn($this->_db) as $orderItemId)
        	if (isset($newStatuses[$orderItemId]))
        		$this->changeItemStatus((int)$data['order_id'], $orderItemId, $newStatuses[$orderItemId]);     

        //add/update calculation rules for added/updated products
        foreach ($stored as $i => $orderItemId){
        	$tableCalcRules = JTable::getInstance('Vm2OrderCalcRules', 'Table');
        	if (!$tableCalcRules->storeRulesPerItem($i, $orderItemId, $data))
        		JError::raiseWarning(0, 'Cannot update calculation rules for ordered item '.$orderItemId.'. '.implode("\r\n", $tableCalcRules->getErrors()));
        }
        
        //delete calc rules records for deleted products
        if ($itemsDelete)
        	foreach ($itemsDelete as $itemDelete){
        		$tableCalcRules = JTable::getInstance('Vm2OrderCalcRules', 'Table');
        		if (!$tableCalcRules->deleteRulesPerItem($itemDelete->virtuemart_order_item_id))
        			JError::raiseWarning(0, 'Cannot delete calculation rules for ordered item '.$itemDelete->virtuemart_order_item_id.'. '.$tableCalcRules->getError());
        	}
        
        return true;
    }
    
    /**
     * Use VM code for change item status. Or just update stock when quantity changes.
     */
    function changeItemStatus($orderId, $orderItemId, $newStatus)
    {
    	$formerView = JRequest::getVar('view');
		JRequest::setVar('view','orders'); //this is also for VM checkFilterDir function. else fatal error because of JRegistry error
							
    	InvoiceHelper::importVMFile('helpers/vmmodel.php');
    	InvoiceHelper::importVMFile('models/orders.php');
    	InvoiceHelper::importVMFile('tables/order_items.php');
    	InvoiceHelper::importVMFile('tables/orders.php');
    	$model = VmModel::getModel('orders');
    	
    	$input = array();
    	$input[$orderItemId] = array('order_status' => $newStatus);
    		
		foreach ($input as $key=>$value) {

			if (!isset($value['comments'])) $value['comments'] = '';

			$data = (object)$value;
			$data->virtuemart_order_id = $orderId;

			$model->updateSingleItem((int)$key, $data);
		}
		
		JRequest::setVar('view',$formerView);
    }
}

?>