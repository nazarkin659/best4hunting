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

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restrict Access');

// Load class
require_once (JPATH_ADMINISTRATOR . '/components/com_vminvoice/helpers/invoicehelper.php');
require_once (JPATH_SITE . '/components/com_vminvoice/mailinvoices.php');

InvoiceHelper::legacyObjects('controller');

class VMInvoiceController extends JControllerLegacy
{

    function __construct ()
    {
        parent::__construct();
        $this->registerTask('cronMail', 'cronMail');
        $this->registerTask('cronGenerate', 'cronGenerate');
        
       
    }

    function cronMail ()
    {
    	InvoiceHelper::createInvoiceNos(); //make sure all invoice numbers are created
    	 
        $mi = new MailInvoices();
        $mi->cronMail();
        exit;
    }
    
    
    
    function cronGenerate()
    {
    	InvoiceHelper::createInvoiceNos(); //make sure all invoice numbers are created
    	 
    	$mi = new MailInvoices();
    	$mi->cronGenerate();
    	exit;
    }
    
    

}
?>