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
	//session_start();
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
	
	//session_unregister("yj_contactus['security_number']");
	//@unset($__yj_contactus['security_number']);
	//if(isset($_SESSION['__yj_contactus'])) unset($_SESSION['__yj_contactus']);	
	//unset($session_var);
	
	$operators=array('+','-','*');
	$first_num=rand(1,5);
	$second_num=rand(6,11);
	shuffle($operators);
	$expression=$second_num.$operators[0].$first_num;

	eval("\$session_var=".$second_num.$operators[0].$first_num.";");

	$session = JFactory::getSession();
	$db		 = JFactory::getDBO(); 
	
/*	include_once("../../../administrator/components/com_yjcontactus/tables/yjcaptcha.php");
	$table = new YjCaptchaTableYjCaptcha($db);

	$table->id 						= '';
	$table->session_id_contactus 	= $session->getId();
	$table->captcha 				= $session_var;
	$table->attachement 			= '';	
	if(!$table->store()){
		echo $table->getError();
		exit;
	}*/

	$query	= $db->getQuery(true);	
	$query->clear();
	$query->select('id');
	$query->from('#__yj_contactus_captcha');
	$query->where("`session_id_contactus` = '".$session->getId()."'");
	$db->setQuery($query);
	$id_captcha = $db->loadResult();
	
	if($id_captcha == ''){
		$query->clear();
		$query->insert("#__yj_contactus_captcha");
		$query->set("`id` = ''");
		$query->set("`session_id_contactus` = '".$session->getId()."'");
		$query->set("`captcha` = '".$session_var."'");
		$query->set("`attachement` = ''");	
	}else{
		$query->clear();
		$query->update("#__yj_contactus_captcha");
		$query->set("`session_id_contactus` = '".$session->getId()."'");
		$query->set("`captcha` = '".$session_var."'");
		$query->where("`id` = ".$id_captcha);
	}
	$db->setQuery($query);
	$db->Query( $query );

	//$_SESSION['yj_contactus']['security_number'] = $session_var;
	//$session->set('security_number',$session_var,'yj_contactus');

	$img=imagecreate(100,50);

	$text_color		 = imagecolorallocate($img,252,252,252);
	$background_color= imagecolorallocate($img,49,175,225);

	imagefill($img,0,150,$background_color);
	imagettftext($img,16,rand(-10,10),rand(10,30),rand(25,35),$background_color,"fonts/courbd.ttf",$expression);

	header("Content-type:image/jpeg");
	header("Content-Disposition:inline ; filename=secure.jpg");
	imagejpeg($img);
?>