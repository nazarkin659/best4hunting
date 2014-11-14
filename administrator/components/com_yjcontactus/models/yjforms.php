<?php

/**
 * Default model for YjContactUS Component
 * 
 * @package     
 * @subpackage Components
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

jimport('joomla.application.component.modellist');

/**
 * Default Model
 *
 */
class YjContactUSModelYjForms extends JModelList{

	/**
	 * Constructor.
	 *
	 * @param	array	An optional associative array of configuration settings.
	 * @see		JController
	 * @since	1.6
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
				'name', 'name',
				'published', 'published',
				'ordering', 'ordering',				
			);
		}

		parent::__construct($config);
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
		$form = $this->loadForm('com_yjcontactus.forms', 'forms', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form)) {
			return false;
		}

		// Modify the form based on access controls.
		if (!$this->canEditState((object) $data)) {
			// Disable fields for display.
			$form->setFieldAttribute('ordering', 'disabled', 'true');
			$form->setFieldAttribute('publish_up', 'disabled', 'true');
			$form->setFieldAttribute('publish_down', 'disabled', 'true');
			$form->setFieldAttribute('state', 'disabled', 'true');
			$form->setFieldAttribute('sticky', 'disabled', 'true');

			// Disable fields while saving.
			// The controller has already verified this is a record you can edit.
			$form->setFieldAttribute('ordering', 'filter', 'unset');
			$form->setFieldAttribute('publish_up', 'filter', 'unset');
			$form->setFieldAttribute('publish_down', 'filter', 'unset');
			$form->setFieldAttribute('state', 'filter', 'unset');
			$form->setFieldAttribute('sticky', 'filter', 'unset');
		}

		return $form;
	}	

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @return	void
	 * @since	1.6
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		// Initialise variables.
		$app = JFactory::getApplication();
		$session = JFactory::getSession();

		// Adjust the context to support modal layouts.
		if ($layout = JRequest::getVar('layout')) {
			$this->context .= '.'.$layout;
		}

		$search = $this->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		$published = $this->getUserStateFromRequest($this->context.'.filter.published', 'filter_published', '');
		$this->setState('filter.published', $published);

		// List state information.
		parent::populateState('ordering', 'asc');
	}

	/**
	 * Method to build an SQL query to load the list data.
	 *
	 * @return	string	An SQL query
	 */
	protected function getListQuery() 
	{
		// Create a new query object.
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);

		// Select some fields
		$query->select('*');

		// From the hello table
		$query->from('#__yj_contactus_forms');
		
		// Add the list ordering clause.
		$orderCol	= $this->state->get('list.ordering','ordering');
		$orderDirn	= $this->state->get('list.direction', 'asc');

/*		if ($orderCol == 'a.ordering' || $orderCol == 'category_title') {
			$orderCol = 'category_title '.$orderDirn.', a.ordering';
		}*/
		$query->order($db->getEscaped($orderCol.' '.$orderDirn));		
		
		return $query;
	}
	
	
//////////////////////////////////////////////////////////JOOMLA 1.5 FUNCTIONS
	
/*	function __construct(){
		parent::__construct();
	}*/
	

