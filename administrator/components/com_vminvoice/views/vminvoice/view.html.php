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

invoiceHelper::legacyObjects('view');

class VMInvoiceViewVMInvoice extends JViewLegacy
{

    function display ($tpl = null)
    {
    	InvoiceHelper::setSubmenu(0);
    	
        $params = InvoiceHelper::getParams();
                
        if ($params->get('version_checker')) {
        	invoiceHelper::legacyObjects('model');
            $model2 =  JModelLegacy::getInstance('Upgrade', 'VMInvoiceModel');
            $newVer = $model2->getNewVMIVersion();
            $vmiinfo = invoiceHelper::getComponentInfo();
            
            if (((strnatcasecmp($newVer, $vmiinfo['version']) > 0) ||
             (strnatcasecmp($newVer, substr($vmiinfo['version'], 0, strpos($vmiinfo['version'], '-'))) == 0))) {
                $newVer = '<span style="font-weight: bold; color: red;">' . $newVer .
                 '</span>&nbsp;&nbsp;<input type="button" class="btn" onclick="showUpgrade();" value="' . JText::_('COM_VMINVOICE_GO_TO_UPGRADE_PAGE') . '" />';
            }
            //$newVer .= ' <input type="button" class="btn" onclick="disableStatus(\'versioncheck\');" value="' . JText::_('COM_VMINVOICE_DISABLE_VERSION_CHECKER') . '" />';
            
            $this->assign('newestVersion', $newVer);
        } else {
            $newestVersion = JText::_('COM_VMINVOICE_VERSION_CHECKER_DISABLED'); // . '&nbsp;&nbsp;<input type="button" onclick="enableStatus(\'versioncheck\');" value="' . JText::_('COM_VMINVOICE_ENABLE') . '" />';
            $this->assign('newestVersion', $newestVersion);
        }
        
        JToolBarHelper::title('ARTIO VM Invoice', 'vminvoice');
                parent::display($tpl);
    }

}
?>
