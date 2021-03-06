<?php 
define( '_VALID_MOS', 1 );
define( '_JEXEC', 1 );

/**
 * Virtuemart Product SOA Connector
 *
 * THis file generate wsdl dynamicly whith good <soap:address location = ....
 *
 * @package    com_vm_soa
 * @subpackage classes
 * @author     Mickael cabanas (cabanas.mickael|at|gmail.com)
 * @copyright  2010 Mickael Cabanas
 * @license    http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version    $Id:$
 */

ob_start();//to prevent some bad users change codes 

 /** loading framework **/
include_once('VM_Commons.php');

/** WSDL file name to load**/
$wsdlFilename = $vmConfig->get('soap_wsdl_prod')!= "" ? $vmConfig->get('soap_wsdl_prod') : WSDL_PROD;

$string = file_get_contents($wsdlFilename,"r");
$wsdlReplace = $string;

//Get URL + BASE From Joomla conf
if (empty($confSoa['BASESITE']) && empty($confSoa['URL']) ){
	$_VERSION = new JVersion();
	$uri = JURI::base();
	$soa_uri = "components/com_vm_soa/services";
	$pos = strpos($uri,$soa_uri);
	if ($_VERSION->RELEASE == "1.5" || $pos === false ){
		$wsdlReplace = str_replace('http://___HOST___/___BASE___/administrator/', JURI::base(), $wsdlReplace);
	}else{
		$wsdlReplace = str_replace('http://___HOST___/___BASE___/administrator/components/com_vm_soa/services/',  JURI::base(), $wsdlReplace);
	}
}
// Else Get URL + BASE form SOA For VM Conf
else if (empty($confSoa['BASESITE']) && !empty($confSoa['URL'])){
	$wsdlReplace = str_replace("___HOST___", $confSoa['URL'], $string);
	$wsdlReplace = str_replace("___BASE___/", $confSoa['BASESITE'], $wsdlReplace);
} else {
	$wsdlReplace = str_replace("___HOST___", $confSoa['URL'], $string);
	$wsdlReplace = str_replace("___BASE___", $confSoa['BASESITE'], $wsdlReplace);
}

$serviceFilename = $vmConfig->get('soap_EP_prod')!= "" ? $vmConfig->get('soap_EP_prod') : SERVICE_PROD;
$wsdlReplace = str_replace("___SERVICE___", $serviceFilename, $wsdlReplace);

ob_end_clean();//to prevent some bad users change code 

/** echo WSDL **/
if ($vmConfig->get('soap_ws_prod_on')==1){
	header('Content-type: text/xml; charset=UTF-8'); 
	header("Content-Length: ".strlen($wsdlReplace));
	echo $wsdlReplace;
}
else{
	echoXmlMessageWSDisabled('Product');
}
?>