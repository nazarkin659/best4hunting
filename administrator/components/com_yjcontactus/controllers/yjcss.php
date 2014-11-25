<?php
/**
 * CSS Controller for YjContactUS Component
 * 
 * @package    
 * @subpackage Components
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

/**
 * CSS Controller
 */
class YjContactUSControllerYjCSS extends JController{

	/**
	 * Class constructor.
	 *
	 * @param	array	$config	A named array of configuration variables.
	 *
	 * @return	JControllerForm
	 * @since	1.6
	 */
	function __construct($config = array()){
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
/*	public function getModel($name = 'YJCSS', $prefix = 'YJContactusModel', $config = array('ignore_request' => true)){

		$model = parent::getModel($name, $prefix, $config);

		return $model;
	}*/
	
	function save_css(){
		$model = $this->getModel('yjcss');

		if ($model->save_css()) {
			$msg = JText::_( 'COM_YJCONTACTUS_CSS_FILE_SAVED' );
		} else {
			$msg = JText::_( 'COM_YJCONTACTUS_CSS_FILE_NOT_SAVED' );
		}

		// Check the table in so it can be edited.... we are done with it anyway
		$this->setRedirect('index.php?option=com_yjcontactus&controller=yjcss&view=yjcss',$msg);
	}	
	
//////////////////////////////////////////////////////////JOOMLA 1.5 FUNCTIONS			

	/**
	 * display 
	 * @return void
	 */
/*	function display(){
		JRequest::setVar( 'view', 'yjcss' );
		JRequest::setVar( 'layout', 'default'  );
		//JRequest::setVar('hidemainmenu', 1);

		parent::display();
	}*/
}
?>
