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
defined('_JEXEC') or die('Restrict Access');

InvoiceHelper::legacyObjects('model');

class VMInvoiceModelVMInvoice extends JModelLegacy
{

    function setId ($id)
    {
        // Set id and wipe data
        $this->_id = $id;
        $this->_data = null;
    }

    function _buildQuery ()
    {
        $query = " SELECT * FROM #__vm_orders";
        return $query;
    }

    function getData ()
    {
        if (empty($this->_data)) {
            $query = $this->_buildQuery();
            $this->_db->setQuery($query);
            $this->_data = $this->_getList($query);
        }
        
        return $this->_data;
    }

}
?>
