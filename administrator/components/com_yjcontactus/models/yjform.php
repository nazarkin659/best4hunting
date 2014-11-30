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
class YjContactUSModelYjForm extends JModelAdmin{

	/**
	 * Method to save the form data.
	 *
	 * @param	array	The form data.
	 *
	 * @return	boolean	True on success.
	 * @since	1.6
	 */ 
	public function getMenuDetails(){

		$id		= JRequest::getInt( 'id' );
		$db		= JFactory::getDbo();

		if($id > 0){
			$query	= $db->getQuery(true);
			$query->clear();
			$query->select('`m`.`menutype`, `m`.`title` as menu_name');
			$query->from('#__menu as m, #__yj_contactus_forms as f');
			$query->where('f.id = '.$id.' AND m.id = f.item_id');
			$db->setQuery($query);
			$id_menu = $db->loadObjectList();

			if(!empty($id_menu)){
				return $id_menu[0];
			}else{
				$return->menutype="";
				$return->menu_name="";
				return $return;
			}
		}else{
			$return->menutype="";
			$return->menu_name="";
			return $return;
		}

	}

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

		$query	= $db->getQuery(true);

		//create new form title for save2copy
		if($task == 'save2copy'){
			$data['name'] 		= "Copy of ".$data['name'];
			$table				= $this->getTable();			
			$data['ordering'] 	= $table->getNextOrder();
			//generate departments field value
			$data['departments'] = implode(",",$data['departments']);
			$data['item_id']	 = $data['item_id'];
			if (parent::save($data)){
				return true;
			}else{
				JError::raiseError(500, $db->getErrorMsg());
				return false;			
			}
		}

