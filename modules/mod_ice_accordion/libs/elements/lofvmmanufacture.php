<?php
/**
 * $ModDesc
 * 
 * @version		$Id: helper.php $Revision
 * @package		modules
 * @subpackage	mod_lofflashcontent
 * @copyright	Copyright (C) JAN 2010 LandOfCoder.com <@emai:landofcoder@gmail.com>. All rights reserved.
 * @license		GNU General Public License version 2
 */
defined('_JEXEC') or die();
//Load virtuemart needed files
if(!defined('VIRTUEMART_PATH')){
	define('VIRTUEMART_PATH', JPATH_ADMINISTRATOR.DS.'components'.DS.'com_virtuemart');
}

if (!class_exists( 'VmConfig' ) && file_exists(VIRTUEMART_PATH.DS.'helpers'.DS.'config.php')) require( VIRTUEMART_PATH.DS.'helpers'.DS.'config.php');
if(class_exists( 'VmConfig' ))	VmConfig::loadConfig();

if(file_exists(VIRTUEMART_PATH.DS.'helpers'.DS."shopfunctions.php"))
	require_once(VIRTUEMART_PATH.DS.'helpers'.DS."shopfunctions.php");

class JFormFieldlofvmmanufacture extends JFormField
{
	protected $type 		= 'Lofvmmanufacture';

	protected function getInput() {
		if(!file_exists(JPATH_SITE.DS.'components'.DS.'com_virtuemart'.DS."virtuemart.php"))
			return JText::_("VIRTUEMART_NOT_EXISTS");
		/* Load the manufacturers*/
		if(!class_exists('VirtueMartModelManufacturer')) require(VIRTUEMART_PATH.DS.'models'.DS.'manufacturer.php');
		$class = isset($this->element["class"])?$this->element["class"]:$this->element->attributes("class");
		$mf_model = new VirtueMartModelManufacturer();
		$manufacturers = $mf_model->getManufacturerDropdown($this->value);
		$output = "";
		if(count($manufacturers)>0 ){
			$output = JHTML::_('select.genericlist', $manufacturers, $this->name, 'class="inputbox '.$class.'"', 'value', 'text', $this->value, $this->id );
		}
		return $output;
	}
	
}