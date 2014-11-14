<?php
/**
 * YjContactUs default controller
 * 
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controlleradmin');

/**
 * @package		Joomla.Administrator
 * @subpackage	com_content
 * @since		1.6
 */
class YJContactusControllerYJForms extends JControllerAdmin
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
	public function getModel($name = 'YJForm', $prefix = 'YJContactusModel', $config = array('ignore_request' => true))
	{

		$model = parent::getModel($name, $prefix, $config);

		return $model;
	}	
}

//////////////////////////////////////////////////////////JOOMLA 1.5 FUNCTIONS

/**
 * General Controller of YJCONTACTUS component
 */
/*class YjContactUSController extends JController
{


	function __construct()
	{
		parent::__construct();
		// Register Extra tasks
		$this->registerTask( 'apply','save' );
		$this->registerTask( 'unpublish', 'publish' );
		$this->registerTask( 'orderdown', 'order' );
		$this->registerTask( 'orderup', 'order' );		
	}
	

	function add(){
		//set model and view
		$model =& $this->getModel('yjcontactus');
		$view 	=& $this->getView( 'yjcontactus', '.html' );
		$view->setModel( $model, true );		
		//function to display
		$view->add();
	}
	

	function save(){
		$model =& $this->getModel('yjcontactus');
		$msg = $model->save();
		
		if(JRequest::getVar('task') == 'apply'){
			$db	=& JFactory::getDBO();
			if($db->insertId()){
				$cid = $db->insertId();
			}else{
				$cid = JRequest::getVar( 'id' );			
			}
			$this->setRedirect('index.php?option=com_yjcontactus&task=edit&cid[]='.$cid);
		}else{
			$this->setRedirect('index.php?option=com_yjcontactus&view=yjcontactus',$msg);
		}
	}	
	

	function edit() {
		$model 	=& $this->getModel( 'yjcontactus' );
		$view 	=& $this->getView( 'yjcontactus', '.html' );
		$view->setModel( $model, true );
		$view->edit();
	}	
	

	function cancel() {
		$this->setRedirect('index.php?option=com_yjcontactus&view=yjcontactus');
	}	
	

	function publish() {
		$model =& $this->getModel( 'yjcontactus' );
		$task = JRequest::getCmd('task');
		if ($task == 'publish') {
			$model->publish(1);
		} elseif ($task == 'unpublish') {
			$model->publish(0);
		} 
		
		$this->setRedirect('index.php?option=com_yjcontactus&view=yjcontactus');
	}
	

	function remove(){
		$model =& $this->getModel('yjcontactus');
		$msg = $model->delete();
		
		$this->setRedirect('index.php?option=com_yjcontactus&view=yjcontactus',$msg);
	}
	

	function order(){
		global $mainframe;
		$model =& $this->getModel('yjcontactus');
		$msg = $model->order();		
		
		$limit		= $mainframe->getUserStateFromRequest('global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
		$limitstart	= $mainframe->getUserStateFromRequest('limitstart', 'limitstart', 0, 'int');

		$link = 'index.php?option=com_yjcontactus&view=yjcontactus&limitstart='.$limitstart.'&limit='.$limit;
		$this->setRedirect($link, $msg);			
		
		return true;		
	}
	

	function saveorder(){
		global $mainframe;
		$model =& $this->getModel('yjcontactus');
		$msg = $model->saveorder();		
		
		$limit		= $mainframe->getUserStateFromRequest('global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
		$limitstart	= $mainframe->getUserStateFromRequest('limitstart', 'limitstart', 0, 'int');

		$link = 'index.php?option=com_yjcontactus&view=yjcontactus&limitstart='.$limitstart.'&limit='.$limit;
		$this->setRedirect($link, $msg);			
		
		return true;		
	}		

}*/
?>