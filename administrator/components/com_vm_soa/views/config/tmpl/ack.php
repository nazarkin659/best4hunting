<?php
/**
 * @package    	com_vm_soa (WebServices for virtuemart)
 * @author		Mickael Cabanas (cabanas.mickael|at|gmail.com)
 * @link 		http://www.virtuemart-datamanager.com
 * @license    	GNU/GPL
*/
//JRequest::getVar('trace')->trace_it(__FILE__." début", 1, 1);
echo "<center>";
echo JText::_('COM_VM_SOA_CONFIG_ACK');
echo "<br />";echo "<br />";
		echo "<a href=http://sourceforge.net/donate/index.php?group_id=300807><img src=http://images.sourceforge.net/images/project-support.jpg width=88 height=32 border=0 alt=Support This Project /> </a>";
		echo "<br />";echo "<br />";
echo "</center>";
 echo "<ul>";
 echo "<li><a href=./components/com_vm_soa/services/VM_CategoriesWSDL.php > View Categories WSDL definition </a></li>";
 echo "<li><a href=./components/com_vm_soa/services/VM_ProductWSDL.php > View Product WSDL definition</a></li>";
 echo "<li><a href=./components/com_vm_soa/services/VM_OrderWSDL.php > View Orders WSDL definition</a></li>";
 echo "<li><a href=./components/com_vm_soa/services/VM_UsersWSDL.php > View Customers WSDL definition</a></li>";
 echo "<li><a href=./components/com_vm_soa/services/VM_SQLQueriesWSDL.php > View SQL WSDL definition</a></li>";
 echo "</ul>";
 
 echo "<ul>";
 echo "<li><a href=http://www.virtuemart-datamanager.com/ >WebSite</a></li>";
 echo "<li><a href=http://www.virtuemart-datamanager.com/index.php?option=com_kunena&Itemid=43&func=listcat >Forum</a></li>";
 echo "<li><a href=http://www.virtuemart-datamanager.com/index.php?option=com_content&view=article&id=60&Itemid=87 >Documentation</a></li>";
 echo "<li><a href=http://www.virtuemart-datamanager.com/index.php?option=com_wrapper&view=wrapper&Itemid=81 >Source Forge</a></li>";
 echo "<li><a href=http://www.virtuemart-datamanager.com/index.php?option=com_content&view=article&id=57&Itemid=68 >VDM Client</a></li>";
 echo "</ul>";

//JRequest::getVar('trace')->trace_it(__FILE__." fin", 1, -1);
?>
