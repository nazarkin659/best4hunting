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
	
	class JElementImage extends JElement
	{
	    var $_name = 'Image';
	
	    function fetchElement ($name, $value, &$node, $control_name)
	    {
	
	    	//NOTE: it is necessary to add this scripts to template:
	    	/*
				//function override to write img src from com_media to text field
				function jInsertEditorText(img,editor)
				{
					pattern =/src=\"([^\"]+)\"/i;
					matches = img.match(pattern);
						
					$(editor).value = matches[1];
				}
	    	*/
	    	$code = '<input style="float:left" type="text" name="'.$control_name .'['.$name .']'.'" value="'.$value.'" id="'.$control_name.$name.'" size="50">';
	        
	        $link = 'index.php?option=com_media&amp;view=images&amp;layout=default&amp;tmpl=component&amp;e_name='.$control_name.$name;
	
			JHTML::_('behavior.modal');
	
			$code.='
				<div class="button2-left">
				<div class="image">
				<a class="modal-button" title="Image" href="'.$link.'" onclick="IeCursorFix(); return false;" 
					rel="{handler: \'iframe\', size: {x: 570, y: 400}}">'.JText::_('COM_VMINVOICE_SELECT').'</a>
				</div>
				</div>';
	
	        return $code;
	    }
	}

}
elseif (COM_VMINVOICE_ISJ16) {
	//1.6 and more
	
	jimport('joomla.form.helper');
	if (class_exists('JFormHelper') AND is_callable('JFormHelper::loadFieldClass'))
		JFormHelper::loadFieldClass('media'); //since 2.5? 
	
	if (!class_exists('JFormFieldMedia')){ //1.6 - 2.5 probably 
		include_once dirname(__FILE__).'/j25/media.php'; //hope it will work
	}
	
	if (class_exists('JFormFieldMedia')){ //only rename media
	
		class JFormFieldImage extends JFormFieldMedia { 
	
			public $type = 'Image';
		}
	}
}
?>