<?php
/**
 * Settings View for YjContactUS Component
 * 
 * @package
 * @subpackage Components
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

jimport( 'joomla.application.component.view' );

/**
 * Settings View
 *
 * @package
 * @subpackage Components
 */
class YjContactUSViewYjSettings extends JView{

	/**
	 * Settings view display method
	 * @return void
	 **/
	function display($tpl = null){

		JToolBarHelper::title( JText::_( 'COM_YJCONTACTUS_TITLE_SETTINGS' ), 'generic.png' );
		//JToolBarHelper::preferences('com_yjcontactus', '200');

		// Get upload folder
		$return			= $this->get('folder');
		$this->folder 	= $return['folder']->upload_folder;

		// Get all images folders
		$return_folders		= $this->get('list_folders');
		$this->list_folders = $return_folders;

		//load the com_media language file for settings page
		$lang = JFactory::getLanguage();
		$lang->load('com_media');
		
		JHTML::_('behavior.framework', true);
		JHTML::_('behavior.mootools');

		$document = JFactory::getDocument();
		//$document->addScriptDeclaration("
		//window.addEvent('domready', function() {
		//	document.preview = SqueezeBox;
		//});");

		//$document->addScript(JURI::root() . "media/system/js/mootree.js");
		//$document->addScript(JURI::root() . "media/media/js/mediamanager.js");		
		
		$document = JFactory::getDocument();
		$document->addScriptDeclaration("
		window.addEvent('domready', function() {
			$$('a.img-preview').each(function(el) {
				el.addEvent('click', function(e) {
					new Event(e).stop();
					window.top.document.preview.fromElement(el);
				});
			});
			// create empty updateUploader function to remove the iframe com_media errors
			document.updateUploader = function() { };			
		});");
		
		
		//JHTML::_('script','../../../media/mediamanager.js', true, true);		
		//JHTML::_('script','../../../system/mootree.js', true, true, false, false);
		//JHTML::_('stylesheet','../../../system/mootree.css', array(), true);			
		//JHTML::_('script','../media/media/js/mediamanager.js', true, true);

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
		JToolBarHelper::title(JText::_('COM_YJCONTACTUS_TITLE_SETTINGS'), 'generic.png');
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
		$document->setTitle(JText::_('COM_YJCONTACTUS_TITLE_SETTINGS'));
		//$document->addScript(JURI::root() . $this->script);
		//JText::script('COM_YJCONTACTUS_ERROR_UNACCEPTABLE');
	}	
}
?>