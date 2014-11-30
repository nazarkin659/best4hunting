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

function VMInvoiceBuildRoute(&$query)
{
	$segments = array();

	if (isset($query['controller'])) {
	    $segments[] = $query['controller'];
	    unset($query['controller']);
	    
		if (isset($query['task'])) {
    	    $segments[] = $query['task'];
    	    unset($query['task']);
    	}
    	if (isset($query['cid'])) {
    	    $segments[] = $query['cid'];
    	    unset($query['cid']);
    	}	    
	}
	elseif (isset($query['view'])) {
	    $segments[] = $query['view'];
    	unset($query['view']);
    	
    	if (isset($query['task'])) {
    	    $segments[] = $query['task'];
    	    unset($query['task']);
    	}	  
	}


	return $segments;
}

function VMInvoiceParseRoute($segments)
{
	$vars = array();

	$count = count($segments);
	
	if ($count == 3) {
		$vars['controller'] = $segments[0];
		$vars['task'] = $segments[1];
		$vars['cid'] = $segments[2];
	}
	elseif ($count == 2) {
	    $vars['view'] = $segments[0];
		$vars['task'] = $segments[1];
	}

	return $vars;
}
?>
