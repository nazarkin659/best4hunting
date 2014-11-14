<?php
/**
 * Lang model for YjContactUS Component
 * 
 * @package     
 * @subpackage Components
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

jimport( 'joomla.application.component.model' );

/**
 * CSS Model
 *
 * @package   
 * @subpackage Components
 */
class YjContactUSModelYjLang extends JModel
{
	
	/**
	 * Retrieves the lang file content
	 * 
	 */
	function getedit_admin_lang(){
		global $mainframe;
		
		// Initialize some variables
		jimport('joomla.client.helper');
		$db			=& JFactory::getDBO();
				
		$lang_file 	= JPATH_ROOT.DS.'administrator'.DS.'language'.DS.'en-GB'.DS.'en-GB.com_yjcontactus.ini';
		 
		@chmod($lang_file,0777);
		$handle = fopen ( $lang_file, 'r' );
		$file_content = fread ( $handle, filesize ( $lang_file ) );
		fclose ( $handle );	
		
		$return = array("lang_file" => $lang_file, "file_content" => $file_content);
		return $return;
	}	
	
	/**
	 * Method to save a lang file
	 *
	 * @access	public
	 * @return	boolean	True on success
	 */
	function save_admin_lang(){
		//language file in admin area	
		$lang_file_content	= JArrayHelper::getValue( $_REQUEST, 'admin_lang_file_content', '' );
		//new lang file
		$lang_file_edited	= JPATH_ROOT.DS.'administrator'.DS.'language'.DS.'en-GB'.DS.'en-GB.com_yjcontactus.ini';			

		JFilterOutput::objectHTMLSafe( $lang_file_content );	
		
		// UPDATE LANGUAGE
		if($lang_file_content != ''){
			//language in admin area
			$fp = @fopen( $lang_file_edited, "w" );
			set_magic_quotes_runtime(0);
			fwrite( $fp, stripslashes($lang_file_content ) );
			fclose( $fp );
		}
		
		return true;		
	}
	
	/**
	 * Retrieves the lang file content
	 * 
	 */
	function getedit_front_lang(){
		global $mainframe;
		
		// Initialize some variables
		jimport('joomla.client.helper');
		$db			=& JFactory::getDBO();
				
		$lang_file 	= JPATH_ROOT.DS.'language'.DS.'en-GB'.DS.'en-GB.com_yjcontactus.ini';
		 
		@chmod($lang_file,0777);
		$handle = fopen ( $lang_file, 'r' );
		$file_content = fread ( $handle, filesize ( $lang_file ) );
		fclose ( $handle );	
		
		$return = array("lang_file" => $lang_file, "file_content" => $file_content);
		return $return;
	}	
	
	/**
	 * Method to save a lang file
	 *
	 * @access	public
	 * @return	boolean	True on success
	 */
	function save_front_lang(){
		//language file in admin area	
		$lang_file_content	= JArrayHelper::getValue( $_REQUEST, 'front_lang_file_content', '' );
		//new lang file
		$lang_file_edited	= JPATH_ROOT.DS.'language'.DS.'en-GB'.DS.'en-GB.com_yjcontactus.ini';			

		JFilterOutput::objectHTMLSafe( $lang_file_content );	
		
		// UPDATE LANGUAGE
		if($lang_file_content != ''){
			//language in admin area
			$fp = @fopen( $lang_file_edited, "w" );
			set_magic_quotes_runtime(0);
			fwrite( $fp, stripslashes($lang_file_content ) );
			fclose( $fp );
		}
		
		return true;		
	}		
	
}
?>