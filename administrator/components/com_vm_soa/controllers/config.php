<?php

/**
 * @package    	com_vm_soa (WebServices for virtuemart)
 * @author		Mickael Cabanas (cabanas.mickael|at|gmail.com)
 * @link 		http://www.virtuemart-datamanager.com
 * @license    	GNU/GPL
*/


class vm_soaControllerconfig extends vm_soaController{
	
function config() {
	//->trace_it(__FILE__." config d�but", 1, 1);

	// On appelle le mod�le pour lire la configuration
	$model = $this->getModel('vm_soa');
	
	switch(JRequest::getVar('task', ''))
	{
	
		case 'save':
			var_dump('save');die;
			echo 'on save';
			$conf = $model->save_conf();
			JRequest::setVar( 'layout', 'ack'  );
		// On selectionne la vue config
			JRequest::setVar( 'view', 'config' );
		$this->display();
		case 'cancel':
		case 'renewConfig':
			var_dump("renewConfig");die;
		break;
		default:
		$conf = $model->read_conf();
	//$this->assignRef( 'conf', $conf );
	JRequest::setVar( 'conf', $conf );
	// On appelle la vue en demandant d'afficher le formulaire
		JRequest::setVar( 'layout', 'form_conf'  );
		JRequest::setVar('hidemainmenu', 1);
	// On selectionne la vue config
		JRequest::setVar( 'view', 'config' );
		$this->display();
		break;
	}
  //parent::display();
	//->trace_it(__FILE__." config fin", 1, -1);
}

	
}
?>