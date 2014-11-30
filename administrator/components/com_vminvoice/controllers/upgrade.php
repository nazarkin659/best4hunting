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

defined('_JEXEC') or die('Direct Access');

invoiceHelper::legacyObjects('controller');

class VMInvoiceControllerUpgrade extends JControllerLegacy
{

    function __construct ($config = array())
    {
        parent::__construct($config);
    }

    function display ($cachable = false, $urlparams = false)
    {
        JRequest::setVar('view', 'upgrade');
        parent::display($cachable, $urlparams);
    }

    function save () //? 
    {
        $model = $this->getModel('upgrade');
        if ($model->store($post)) {
            $msg = JText::_('COM_VMINVOICE_MSG_CONFIG_SAVED');
        } else {
            $msg = JText::_('COM_VMINVOICE_MSG_CONFIG_SAVE_ERROR');
        }
        
        $model->update_tmpl();
        $link = 'index.php?option=com_vminvoice&controller=upgrade';
        $this->setRedirect($link, $msg);
    }

    function cancel ()
    {        
        $link = 'index.php?option=com_vminvoice';        
        $this->setRedirect($link);   
    }
    
    function doUpgrade()
    {
        JRequest::checkToken() or jexit( 'Invalid Token' );
        
        $model =  $this->getModel('upgrade');
        $result = $model->upgrade();
        $model->setState('result', $result);
        
        $view =  $this->getView('upgrade', 'html');
        $view->setModel($model, true);
        $view->showMessage();
    }

}
