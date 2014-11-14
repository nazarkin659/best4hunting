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
 
// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();
 
/**
 * Renders a multiple item select element
 * http://docs.joomla.org/Adding_a_multiple_item_select_list_parameter_type
 * 
 * Updated to select values based on sql
 */

if (!COM_VMINVOICE_ISJ16 AND class_exists('JElement')){
	
	class JElementMultilistsql extends JElement
	{
		/**
		* Element name
		*
		* @access	protected
		* @var		string
		*/
		var	$_name = 'multilistsql';
	 
		function fetchElement($name, $value, &$node, $control_name)
		{
			// Base name of the HTML control.
			$ctrl	= $control_name .'['. $name .']';
	 
			// Construct an array of the HTML OPTION statements.
			$options = array ();
			foreach ($node->children() as $option)
			{
				$val	= $option->attributes('value');
				$text	= $option->data();
				$options[] = JHTML::_('select.option', $val, JText::_($text));
			}
	 
			// Construct the various argument calls that are supported.
			$attribs	= ' ';
			if ($v = $node->attributes( 'size' )) {
				$attribs	.= 'size="'.$v.'"';
			}
			if ($v = $node->attributes( 'class' )) {
				$attribs	.= 'class="'.$v.'"';
			} else {
				$attribs	.= 'class="inputbox"';
			}
			if ($m = $node->attributes( 'multiple' ))
			{
				$attribs	.= ' multiple="multiple"';
				$ctrl		.= '[]';
			}
			
			//get options from sql
			$db	 =  JFactory::getDBO();
			$db->setQuery($node->attributes('query'));
			$key = ($node->attributes('key_field') ? $node->attributes('key_field') : 'value');
			$val = ($node->attributes('value_field') ? $node->attributes('value_field') : $name);
			
			$result = $db->loadObjectList();
			if ($node->attributes('translate')!="false") //translate
				foreach ($result as &$row ) 
					$row->$val = JText::_($row->$val);
		
			if (!is_array($value))
				$value = explode(';',$value);
			
			// Render the HTML SELECT list.
			return JHTML::_('select.genericlist', $result, $ctrl, $attribs, $key, $val, $value, $control_name.$name );
		}
	}

}
elseif (COM_VMINVOICE_ISJ16){
	//for J1.6
	
	jimport('joomla.form.helper');
	if (class_exists('JFormHelper') AND is_callable('JFormHelper::loadFieldClass'))
		JFormHelper::loadFieldClass('sql');
	
	
	if (class_exists('JFormFieldSQL')){
		
		class JFormFieldMultilistsql extends JFormFieldSQL { //it can do everyting we want
			
			public $type = 'Multilistsql';
		}
	}
}