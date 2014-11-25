<?php
/**
 * IceCarousel Extension for Joomla 2.5 By IceTheme
 * 
 * 
 * @copyright	Copyright (C) 2008 - 2012 IceTheme.com. All rights reserved.
 * @license		GNU General Public License version 2
 * 
 * @Website 	http://www.icetheme.com/Joomla-Extensions/icecarousel.html
 * @Support 	http://www.icetheme.com/Forums/IceCarousel/
 *
 */
 

/* no direct access*/
defined('_JEXEC') or die;

if( !defined('PhpThumbFactoryLoaded') ) {
  require_once dirname(__FILE__).DS.'libs'.DS.'phpthumb'.DS.'ThumbLib.inc.php';
  define('PhpThumbFactoryLoaded',1);
}

// Include the syndicate functions only once
require_once dirname(__FILE__).DS.'helper.php';
if( !modIceCarousel::checkActualCat( $module->id, $params) ){
	$module->showtitle=0;
	//echo JText::_("THIS_PAGE_IS_NOT_PRODUCT_DETAIL");
}
else{
$list = modIceCarousel::getList( $params );
$jshopConfig = null;
if(class_exists( "JSFactory")){
	$jshopConfig = &JSFactory::getConfig();
	JSFactory::loadCssFiles();
}

$group 			= $params->get( 'data_source','content' );
$tmp 		 	= $params->get( 'module_height', 'auto' );
$moduleHeight   =  ( $tmp=='auto' ) ? 'auto' : (int)$tmp.'px';
$tmp 			= $params->get( 'module_width', 'auto' );
$moduleWidth    =  ( $tmp=='auto') ? 'auto': (int)$tmp.'px';
$themeClass 	= $params->get( 'theme' , '');
$openTarget 	= $params->get( 'open_target', 'parent' );
$class 			= !$params->get( 'navigator_pos', 0 ) ? '':'ice-'.$params->get( 'navigator_pos', 0 );
$theme		    =  $params->get( 'theme', '' );
$target = $params->get('open_target','_parent') != 'modalbox'
							? 'target="'.$params->get('open_target','_parent').'"'
							: 'rel="'.$params->get('modal_rel','width:800,height:350').'" class="mb"'; 
//Allow multiplie Id's
if (!isset($GLOBALS['add_icecarousel_toggler'])) { $GLOBALS['add_icecarousel_toggler'] = 1; } else { $GLOBALS['add_icecarousel_toggler']++; }
if (!isset($GLOBALS['add_icecarousel_content'])) { $GLOBALS['add_icecarousel_content'] = 1; } else { $GLOBALS['add_icecarousel_content']++; }


// Variables
$mod_url  		 								= JURI::base() . 'modules/mod_ice_accordion/';
$style           								= $params->get('style', 'default');

$isThumb       = $params->get( 'auto_renderthumb',1);
$itemContent= $isThumb==1?'desc-image':'introtext';
$icecarousel_toggler   						= 'icecarousel_toggler_' . $GLOBALS['add_icecarousel_toggler'];
$icecarousel_content   						= 'icecarousel_content_' . $GLOBALS['add_icecarousel_content'];
$icecarousel_activecolor   					= $params->get('icecarousel_activecolor', '222');
$icecarousel_inactivecolor   					= $params->get('icecarousel_inactivecolor', '888');
/*Paging*/

$maxPages = (int)$params->get( 'max_items_per_page', 3 );
$pages = array_chunk( $list, $maxPages  );
$totalPages = count($pages);
// calculate width of each row.
$itemWidth = 100/$maxPages -0.1;
$isAjax = $params->get('enable_ajax', 0 );
$item_heading = $params->get('item_heading');
$tmp = $params->get( 'module_height', 'auto' );
$moduleHeight   =  ( $tmp=='auto' ) ? 'auto' : (int)$tmp.'px';
$tmp = $params->get( 'module_width', 'auto' );
$moduleWidth    =  ( $tmp=='auto') ? 'auto': (int)$tmp.'px';
/*Paging*/
$module_id = JRequest::getVar("moduleId",0);
$layout = JRequest::getVar("layout","");
$page = JRequest::getVar("p", 1);
$tmp_number_page = $maxPages;
$limitstart	= 0;

/*k2 config*/
$show_k2_preview_image = $params->get("show_k2_preview_image", 0);
/*Joom shopping config*/
$show_preview = $params->get("show_preview",1);
$show_image_label = $params->get("show_image_label",1);
$show_rating = $params->get("show_rating",1);
$show_product_image = $params->get("show_product_image",1);
$show_description = $params->get("show_description",1);
$show_old_price = $params->get("show_old_price",1);
$show_price = $params->get("show_price",1);
$js_source_from = $params->get("js_source_from","users");
/*Virtuemart config*/
$vm_show_preview = $params->get("vm_show_preview",1);
$vm_show_image_label = $params->get("vm_show_image_label",1);
$vm_show_rating = $params->get("vm_show_rating",1);
$vm_show_product_image = $params->get("vm_show_product_image",1);
$vm_show_description = $params->get("vm_show_description",1);
$vm_show_base_price = $params->get("vm_show_base_price",1);
$vm_show_price = $params->get("vm_show_sale_price",1);
$vm_show_quantity = $params->get("vm_show_quantity",1);
$vm_show_feature_icon = $params->get("vm_show_feature_icon",1);
$vm_show_current_stock = $params->get("vm_show_current_stock",1);
$vm_show_cart = $params->get("vm_show_cart",1);
$vmpreviewWidth    = (int)$params->get( 'vm_preview_width', 200 );
$vmpreviewWidth = $vmpreviewWidth !="auto"?(int)$vmpreviewWidth."px":$vmpreviewWidth;
$vmpreviewHeight   = (int)$params->get( 'vm_preview_height', 210 );
$vmpreviewHeight = $vmpreviewHeight !="auto"?(int)$vmpreviewHeight."px":$vmpreviewHeight;
/*EXP Autos*/
$exp_display_icons = $params->get("exp_display_icons",1);
$exp_only_images = $params->get("exp_only_images",1);
$exp_show_sold = $params->get("exp_show_sold",1);
$exp_show_reserved = $params->get("exp_show_reserved",1);
$exp_show_year = $params->get("exp_show_year",1);
$exp_show_fuel = $params->get("exp_show_fuel",1);
$exp_show_mileage = $params->get("exp_show_mileage",1);
$exp_show_price = $params->get("exp_show_price",1);
$exp_show_preview = $params->get("exp_show_preview",1);
$exppreviewWidth    = (int)$params->get( 'exp_preview_width', 200 );
$exppreviewWidth = $exppreviewWidth !="auto"?(int)$exppreviewWidth."px":$exppreviewWidth;
$exppreviewHeight   = (int)$params->get( 'exp_preview_height', 210 );
$exppreviewHeight = $exppreviewHeight !="auto"?(int)$exppreviewHeight."px":$exppreviewHeight;
$item_layout = "_items";
$tmpl_layout = $item_layout;
$link_all = "";
if($group == "joomshopping"){
	$item_layout  = $tmpl_layout = "_products";
}
elseif($group == "virtuemart"){
	$item_layout  = $tmpl_layout = "_virtuemart";
	if ($vm_show_cart) {
		vmJsApi::jQuery();
		vmJsApi::jPrice();
		vmJsApi::cssSite();
	}
}
elseif($group == "k2"){
	// Get component params
	$componentParams = & JComponentHelper::getParams('com_k2');
	$tmpl_layout = $item_layout = "_k2items";
}
elseif($group == "jomsocial"){
	$item_layout = "_jomsocial";
	$tmpl_layout = $item_layout."_".$js_source_from;
	$Itemid = modIceCarousel::getSocialMenuId();
	$Itemid = !empty($Itemid)?$Itemid:JRequest::getInt("Itemid", 0);
	switch($js_source_from){
		case "users":
			if($params->get("show_all_members",0)){
				$link_all = '<a href="'.CRoute::_('index.php?option=com_community&view=search&task=advancesearch&field0=username&condition0=contain&value0=&fieldType0=text&operator=and&key-list=0&Itemid='.$Itemid).'">'.JText::_("ALL_MEMBERS").'</a>';
			}
		break;
		case "photos":
			if($params->get("show_all_photo_link",0)){
				$link_all = '<a href="'.CRoute::_("index.php?option=com_community&view=photos").'">'.JText::_("ALL_PHOTOS").'</a>';
			}
		break;
		case "groups":
			if($params->get("show_all_group_link", 0)){
				$link_all = '<a href="'.CRoute::_("index.php?option=com_community&view=groups").'">'.JText::_("ALL_GROUPS").'</a>';
			}
		break;
		case "videos":
			if($params->get("show_video_all_link",0)){
				$link_all = '<a href="'.CRoute::_("index.php?option=com_community&view=videos").'">'.JText::_("ALL_VIDEOS").'</a>';
			}
		break;
		case "events":
			if($params->get("show_event_all_link", 0)){
				$link_all = '<a href="'.CRoute::_("index.php?option=com_community&view=events").'">'.JText::_("ALL_EVENTS").'</a>';
			}
		break;
	}
}
elseif($group == "ip"){
	if( file_exists(JPATH_SITE.DS.'components'.DS.'com_iproperty'.DS.'iproperty.php'))
	{
		$settings           = ipropertyAdmin::config();
		$item_layout  = $item_layout = "_properties";
	}
	else{
		$totalPages = 0;
	}
}
elseif($group == "expautos"){
	$item_layout = $tmpl_layout = "_expautos";
}
/*End Paging*/
if(modIceCarousel::checkIceAjax() ){
	$lang_tag = JRequest::getVar("lang","en-GB");
	$lang =& JFactory::getLanguage();
	$lang->load( "mod_ice_carousel",JPATH_SITE, $lang_tag, true );
}
$itemLayoutPath = modIceCarousel::getLayoutByTheme($module, $theme, $tmpl_layout);
if( $totalPages > 0){

	if( $module_id == $module->id && $layout == $item_layout ){
		require_once( $itemLayoutPath );
	}
	else{
		// load custom theme'
		$lang =& JFactory::getLanguage();
		$lang_tag =  $lang->getTag();
		if( $theme && $theme != -1 ) {
			require( modIceCarousel::getLayoutByTheme($module, $theme) );
		} else {
			require( JModuleHelper::getLayoutPath($module->module) );	
		}
		modIceCarousel::loadMediaFiles( $params, $module, $theme );
		?>
		 <script type="text/javascript">
			var _icemain =  $('icecarousel<?php echo $module->id;?>'); 
			var object = new IceCarousel ( _icemain,
											  { 
												  fxObject:{
													transition:<?php echo $params->get("transition","Fx.Transitions.Back.easeInOut"); ?>,  
													duration:<?php echo $params->get("duration", 1000); ?>								 },
												  startItem:0,
												  interval:<?php echo $params->get("interval", 5000); ?>,
												  direction :'hrleft', 
												  navItemHeight:<?php echo $params->get('navitem_height', 32) ?>,
												  navItemWidth:<?php echo $params->get('navitem_width', 32) ?>,
												  navItemsDisplay:3,
												  navPos:'<?php echo $params->get( 'navigator_pos', 0 ); ?>',
												  nextButton: '.ice-next',
												  datagroup: '<?php echo $group; ?>',
												  prevButton:  '.ice-previous'
												  <?php 
													if($isAjax ){
														echo ',isAjax:true';
														echo ',url:"'.JURI::base()."modules/mod_ice_carousel/ajax.php?moduleId=".$module->id."&lang=".$lang_tag."&layout=".$item_layout.'&type=ice_carousel"';
														echo ',maxItemSelector:'.$totalPages;
													}
												  ?>
											  } );
					object.registerButtonsControl( 'click', {next:_icemain.getElement('.ice-next'),
															 previous:_icemain.getElement('.ice-previous')} );
					object.start( <?php echo $params->get('auto_start',1)?>, null );
			</script>
		<?php
	}
	if( modIceCarousel::checkIceAjax() ){
		//exit();
	}
}
}
?>
