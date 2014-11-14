<?php
/**
 * Settings Table for YjContactUS Component
 * 
 * @package    
 * @subpackage Components
 */

// no direct access
defined('_JEXEC') or die('Restricted access');


/**
 * Settings Table class
 *
 * @package    
 * @subpackage Components
 */
class TableYjSettings extends JTable
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
	var $upload_folder = null;	

	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	function TableYjSettings(& $db) {
		parent::__construct('#__yj_contactus_settings', 'id', $db);
	}
}
?>
