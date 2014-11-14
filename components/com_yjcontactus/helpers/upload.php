<?php
/*======================================================================*\
|| #################################################################### ||
|| # Youjoomla LLC - YJ- Licence Number 4719UB372
|| # Licensed to - alexs malov
|| # ---------------------------------------------------------------- # ||
|| # Copyright (C) 2006-2009 Youjoomla LLC. All Rights Reserved.        ||
|| # This file may not be redistributed in whole or significant part. # ||
|| # ---------------- THIS IS NOT FREE SOFTWARE ---------------- #      ||
|| # http://www.youjoomla.com | http://www.youjoomla.com/license.html # ||
|| #################################################################### ||
\*======================================================================*/
	// Set flag that this is a parent file
	if(!defined('_JEXEC')) define( '_JEXEC', 1 );
	define( 'DS', DIRECTORY_SEPARATOR );		
	define('JPATH_BASE', str_replace("components".DS."com_yjcontactus".DS."helpers","",dirname(__FILE__)) );
	require_once ( JPATH_BASE .DS.'includes'.DS.'defines.php' );
	require_once ( JPATH_BASE .DS.'includes'.DS.'framework.php' );		

	$mainframe =& JFactory::getApplication('site');
	//joomla requred files and variables
	//include("../../../administrator/includes/defines.php");
	//include("../../../libraries/joomla/utilities/compat/compat.php");
	//include("../../../libraries/joomla/utilities/string.php");		
	//include("../../../libraries/joomla/environment/uri.php");		
	//include("../../../libraries/joomla/plugin/helper.php");
	//include("../../../libraries/joomla/event/dispatcher.php");
	//include("../../../libraries/joomla/methods.php");
	//include("../../../administrator/components/com_search/helpers/search.php");	
	//include("../../../plugins/system/legacy/functions.php");
	//include("../../../plugins/system/legacy/dbtable.php");
	//include("../../../libraries/joomla/application/router.php");
	//include("../../../includes/router.php");

	$session = JFactory::getSession();

	/* add any other file types you need here */
	$allowed_files = array( 'application/zip',
                            'multipart/x-zip',
                            'application/x-compressed',
                            'application/x-zip-compressed',
                            'application/x-tar',
                            'application/x-tar-compressed',
                            'application/x-gzip',
                            'application/x-gzip-compressed'); 
	
	if( $_FILES['archive']['size'] > 0 && 
		is_uploaded_file($_FILES['archive']['tmp_name']) && 
		in_array($_FILES['archive']['type'],$allowed_files) )
	{
		$db = JFactory::getDBO(); 
		$query	= $db->getQuery(true);
		$query->clear();		
		// Get the total records
/*		$query = 'SELECT upload_folder' 
				.' FROM #__yj_contactus_settings';
		$db->setQuery($query,0, 1);
		$folder = $db->loadObjectList();*/
		$query->select('upload_folder');
		$query->from('#__yj_contactus_settings');
		//$query->where("`session_id_contactus` = '".$session->getId()."'");
		$db->setQuery($query);
		$folder = $db->loadObjectList();		
		
		if(move_uploaded_file($_FILES['archive']['tmp_name'], JPATH_ROOT.DS.'images/'.$folder[0]->upload_folder.'/'.$_FILES['archive']['name'])){

			//$table =& JTable::getInstance( 'YjCaptcha', 'YjCaptchaTable');
			include_once("../../../administrator/components/com_yjcontactus/tables/yjcaptcha.php");
			$table = new YjCaptchaTableYjCaptcha($db);
		
			$table->session_id_contactus = $session->getId();
			$table->attachement = $_FILES['archive']['name'];
			$table->store();
			
			//$session->set('file_attached',$_FILES['archive']['name']);
			//$_SESSION['file_attached'] = $_FILES['archive']['name'];
			$result = 1;
		}
	}	
	else $result = 0;	
?>
<script language="javascript" type="text/javascript">window.parent.window.t.stopUpload(<?php echo $result; ?>);</script>