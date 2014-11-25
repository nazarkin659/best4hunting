<?php
/**
 * Department Table for YjContactUS Component
 * 
 * @package    
 * @subpackage Components
 */

// no direct access
defined('_JEXEC') or die('Restricted access');


/**
 * Department Table class
 *
 * @package    
 * @subpackage Components
 */
class YjContactUSTableYjDepartment extends JTable
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
	var $description = null;	
	/**
	 * 
	 *
	 * @var string
	 */
	var $message = null;
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
	var $enabled = null;
	/**
	 * 
	 *
	 * @var string
	 */
	var $upload = null;	
	/**
	 * 
	 *
	 * @var string
	 */
	var $email = null;			

	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	function YjContactUSTableYjDepartment(& $db) {
		parent::__construct('#__yj_contactus_departments', 'id', $db);
	}
}
?>