/*	function getitems(){ 
	
		global $mainframe, $option;
		$db 			=& JFactory::getDBO(); 
		$config 		= new JConfig();
		$task 			= JArrayHelper::getValue( $_REQUEST, 'task', '' ); 
		$context		= $option;	
		
		$filter_order		= $mainframe->getUserStateFromRequest( 'filter_order', 'filter_order', 'ordering', '' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( 'filter_order_Dir', 'filter_order_Dir', '', '' );	
		$limit 				= intval( $mainframe->getUserStateFromRequest( "global.list.limit", 'limit', $mainframe->getCfg('list_limit'), 'int' ) );
		$limitstart 		= intval( $mainframe->getUserStateFromRequest( "limitstart", 'limitstart', 0, 'int' ) );
		
		// build query 's order
		$orderby 	= ' ORDER BY '. $filter_order .' '. $filter_order_Dir;
		
		// Get the total number of records
		$query = 'SELECT COUNT(*)' .
				' FROM #__yj_contactus_forms';
		$db->setQuery($query);
		$total = $db->loadResult();
				
		require_once( JPATH_SITE.DS.'administrator'.DS.'includes'.DS.'pageNavigation.php' );
		$pageNav = new JPagination( $total, $limitstart, $limit );	
	
		// Get the total records
		$query = 'SELECT *' 
				.' FROM #__yj_contactus_forms'
				."\n {$orderby}";
		$db->setQuery($query,$limitstart, $limit);
		$items = $db->loadObjectList();	
		// table ordering
		$lists['order_Dir']	= $filter_order_Dir;
		$lists['order']		= $filter_order;		

		$return = array("items"=>$items,"pageNav"=>$pageNav,"lists"=>$lists);
		
		return $return;
	}
	
	function getdepartments(){ 

		global $mainframe, $option;
		$db 			=& JFactory::getDBO(); 
		
		$filter_order		= $mainframe->getUserStateFromRequest( 'filter_order', 'filter_order', 'ordering', '' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( 'filter_order_Dir', 'filter_order_Dir', '', '' );	
		
		// build query 's conditions
		$where 	= ' WHERE `published` = \'1\' ';		
		
		// build query 's order
		$orderby 	= ' ORDER BY '. $filter_order .' '. $filter_order_Dir;
		
		// Get the total records
		$query = 'SELECT *' 
				.' FROM #__yj_contactus_departments'
				."\n {$where}"
				."\n {$orderby}";
		$db->setQuery($query);
		$items = $db->loadObjectList();	

		return $items;
	}
	

	function getmenu_name(){ 

		global $mainframe, $option;
		$db  =& JFactory::getDBO(); 

		$item = $this->getitem();

		$where = ' WHERE m.id = "'.$item->item_id.'" ';
		
		// Get the total records
		$query = 'SELECT m.*' 
				.' FROM #__menu as m'
				."\n {$where}";
		$db->setQuery($query);
		$items = $db->loadObjectList();	

		return $items;
	}	
	

	function getmenu(){ 

		global $mainframe, $option;
		$db  =& JFactory::getDBO(); 
		$cid = JRequest::getVar( 'cid', array(), '', 'array' );
		$extra_from = '';
		
		// build query 's conditions
		if(empty($cid[0])){
			// Get all item_id
			$query = 'SELECT item_id' 
					.' FROM #__yj_contactus_forms'
					.' WHERE item_id > 0';
			$db->setQuery($query);
			$items_id = $db->loadResultArray();	

			$where = ' WHERE m.link LIKE "%com_yjcontactus%" AND m.type="component" ';
			if(!empty($items_id)){	
				$where .= ' AND id NOT IN ('.implode(",",$items_id).')';	
				$extra_from = ', #__yj_contactus_forms as f';
			}
		}else{
			$extra_from = ', #__yj_contactus_forms as f';		
			$where = ' WHERE m.link LIKE "%com_yjcontactus%" AND m.type="component" AND f.id != "'.$cid[0].'" AND m.id != f.item_id ';	
		}
		
		// build query 's conditions
		$order 	= ' GROUP BY m.id ORDER BY m.menutype, m.parent, m.ordering ';
		
		// Get the total records
		$query = 'SELECT m.*' 
				.' FROM #__menu_types as m';
				//."\n {$extra_from}"
				//."\n {$where}"				
				//.' WHERE m.link LIKE "%com_yjcontactus%" AND m.type="component" AND published = 1 '
				//."\n {$order}";
		$db->setQuery($query);
		$items = $db->loadObjectList();	

		return $items;
	}	
	

	function save() {

		global $mainframe, $option;
		$database  =& JFactory::getDBO();
		
		$row 			=& $this->getTable();
		$cid 			= JRequest::getVar( 'id', array(), '', 'array' );
		$departments 	= JRequest::getVar( 'departments', array(), '', 'array' );
		$menu_type 		= JRequest::getVar( 'menu_type', 'mainmenu');
		$menu_name 		= JRequest::getVar( 'menu_name', 'Contact us');				
		JArrayHelper::toInteger($cid, array());

		// is new or not
		$is_new = true;
		if ($cid[0] != 0) {
			$is_new = false;
			$row->load($cid[0]);
		}else{
			$row->ordering = $row->getNextOrder();
			//create menu item
			//yj_contactus component id
			$database->setQuery( "SELECT id FROM #__components WHERE admin_menu_link = 'option=com_yjcontactus'" );
			$id_component = $database->loadResult();
			
			$database->setQuery( "SELECT MAX(ordering) FROM #__menu" );
			$ordering = $database->loadResult();
			$next_ordering = $ordering + 1;
			
			$sql = "INSERT INTO `#__menu` (`id`, `menutype`, `name`, `alias`, `link`, `type`, `published`, `parent`, `componentid`, `sublevel`, `ordering`, `checked_out`, `checked_out_time`, `pollid`, `browserNav`, `access`, `utaccess`, `params`, `lft`, `rgt`, `home`) ".
					"VALUES ('', '".$menu_type."', '".$menu_name."', '".strtolower(str_replace(" ","-",$menu_name))."' , 'index.php?option=com_yjcontactus', 'component', '1', '0', '".$id_component."', '0', '".$next_ordering."' , '0', '0000-00-00 00:00:00', '0', '0', '0', '0', '', '0', '0', '0')";
			$database->setQuery( $sql );
			$database->Query( $sql );
			$insert_id = $database->insertid();
			
			//last insert id in menu table, item_id for forms table
			$row->item_id = $insert_id;			
		}
		
		//check to see if the form that is not new have a menu item assigned
		//if not insert one with new values and update item_id field in yj_contactus_forms

		$database->setQuery( "SELECT id FROM #__menu WHERE id = '".$row->item_id."'" );
		$id_menu = $database->loadResult();
		if($id_menu == ""){
			//yj_contactus component id		
			$database->setQuery( "SELECT id FROM #__components WHERE admin_menu_link = 'option=com_yjcontactus'" );
			$id_component = $database->loadResult();
			
			$database->setQuery( "SELECT MAX(ordering) FROM #__menu" );
			$ordering = $database->loadResult();
			$next_ordering = $ordering + 1;	
				
			//insert new menu item 
			$sql = "INSERT INTO `#__menu` (`id`, `menutype`, `name`, `alias`, `link`, `type`, `published`, `parent`, `componentid`, `sublevel`, `ordering`, `checked_out`, `checked_out_time`, `pollid`, `browserNav`, `access`, `utaccess`, `params`, `lft`, `rgt`, `home`) ".
					"VALUES ('', '".$menu_type."', '".$menu_name."', '".strtolower(str_replace(" ","-",$menu_name))."' , 'index.php?option=com_yjcontactus', 'component', '1', '0', '".$id_component."', '0', '".$next_ordering."' , '0', '0000-00-00 00:00:00', '0', '0', '0', '0', '', '0', '0', '0')";
			$database->setQuery( $sql );
			$database->Query( $sql );
			$insert_id = $database->insertid();
			//update form item_id
			$row->item_id = $insert_id;
		}else{
		//Update values in menu table
			$row->item_id = $id_menu;
			$sql = "UPDATE `#__menu`  SET `name` = '".$menu_name."' , `alias` = '".strtolower(str_replace(" ","-",$menu_name))."' , `menutype` = '".$menu_type."' WHERE `id` = '".$id_menu."'";
			$database->setQuery( $sql );
			$database->Query( $sql );
		}

		if (!$row->bind( $_POST )) {
			echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
			return false;
		}

		if (!$row->check()) {
			echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
			return false;
		}
		
		$row->departments = implode(",",$departments);
				
		if (!$row->store()) {
			$msg = "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
		}else{
			if($is_new === true){
				$msg = JText::_("SUCC ADDED");
			}else{
				$msg = JText::_("SUCC EDITED");
			}
		}

		return $msg;
	}
	

	function getitem() {
		global $mainframe, $option;

		$db	=& JFactory::getDBO();
		$cid = JRequest::getVar( 'cid', array(), '', 'array' );
		JArrayHelper::toInteger($cid, array());		
		
		// array where[]
		$where = array();
		$where[] = " id  = '".$cid[0]."'";
		
		// build query 's where
		$where 		= ( count( $where ) ? ' WHERE ' . implode( ' AND ', $where ) : '' );		
		
		// get list of horse
		$sql = "SELECT * "
		."\n FROM `#__yj_contactus_forms` "
		."\n {$where}";
		$db->setQuery($sql);
		$rows = $db->loadObjectList();

		return $rows[0];
	}
	
	function publish($state) {
		$cid = JRequest::getVar( 'cid', array(), '', 'array' );
		JArrayHelper::toInteger($cid, array());

		if (count($cid) > 0) {
			$row =& $this->getTable();
			foreach ($cid as $id) {
				$row->load($id);
				$row->published = $state;
				$row->store();

				if (!$row->check()) {
					$this->setError($row->getError());
					return false;
				}
				if (!$row->store()) {
					$this->setError($row->getError());
					return false;
				}
			}
		}
		return true;
	}
	
	function delete() {
		$cid = JRequest::getVar( 'cid', array(), '', 'array' );
		JArrayHelper::toInteger($cid, array());
		
		if (count($cid) > 0) {
			$row =& $this->getTable();
			foreach ($cid as $id) {
				$row->delete($id);
			}
			$msg = JText::_("FORM SUCC DELETED");
		} else {
			JError::raiseError(500, JText::_( 'SELECT FORM DELETE', true ));
		}
		return $msg;
	}
	
	function order(){
		global $mainframe;
		$db 	=& JFactory::getDBO();

		$cid	= JRequest::getVar( 'cid', array(), 'post', 'array' );
		//generate move up or dowm
		$task = JRequest::getCmd('task');
		if ($task == 'orderup') {
			$direction = -1;
		} elseif ($task == 'orderdown') {
			$direction = +1;
		}else{
			$direction = $ordering;
		}
		
		if (isset( $cid[0] ))
		{
			$row = & $this->getTable();
			$row->load( (int) $cid[0] );
			$row->move($direction);

			$cache = & JFactory::getCache('com_yjcontactus');
			$cache->clean();
		}		

		$msg = JText::_('ORDERING SAVED');

		return $msg;
	}
	
	function saveOrder()
	{
		global $mainframe;

		// Initialize variables
		$db			= & JFactory::getDBO();

		$cid		= JRequest::getVar( 'cid', array(0), 'post', 'array' );
		$order		= JRequest::getVar( 'order', array (0), 'post', 'array' );
		$total		= count($cid);
		$conditions	= array ();

		JArrayHelper::toInteger($cid, array(0));
		JArrayHelper::toInteger($order, array(0));

		// Instantiate an article table object
		$row = & $this->getTable();

		// Update the ordering for items in the cid array
		for ($i = 0; $i < $total; $i ++)
		{
			$row->load( (int) $cid[$i] );
			if ($row->ordering != $order[$i]) {
				$row->ordering = $order[$i];
				if (!$row->store()) {
					JError::raiseError( 500, $db->getErrorMsg() );
					return false;
				}
			}
		}

		$cache = & JFactory::getCache('com_yjcontactus');
		$cache->clean();

		$msg = JText::_('ORDERING SAVED');
		return $msg;
	}*/	
}
?>
