<?php

/**
 * Default model for YjContactUS Component
 * 
 * @package     
 * @subpackage Components
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla modelform library
jimport('joomla.application.component.modeladmin');

/**
 * Default Model
 *
 */
class YjContactUSModelYjDepartment extends JModelAdmin{

	/**
	 * Method to save the form data.
	 *
	 * @param	array	The form data.
	 *
	 * @return	boolean	True on success.
	 * @since	1.6
	 */ 
	public function save($data){

		//$id 	= JRequest::getInt( 'id' );
		//$app	= JFactory::getApplication();
		$task	= JRequest::getVar( 'task', '' );
		$db		= JFactory::getDbo();

		// is new or not
		if ($data['id'] == 0) {
			// if new item order last in appropriate group
			//$where = " parent = ".(int) $parent;
			//$row->ordering = $row->getNextOrder( $where );
			$table				= $this->getTable();			
			$data['ordering'] 	= $table->getNextOrder();
			//$row->published	= 1;
			//create new video title for save2copy
			if($task == 'save2copy'){
				$data['name'] = "Copy of ".$data['name'];
			}
		}/*else{
			$data['id'] = $id;
		}*/

		if (parent::save($data)){
			return true;
		}

		return false;
	}

	/**
	 * Method override to check if you can edit an existing record.
	 *
	 * @param	array	$data	An array of input data.
	 * @param	string	$key	The name of the key for the primary key.
	 *
	 * @return	boolean
	 * @since	1.6
	 */
	protected function allowEdit($data = array(), $key = 'id')
	{
		// Check specific edit permission then general edit permission.
		return JFactory::getUser()->authorise('core.edit', 'com_yjcontactus.message.'.((int) isset($data[$key]) ? $data[$key] : 0)) or parent::allowEdit($data, $key);
	}
	/**
	 * Returns a reference to the a Table object, always creating it.
	 *
	 * @param	type	The table type to instantiate
	 * @param	string	A prefix for the table class name. Optional.
	 * @param	array	Configuration array for model. Optional.
	 * @return	JTable	A database object
	 * @since	1.6
	 */
	public function getTable($type = 'YjDepartment', $prefix = 'YjContactUSTable', $config = array()) 
	{
		return JTable::getInstance($type, $prefix, $config);
	}
	/**
	 * Method to get the record form.
	 *
	 * @param	array	$data		Data for the form.
	 * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
	 * @return	mixed	A JForm object on success, false on failure
	 * @since	1.6
	 */
	public function getForm($data = array(), $loadData = true) 
	{
		// Get the form.
		$form = $this->loadForm('com_yjcontactus.yjdepartment', 'department', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form)) 
		{
			return false;
		}
		return $form;
	}
	/**
	 * Method to get the script that have to be included on the form
	 *
	 * @return string	Script files
	 */
	public function getScript() 
	{
		//return 'administrator/components/com_yjcontactus/models/forms/form.js';
		return "";
	}
	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return	mixed	The data for the form.
	 * @since	1.6
	 */
	protected function loadFormData() 
	{
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_yjcontactus.edit.department.data', array());
		if (empty($data)) 
		{
			$data = $this->getItem();
		}
		return $data;
	}

}
?>