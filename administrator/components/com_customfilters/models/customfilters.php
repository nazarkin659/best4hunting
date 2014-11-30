<?php
/**
 * The Customfilters model file
 *
 * @package 	customfilters
 * @author		Sakis Terz
 * @link		http://breakdesigns.net
 * @copyright	Copyright (c) 2010 - 2011 breakdesigns.net. All rights reserved.
 * @license		http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 *				customfilters is free software. This version may have been modified
 *				pursuant to the GNU General Public License, and as distributed
 *				it includes or is derivative of works licensed under the GNU
 *				General Public License or other free or open source software
 *				licenses.
 * @version $Id: customfilters.php 3 2012-01-05 18:34 sakis $
 * @since		1.0
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die;
// Load the model framework
jimport('joomla.application.component.modellist');

/**
 * The basic model class
 *
 * @author	Sakis Terz
 * @since	1.0
 */
class CustomfiltersModelCustomfilters extends JModelList{
     
	/**
	 * @var string Model context string
	 */
	//var $_context = 'com_customfilters.customfilters';

	/**
	 * Constructor.
	 *
	 * @param	array	An optional associative array of configuration settings.
	 * @see		JController
	 * @since	1.0
	 */
	public function __construct($config = array())	{
		
		parent::__construct($config);

	}
	

	/**
	 * Function that returns version info in JSON format
	 * @return string
	 * @since 1.3.1
	 */
	function getVersionInfo($updateFrequency=2){
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'update.php');
		$version_info=array();
		$html='';
		$html_current='';
		$html_outdated='';
		$pathToXML=JPATH_COMPONENT_ADMINISTRATOR.DIRECTORY_SEPARATOR.'customfilters.xml';
		$installData=JApplicationHelper::parseXMLInstallFile($pathToXML);

		$updateHelper=extensionUpdateHelper::getInstance($extension='com_customfilters_starter',$targetFile='assets/lastversion.ini',$updateFrequency=2);
		$updateRegistry=$updateHelper->getData();

		if($installData['version']){
			if(is_object($updateRegistry) && $updateRegistry!==false){
				$isoutdated_code=version_compare($installData['version'], $updateRegistry->version);
				if($isoutdated_code<0){
					$html_current='<div class="cfversion">
					<span class="pbversion_label">'.JText::_('COM_CUSTOMFILTERS_LATEST_VERSION') .' : v. </span>
					<span class="cfversion_no">'.$updateRegistry->version.'</span><span> ('.$updateRegistry->date.')</span>
					</div>';
				}

				if($isoutdated_code<0)$html_outdated=' <span id="cfoutdated">!Outdated</span>';
				else $html_outdated=' <span id="cfupdated">Updated</span>';				
			}

			$html.='<div class="cfversion">
			<span class="pbversion_label">'.JText::_('COM_CUSTOMFILTERS_CURRENT_VERSION') .' : v. </span>
			<span class="cfversion_no">'.$installData['version'].'</span><span> ('.$installData['creationDate'].')</span>'.$html_outdated.
			'</div>';

		}
		$html.=$html_current;
		$version_info['html']=$html;
		$version_info['status_code']=$isoutdated_code;
		return $version_info;
	}

}
