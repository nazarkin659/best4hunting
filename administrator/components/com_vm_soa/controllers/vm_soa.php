<?php
/**
 * @package    	com_vm_soa (WebServices for virtuemart)
 * @author		Mickael Cabanas (cabanas.mickael|at|gmail.com)
 * @link 		http://www.virtuemart-datamanager.com
 * @license    	GNU/GPL
*/

/**
 * Controleur controleur secondaire
 */
defined('_JEXEC') or die();
class vm_soaControllervm_soa extends vm_soaController{
	
	function vm_soa() {
		//->trace_it(__FILE__." vm_soa d�but", 1, 1);
	  	parent::display();
		//->trace_it(__FILE__." vm_soa fin", 1, -1);
	}
	
	function commandes() {
		//->trace_it(__FILE__." commandes d�but", 1, 1);
		
		$this->read_config();
		$model = $this->getModel('vm_soa');
		switch(JRequest::getVar('task', ''))
		{
			case 'apply':
				$model->commandesApply();
			case 'getLicen':
				$model->isLicenceValid();
			break;
			default:
				$model->commandesList();
			break;
		}
	  parent::display();
		//->trace_it(__FILE__." commandes fin", 1, -1);
	}
	
	function clients() {
		//->trace_it(__FILE__." clients d�but", 1, 1);
		$this->read_config();
		$model = $this->getModel('vm_soa');
		switch(JRequest::getVar('task', ''))
		{
			case 'apply':
				$model->clientsApply();
			break;
			default:
				$model->clientsList();
			break;
		}
	  parent::display();
		//->trace_it(__FILE__." clients fin", 1, -1);
	}
	
	

}
?>