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
defined('_JEXEC') or die('Restricted access');
?>

<div id="cpanel">
	 <p><strong><?php echo JText::_('COM_VMINVOICE_INFO_SUPPORT_CHANNELS'); ?></strong></p>
	 
	 <!-- Frequently Asked Questions -->
	 <div class="icon">
	 	<a href="http://www.artio.net/faqs/vm-invoice" target="_blank" title="<?php echo JText::_('COM_VMINVOICE_CHECK_VM_INVOICE_FREQUENTLY_ASKED_QUESTIONS_AND_KNOWLEDGE_BASE'); ?>">
	 		<img src="<?php echo InvoiceHelper::imgSrc('icon-48-help.png')?>" alt="" width="48" height="48" border="0" />
	 		<span><?php echo JText::_('COM_VMINVOICE_VM_INVOICE_FAQ_AND_KNOWLEDGE_BASE'); ?></span>
	 	</a>
	 </div>
	 <!-- ARTIO VM Invoice Support Forums -->
	 <div class="icon">
	 	<a href="http://www.artio.net/support-forums/vm-invoice" target="_blank" title="<?php echo JText::_('COM_VMINVOICE_VISIT_VM_INVOICE_SUPPORT_FORUMS'); ?>">
	 		<img src="<?php echo InvoiceHelper::imgSrc('icon-48-forum.png')?>" alt="" width="48" height="48" border="0" />
	 		<span><?php echo JText::_('COM_VMINVOICE_ARTIO_SUPPORT_FORUMS'); ?></span>
	 	</a>
	 </div>
	 <!-- ARTIO Paid Support -->
	 <div class="icon">
	 	<a href="http://www.artio.net/en/e-shop/support-services" target="_blank" title="<?php echo JText::_('COM_VMINVOICE_GET_PAID_SUPPORT_FROM_ARTIO'); ?>">
	 		<img src="<?php echo InvoiceHelper::imgSrc('icon-48-support.png')?>" alt="" width="48" height="48" border="0" />
	 		<span><?php echo JText::_('COM_VMINVOICE_ARTIO_PAID_SUPPORT'); ?></span>
	 	</a>
	 </div>
</div>
