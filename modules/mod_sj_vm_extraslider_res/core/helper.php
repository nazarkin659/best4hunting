<?php
/**
 * @package Sj Vm Extra Slider Responsive
 * @version 2.0
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @copyright (c) 2013 YouTech Company. All Rights Reserved.
 * @author YouTech Company http://www.smartaddons.com
 *
 */
defined( '_JEXEC' ) or die;

require dirname(__FILE__).'/vmloader.php';

JFactory::getLanguage()->load('com_virtuemart');

if(!class_exists('sj_vm_extrasliderres_helper')){
	class sj_vm_extrasliderres_helper  {
		protected static $helper = null;

		protected $categories = null;
		protected $children = array();
		protected $products = null;
		protected $params = null;

		public static function getList( $params, $module){
			$helper = & self::getInstance();
			return $helper->_getList( $params, $module );
		}

		protected function _getList( $params, $module ){
			if(class_exists('VmModel')) {
				$this->params = $params;
				
				$source_category = $params->get('source_category', '');
				$categories = $this->getCategories($source_category);
				$include_subcategory = $params->get('subcategories');
				if($include_subcategory == 1){
					$include_subcategory = true;
				}else{
					$include_subcategory = false;
				}
				$category_ids = array();
				for ($i = 0; $i < count($categories); $i++){
					is_object($categories[$i]) && array_push($category_ids, $categories[$i]->virtuemart_category_id);
				}
			
			
				// sort categories
				$source_category_order = $params->get('source_category_order', null);
				if ( !is_null($source_category_order) ){
					// ex
					// usort($categories, create_function('$a, $b', 'return $a->ordering > $b->ordering;'));
				}
				
				$source_order = $params->get('source_order','group.featured');
				$source_group = null;
				if(strpos($source_order, 'group.') === 0){
						$source_group = substr($source_order,6);
						$source_order = null;
				}else{
					$source_group = null;
				}
				$source_limit = $params->get('source_limit', 20);
				$show_price   = $params->get('item_price_display', 1);
				$custom = $this->_getCustomUrl($params);
				$products = $this->getProductsInCategory($category_ids, $include_subcategory, $source_group, $source_order, $source_limit, $show_price);
				if(!empty($products)){
					foreach($products as $i => $product){
						if(array_key_exists($products[$i]->virtuemart_product_id, $custom)){
								$products[$i]->link = trim($custom[$products[$i]->virtuemart_product_id]->url);
							}
							$product->images =(!empty($product->images[0]->file_url_thumb) &&  file_exists($product->images[0]->file_url))?$product->images[0]->file_url:'modules/'.YTools::getModule()->module.'/assets/images/nophoto.png';
						}
						/* YTools::dump($products); */
					return $products;
				}
			}
		}
		
		private function _getCustomUrl($params, $key = 'id') {
			$custom = array();
			$params_custom = $params->get('custom');
			if (isset($params_custom)){
				if (count($params_custom)){
					foreach($params_custom as $obj){
						if (is_array($obj)){
							$obj = JArrayHelper::toObject($obj);
						}
						if (isset($obj->$key) && !empty($obj->$key)){
							$custom[$obj->$key] = $obj;
						}
					}
				}
			}
			return $custom;
		}
		
		public function getCategory( $cid = 0 ){
			$categories = &$this->_getCategories();
			
			if ( isset($categories[$cid]) ){
				return $categories[$cid];
			}
			return false;
		}

		public function getCategories( $cids = null ){
			if ( empty($cids) || is_null($cids) ){
				$categories = &$this->_getCategories();
				return array_values($categories);
			}
			if ( is_string($cids) && preg_match('/[\s|,]+/', $cids)){
				$cids = preg_split('/[\s|,]+/', $cids);
			}
			if ( !is_array($cids) ){
				settype($cids, 'array');
			}
			$cats = array();
			for ($i = 0; $i < count($cids); $i++ ){
				$cats[] = $this->getCategory( $cids[$i] );
			}
			return $cats;
		}

		public function getChildCategories( $cid = 0 ){
			if ( isset($this->children[$cid]) ){
				$childrens = array();
				foreach ($this->children[$cid] as $child){
					if ( isset($this->children[$child]) ){
						$queue = array( $child );
						while ( count($queue) ){
							$c0 = array_shift($queue);
							array_push($childrens, $c0);
							if ( isset($this->children[$c0]) ){
								foreach (array_reverse($this->children[$c0]) as $c1){
									array_unshift($queue , $c1);
								}
							}
						}
					} else {
						array_push($childrens, $child);
					}
				}
				return $childrens;
			}
			return array();
		}

		public function getProducts( $pids = null ){
			if ( empty($pids) || is_null($pids) ){
				return array();
			}
			
			if ( is_string($pids) && preg_match('/[\s|,]+/', $pids)){
				$cids = preg_split('/[\s|,]+/', $pids);
			}
			
			if ( !is_array($pids) ){
				settype($pids, 'array');
			}
			
			$products = & $this->getLoadedProducts();
			$product_ids = array();
			for ($i = 0; $i < count($pids); $i++ ){
				if ( !isset($products[$pids[$i]]) ){
					array_push($product_ids, $pids[$i]);
				}
			}
			if ( count($product_ids) ){
				array_unique($product_ids);
				$productModel = new VirtuemartModelProductExtend();
				$productLoaded = $productModel->getProducts($product_ids);
				foreach ($productLoaded as $i => $product){
					if ( !isset($this->products[$product->virtuemart_product_id]) ){
						$this->products[$product->virtuemart_product_id] = &$productLoaded[$i];
					}
				}
			}
			
			$return_products = array();
			foreach($pids as $pid){
				if (isset($this->products[$pid])) $return_products[] = &$this->products[$pid];
			}
			
			return $return_products;
		}

		public function getProductsInCategory( $category_ids = 0, $include_sub = false, $group = null, $order = null, $limit = null, $show_price = true){
			if (!is_array($category_ids)){
				$category_ids = array($category_ids);
			}
			if ( $include_sub===true ){
				foreach ($category_ids as $cid){
					
					for(
						$i = 0,
						$child = $this->getChildCategories($cid),
						$child_count = count($child);
						$i < $child_count;
						$i++){
						array_push($category_ids, $child[$i]);
					}
					
				}
			}
			return $this->getProductsListing($group, $order, $limit, $show_price, $category_ids);
		}

		public function getProductsListing($group, $order, $limit, $show_price, $category_ids){
			if ( empty($category_ids) ){
				$category_filter = false;
			} else {
				$category_filter = true;
			}
			if(class_exists('VirtuemartModelProductExtend')){
				$productModel = new VirtuemartModelProductExtend();
				$productModel->source_order = $order;
				$loadProducts = $productModel->getProductListing( $group, $limit, $show_price, true, false, $category_filter, $category_ids );
				$productModel->addImages($loadProducts);
				$products = array();
				if ( count($loadProducts) ){
					foreach ($loadProducts as $i => $product){
						if ( !isset($this->products[$product->virtuemart_product_id]) ){
							$this->products[$product->virtuemart_product_id] = &$loadProducts[$i];
						}
						$products[] = $this->products[$product->virtuemart_product_id];
					}
				}
				return $products;
			}	
		}

		public function getLoadedProducts(){
			if ( is_null($this->products) ){
				$this->products = array();
			}
			return $this->products;
		}

			protected function _getCategories(){
			if (is_null($this->categories)){
				$categoryModel = VmModel::getModel('category');
				$categoryModel->_noLimit = true;
				$categories = $categoryModel->getCategories(true);
				if ( count($categories) ){
					$_categories = array();
					foreach ($categories as $i => $category){
						if (!isset($category->virtuemart_media_id)){
							$table = &JTable::getInstance('category_medias', 'Table', array());
							$category->virtuemart_media_id = $table->load((int)$category->virtuemart_category_id);
						}
						$_categories[$category->virtuemart_category_id] = &$categories[$i];
					}
					$this->categories = &$_categories;
					$this->_buildCategoryTree();
				} else {
					$this->categories = array();
				}
			}
			return $this->categories;
		}

		protected function _buildCategoryTree(){
			$categoryModel = VmModel::getModel('category');
			$categories = &$this->_getCategories();
			if ( count($categories) ){
				foreach ($categories as $cid => $category){
					$cid = $category->virtuemart_category_id;
					$pid = $category->category_parent_id;
					if (isset($categories[$pid])){
						if (!isset($this->children[$pid])){
							$this->children[$pid] = array();
						}
						$this->children[$pid][$cid] = $cid;
					}
				}
				return 1;
			}
			return 0;
		}

		public static function getInstance(){
			if (is_null(self::$helper)){
				$classname = __CLASS__;
				self::$helper = new $classname();
			}
			return self::$helper;
		}

	}
}

