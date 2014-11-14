<?php
/**
 * Departments Controller for YjContactUS Component
 * 
 * @package    
 * @subpackage Components
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

jimport('joomla.application.component.controlleradmin');

/**
 * Departments Controller
 */
class YjContactUSControllerYjDepartments extends JControllerAdmin
{
	/**
	 * Constructor.
	 *
	 * @param	array	$config	An optional associative array of configuration settings.

	 * @return	ContentControllerArticles
	 * @see		JController
	 * @since	1.6
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);
	}
	
	/**
	 * Proxy for getModel.
	 *
	 * @param	string	$name	The name of the model.
	 * @param	string	$prefix	The prefix for the PHP class name.
	 *
	 * @return	JModel
	 * @since	1.6
	 */
	public function getModel($name = 'YJDepartment', $prefix = 'YJContactusModel', $config = array('ignore_request' => true))
	{

		$model = parent::getModel($name, $prefix, $config);

		return $model;
	}
	
	
//////////////////////////////////////////////////////////JOOMLA 1.5 FUNCTIONS	
	
/*	function __construct()
	{
		parent::__construct();
		// Register Extra tasks
		$this->registerTask( 'apply','save' );
		$this->registerTask( 'unpublish', 'publish' );
		$this->registerTask( 'orderdown', 'order' );
		$this->registerTask( 'orderup', 'order' );		
	}

	function display(){
		JRequest::setVar( 'view', 'yjdepartments' );
		JRequest::setVar( 'layout', 'default'  );
		//JRequest::setVar('hidemainmenu', 1);

		parent::display();
	}
	
	function add(){
		$view 	=& $this->getView( 'yjdepartments', '.html' );
		$view->add();
	}
	
	function save(){
		$model =& $this->getModel('yjdepartments');
		$msg = $model->save();
		
		if(JRequest::getVar('task') == 'apply'){
			$db	=& JFactory::getDBO();
			if($db->insertId()){
				$cid = $db->insertId();
			}else{
				$cid = JRequest::getVar( 'id' );			
			}
			$this->setRedirect('index.php?option=com_yjcontactus&controller=yjdepartments&task=edit&cid[]='.$cid);
		}else{
			$this->setRedirect('index.php?option=com_yjcontactus&controller=yjdepartments&view=yjdepartments',$msg);
		}
	}
	
	function remove(){
		$model =& $this->getModel('yjdepartments');
		$msg = $model->delete();
		
		$this->setRedirect('index.php?option=com_yjcontactus&controller=yjdepartments&view=yjdepartments',$msg);
	}	
	
	function cancel() {
		$this->setRedirect('index.php?option=com_yjcontactus&controller=yjdepartments&view=yjdepartments');
	}	
	
	function edit() {
		$model 	=& $this->getModel( 'yjdepartments' );
		$view 	=& $this->getView( 'yjdepartments', '.html' );
		$view->setModel( $model, true );
		$view->edit();
	}
	
	function publish() {
		$model =& $this->getModel( 'yjdepartments' );
		$task = JRequest::getCmd('task');
		if ($task == 'publish') {
			$model->publish(1);
		} elseif ($task == 'unpublish') {
			$model->publish(0);
		} 
		
		$this->setRedirect('index.php?option=com_yjcontactus&controller=yjdepartments&view=yjdepartments');
	}	
	
	function order(){
		global $mainframe;
		$model =& $this->getModel('yjdepartments');
		$msg = $model->order();		
		
		$limit		= $mainframe->getUserStateFromRequest('global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
		$limitstart	= $mainframe->getUserStateFromRequest('limitstart', 'limitstart', 0, 'int');

		$link = 'index.php?option=com_yjcontactus&controller=yjdepartments&view=yjdepartments&limitstart='.$limitstart.'&limit='.$limit;
		$this->setRedirect($link, $msg);			
		
		return true;		
	}
	
	function saveorder(){
		global $mainframe;
		$model =& $this->getModel('yjdepartments');
		$msg = $model->saveorder();		
		
		$limit		= $mainframe->getUserStateFromRequest('global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
		$limitstart	= $mainframe->getUserStateFromRequest('limitstart', 'limitstart', 0, 'int');

		$link = 'index.php?option=com_yjcontactus&controller=yjdepartments&view=yjdepartments&limitstart='.$limitstart.'&limit='.$limit;
		$this->setRedirect($link, $msg);			
		
		return true;		
	}*/
	
}
?>