		// save new created item
		if ($data['id'] == '') {
			// if new item order last in appropriate group
			//$where = " parent = ".(int) $parent;
			//$row->ordering = $row->getNextOrder( $where );
			$table				= $this->getTable();			
			$data['ordering'] 	= $table->getNextOrder();

			//generate departments field value
			$data['departments'] = implode(",",$data['departments']);
	
			if (parent::save($data)){
				$id = $db->insertId();
	
				//yj_contactus component id		
				//$database->setQuery( "SELECT id FROM #__components WHERE admin_menu_link = 'option=com_yjcontactus'" );
				//$id_component = $database->loadResult();
				$query->clear();
				$query->select('extension_id');
				$query->from('#__extensions');
				$query->where('`type` = "component"');
				$query->where('`element` = "com_yjcontactus"');
				$db->setQuery($query);
				$id_component = $db->loadResult();
				//print_r($query->__toString());
		
				//$database->setQuery( "SELECT MAX(ordering) FROM #__menu" );
				//$ordering = $database->loadResult();
				$query->clear();
				$query->select('MAX(ordering)');
				$query->from('#__menu');
				$query->where('`menutype` = "mainmenu"');
				$db->setQuery($query);
				$ordering = $db->loadResult();				
				$next_ordering = $ordering + 1;	
					
				//insert new menu item 
				//$sql = "INSERT INTO `#__menu` (`id`, `menutype`, `name`, `alias`, `link`, `type`, `published`, `parent`, `componentid`, `sublevel`, `ordering`, `checked_out`, `checked_out_time`, `pollid`, `browserNav`, `access`, `utaccess`, `params`, `lft`, `rgt`, `home`) ".
				//		"VALUES ('', '".$menu_type."', '".$menu_name."', '".strtolower(str_replace(" ","-",$menu_name))."' , 'index.php?option=com_yjcontactus', 'component', '1', '0', '".$id_component."', '0', '".$next_ordering."' , '0', '0000-00-00 00:00:00', '0', '0', '0', '0', '', '0', '0', '0')";
				//$database->setQuery( $sql );
				//$database->Query( $sql );
		
				$query->clear();
				$query->insert("#__menu");
				$query->set("`id` = ''");
				$query->set("`menutype` = '".$data['menutype']."'");
				$query->set("`title` = '".$data['menu_name']."'");
				$query->set("`alias` = '".strtolower(str_replace(" ","-",$data['menu_name']))."'");
				$query->set("`note` = ''");
				$query->set("`path` = '".strtolower(str_replace(" ","-",$data['menu_name']))."'");
				$query->set("`link` = 'index.php?option=com_yjcontactus&view=yjcontactus'");
				$query->set("`type` = 'component'");
				$query->set("`published` = '1'");
				$query->set("`parent_id` = '1'");
				$query->set("`level` = '1'");
				$query->set("`component_id` = '".$id_component."'");
				$query->set("`ordering` = '".$next_ordering."'");
				$query->set("`checked_out` = '0'");
				$query->set("`checked_out_time` = '0000-00-00 00:00:00'");
				$query->set("`browserNav` = '0'");
				$query->set("`access` = '1'");
				$query->set("`img` = 'class:component'");
				$query->set("`template_style_id` = '0'");
				$query->set("`params` = ''");
				$query->set("`lft` = '0'");
				$query->set("`rgt` = '1'");
				$query->set("`home` = '0'");
				$query->set("`language` = '*'");
				$query->set("`client_id` = '0'");
		
				$db->setQuery($query);
				if(!$db->Query($query)){
					JError::raiseError(500, $db->getErrorMsg());
					return false;
				}
				
				$insert_id = $db->insertid();
		
				//update form item_id
				//$menu_item_id = $insert_id;
				$query->clear();
				$query->update("#__yj_contactus_forms");
				$query->set("`item_id` = '".$insert_id."'");
				$query->where("`id` = ".$id);
				$db->setQuery($query);
				if(!$db->Query($query)){
					JError::raiseError(500, $db->getErrorMsg());
					return false;
				}
	
				return true;
			}else{
				JError::raiseError(500, $db->getErrorMsg());
				return false;				
			}
		}else{
		
			//update existing items		
			$query->clear();
			$query->select('id');
			$query->from('#__menu');
			$query->where('`id` = "'.$data['item_id'].'"');
			$query->where('`published` > -2');
		
			$db->setQuery($query);
			$id_menu = $db->loadResult();

			if($id_menu == ""){
				JError::raiseWarning(500, JText::_("COM_YJCONTACTUS_INVALID_MENUITEM"));
				return false;
			}else{
				//Update values in menu table
				$query->clear();
				$query->update("#__menu");
				$query->set("`menutype` = '".$data['menutype']."'");
				$query->set("`title` = '".$data['menu_name']."'");
				$query->where("`id` = ".$data['item_id']);
				$db->setQuery($query);
				if(!$db->Query($query)){
					JError::raiseError(500, $db->getErrorMsg());
					return false;
				}
					
				//update form item_id
				$data['departments'] = implode(",",$data['departments']);			
				$query->clear();
				$query->update("#__yj_contactus_forms");
				$query->set("`name` = '".$data['name']."'");
				$query->set("`departments` = '".$data['departments']."'");			
				$query->where("`id` = ".$data['id']);
				$db->setQuery($query);
				if(!$db->Query($query)){
					JError::raiseError(500, $db->getErrorMsg());			
					//JError::raiseWarning(500, JText::_("COM_YJCONTACTUS_MENUITEM_EXISTS"));
					return false;
				}
			}
			return true;
		}		
		return false;
	}
	
	/**
	 * delete movie
	 *
	 * @return unknown_type
	 */
	function delete($id_delete = '') {
		$app	= JFactory::getApplication();
		$db		=& JFactory::getDBO();
		$msg 	= array("msg"=>array("message"=>array(),"type"=>array()),"id"=>"");

		if($id_delete == ''){
			$cid = JRequest::getVar( 'cid', array(), '', 'array' );
		}else{
			$cid = $id_delete;
		}
		JArrayHelper::toInteger($cid, array());

		if (count($cid) > 0) {
			$row =& $this->getTable();
			foreach ($cid as $id) {
				$row->load($id);			

				//delete the db record
				if(parent::delete($id)){

					//delete menu item created for this form (itemid)
					$query	= $db->getQuery(true);
					$query->clear();
					$query->delete('#__menu');
					$query->where('`id` = '.$row->item_id);
					$db->setQuery($query);
					if(!$db->Query($query)){
						$app->setError(JText::_("COM_YJCONTACTUS_DELETE_MENU_ERROR"));
						return false;					
					}
					
				}
			}
		}else{
			$app->setMessage(JText::_("COM_YJCONTACTUS_SELECT_FORM_DELETE"));
			return false;
		}
		return true;
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
	public function getTable($type = 'YjForm', $prefix = 'YjContactUSTable', $config = array()) 
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
		$form = $this->loadForm('com_yjcontactus.yjform', 'form', array('control' => 'jform', 'load_data' => $loadData));
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
		return 'administrator/components/com_yjcontactus/models/forms/form.js';
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
		$data = JFactory::getApplication()->getUserState('com_yjcontactus.edit.form.data', array());
		if (empty($data)) 
		{
			$data = $this->getItem();
		}
		return $data;
	}

}
?>