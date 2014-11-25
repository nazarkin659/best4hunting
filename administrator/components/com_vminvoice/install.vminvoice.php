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

//we need to get these addresses from 1.5 installer
global $vminvoiceExtPathAdmin, $vminvoiceExtPathSource;
$vminvoiceExtPathAdmin = $this->parent->getPath('extension_administrator');
$vminvoiceExtPathSource = $this->parent->getPath('source');

function com_install()
{
	global $vminvoiceExtPathAdmin, $vminvoiceExtPathSource;
	
	include_once (JPATH_SITE . '/administrator/components/com_vminvoice/install.php');

	$installer = new com_vminvoiceInstallerScript();
	$installer->pathAdmin = $vminvoiceExtPathAdmin;
	$installer->pathSource = $vminvoiceExtPathSource;
	$installer->install();

    return true;
}

?>