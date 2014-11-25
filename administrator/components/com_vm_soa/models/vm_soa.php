<?php
/**
 * @package    	com_vm_soa (WebServices for virtuemart)
 * @author		Mickael Cabanas (cabanas.mickael|at|gmail.com)
 * @link 		http://www.virtuemart-datamanager.com
 * @license    	GNU/GPL
*/
jimport( 'joomla.application.component.model' );
class vm_soaModelvm_soa extends JModel
{
	function save_conf()
	{

	$conf=JRequest::getVar('conf');
	print ('vm_soaModelvm_soa');

	}
	function read_conf()
	{

	}
	/**
	 * Cette fonction va envoyer les commandes VM s�lectionn�es vers 
	 */
	function commandesApply()
	{
		//->trace_it(__FILE__." commandesApply d�but", 1, 1);
		//->trace_it(__FILE__." commandesApply fin", 1, -1);
	}
	/**
	 * Cette fonction va lister les commandes Vm et 
	 */
	function commandesList()
	{
		//->trace_it(__FILE__." commandesList d�but", 1, 1);
		//->trace_it(__FILE__." commandesList fin", 1, -1);
	}
	/**
	 * Cette fonction va envoyer les produits VM s�lectionn�s vers 
	 */
	function produitsApply()
	{
	//	JRequest::getVar('trace')->trace_it(__FILE__." produitsApply d�but", 1, 1);
	//	JRequest::getVar('trace')->trace_it(__FILE__." produitsApply fin", 1, -1);
	}
	/**
	 * Cette fonction va envoyer les clients VM s�lectionn�s vers 
	 */
	function clientsApply()
	{
		//->trace_it(__FILE__." clientsApply d�but", 1, 1);
		//->trace_it(__FILE__." clientsApply fin", 1, -1);
	}
	/**
	 * Cette fonction va lister les clients Vm et 
	 */
	function clientsList()
	{
		//->trace_it(__FILE__." clientsList d�but", 1, 1);
		//->trace_it(__FILE__." clientsList fin", 1, -1);
	}
	

}
?>