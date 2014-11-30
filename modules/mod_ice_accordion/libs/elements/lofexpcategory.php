<?php
/**
 * @version		$Id: categoriesmultiple.php 1034 2011-10-04 17:00:00Z joomlaworks $
 * @package		K2
 * @author		JoomlaWorks http://www.joomlaworks.gr
 * @copyright	Copyright (c) 2006 - 2011 JoomlaWorks Ltd. All rights reserved.
 * @license		GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.html.html');
jimport('joomla.form.formfield');

class JFormFieldLofexpCategory extends JFormField
{

	var	$_name = 'lofexpcategory';

	function getInput(){
		
		$fieldName = $this->name;
		$class = isset($this->element["class"])?$this->element["class"]:$this->element->attributes("class");
		// Load some common models
		$db = &JFactory::getDBO();
		$query = "SELECT id,name FROM #__expautos_categories";
		$db->setQuery($query);
		$data = $db->loadObjectList();
		return JHtml::_( 'select.genericlist', 
						 $data, ''.$this->name,
						 'class="inputbox '.$class.'"   multiple="multiple" size="10"',
						 'id', 
						 'name', 
						 $this->value,$this->id );
	}
}
