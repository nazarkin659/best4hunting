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

class TableVm2OrderCalcRules extends JTable
{
    var $virtuemart_order_calc_rule_id = null;
    var $virtuemart_order_id = null;
    var $virtuemart_vendor_id = null;
    var $calc_rule_name = null;
    var $calc_kind = null;
    var $calc_amount = null;
    var $created_on = null;
    var $created_by = null;
    var $modified_on = null;
    var $modified_by = null;
    
    function __construct(&$db)
    {
        parent::__construct('#__virtuemart_order_calc_rules', 'virtuemart_order_calc_rule_id', $db);
        
        foreach (invoiceGetter::getOrderCalcRulesFields() as $field) //create object vars dynamically, can differ. J1.6's JTable does that also, but we can be using J1.5 in rare cases.
        	$this->$field = null;
    }

    function store(&$data)
    {
    	return false;
    }
    
    /**
     * Add/update calc rules for ordered item, similar of how ordered items are saved.
     * We must delete also rules that are deleted.
     * 
     * @param unknown $i			key of ordered item in multi dimenasional vars. SPECIAL: -1: general rules per bill, -2 for shipment rules, -3 for payment.
     * @param unknown $orderItemId	order item id we are conserning about. can be null if $i < 0
     * @param unknown $data			POST data
     * @return boolean
     */
    function storeRulesPerItem($i, $orderItemId, $data)
    {
    	$ok = true;
    	$stored = array();
    	$now = gmdate('Y-m-d H:i:s');
    	$currentUser = JFactory::getUser();

    	if (isset($data['calc_rule_name'][$i])) foreach ($data['calc_rule_name'][$i] as $j => $calcRuleName) {
    		
    		$this->reset();
    		
    		$this->virtuemart_order_item_id = ($i<0) ? 'NULL' : $orderItemId;
    		
    		$this->calc_mathop = $data['calc_mathop'][$i][$j];
    		$this->calc_value = $data['calc_value'][$i][$j]*1;
    		$this->calc_currency = (int)$data['calc_currency'][$i][$j];    		
    		
    		//pk. null for new.
    		$this->virtuemart_order_calc_rule_id = $data['virtuemart_order_calc_rule_id'][$i][$j] ? $data['virtuemart_order_calc_rule_id'][$i][$j] : null;

    		$this->virtuemart_order_id = $data['order_id'];
    		$this->virtuemart_vendor_id = $data['vendor'];
    		
    		$this->calc_rule_name = htmlspecialchars_decode($data['calc_rule_name'][$i][$j]);
    		$this->calc_kind = $data['calc_kind'][$i][$j];
    		
    		//calc_amount is used in vm, only for general rules (...)
    		if ($i==-1)
    			$this->calc_amount =  $data['calc_amount'][$i][$j];
    		
    		$this->modified_on = $now;
    		$this->modified_by = $currentUser->id;
    		if (!$this->virtuemart_order_calc_rule_id){ //new record
    			$this->created_on = $now;
    			$this->created_by = $currentUser->id;
    		}
    		
    		//store items
    		if (!parent::store())
    			$ok = false;
    		else
    			$stored[] = $this->virtuemart_order_calc_rule_id;
    	}
    	
    	if ($ok){ //delete rules for this order/item/shipping/payment that was not saved now.
    		
    		//NOTE: must follow same structure as in getter::getOrderCalcRules
    		
    		if ($i==-1) //general rules: empty item id and NOT shipment or payment.
    			$cond = '(`virtuemart_order_item_id` IS NULL OR `virtuemart_order_item_id`=0) AND `calc_kind` != '.$this->_db->Quote('shipment').' AND `calc_kind` != '.$this->_db->Quote('payment');
    		elseif ($i==-2)
    			$cond = '`calc_kind` = '.$this->_db->Quote('shipment');
    		elseif ($i==-3)
    			$cond = '`calc_kind` = '.$this->_db->Quote('payment');
    		else
    			$cond = '`virtuemart_order_item_id` = ' . (int)$orderItemId;
    		
    		if ($stored)
    			$cond .= ' AND virtuemart_order_calc_rule_id NOT IN ('.implode(', ', $stored).')';
    					
    		$this->_db->setQuery('DELETE FROM `#__virtuemart_order_calc_rules` WHERE '.$cond);
    		if (!$this->_db->query()){
    			$this->setError($this->_db->getErrorMsg());
    			$ok = false;}
    	}
    	
    	return $ok;
    }
    
    /**
     * Delete all calc rules for order item id.
     * 
     * @param int $orderItemId
     * @return boolean
     */
    function deleteRulesPerItem($orderItemId)
    {
    	$this->_db->setQuery('DELETE FROM `#__virtuemart_order_calc_rules` WHERE `virtuemart_order_item_id` = ' . (int)$orderItemId);
    	if (!$this->_db->query()){
    		$this->setError($this->_db->getErrorMsg());
    		return false;}
    	
    	return true;
    }
}

?>