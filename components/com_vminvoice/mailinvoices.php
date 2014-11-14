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

// load classes
require_once (JPATH_ADMINISTRATOR . '/components/com_vminvoice/helpers/invoicehelper.php');

class MailInvoices
{

    function cronMail ()
    {
        $params = InvoiceHelper::getParams();
        $sendBoth =  InvoiceHelper::getSendBoth();
         
        if ($params->get('auto_manual') == '1') {
            $orderIds = InvoiceGetter::getUnsentOrderIDs();

            if (count($orderIds) > 0) {
                foreach ($orderIds as $orderId) {
                	
                    // Generate PDF only if invoice set to send as PDF attachment
                    if (($params->get('invoice_attach_type', '0') == '0') && !InvoiceHelper::generatePDF($orderId, true, false))
                   		continue; //can't get generate pdf (e.g. not have in. number)
                    
                    // Generate PDF only if delivery note set to send as PDF attachment
                    if ($sendBoth && ($params->get('dn_attach_type', '0') == '0') && !InvoiceHelper::generatePDF($orderId, true, true))
                    	continue;
                    
                    InvoiceHelper::sendMail($orderId, true, $sendBoth);
                }
            }
        }
    }
    
    /**
     * Generate ungenerated pdf's
     */
    function cronGenerate($orderId=null)
    {
    	$params = InvoiceHelper::getParams();

    	if (!is_null($orderId)) //generate just for one order
    	{
	    	if (InvoiceHelper::generatePDF($orderId, true, false))
	    		echo "Generated invoice for order Id ".$orderId."<br>\n";
	    	if ($params->get('delivery_note')==1){
	    		if (InvoiceHelper::generatePDF($orderId, true, true))
	    			echo "Generated delivery note for order Id ".$orderId."<br>\n";}
    	}
    	else //generate for all new orders from last week
    	{
	    	$orderIds = InvoiceGetter::getOrdersFromTime(time()-604800);
	    	
	    	if (count($orderIds)>0)
		    	foreach ($orderIds as $orderId)
		    	{
		    		if (!InvoiceHelper::getInvoiceNo($orderId)) //check if we have invoice id
		    			continue;
		    		
		    		echo "Order ".$orderId.": ";
		    		
		    		ob_start();
		    		if (InvoiceHelper::canUseActualPDF($orderId,false)==false)
		    		{
		    			echo "not actual invoice";
		    			ob_start();
		    			if (InvoiceHelper::generatePDF($orderId, true, false)===true)
		    				echo "... <b>generated</b>";
		    			else
		    				echo "... <b>NOT generated</b>";
		    		}
		    		else
		    			echo 'actual invoice'; 
		    		
		    		
		    		if ($params->get('delivery_note')==1 AND InvoiceHelper::canUseActualPDF($orderId,true)==false)
		    		{
		    			echo ', not actual delivery note';
		    			ob_start();
		    			if (InvoiceHelper::generatePDF($orderId, true, true)===true)
		    				echo "... <b>generated</b>.";
		    			else
		    				echo "... <b>NOT generated</b>.";
		    		}
		    		elseif ($params->get('delivery_note')==1)
		    			echo ', actual delivery note. ';
		    		
		    		echo "<br>\n";
		    			
		    		ob_flush();
		    		flush();
		    	}
		    else
		    	echo "No new orders during last 7 days.";
    	}
    }
    
}
?>