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

invoiceHelper::legacyObjects('view');

class VMInvoiceViewInfo extends JViewLegacy
{

	function display($tpl = null)
	{
		$task = JRequest::getVar('task');
	
		switch ($task) {
		    case 'help':
		    default:
		        $title = 'VM Invoice '.JText::_('COM_VMINVOICE_SUPPORT');
		        $icon = 'help.png';
		}

		InvoiceHelper::setSubmenu(8);
		JToolBarHelper::title($title, $icon);		
		JToolBarHelper::back(JText::_('COM_VMINVOICE_BACK'), 'index.php?option=com_vminvoice');

		parent::display($tpl);
	}

}
