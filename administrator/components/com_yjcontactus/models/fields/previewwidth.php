<?php
/**
 * @version		$Id$
 * @package		Joomla.Framework
 * @subpackage	Form
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

jimport('joomla.html.html');
jimport('joomla.form.formfield');

/**
 * Supports an HTML select list of banners
 *
 * @package		Joomla.Administrator
 * @subpackage	com_banners
 * @since		1.6
 */
class JFormFieldpreviewwidth extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since	1.6
	 */
	protected $type = 'previewwidth';

	/**
	 * Method to get the field input markup.
	 *
	 * @return	string	The field input markup.
	 * @since	1.6
	 */
	protected function getInput(){
 
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		$query->select('params');
		$query->from('#__extensions');
		$query->where('`element` = "com_miaflv"');
		$query->where('`type` = "component"');		
		$db->setQuery($query);
		$global_params = $db->loadObjectList();
		
		$registry = new JRegistry;
		$registry->loadJSON($global_params[0]->params);

		$param_preview_width	= $registry->get( 'preview_width', 100 );

		$class		= ' class="validate-numeric text_area"';
		$onchange	= "";//' onchange="document.id(\''.$this->id.'_unlimited\').checked=document.id(\''.$this->id.'\').value==\'\';"';
		$onclick	= "";//' onclick="if (document.id(\''.$this->id.'_unlimited\').checked) document.id(\''.$this->id.'\').value=\'\';"';
		$value		= empty($this->value) ? $param_preview_width : $this->value;

		return '<input type="text" name="'.$this->name.'" id="'.$this->id.'" value="'.htmlspecialchars($value, ENT_COMPAT, 'UTF-8').'" '.$class.$onchange.' />';
	}
}