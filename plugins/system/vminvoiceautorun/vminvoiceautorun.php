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

// no direct access
defined('_JEXEC') or die('Restricted access');

/**
 * VMInvoice autorun plugin
 */
class plgSystemVminvoiceAutorun extends JPlugin
{
    
    var $filename;
    var $nextrun;

    function plgSystemVminvoiceAutorun (&$subject)
    {
        parent::__construct($subject);
        
        // get filename
        $mainframe = JFactory::getApplication();
        $dirname = JPath::clean(trim($mainframe->getCfg('tmp_path') ? $mainframe->getCfg('tmp_path') : $mainframe->getCfg('config.tmp_path')));
        if (!is_writeable($dirname) && function_exists('sys_get_temp_dir'))
            $dirname = sys_get_temp_dir();
        $this->filename = $dirname . '/'. 'vminvoiceautorun.log';
        
        // check if file exists
        $this->nextrun = file_exists($this->filename) ? file_get_contents($this->filename) : false;
    }

    /**
     * After document was rendered.
     */
    function onAfterRender ()
    {
        if (! $this->nextrun || time() >= $this->nextrun) {
            $this->setNextRunTime();
            $this->runNow();
        }
    }

    /**
     * 
     * Initiate emails sent now.
     */
    function runNow ()
    {
        // load needed sources
        require_once (JPATH_SITE . '/components/com_vminvoice/mailinvoices.php');
        
        $mi = new MailInvoices();
        $mi->cronMail();
    }

    /**
     * 
     * Set next run time by writing it into file.
     */
    function setNextRunTime ()
    {
        require_once (JPATH_ADMINISTRATOR . '/components/com_vminvoice/helpers/invoicehelper.php');
        
        $params = InvoiceHelper::getParams();
        
        // load sending period settings
        $hr = $params->get('pre_def_time_h');
        $min = $params->get('pre_def_time');
        
        $lastrun = (! $this->nextrun) ? time() : $this->nextrun;
        $nextrun = $lastrun + (60 * $min + (3600 * $hr));
        
        // write next runtime to file
        file_put_contents($this->filename, $nextrun);
    }

}
?>