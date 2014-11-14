<?php
/**
 * @version		$Id: categoriesmultiple.php 1034 2011-10-04 17:00:00Z joomlaworks $
 * @package		K2
 * @author		JoomlaWorks http://www.joomlaworks.gr
 * @copyright	Copyright (c) 2006 - 2011 JoomlaWorks Ltd. All rights reserved.
 * @license		GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 */

// no direct access
defined('_JEXEC') or die('Restricted access');
//Load virtuemart needed files
if(!defined('VIRTUEMART_PATH')){
	define('VIRTUEMART_PATH', JPATH_ADMINISTRATOR.DS.'components'.DS.'com_virtuemart');
}

if (!class_exists( 'VmConfig' ) && file_exists(VIRTUEMART_PATH.DS.'helpers'.DS.'config.php')) require( VIRTUEMART_PATH.DS.'helpers'.DS.'config.php');
if(class_exists( 'VmConfig' ))	VmConfig::loadConfig();

if(file_exists(VIRTUEMART_PATH.DS.'helpers'.DS."shopfunctions.php"))
	require_once(VIRTUEMART_PATH.DS.'helpers'.DS."shopfunctions.php");

jimport('joomla.html.parameter.element');

class JFormFieldLofvmCategory extends JFormField
{

	var	$_name = 'lofvmcategory';

	function getInput(){
		if(!file_exists(JPATH_SITE.DS.'components'.DS.'com_virtuemart'.DS."virtuemart.php"))
			return JText::_("VIRTUEMART_NOT_EXISTS");
		$fieldName = $this->name;
		$class = isset($this->element["class"])?$this->element["class"]:$this->element->attributes("class");
		// Load some common models
		if(!class_exists('TableProducts')) require(VIRTUEMART_PATH.DS.'tables'.DS.'products.php');
		if(!class_exists('TableCategories')) require(VIRTUEMART_PATH.DS.'tables'.DS.'categories.php');
		
		if(!class_exists('VirtueMartModelCategory')) require(VIRTUEMART_PATH.DS.'models'.DS.'category.php');
		$category_model = new VirtueMartModelCategory();
		
		/* Load the product */
		if(!class_exists('VirtueMartModelProduct')) require(VIRTUEMART_PATH.DS.'models'.DS.'product.php');
		$product_model = new VirtueMartModelProduct();
		if (!empty($this->value)) $category_tree = ShopFunctions::categoryListTree($this->value);
		else $category_tree = ShopFunctions::categoryListTree();
		ob_start();
		?>
		<select class="inputbox <?php echo $class; ?>" id="<?php echo $this->id; ?>" name="<?php echo $fieldName; ?>" style="width:90%;" multiple="multiple" size="10">
					<option value=""><?php echo JText::_('SELECT_ALL') ; ?></option>
					<?php echo $category_tree; ?>
		</select>
		<?php
        $output = ob_get_contents();
		ob_end_clean();
		return $output;
	}
}
