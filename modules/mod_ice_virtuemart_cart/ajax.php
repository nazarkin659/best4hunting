<?php

/**
 * IceAccordion Extension for Joomla 1.6 By IceTheme
 * 
 * 
 * @copyright	Copyright (C) 2008 - 2011 IceTheme.com. All rights reserved.
 * @license		GNU General Public License version 2
 * 
 * @Website 	http://www.icetheme.com/Joomla-Extensions/iceaccordion.html
 * @Support 	http://www.icetheme.com/Forums/IceAccordion/
 *
 */
if (!defined ('_JEXEC')) {
	define( '_JEXEC', 1 );
	$path = dirname(dirname(dirname(__FILE__)));
	define('JPATH_BASE', $path );
	if (strpos(php_sapi_name(), 'cgi') !== false && !empty($_SERVER['REQUEST_URI'])) {
		//Apache CGI
		$_SERVER['PHP_SELF'] =  rtrim(dirname(dirname($_SERVER['PHP_SELF'])), '/\\');
	} else {
		//Others
		$_SERVER['SCRIPT_NAME'] =  rtrim(dirname(dirname($_SERVER['SCRIPT_NAME'])), '/\\');
	}
	
	define( 'DS', DIRECTORY_SEPARATOR );
	require_once ( JPATH_BASE .DS.'includes'.DS.'defines.php' );
	require_once ( JPATH_BASE .DS.'includes'.DS.'framework.php' );
	JDEBUG ? $_PROFILER->mark( 'afterLoad' ) : null;

	/**
	 * CREATE THE APPLICATION
	 *
	 * NOTE :
	 */
	$mainframe = JFactory::getApplication('site');
	
	/**
	 * INITIALISE THE APPLICATION
	 *
	 * NOTE :
	 */
	$mainframe->initialise(array(
		'language' => $mainframe->getUserState( "application.lang", 'lang' )
	));
	JPluginHelper::importPlugin('system');
	
	// trigger the onAfterInitialise events
	JDEBUG ? $_PROFILER->mark('afterInitialise') : null;
	
	//$mainframe->triggerEvent('onAfterInitialise');
	// Route the application.
}
	
	/*Load  module language file*/
	$lang =& JFactory::getLanguage();
	$lang->load( "mod_ice_virtuemart_cart" );
	
	
	jimport('joomla.application.component.model');
	if(!defined('VIRTUEMART_PATH')){
		define('VIRTUEMART_PATH', JPATH_ADMINISTRATOR.DS.'components'.DS.'com_virtuemart');
	}
	if (!class_exists( 'VmConfig' )) require( VIRTUEMART_PATH.DS.'helpers'.DS.'config.php');
	if(class_exists( 'VmConfig' ))	VmConfig::loadConfig();

	if(!class_exists('VirtueMartCart')) require(JPATH_VM_SITE.DS.'helpers'.DS.'cart.php');

	$task = JRequest::getVar('task');
	
	$dropdown = JRequest::getVar('dropdown', 1);
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
			$data->products[$key]["product_name"] = $product->product_name;
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
			if(strpos( $data->products[$key]["image"], "http") === FALSE && strpos($data->products[$key]["image"], "https") === FALSE){
				$data->products[$key]["image"] = JURI::base().$data->products[$key]["image"];
			}
		}
	}
}

	?>
	<div class="lof_vm_top">
       
       <?php if( count( $data->products) == 0): ?>
         <p class="vm_cart_empy"><?php print JText::_('EMPTY_CART')?></p>
         
         <?php else:?>
           
        <div class="lof_top_1">
			<span class="vm_products"><?php echo  $data->totalProductTxt ?>&nbsp;</span>
            <span class="vm_sum"><?php if ($data->totalProduct) echo  $data->billTotal; ?></span>
		</div>
        
		<div class="lof_top_2">
	    	<?php if ($data->totalProduct) echo  $data->cart_show; ?>
			<?php if($dropdown){ ?>
				<?php if( count( $data->products) == 0): ?>
					<span class="vm_readmore"><?php print JText::_('SHOW_MORE')?></span>
				<?php else:?>
					<a class="vm_readmore showmore" href = "javascript:void(0)"><?php print JText::_('SHOW_MORE')?></a>
				<?php endif; ?>
			<?php } ?>
		</div>
        
       
        <?php endif; ?>
			
		</div>
	</div>
	<?php
		if($dropdown){
		
	?>
	<div class="lof_vm_bottom" style="display:none;">
		<?php 
		foreach ($data->products as $product){
		$product["image"] = JString::str_ireplace( 'modules/mod_ice_virtuemart_cart/', '', $product["image"]);
		?><div style="clear:both;"></div> 
		<div class="lof_item">
			<a href="<?php echo $product["link"];?>"><img src="<?php echo $product["image"]; ?>" alt="<?php print htmlspecialchars($product['product_name']);?>"/></a>
			<div class="lof_info">
				<a href = "<?php echo $product["link"]; ?>" title = "<?php print htmlspecialchars($product['product_name']);?>">
					<?php print htmlspecialchars($product['product_name']);?>
				</a>
				<span class="lof_quantity"><?php print JText::_('QUANTITY')?> : <?php echo  $product['quantity'] ?></span>
				<span class="lof_price"><?php print JText::_('PRICE')?> : <?php echo  $product['prices'] ?></span>
			</div>
		</div>
		<?php }?>
		<div class="lof_vm_bottom_btn">
			<?php if ($data->totalProduct) echo  $data->cart_show; ?>
			<a class="lofclose" href = "javascript:void(0)"><?php print JText::_('CLOSE')?></a>
		</div>
	</div>
	<?php } ?>
	<script language="javascript" type="text/javascript">
		jQuery(document).ready(function(){
			setTimeout(function(){ 
					jQuery('.lof_vm_bottom').slideUp();
			}, 3000);
			setTimeout(function(){ 
					jQuery('.lof_vm_bottom').slideDown("slow");
			}, 100);
			<?php if($dropdown){ ?>
			jQuery('.lof_vm_top .showmore').click(function(){
				if(jQuery(this).hasClass('showmore')){
					jQuery('.lof_vm_bottom').slideDown("slow");
					jQuery(this).text('<?php print JText::_('SHOW_LESS')?>');
					$(this).removeClass('showmore').addClass('showless');
				}else{
					jQuery('.lof_vm_bottom').slideUp();
					jQuery(this).text('<?php print JText::_('SHOW_MORE')?>');
					$(this).removeClass('showless').addClass('showmore');
				}
			});
			jQuery('.lof_vm_bottom_btn .lofclose').click(function(){
				jQuery('.lof_vm_bottom').slideUp();
				jQuery('.lof_vm_top .lof_top_2 .showless').text('<?php print JText::_('SHOW_MORE')?>');
				jQuery('.lof_vm_top .lof_top_2 .showless').removeClass('showless').addClass('showmore');
			});
			<?php } ?>
			jQuery('#main').find('.vm table.cart').addClass("cart-full");
			
		});	
	</script>
	<?php  ?>