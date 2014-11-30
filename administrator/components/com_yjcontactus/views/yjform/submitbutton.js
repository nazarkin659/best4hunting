/**
 * @version		$Id: submitbutton.js 74 2010-12-01 22:04:52Z chdemko $
 * @package		Joomla16.Tutorials
 * @subpackage	Components
 * @copyright	Copyright (C) 2005 - 2010 Open Source Matters, Inc. All rights reserved.
 * @author		Christophe Demko
 * @link		http://joomlacode.org/gf/project/helloworld_1_6/
 * @license		License GNU General Public License version 2 or later
 */

Joomla.submitbutton = function(task)
{
	if (task == '')
	{
		return false;
	}
	else
	{
		var isValid=true;
		var action = task.split('.');
		if (action[1] != 'cancel' && action[1] != 'close')
		{
			var forms = $$('form.form-validate');
			for (var i=0;i<forms.length;i++)
			{
				if (!document.formvalidator.isValid(forms[i]))
				{
					isValid = false;
					break;
				}
			}
		}
	
/*	var form = document.adminForm;
	if (pressbutton == 'cancel') {
		submitform( pressbutton );
		return;
	}
	// do field validation
	if ( form.name.value == "" ) {
		alert( "<?php //echo JText::_('ALERT FORM NAME') ?>" );
	} else if ( form.departments && form.departments.value == "" ) {
		alert( "<?php //echo JText::_('ALERT DEPART VALUE') ?>" );
	} else if ( form.menu_type && form.menu_type.value == "" ) {
		alert( "<?php //echo JText::_('ALERT SELECT MENU TYPE') ?>" );
	} else if ( form.menu_name && form.menu_name.value == "" ) {
		alert( "<?php //echo JText::_('ALERT MENU NAME') ?>" );		
	} else {
		submitform( pressbutton );
	}*/	
	
		if (isValid)
		{
			Joomla.submitform(task);
			return true;
		}
		else
		{
			alert(Joomla.JText._('COM_YJMS_ERROR_UNACCEPTABLE','Some values are unacceptable'));
			return false;
		}
	}
}

