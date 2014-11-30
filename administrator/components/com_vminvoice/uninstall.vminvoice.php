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

defined('_JEXEC') or die('Restricted access');

function com_uninstall()
{
	include_once (JPATH_SITE . '/administrator/components/com_vminvoice/install.php');
	
	$installer = new com_vminvoiceInstallerScript();
	$installer->uninstall();
	
	return true;
}

?>