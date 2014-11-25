<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

jimport('joomla.application.component.controller');

/**
 * Component Controller
 *
 * @package	
 */
class YjContactUSController extends JController
{

	/**
	 * constructor (registers additional tasks to methods)
	 * @return void
	 */
	function __construct(){
		parent::__construct();

		// Register Extra tasks
		$this->registerTask( 'send'	, 'send' );
	}


	/**
	 * Method to display the home page
	 *
	 * @access	public
	 */
	function display()
	{
		parent::display();
	}
	
	function send(){
	
		JRequest::setVar( 'view', 'YjContactUS' );
		JRequest::setVar( 'layout', 'default'  );
		$model = $this->getModel('YjContactUS');
		$model->send_email();
		
		parent::display();	
	}
}
?>