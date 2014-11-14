<?php
/**
 * @package    	com_vm_soa (WebServices for virtuemart)
 * @author		Mickael Cabanas (cabanas.mickael|at|gmail.com)
 * @link 		http://www.virtuemart-datamanager.com
 * @license    	GNU/GPL
*/
 
// no direct access
 
defined( '_JEXEC' ) or die( 'Restricted access' );
 
jimport( 'joomla.application.component.view');
 
/**
 * HTML View class for the HelloWorld Component
 *
 * @package    HelloWorld
 */
 
class vm_soaViewvm_soa extends JView
{
    function display($tpl = null)
    {
        $greeting = "Welcome to SOA For Virtuemart component";
        $this->assignRef( 'greeting', $greeting );
		
 
        parent::display($tpl);
    }
}
