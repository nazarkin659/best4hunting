<?php
/**
 * Lang View for YjContactUS Component
 * 
 * @package
 * @subpackage Components
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

jimport( 'joomla.application.component.view' );

/**
 * CSS View
 *
 * @package
 * @subpackage Components
 */
class YjContactUSViewYjLang extends JView
{
	/**
	 * Settings view display method
	 * @return void
	 **/
	function display($tpl = null){
	
		JToolBarHelper::title( JText::_( 'COM_YJCONTACTUS_TITLE_LANG_MANAGER' ), 'generic.png' );
		//JToolBarHelper::preferences('com_yjcontactus', '200');
		
		//get admin lang file for edit
		$admin_lang_file 			= $this->get( 'edit_admin_lang');	
		$this->admin_lang_file 		= $admin_lang_file['file_content'];
		$this->admin_lang_file_name = $admin_lang_file['lang_file'];

		//get front-end lang file for edit
		$front_lang_file 			= $this->get( 'edit_front_lang');	
		$this->front_lang_file 		= $front_lang_file['file_content'];
		$this->front_lang_file_name = $front_lang_file['lang_file'];

		// Set the toolbar
		$this->addToolBar();

		// Set the submenu
		YjContactUSHelpers::addSubmenu('messages');

		parent::display($tpl);
		
		// Set the document
		$this->setDocument();
	}

	/**
	 * Setting the toolbar
	 */
	protected function addToolBar() 
	{
		$canDo = YjContactUSHelpers::getActions();
		JToolBarHelper::title(JText::_('COM_YJCONTACTUS_TITLE_DEPART'), 'generic.png');
		if ($canDo->get('core.admin')){
			JToolBarHelper::preferences('com_yjcontactus', 400, 600);
		}
	}

	/**
	 * Method to set up the document properties
	 *
	 * @return void
	 */
	protected function setDocument() 
	{
		$document = JFactory::getDocument();
		$document->setTitle(JText::_('COM_YJCONTACTUS_TITLE_LANG_MANAGER'));
		//$document->addScript(JURI::root() . $this->script);
		//$document->addScript(JURI::root() . "/administrator/components/com_yjcontactus/views/yjdepartment/submitbutton.js");
		//JText::script('COM_YJCONTACTUS_ERROR_UNACCEPTABLE');
	}
}
?>