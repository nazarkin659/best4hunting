<?php
/**
 * IceVMCart Extension for Joomla 2.5 By IceTheme
 * 
 * 
 * @copyright	Copyright (C) 2008 - 2012 IceTheme.com. All rights reserved.
 * @license		GNU General Public License version 2
 * 
 * @Website 	http://www.icetheme.com/Joomla-Extensions/icevmcart.html
 * @Support 	http://www.icetheme.com/Forums/IceVmCart/
 *
 */
defined('_JEXEC') or die('Restricted access');
error_reporting(E_ALL & ~E_NOTICE);
if(!defined('VIRTUEMART_PATH')){
	define('VIRTUEMART_PATH', JPATH_ADMINISTRATOR.DS.'components'.DS.'com_virtuemart');
}
if (!file_exists(JPATH_SITE.DS."components".DS."com_virtuemart".DS.'virtuemart.php')){
    JError::raiseError(500,"Please install component \"Virtuemart\"");
}
$mainframe = &JFactory::getApplication();
$document = &JFactory::getDocument();

	$tPath = JPATH_BASE.DS.'templates'.DS.$mainframe->getTemplate().DS.'html'.DS.$module->module.DS.'assets'.DS.'style.css';
	if( file_exists($tPath) ){
		JHTML::stylesheet( 'templates/'.$mainframe->getTemplate().'/html/'.$module->module.'/assets/style.css');
	}else{
		$document->addStyleSheet(JURI::base().'modules/mod_ice_virtuemart_cart/assets/style.css');
	}


$vm_cart_empy	= $params->get('vm_cart_empy', 'Your Cart is Empty !');
$margin_top = (int)$params->get("popup_top", 150);
$document->addCustomTag('<style type="text/css"> #facebox { top: '.$margin_top.'px!important } </style>');

jimport('joomla.application.component.model');

if (!class_exists( 'VmConfig' )) require( VIRTUEMART_PATH.DS.'helpers'.DS.'config.php');
if(class_exists( 'VmConfig' ))	VmConfig::loadConfig();

$jsVars  = ' jQuery(document).ready(function(){
	jQuery(".vmCartModule").productUpdate();

});' ;

if(!class_exists('VirtueMartCart')) require(JPATH_VM_SITE.DS.'helpers'.DS.'cart.php');
$cart = VirtueMartCart::getCart(false);
$data = $cart->prepareAjaxData();
$lang = JFactory::getLanguage();
$extension = 'com_virtuemart';
$lang->load($extension);
if ($data->totalProduct>1) $data->totalProductTxt = JText::sprintf('COM_VIRTUEMART_CART_X_PRODUCTS', $data->totalProduct);
else if ($data->totalProduct == 1) $data->totalProductTxt = JText::_('COM_VIRTUEMART_CART_ONE_PRODUCT');
else $data->totalProductTxt = JText::_('COM_VIRTUEMART_EMPTY_CART');
if (false && $data->dataValidated == true) {
	$taskRoute = '&task=confirm';
	$linkName = JText::_('COM_VIRTUEMART_CART_CONFIRM');
} else {
	$taskRoute = '';
	$linkName = JText::_('COM_VIRTUEMART_CART_SHOW');
}
$useSSL = VmConfig::get('useSSL',0);
$useXHTML = true;
$data->cart_show = '<a class="vm_viewcart" href="'.JRoute::_("index.php?option=com_virtuemart&view=cart".$taskRoute,$useXHTML,$useSSL).'">'.$linkName.'</a>';
$data->billTotal = $lang->_('COM_VIRTUEMART_CART_TOTAL').' : <strong>'. $data->billTotal .'</strong>';

vmJsApi::jQuery();
vmJsApi::jPrice();
vmJsApi::cssSite();
$moduleclass_sfx = $params->get('moduleclass_sfx', '');
$tmp = $tmp2 = array();
if(!empty($cart->products)){
	
	foreach($cart->products as $key=>$value){
			if(!empty($value->product_sku)){
				$tmp[$value->product_sku] = $value;
		   }
		   else{
			   $product_name = strip_tags($value->product_name);
			   $product_name = JFilterOutput::stringURLSafe($product_name);
			   $tmp2[$product_name] = $value;
		   }
	}
}

if(!empty($data->products)){
	foreach($data->products as $key=>$value){
		$product = isset($tmp[$value["product_sku"]])?$tmp[$value["product_sku"]]:null;
		if(empty($product)){
			$product_name = strip_tags($value["product_name"]);
			$product_name = JFilterOutput::stringURLSafe($product_name);
			$product = isset($tmp2[$product_name])?$tmp2[$product_name]:null;
		}
		if(!empty($product)){
			$tmpArray = array();
			$data->products[$key]["virtuemart_product_id"] = $product->virtuemart_product_id;
			$data->products[$key]["product_name"] = strip_tags($product->product_name);
			$data->products[$key]["product_in_stock"] = $product->product_in_stock;
			$data->products[$key]["virtuemart_media_id"] = $product->virtuemart_media_id;
			$data->products[$key]["categories"] = $product->categories;
			$data->products[$key]["virtuemart_category_id"] = $product->virtuemart_category_id;
			$data->products[$key]["link"] = $product->link;
			$data->products[$key]["image"] = "";
			$mediaModel = VmModel::getModel('Media');
			$tmpProduct = new stdClass;
			$tmpProduct->virtuemart_media_id = $data->products[$key]["virtuemart_media_id"];
			$mediaModel->attachImages($tmpProduct, "products", "image", 0);
			if(isset($tmpProduct->images) && !empty($tmpProduct->images)){
				$data->products[$key]["image"] = $tmpProduct->images[0]->file_url_thumb;
			}
		}
	}
}
require(JModuleHelper::getLayoutPath('mod_ice_virtuemart_cart')); 
$dropdown = $params->get('dropdown',1);
$ajax = $params->get('ajax',1);
if($ajax == 1){
 $version = 202;
 if( class_exists( 'vmVersion' ) ) {
	$release_number = vmVersion::$RELEASE;
	$tmp = explode(".",$release_number);
	$version = "";
	if(isset($tmp[0])){
		$version .= $tmp[0];
		unset($tmp[0]);
	}
	if(isset($tmp[1])){
		$version .= $tmp[1];
		unset($tmp[1]);
	}
	if(isset($tmp[2])){
		$version .= $tmp[2];
		unset($tmp[2]);
	}
	if(!empty($tmp)){
		$version .= ".".implode("", $tmp);
	}
	if(!empty($version)){
		$version = (float)$version;
	}
 }
 if( $version > 202 )
	JHTML::script(JURI::base().'modules/mod_ice_virtuemart_cart/assets/vmprices_2.js');
 else
	JHTML::script(JURI::base().'modules/mod_ice_virtuemart_cart/assets/vmprices.js');
?>
<?php } ?>