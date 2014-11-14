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

defined('_JEXEC') or ('Restrict Access');

invoiceHelper::legacyObjects('view');

class VMInvoiceViewInvoices extends JViewLegacy
{

    function display ($tpl = null)
    {
    	InvoiceHelper::setSubmenu(5);
    	
        JToolBarHelper::title('VM Invoice: ' . JText::_('COM_VMINVOICE_INVOICE_ORDER_MANAGEMENT'), 'invoices');
        JToolBarHelper::cancel('cancel', 'COM_VMINVOICE_CLOSE');
        
        JToolBarHelper::addNew('addOrder');
        
        
        $params = InvoiceHelper::getParams();
        $this->delivery_note = $params->get('delivery_note');
        $this->invoice_numbering = $params->get('invoice_number');
        $this->prefix_editing = $params->get('allow_prefix_editing',0);
        $this->default_prefix = $params->get('number_prefix','');
        $this->pagination = $this->get('Pagination');
        $this->invoices = $this->get('Data');
        $this->statuses = InvoiceGetter::getOrderStates();   
        $this->sort_options = $this->get('SortOptions');
        $this->sort_dir_opts = $this->get('SortDirOptions');
        
        
        if (COM_VMINVOICE_ISVM2){
            $this->shippings = InvoiceGetter::getShippingsVM2();
        }
        
        
        $db =  JFactory::getDBO();
        	
        //set additioanl invoice variables
        foreach ($this->invoices as &$invoice)
        {
        	
        	if (COM_VMINVOICE_ISVM2){
        		$invoice->cdate = InvoiceHelper::gmStrtotime($invoice->created_on);
        		$invoice->mdate = InvoiceHelper::gmStrtotime($invoice->modified_on);
        	}

        	// Parse shipment name
            if (COM_VMINVOICE_ISVM1) {
                $parts = explode('|', $invoice->ship_method_id);
                $invoice->shipment_name = (isset($parts[1]) ? $parts[1] : '');
            }
			
        	
        	        	
        	//get invoice numbers.
        	$invoice->invoiceNoFull = InvoiceHelper::getInvoiceNo($invoice->order_id); //full value 
        	
        	//load nos and prefixes after getInvoiceNo, because could be created new invoice numbers
        	$db->setQuery("SELECT `invoice_no`, `invoice_prefix`, `invoice_date` FROM `#__vminvoice_mailsended` WHERE `order_id` = ".(int)$invoice->order_id);
        	$invoiceNos = $db->loadObject();

        	$invoice->invoiceDate = !empty($invoiceNos) ? $invoiceNos->invoice_date : false;
        	$invoice->invoiceNoPrefix = !empty($invoiceNos) ? (is_null($invoiceNos->invoice_prefix) ? $this->default_prefix : $invoiceNos->invoice_prefix) : false; //prefix
        	$invoice->invoiceNoDb = !empty($invoiceNos) ? $invoiceNos->invoice_no : false; //value without prefix
        	
        	$invoice->generated = InvoiceHelper::canUseActualPDF($invoice->order_id,false);
        	if ($this->delivery_note==1)
        		$invoice->generatedDN = InvoiceHelper::canUseActualPDF($invoice->order_id,true);
        }

        $this->newInoviceNo = $this->get('NewInvoiceNo');
        
        JPluginHelper::importPlugin('vminvoice');
        $this->dispatcher = JDispatcher::getInstance();
        
        parent::display($tpl);
    }

}
?>