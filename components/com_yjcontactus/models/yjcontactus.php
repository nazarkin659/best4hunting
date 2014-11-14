<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

jimport( 'joomla.application.component.model' );

/**
 * YjWebGallery Model
 *
 */
class YjContactUSModelYjContactUS extends JModel
{

	function getdisplay(){
		//component's settings and params
		$Itemid	= JRequest::getInt( 'Itemid' );
		$db 	= JFactory::getDBO();
		$query	= $db->getQuery(true);
				
		$session = JFactory::getSession();
		$forms = $this->getform_details();	

		//if no form is published 
		if(empty($forms)){
			return JTEXT::_('COM_YJCONTACTUS_NO_FORM');
		}else{
			//insert into contactus_captcha session_id with no captcha - insert it only once
			$query->clear();
			$query->select('*');
			$query->from('#__yj_contactus_captcha');
			$query->where('session_id_contactus = "'.$session->getId().'"');
			$db->setQuery($query);
			$session_insert = $db->loadObjectList();
			if(empty($session_insert)){
				//insert it	
				$query->clear();
				$query->insert('#__yj_contactus_captcha');
				$query->set('id = ""');
				$query->set('session_id_contactus = ""'.$session->getId().'""');
				$query->set('captcha = ""');
				$db->setQuery($query);
				$db->Query();
			}
			
			//get all departments
			$query->clear();
			$query->select('*');
			$query->from('#__yj_contactus_departments');
			$query->where('id IN ('.$forms->departments.')');
			$query->where('published = 1');
			$query->order(' ordering ASC ');
			$db->setQuery($query);			
			$departments_rows = $db->loadObjectList();
			$layout_display = array();

			//require_once('components/com_yjcontactus/helpers/config.php');
			$append_session = $this->getappend_session();
			$default_category = '' ;
			
			//add header informations
			JHTML::_('behavior.mootools');
			$document = JFactory::getDocument();	
			$document->addScript(JURI::base().'components/com_yjcontactus/helpers/script/AjaxForm.js');	
			$document->addScript(JURI::base().'components/com_yjcontactus/helpers/script/input_file_modifier.js');	
			$document->addScript(JURI::base().'components/com_yjcontactus/helpers/script/captcha.js');				
			$document->addStyleSheet(JURI::base(). 'components/com_yjcontactus/helpers/yjcontactus.css');
			$document->addCustomTag('<!--[if lt IE 8]> <style type="text/css">
#contactForm table{
*border-collapse: expression(\'separate\', cellSpacing = \'0px\');
}
</style>
<![endif]-->
');		
			$layout_js = '
			window.addEvent(\'domready\', function(){
			/* put the whole instance in a global var. remember to change it in upload.php to refer to this variable */
			this.t = new AjaxForm({
			form: \'contactForm\',
			categorySelect: \'category\',
			messageTextarea: \'message\',
			errorMessageClass: \'error\',
			disableElements: [\'message\',\'submit_btn\', \'captcha\', \'archive\',\'name\',\'email\',\'subject\'],
			categoryOptions: {';
			foreach($departments_rows as $rows => $departments){
				$disabled = $departments->enabled == 1 ? "false" : "true"; 
				
				$disable_fields = array();
				if($forms->captcha == 0) $disable_fields[] = "'captcha'";			
				if($departments->upload == 0 ) $disable_fields[] = "'archive'";
				$disable_fields_final = "[".implode(",",$disable_fields)."]";
				
				$departments->description = str_replace("\r\n", "", htmlspecialchars(stripslashes($departments->description), ENT_QUOTES));
				$departments->description = str_replace("\n", "", $departments->description);
				$departments->description = str_replace("\r", "", $departments->description);				
				
				$layout_display[] = $departments->id.': {
				\'option\':\''.$departments->name.'\',
				\'id\':\''.$departments->id.'\',				
				\'message\':\''.$departments->description.'\',
				\'disabled\':'.$disabled.',
				\'disabled_message\':\''.str_replace("\r\n","\\n",$departments->message).'\',
				\'disable_fields\':'.$disable_fields_final.'
				}';
				unset($disable_fields);
				unset($disable_fields_final);			
			}
			$layout_js .= implode(",",$layout_display);
			$layout_js .= '
			},
			selectedOption: \''.$default_category.'\',
			responseParent: \'show_response\',
			responseLoadClass: \'loading\',
			responseClass: \'response\',
			uploadLink: \''.JURI::base().'components/com_yjcontactus/helpers/upload.php'.$append_session.'\',
			uploadElem: \'archive\',
			uploadMessage: \'uploadMessage\',
			uploadLoading: \'uploadLoading\',
			LiveSite : \''.JURI::base().'\',
			imageId : \'captcha_image\',
			sessionId : \''.$append_session.'\',
			inputId : \'captcha\',
			selectDepartmentTxt : \''.JTEXT::_("COM_YJCONTACTUS_SELECT_DEPARTMENT").'\',
			selectDepartmentError : \''.htmlspecialchars("<span class=\"error_field\">".JTEXT::_("COM_YJCONTACTUS_SELECT_DEPARTMENT")."</span>").'\',			
			uploadZipTxt : \''.JTEXT::_("COM_YJCONTACTUS_UPLOAD_ZIP").'\',
			uploadSuccesTxt : \''.JTEXT::_("COM_YJCONTACTUS_UPLOAD_SUCCESS").'\'
			});
			var JTooltips = new Tips($$(\'.hasTip\'), {fixed: false});
			});';
			$document->addScriptDeclaration($layout_js);
			return "published";
		}
	}
	
	//function to generate captcha code from session
	function getappend_session(){
		$session = JFactory::getSession();

		$append_session = '?'.$session->getName().'='.$session->getId();	
		return $append_session;
	}
	
	//function to generate captcha code from session
	function send_email(){
		global $mainframe;
		$app = JFactory::getApplication('site');
		
		unset($security_number);
		//require('config.php');
		/* change the values from array to the fields that are required */
		$not_empty = array('name','email','subject','message');//'category',
		/* change this to the name of the e-mail field */
		$email_field = 'email';
		$default_category = 0 ;
		$error = array();
		$success = NULL;
		/* send the form */
		if($_POST){
			// Check for request forgeries
			JRequest::checkToken('request') or $error[] = '<font color=\"#BC0202\">'.JText::_("COM_YJCONTACTUS_TOKEN_ERROR").'</font>';//or jexit( 'Invalid Token' );
			$fields = array();
			
			$fields['category'] = 0;
			if($_POST['category'] == ''){ 
				$error[] = '<span class=\"error_field\">'.JText::_("COM_YJCONTACTUS_ERROR_CATEGORY").'</span>';
				$fields['category'] = 1;
			}	

			foreach( $not_empty as $key=>$value ){
				$fields[$value] = 0;
				if( $_POST[$value] == ''){
					$error[] = '<span class=\"error_field\">'.JText::_("COM_YJCONTACTUS_ERROR_FIELDS").'</span>';
					$fields[$value] = 1;
				}
			}
			//valid email check
			$fields[$email_field] = 0;
			if(!preg_match("/^([a-zA-Z?0-9?\_?\.?]+)@([a-zA-Z?\.?\-?]+)\.([a-zA-Z?]{2,6})$/i",$_POST[$email_field])){
				$error[] = '<span class=\"error_email\">'.JText::_("COM_YJCONTACTUS_ERROR_EMAIL").'</span>';
				$fields[$email_field] = 1;
			}
			/* The captcha result is stored in $_SESSION['security_number'] */
			
			$session = JFactory::getSession();
			//$security_number=$session->get('security_number','','yj_contactus');
			//if(isset($_SESSION['yj_contactus'])) $security_number = $_SESSION['yj_contactus']['security_number'];
			//else $security_number = "";
			$security_number = $this->getsecurity_number();			 
			
			$fields['captcha'] = 0; 
			if( isset($_POST['captcha']) && ((int)$_POST['captcha']) != $security_number){
				$error[] = '<span class=\"error_captcha\">'.JText::_("COM_YJCONTACTUS_ERROR_CAPTCHA").'</span>';
				$fields['captcha'] = 1;
			}
			/* if no errors, do what needs to be done */
			if(count($error)==0){
				$file_attached =& $this->getattachement();
				$email_send_to = $this->getform_email();
				/* change this with your e-mail address */
				$send_to 	= !empty($email_send_to) ? $email_send_to : $app->getCfg('mailfrom') ;
				settype($send_to, "string");

				$Subject 	= JRequest::getVar('subject');
				$Message 	= nl2br(JRequest::getVar('message'));
				$From 		= JRequest::getVar('email');
				$FromName 	= JRequest::getVar('name');
				$mode 		= 1;//html format for 1, simple format for 0		
				/* the name of the attached file is saved in session after upload */
				if( $file_attached != ''){
					$attach = $this->getupload_folder();
					$attach .= "/".$file_attached; 
				}else{
					$attach = NULL;
				}

/*				try {
					// if mail is send, delete uploaded files and set message
					JUtility::sendMail($From, $FromName, $send_to, $Subject, $Message, 1, '', '', $attach, $From, $FromName );
					$success = '<span class=\"email_sent\">'.JText::_("COM_YJCONTACTUS_SUCC_SEND_EMAIL").'</span>';					
				} catch (Exception $e) {
					//echo 'Caught exception: ',  $e->getMessage(), "\n";
					$error[] = '<span class=\"error_send_email\">'.JText::_("COM_YJCONTACTUS_ERROR_SEND_EMAIL").'</span>';
				}*/

				// if mail is send, delete uploaded files and set message
				$mail_send = JUtility::sendMail($From, $FromName, $send_to, $Subject, $Message, 1, '', '', $attach, $From, $FromName );
			
				if( $mail_send ){
					$success = '<span class=\"email_sent\">'.JText::_("COM_YJCONTACTUS_SUCC_SEND_EMAIL").'</span>';
				}else{
					//$this->delsecurity_number();				
					$error[] = '<span class=\"error_send_email\">'.JText::_("COM_YJCONTACTUS_ERROR_SEND_EMAIL").'</span>';
				}
			}
			//check if error
			if(!empty($error[0])){
				$error_display = $error[0];
			}else{
				$error_display = NULL;			
			}
			
			$response = array('fields'=>$fields, 'error'=>$error_display, 'success'=>$success);
			require_once('components/com_yjcontactus/helpers/libs/JSON.class.php');
			$json = new JSON($response);
			ob_clean();
			echo $json->result;
			exit();
		}
	}
	
	//get form details from db after Itemid or request id
	function getform_details(){
		//component's settings and params
		$Itemid	= JRequest::getInt( 'Itemid' );
		$id 	= JRequest::getVar('form_id', $Itemid);
		$db 	= JFactory::getDBO();
		$query	= $db->getQuery(true);
		
		$query->clear();
		$query->select('*');
		$query->from('#__yj_contactus_forms');
		$query->where('item_id = "'.$id.'"');
		$query->where('published = 1');
		$query->order('ordering ASC');
		//echo $query->__toString();
		$db->setQuery($query);
		$forms = $db->loadObjectList();
		//select first form if empty form for this itemid
		if(empty($forms[0])){
			//get form details from db
			$query->clear();
			$query->select('*');
			$query->from('#__yj_contactus_forms');
			$query->where('item_id = "'.$id.'"');
			$query->where('published = 1');
			$db->setQuery($query,0,1);
			$forms = $db->loadObjectList();		
		}
		
		if(!empty($forms)) return $forms[0];
		else return FALSE;
	}

	//get form details from db after Itemid or request id
	function getform_email(){
		//component's settings and params
		$Itemid		= JRequest::getInt( 'Itemid' );		
		$form_id 	= JRequest::getVar('form_id', $Itemid);
		$category 	= JRequest::getVar('category', $Itemid);
		$db 		=& JFactory::getDBO();
		$query		= $db->getQuery(true);

		$query->clear();
		$query->select('*');
		$query->from('#__yj_contactus_departments');
		$query->where('id = '.$category);
		//echo $query->__toString(); exit;
		$db->setQuery($query);
		$department = $db->loadObjectList();

		//select first form if empty form for this itemid
		if(!empty($department) && $department[0]->email == ""){
			//get form details from db
			$query->clear();
			$query->select('*');
			$query->from('#__yj_contactus_forms');
			$query->where('item_id = "'.$form_id.'"');
			$query->where('published = 1 ');
			$query->order('ordering ASC ');			
			//echo $query->__toString();
			$db->setQuery($query);
			$forms = $db->loadObjectList();
			//select first form if empty form for this itemid
			if(empty($forms[0])){
				//get form details from db
				$query->clear();
				$query->select('*');
				$query->from('#__yj_contactus_forms');
				$query->where('published = 1 ');
				$query->order('ordering ASC ');			
				//echo $query->__toString();
				$db->setQuery($query, 0, 1);
				$forms = $db->loadObjectList();				
			}
			return $forms[0]->email;
		}else{
			return ($department[0]->email);
		}
	}
	
	//get captcha security number from db 
	function getsecurity_number(){
		//component's settings and params
		$session 	= JFactory::getSession();
		$db 		=& JFactory::getDBO();
		$query		= $db->getQuery(true);

		//get form details from db
		$query->clear();
		$query->select('captcha');
		$query->from('#__yj_contactus_captcha');
		$query->where('session_id_contactus = "'.$session->getId().'"');
		//echo $query->__toString();
		$db->setQuery($query);
		$security_number = $db->loadObjectList();

		return ($security_number[0]->captcha);
	}
	
	//get captcha security number from db 
	function getattachement(){
		//component's settings and params
		$session 	= JFactory::getSession();
		$db 		=& JFactory::getDBO();
		$query		= $db->getQuery(true);
		
		//get form details from db
		$query->clear();
		$query->select('attachement');
		$query->from('#__yj_contactus_captcha');
		$query->where('session_id_contactus = "'.$session->getId().'"');
		//echo $query->__toString();
		$db->setQuery($query);
		$security_number = $db->loadObjectList();

		if(!empty($security_number)){
			return ($security_number[0]->attachement);
		}else{
			return false;
		}
	}	
	
	//delete security number from db
	function delsecurity_number(){
		//component's settings and params
		$session 	= JFactory::getSession();
		$db 		= JFactory::getDBO();
		$query		= $db->getQuery(true);
		
		//get form details from db
		$query->clear();
		$query->delete('#__yj_contactus_captcha');
		$query->where('session_id_contactus = "'.$session->getId().'"');
		//echo $query->__toString();
		$db->setQuery($query);
		$db->Query();

		return true;
	}
	
	//get form details from db after Itemid or request id
	function getupload_folder(){
		//component's settings and params
		$db 		= JFactory::getDBO();
		$query		= $db->getQuery(true);
		
		//get form details from db
		$query->clear();
		$query->select('upload_folder');
		$query->from('#__yj_contactus_settings');		
		//echo $query->__toString();
		$db->setQuery($query);
		$upload_folder = $db->loadObjectList();
		
		return "images/".$upload_folder[0]->upload_folder;
	}				
}
?>