<?php
/**
 *
 * Description
 *
 * @package	VirtueMart
 * @subpackage Config
 * @author RickG
 * @link http://www.virtuemart.net
 * @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * @version $Id: default.php 3909 2011-08-19 09:03:53Z Milbo $
 */

// Check to ensure this file is included in Joomla!
defined ( '_JEXEC' ) or die ( 'Restricted access' );

?>
<form action="index.php" method="post" name="adminForm" id="adminForm">

<?php // Loading Templates in Tabs

if (isSupEqualVmVersion("2.0.7")){

	AdminUIHelper::buildTabs ( $this,array (	'soap' 			=> 	'COM_VIRTUEMART_ADMIN_CFG_SOAP'/* ,
									'soap2' 			=> 	'COM_VIRTUEMART_ADMIN_CFG_SOAP'*/ )
																					 );
}else{
	/*for older version*/
	AdminUIHelper::buildTabs ( array (	'soap' 			=> 	'COM_VIRTUEMART_ADMIN_CFG_SOAP'/* ,
									'soap2' 			=> 	'COM_VIRTUEMART_ADMIN_CFG_SOAP'*/ )
																					 );
}


?>

<!-- Hidden Fields --> 
<input type="hidden" name="task" value="save" /> 
<input type="hidden" name="option" value="com_vm_soa" />
<input type="hidden" name="view" value=config />
	
<?php
echo JHTML::_ ( 'form.token' );
?>
</form>
<?php

/**
	* check if verison if sup or equal
	*/
	function isSupEqualVmVersion($version){
	
		$VMVERSION = new vmVersion();
		$numericCurrentVerion =  intval(str_replace('.','',vmVersion::$RELEASE));
		$numericTestedVerion =  intval(str_replace('.','',$version));
		//var_dump($numericCurrentVerion);die;
		if ($numericCurrentVerion >= $numericTestedVerion ){
			return true;
		} else {
			return false;
		}
	}


?>