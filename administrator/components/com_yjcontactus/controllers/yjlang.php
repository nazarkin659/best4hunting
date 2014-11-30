<?php
/**
 * Lang Controller for YjContactUS Component
 * 
 * @package    
 * @subpackage Components
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

/**
 * CSS Controller
 */
class YjContactUSControllerYjLang extends JController{

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
	 * save a record (and redirect to main page)
	 * @return void
	 */
	function save_admin_lang(){
		$model = $this->getModel('yjlang');

		if ($model->save_admin_lang()) {
			$msg = JText::_( 'COM_YJCONTACTUS_LANG_FILE_SAVED' );
		} else {
			$msg = JText::_( 'COM_YJCONTACTUS_LANG_FILE_NOT_SAVED' );
		}

		// Check the table in so it can be edited.... we are done with it anyway
		$this->setRedirect('index.php?option=com_yjcontactus&controller=yjlang&view=yjlang',$msg);
	}
	
	/**
	 * save a record (and redirect to main page)
	 * @return void
	 */
	function save_front_lang(){
		$model = $this->getModel('yjlang');

		if ($model->save_front_lang()) {
			$msg = JText::_( 'COM_YJCONTACTUS_LANG_FILE_SAVED' );
		} else {
			$msg = JText::_( 'COM_YJCONTACTUS_LANG_FILE_NOT_SAVED' );
		}

		// Check the table in so it can be edited.... we are done with it anyway
		$this->setRedirect('index.php?option=com_yjcontactus&controller=yjlang&view=yjlang',$msg);
	}
	
//////////////////////////////////////////////////////////JOOMLA 1.5 FUNCTIONS	
	
	/**
	 * display 
	 * @return void
	 */
/*	function display(){
		JRequest::setVar( 'view', 'yjlang' );
		JRequest::setVar( 'layout', 'default'  );
		//JRequest::setVar('hidemainmenu', 1);

		parent::display();
	}*/				
}
?>
