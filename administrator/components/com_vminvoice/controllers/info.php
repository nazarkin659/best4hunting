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
defined('_JEXEC') or die();

invoiceHelper::legacyObjects('controller');

class VMInvoiceControllerInfo extends JControllerLegacy
{
    function help()
    {
        JRequest::setVar('view', 'info');
        JRequest::setVar('layout' , 'help');

        parent::display();
    }

}
?>
