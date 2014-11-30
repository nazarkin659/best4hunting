<?php
/**
 * IceAccordion Extension for Joomla 2.5 By IceTheme
 * 
 * 
 * @copyright	Copyright (C) 2008 - 2012 IceTheme.com. All rights reserved.
 * @license		GNU General Public License version 2
 * 
 * @Website 	http://www.icetheme.com/Joomla-Extensions/iceaccordion.html
 * @Support 	http://www.icetheme.com/Forums/IceAccordion/
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
if( !modIceAccordion::checkActualCat( $module->id, $params) ){
	$module->showtitle=0;
	//echo JText::_("THIS_PAGE_IS_NOT_PRODUCT_DETAIL");
}
else{
	$list = modIceAccordion::getList( $params );
	if(empty($list)){
		echo JText::_("THIS_CATE_DONT_HAVE_PRODUCTS");
	}
	else{
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
		if (!isset($GLOBALS['add_iceaccordion_toggler'])) { $GLOBALS['add_iceaccordion_toggler'] = 1; } else { $GLOBALS['add_iceaccordion_toggler']++; }
		if (!isset($GLOBALS['add_iceaccordion_content'])) { $GLOBALS['add_iceaccordion_content'] = 1; } else { $GLOBALS['add_iceaccordion_content']++; }


		// Variables
		$mod_url  		 								= JURI::base() . 'modules/mod_ice_accordion/';
		$style           								= $params->get('style', 'default');

		$isThumb       = $params->get( 'auto_renderthumb',1);
		$itemContent= $isThumb==1?'desc-image':'introtext';
		$iceaccordion_toggler   						= 'iceaccordion_toggler_' . $GLOBALS['add_iceaccordion_toggler'];
		$iceaccordion_content   						= 'iceaccordion_content_' . $GLOBALS['add_iceaccordion_content'];
		$iceaccordion_activecolor   					= $params->get('iceaccordion_activecolor', '222');
		$iceaccordion_inactivecolor   					= $params->get('iceaccordion_inactivecolor', '888');
		$item_heading = $params->get('item_heading', 3);
		/*Paging*/
		$module_id = JRequest::getVar("moduleId",0);
		$layout = JRequest::getVar("layout","");
		$page = JRequest::getVar("p", 1);
		$tmp_number_page = $params->get('number_page', '1');

		$number_show_on_page = count( $list ) / $tmp_number_page;
		$number_show_on_page = round( $number_show_on_page );
		if( empty($number_show_on_page)){
			$number_show_on_page = 1;
		}
		$countlist = count( $list );
		$limitstart = $number_show_on_page * ( $page - 1 );
		$limit = $number_show_on_page * $page;
		$limit = $limit > $countlist? $countlist: $limit;
		$number_page = count( $list ) / $number_show_on_page;
		$number_page = round( $number_page );
		$number_page = $number_page > (int) $tmp_number_page?$tmp_number_page: $number_page;
		$pagin = "";
		
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
		
		$mainWidth    = (int)$params->get( 'main_width', 200 );
		$mainWidth = $mainWidth !="auto"?(int)$mainWidth."px":$mainWidth;
		$mainHeight   = (int)$params->get( 'main_height', 210 );
		$mainHeight = $mainHeight !="auto"?(int)$mainHeight."px":$mainHeight;
		
		$previewWidth    = (int)$params->get( 'preview_width', 200 );
		$previewWidth = $previewWidth !="auto"?(int)$previewWidth."px":$previewWidth;
		$previewHeight   = (int)$params->get( 'preview_height', 210 );
		$previewHeight = $previewHeight !="auto"?(int)$previewHeight."px":$previewHeight;
		$hideArrows = $params->get('hide_arrows', 0);
		$hideArrows = $hideArrows == 1?true:false;
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
		
		$js_source_from = $params->get("js_source_from","users");
		$item_layout = "_items";
		$tmpl_layout = $item_layout;
		if($group == "joomshopping"){
			$item_layout = $tmpl_layout = "_products";
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
			$previewWidth    = (int)$params->get( 'k2_preview_width', 200 );
			$previewWidth = $previewWidth !="auto"?(int)$previewWidth."px":$previewWidth;
			$previewHeight   = (int)$params->get( 'k2_preview_height', 210 );
			$previewHeight = $previewHeight !="auto"?(int)$previewHeight."px":$previewHeight;
		}
		elseif($group == "jomsocial"){
			$item_layout = "_jomsocial";
			$tmpl_layout = $item_layout."_".$js_source_from;
			$Itemid = modIceAccordion::getSocialMenuId();
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
			$settings           = ipropertyAdmin::config();
			$item_layout = $tmpl_layout = "_properties";
		}elseif($group == "expautos"){
			$item_layout = $tmpl_layout = "_expautos";
			
		}
		if($params->get('number_page', '1') > 1){
			$paging = modIceAccordion::getPaging( $number_page, $page);
		}
		else{
			$paging = "";
		}
		/*End Paging*/
		if(modIceAccordion::checkIceAjax() ){
			$lang_tag = JRequest::getVar("lang","en-GB");
			$lang =& JFactory::getLanguage();
			$lang->load( "mod_ice_accordion",JPATH_SITE, $lang_tag, true );
			$lang =& JFactory::getLanguage();
			$lang->load( "com_k2",JPATH_SITE, $lang_tag, true );
		}
		
		$item_path = modIceAccordion::getLayoutByTheme($module, $theme, $tmpl_layout);
		if( $module_id == $module->id && $layout == $item_layout ){
			require_once( $item_path );
		}
		else{
			$lang =& JFactory::getLanguage();
			$lang_tag =  $lang->getTag();
			// load custom theme
			if( $theme && $theme != -1 ) {
				require( modIceAccordion::getLayoutByTheme($module, $theme) );
			} else {
				require( JModuleHelper::getLayoutPath($module->module) );	
			}
			?>
			<script type="text/javascript">
				window.addEvent('load', function() {
				var start_index<?php echo $module->id; ?> = <?php echo $params->get("default_item", -9);?>;
				start_index<?php echo $module->id; ?> = start_index<?php echo $module->id; ?> - 1;
				var current_page = <?php echo (int) $page;  ?>;
				var number_page = <?php echo (int) $number_page; ?>;
				var ajax_url = "<?php echo JURI::base()."modules/mod_ice_accordion/ajax.php?moduleId=".$module->id."&layout=".$item_layout."&type=ice_accordion&lang=".$lang_tag;?>";
				var listpage = $("iceaccordion<?php echo $module->id; ?>").getElements(".iceaccordion-paging li");
				
				function addPagingEvent<?php echo $module->id; ?>(){
					if(listpage && number_page > 1){
						listpage.each( function(el, index){
							el.addEvent("click", function( event ){
									event.stop();
									if( (index+1) != current_page){
										getAccordion( index + 1 );
										updateActiveClass( listpage, index );
									}
								});
						});
						if( $("iceaccordion<?php echo $module->id; ?>").getElement(".iceaccordion-prev") ){
							$("iceaccordion<?php echo $module->id; ?>").getElement(".iceaccordion-prev").addEvent( "click", function( event ){
								index = current_page - 1;
								if(index > 0){
									getAccordion( index );
									updateActiveClass( listpage, index -1);
								}
								
							});
						}
						if( $("iceaccordion<?php echo $module->id; ?>").getElement(".iceaccordion-next") ){
							$("iceaccordion<?php echo $module->id; ?>").getElement(".iceaccordion-next").addEvent( "click", function( event ){
								index = current_page + 1;
								if(index <= number_page){
									getAccordion( index );
									updateActiveClass( listpage, index - 1  );
								}
							});
						}
						updateClassArrows( current_page );
					}
					
				}
				function getAccordion( nextpage ){
					updateClassArrows( nextpage );
					var blockHidden = $("iceaccordion<?php echo $module->id; ?>").getElement(".iceaccordion-hidden");
					if( $(blockHidden).getElement(".page-"+nextpage)){
						var htmlHidden = $(blockHidden).getElement(".page-"+nextpage).get("html");
						current_page = nextpage; 
						$("iceaccordion<?php echo $module->id; ?>").getElement(".iceaccordion").set("html", htmlHidden);
						var myAccordion<?php echo $module->id; ?> = new Accordion( $("iceaccordion<?php echo $module->id; ?>").getElement(".iceaccordion"), '#iceaccordion<?php echo $module->id; ?> .toggler', '#iceaccordion<?php echo $module->id; ?> .accordion_content', {
							opacity: false,
							alwaysHide:true,
							display: start_index<?php echo $module->id; ?>,
							onActive: function(toggler, element){
								toggler.addClass('open');
							},
							onBackground: function(toggler, element){
								toggler.removeClass('open');
							}
						});
						imageAccordionPreview( $("iceaccordion<?php echo $module->id; ?>") );
					}
					else{
						$("iceaccordion<?php echo $module->id; ?>").getElement(".iceaccordion-loading").setStyle("display","block");
						var req =new Request({
										  method: 'get',
										  url: ajax_url,
										  data: { 'p' : nextpage <?php echo modIceAccordion::getAjaxData( $params ); ?> },
										  onComplete: function(response) {
												$("iceaccordion<?php echo $module->id; ?>").getElement(".iceaccordion-loading").setStyle("display","none");
												if(response !=""){
														current_page = nextpage; $("iceaccordion<?php echo $module->id; ?>").getElement(".iceaccordion").set("html", response);
														var myAccordion<?php echo $module->id; ?> = new Accordion( $("iceaccordion<?php echo $module->id; ?>").getElement(".iceaccordion"), '#iceaccordion<?php echo $module->id; ?> .toggler', '#iceaccordion<?php echo $module->id; ?> .accordion_content', {
															opacity: false,
															alwaysHide:true,
															display: start_index<?php echo $module->id; ?>,
															onActive: function(toggler, element){
																toggler.addClass('open');
															},
															onBackground: function(toggler, element){
																toggler.removeClass('open');
															}
														});
														imageAccordionPreview( $("iceaccordion<?php echo $module->id; ?>") );
														$(blockHidden).set("html", $(blockHidden).get("html")+'<div class="page-'+nextpage+'">'+response+'</div>');
														<?php
														if($group == "virtuemart"){ ?>
														if(typeof Virtuemart != 'undefined'){
															jQuery(".product").each(function(){
																var cart = jQuery(this);
																plus   = cart.find('.quantity-plus');
																minus  = cart.find('.quantity-minus');
																addtocart = cart.find('input.addtocart-button');
																addtocart.unbind("click");
																plus.unbind("click");
																minus.unbind("click");
															});
															
															Virtuemart.product(jQuery(".product"));
														}
														else if(typeof jQuery != 'undefined')
															jQuery(".product").product();
														<?php 
															}
														?>
												}
												
											}
										}).send();
					}
				}
				function updateClassArrows( currentIndex ){
					var prev = $("iceaccordion<?php echo $module->id; ?>").getElement(".iceaccordion-prev");
					var next = $("iceaccordion<?php echo $module->id; ?>").getElement(".iceaccordion-next");
					if( currentIndex == 1){
						prev.addClass("disabled");
						next.removeClass("disabled");
					}
					else if( currentIndex == number_page ){
						prev.removeClass("disabled");
						next.addClass("disabled");
					}
					else{
						prev.removeClass("disabled");
						next.removeClass("disabled");
					}
					
				}
				function updateActiveClass( listpage, nextpage ){
					
					if( listpage[ nextpage ] ){
						listpage.removeClass("active");
						listpage[ nextpage ].addClass("active");
					}
					
				}
				
				imageAccordionPreview( $("iceaccordion<?php echo $module->id; ?>") );
			
				addPagingEvent<?php echo $module->id; ?>();
				
				$$("#iceaccordion<?php echo $module->id; ?> .iceaccordion").each( function( item ){
						var myAccordion<?php echo $module->id; ?> = new Accordion( item, '#iceaccordion<?php echo $module->id; ?> .toggler', '#iceaccordion<?php echo $module->id; ?> .accordion_content', {
							opacity: false,
							alwaysHide:true,
							display: start_index<?php echo $module->id; ?>,
							onActive: function(toggler, element){
								toggler.addClass('open');
							},
							onBackground: function(toggler, element){
								toggler.removeClass('open');
							}
						});
					} );
				});
			</script>
			<?php
				modIceAccordion::loadMediaFiles( $params, $module, $theme );
		}
		if(!empty($paging) && modIceAccordion::checkIceAjax() ){
			//exit();
		}
	}
}
?>
