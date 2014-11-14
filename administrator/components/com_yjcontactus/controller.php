<?php
/**
 * YjContactUs default controller
 * 
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla controller library
jimport('joomla.application.component.controller');

/**
 * General Controller of YJCONTACTUS component
 */
class YjContactUSController extends JController
{

	/**
	 * @var		string	The default view.
	 * @since	1.6
	 */
	protected $default_view = 'yjforms';

	/**
	 * Method to display a view.
	 *
	 * @param	boolean			If true, the view output will be cached
	 * @param	array			An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
	 *
	 * @return	JController		This object to support chaining.
	 * @since	1.5
	 */
	public function display($cachable = false, $urlparams = false){
		//require_once JPATH_COMPONENT.'/helpers/helper.php';

		// Load the submenu.
		//MiaFlvHelpers::addSubmenu(JRequest::getCmd('view', 'movies'));

		$view		= JRequest::getCmd('view', 'yjforms');
		$layout 	= JRequest::getCmd('layout', 'default');
		$id			= JRequest::getInt('id');

		//load component css styles
		$document 	= JFactory::getDocument();
		$document->addStyleSheet('components/com_yjcontactus/css/admin.yjcontactus.css');		

		// Check for edit form.
		if ($view == 'yjforms' && $layout == 'edit' && !$this->checkEditId('com_yjcontactus.edit.yjforms', $id)) {
			// Somehow the person just went to the form - we don't allow that.
			$this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_UNHELD_ID', $id));
			$this->setMessage($this->getError(), 'error');
			$this->setRedirect(JRoute::_('index.php?option=com_yjcontactus&view=yjforms', false));

			return false;
		}

		parent::display();
		
		return $this;
	}

}
?>