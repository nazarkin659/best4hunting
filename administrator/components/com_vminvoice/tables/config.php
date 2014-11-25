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

class TableConfig extends JTable
{
    
    var $id = null;    
    var $params = null;
    var $template_header = null;
    var $template_body = null;
    var $template_items = null;
    var $template_footer = null;
    
    var $template_tax_header = null;
    var $template_tax_row = null;
    
    var $template_dn_header = null;
    var $template_dn_body = null;
    var $template_dn_items = null;
    var $template_dn_footer = null;
    
    var $template_restore = null;
    
    var $template_dn_restore = null;
	
    
    function TableConfig (& $db)    
    {        
        parent::__construct('#__vminvoice_config', 'id', $db);    
    }

}
?>