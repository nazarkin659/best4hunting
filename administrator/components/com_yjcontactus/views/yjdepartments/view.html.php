<?php
/**
 * Departments View for YjContactUS Component
 * 
 * @package
 * @subpackage Components
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

jimport( 'joomla.application.component.view' );

/**
 * Departments View
 *
 * @package
 * @subpackage Components
 */
class YjContactUSViewYjDepartments extends JView
{
	/**
	 * display method of YjContactUS view
	 * @return void
	 **/
	function display($tpl = null){

		// Get data from the model
		$this->rows 		= $this->get('Items');
		$this->pagination 	= $this->get('Pagination');
		$this->state		= $this->get('State');

		// Check for errors.
		if (count($errors = $this->get('Errors'))) 
		{
			JError::raiseError(500, implode('<br />', $errors));
			return false;
		}
		// Assign data to the view
		//$this->items = $items;
		//$this->pagination = $pagination;

		// Set the toolbar
		$this->addToolBar();
		
		// Set the submenu
		YjContactUSHelpers::addSubmenu('messages');		

		// Display the template
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
		if ($canDo->get('core.manage')){		
			JToolBarHelper::custom('yjdepartments.publish', 'publish.png', 'publish_f2.png','JTOOLBAR_PUBLISH', true);
			JToolBarHelper::custom('yjdepartments.unpublish', 'unpublish.png', 'unpublish_f2.png', 'JTOOLBAR_UNPUBLISH', true);
		}		
		if ($canDo->get('core.create')){
			JToolBarHelper::addNew('yjdepartment.add', 'JTOOLBAR_NEW');
		}
		if ($canDo->get('core.edit')){
			JToolBarHelper::editList('yjdepartment.edit', 'JTOOLBAR_EDIT');
		}
		if ($canDo->get('core.delete')){
			JToolBarHelper::deleteList('', 'yjdepartments.delete', 'JTOOLBAR_DELETE');
		}
		if ($canDo->get('core.admin')){
			JToolBarHelper::divider();
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
		$document->setTitle(JText::_('COM_YJCONTACTUS_TITLE_DEPART'));
	}
	
//////////////////////////////////////////////////////////JOOMLA 1.5 FUNCTIONS	
	
/*	function add($tpl = null) { 
		JToolBarHelper::title(   JText::_( 'TITLE NEW DEPART' ), 'generic.png' );
		JToolBarHelper::Save();
		JToolBarHelper::Apply();		
		JToolBarHelper::Cancel();
		JToolBarHelper::preferences('com_yjcontactus', '200');		
			
		$this->setLayout('add');
		parent::display($tpl);
	}	
	
	function edit($tpl = null) {
		JToolBarHelper::title(   JText::_( 'TITLE EDIT DEPART' ), 'generic.png' );
		JToolBarHelper::Save();
		JToolBarHelper::Apply();		
		JToolBarHelper::Cancel();
		JToolBarHelper::preferences('com_yjcontactus', '200');		
		
		$item = $this->get('item');
		$this->assignRef('item', $item);

		$this->setLayout('add');
		parent::display($tpl);
	}*/	
}
?>