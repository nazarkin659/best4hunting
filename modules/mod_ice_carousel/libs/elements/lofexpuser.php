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

class JFormFieldLofexpUser extends JFormField
{

	var	$_name = 'lofexpuser';

	function getInput(){
		
		$fieldName = $this->name;
		$class = isset($this->element["class"])?$this->element["class"]:$this->element->attributes("class");
		// Load some common models
		$db = &JFactory::getDBO();
		$query = "SELECT expuser.userid as user_id,username FROM #__expautos_expuser as expuser LEFT JOIN #__users AS u On expuser.userid=u.id ";
		$db->setQuery($query);
		$data = $db->loadObjectList();
		return JHtml::_( 'select.genericlist', 
						 $data, ''.$this->name,
						 'class="inputbox '.$class.'"   multiple="multiple" size="10"',
						 'user_id',
						 'username',
						 $this->value,$this->id );
	}
}
