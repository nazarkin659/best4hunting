<?php
/**
 * Helpers tmpl for YjContactUS Component
 * 
 * @package    
 * @subpackage Components
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die(); 

jimport('joomla.application.component.model');

class YjContactUSHelpers {

	/**
	 * get the name of department
	 * 
	 * @param $id
	 * @return unknown_type
	 */
	function get_departments_name($id) { 

		global $mainframe, $option;
		$db =& JFactory::getDBO(); 
		
		$id_array = explode(",",$id);
		$name = array();
		
		foreach($id_array as $id_array_value){
			// build query 's conditions
			$where 	= ' WHERE `id` = "'.$id_array_value.'" ';		
			
			// Get the name - query
			$query = 'SELECT name' 
					.' FROM #__yj_contactus_departments'
					."\n {$where}";
			$db->setQuery($query);
			$name[] = $db->loadResult();
			
		}		
		
		$return_name = implode(", ",$name);
		return $return_name;
	}
	
	/**
	 * get the name of department
	 * 
	 * @param $id
	 * @return unknown_type
	 */
	function get_menu_name($id) { 

		global $mainframe, $option;
		$db =& JFactory::getDBO(); 
		
		// build query 's conditions
		$where 	= ' WHERE `id` = "'.$id.'" ';		
		
		// Get the name - query
		$query = 'SELECT title ' 
				.' FROM #__menu'
				."\n {$where}";
		$db->setQuery($query);
		$name = $db->loadResult();
		if(!empty($name)){
			return $name;
		}else{
			return "<font color=red>".JText::_('COM_YJCONTACTUS_CHECK_MENU_ITEMS')."</font>";
		}
	}
	
	/**
	 * see if a menu item_id is already selected for another form
	 * 
	 * @param $id
	 * @return unknown_type
	 */
	function generate_disable_menu($id) { 

		global $mainframe, $option;
		$db =& JFactory::getDBO(); 
		
		// build query 's conditions
		$where 	= ' WHERE `item_id` = "'.$id.'" ';		
		
		// Get the name - query
		$query = 'SELECT id' 
				.' FROM #__yj_contactus_forms'
				."\n {$where}";
		$db->setQuery($query);
		$name = $db->loadResult();
		
		if($name) return 'disabled';
		else return '';
	}			

	/**
	 * Configure the Linkbar.
	 */
	public static function addSubmenu($submenu) 
	{
		JSubMenuHelper::addEntry(JText::_('COM_YJCONTACTUS_SUBMENU_FORMS'), 'index.php?option=com_yjcontactus&amp;view=yjforms', $submenu == 'Forms');
		JSubMenuHelper::addEntry(JText::_('COM_YJCONTACTUS_SUBMENU_DEPARTMENTS'), 'index.php?option=com_yjcontactus&amp;view=yjdepartments&amp;controller=yjdepartments', $submenu == 'Departments');
		JSubMenuHelper::addEntry(JText::_('COM_YJCONTACTUS_SUBMENU_SETTINGS'), 'index.php?option=com_yjcontactus&amp;view=yjsettings&amp;controller=yjsettings', $submenu == '');
		JSubMenuHelper::addEntry(JText::_('COM_YJCONTACTUS_SUBMENU_CSS_MANAGER'), 'index.php?option=com_yjcontactus&amp;view=yjcss&amp;controller=yjcss', $submenu == '');
		JSubMenuHelper::addEntry(JText::_('COM_YJCONTACTUS_SUBMENU_LANGUAGE_MANAGER'), 'index.php?option=com_yjcontactus&amp;view=yjlang&amp;controller=yjlang', $submenu == '');
		// set some global property
		$document = JFactory::getDocument();
		$document->addStyleDeclaration('.icon-48-helloworld {background-image: url(../media/com_helloworld/images/tux-48x48.png);}');
/*		if ($submenu == 'categories'){
			$document->setTitle(JText::_('COM_YJCONTACTUS_ADMINISTRATION_CATEGORIES'));
		}*/
	}
	
	/**
	 * Get the actions
	 */
	public static function getActions($messageId = 0)
	{
		$user	= JFactory::getUser();
		$result	= new JObject;

		if (empty($messageId)) {
			$assetName = 'com_yjcontactus';
		}
		else {
			$assetName = 'com_yjcontactus.message.'.(int) $messageId;
		}

		$actions = array(
			'core.admin', 'core.manage', 'core.create', 'core.edit', 'core.delete'
		);

		foreach ($actions as $action) {
			$result->set($action,	$user->authorise($action, $assetName));
		}

		return $result;
	}
	
	/**
	* Applies the content tag filters to arbitrary text as per settings for current user group
	* @param text The string to filter
	* @return string The filtered string
	*/
	public static function filterText($text)
	{
		// Filter settings
		jimport('joomla.application.component.helper');
		$config		= JComponentHelper::getParams('com_content');
		$user		= JFactory::getUser();
		$userGroups	= JAccess::getGroupsByUser($user->get('id'));

		$filters = $config->get('filters');

		$blackListTags			= array();
		$blackListAttributes	= array();

		$whiteListTags			= array();
		$whiteListAttributes	= array();

		$noHtml		= false;
		$whiteList	= false;
		$blackList	= false;
		$unfiltered	= false;

		// Cycle through each of the user groups the user is in.
		// Remember they are include in the Public group as well.
		foreach ($userGroups AS $groupId)
		{
			// May have added a group by not saved the filters.
			if (!isset($filters->$groupId)) {
				continue;
			}

			// Each group the user is in could have different filtering properties.
			$filterData = $filters->$groupId;
			$filterType	= strtoupper($filterData->filter_type);

			if ($filterType == 'NH') {
				// Maximum HTML filtering.
				$noHtml = true;
			}
			else if ($filterType == 'NONE') {
				// No HTML filtering.
				$unfiltered = true;
			}
			else {
				// Black or white list.
				// Preprocess the tags and attributes.
				$tags			= explode(',', $filterData->filter_tags);
				$attributes		= explode(',', $filterData->filter_attributes);
				$tempTags		= array();
				$tempAttributes	= array();

				foreach ($tags AS $tag)
				{
					$tag = trim($tag);

					if ($tag) {
						$tempTags[] = $tag;
					}
				}

				foreach ($attributes AS $attribute)
				{
					$attribute = trim($attribute);

					if ($attribute) {
						$tempAttributes[] = $attribute;
					}
				}

				// Collect the black or white list tags and attributes.
				// Each list is cummulative.
				if ($filterType == 'BL') {
					$blackList				= true;
					$blackListTags			= array_merge($blackListTags, $tempTags);
					$blackListAttributes	= array_merge($blackListAttributes, $tempAttributes);
				}
				else if ($filterType == 'WL') {
					$whiteList				= true;
					$whiteListTags			= array_merge($whiteListTags, $tempTags);
					$whiteListAttributes	= array_merge($whiteListAttributes, $tempAttributes);
				}
			}
		}

		// Remove duplicates before processing (because the black list uses both sets of arrays).
		$blackListTags			= array_unique($blackListTags);
		$blackListAttributes	= array_unique($blackListAttributes);
		$whiteListTags			= array_unique($whiteListTags);
		$whiteListAttributes	= array_unique($whiteListAttributes);

		// Unfiltered assumes first priority.
		if ($unfiltered) {
			$filter = JFilterInput::getInstance(array(), array(), 1, 1, 0);
		}
		// Black lists take second precedence.
		else if ($blackList) {
			// Remove the white-listed attributes from the black-list.
			$filter = JFilterInput::getInstance(
				array_diff($blackListTags, $whiteListTags), 			// blacklisted tags
				array_diff($blackListAttributes, $whiteListAttributes), // blacklisted attributes
				1,														// blacklist tags
				1														// blacklist attributes
			);
		}
		// White lists take third precedence.
		else if ($whiteList) {
			$filter	= JFilterInput::getInstance($whiteListTags, $whiteListAttributes, 0, 0, 0);  // turn off xss auto clean
		}
		// No HTML takes last place.
		else {
			$filter = JFilterInput::getInstance();
		}

		$text = $filter->clean($text, 'html');

		return $text;
	}	
}
?>