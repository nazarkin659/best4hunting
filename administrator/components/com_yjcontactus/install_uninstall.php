<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * Script file of YJMS component
 */
class com_yjcontactusInstallerScript
{
	/**
	 * method to install the component
	 *
	 * @return void
	 */
	function install($parent){
		$db 	= JFactory::getDBO(); 
		$query	= $db->getQuery(true);	

		$query->clear();
		$query->insert("#__yj_contactus_settings");
		$query->set("`id` = '1'");
		$query->set("`upload_folder` = 'yjcontactus'");
		$db->setQuery($query);
		$db->Query( $query );				
	
		//create upload folder to joomla /images folder
		$file_path = JPATH_ROOT.DS.'images'.DS.'yjcontactus'.DS;
		if( !is_dir($file_path) ) {
			mkdir( $file_path, 0755);
			fopen($file_path.'index.html', 'a');
		}
		//$parent->getParent()->setRedirectURL('index.php?option=com_yjms');
	}

	/**
	 * method to uninstall the component
	 *
	 * @return void
	 */
	function uninstall($parent){
		$db 	= JFactory::getDBO(); 
		$query	= $db->getQuery(true);	

		$query->clear();
		$query->select("params");
		$query->from("#__extensions");		
		$query->where("`name` = 'com_yjcontactus'");
		$query->where("`type` = 'component'");		
		$db->setQuery($query);
		$cparams = $db->loadResult();
		
		//$cparams = JComponentHelper::getParams('com_yjcontactus');
		
		if($cparams != ''){
			$params = new JRegistry;
			$params->loadJSON($cparams);
			
			//remove database
			if($params->get('remove_db',0) == 1){
				?>
				<script type="text/javascript">alert('YjContactUs database will be removed');</script>
				<?php	
				$sqls = array();
				
				$sqls[] = "DROP TABLE #__yj_contactus_departments;";
				$sqls[] = "DROP TABLE #__yj_contactus_forms;";
				$sqls[] = "DROP TABLE #__yj_contactus_settings;";
				$sqls[] = "DROP TABLE #__yj_contactus_captcha;";

				foreach( $sqls as $query ){
					$db->setQuery( $query );
					if( !$db->query() ){
						//echo $database->getErrorMsg();
						//exit;
					}
				}
			}
			
			if($params->get('remove_menu',0) == 1){
				?>
				<script type="text/javascript">alert('YjContactUs menu items will be removed');</script>
				<?php
				$query	= $db->getQuery(true);
				$query->clear();
				$query->delete("#__menu");
				$query->where("`link` = 'index.php?option=com_yjcontactus&view=yjcontactus'");
				$query->where("`type` = 'component'");
				$query->where("`client_id` = 0 ");
				$db->setQuery($query);
				$db->Query();
			}			
		}
		
		// $parent is the class calling this method
		echo '<p>' . JText::_('COM_YJCONTACTUS_UNINSTALL_TEXT') . '</p>';
	}

	/**
	 * method to update the component
	 *
	 * @return void
	 */
	function update($parent) 
	{
		// $parent is the class calling this method
		echo '<p>' . JText::_('COM_YJCONTACTUS_UPDATE_TEXT') . '</p>';
	}

	/**
	 * method to run before an install/update/uninstall method
	 *
	 * @return void
	 */
	function preflight($type, $parent) 
	{
		// $parent is the class calling this method
		// $type is the type of change (install, update or discover_install)
		echo '<p>' . JText::_('COM_YJCONTACTUS_PREFLIGHT_' . $type . '_TEXT') . '</p>';
	}

	/**
	 * method to run after an install/update/uninstall method
	 *
	 * @return void
	 */
	function postflight($type, $parent) 
	{
		// $parent is the class calling this method
		// $type is the type of change (install, update or discover_install)
		echo '<p>' . JText::_('COM_YJCONTACTUS_POSTFLIGHT_' . $type . '_TEXT') . '</p>';
	}
}
