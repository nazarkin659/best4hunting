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

class VMInvoiceViewUpgrade extends JViewLegacy
{
    function display ($tpl = null)
    {
    	InvoiceHelper::setSubmenu(7);
    	
        $params = InvoiceHelper::getParams();
        
        $downloadId = $params->get('download_id');
        $this->assignRef('downloadId', $downloadId);
        
        JToolBarHelper::title('VM Invoice: ' . JText::_('COM_VMINVOICE_UPGRADE'), 'update.png');
        
        JToolBarHelper::back('Back', 'index.php?option=com_vminvoice');
        
        $oldVer = invoiceHelper::getVMIVersion();
        $this->assignRef('oldVer', $oldVer);
        
        $newVer = $this->getNewestVersion();
        $this->assignRef('newVer', $newVer);
        
        $regInfo = VMInvoiceModelUpgrade::getRegisteredInfo($downloadId);
        $this->assignRef('regInfo', $regInfo);
        
        $isPaidVersion = $this->get('IsPaidVersion');
        $this->assignRef('isPaidVersion', $isPaidVersion);
        
        JHTML::_('behavior.tooltip');
        JHTML::_('behavior.modal');
        
        parent::display($tpl);
    }

    function showMessage ()
    {
        JToolBarHelper::title('VM Invoice ' . JText::_('COM_VMINVOICE_UPGRADE'), 'update.png');
        
        $url = 'index.php?option=com_vminvoice&task=showupgrade';
        $redir = JRequest::getVar('redirto', null, 'post');
        if (! is_null($redir)) {
            $url = 'index.php?option=com_vminvoice&' . $redir;
        }
        JToolBarHelper::back('Back', $url);
        
        $this->assign('url', $url);
        
        $this->setLayout('message');
        parent::display();
    }

    function getNewestVersion ()
    {
        $db =  JFactory::getDBO();

        $configs = InvoiceHelper::getParams();
        
        $newVer = '';
        if ($configs->get('version_checker')) {
        	invoiceHelper::legacyObjects('model');
            $model2 =  JModelLegacy::getInstance('Upgrade', 'VMInvoiceModel');
            $newVer = $model2->getNewVMIVersion();
            $vmiinfo = invoiceHelper::getComponentInfo();
            
            if (((strnatcasecmp($newVer, $vmiinfo['version']) > 0) ||
             (strnatcasecmp($newVer, substr($vmiinfo['version'], 0, strpos($vmiinfo['version'], '-'))) == 0))) {
                $newVer = '<span style="font-weight: bold; color: red;">' . $newVer . '</span>';
            }
            
            $this->assign('newestVersion', $newVer);
        } else {
            $newVer = JText::_('COM_VMINVOICE_VERSION_CHECKER_DISABLED');
        }
        return $newVer;
    }

}
?>