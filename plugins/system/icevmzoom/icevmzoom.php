<?php
/**
 * IceVmZoom Plugin for Joomla 2.5 By IceTheme
 * 
 * 
 * @copyright	Copyright (C) 2008 - 2012 IceTheme.com. All rights reserved.
 * @license		GNU General Public License version 2
 * 
 * @Website ://www.icetheme.com/Joomla-Extensions/IceSlideshow.html
 * @Support 	http://www.icetheme.com/Forums/IceVmZoom/
 *
 */
// no direct access
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.plugin.plugin' );
jimport( 'joomla.form.form' );
jimport( 'joomla.application.component.model');
jimport('joomla.application.component.controller');
 
if( !defined('PhpThumbFactoryLoaded') ) {
  require_once dirname(__FILE__).DS.'libs'.DS.'phpthumb'.DS.'ThumbLib.inc.php';
  define('PhpThumbFactoryLoaded',1);
}

error_reporting(E_ALL & ~E_NOTICE);
// 

 /**
  * plgSystemLofcontentmart Class
  */
class plgSystemIcevmzoom extends JPlugin {
    
    // Associative array to hold results of the plugin
    var $type_cart = "cart"; //cart,wishlist
    var $products = array();
    var $count_product = 0;
    var $price_product = 0;
    var $summ = 0;
    var $rabatt_id = 0;
    var $rabatt_value = 0;
    var $rabatt_type = 0;
    var $rabatt_summ = 0;
    var $pluginName = 'lofdeals';
	var $isComInstalled = false;
    var $allowed = false;
	var $zoom_type = "innerzoom";
    
	/**
	 * Constructor 
	 */
	function plgSystemIcevmzoom(&$subject, $config) {
		parent::__construct($subject, $config);
        $this->isComInstalled=$this->isInstalled();
        $enable = $this->params->get("enable", 1);
		
        if( $enable ) {
            $this->allowed = true;
        } 
	}
    
