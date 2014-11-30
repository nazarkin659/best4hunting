<?php
/**
 * YjContactUs default controller
 * 
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controllerform');

/**
 * @package		Joomla.Administrator
 * @subpackage	com_content
 * @since		1.6
 */
class YJContactusControllerYJForm extends JControllerForm
{
	/**
	 * Class constructor.
	 *
	 * @param	array	$config	A named array of configuration variables.
	 *
	 * @return	JControllerForm
	 * @since	1.6
	 */
	function __construct($config = array())
	{
		// An article edit form can come from the articles or featured view.
		// Adjust the redirect view on the value of 'return' in the request.
		if (JRequest::getCmd('task') == 'cancel' || JRequest::getCmd('task') == 'save') {
			$this->view_list = 'yjforms';
		}

		parent::__construct($config);
	}
	
}
?>