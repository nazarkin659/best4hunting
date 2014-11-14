<?php
/**
 * CSS View for YjContactUS Component
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
class YjContactUSViewYjCSS extends JView
{
	/**
	 * Settings view display method
	 * @return void
	 **/
	function display($tpl = null){
	
		JToolBarHelper::title( JText::_( 'COM_YJCONTACTUS_TITLE_CSS_MANAGER' ), 'generic.png' );
		//JToolBarHelper::preferences('com_yjcontactus', '200');
		
		//get css file for edit
		$css_file 			= $this->get( 'edit_css');	
		$this->css_file 	= $css_file['file_content'];
		$this->css_file_name= $css_file['css_file_name'];

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
	protected function setDocument(){
	
		$document = JFactory::getDocument();
		$document->setTitle(JText::_('COM_YJCONTACTUS_TITLE_CSS_MANAGER'));
		//$document->addScript(JURI::root() . $this->script);
		//$document->addScript(JURI::root() . "/administrator/components/com_yjcontactus/views/yjdepartment/submitbutton.js");
		//JText::script('COM_YJCONTACTUS_ERROR_UNACCEPTABLE');
	}	
}
?>