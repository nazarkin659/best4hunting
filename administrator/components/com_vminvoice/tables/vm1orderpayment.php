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

class TableVm1OrderPayment extends JTable
{
    var $order_id = null;
    var $payment_method_id = null;
    var $order_payment_code = null;
    var $order_payment_number = null;
    var $order_payment_expire = null;
    var $order_payment_name = null;
    var $order_payment_log = null;
    var $order_payment_trans_id = null;

    /**
     * Create object. Set database connector. Set table name and primary key.
     * Table #__vm_order_payment hasn't primary key. Is used order_id and before storing is controled.
     * 
     * @param JDatabaseMySQL $db
     */
    function __construct(&$db)
    {
        parent::__construct('#__vm_order_payment', 'order_id', $db);
    }

    /**
     * Store object. Control if order_id exist in database to recognise update/insert operation.
     * 
     * @return booelan true/false - succed/unsucced 
     */
    function store()
    {
        $this->_db->setQuery('SELECT COUNT(*) FROM `#__vm_order_payment` WHERE `order_id` = ' . (int) $this->order_id);
        if ($this->_db->loadResult() == 0)
            $ret = $this->_db->insertObject($this->getTableName(), $this, $this->getKeyName());
        $ret = $this->_db->updateObject($this->getTableName(), $this, $this->getKeyName());
        
    	if( !$ret ){
			$this->setError(get_class( $this ).'::store failed - '.$this->_db->getErrorMsg());
			return false;
		}
		else {
			return true;
		}
    }
}

?>