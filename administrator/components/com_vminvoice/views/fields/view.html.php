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

invoiceHelper::legacyObjects('view');

class VMInvoiceViewFields extends JViewLegacy
{

    function display ($tpl = null)
    {        
    	InvoiceHelper::setSubmenu(4);
    	
        JToolBarHelper::title('VM Invoice: ' . JText::_('COM_VMINVOICE_FIELDS'), 'fields');        
        //JToolBarHelper::back(JText::_('COM_VMINVOICE_BACK'));
        JToolBarHelper::cancel('cancel', JText::_('COM_VMINVOICE_CLOSE'));
        JToolBarHelper::save('save', JText::_('COM_VMINVOICE_SAVE'));
        
        $fields =  $this->get('Data');
        $this->assignRef('fields', $fields);
        
        parent::display($tpl);
    }

}
?>