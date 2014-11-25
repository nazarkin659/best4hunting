<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();	

jimport( 'joomla.application.component.view');

/**
 * HTML View class for the YjContactUS Component
 *
 */

class YjContactUSViewYjContactUS extends JView
{
	/**
	 * Method to display the ContactUS page
	 *
	 * @access	public
	 */
	function display($tpl = null){
		
		$display_msg = $this->get('display');
		
		if($display_msg == 'published'){
			$append_session = $this->get('append_session');
			$this->assignRef('append_session', $append_session);
		
			$form_details = $this->get('form_details');
			$this->assignRef('form_details', $form_details);
		}else{
			$this->assignRef('display_msg', $display_msg);
		}
				
		parent::display($tpl);
	}
}
?>