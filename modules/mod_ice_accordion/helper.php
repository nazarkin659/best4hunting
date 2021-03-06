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

if(file_exists(JPATH_SITE.DS.'components'.DS.'com_jshopping'.DS.'lib'.DS.'factory.php'))
{
	require_once(JPATH_SITE.DS.'components'.DS.'com_jshopping'.DS.'lib'.DS.'factory.php');
	 JSFactory::loadLanguageFile();
}
if(file_exists(JPATH_SITE.DS.'components'.DS.'com_jshopping'.DS.'lib'.DS.'functions.php'))
{
	require_once(JPATH_SITE.DS.'components'.DS.'com_jshopping'.DS.'lib'.DS.'functions.php');
}

require_once JPATH_SITE.DS.'components'.DS.'com_content'.DS.'helpers'.DS.'route.php';

if( !class_exists('IceAccordionGroupBase') ){

	require_once( dirname(__FILE__).DS.'libs'.DS.'group_base.php' );
}

abstract class modIceAccordion {
	
	/**
	 * get list articles
	 */
	public static function getList( $params ){
		if ( $params->get('enable_cache') ) {
			$cache =& JFactory::getCache('mod_ice_accordion');
			$cache->setCaching( true );
			$cache->setLifeTime( $params->get( 'cache_time', 15 ) * 60 );	
			return $cache->get( array( 'modIceAccordion' , 'getGroupObject' ), array( $params ) ); 
		} else {
			return self::getGroupObject( $params );
		}	
	}
	public static function getSocialMenuId(){
		$db = &JFactory::getDBO();
		$query = "SELECT id FROM #__menu WHERE alias='jomsocial' AND link LIKE '%option=com_community%' LIMIT 0,1";
		$db->setQuery($query);
		$itemid = $db->loadResult();
		return $itemid;
	}
	/**
	 * get list articles
	 */
	public static function getGroupObject( $params ){
		$group = $params->get( 'data_source', 'content' );
		$file = dirname(__FILE__).DS.'libs'.DS.trim($group).'.php';	
		if( file_exists($file) ){
			require_once( $file );
			$className = 'IceGroupAccordion'.ucfirst($group);
			if( class_exists($className) ){
				$object = new $className( $group );					
				$object->setCurrentPath(  dirname(__FILE__).DS.'libs'.DS.'groups'.DS.strtolower($group).DS );							
			
			}
		}
		if( $object ){
			return $object->getListByParameters( $params );	
		} else {
			return array();
		}
	}
	

	/**
	 * load css - javascript file.
	 * 
	 * @param JParameter $params;
	 * @param JModule $module
	 * @return void.
	 */
	public static function loadMediaFiles( $params, $module, $theme='' ){
		$mainframe = &JFactory::getApplication();
		// if the verion is equal 1.6.x
		JHTML::_('behavior.mootools');
		JHTML::script( 'modules/'.$module->module.'/assets/script_16.js');
		JHTML::stylesheet( 'modules/'.$module->module.'/assets/style.css' );
		if( $theme && $theme != -1 ){
			$tPath = JPATH_BASE.DS.'templates'.DS.$mainframe->getTemplate().DS.'css'.DS.$module->module.'_'.$theme.'.css';
			if( file_exists($tPath) ){
				JHTML::stylesheet( 'templates/'.$mainframe->getTemplate().'/css/'.$module->module.'_'.$theme.'.css');
			} else {
				JHTML::stylesheet('modules/'.$module->module.'/themes/'.$theme.'/assets/style.css');	
			}
		}
		else{
			$tPath = JPATH_BASE.DS.'templates'.DS.$mainframe->getTemplate().DS.'html'.DS.$module->module.DS.'assets'.DS.'style.css';
			if( file_exists($tPath) ){
				JHTML::stylesheet( 'templates/'.$mainframe->getTemplate().'/html/'.$module->module.'/assets/style.css');
			} else {
				JHTML::stylesheet( 'modules/'.$module->module.'/assets/style.css' );	
			}
		}
		// load js of modalbox
		if( $params->get('load_jslibs','modal') && !defined('LOF_ADDED_MODALBOX') && $params->get('open_target','')== 'modalbox' ){
				$doc =& JFactory::getDocument();
				$string  = '<script type="text/javascript">';
				$string .= "
					var box = {};
					window.addEvent('domready', function(){
						box = new MultiBox('mb', {  useOverlay: false,initialWidth:1000});
					});
				";
				$string .= '</script>';
				$doc->addCustomTag( $string );
				JHTML::stylesheet( 'modules/'.$module->module.'/assets/multibox/multibox.css');
				JHTML::script( 'modules/'.$module->module.'/assets/multibox/multibox.js');
				JHTML::script( 'modules/'.$module->module.'/assets/multibox/overlay.js');
		}
	}
	
