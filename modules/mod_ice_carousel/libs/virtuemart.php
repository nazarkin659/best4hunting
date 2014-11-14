<?php
// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
// 
//Load virtuemart needed files
if(!defined('VIRTUEMART_PATH')){
	define('VIRTUEMART_PATH', JPATH_ADMINISTRATOR.DS.'components'.DS.'com_virtuemart');
}
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
/**
 * $ModDesc
 * 
 * @version   $Id: helper.php $Revision
 * @package   modules
 * @subpackage  $Subpackage
 * @copyright Copyright (C) May 2010 LandOfCoder.com <@emai:landofcoder@gmail.com>. All rights reserved.
 * @website   htt://landofcoder.com
 * @license   GNU General Public License version 2
 */
if( !class_exists('IceGroupCarouselVirtuemart') ) {
	/**
   * IceGroupCarouselJoomshopping Class  extends the LofSliderGroupBase Class
   */   
   class IceGroupCarouselVirtuemart extends IceGroupBase{
    
    /**
     * @var string $__name 
     *
     * @access private;
     */
    var $__name = 'virtuemart';
    
    /**
     * @var static $regex is a pattern using for maching imag tag.
     * 
     * @access public.
     */
    static $regex = "#<img.+src\s*=\s*\"([^\"]*)\"[^\>]*\>#iU"; 
    
    /**
     * override get List of Item by the module's parameters
     */
    public function getListByParameters( $params ){
      if( !IceGroupCarouselVirtuemart::isVmExisted() ){
        return array();
      }
      return $this->__getList( $params );
    }
    
    
    /**
     * check virtuemart Existed ?
     */
    public function isVmExisted(){
      return is_file( JPATH_SITE.DS.  "components" . DS . "com_virtuemart" . DS . "virtuemart.php" ); 
    }
    
	function getOrdering( $string = ""){
		$ordering = "product_id";
		$dir = "ASC";
		if( !empty($string )){
				$tmp = explode("__", $string);	
				$ordering = $string;
				if( count($tmp) == 2){
					$ordering = $tmp[0];
					$dir = $tmp[1];
				}
		}
		
		$order["ordering"] = $ordering;
		$order["dir"] = $dir;	
		return $order;
	}
    /**
     * get the list of virtuemart items
     * 
     * @param JParameter $params;
     * @return Array
     */
    public function __getList( $params ){
	
	  if (!class_exists( 'VmConfig' )) require( VIRTUEMART_PATH.DS.'helpers'.DS.'config.php');
	  if(class_exists( 'VmConfig' ))	VmConfig::loadConfig();

      $mainframe = &JFactory::getApplication();
	  $noimage = "noimage.gif";
	  $order_complate_id = "C";
	  
      $titleMaxChars      = $params->get( 'title_max_chars', '100' );
      $openTarget    = $params->get( 'open_target', 'parent' );
      $descriptionMaxChars = $params->get( 'description_max_chars', 100 );
      $condition     = $this->buildConditionQuery( $params );

      $ordering      = $params->get( 'vm_sort_product', 'date_asc');
      $limit         = $params->get( 'limit_items',  5 );
      $orders      = $this->getOrdering( $ordering );
	  $dir = $orders["dir"];
	  $ordering = $orders["ordering"];
      $my          = &JFactory::getUser();
      $aid         = $my->get( 'aid', 0 );
      $thumbWidth    = (int)$params->get( 'vm_preview_width', 200 );
      $thumbHeight   = (int)$params->get( 'vm_preview_height', 210 );
      $imageHeight   = (int)$params->get( 'main_height', 300 ) ;
      $imageWidth    = (int)$params->get( 'main_width', 660 ) ;
	  $show_preview = $params->get( 'vm_show_preview', 1);
      $isThumb       = $params->get( 'auto_renderthumb',1);
	  $show_feature = $params->get("vm_show_feature", 2);
	  $image_quanlity = $params->get('image_quanlity', 100);
      $isStripedTags = $params->get( 'auto_strip_tags', 0 );
      $extraURL     = $params->get('open_target')!='modalbox'?'':'&tmpl=component'; 
      $db     = &JFactory::getDBO();
      $date   =& JFactory::getDate();
      $now    = $date->toMySQL();
		
		$query_join = "";
		$query_filed = "";
		/*Use this code for check ordering*/
		switch( $ordering ){
			case 'date':
				$ordering = 'p.`modified_on` '.$dir;
			break;
			case 'price':
				$ordering = "pp.product_price ".$dir;
			break;
			case 'name':
				$ordering = "l.`product_name` ".$dir;
			break;
			case 'most_sold':
				$ordering = " op.product_quantity DESC";
				$query_join .= " LEFT JOIN `#__virtuemart_order_items` AS op ON op.virtuemart_product_id = p.virtuemart_product_id AND op.order_status= '".$order_complate_id."'";
				$query_filed .=" ,op.virtuemart_order_id";
			break;
			case 'latest_sold':
				$ordering = " oh.created_on DESC";
				$query_join .= " LEFT JOIN `#__virtuemart_order_items` AS op ON op.virtuemart_product_id = p.virtuemart_product_id ";
				$query_join .= " LEFT JOIN `#__virtuemart_order_histories` AS oh ON oh.virtuemart_order_id = op.virtuemart_order_id AND oh.order_status_code ='".$order_complate_id."' ";
				$query_filed .=" ,op.virtuemart_order_id";
			break;
			case 'most_rate':
				$ordering = " r.rates DESC";
				$query_join .=" LEFT JOIN #__virtuemart_ratings AS r ON r.virtuemart_product_id=p.virtuemart_product_id";
			break;
			case 'random':
				$ordering = " RAND()";
			break;
		}
		if(!empty($ordering)){
			$ordering = " ORDER BY ".$ordering;
		}
		$where = array();
		$where[] = ' p.`published`="1" ';
		if($show_feature==2){
			$where[] = ' p.`product_special` = 1 ';
		}
		elseif($show_feature == 0){
			$where[] = ' p.`product_special` = 0 ';
		}
		$groupBy = 'group by p.`virtuemart_product_id`';
		$where[] = $this->buildConditionQuery($params);
		$select = ' * '.$query_filed.' FROM `#__virtuemart_products_'.VMLANG.'` as l';
		$joinedTables = ' JOIN `#__virtuemart_products` AS p using (`virtuemart_product_id`)';
		
		$source = trim($params->get( 'vm_source_from', 'cat_ids' ) );
		$joinedTables .= ' LEFT JOIN `#__virtuemart_product_categories` AS pr_cat ON p.`virtuemart_product_id` = pr_cat.`virtuemart_product_id`
			 LEFT JOIN `#__virtuemart_categories_'.VMLANG.'` as c ON c.`virtuemart_category_id` = pr_cat.`virtuemart_category_id`';

		if ($source == "vm_manufactures") {
			$joinedTables .= ' LEFT JOIN `#__virtuemart_product_manufacturers` AS pm ON p.`virtuemart_product_id` = pm.`virtuemart_product_id`
			 LEFT JOIN `#__virtuemart_manufacturers_'.VMLANG.'` as m ON m.`virtuemart_manufacturer_id` = pm.`virtuemart_manufacturer_id` ';
		}
		$joinedTables .= ' LEFT JOIN `#__virtuemart_product_prices` as pp ON p.`virtuemart_product_id` = pp.`virtuemart_product_id` ';
		$joinedTables .= $query_join;
		if(count($where)>0){
			$whereString = ' WHERE ('.implode(' AND ', $where ).') ';
		} else {
			$whereString = '';
		}
		/*End*/
	
		$productModel = VmModel::getModel('Product');
	    //and the where conditions
		$joinedTables .= $whereString .$groupBy .$ordering ;

		$q = 'SELECT '.$select.$joinedTables;
		$db->setQuery($q,0,$limit);

	   $product_ids= $db->loadResultArray();
	   $products = $productModel->getProducts($product_ids, true, true, false,false);
	 
	   $productModel->addImages($products);
	
      if( empty($products) ) return array();
	  $currency = CurrencyDisplay::getInstance( );
	  $check = array();
	  $data = array();
      foreach( $products as $key => $item ){
		if(in_array($item->virtuemart_product_id, $check)){
			continue;
		}
        $item->link = JRoute::_('index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id='.$item->virtuemart_product_id.'&virtuemart_category_id='.$item->virtuemart_category_id);
        $item->date = JHtml::_('date', $item->modified_on, JText::_('DATE_FORMAT_LC2'));
        $item->title = $item->product_name;
        $item->subtitle = self::substring( $item->title, $titleMaxChars );
        $item->description = self::substring( $item->product_s_desc, $descriptionMaxChars,true);
		$item->product_price = $currency->createPriceDiv('salesPrice','',$item->prices,true);
		$item->product_old_price = $currency->createPriceDiv('basePrice','',$item->prices,true);
		$item->product_full_image = isset($item->images[0]->file_url)?$item->images[0]->file_url: $noimage;
        $item->mainImage = $item->product_full_image;
		$item->thumbnail =  $item->mainImage;
		$item->introtext = $item->description;
		$item->image_label = "";
		$item->review = "";
		$item->product_full_image = $item->mainImage;
		
        if( $item->mainImage &&  $image=$this->renderThumb($item->mainImage, $imageWidth, $imageHeight, $item->product_name, $isThumb, $image_quanlity ) ){
          $item->mainImage = $image;
        }
		if( $show_preview ){
			
			if( $item->thumbnail &&  $image = $this->renderThumb($item->thumbnail, $thumbWidth, $thumbHeight, $item->product_name, $isThumb, $image_quanlity, true ) ){
			  $item->thumbnail = $image;
			}
			
		}
		$check[] = $item->product_id;
		$data[] = $item;
      }
      return $data; 
    }
    
    /**
     * build condition query base parameter  
     * 
     * @param JParameter $params;
     * @return string.
     */
    public  function buildConditionQuery( $params ){
      $source = trim($params->get( 'vm_source_from', 'cat_ids' ) );
	  $filter_type = $params->get( 'vm_filtering_type','1');
	
      if( $source == 'vm_cat_ids' ){
        $catids = $params->get( 'vmcat_ids','');
        $catids = !is_array($catids) ? $catids : implode(",",$catids);
		if( empty( $catids ) ){
          return '';
        }
		if($filter_type){
			$condition = '  pr_cat.virtuemart_category_id IN( '.$catids.' )';
		}
		else{
			$condition = '  pr_cat.virtuemart_category_id NOT IN( '.$catids.' )';
		}
      }
	  elseif( $source == "vm_product_ids"){
		 $ids = explode(',',$params->get( 'vmproduct_ids',''));  
        $tmp = array();
        foreach( $ids as $id ){
          $tmp[] = (int) trim($id);
        }
		if($filter_type){
			$condition = '  p.virtuemart_product_id IN( '.implode( ",", $tmp ).' )';
		}
		else{
			$condition = '  p.virtuemart_product_id NOT IN( '.implode(",", $tmp).' )';
		}
	  }
	  elseif( $source == "vm_manufactures"){
		$manufactures = $params->get("vmmanufactures", "");
		if(empty($manufactures)){
			return "";
		}
		if($filter_type){
			$condition = ' m.virtuemart_manufacturer_id IN( '.$manufactures.' )';
		}
		else{
			$condition = ' m.virtuemart_manufacturer_id NOT IN( '.$manufactures.' )';
		}
	  }
	  else {
		$option = JRequest::getCmd('option','');
		$view = JRequest::getCmd('view','');
		$product_id = JRequest::getCmd('virtuemart_product_id',0);
		$category_id = JRequest::getCmd('virtuemart_category_id', 0);
		if( $option=="com_virtuemart" && ($view == "productdetails" || $view == "category") && ( !empty($category_id) || !empty($product_id))){
			$db = &JFactory::getDBO();
			if(!empty($product_id)){
				$query = "SELECT virtuemart_category_id FROM #__virtuemart_product_categories WHERE virtuemart_product_id=".$product_id;
				$db->setQuery($query);
				$rows = $db->loadObjectList();
				if(is_array( $rows)){
					$tmp = array();
					foreach($rows as $row){
						$tmp[] = (int) $row->virtuemart_category_id;
					}
				}
			}
			else{
				$tmp[] = $category_id;
				/*Select children category ids*/
				$query = "SELECT category_parent_id, category_child_id FROM #__virtuemart_category_categories AS cc
								 ORDER BY category_parent_id, ordering";
				$db->setQuery($query);
				$all_cats = $db->loadObjectList();
				
				if(count($all_cats)) {
					foreach ($all_cats as $key => $value) {
						if(!empty( $value->category_parent_id ) && in_array($value->category_parent_id, $tmp)){
							$tmp[] = $value->category_child_id;
						}
					}
				}
			}
			if($filter_type){
				$condition = ' pr_cat.virtuemart_category_id IN( '.implode(",",$tmp).' ) AND p.virtuemart_product_id <>'.$product_id;
			}
			else{
				$condition = ' pr_cat.virtuemart_category_id NOT IN( '.implode(",",$tmp).' ) AND p.virtuemart_product_id <>'.$product_id;
			}
		}
		else{
			return "";
		}
      }
      return $condition;
    }
  }
}
?>