	/**
	 * Check the component is installed or not?
	 */
    function isInstalled() {
        jimport('joomla.filesystem.file');
        return JFile::exists(JPATH_SITE.DS.'components'.DS.'com_virtuemart'.DS.'virtuemart.php');
    }
    public function onBeforeRender(){
		// Check that we are in the site application.
		if (JFactory::getApplication()->isAdmin())
		{
			return true;
		}
		if(  !$this->isComInstalled || !$this->allowed ){ return ; }
		$option = JRequest::getVar("option","");
		$view = JRequest::getVar("view","");
		$product_id = JRequest::getInt("virtuemart_product_id", 0);
        if( $option == "com_virtuemart" && $view == "productdetails" && !empty($product_id)){
			if (!class_exists( 'VmConfig' )) require(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_virtuemart'.DS.'helpers'.DS.'config.php');
			VmConfig::loadConfig();
			// Load the language file of com_virtuemart.
			JFactory::getLanguage()->load('com_virtuemart');
			if (!class_exists( 'calculationHelper' )) require(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_virtuemart'.DS.'helpers'.DS.'calculationh.php');
			if (!class_exists( 'CurrencyDisplay' )) require(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_virtuemart'.DS.'helpers'.DS.'currencydisplay.php');
			if (!class_exists( 'VirtueMartModelVendor' )) require(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_virtuemart'.DS.'models'.DS.'vendor.php');
			if (!class_exists( 'VmImage' )) require(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_virtuemart'.DS.'helpers'.DS.'image.php');
			if (!class_exists( 'shopFunctionsF' )) require(JPATH_SITE.DS.'components'.DS.'com_virtuemart'.DS.'helpers'.DS.'shopfunctionsf.php');
			if (!class_exists( 'calculationHelper' )) require(JPATH_COMPONENT_SITE.DS.'helpers'.DS.'cart.php');
			if (!class_exists( 'VirtueMartModelProduct' )){
			   JLoader::import( 'product', JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_virtuemart' . DS . 'models' );
			}
			 
			  $loadJquery = $this->params->get("load_jquery", 1);
			  $this->zoom_type = $this->params->get("zoom_type", "standard");
			  $image_small_width = (int)$this->params->get("image_small_width", 200);
			  $image_small_height = (int)$this->params->get("image_small_height", 200);
			  $image_thumb_width = (int)$this->params->get("image_thumb_width", 60);
			  $image_thumb_height = (int)$this->params->get("image_thumb_height", 50);
			  $zoombox_width = (int)$this->params->get("zoombox_width", 300);
			  $zoombox_height = (int)$this->params->get("zoombox_height", 300);
			  $image_large_width = (int)$this->params->get("image_large_width", 120);
			  $image_large_height = (int)$this->params->get("image_large_height", 120);
			  $image_resize = (int)$this->params->get("is_resize", true);
			  $productModel = VmModel::getModel('Product');
			  $product = $productModel->getProductSingle( $product_id );
			  $productModel->addImages($product);
			  $images = isset($product->images)?$product->images:array();
			  if( !defined("_LOADED_ICEVM_MEDIA") ){
				define("_LOADED_ICEVM_MEDIA",1);
				$document = &JFactory::getDocument();
				//if($loadJquery){
				  $document->addCustomTag( '
						<script type="text/javascript" src="'.JURI::base().'plugins/system/icevmzoom/assets/jquery-1.6.js'.'"></script>
					' );
				//}
				if( !defined("_ICEZOOM") ) {
				   $document->addCustomTag( '
						<script type="text/javascript" src="'.JURI::base().'plugins/system/icevmzoom/assets/jqzoom.js'.'"></script>
					' );
					define( "_ICEZOOM",1 );
				}
				$document->addStyleDeclaration( '.prod_attr_img img{ display:none;}' );
				$productimages = array();
				if(!empty($images)){
					foreach($images as $imgobj){
						$tmpobj = new stdClass;
						$tmpobj->small = $imgobj->file_url_thumb;
						$tmpobj->medium = $imgobj->file_url;
						$tmpobj->large = $imgobj->file_url;
						if($image = $this->renderThumb($tmpobj->medium,$image_small_width, $image_small_height, "", true, 100, true, $image_resize)){
							$tmpobj->medium = $image;
						}
						if($image2 = $this->renderThumb($imgobj->file_url,$image_thumb_width, $image_thumb_height)){
							$tmpobj->small = $image2;
						}
						$productimages[] = $tmpobj;
					}
				}
				$jsconfig = "";
				 ob_start();
				 ?>
				 
				 <script language="javascript" type="text/javascript">
						jQuery(document).ready(function() {
						var schema=window.location.protocol.replace(":","");
						console.log(schema);
						<?php
							if(strpos($productimages[0]->large,schema) === FALSE){
								$productimages[0]->large = JURI::base().$productimages[0]->large;
							}
							if(strpos($productimages[0]->medium,schema) === FALSE){
								$productimages[0]->medium = JURI::base().$productimages[0]->medium;
							}
						?> 
							var mainimage_html = '<a href="<?php echo isset($productimages[0])?$productimages[0]->large:'';?>" class="jqzoom" rel=\'gal1\'  title="<?php echo isset($product->product_name)?$product->product_name:''; ?>" >';
							mainimage_html +='<img src="<?php echo isset($productimages[0])?$productimages[0]->large:'' ?>"  title="<?php echo isset($product->product_name)?$product->product_name:''; ?>" class="iceproductimage"></a>';
							var thumblist_html = '<ul id="thumblist" class="clearfix" >';
							<?php
							 if(!empty($productimages)){
								foreach($productimages as $key=>$img){
									$class = "";
									if($key == 0) $class= "zoomThumbActive";
									/* if(strpos($img->medium,schema) === FALSE){
										$img->medium = JURI::base().$img->medium;
									}
									if(strpos($img->large,schema) === FALSE){
										$img->large = JURI::base().$img->large;
									}
									if(strpos($img->small,schema) === FALSE){
										$img->small = JURI::base().$img->small;
									} */
									?>
									thumblist_html +='<li><a class="<?php echo $class; ?>" href=\'javascript:void(0);\' rel="{gallery: \'gal1\', smallimage: \'<?php echo $img->medium;?>\',largeimage: \'<?php echo $img->large;?>\'}"><img src=\'<?php echo $img->small;?>\'></a></li>';
									<?php
								}
							 } 
							?>
							thumblist_html +='</ul>';
							var main_image_obj = jQuery('<?php echo $this->params->get("mainimage_wrapper",".main-image"); ?>');
							var add_image_obj = jQuery('<?php echo $this->params->get("thumblist_wrapper",".additional-images"); ?>');
							if(typeof(main_image_obj) !='undefined'){
								main_image_obj.html( mainimage_html );
							}
							if(typeof(add_image_obj) !='undefined'){
								add_image_obj.html( thumblist_html );
							}
							jQuery('.jqzoom').jqzoom({
									zoomType: '<?php echo $this->zoom_type;?>',
									zoomWidth: <?php echo (int)$zoombox_width; ?>,
									zoomHeight: <?php echo (int)$zoombox_height; ?>,
									lens:<?php echo $this->params->get("enable_lens",1) == '1'?'true':'false';?>,
									preloadImages: <?php echo $this->params->get("preload_images",1) == '1'?'true':'false'; ?>,
									alwaysOn:<?php echo $this->params->get("always_on",0) == '1'?'true':'false';?>,
									 xOffset: <?php echo (int)$this->params->get("xoffset", 10);?>,
									//zoomWindow x offset, can be negative(more on the left) or positive(more on the right)
									yOffset: <?php echo (int)$this->params->get("xoffset", 0);?>,
									//zoomWindow y offset, can be negative(more on the left) or positive(more on the right)
									position: "<?php echo $this->params->get("zoom_position", "right");?>",
									//image preload
									preloadText: "<?php echo $this->params->get("preload_text", "Loading zoom");?>", 
									title: <?php echo $this->params->get("enable_title",1) == '1'?'true':'false';?>,
									imageOpacity: <?php echo $this->params->get("image_opacity", 0.4);?>,
									showEffect: "<?php echo $this->params->get("show_effect", "show");?>", 
									//show/fadein
									hideEffect: "<?php echo $this->params->get("hide_effect", "hide");?>", 
									//hide/fadeout
									fadeinSpeed: <?php echo (int)$this->params->get("fadein_speed", "4000");?>, 
									//fast/slow/number
									fadeoutSpeed: <?php echo (int)$this->params->get("fadeout_speed", "2000");?>
								});
						});
				 </script>
				 <?php
				$jsconfig = ob_get_clean();
				$document->addCustomTag( '<link rel="stylesheet" type="text/css" href="'.JURI::base().'plugins/system/icevmzoom/assets/style.css"/>' );
				$document->addCustomTag( $jsconfig );
			}
		}
	}
   /**
	* looing for a image in content of article or k2
	*/
   function getImage($str){
		$regex = "/\<img.+src\s*=\s*\"([^\"]*)\"[^\>]*\>/";
		$matches = array();
		preg_match ( $regex, $str , $matches );  
		$images = (count($matches)) ? $matches : array ();
		$image = count($images) > 1 ? $images[1] : '';
		
		return $image;
	}
	 /**
     *  check the folder is existed, if not make a directory and set permission is 755
     *
     * @param array $path
     * @access public,
     * @return boolean.
     */
     function renderThumb( $path, $width=100, $height=100, $title='', $isThumb=true, $image_quanlity = 100, $returnPath = true, $resize=false ){
      if( !preg_match("/.jpg|.png|.gif/",strtolower($path)) ) return '&nbsp;';

      if( $isThumb ){
		if(empty($image_quanlity)){
			$image_quanlity = 100;
		}
        $path = str_replace( JURI::base(), '', $path );		$uribase = JURI::root( true );		$path = empty($uribase)?$path:str_replace( JURI::root( true )."/", '', $path);
        $imagSource = JPATH_SITE.DS. str_replace( '/', DS,  $path );
        if( file_exists($imagSource) ) {
		  if($resize){
			$path =  $width."x".$height.'/'.$image_quanlity.'_resize/'.$path;
		  }
		  else{
			$path =  $width."x".$height.'/'.$image_quanlity.'_unresize/'.$path;
		  }
          $thumbPath = JPATH_SITE.DS.'images'.DS.'icethumbs'.DS. str_replace( '/', DS,  $path );
          if( !file_exists($thumbPath) ) {
            $thumb = PhpThumbFactory::create( $imagSource  );
			$thumb->setOptions( array('jpegQuality'=> $image_quanlity) );
            if( !$this->makeDir( $path ) ) {
                return '';
            }   
            $thumb->adaptiveResize( $width, $height, $resize);
             
            $thumb->save( $thumbPath  ); 
          }
          $path = JURI::base().'images/icethumbs/'.$path;
        } 
      }
	  if( $returnPath ){
		return $path;
	  }
	  else{
		return '<img src="'.$path.'" title="'.$title.'" alt="'.$title.'" width="'.$width.'px" height="'.$height. 'px" />';
	  }
    }
	/**
     *  check the folder is existed, if not make a directory and set permission is 755
     *
     * @param array $path
     * @access public,
     * @return boolean.
     */
     function makeDir( $path ){
      $folders = explode ( '/',  ( $path ) );
      $tmppath =  JPATH_SITE.DS.'images'.DS.'icethumbs'.DS;
      if( !file_exists($tmppath) ) {
        JFolder::create( $tmppath, 0755 );
      }; 
      for( $i = 0; $i < count ( $folders ) - 1; $i ++) {
        if (! file_exists ( $tmppath . $folders [$i] ) && ! JFolder::create( $tmppath . $folders [$i], 0755) ) {
          return false;
        } 
        $tmppath = $tmppath . $folders [$i] . DS;
      }   
      return true;
    }
   
}