/*======================================================================*\
|| #################################################################### ||
|| # Copyright ©2006-2009 Youjoomla LLC. All Rights Reserved.           ||
|| # This file may not be redistributed in whole or significant part. # ||
|| # ---------------- THIS IS NOT FREE SOFTWARE ---------------- #      ||
|| # http://www.youjoomla.com | http://www.youjoomla.com/license.html # ||
|| #################################################################### ||
\*======================================================================*/

function getHTTPObject(){
	if (window.ActiveXObject) 
		  return new ActiveXObject("Microsoft.XMLHTTP");
	
	else if (window.XMLHttpRequest) 
		  return new XMLHttpRequest();
	
	else {
		alert("Your browser does not support AJAX.");
		return null;
	}
}

function reload_captcha(live_site,id,session_id,input_id){

	httpObject = getHTTPObject();

	if (httpObject != null) {
		//Get a reference to CAPTCHA image
		img = document.getElementById(id);
		//Change the image
		img.src = live_site+"components/com_yjcontactus/helpers/captcha.php"+session_id + "&refresh=" + Math.random();
		document.getElementById(input_id).value = '';
	}
}