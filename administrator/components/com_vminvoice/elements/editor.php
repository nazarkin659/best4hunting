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

//J1.5
if (!COM_VMINVOICE_ISJ16 AND class_exists('JElement')){
	
	class JElementEditor extends JElement
	{
	    
	    var $_name = 'Editor';
	
	    function fetchElement ($name, $value, &$node, $control_name)
	    {
	        $editor = JFactory::getEditor();
	        /* @var $editor JEditor */
	
	        $cols = $node->attributes( 'cols' )>0 ? $node->attributes( 'cols' ) : 1;
	        $rows = $node->attributes( 'rows' )>0 ? $node->attributes( 'rows' ) : 1;
	        $width = $node->attributes( 'width' )>0 ? $node->attributes( 'width' ) : 800;
	        $height = $node->attributes( 'height' )>0 ? $node->attributes( 'height' ) : 500;
	        
	        $code = $editor->display($control_name . '[' . $name . ']', $value, $width, $height, $cols, $rows);
	        return $code;
	    }
	}
}
elseif (COM_VMINVOICE_ISJ16){
	//sice 2.5, there is "editor" form field. but what about 1.6? load from out subfolder!
	
	if (!class_exists('JFormFieldEditor')){ //hope it will work.
		include_once dirname(__FILE__).'/j25/editor.php';
	}
}

?>