
<?php

/**
 * @package    	com_vm_soa (WebServices for virtuemart)
 * @author		Mickael Cabanas (cabanas.mickael|at|gmail.com)
 * @link 		http://www.virtuemart-datamanager.com
 * @license    	GNU/GPL
*/

//->trace_it(__FILE__." début", 1, 1);
$task=JRequest::getVar('task');
$act=JRequest::getVar('act');
$option=JRequest::getVar('option');
$conf=JRequest::getVar('conf');
//var_dump($conf);
	//$classname = "com_soa.cfg";
		/** Read current Configuration ***/
		//require_once("/".$this->classname.".php");

		//echo "<center>";
		
		echo "<h3>";
		echo JText::_('COM_VM_SOA_ADMIN_IN_VM');
		echo "<a href='./index.php?option=com_virtuemart&view=config' > here </a>";
		echo "<h3>";
		
		/*echo "";
		echo "<a href=http://sourceforge.net/donate/index.php?group_id=300807><img src=http://images.sourceforge.net/images/project-support.jpg width=88 height=32 border=0 alt=Support This Project /> </a>";
		echo "<br />";echo "<br />";
		echo "<form action='index.php' method='post' name='adminForm' id='adminForm' class='adminForm'>";
		echo "<input type='hidden' name='task' value='".$task."'>";
		echo "<input type='hidden' name='act' value='".$act."'>";
		echo "<input type='hidden' name='option' value='".$option."'>";
		//get old values
		echo "<input type='hidden' name='conf[wsdl_product]' value='".$conf['wsdl_product']."'>";
		echo "<input type='hidden' name='conf[EP_product]' value='".$conf['EP_product']."'>";
		echo "<input type='hidden' name='conf[product_actif]' value='".$conf['product_actif']."'>";
		echo "<input type='hidden' name='conf[wsdl_users]' value='".$conf['wsdl_users']."'>";
		echo "<input type='hidden' name='conf[EP_users]' value='".$conf['EP_users']."'>";
		echo "<input type='hidden' name='conf[users_actif]' value='".$conf['users_actif']."'>";
		echo "<input type='hidden' name='conf[wsdl_cat]' value='".$conf['wsdl_cat']."'>";
		echo "<input type='hidden' name='conf[EP_cat]' value='".$conf['EP_cat']."'>";
		echo "<input type='hidden' name='conf[cat_actif]' value='".$conf['cat_actif']."'>";
		echo "<input type='hidden' name='conf[wsdl_order]' value='".$conf['wsdl_order']."'>";
		echo "<input type='hidden' name='conf[EP_order]' value='".$conf['EP_order']."'>";
		echo "<input type='hidden' name='conf[order_actif]' value='".$conf['order_actif']."'>";
		echo "<input type='hidden' name='conf[wsdl_sql]' value='".$conf['wsdl_sql']."'>";
		echo "<input type='hidden' name='conf[EP_sql]' value='".$conf['EP_sql']."'>";
		echo "<input type='hidden' name='conf[querie_actif]' value='".$conf['querie_actif']."'>";
		echo "<input type='hidden' name='conf[trace]' value='".$conf['trace']."'>";
		echo "<input type='hidden' name='conf[remove]' value='".$conf['remove']."'>";
		echo "<input type='hidden' name='conf[product_cache]' value='".$conf['product_cache']."'>";
		echo "<input type='hidden' name='conf[cat_cache]' value='".$conf['cat_cache']."'>";
		echo "<input type='hidden' name='conf[order_cache]' value='".$conf['order_cache']."'>";
		echo "<input type='hidden' name='conf[querie_cache]' value='".$conf['querie_cache']."'>";					
		echo "<input type='hidden' name='conf[users_cache]' value='".$conf['users_cache']."'>";
		echo "<input type='hidden' name='conf[auth_cat_getall]' value='".$conf['auth_cat_getall']."'>";
		echo "<input type='hidden' name='conf[auth_cat_getchild]' value='".$conf['auth_cat_getchild']."'>";
		echo "<input type='hidden' name='conf[auth_cat_addcat]' value='".$conf['auth_cat_addcat']."'>";
		echo "<input type='hidden' name='conf[auth_cat_delcat]' value='".$conf['auth_cat_delcat']."'>";		
		echo "<input type='hidden' name='conf[auth_cat_getimg]' value='".$conf['auth_cat_getimg']."'>";	
		echo "<input type='hidden' name='conf[auth_cat_updatecat]' value='".$conf['auth_cat_updatecat']."'>";	
		echo "<input type='hidden' name='conf[auth_users_getall]' value='".$conf['auth_users_getall']."'>";
		echo "<input type='hidden' name='conf[auth_users_adduser]' value='".$conf['auth_users_adduser']."'>";
		echo "<input type='hidden' name='conf[auth_users_deluser]' value='".$conf['auth_users_deluser']."'>";
		echo "<input type='hidden' name='conf[auth_users_sendmail]' value='".$conf['auth_users_sendmail']."'>";	
		echo "<input type='hidden' name='conf[auth_users_search]' value='".$conf['auth_users_search']."'>";	
		echo "<input type='hidden' name='conf[auth_users_getcountry]' value='".$conf['auth_users_getcountry']."'>";	
		echo "<input type='hidden' name='conf[auth_users_getauthgrp]' value='".$conf['auth_users_getauthgrp']."'>";	
		echo "<input type='hidden' name='conf[auth_users_addautgrp]' value='".$conf['auth_users_addautgrp']."'>";	
		echo "<input type='hidden' name='conf[auth_users_delauthgrp]' value='".$conf['auth_users_delauthgrp']."'>";	
		echo "<input type='hidden' name='conf[auth_users_getstate]' value='".$conf['auth_users_getstate']."'>";	
		echo "<input type='hidden' name='conf[auth_users_addstate]' value='".$conf['auth_users_addstate']."'>";	
		echo "<input type='hidden' name='conf[auth_users_delstate]' value='".$conf['auth_users_delstate']."'>";	
		echo "<input type='hidden' name='conf[auth_users_getshopgrp]' value='".$conf['auth_users_getshopgrp']."'>";	
		echo "<input type='hidden' name='conf[auth_users_addshopgrp]' value='".$conf['auth_users_addshopgrp']."'>";	
		echo "<input type='hidden' name='conf[auth_users_upshopgrp]' value='".$conf['auth_users_upshopgrp']."'>";	
		echo "<input type='hidden' name='conf[auth_users_delshopgroup]' value='".$conf['auth_users_delshopgroup']."'>";	
		echo "<input type='hidden' name='conf[auth_users_upuser]' value='".$conf['auth_users_upuser']."'>";	
		echo "<input type='hidden' name='conf[auth_users_getvendor]' value='".$conf['auth_users_getvendor']."'>";	
		echo "<input type='hidden' name='conf[auth_users_addvendor]' value='".$conf['auth_users_addvendor']."'>";	
		echo "<input type='hidden' name='conf[auth_users_upvendor]' value='".$conf['auth_users_upvendor']."'>";	
		echo "<input type='hidden' name='conf[auth_users_delvendor]' value='".$conf['auth_users_delvendor']."'>";	
		echo "<input type='hidden' name='conf[auth_users_getvendorcat]' value='".$conf['auth_users_getvendorcat']."'>";	
		echo "<input type='hidden' name='conf[auth_users_addvendorcat]' value='".$conf['auth_users_addvendorcat']."'>";	
		echo "<input type='hidden' name='conf[auth_users_upvendorcat]' value='".$conf['auth_users_upvendorcat']."'>";	
		echo "<input type='hidden' name='conf[auth_users_delvendorcat]' value='".$conf['auth_users_delvendorcat']."'>";	
		echo "<input type='hidden' name='conf[auth_users_getmanufacturer]' value='".$conf['auth_users_getmanufacturer']."'>";	
		echo "<input type='hidden' name='conf[auth_users_addmanufacturer]' value='".$conf['auth_users_addmanufacturer']."'>";	
		echo "<input type='hidden' name='conf[auth_users_upmanufacturer]' value='".$conf['auth_users_upmanufacturer']."'>";	
		echo "<input type='hidden' name='conf[auth_users_delmanufacturer]' value='".$conf['auth_users_delmanufacturer']."'>";	
		echo "<input type='hidden' name='conf[auth_users_getmanufacturercat]' value='".$conf['auth_users_getmanufacturercat']."'>";	
		echo "<input type='hidden' name='conf[auth_users_addmanufacturercat]' value='".$conf['auth_users_addmanufacturercat']."'>";	
		echo "<input type='hidden' name='conf[auth_users_upmanufacturercat]' value='".$conf['auth_users_upmanufacturercat']."'>";	
		echo "<input type='hidden' name='conf[auth_users_delmanufacturercat]' value='".$conf['auth_users_delmanufacturercat']."'>";	
		echo "<input type='hidden' name='conf[auth_users_getvendorimg]' value='".$conf['auth_users_getvendorimg']."'>";	
		echo "<input type='hidden' name='conf[auth_users_getversion]' value='".$conf['auth_users_getversion']."'>";	
		echo "<input type='hidden' name='conf[auth_prod_getfromcat]' value='".$conf['auth_prod_getfromcat']."'>";
		echo "<input type='hidden' name='conf[auth_prod_getchild]' value='".$conf['auth_prod_getchild']."'>";
		echo "<input type='hidden' name='conf[auth_prod_getfromid]' value='".$conf['auth_prod_getfromid']."'>";
		echo "<input type='hidden' name='conf[auth_prod_updateprod]' value='".$conf['auth_prod_updateprod']."'>";	
		echo "<input type='hidden' name='conf[auth_prod_getfromoderid]' value='".$conf['auth_prod_getfromoderid']."'>";
		echo "<input type='hidden' name='conf[auth_prod_addprod]' value='".$conf['auth_prod_addprod']."'>";
		echo "<input type='hidden' name='conf[auth_prod_delprod]' value='".$conf['auth_prod_delprod']."'>";		
		echo "<input type='hidden' name='conf[auth_prod_getcurrency]' value='".$conf['auth_prod_getcurrency']."'>";
		echo "<input type='hidden' name='conf[auth_prod_gettax]' value='".$conf['auth_prod_gettax']."'>";
		echo "<input type='hidden' name='conf[auth_prod_addtax]' value='".$conf['auth_prod_addtax']."'>";
		echo "<input type='hidden' name='conf[auth_prod_updatetax]' value='".$conf['auth_prod_updatetax']."'>";
		echo "<input type='hidden' name='conf[auth_prod_deltax]' value='".$conf['auth_prod_deltax']."'>";
		echo "<input type='hidden' name='conf[auth_prod_getallprod]' value='".$conf['auth_prod_getallprod']."'>";
		echo "<input type='hidden' name='conf[auth_prod_getimg]' value='".$conf['auth_prod_getimg']."'>";
		echo "<input type='hidden' name='conf[auth_order_getfromstatus]' value='".$conf['auth_order_getfromstatus']."'>";
		echo "<input type='hidden' name='conf[auth_order_getorder]' value='".$conf['auth_order_getorder']."'>";		
		echo "<input type='hidden' name='conf[auth_order_getstatus]' value='".$conf['auth_order_getstatus']."'>";
		echo "<input type='hidden' name='conf[auth_order_getall]' value='".$conf['auth_order_getall']."'>";
		echo "<input type='hidden' name='conf[auth_order_updatestatus]' value='".$conf['auth_order_updatestatus']."'>";
		echo "<input type='hidden' name='conf[auth_order_deleteorder]' value='".$conf['auth_order_deleteorder']."'>";
		echo "<input type='hidden' name='conf[auth_order_createorder]' value='".$conf['auth_order_createorder']."'>";
		echo "<input type='hidden' name='conf[auth_order_getcoupon]' value='".$conf['auth_order_getcoupon']."'>";	
		echo "<input type='hidden' name='conf[auth_order_addcoupon]' value='".$conf['auth_order_addcoupon']."'>";	
		echo "<input type='hidden' name='conf[auth_order_delcoupon]' value='".$conf['auth_order_delcoupon']."'>";	
		echo "<input type='hidden' name='conf[auth_order_getshiprate]' value='".$conf['auth_order_getshiprate']."'>";	
		echo "<input type='hidden' name='conf[auth_order_addshiprate]' value='".$conf['auth_order_addshiprate']."'>";	
		echo "<input type='hidden' name='conf[auth_order_delshiprate]' value='".$conf['auth_order_delshiprate']."'>";	
		echo "<input type='hidden' name='conf[auth_order_getshipcarrier]' value='".$conf['auth_order_getshipcarrier']."'>";	
		echo "<input type='hidden' name='conf[auth_order_addshipcarrier]' value='".$conf['auth_order_addshipcarrier']."'>";	
		echo "<input type='hidden' name='conf[auth_order_delshipcarrier]' value='".$conf['auth_order_delshipcarrier']."'>";	
		echo "<input type='hidden' name='conf[auth_order_getpayment]' value='".$conf['auth_order_getpayment']."'>";	
		echo "<input type='hidden' name='conf[auth_order_addpayment]' value='".$conf['auth_order_addpayment']."'>";	
		echo "<input type='hidden' name='conf[auth_order_updatepayment]' value='".$conf['auth_order_updatepayment']."'>";	
		echo "<input type='hidden' name='conf[auth_order_delapyment]' value='".$conf['auth_order_delapyment']."'>";	
		echo "<input type='hidden' name='conf[auth_order_getorderfromdate]' value='".$conf['auth_order_getorderfromdate']."'>";	
		echo "<input type='hidden' name='conf[auth_order_getcreditcard]' value='".$conf['auth_order_getcreditcard']."'>";	
		echo "<input type='hidden' name='conf[auth_order_addcreditcard]' value='".$conf['auth_order_addcreditcard']."'>";	
		echo "<input type='hidden' name='conf[auth_order_upcreditcard]' value='".$conf['auth_order_upcreditcard']."'>";	
		echo "<input type='hidden' name='conf[auth_order_delcreditcard]' value='".$conf['auth_order_delcreditcard']."'>";	
		echo "<input type='hidden' name='conf[auth_order_addstatus]' value='".$conf['auth_order_addstatus']."'>";	
		echo "<input type='hidden' name='conf[auth_order_upstatus]' value='".$conf['auth_order_upstatus']."'>";	
		echo "<input type='hidden' name='conf[auth_order_delstatus]' value='".$conf['auth_order_delstatus']."'>";			
		echo "<input type='hidden' name='conf[auth_sql_sqlrqst]' value='".$conf['auth_sql_sqlrqst']."'>";
		echo "<input type='hidden' name='conf[auth_sql_select]' value='".$conf['auth_sql_select']."'>";
		echo "<input type='hidden' name='conf[auth_sql_insert]' value='".$conf['auth_sql_insert']."'>";
		echo "<input type='hidden' name='conf[auth_sql_update]' value='".$conf['auth_sql_update']."'>";	
		echo "<input type='hidden' name='conf[auth_order_upshiprate]' value='".$conf['auth_order_upshiprate']."'>";	
		echo "<input type='hidden' name='conf[auth_order_upshipcarrier]' value='".$conf['auth_order_upshipcarrier']."'>";
		
		echo JText::_('COM_VM_SOA_CONFIG_FORM_CONF_INV');
		echo "<br />";echo "<br />";
		echo "<br />";echo "<br />";
		echo "<strong>";
		echo JText::_('COM_VM_SOA_CONFIG_FORM_CONF_WARN_NO_NEED');
		echo "</strong>";
		echo "<br />";echo "<br />";
		echo "<table>";
		
		
		
		echo "<tr>";
			echo "<td>";
			echo JText::_('COM_VM_SOA_CONFIG_FORM_CONF_ACC');
			echo "</td>";
			echo "<td>";
			echo "<input type='text' name='conf[URL]' value='".$conf['URL']."'>";
			echo "</td>";
			echo "<td>";
			echo JHTML::_('tooltip', JText::_( 'COM_VM_SOA_CONFIG_FORM_CONF_ACC_TT' ) );
			echo "</td>";
		echo "</tr>";
		echo "<tr>";
			echo "<td>";
			echo JText::_('COM_VM_SOA_CONFIG_FORM_CONF_BASE_ACC');
			echo "</td>";
			echo "<td>";
			echo "<input type='text' name='conf[BASESITE]' value='".$conf['BASESITE']."'>";
			echo "</td>";
			echo "<td>";
			echo JHTML::_('tooltip', JText::_( 'COM_VM_SOA_CONFIG_FORM_CONF_BASE_ACC_TT' ) );
			echo "</td>";
		echo "</tr>";
		echo "<tr>";
			echo "<td>";
			echo JText::_('COM_VM_SOA_CONFIG_FORM_SOAP_VERSION');
			echo "</td>";
			echo "<td>";
			echo "<select name='conf[soap_version]'>";
			$selected1="selected";$selected2="";if ($conf['soap_version']=="SOAP_1_2"){$selected1="";$selected2="selected";}
			echo "<option $selected1>SOAP_1_1</option>";
			echo "<option $selected2>SOAP_1_2</option>";
			echo "</select>";
			echo "</td>";
			echo "<td>";
			echo JHTML::_('tooltip', JText::_( 'COM_VM_SOA_CONFIG_FORM_SOAP_VERSION_TT' ) );
			echo "</td>";
		echo "</tr>";	
		
		echo "</tr>";
			echo "<tr>";
			echo "<td>";
			echo JText::_('COM_VM_SOA_CONFIG_FORM_ALLOW_UPLOAD_SERVICE');
			echo "</td>";
			echo "<td>";
			echo "<input type='checkbox' name='conf[auth_all_upload]'";
			if ($conf['auth_all_upload']=="on") {
				echo 'checked >';
				} else {
				echo " >";}
			echo "</td>";
			echo "<td>";
			echo JHTML::_('tooltip', JText::_( 'COM_VM_SOA_CONFIG_FORM_ALLOW_UPLOAD_SERVICE_TT' ) );
			echo "</td>";
		echo "</tr>";
		
		echo "<tr>";
			echo "<td>";
			echo JText::_('COM_VM_SOA_CONFIG_FORM_CONF_SQL_TRACE_LVL');
			echo "</td>";
			echo "<td>";
			echo "<select name='conf[trace]'>";
			echo "<option>0</option>";
			echo "<option>1</option>";
			echo "<option>2</option>";
			echo "<option>3</option>";
			echo "<option>4</option>";
			echo "<option>5</option>";
			echo "<option>6</option>";
			echo "<option>7</option>";
			echo "<option>8</option>";
			echo "<option>9</option>";
			echo "<option>10</option>";
			echo "</select>";
			echo "</td>";
			echo "<td>";
			echo JHTML::_('tooltip', JText::_( 'COM_VM_SOA_CONFIG_FORM_CONF_SQL_TRACE_LVL_TT' ) );
			echo "</td>";
		echo "</tr>";
		echo "<tr>";
			echo "<td>";
			echo JText::_('COM_VM_SOA_CONFIG_FORM_CONF_SQL_RAZ_T');
			echo "</td>";
			echo "<td>";
			echo "<input type='checkbox' name='conf[trace]'";
			if ($conf['trace']=="on") {
				echo 'checked >';
				} else {
				echo " >";}
			echo "</td>";
			echo "<td>";
			echo JHTML::_('tooltip', JText::_( 'COM_VM_SOA_CONFIG_FORM_CONF_SQL_RAZ_TT' ) );
			echo "</td>";
		echo "</tr>";
		echo "<tr>";
			echo "<td>";
			echo JText::_('COM_VM_SOA_CONFIG_FORM_CONF_FC');
			echo "</td>";
			echo "<td>";
			echo "<input type='text' name='conf[remove]' value='".$conf['remove']."'>";
			echo "</td>";
			echo "<td>";
			echo JHTML::_('tooltip', JText::_( 'COM_VM_SOA_CONFIG_FORM_CONF_FC_TT' ) );
			echo "</td>";
		echo "</tr>";
		echo "</table>";
		echo "</form>";*/
		//echo "</center>";
//->trace_it(__FILE__." fin", 1, -1);
?>
