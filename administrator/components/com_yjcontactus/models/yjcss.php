<?php
/**
 * CSS model for YjContactUS Component
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
class YjContactUSModelYjCSS extends JModel
{
	
	/**
	 * Retrieves the css file content
	 * 
	 */
	function getedit_css(){
		global $mainframe;
		
		// Initialize some variables
		jimport('joomla.client.helper');
		$db			=& JFactory::getDBO();
				
		$css_file 	= JPATH_ROOT.DS.'components'.DS.'com_yjcontactus'.DS.'helpers'.DS.'yjcontactus.css';
		 
		@chmod($css_file,0777);
		$handle = fopen ( $css_file, 'r' );
		$file_content = fread ( $handle, filesize ( $css_file ) );
		fclose ( $handle );	
		
		$return = array("css_file_name" => $css_file, "file_content" => $file_content);
		return $return;
	}	
	
	/**
	 * Method to save a css file
	 *
	 * @access	public
	 * @return	boolean	True on success
	 */
	function save_css(){

		//language file in admin area	
		$css_file_content	= JArrayHelper::getValue( $_REQUEST, 'css_file_content', '' );
		//new lang file
		$lang_file_edited	= JPATH_ROOT.DS.'components'.DS.'com_yjcontactus'.DS.'helpers'.DS.'yjcontactus.css';			

		JFilterOutput::objectHTMLSafe( $css_file_content );	
		
		// UPDATE LANGUAGE
		if($css_file_content != ''){
			//language in admin area
			$fp = @fopen( $lang_file_edited, "w" );
			set_magic_quotes_runtime(0);
			fwrite( $fp, stripslashes($css_file_content ) );
			fclose( $fp );
		}
		
		return true;		
	}	
	
}
?>