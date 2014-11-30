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
class JFormFieldformdepartments extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since	1.6
	 */
	protected $type = 'formdepartments';

	/**
	 * Method to get the field input markup.
	 *
	 * @return	string	The field input markup.
	 * @since	1.6 
	 */ 
	protected function getInput(){

		jimport('joomla.filesystem.file');

		// Initialize variables.
		$html = array();
		$attr = '';

		// Initialize some field attributes.
		$attr .= $this->element['class'] ? ' class="'.(string) $this->element['class'].'"' : '';
		$attr .= ((string) $this->element['disabled'] == 'true') ? ' disabled="disabled"' : '';
		$attr .= $this->element['size'] ? ' size="'.(int) $this->element['size'].'"' : '';

		// Initialize JavaScript field attributes.
		$attr .= $this->element['onchange'] ? ' onchange="'.(string) $this->element['onchange'].'"' : '';

		// Get some field values from the form.
		$departments_selected = explode(",",$this->form->getValue('departments'));

		$db    				=& JFactory::getDBO();
		$app 				= JFactory::getApplication();

		$filter_order		= $app->getUserStateFromRequest( 'filter_order', 'filter_order', 'ordering', '' );
		$filter_order_Dir	= $app->getUserStateFromRequest( 'filter_order_Dir', 'filter_order_Dir', '', '' );
		
		// build query 's conditions
		$where 	= ' WHERE `published` = \'1\' ';		
		
		// build query 's order
		$orderby 	= ' ORDER BY '. $filter_order .' '. $filter_order_Dir;
		
		// Get the total records
		$query = 'SELECT *' 
				.' FROM #__yj_contactus_departments'
				."\n {$where}"
				."\n {$orderby}";
		$db->setQuery($query);
		$items = $db->loadObjectList();	

		$putG = array();
		if(!empty($items)){
			foreach($items as $departments){
				$putG[] = JHTML::_( 'select.option',  JText::_($departments->id), JText::_($departments->name));
			}
			$html[] = JHTML::_('select.genericlist', $putG, 'jform[departments][]', array('class'=>'inputbox', 'size'=>'5', 'multiple'=>'multiple'), 'value', 'text', !empty($departments_selected) ? $departments_selected : array(), 'jform_departments' );
		}else{
			$html[] = "<input type='text' value='".JText::_( 'COM_YJCONTACTUS_CHECK_DEPARTMENTS' )."' style='border:none; width:70%; color:red; font-size:1.091em;' disabled='disabled' />";
		}

		return implode($html);
	}
}