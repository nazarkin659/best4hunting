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
class YjContactUSViewYjForm extends JView
{
	/**
	 * display method of YjContactUS view
	 * @return void
	 **/
	function display($tpl = null){
		
		// get the Data
		$form 	= $this->get('Form');
		$item 	= $this->get('Item');
		$script = $this->get('Script');
		$menu 	= $this->get('MenuDetails');
		
		// Check for errors.
		if (count($errors = $this->get('Errors'))) 
		{
			JError::raiseError(500, implode('<br />', $errors));
			return false;
		}
		// Assign the Data
		$this->form = $form;
		$this->item = $item;
		$this->script = $script;
		$this->menu = $menu;

		//assign menu values to form values for selected form options
		$this->form->setValue('menutype', NULL, $menu->menutype);
		$this->form->setValue('menu_name', NULL, $menu->menu_name);
		//$this->form->setValue('departments', NULL, $item->departments);

		// Set the toolbar
		$this->addToolBar();

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
		JRequest::setVar('hidemainmenu', true);
		$user = JFactory::getUser();
		$userId = $user->id;
		$isNew = $this->item->id == 0;
		$canDo = YjContactUSHelpers::getActions($this->item->id);
		
		JToolBarHelper::title($isNew ? JText::_('COM_YJCONTACTUS_TITLE_NEW_FORM') : JText::_('COM_YJCONTACTUS_TITLE_EDIT_FORM'), 'generic.png');
		// Built the actions for new and existing records.
		if ($isNew) 
		{
			// For new records, check the create permission.
			if ($canDo->get('core.create')) 
			{
				JToolBarHelper::apply('yjform.apply', 'JTOOLBAR_APPLY');
				JToolBarHelper::save('yjform.save', 'JTOOLBAR_SAVE');
				JToolBarHelper::custom('yjform.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
			}
			JToolBarHelper::cancel('yjform.cancel', 'JTOOLBAR_CANCEL');
		}
		else
		{
			if ($canDo->get('core.edit'))
			{
				// We can save the new record
				JToolBarHelper::apply('yjform.apply', 'JTOOLBAR_APPLY');
				JToolBarHelper::save('yjform.save', 'JTOOLBAR_SAVE');

				// We can save this record, but check the create permission to see if we can return to make a new one.
				if ($canDo->get('core.create')) 
				{
					JToolBarHelper::custom('yjform.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
				}
			}
			if ($canDo->get('core.create')) 
			{
				JToolBarHelper::custom('yjform.save2copy', 'save-copy.png', 'save-copy_f2.png', 'JTOOLBAR_SAVE_AS_COPY', false);
			}
			JToolBarHelper::cancel('yjform.cancel', 'JTOOLBAR_CLOSE');
		}
	}
	/**
	 * Method to set up the document properties
	 *
	 * @return void
	 */
	protected function setDocument() 
	{
		$isNew = $this->item->id == 0;
		$document = JFactory::getDocument();
		$document->setTitle($isNew ? JText::_('COM_YJCONTACTUS_TITLE_NEW_FORM') : JText::_('COM_YJCONTACTUS_TITLE_EDIT_FORM'));
		$document->addScript(JURI::root() . $this->script);
		$document->addScript(JURI::root() . "/administrator/components/com_yjcontactus/views/yjform/submitbutton.js");
		JText::script('COM_YJCONTACTUS_ERROR_UNACCEPTABLE');
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
	}	*/
}
?>