	/**
	 *
	 */
	public function renderItem( &$row, $params, $layout='_item' ){
		$target = $params->get('open_target','_parent') != 'modalbox'
							? 'target="'.$params->get('open_target','_parent').'"'
							: 'rel="'.$params->get('modal_rel','width:800,height:350').'" class="mb"'; 
							
		$path = dirname(__FILE__).DS.'themes'.DS.$params->get('theme').DS;
		if( file_exists($path.$params->get('group').$layout.'.php') ){
			require( $path.$params->get('group').$layout.'.php' );
			return ;
		}
		require( $path.$layout.'.php' );
	}
	
	/**
	 * load theme
	 */
	public static function getLayoutByTheme( $module, $theme= '', $layout1=""){
		$mainframe = &JFactory::getApplication();
		$layout = 'default';
		if( $theme ) {
			$layout = trim($theme).'_default';
		}
		if( !empty($layout1)){
			$layout = $layout1;
		}
		// Build the template and base path for the layout
		$tPath = JPATH_BASE.DS.'templates'.DS.$mainframe->getTemplate().DS.'html'.DS.$module->module.DS.$layout.'.php';
		$bPath = JPATH_BASE.DS.'modules'.DS.$module->module.DS.'tmpl'.DS.$layout.'.php';

		// If the template has a layout override use it
		if (file_exists($tPath)) {
			return $tPath;
		} elseif( file_exists($bPath) ) {
			return $bPath;
		}
		return JPATH_BASE.DS.'modules'.DS.$module->module.DS.'themes'.DS.$theme.DS.'default.php';
	}
	public static function getPaging( $numb_page = 1, $current_page = 1)
	{
		$str = '<div class="iceaccordion-paging">';
		$str .= '<div class="iceaccordion-loading" style="display: none;"></div>';
		$str .= ' <div class="iceaccordion-prev"><span>'.JText::_("Prev").'</span></div>
		<div class="iceaccordion-next"><span>'.JText::_("Next").'</span></div>';
		
		$str .= ' <ul class="iceaccordion-numbers">';
		if(!empty($numb_page)){
			for( $i = 1; $i <= $numb_page; $i++){
				if($i == $current_page){
					$str .= '<li class="active">'.$i.'</li>';
				}
				else{
					$str .= '<li class="">'.$i.'</li>';
				}
			}
		}
		$str .='</ul>';
		$str .= '</div>';
		return $str;
	}
	public static function checkIceAjax(){
		$type = JRequest::getVar("type","");
		$module_id = JRequest::getVar("moduleId",0);
		$layout = JRequest::getVar("layout","");
		$page = JRequest::getVar("p", 1);
		$layouts = array("_items","_products","_properties","_virtuemart","_expautos");
		if(!empty($module_id) && in_array($layout, $layouts) && $type == "ice_accordion"){
			return true;
		}
		return false;
	}
	 public static function checkActualCat( $module_id=-1, $params = array()){
		$source = trim($params->get( 'source_from', 'cat_ids' ) );
		$item_layout = "_items";
		$group 			= $params->get( 'data_source','content' );
		if($group == "joomshopping"){
			$item_layout = "_products";
		}
		elseif($group == "virtuemart"){
			$item_layout = "_virtuemart";
		}
		if($source =="actual_cat"){
			$controller = JRequest::getCmd('controller','');
			$task = JRequest::getCmd('task','');
			$product_id = JRequest::getCmd('product_id',0);
			$category_id = JRequest::getCmd('category_id', 0);
			$moduleId = JRequest::getInt("moduleId",0);
			$layout = JRequest::getVar("layout","");
			if( ( ($controller == "product" || $controller == "category") && $task == "view" && ( !empty($product_id) || !empty($category_id) ) ) || ( $moduleId == $module_id && $layout == $item_layout)){
				return true;
			}
			else{
				return false;
			}
		}
		elseif($group =="virtuemart" && $source == "actual_cat"){
			$option = JRequest::getCmd('option','');
			$view = JRequest::getCmd('view','');
			$product_id = JRequest::getCmd('virtuemart_product_id',0);
			$category_id = JRequest::getCmd('virtuemart_category_id', 0);
			$moduleId = JRequest::getInt("moduleId",0);
			$layout = JRequest::getVar("layout","");
			if( ( ($option == "com_virtuemart" &&( $view == "productdetails" || $view== "categories"))  && ( !empty($product_id) || !empty($category_id) ) ) || ( $moduleId == $module_id && $layout == $item_layout)){
				return true;
			}
			else{
				return false;
			}
		}
		return true;
	}
	public static function getAjaxData( $params = array()){
		$string = "";
		$source = trim($params->get( 'source_from', 'cat_ids' ) );
		if($source =="actual_cat"){
			$controller = JRequest::getCmd('controller','');
			$task = JRequest::getCmd('task','');
			$product_id = JRequest::getCmd('product_id',0);
			$category_id = JRequest::getCmd('category_id', 0);
			if( ($controller == "product" || $controller == "category") && $task == "view" && ( !empty($product_id) || !empty($category_id) )){
				$string = ',controller:"'.$controller.'", task:"'.$task.'", product_id:'.$product_id.',category_id:'.$category_id;
			}
		}
		return $string;
	}
	function addtocart($product, $showQuantity=true) {
            if (!VmConfig::get('use_as_catalog',0)) { ?>
            <div class="addtocart-area">

			<form method="post" class="product" action="index.php">
			<?php
				// Product custom_fields
			?>

                <div class="addtocart-bar">

			<?php
               // Display the quantity box
				if($showQuantity){
            ?>
			<span class="quantity-box">
			<input type="text" class="quantity-input" name="quantity[]" value="1" />
			</span>
			<span class="quantity-controls">
			<input type="button" class="quantity-controls quantity-plus" />
			<input type="button" class="quantity-controls quantity-minus" />
			</span>
			<?php
			}
            // Add the button
			$button_lbl = JText::_('COM_VIRTUEMART_CART_ADD_TO');
			$button_cls = '';
			// Display the add to cart button
			$stockhandle = VmConfig::get('stockhandle','none');
			if(($stockhandle=='disableit' or $stockhandle=='disableadd') and ($product->product_in_stock - $product->product_ordered)<1){
				$button_lbl = JText::_('COM_VIRTUEMART_CART_NOTIFY');
				$button_cls = 'notify-button';
				$button_name = 'notifycustomer';
			}
			?>
			<?php /* Display the add to cart button*/ ?>
			<span class="addtocart-button">
				<input type="submit" name="addtocart"  class="addtocart-button" value="<?php echo $button_lbl ?>" title="<?php echo $button_lbl ?>" />
			</span>

                    <div class="clear"></div>
                    </div>

                    <input type="hidden" class="pname" value="<?php echo $product->product_name ?>"/>
                    <input type="hidden" name="option" value="com_virtuemart" />
                    <input type="hidden" name="view" value="cart" />
                    <noscript><input type="hidden" name="task" value="add" /></noscript>
                    <input type="hidden" name="virtuemart_product_id[]" value="<?php echo $product->virtuemart_product_id ?>" />
                    <input type="hidden" name="virtuemart_category_id[]" value="<?php echo $product->virtuemart_category_id ?>" />
                </form>
				<div class="clear"></div>
            </div>
        <?php }
     }
}
?>
