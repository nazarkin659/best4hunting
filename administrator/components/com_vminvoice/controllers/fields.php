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
defined('_JEXEC') or die('Direct Access');

invoiceHelper::legacyObjects('controller');

class VMInvoiceControllerFields extends JControllerLegacy
{

    function __construct ($config = array())   
    {
        parent::__construct($config);        
        $this->registerTask('add', 'edit');
    }

    function display ($cachable = false, $urlparams = false)
    {        
        JRequest::setVar('view', 'fields');
        parent::display($cachable, $urlparams);
    }

    function save ()
    {        
        $model = $this->getModel('fields');
        
        if ($model->store()) {            
            $msg = JText::_('COM_VMINVOICE_MSG_FIELDS_SAVED');        
        } else {            
            $msg = JText::_('COM_VMINVOICE_MSG_FIELDS_SAVE_ERROR');        
        }
        
        $link = 'index.php?option=com_vminvoice&controller=fields';        
        $this->setRedirect($link, $msg);    
    }

    function cancel ()   
    {        
        $link = 'index.php?option=com_vminvoice';
        $this->setRedirect($link);    
    }

}