if(!class_exists('VirtuemartModelProductExtend') && class_exists('VirtueMartModelProduct')){
	class VirtuemartModelProductExtend extends VirtueMartModelProduct{
		
		/**
		 * Override virtuemart function
		 *
		 * @see VirtueMartModelProduct::sortSearchListQuery()
		 */
		
		function sortSearchListQuery($onlyPublished = true, $virtuemart_category_id = false, $group = false, $nbrReturnProducts = false){

			$app = JFactory::getApplication() ;

			$groupBy = 'group by p.`virtuemart_product_id`';

			//administrative variables to organize the joining of tables
			$joinCategory	= false;
			$joinMf			= false;
			$joinPrice		= false;
			$joinCustom		= false;
			$joinShopper	= false;
			$joinLang		= true; // test fix Patrick
			$orderBy		= ' ';

			$where			= array();
			$useCore		= true;
			
			if ($this->searchplugin !== 0){
				// reset generic filters ! Why? the plugin can do it, if it wishes it.
				JPluginHelper::importPlugin('vmcustom');
				$dispatcher = JDispatcher::getInstance();
				$PluginJoinTables = array();
				$ret = $dispatcher->trigger('plgVmAddToSearch', array(&$where, &$PluginJoinTables, $this->searchplugin));
				foreach($ret as $r){
					if(!$r) $useCore = false;
				}
			}

			if($useCore){
				if ( !empty($this->keyword) and $this->keyword !=='' and $group ===false) {
					$keyword = '"%' . $this->_db->getEscaped($this->keyword, true) . '%"';

					foreach ($this->valid_search_fields as $searchField) {
						if($searchField == 'category_name' || $searchField == 'category_description'){
							$joinCategory = true;
						} else if ($searchField == 'mf_name'){
							$joinMf = true;
						} else if ($searchField == 'product_price'){
							$joinPrice = true;
						} else if (strpos($searchField, '.')== 1){
							$searchField = 'p`.`'.substr($searchField, 2, (strlen($searchField)));
						}
						$filter_search[] = '`'.$searchField.'` LIKE '.$keyword;
					}
					if ( !empty($filter_search) ){
						$where[] = implode(' OR ', $filter_search );
					} else {
						$where[] = '`product_name` LIKE '.$search;
						//If they have no check boxes selected it will default to product name at least.
					}
					$joinLang = true;
				}

				// vmdebug('my $this->searchcustoms ',$this->searchcustoms);
				if ( !empty($this->searchcustoms) ){
					$joinCustom = true ;
					foreach ($this->searchcustoms as $key => $searchcustom) {
						$custom_search[] = '(pf.`virtuemart_custom_id`="'.(int)$key.'" and pf.`custom_value` like "%' . $this->_db->getEscaped( $searchcustom, true ) . '%")';
					}
					$where[] = " ( ".implode(' OR ', $custom_search )." ) ";
				}

				if($onlyPublished){
					$where[] = ' p.`published`="1" ';
				}

				if($app->isSite() && !VmConfig::get('use_as_catalog',0) && VmConfig::get('stockhandle', 'none')=='disableit' ){
					$where[] = ' p.`product_in_stock`>"0" ';
				}

				if ( $virtuemart_category_id !== false ){
					$joinCategory = true ;
					
					if ( is_string($virtuemart_category_id) && preg_match('/[\s|,]+/', $virtuemart_category_id)){
						$virtuemart_category_id = preg_split('/[\s|,]+/', $virtuemart_category_id);
					}
					if ( !is_array($virtuemart_category_id) ){
						settype($virtuemart_category_id, 'array');
					}
					
					$where[] = ' `#__virtuemart_product_categories`.`virtuemart_category_id` IN ('.implode(',', $virtuemart_category_id).')';
				}

				if ($this->product_parent_id){
					$where[] = ' p.`product_parent_id` = '.$this->product_parent_id;
				}

				if ( $app->isSite() ) {
					if(!class_exists('VirtueMartModelUser')) require(JPATH_VM_ADMINISTRATOR.DS.'models'.DS.'user.php');
					$usermodel = VmModel::getModel('user');
					$currentVMuser = $usermodel->getUser();
					$virtuemart_shoppergroup_ids =  (array)$currentVMuser->shopper_groups;

					if(is_array($virtuemart_shoppergroup_ids)){
						foreach ($virtuemart_shoppergroup_ids as $key => $virtuemart_shoppergroup_id){
							$where[] .= '(s.`virtuemart_shoppergroup_id`= "' . (int) $virtuemart_shoppergroup_id . '" OR' . ' (s.`virtuemart_shoppergroup_id`) IS NULL )';
						}
						$joinShopper = true;
					}
				}

				if ($this->virtuemart_manufacturer_id) {
					$joinMf = true ;
					$where[] = ' `#__virtuemart_product_manufacturers`.`virtuemart_manufacturer_id` = '.$this->virtuemart_manufacturer_id;
				}

				// Time filter
				if ( $this->search_type != '' ) {
					$search_order = $this->_db->getEscaped(JRequest::getWord('search_order') == 'bf' ? '<' : '>');
					switch ($this->search_type) {
						case 'parent':
							$where[] = 'p.`product_parent_id` = "0"';
							break;
						case 'product':
							$where[] = 'p.`modified_on` '.$search_order.' "'.$this->_db->getEscaped(JRequest::getVar('search_date')).'"';
							break;
						case 'price':
							$joinPrice = true ;
							$where[] = 'pp.`modified_on` '.$search_order.' "'.$this->_db->getEscaped(JRequest::getVar('search_date')).'"';
							break;
						case 'withoutprice':
							$joinPrice = true ;
							$where[] = 'pp.`product_price` IS NULL';
							break;
					}
				}

				// special orders case
				switch ($this->source_order) {
					case 'product_special':
						$where[] = ' p.`product_special`="1" ';// TODO Change  to  a  individual button
						$orderBy = 'ORDER BY RAND()';
						break;
					case 'category_name':
						$orderBy = ' ORDER BY `category_name` ';
						$joinCategory = true ;
						break;
					case 'category_description':
						$orderBy = ' ORDER BY `category_description` ';
						$joinCategory = true ;
						break;
					case 'mf_name':
						$orderBy = ' ORDER BY `mf_name` ';
						$joinMf = true ;
						break;
					case 'ordering':
						$orderBy = ' ORDER BY `#__virtuemart_product_categories`.`ordering` ';
						$joinCategory = true ;
						break;
					case 'product_price':
						//$filters[] = 'p.`virtuemart_product_id` = p.`virtuemart_product_id`';
						$orderBy = ' ORDER BY `product_price` ';
						$joinPrice = true ;
						
						break;
					case 'created_on':
						$orderBy = ' ORDER BY p.`created_on` ';
						break;
					default ;
					if(!empty($this->source_order)){
						$orderBy = ' ORDER BY '.$this->_db->getEscaped($this->source_order).' ';
					} else {
						$this->filter_order_Dir = '';
					}
					break;
				}

				//Group case from the modules
				if($group){
					$groupBy = 'group by p.`virtuemart_product_id`';
					switch ($group) {
						case 'featured':
							$where[] = 'p.`product_special`="1" ';
							$orderBy = 'ORDER BY RAND()';
							break;
						case 'latest':
							// $date = JFactory::getDate( time()-(60*60*24*7) ); //Set on a week, maybe make that configurable
							// $dateSql = $date->toMySQL();
							// $where[] = 'p.`modified_on` > "'.$dateSql.'" ';
							$orderBy = 'ORDER BY p.`modified_on`';
							$this->filter_order_Dir = 'DESC';
							break;
						case 'random':
							$orderBy = ' ORDER BY RAND() ';//LIMIT 0, '.(int)$nbrReturnProducts ; //TODO set limit LIMIT 0, '.(int)$nbrReturnProducts;
							break;
						case 'topten';
							$orderBy = ' ORDER BY product_sales ';//LIMIT 0, '.(int)$nbrReturnProducts;  //TODO set limitLIMIT 0, '.(int)$nbrReturnProducts;
							$this->filter_order_Dir = 'DESC';
					}
					// $joinCategory	= false ; //creates error
					// $joinMf			= false ; //creates error
					$joinPrice			= true ;
					$this->searchplugin	= false ;
					// $joinLang 		= false;
				}
			}

			// write the query, incldue the tables
			// $selectFindRows = 'SELECT SQL_CALC_FOUND_ROWS * FROM `#__virtuemart_products` ';
			// $selectFindRows = 'SELECT COUNT(*) FROM `#__virtuemart_products` ';
			if( $joinLang ){
				$select = ' * FROM `#__virtuemart_products_'.VMLANG.'` as l';
				$joinedTables = ' JOIN `#__virtuemart_products` AS p using (`virtuemart_product_id`)';
			} else {
				$select = ' * FROM `#__virtuemart_products` as p';
				$joinedTables = '';
			}

			if ($joinCategory == true) {
				$joinedTables .= ' LEFT JOIN `#__virtuemart_product_categories` ON p.`virtuemart_product_id` = `#__virtuemart_product_categories`.`virtuemart_product_id`
						LEFT JOIN `#__virtuemart_categories_'.VMLANG.'` as c ON c.`virtuemart_category_id` = `#__virtuemart_product_categories`.`virtuemart_category_id`';
			}
			
			if ($joinMf == true) {
				$joinedTables .= ' LEFT JOIN `#__virtuemart_product_manufacturers` ON p.`virtuemart_product_id` = `#__virtuemart_product_manufacturers`.`virtuemart_product_id`
						LEFT JOIN `#__virtuemart_manufacturers_'.VMLANG.'` as m ON m.`virtuemart_manufacturer_id` = `#__virtuemart_product_manufacturers`.`virtuemart_manufacturer_id` ';
			}

			if ($joinPrice == true) {
				$joinedTables .= ' LEFT JOIN `#__virtuemart_product_prices` as pp ON p.`virtuemart_product_id` = pp.`virtuemart_product_id` ';
			}
			
			if ($this->searchcustoms) {
				$joinedTables .= ' LEFT JOIN `#__virtuemart_product_customfields` as pf ON p.`virtuemart_product_id` = pf.`virtuemart_product_id` ';
			}
			
			if ($this->searchplugin!==0) {
				if (!empty( $PluginJoinTables) ) {
					$plgName = $PluginJoinTables[0] ;
					$joinedTables .= ' LEFT JOIN `#__virtuemart_product_custom_plg_'.$plgName.'` as '.$plgName.' ON '.$plgName.'.`virtuemart_product_id` = p.`virtuemart_product_id` ' ;
				}
			}
			
			if ($joinShopper == true) {
				$joinedTables .= ' LEFT JOIN `#__virtuemart_product_shoppergroups` ON p.`virtuemart_product_id` = `#__virtuemart_product_shoppergroups`.`virtuemart_product_id`
						LEFT  OUTER JOIN `#__virtuemart_shoppergroups` as s ON s.`virtuemart_shoppergroup_id` = `#__virtuemart_product_shoppergroups`.`virtuemart_shoppergroup_id`';
			}

			if(count($where)>0){
				$whereString = ' WHERE ('.implode(' AND ', $where ).') ';
			} else {
				$whereString = '';
			}
			$product_ids =  $this->exeSortSearchListQuery(2, $select, $joinedTables, $whereString, $groupBy, $orderBy, $this->filter_order_Dir, $nbrReturnProducts);
		/* 	var_dump($product_ids); */
			return $product_ids;
		}
	}
}