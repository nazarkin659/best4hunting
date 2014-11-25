<?php
/**
 * Default View for YjContactUS Component
 * 
 * @package    
 * @subpackage Components
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

jimport( 'joomla.application.component.view' );

/**
 * Default View
 *
 * @package    
 * @subpackage Components
 */
class YjContactUSViewYjForms extends JView
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
		JToolBarHelper::title(JText::_('COM_YJCONTACTUS_TITLE_FORM'), 'generic.png');
		if ($canDo->get('core.manage')){		
			JToolBarHelper::custom('yjforms.publish', 'publish.png', 'publish_f2.png','JTOOLBAR_PUBLISH', true);
			JToolBarHelper::custom('yjforms.unpublish', 'unpublish.png', 'unpublish_f2.png', 'JTOOLBAR_UNPUBLISH', true);
		}		
		if ($canDo->get('core.create')){
			JToolBarHelper::addNew('yjform.add', 'JTOOLBAR_NEW');
		}
		if ($canDo->get('core.edit')){
			JToolBarHelper::editList('yjform.edit', 'JTOOLBAR_EDIT');
		}
		if ($canDo->get('core.delete')){
			JToolBarHelper::deleteList('', 'yjforms.delete', 'JTOOLBAR_DELETE');
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
		$document->setTitle(JText::_('COM_YJCONTACTUS_TITLE_FORM'));
	}
	

//////////////////////////////////////////////////////////JOOMLA 1.5 FUNCTIONS	
		
/*	function add($tpl = null) { 
		JToolBarHelper::title( JText::_( 'TITLE NEW FORM' ), 'generic.png' );
		JToolBarHelper::Save();
		JToolBarHelper::Apply();
		JToolBarHelper::Cancel();
		JToolBarHelper::preferences('com_yjcontactus', '200');		

		// Get departments 
		$departments = & $this->get( 'departments');
		$this->assignRef('departments', $departments);

		// Get menu items assign to yjcontactus
		$menu = & $this->get( 'menu');
		$this->assignRef('menu', $menu);		

		$this->setLayout('add');
		parent::display($tpl);
	}
	

	function edit($tpl = null) {
		JToolBarHelper::title(   JText::_( 'TITLE EDIT FORM' ), 'generic.png' );
		JToolBarHelper::Save();
		JToolBarHelper::Apply();		
		JToolBarHelper::Cancel();
		JToolBarHelper::preferences('com_yjcontactus', '200');		
		
		// Get departments 
		$departments = & $this->get( 'departments');		
		$this->assignRef('departments', $departments);		
		
		$item = $this->get('item');
		$this->assignRef('item', $item);

		//create new object to work selected values on multiple select
		$departments_array = explode(",",$item->departments);
		foreach($departments_array as $departments_row => $departments_value){
			$departments_selected[$departments_row]->value = $departments_value;
		}
		$this->assignRef('departments_selected', $departments_selected);

		// Get menu name assigned for this form
		$menu_name = & $this->get( 'menu_name' );
		$this->assignRef('menu_name', $menu_name);

		// Get menu type
		$menu = & $this->get( 'menu');
		$this->assignRef('menu', $menu);
		
		$this->setLayout('add');
		parent::display($tpl);
	}*/	
}
?>