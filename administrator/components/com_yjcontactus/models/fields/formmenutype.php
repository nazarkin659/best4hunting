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
class JFormFieldformmenutype extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since	1.6
	 */
	protected $type = 'formmenutype';

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
		$item_id 	= $this->form->getValue('item_id');
		$menutype	= $this->form->getValue('menutype');
		$menu_name	= $this->form->getValue('menu_name');
		
		$db    =& JFactory::getDBO(); 
		$cid = JRequest::getVar( 'cid', array(), '', 'array' );
		$extra_from = '';
		
		// build query 's conditions
		if(empty($cid[0])){
			// Get all item_id
			$query = 'SELECT item_id' 
					.' FROM #__yj_contactus_forms'
					.' WHERE item_id > 0';
			$db->setQuery($query);
			$items_id = $db->loadResultArray();	

			$where = ' WHERE m.link LIKE "%com_yjcontactus%" AND m.type="component" ';
			if(!empty($items_id)){	
				$where .= ' AND id NOT IN ('.implode(",",$items_id).')';	
				$extra_from = ', #__yj_contactus_forms as f';
			}
		}else{
			$extra_from = ', #__yj_contactus_forms as f';		
			$where = ' WHERE m.link LIKE "%com_yjcontactus%" AND m.type="component" AND f.id != "'.$cid[0].'" AND m.id != f.item_id ';	
		}
		
		// build query 's conditions
		$order 	= ' GROUP BY m.id ORDER BY m.menutype, m.parent, m.ordering ';
		
		// Get the total records
		$query = 'SELECT m.*' 
				.' FROM #__menu_types as m';
				//."\n {$extra_from}"
				//."\n {$where}"				
				//.' WHERE m.link LIKE "%com_yjcontactus%" AND m.type="component" AND published = 1 '
				//."\n {$order}";
		$db->setQuery($query);
		$items = $db->loadObjectList();	

		$putG = array();
		if(isset($menu_name) && !empty($menu_name)){
			//echo $this->menu_name[0]->name;
			$putG[] = JHTML::_( 'select.option',  "", JText::_('COM_YJCONTACTUS_SELECT_MENU_TYPE'));
			foreach($items as $menu){
				$putG[] = JHTML::_( 'select.option',  JText::_($menu->menutype), JText::_($menu->title), 'value', 'text', false);
			}
			$html[] = JHTML::_('select.genericlist', $putG, 'jform[menutype]', array('class'=>'inputbox required', 'size'=>'1'),'value','text', $menutype ? $menutype : "", 'jform_menutype');
			//$html[] = "&nbsp;&nbsp;<strong>".JText::_( 'COM_YJCONTACTUS_MENU_NAME' )."</strong>&nbsp;:&nbsp;&nbsp;<input type=\"text\" size=\"96\" value=\"".$this->menu_name[0]->name."\" name=\"jform[menu_name]\" id=\"menu_name\" maxlength=\"500\" />";
		}elseif(!empty($items)){
			$putG[] = JHTML::_( 'select.option',  "", JText::_('COM_YJCONTACTUS_SELECT_MENU_TYPE'));
			foreach($items as $menu){
				$putG[] = JHTML::_( 'select.option',  JText::_($menu->menutype), JText::_($menu->title), 'value', 'text', false);
			}
			$html[] = JHTML::_('select.genericlist', $putG, 'jform[menutype]', array('class'=>'inputbox required', 'size'=>'1'),'value','text', isset($item_id) ? $item_id : "", 'jform_menutype');
			//$html[] = "&nbsp;&nbsp;<label title=\"\" class=\"hasTip\" for=\"jform_formmenu_name\" id=\"jform_formmenu_name-lbl\">".JText::_( 'COM_YJCONTACTUS_MENU_NAME' )."</label><input type=\"text\" size=\"40\" value=\"\" name=\"jform[menu_name]\" id=\"menu_name\" maxlength=\"500\" />";
		}else{
			$html[] = JText::_('COM_YJCONTACTUS_CHECK_MENU_ITEMS');
		}

		return implode($html);
	}
}