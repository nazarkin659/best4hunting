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

class VMInvoiceModelInvoices extends JModelLegacy
{
    
    var $_data = null;
    var $_pagination = null;

    function __construct ()
    {
        //global $mainframe, $option;
        $mainframe = JFactory::getApplication();
        $option = JRequest::getString('option');
        parent::__construct();
        /*
        $array = JRequest::getVar('cid', 0, '', 'array');
        $this->setId((int) $array[0]);
        */
        $limit = $mainframe->getUserStateFromRequest('global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
        $limitstart = $mainframe->getUserStateFromRequest($option . 'limitstart', 'limitstart', 0, 'int');
        $this->setState('limit', $limit);
        $this->setState('limitstart', $limitstart);
    }

    /*
    function setId ($id)
    {
        // Set id and wipe data
        $this->_id = $id;
        $this->_data = null;
    }
	*/
    function getData ($sort = false)
    {
        $this->_data = $this->_getList($this->_buildQuery(false, $sort), $this->getState('limitstart'), $this->getState('limit'));
        return $this->_data;
    }

    function getPagination ()
    {
        if (empty($this->_pagination)) {
            jimport('joomla.html.pagination');
            $total = $this->_getListCount($this->_buildQuery(true));
            $this->_pagination = new JPagination($total, $this->getState('limitstart'), $this->getState('limit'));
        }
        
        return $this->_pagination;
    }

    /**
     * Returns the list of sorting options for batch download of PDFs
     */
    function getSortOptions ()
    {
        static $opts = null;
        if (is_null($opts)) {
            $vals = array('COM_VMINVOICE_SORT_BY' => '', 'COM_VMINVOICE_ORDER_ID' => 'order_id', 'COM_VMINVOICE_INVOICE_NUM' => 'invoice_num');
            
            $opts = array();
            foreach ($vals as $key => $val) {
                $opt = new stdClass();
                $opt->value = $val;
                $opt->text = JText::_($key);
                $opts[] = $opt;
            }
        }
        
        return $opts;
    }
    
    /**
     * Returns the list of sorting direction options (asc/desc)
     */
    function getSortDirOptions ()
    {
        static $opts = null;
        if (is_null($opts)) {
            $vals = array('COM_VMINVOICE_SORT_DESC' => 'desc', 'COM_VMINVOICE_SORT_ASC' => 'asc');
            
            $opts = array();
            foreach ($vals as $key => $val) {
                $opt = new stdClass();
                $opt->value = $val;
                $opt->text = JText::_($key);
                $opts[] = $opt;
            }
        }
        
        return $opts;
    }

    function _buildQuery ($countOnly = false, $batch_sort = false)
    {
    	$params = InvoiceHelper::getParams();
    	
    	//get search values
    	$start_date = strtotime(JRequest::getVar('filter_start_date')); //user input dates are converted to utc by strtotime. orders are in utc
    	$end_date = strtotime(JRequest::getVar('filter_end_date'));
    	$order_status = (array)JRequest::getVar('filter_order_status');
    	$id = JRequest::getVar('filter_id');
    	$inv_prefix = JRequest::getVar('filter_inv_prefix');
    	$inv_no = JRequest::getVar('filter_inv_no');
    	$name = JRequest::getVar('filter_name'); 
    	$email = JRequest::getVar('filter_email'); 
        $start_inv_date = strtotime(JRequest::getVar('filter_start_inv_date'));
        $end_inv_date = strtotime(JRequest::getVar('filter_end_inv_date'));
        
        $shipping = JRequest::getVar('filter_shipping');
        
        $sort_by = $batch_sort ? JRequest::getVar('batch_sort_by', '') : JRequest::getVar('filter_order', 'order_id');
        $sort_dir = $batch_sort ? JRequest::getVar('batch_sort_dir', '') : JRequest::getVar('filter_order_Dir', 'desc');
           
    	//build search conditions
    	$where=array();
    	
    	$joinMailsended = $countOnly ? false : true; //join VM invoice table?
    	$joinOrderUserInfo = $countOnly ? false : true; //join order user info table ?
    	
    	
    	if(COM_VMINVOICE_ISVM2)
    	{
    		/*
    		if ($this->_id)
	            $where[] = 'o.virtuemart_order_id = '.(int)$id;
	        */
	    	if ($start_date)
	    		$where[] = 'o.created_on > \''.gmdate('Y-m-d H:i:s',$start_date).'\'';
	    	if ($end_date)
	    		$where[] = 'o.created_on <= \''.gmdate('Y-m-d H:i:s',(int)$end_date+86400).'\'';
	    	if (isset($start_inv_date) AND $start_inv_date>0) {
	    		$where[] = 'ms.invoice_date > '.((int)$start_inv_date + (int)date('Z'));
                $joinMailsended = true;
            }
	    	if (isset($end_inv_date) AND $end_inv_date>0) {
	    		$where[] = 'ms.invoice_date <= '.((int)$end_inv_date + (int)date('Z') + 86400);
                $joinMailsended = true;
	    	}
	    	if (isset($id) AND $id>0)
	    		$where[] ='o.virtuemart_order_id = '.(int)$id.' OR o.order_number='.$this->_db->Quote($id);

	        if (isset($inv_no) AND !empty($inv_no)) {
	        	if ($params->get('invoice_number')=='own'){
		        	$where[] = 'ms.invoice_no ='.$this->_db->Quote($inv_no); $joinMailsended = true;}
		        else
		        	$where[] = 'o.virtuemart_order_id  ='.(int)$inv_no.' OR o.order_number = '.$this->_db->Quote($inv_no);
	        }
	        if (isset($email) AND !empty($email)){
	        	$email = invoiceHelper::escape($this->_db, $email);
	            $where[] = "i.email LIKE '%$email%'";
	            $joinOrderUserInfo = true;
	        }
            if (isset($shipping) && !empty($shipping)) {
                $where[] = "o.`virtuemart_shipmentmethod_id` = ".$this->_db->quote($shipping);
            }
    	}
    	else
    	{
    		/*
	        if ($this->_id)
	            $where[] = 'o.order_id = '.(int)$id;
	        */
	    	if (isset($start_date) AND $start_date>0)
	    		$where[] = 'o.cdate > '.(int)$start_date;
	    	if (isset($end_date) AND $end_date>0)
	    		$where[] = 'o.cdate <= '.((int)$end_date+86400);
	    	if (isset($start_inv_date) AND $start_inv_date>0) {
	    		$where[] = 'ms.invoice_date > '.((int)$start_inv_date + (int)date('Z'));
                $joinMailsended = true;
            }
	    	if (isset($end_inv_date) AND $end_inv_date>0) {
	    		$where[] = 'ms.invoice_date <= '.((int)$end_inv_date + (int)date('Z') + 86400);
                $joinMailsended = true;
	    	}
	    	if (isset($id) AND $id>0)
	    		$where[] = 'o.order_id = '.(int)$id;
	        if (isset($inv_no) AND !empty($inv_no)) {
	        	if ($params->get('invoice_number')=='own'){
		        	$where[] = 'ms.invoice_no ='.$this->_db->Quote($inv_no); $joinMailsended = true;}
		        else
		        	$where[] = 'o.order_id  ='.$this->_db->Quote($inv_no).' OR o.order_number = '.$this->_db->Quote($inv_no);
	        }
	        if (isset($email) AND !empty($email)){
	        	$email = invoiceHelper::escape($this->_db, $email);
	            $where[] = "i.user_email LIKE '%$email%'";
	            $joinOrderUserInfo = true;
	        }
            if (isset($shipping) && !empty($shipping)) {
                $shipping = invoiceHelper::escape($this->_db, $shipping);
                $where[] = "o.ship_method_id LIKE '%|%{$shipping}%|%'";
            }
	    }
	    
    	if (!empty($order_status)) { //common for VM 1 and 2
	        $statusCond=array();
	      	foreach ($order_status as $status)
	        	$statusCond[]='o.`order_status` = '.$this->_db->Quote($status);
	        $where[] = '('.implode(' OR ',$statusCond).')';
	    }
	   	if (isset($inv_prefix) AND !empty($inv_prefix)){
	    	$where[] = 'ms.invoice_prefix LIKE \'%'.invoiceHelper::escape($this->_db, $inv_prefix).'%\''; 
	    	$joinMailsended = true;}
	    if (isset($name) AND !empty($name)){
	       $name = invoiceHelper::escape($this->_db, strtolower($name));
	       $where[] = "(i.first_name LIKE '%$name%' OR i.last_name LIKE '%$name%' OR i.company LIKE '%$name%')";
	       $joinOrderUserInfo = true;
	    }
	        
	    //lets believe that there is always only one BT record. else group by would slow query much.
        $where = count($where)>0 ? " WHERE (" . implode(') AND (', $where).')' : '';
        
        $order_by = '';
        if (!$countOnly && !empty($sort_by)) {
            if ($sort_by == 'order_id') {
                $order_by = COM_VMINVOICE_ISVM2 ? 'ORDER BY o.`virtuemart_order_id` ' : 'ORDER BY o.`order_id` ';
            }
            else if ($sort_by == 'invoice_num') {
                $order_by = 'ORDER BY ms.invoice_no ';
            }
            else if ($sort_by == 'created_date') {
                $order_by = COM_VMINVOICE_ISVM2 ? 'ORDER BY o.`created_on` ' : 'ORDER BY o.`cdate` ';
            }
            else if ($sort_by == 'modified_date') {
                $order_by = COM_VMINVOICE_ISVM2 ? 'ORDER BY o.`modified_on` ' : 'ORDER BY o.`mdate` ';
            }
            else if ($sort_by == 'order_total') {
                $order_by = 'ORDER BY o.`order_total` ';
            }
            else if ($sort_by == 'invoice_date') {
                $order_by = 'ORDER BY ms.`invoice_date` ';
            }
            
            if (!empty($order_by)) {
                if (in_array(strtolower($sort_dir), array('asc', 'desc'))) {
                    $order_by .= strtoupper($sort_dir);
                }
                else {
                    $order_by .= 'DESC';
                }
            }
        }
        if (empty($order_by) && !$countOnly) {
            $order_by = COM_VMINVOICE_ISVM2 ? "ORDER BY o.`virtuemart_order_id` DESC" : "ORDER BY o.`order_id` DESC";
        }

        if(COM_VMINVOICE_ISVM2) {
            $shipTable = InvoiceGetter::getVm2LanguageTable('#__virtuemart_shipmentmethods');
            
        	$query = "SELECT 
        	".($countOnly ? "o.virtuemart_order_id" : "o.*, o.virtuemart_order_id AS order_id, 
        	ms.order_id as 'order_mailed', ms.invoice_mailed, ms.dn_mailed, ms.invoice_date, ms.params, 
             i.first_name, i.last_name, i.company, i.email, o.created_on AS created_on , o.modified_on AS modified_on ").",
             sh.shipment_name
    		FROM `#__virtuemart_orders` AS o 
    		".($joinOrderUserInfo ? ' LEFT JOIN `#__virtuemart_order_userinfos` AS i ON (o.`virtuemart_order_id` = i.`virtuemart_order_id` AND i.`address_type` = \'BT\') ' : '')."
    		".($joinMailsended ? ' LEFT JOIN `#__vminvoice_mailsended` AS ms ON (o.`virtuemart_order_id` = ms.`order_id`) ' : '')."    		
            LEFT JOIN `{$shipTable}` sh ON (sh.`virtuemart_shipmentmethod_id` = o.`virtuemart_shipmentmethod_id`)
    	    $where 
    	    ".$order_by;
        }
        else {
        	$query = "SELECT 
        	".($countOnly ? "o.order_id" : " o.*, ms.order_id as 'order_mailed', ms.invoice_mailed, ms.dn_mailed, ms.invoice_date, ms.params, 
             i.first_name, i.last_name, i.company, i.user_email AS email").", o.ship_method_id
    		FROM `#__vm_orders` AS o 
    		".($joinOrderUserInfo ? ' LEFT JOIN `#__vm_order_user_info` AS i ON (o.`order_id` = i.`order_id` AND i.`address_type` = \'BT\') ' : '')."
    		".($joinMailsended ? ' LEFT JOIN `#__vminvoice_mailsended` AS ms ON (o.`order_id` = ms.`order_id`) ' : '')."	
    	    $where 
    	    ".$order_by;
        }

        //NOTE: removed group by, because it slows down query and probability that there will be more than 1  billing address is ... small.
        //GROUP BY o.virtuemart_order_id
        //GROUP BY o.order_id
        
		
    	
    	    
        return $query;
    }
    
    
    /**
     * Update order's states, using POST values.
     * 
     * @param mixed reference to 'order' model
     */
    public function updateStates(&$model)
    {
    	$mainframe = JFactory::getApplication();
    	
        //load variables
    	$this->getData();
    	$postStatuses = JRequest::getVar('status',array(),'post','array');
    	$postNotify = JRequest::getVar('notify',array(),'post','array');
    	$success = 0;
    	//$model = JController::getModel('order','VMInvoice');
    	foreach ($this->_data as $order)
    	{
    		$update = $model->updateState($order->order_id, $postStatuses[$order->order_id], isset($postNotify[$order->order_id]) ? $postNotify[$order->order_id] : 'N','',false,true); //for all products also
    		
    		if ($update===true)
    			$success++;
    		elseif ($update===false)
    			$mainframe->enqueueMessage(JText::_('COM_VMINVOICE_COULD_NOT_CHANGE_STATUS_OR_SEND_NOTIFY_E-MAIL_AT_ORDER_').$order->order_id, 'error');
    	}

    	if ($success==1)
    		$mainframe->enqueueMessage(JText::sprintf('Order status successfully changed'));
    	elseif ($success>1)
    		$mainframe->enqueueMessage(JText::sprintf('Order\'s statuses successfully changed',$success));
    	
    }
    
    /**
     * Add/Edit single invoice number from Invoices view
     */
	public function editInvoiceNo()
    {
    	$db = JFactory::getDBO();
    	$mainframe = JFactory::getApplication();
    	$params = InvoiceHelper::getParams();
       
    	//check posted values
    	$postSubmits = JRequest::getVar('update_inv_no',null,'post','array');
    	$postInvoiceNo = JRequest::getVar('invoice_no',null,'post','array');
    	
    	@$orderNo = key($postSubmits)*1;
    	if (count($postSubmits)!=1 OR !isset($postInvoiceNo[$orderNo])){
    		$mainframe->enqueueMessage(JText::sprintf('Error in submit values.'));return false;}
    		
    	//get prefix
    	if ( $params->get('allow_prefix_editing',0)==1){ //from post
    		$postInvoicePrefix = JRequest::getVar('invoice_prefix',null,'post','array');
    		$invoicePrefix = $postInvoicePrefix[$orderNo];
    	}
    	else //we dont edit prefixes - load "old" prefix from db, if not yet or empty, use default
    	{
    		$db->setQuery("SELECT id,invoice_prefix FROM #__vminvoice_mailsended WHERE order_id=".(int)$orderNo);
    		$oldInv = $db->loadObject();
    		if (!empty($oldInv) AND !empty($oldInv->invoice_prefix))
    			$invoicePrefix = $oldInv->invoice_prefix;
    		else
    			$invoicePrefix = $params->get('number_prefix');
    	}
    	
        if (!InvoiceGetter::checkOrderExists($orderNo)){
    		$mainframe->enqueueMessage(JText::sprintf('Order not found.'));return false;}
        
    	$invoiceNo = $postInvoiceNo[$orderNo] * 1;

    	//check if invoice no already exists
    	if (!empty($invoiceNo)) {
	        $db->setQuery("SELECT id FROM #__vminvoice_mailsended WHERE NOT (order_id=".(int)$orderNo.") AND invoice_no=".(int)$invoiceNo." AND invoice_prefix=".$db->Quote($invoicePrefix));
	    	$db->query();
	    	if ($db->getNumRows()>0){
	    		$mainframe->enqueueMessage(JText::_('COM_VMINVOICE_THIS_INVOICE_NUMBER_IS_ALREADY_IN_DB'), 'error');
	    		return false;}
    	}
    	 
    	//build query
    	$db->setQuery("SELECT id FROM #__vminvoice_mailsended WHERE order_id=".(int)$orderNo);
    	$db->query();
    	$numrows = $db->getNumRows();
    	if ($numrows==1){ //update
    		 if (empty($invoiceNo))
    		 	return false; //no number posted, don't change
    		$mailSendedId = $db->loadResult();
    	 	$db->setQuery("UPDATE #__vminvoice_mailsended SET invoice_no=".$invoiceNo.",invoice_prefix=".$db->Quote($invoicePrefix)." WHERE id=".(int)$mailSendedId);
    	}
    	elseif($numrows==0){ //create
    	    if (empty($invoiceNo)) //no number posted, create new
    			$invoiceNo = $this->getNewInvoiceNo($invoicePrefix);
    	    $db->setQuery("INSERT INTO `#__vminvoice_mailsended` (`order_id`,`invoice_no`,`invoice_prefix`) VALUES ('$orderNo', '$invoiceNo',".$db->Quote($invoicePrefix).")");
    	}
    	else
    		return false;
    		
    	//execute
        if ($db->query()){
    		$mainframe->enqueueMessage(JText::sprintf('Invoice number successfully changed')); return true;}
    	else {
    		$mainframe->enqueueMessage(JText::_('COM_VMINVOICE_INVOICE_NUMBER_UPDATE_ERROR'), 'error'); return false;}	
    }
    
    public function editInvoiceDate()
    {
    	$db = JFactory::getDBO();
    	$mainframe = JFactory::getApplication();
    	$params = InvoiceHelper::getParams();
       
    	//check posted values
    	$postSubmits = JRequest::getVar('update_inv_date',null,'post','array');
    	$postInvoiceDates = JRequest::getVar('invoice_date',null,'post','array');
    	
    	@$orderNo = key($postSubmits)*1;
    	if (count($postSubmits)!=1 OR !isset($postInvoiceDates[$orderNo])){
    		$mainframe->enqueueMessage(JText::sprintf('Error in submit values.'));return false;}
    		
    	if (!$newDate = InvoiceHelper::gmStrtotime($postInvoiceDates[$orderNo])) {
    		$mainframe->enqueueMessage(JText::_('COM_VMINVOICE_NOT_ENTERED_PROPER_DATE'), 'error'); return false;}	
    	
    	if (InvoiceHelper::getInvoiceNo($orderNo)!=false) //for all cases, to create row/check if we have right to update row
    		$db->setQuery('UPDATE #__vminvoice_mailsended SET invoice_date='.(int)$newDate.',invoice_lastchanged='.time().' WHERE order_id='.(int)$orderNo.' LIMIT 1');

    	//execute
        if ($db->query()){
    		$mainframe->enqueueMessage(JText::sprintf('Invoice date successfully changed')); return true;}
    	else {
    		$mainframe->enqueueMessage(JText::_('COM_VMINVOICE_INVOICE_DATE_CHANGE_ERROR'), 'error'); return false;}		
    }
    
    public function updateOrderNote()
    {
    	$db = JFactory::getDBO();
    	$mainframe = JFactory::getApplication();
    	$params = InvoiceHelper::getParams();

    	//check posted values
    	$postSubmits = JRequest::getVar('update_order_note',null,'post','array');
    	$postNotes = JRequest::getVar('order_note',null,'post','array');

    	@$orderNo = key($postSubmits)*1;
    	if (count($postSubmits)!=1 OR !isset($postNotes[$orderNo])){
    		$mainframe->enqueueMessage(JText::sprintf('Error in submit values.'));return false;
    	}
    	 
    	$orderParams = invoicegetter::getOrderParams($orderNo);
    	$orderParams->order_note = $postNotes[$orderNo];
    	if (InvoiceHelper::getInvoiceNo($orderNo)!=false)
    		$db->setQuery('UPDATE #__vminvoice_mailsended SET params='.$db->Quote(json_encode($orderParams)).',invoice_lastchanged='.time().' WHERE order_id='.(int)$orderNo.' LIMIT 1');
    	 
    	//execute
    	if ($db->query()){
    		$mainframe->enqueueMessage(JText::_('COM_VMINVOICE_ORDER_NOTE_SAVED')); return true;}
    	else {
    		$mainframe->enqueueMessage('Cannnot save order note. '.$db->getErrorMsg(), 'error'); return false;}
    }
    
    /**
     * Get new invoice number with given prefix
     * 
     * @param text $invoicePrefix. if not passed, default prefix from config is checked.
     */
    public function getNewInvoiceNo($invoicePrefix=false)
    {
    	if (!$invoicePrefix){
    		$params = InvoiceHelper::getParams();
    		$invoicePrefix = $params->get('number_prefix');}
    	
    	$params = InvoiceHelper::getParams();
		$startNo = $params->get('start_number');
        
         // find last number
         $db = JFactory::getDBO();
         $db->setQuery('SELECT MAX(`invoice_no`) FROM `#__vminvoice_mailsended` WHERE `invoice_prefix`='.$db->Quote($invoicePrefix));
         $no = $db->loadResult();
         // set next number
         return ($no < $startNo) ? $startNo : ++$no;
    }
    
    
}
?>