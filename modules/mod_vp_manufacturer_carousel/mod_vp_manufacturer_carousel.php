<?php defined('_JEXEC') or die('Restricted access');
/*------------------------------------------------------------------------------------------------------------
# VP Manufacturer Carousel! Joomla 2.5 Module for VirtueMart 2.0 Ver. 1.0
# ------------------------------------------------------------------------------------------------------------
# Copyright (C) 2012 VirtuePlanet Services LLP. All Rights Reserved.
# License - GNU General Public License version 2. http://www.gnu.org/licenses/gpl-2.0.html
# Author: VirtuePlanet Services LLP
# Email: info@virtueplanet.com
# Websites:  http://www.virtueplanet.com
------------------------------------------------------------------------------------------------------------*/
require('helper.php');
if (!class_exists( 'VirtueMartModelManufacturer' )) 
	 JLoader::import( 'manufacturer', JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_virtuemart' . DS . 'models' );
if (!class_exists( 'VmConfig' )) 
	 require(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_virtuemart'.DS.'helpers'.DS.'config.php');

VmConfig::loadConfig();
vmJsApi::jQuery();

/* Start - Params */
$orientation 						= $params->get( 'orientation', 'horizontal' ); 
$visible 								= $params->get( 'visible', 7 ); 
$step										= $params->get( 'step', 7); 
$width									= $params->get( 'width', 90);
$height									= $params->get( 'height', 60);
$speed									= $params->get( 'speed', 1000);
$margin									= $params->get( 'margin', 10);
$auto										= $params->get( 'auto', true);
$direction							= $params->get( 'direction', 'prev');
$navigation							= $params->get( 'navigation', true);
$linkType								= $params->get( 'link', 'listing' );
$menuitem 							= $params->get( 'menuitem', '1' ); 
$backgroundColor				= $params->get( 'backgroundColor', '');
$borderColor						= $params->get( 'borderColor', '');
$logoRadius							= $params->get( 'logoRadius', 2);
$imgTopPadding					= $params->get( 'imgTopPadding', null);
$headerText							= $params->get( 'headerText', null);
$footerText							= $params->get( 'footerText', null);
$load_jquery 						= (bool)$params->get( 'load_jquery', 0 );
$load_jqui 							= (bool)$params->get( 'load_jquery_ui', 0 );
$load_jquiwidget 				= (bool)$params->get( 'load_jquery_ui_widget', 1 );
$load_corousel 					= (bool)$params->get( 'load_jquery_rcarousel', 1 );
/* End - Params */

$vendorId = JRequest::getInt('vendorid', 1);
$model = VmModel::getModel('Manufacturer');
$manufacturers = $model->getManufacturers(true, true,true);
$model->addImages($manufacturers);
if(empty($manufacturers)) return false;

$document = JFactory::getDocument();
$modulepath = JURI::root().'modules/'.$module->module.'/';
$document->addStyleSheet($modulepath.'assets/css/rcarouselmod.css?v=1.0');
if($load_jquery){
	$document->addScript($modulepath.'assets/lib/jquery-1.7.1.min.js');	
}
if($load_jqui){
	$document->addScript($modulepath.'assets/lib/jquery.ui.core.min.js');	
}
if($load_jquiwidget){
	$document->addScript($modulepath.'assets/lib/jquery.ui.widget.min.js');	
}
if($load_corousel){
	$document->addScript($modulepath.'assets/lib/jquery.ui.rcarousel.min.js?v=1.0');
}
$ID = str_replace('.', '-', substr(microtime(true), -8, 8));
if($orientation == 'horizontal' and $navigation) {
$js="
jQuery(document).ready(function(){
		
	var availableWidth = 0;
	var new_number = 6;
	jQuery('#carousel-".$ID."').each(function(){
		availableWidth = jQuery(this).width();
		new_number = Math.round((availableWidth/$width)-1)		
	});	
	
	jQuery('#carousel-".$ID."').rcarousel({
		orientation: '".$orientation."',
		visible: new_number,	
		step: new_number,	
		margin: $margin,
		auto: {
			enabled: $auto,
			interval: $speed,
			direction: '$direction'
		},
		navigation: {
			next: '#ui-carousel-next-".$ID."', 
			prev: '#ui-carousel-prev-".$ID."'
		},
		width: $width,
		height:$height,
				
	});		
	new_number = 0;
	jQuery('#ui-carousel-next-".$ID."')
		.add('#ui-carousel-prev-".$ID."')
		.css('opacity', 0)
		.parent().hover(
		function(){
			jQuery('#ui-carousel-next-".$ID."').css('opacity', 0.7).css('right', 0);
			jQuery('#ui-carousel-prev-".$ID."').css('opacity', 0.7).css('left', 0);
		},
		function(){
			jQuery('#ui-carousel-next-".$ID."').css('opacity', 0).css('right', -30);
			jQuery('#ui-carousel-prev-".$ID."').css('opacity', 0).css('left', -30);
		}
	);

});
";		
} else if($orientation == 'vertical' and $navigation) {
$js="
jQuery(document).ready(function(){
	jQuery('#carousel-".$ID."').rcarousel({
		orientation: '".$orientation."',
		visible:$visible,		
		margin: $margin,
		auto: {
			enabled: $auto,
			interval: $speed,
			direction: '$direction'
		},
		navigation: {
			next: '#ui-carousel-next-".$ID."', 
			prev: '#ui-carousel-prev-".$ID."'
		},
		width:$width,
		height:$height
	});		
	jQuery('#ui-carousel-next-".$ID."')
		.add('#ui-carousel-prev-".$ID."')
		.css('opacity', 0)
		.parent().hover(
		function(){
			jQuery('#ui-carousel-next-".$ID."').css('opacity', 0.7).css('top', 0);
			jQuery('#ui-carousel-prev-".$ID."').css('opacity', 0.7).css('bottom', 0);
		},
		function(){
			jQuery('#ui-carousel-next-".$ID."').css('opacity', 0).css('top', -30);
			jQuery('#ui-carousel-prev-".$ID."').css('opacity', 0).css('bottom', -30);
		}
	);

});
";		
}  else if($orientation == 'vertical' and $navigation == 0) {
$js="
jQuery(document).ready(function(){
	jQuery('#carousel-".$ID."').rcarousel({
		orientation: '".$orientation."',
		visible:$visible,		
		margin: $margin,
		auto: {
			enabled: $auto,
			interval: $speed,
			direction: '$direction'
		},
		navigation: {
			next: '#ui-carousel-next-".$ID."', 
			prev: '#ui-carousel-prev-".$ID."'
		},
		width:$width,
		height:$height
	});		
	jQuery('#ui-carousel-next-".$ID."')
		.add('#ui-carousel-prev-".$ID."')
		.hide();

});
";		
}  else {
$js="
jQuery(document).ready(function(){
	var availableWidth = 0;
	var new_number = 6;
	jQuery('#carousel-".$ID."').each(function(){
		availableWidth = jQuery(this).width();
		new_number = Math.round((availableWidth/$width)-1)
		
	});	
	jQuery('#carousel-".$ID."').rcarousel({
		orientation: '".$orientation."',
		visible: new_number,	
		step: new_number,		
		margin: $margin,
		auto: {
			enabled: $auto,
			interval: $speed,
			direction: '$direction'
		},
		navigation: {
			next: '#ui-carousel-next-".$ID."', 
			prev: '#ui-carousel-prev-".$ID."'
		},
		width:$width,
		height:$height
	});		
	jQuery('#ui-carousel-next-".$ID."')
		.add('#ui-carousel-prev-".$ID."')
		.hide();
});
";		
}

$document->addScriptDeclaration($js);
if($imgTopPadding) {
	$TopPad = $imgTopPadding.'px';
	$document->addStyleDeclaration("
	#carousel-$ID .wrapper img {
		padding-top:$TopPad;
	}
	");
}
$document->addStyleDeclaration("
#carousel-".$ID." .wrapper a {line-height:".$height."px;text-align:center;vertical-align:middle;}
");
if($borderColor) {
	$new_height = $height - 2;
	$new_width = $width - 2;
	$style1="#carousel-".$ID." .wrapper a {line-height:".$new_height."px;height:".$new_height."px !important;width:".$new_width."px !important;border:1px solid #".$borderColor.";}";	
	$document->addStyleDeclaration($style1);
}
if($backgroundColor) {
	$style2="#carousel-".$ID." .wrapper a {background-color:#".$backgroundColor.";}";	
	$document->addStyleDeclaration($style2);
}
if($logoRadius) {
	$style3="#carousel-".$ID." .wrapper a {border-radius:".$logoRadius."px;}";	
	$document->addStyleDeclaration($style3);
}

require(JModuleHelper::getLayoutPath($module->module));
?>