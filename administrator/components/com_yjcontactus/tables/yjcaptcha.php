<?php
/**
 * Captcha Table for YjContactUS Component
 * 
 * @package
 * @subpackage Components
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

/**
 * Captcha Table class
 *
 * @package
 * @subpackage Components
 */
class YjCaptchaTableYjCaptcha extends JTable
{
	/**
	 * Primary Key
	 *
	 * @var int
	 */
	var $id = null;
	/**
	 * 
	 *
	 * @var int
	 */
	var $session_id_contactus = null;	
	/**
	 * 
	 *
	 * @var int
	 */
	var $captcha = null;		
	/**
	 * 
	 *
	 * @var char
	 */
	var $attachement = null;
	
	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	function YjCaptchaTableYjCaptcha($db){
		parent::__construct('#__yj_contactus_captcha', 'session_id_contactus', $db);
	}
}
?>