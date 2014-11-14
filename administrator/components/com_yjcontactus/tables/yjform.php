<?php
/**
 * Default Table for YjContactUS Component
 * 
 * @package    
 * @subpackage Components
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

/**
 * Default Table class
 *
 * @package    
 * @subpackage Components
 */
class YjContactUSTableYjForm extends JTable
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
	var $ordering = null;	
	/**
	 * 
	 *
	 * @var int
	 */
	var $published = null;	
	/**
	 * 
	 *
	 * @var varchar
	 */
	var $name = null;	
	/**
	 * 
	 *
	 * @var string
	 */
	var $email = null;	
	/**
	 * 
	 *
	 * @var string
	 */
	var $departments = null;
	/**
	 * 
	 *
	 * @var string
	 */
	var $checked_out = null;
	/**
	 * 
	 *
	 * @var string
	 */
	var $checked_out_time = null;
	/**
	 * 
	 *
	 * @var string
	 */
	var $captcha = null;
	/**
	 * 
	 *
	 * @var string
	 */
	var $item_id = null;		
	
	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	function YjContactUSTableYjForm(& $db) {
		parent::__construct('#__yj_contactus_forms', 'id', $db);
		return true;
	}
}
?>
