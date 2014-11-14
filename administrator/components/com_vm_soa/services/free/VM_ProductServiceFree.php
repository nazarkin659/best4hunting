<?php
define( '_VALID_MOS', 1 );
define( '_JEXEC', 1 );

/**
 * Virtuemart Product SOA Connector (For J16 and VM2)
 * Virtuemart Product SOA Connector (Provide functions GetProductFromId, GetProductFromId, GetChildsProduct, GetProductsFromCategory)
 * The return classes are a "Product", "Currencies", "Countries" ... 
 * attributes, parent produit, child id)
 *
 * @package    com_vm_soa
 * @subpackage component
 * @author     Mickael cabanas (cabanas.mickael|at|gmail.com)
 * @copyright  2012 Mickael Cabanas
 * @license    http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version    $Id:$
 */
 
/** loading framework **/
include_once('../VM_Commons.php');

/**
 * Class Product
 *
 * Class "Product" with attribute : product_id, virtuemart_vendor_id, product_sku, product_name, product_s_desc, product_length)
 *
 * @author     Mickael cabanas (cabanas.mickael|at|gmail.com)
 * @copyright  Mickael cabanas
 * @license    http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version    Release:
 */
	class Product {
		public $product_id="";
		public $virtuemart_vendor_id="";
		public $product_parent_id="";
		public $product_sku="";
		public $product_name="";
		public $slug="";
		public $product_s_desc="";
		public $product_desc="";
		public $product_weight="";
		public $product_weight_uom="";
		public $product_length="";
		public $product_width="";
		public $product_height="";
		public $product_lwh_uom="";
		public $product_url="";
		public $product_in_stock="";
		public $low_stock_notification="";
		public $product_available_date="";
		public $product_availability="";
		public $product_special="";
		public $ship_code_id="";
		public $product_sales="";
		public $product_unit="";
		public $product_packaging="";
		public $product_ordered="";
		public $hits="";
		public $intnotes="";
		public $metadesc="";
		public $metakey="";
		public $metarobot="";
		public $metaauthor="";
		public $layout="";
		public $published="";
		public $product_categories="";
		public $manufacturer_id="";
		public $product_params="";
		public $img_uri="";
		public $img_thumb_uri="";
		public $shared="";
		public $ordering="";
		public $customtitle="";
		public $shopper_group_ids="";
		public $prices="";
		
		
		//constructeur
		/**
		 * Enter description here...
		 *
		 * @param String $name
		 * @param String $price
		 * @param String $description
		 * @param String $image
		 * @param String $product_ordered
		 * @param String $id
		 */
		function __construct($product_id, $virtuemart_vendor_id, $product_parent_id, $product_sku, $product_name, $slug, $product_s_desc, $product_desc, $product_weight
							, $product_weight_uom, $product_length, $product_width, $product_height, $product_lwh_uom, $product_url, $product_in_stock, $low_stock_notification, $product_available_date, $product_availability, $product_special
							, $ship_code_id, $product_sales, $product_unit, $product_packaging, $product_ordered, $hits, $intnotes, $metadesc, $metakey, $metarobot, $metaauthor, $layout, $published,$product_categories,$manufacturer_id,$product_params,$img_uri,$img_thumb_uri,$shared,$ordering,$customtitle,$shopper_group_ids,$prices) {
			
			$this->product_id 				= $product_id;
			$this->virtuemart_vendor_id 	= $virtuemart_vendor_id;
			$this->product_parent_id 		= $product_parent_id;
			$this->product_sku 				= $product_sku;
			$this->product_name 			= $product_name;
			$this->slug 					= $slug;
			$this->product_s_desc 			= $product_s_desc;
			$this->product_desc 			= $product_desc;
			$this->product_weight 			= $product_weight;
			$this->product_weight_uom 		= $product_weight_uom;
			$this->product_length 			= $product_length;
			$this->product_width 			= $product_width;
			$this->product_height 			= $product_height;
			$this->product_lwh_uom 			= $product_lwh_uom;
			$this->product_url 				= $product_url;
			$this->product_in_stock 		= $product_in_stock;
			$this->low_stock_notification 	= $low_stock_notification;
			$this->product_available_date 	= $product_available_date;
			$this->product_availability 	= $product_availability;
			$this->product_special 			= $product_special;
			$this->ship_code_id 			= $ship_code_id;
			$this->product_sales 			= $product_sales;
			$this->product_unit 			= $product_unit;
			$this->product_packaging 		= $product_packaging;
			$this->product_ordered 			= $product_ordered;
			$this->hits 					= $hits;
			$this->intnotes 				= $intnotes;
			$this->metadesc 				= $metadesc;
			$this->metakey 					= $metakey;
			$this->metarobot 				= $metarobot;
			$this->metaauthor 				= $metaauthor;
			$this->layout 					= $layout;
			$this->published 				= $published;
			$this->product_categories 		= $product_categories;
			$this->manufacturer_id 			= $manufacturer_id;
			$this->product_params 			= $product_params;
			$this->img_uri 					= $img_uri;
			$this->img_thumb_uri 			= $img_thumb_uri;
			$this->shared 					= $shared;
			$this->ordering 				= $ordering;
			$this->customtitle 				= $customtitle;
			$this->shopper_group_ids 		= $shopper_group_ids;
			
			$this->prices 					= $prices;
			
		}
	}


/**
 * Class OrderItemInfo
 *
 * Class "OrderItemInfo" with attribute : id, name, description, price, quantity, image, fulliamage ,
 * attributes, parent Product, child id)
 *
 * @author     Mickael cabanas (cabanas.mickael|at|gmail.com)
 * @copyright  Mickael cabanas
 * @license    http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version    Release:
 */
	class OrderItemInfo {
		
		public $order_id="";
		public $userinfo_id="";
		public $vendor_id="";
		public $product_id="";
		public $order_item_sku="";
		public $order_item_name="";
		public $product_quantity="";
		public $product_item_price="";
		public $product_final_price="";
		public $order_item_currency="";
		public $order_status="";
		public $product_attribute;
		public $created_on="";
		public $modified_on="";
		
		
		//constructeur
		/**
		 * Enter description here...
		 *
		 * @param String $name
		 * @param String $price
		 * @param String $description
		 * @param String $image
		 * @param String $fullimage
		 * @param String $id
		 */
		function __construct($order_id, $userinfo_id, $vendor_id, $product_id, $order_item_sku, $order_item_name, $product_quantity, $product_item_price, $product_final_price,$order_item_currency,$order_status,$product_attribute,$created_on,$modified_on) {
			
			$this->order_id = $order_id;
			$this->userinfo_id = $userinfo_id;
			$this->vendor_id = $vendor_id;
			$this->product_id = $product_id;
			$this->order_item_sku = $order_item_sku;
			$this->order_item_name = $order_item_name; 
			$this->product_quantity = $product_quantity;
			$this->product_item_price = $product_item_price;
			$this->product_final_price = $product_final_price;
			$this->order_item_currency = $order_item_currency;
			$this->order_status = $order_status;
			$this->product_attribute = $product_attribute;
			$this->created_on = $created_on;
			$this->modified_on = $modified_on;
			
			
		}
	}	

	/**
 * Class Currency
 *
 * Class "Currency" with attribute : id, name, code, 
 *
 * @author     Mickael cabanas (cabanas.mickael|at|gmail.com)
 * @copyright  Mickael cabanas
 * @license    http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version    Release:
 */
	class Currency {
	
		public $currency_id="";
		public $vendor_id="";
		public $currency_name="";
		public $currency_code_2="";
		public $currency_code_3="";
		public $currency_numeric_code="";
		public $currency_exchange_rate="";
		public $currency_symbol="";
		public $currency_decimal_place="";
		public $currency_decimal_symbol="";
		public $currency_thousands="";
		public $currency_positive_style="";
		public $currency_negative_style="";
		public $ordering="";
		public $shared="";
		public $published="";
		

		//constructeur
		/**
		 * Enter description here...
		 *
		 * @param String $currency_id
		 * @param String $currency_name
		 * @param String $currency_code_2
		 */
		function __construct($currency_id, $vendor_id, $currency_name, $currency_code_2, $currency_code_3, $currency_numeric_code, $currency_exchange_rate, $currency_symbol
						, $currency_decimal_place, $currency_decimal_symbol, $currency_thousands, $currency_positive_style, $currency_negative_style, $ordering, $shared, $published) {
			
			$this->currency_id 				= $currency_id;
			$this->vendor_id 				= $vendor_id;
			$this->currency_name 			= $currency_name;
			$this->currency_code_2 			= $currency_code_2;
			$this->currency_code_3 			= $currency_code_3;
			$this->currency_numeric_code 	= $currency_numeric_code;
			$this->currency_exchange_rate 	= $currency_exchange_rate;
			$this->currency_symbol 			= $currency_symbol;
			$this->currency_decimal_place 	= $currency_decimal_place;
			$this->currency_decimal_symbol 	= $currency_decimal_symbol;
			$this->currency_thousands 		= $currency_thousands;
			$this->currency_positive_style 	= $currency_positive_style;
			$this->currency_negative_style 	= $currency_negative_style;
			$this->ordering 				= $ordering;
			$this->shared 					= $shared;
			$this->published 				= $published;
			
			
		}
	}	
	
	
	/**
	 * Class ProductPrice
	 *
	 * Class "ProductPrice" with attribute : product_price_id ...
	 *
	 * @author     Mickael cabanas (cabanas.mickael|at|gmail.com)
	 * @copyright  Mickael cabanas
	 * @license    http://www.gnu.org/copyleft/gpl.html GNU/GPL
	 * @version    Release:
	 */
	class ProductPrice {
		public $product_price_id="";
		public $product_id="";
		public $product_price="";
		public $product_currency="";
		public $product_price_vdate="";
		public $product_price_edate="";
		public $created_on="";
		public $modified_on="";
		public $shopper_group_id="";
		public $price_quantity_start="";
		public $price_quantity_end="";
		public $override="";
		public $product_override_price="";
		public $product_tax_id="";
		public $product_discount_id="";
		public $product_final_price="";//calculated
		public $product_price_info="";//calculated
		

		//constructeur
		/**
		 * Enter description here...
		 *
		 * @param String $product_price_id
		 * @param String $product_id
		 * @param String $product_price
		 * ...
		 */
		function __construct($product_price_id, $product_id, $product_price,$product_currency,$product_price_vdate,$product_price_edate,
							$created_on,$modified_on,$shopper_group_id,$price_quantity_start,$price_quantity_end,$override,$product_override_price,$product_tax_id,$product_discount_id,
							$product_final_price,$product_price_info) {
			
			$this->product_price_id 		= $product_price_id;
			$this->product_id 				= $product_id;
			$this->product_price 			= $product_price;
			$this->product_currency 		= $product_currency;
			$this->product_price_vdate 		= $product_price_vdate;
			$this->product_price_edate 		= $product_price_edate;
			$this->created_on 				= $created_on;
			$this->modified_on 				= $modified_on;
			$this->shopper_group_id 		= $shopper_group_id;
			$this->price_quantity_start 	= $price_quantity_start;
			$this->price_quantity_end 		= $price_quantity_end;
			$this->override 				= $override;
			$this->product_override_price 	= $product_override_price;
			$this->product_tax_id 			= $product_tax_id;
			$this->product_discount_id 		= $product_discount_id;
			$this->product_final_price 		= $product_final_price;
			$this->product_price_info 		= $product_price_info;
		}
	}	
	
	/**
	 * Class ProductFile
	 *
	 * Class "ProductFile" with attribute : file_id ...
	 *
	 * @author     Mickael cabanas (cabanas.mickael|at|gmail.com)
	 * @copyright  Mickael cabanas
	 * @license    http://www.gnu.org/copyleft/gpl.html GNU/GPL
	 * @version    Release:
	 */
	class ProductFile {
		public $file_id="";
		public $file_product_id="";
		public $file_name="";
		public $file_title="";
		public $file_description="";
		public $file_extension="";
		public $file_mimetype="";
		public $file_url="";
		public $file_published="";
		public $file_is_image="";
		public $file_image_height="";
		public $file_image_width="";
		public $file_image_thumb_height="";
		public $file_image_thumb_width="";

		//constructeur
		/**
		 * Enter description here...
		 *
		 * @param String $file_id
		 * @param String $file_product_id
		 * @param String $file_name
		 * ...
		 */
		function __construct($file_id, $file_product_id, $file_name,$file_title,$file_description,$file_extension,$file_mimetype,$file_url,$file_published,$file_is_image,$file_image_height,$file_image_width,$file_image_thumb_height,$file_image_thumb_width) {
			$this->file_id = $file_id;
			$this->file_product_id = $file_product_id;
			$this->file_name = $file_name;
			$this->file_title = $file_title;
			$this->file_description = $file_description;
			$this->file_extension = $file_extension;
			$this->file_mimetype = $file_mimetype;
			$this->file_url = $file_url;
			$this->file_published = $file_published;
			$this->file_is_image = $file_is_image;
			$this->file_image_height = $file_image_height;
			$this->file_image_width = $file_image_width;
			$this->file_image_thumb_height = $file_image_thumb_height;
			$this->file_image_thumb_width = $file_image_thumb_width;
		}
	}	
	
	/**
	 * Class Tax
	 *
	 * Class "Tax" with attribute : tax_rate_id
	 *
	 * @author     Mickael cabanas (cabanas.mickael|at|gmail.com)
	 * @copyright  Mickael cabanas
	 * @license    http://www.gnu.org/copyleft/gpl.html GNU/GPL
	 * @version    Release:
	 */
	class Tax {
		public $tax_rate_id="";
		public $vendor_id="";
		public $tax_state="";
		public $tax_country="";
		public $mdate="";
		public $tax_rate="";

		//constructeur
		/**
		 * Enter description here...
		 *
		 * @param String $tax_rate_id
		 * @param String $vendor_id
		 * @param String $tax_state
		 */
		function __construct($tax_rate_id, $vendor_id, $tax_state,$tax_country,$mdate,$tax_rate) {
			$this->tax_rate_id = $tax_rate_id;
			$this->vendor_id = $vendor_id;
			$this->tax_state = $tax_state;
			$this->tax_country = $tax_country;
			$this->mdate = $mdate;
			$this->tax_rate = $tax_rate;
		}
	}	
	
	/**
	 * Class Discount
	 *
	 * Class "Discount" with attribute : discount_id ...
	 *
	 * @author     Mickael cabanas (cabanas.mickael|at|gmail.com)
	 * @copyright  Mickael cabanas
	 * @license    http://www.gnu.org/copyleft/gpl.html GNU/GPL
	 * @version    Release:
	 */
	class Discount {
		public $discount_id="";
		public $vendor_id="";
		public $calc_name="";
		public $calc_descr="";
		public $calc_kind="";
		public $calc_value_mathop="";
		public $calc_value="";
		public $calc_currency="";
		public $calc_shopper_published="";
		public $calc_vendor_published="";
		public $publish_up="";
		public $publish_down="";
		public $calc_qualify="";
		public $calc_affected="";
		public $calc_amount_cond="";
		public $calc_amount_dimunit="";
		public $for_override="";
		public $ordering="";
		public $shared="";
		public $published="";
		public $discount_cat_ids="";
		public $discount_countries_ids="";
		public $discount_shoppergroups_ids="";
		public $discount_states_ids="";
		

		//constructeur
		/**
		 * Enter description here...
		 *
		 * @param String $discount_id
		 * @param String $vendor_id
		 * @param String $calc_name
		 * @param String $calc_descr
		 * @param String $calc_kind
		 */
		function __construct($discount_id, $vendor_id, $calc_name, $calc_descr, $calc_kind, $calc_value_mathop, $calc_value,
							$calc_currency, $calc_shopper_published, $calc_vendor_published, $publish_up, $publish_down,
							$calc_qualify, $calc_affected, $calc_amount_cond, $calc_amount_dimunit, $for_override, 
							$ordering, $shared, $published,$discount_cat_ids,$discount_countries_ids,$discount_shoppergroups_ids,$discount_states_ids) {
			
			$this->discount_id 				= $discount_id;
			$this->vendor_id 				= $vendor_id;
			$this->calc_name 				= $calc_name;
			$this->calc_descr 				= $calc_descr;
			$this->calc_kind 				= $calc_kind;
			$this->calc_value_mathop 		= $calc_value_mathop;
			$this->calc_value 				= $calc_value;
			$this->calc_currency 			= $calc_currency;
			$this->calc_shopper_published 	= $calc_shopper_published;
			$this->calc_vendor_published 	= $calc_vendor_published;
			$this->publish_up 				= $publish_up;
			$this->publish_down 			= $publish_down;
			$this->calc_qualify 			= $calc_qualify;
			$this->calc_affected 			= $calc_affected;
			$this->calc_amount_cond 		= $calc_amount_cond;
			$this->calc_amount_dimunit 		= $calc_amount_dimunit;
			$this->for_override 			= $for_override;
			$this->ordering 				= $ordering;
			$this->shared 					= $shared;
			$this->published 				= $published;
			$this->discount_cat_ids 		= $discount_cat_ids;
			$this->discount_countries_ids 	= $discount_countries_ids;
			$this->discount_shoppergroups_ids = $discount_shoppergroups_ids;
			$this->discount_states_ids 		= $discount_states_ids;
		}
	}	
	
/**
 * Class AvalaibleImage
 *
 * Class "AvalaibleImage" with attribute : id, name, code, 
 *
 * @author     Mickael cabanas (cabanas.mickael|at|gmail.com)
 * @copyright  Mickael cabanas
 * @license    http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version    Release:
 */
	class AvalaibleImage {
		public $image_name="";
		public $image_url="";
		public $realpath="";
		public $image_dir="";

		//constructeur
		/**
		 * Enter description here...
		 *
		 * @param String $image_name
		 * @param String $image_url
		 */
		function __construct($image_name, $image_url, $realpath,$image_dir) {
			$this->image_name = $image_name;
			$this->image_url = $image_url;	
			$this->realpath = $realpath;	
			$this->image_dir = $image_dir;			
		}
	}	
	
	/**
	 * Class AvalaibleImage
	 *
	 * Class "AvalaibleImage" with attribute : id, name, code, 
	 *
	 * @author     Mickael cabanas (cabanas.mickael|at|gmail.com)
	 * @copyright  Mickael cabanas
	 * @license    http://www.gnu.org/copyleft/gpl.html GNU/GPL
	 * @version    Release:
	 */
	class AvalaibleFile {
		public $file_name="";
		public $file_url="";
		public $realpath="";

		//constructeur
		/**
		 * Enter description here...
		 *
		 * @param String $file_name
		 * @param String $file_url
		 */
		function __construct($file_name, $file_url, $realpath) {
			$this->file_name = $file_name;
			$this->file_url = $file_url;
			$this->realpath = $realpath;			
		}
	}	
	
	/**
	 * Class ProductVote
	 *
	 * Class "ProductVote" with attribute : id, name, code, 
	 *
	 * @author     Mickael cabanas (cabanas.mickael|at|gmail.com)
	 * @copyright  Mickael cabanas
	 * @license    http://www.gnu.org/copyleft/gpl.html GNU/GPL
	 * @version    Release:
	 */
	class ProductVote {
		public $rating_id="";
		public $product_id="";
		public $product_name="";
		public $product_sku="";
		public $rates="";
		public $ratingcount="";
		public $rating="";
		public $published="";

		//constructeur
		/**
		 * Enter description here...
		 *
		 * @param String $product_id
		 * @param String $product_name
		 */
		function __construct($rating_id, $product_id, $product_name, $product_sku,$rates, $ratingcount, $rating, $published) {
			$this->rating_id = $rating_id;
			$this->product_name = $product_name;
			$this->product_sku = $product_sku;
			$this->rates = $rates;
			$this->ratingcount = $ratingcount;
			$this->rating = $rating;
			$this->published = $published;			
		}
	}	
	
	/**
	 * Class productReview
	 *
	 * Class "productReview" with attribute : id, name, code, 
	 *
	 * @author     Mickael cabanas (cabanas.mickael|at|gmail.com)
	 * @copyright  Mickael cabanas
	 * @license    http://www.gnu.org/copyleft/gpl.html GNU/GPL
	 * @version    Release:
	 */
	class ProductReview {
		public $review_id="";
		public $product_id="";
		public $comment="";
		public $review_ok="";
		public $review_rates="";
		public $review_ratingcount="";
		public $review_rating="";
		public $lastip="";
		public $published="";

		//constructeur
		/**
		 * Enter description here...
		 *
		 * @param String $review_id
		 * @param String $product_id
		 */
		function __construct($review_id, $product_id, $comment,$review_ok, $review_rates, $review_ratingcount, $review_rating, $lastip, $published) {
			$this->review_id = $review_id;
			$this->product_id = $product_id;
			$this->comment = $comment;
			$this->review_ok = $review_ok;
			$this->review_rates = $review_rates;
			$this->review_ratingcount = $review_ratingcount;
			$this->review_rating = $review_rating;	
			$this->lastip = $lastip;
			$this->published = $published;			
		}
	}	
	
		
/**
 * Class Media
 *
 * Class "Media" with attribute : id, name, description,  image, fulliamage , parent category
 * attributes, parent produit, child id)
 *
 * @author     Mickael cabanas (cabanas.mickael|at|gmail.com)
 * @copyright  Mickael cabanas
 * @license    http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version    Release:
 */
	class Media {
		public $virtuemart_media_id="";
		public $virtuemart_vendor_id="";
		public $file_title="";
		public $file_description="";
		public $file_meta="";
		public $file_mimetype="";
		public $file_type="";
		public $file_url="";
		public $file_url_thumb="";
		public $file_is_product_image="";	
		public $file_is_downloadable="";
		public $file_is_forSale="";
		public $file_params="";	
		public $ordering="";
		public $shared="";
		public $published="";
		public $attachValue="";
		
		
				
		//constructeur
		function __construct($virtuemart_media_id, $virtuemart_vendor_id, $file_title, $file_description, $file_meta, $file_mimetype, $file_type, $file_url, $file_url_thumb,
								$file_is_product_image,$file_is_downloadable,$file_is_forSale,$file_params,$ordering,$shared,$published,$attachValue) {
								
			$this->virtuemart_media_id = $virtuemart_media_id;
			$this->virtuemart_vendor_id = $virtuemart_vendor_id;
			$this->file_title = $file_title;
			$this->file_description = $file_description;
			$this->file_meta = $file_meta;
			$this->file_mimetype = $file_mimetype;
			$this->file_type = $file_type;
			$this->file_url = $file_url;
			$this->file_url_thumb = $file_url_thumb;
			$this->file_is_product_image = $file_is_product_image;
			$this->file_is_downloadable = $file_is_downloadable;
			$this->file_is_forSale = $file_is_forSale;
			$this->file_params = $file_params;
			$this->ordering = $ordering;
			$this->shared = $shared;
			$this->published = $published;
			$this->attachValue = $attachValue;
			
		}
	}
	
	
	/**
	 * Class Custom
	 *
	 * Class "Custom" with attribute : id, custom_parent_id, admin_only, ...
	 *
	 * @author     Mickael cabanas (cabanas.mickael|at|gmail.com)
	 * @copyright  Mickael cabanas
	 * @license    http://www.gnu.org/copyleft/gpl.html GNU/GPL
	 * @version    Release:
	 */
	class Custom {
		public $virtuemart_custom_id="";
		public $custom_parent_id="";
		public $admin_only="";
		public $custom_title="";
		public $custom_tip="";
		public $custom_value="";
		public $custom_field_desc="";
		public $field_type="";
		public $is_list="";
		public $is_hidden="";
		public $is_cart_attribute="";
		public $published="";
		public $virtuemart_vendor_id="";
		public $custom_jplugin_id="";
		public $custom_element="";
		public $layout_pos="";
		public $custom_params="";

		//constructeur
		/**
		 * Enter description here...
		 *
		 * @param String virtuemart_custom_id
		 * @param String custom_parent_id
		 */
		function __construct($virtuemart_custom_id, $custom_parent_id, $admin_only,$custom_title, $custom_tip, $custom_value, $custom_field_desc, $field_type, $is_list, $is_hidden, $is_cart_attribute, $published,
							$virtuemart_vendor_id,$custom_jplugin_id,$custom_element,$layout_pos,$custom_params) {
			
			$this->virtuemart_custom_id = $virtuemart_custom_id;
			$this->custom_parent_id = $custom_parent_id;
			$this->admin_only = $admin_only;
			$this->custom_title = $custom_title;
			$this->custom_tip = $custom_tip;
			$this->custom_value = $custom_value;
			$this->custom_field_desc = $custom_field_desc;	
			$this->field_type = $field_type;
			$this->is_list = $is_list;	
			$this->is_hidden = $is_hidden;
			$this->is_cart_attribute = $is_cart_attribute;
			$this->published = $published;	
			$this->virtuemart_vendor_id = $virtuemart_vendor_id;	
			$this->custom_jplugin_id = $custom_jplugin_id;	
			$this->custom_element = $custom_element;	
			$this->layout_pos = $layout_pos;	
			$this->custom_params = $custom_params;		
		
		}
	}	
	
	
		
	/**
	 * Class CustomField
	 *
	 * Class "CustomField" with attribute : id, custom_parent_id, admin_only, ...
	 *
	 * @author     Mickael cabanas (cabanas.mickael|at|gmail.com)
	 * @copyright  Mickael cabanas
	 * @license    http://www.gnu.org/copyleft/gpl.html GNU/GPL
	 * @version    Release:
	 */
	class CustomField {
		public $virtuemart_customfield_id="";
		public $virtuemart_product_id="";
		public $virtuemart_custom_id="";
		
		public $custom_value="";
		public $custom_price="";
		public $custom_param="";
		public $published="";
		

		//constructeur
		/**
		 * Enter description here...
		 *
		 * @param String virtuemart_customfield_id
		 * @param String virtuemart_product_id
		 */
		function __construct($virtuemart_customfield_id, $virtuemart_product_id,$virtuemart_custom_id, $custom_value,$custom_price, $custom_param, $published) {
			
			$this->virtuemart_customfield_id = $virtuemart_customfield_id;
			$this->virtuemart_product_id = $virtuemart_product_id;
			$this->virtuemart_custom_id = $virtuemart_custom_id;
			$this->custom_value = $custom_value;
			$this->custom_price = $custom_price;
			$this->custom_param = $custom_param;
			$this->published = $published;
			
		}
	}
	
		/**
	 * Class Worldzone
	 *
	 * Class "Worldzone" with attribute : virtuemart_worldzone_id ...
	 *
	 * @author     Mickael cabanas (cabanas.mickael|at|gmail.com)
	 * @copyright  Mickael cabanas
	 * @license    http://www.gnu.org/copyleft/gpl.html GNU/GPL
	 * @version    Release:
	 */
	class Worldzone {
		public $virtuemart_worldzone_id="";
		public $virtuemart_vendor_id="";
		public $zone_name="";
		public $zone_cost="";
		public $zone_limit="";
		public $zone_description="";
		public $zone_tax_rate="";
		public $ordering="";
		public $shared="";
		public $published="";
		

		//constructeur
		/**
		 * Enter description here...
		 *
		 * @param String $virtuemart_worldzone_id
		 * @param String $virtuemart_vendor_id
		 * @param String $zone_name
		 * ...
		 */
		function __construct($virtuemart_worldzone_id, $virtuemart_vendor_id, $zone_name,$zone_cost,$zone_limit,$zone_description,
							$zone_tax_rate,$ordering,$shared,$published) {
			
			$this->virtuemart_worldzone_id 	= $virtuemart_worldzone_id;
			$this->virtuemart_vendor_id 	= $virtuemart_vendor_id;
			$this->zone_name 				= $zone_name;
			$this->zone_cost 				= $zone_cost;
			$this->zone_limit 				= $zone_limit;
			$this->zone_description 		= $zone_description;
			$this->zone_tax_rate 			= $zone_tax_rate;
			$this->ordering 				= $ordering;
			$this->shared 					= $shared;
			$this->published 				= $published;
	
		}
	}		
	
	
/**
 * Class CommonReturn
 *
 * Class "CommonReturn" with attribute : returnCode, message, code, 
 *
 * @author     Mickael cabanas (cabanas.mickael|at|gmail.com)
 * @copyright  Mickael cabanas
 * @license    http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version    Release:
 */
	class CommonReturn {
		public $returnCode="";
		public $message="";
		public $returnData="";

		//constructeur
		/**
		 * Enter description here...
		 *
		 * @param String $returnCode
		 * @param String $message
		 */
		function __construct($returnCode, $message, $returnData) {
			$this->returnCode = $returnCode;
			$this->message = $message;	
			$this->returnData = $returnData;				
		}
	}		

	
	/**
    * This function get Attributes for a product ID
	* (not expose as WS)
    * @param string The if of the product
    * @return array of attribute and value
   */
	function getAttributes($product_id){
	
		////////////// WARNING TABLE NOT IN VM2 ////////////////
		$db = JFactory::getDBO();	
		$query  = "SELECT at.attribute_name, at.attribute_value  ";
		$query .= "FROM #__virtuemart_product_attribute at WHERE ";
		$query .= "at.product_id = '$product_id' ";
		$query .= " LIMIT 0,100 "; 
		$db->setQuery($query);
		
		$rows = $db->loadObjectList();
		
		foreach ($rows as $row){
				$attributesArray = array("name" => $row->attribute_name, "value" => $row->attribute_value);
		}
		return $attributesArray;
		
		
		/*$list  = "SELECT at.attribute_name, at.attribute_value  ";
		$list .= "FROM #__{vm}_product_attribute at WHERE ";
		$q .= "at.product_id = '$product_id' ";
		$list .= $q . " LIMIT 0,100 "; 
		
		$db = new ps_DB;
		$db->query($list);
		
		while ($db->next_record()) {
				 			  			 
			  $attributesArray = array("name" => $db->f("attribute_name"), "value" => $db->f("attribute_value") );
		}
		return $attributesArray;*/
	}

	/**
    * This function get categoriesID for a product ID
	* (not expose as WS)
    * @param string The if of the product
    * @return array of categorie id
   */
	function getCategoriesIds($product_id){
		
		$db = JFactory::getDBO();	
				
		$query  = "SELECT `virtuemart_category_id` FROM `#__virtuemart_product_categories` ";
		$query .= "WHERE `virtuemart_product_id` = '" . $product_id . "' ";
		$query .= " LIMIT 0,500 "; 
		
		$db->setQuery($query);
		
		$rows = $db->loadObjectList();
		
		$str = "";
		foreach ($rows as $row){
			$str .= $row->virtuemart_category_id."|";
		}
		$str = substr_replace($str,"",strlen($str)-1,1);//remove last  '|'
		
		return $str;

	}	
	/**
    * This function get getManufacturerId for a product ID
	* (not expose as WS)
    * @param string The if of the product
    * @return array of categorie id
   */
	function getManufacturerId($product_id){
		
		$db = JFactory::getDBO();	
				
		$query  = "SELECT `virtuemart_manufacturer_id` FROM `#__virtuemart_product_manufacturers` ";
		$query .= "WHERE `virtuemart_product_id` = '" . $product_id . "' ";
		$query .= " LIMIT 0,500 "; 
		
		$db->setQuery($query);
		
		$rows = $db->loadObjectList();
		
		$mf_id = "";
		foreach ($rows as $row){
			$mf_id .= $row->virtuemart_manufacturer_id;
		}
		
		return $mf_id;
	}	
	
	/**
    * This function get getManufacturerId for a product ID
	* (not expose as WS)
    * @param string The if of the product
    * @return array of categorie id
   */
	function getPrices($product_id){
		
		$db = JFactory::getDBO();	
				
		$query  = "SELECT * FROM `#__virtuemart_product_prices` pr ";
		$query .= "JOIN `#__virtuemart_currencies` cur ON cur.virtuemart_currency_id = pr.virtuemart_currency_id";
		$query .= "WHERE `virtuemart_product_id` = '" . $product_id . "' ";
		$query .= " LIMIT 0,500 "; 
		
		$db->setQuery($query);
		
		$rows = $db->loadObjectList();
		
		$price = "";
		foreach ($rows as $row){
			$price .= $row->product_price;
			$price .= ' '.$row->currency_code_3;
		}
		
		return $price;
	}	
	
	/**
    * This function get Product for a product ID
	* (expose as WS)
    * @param string The if of the product
    * @return array (Product)
   */
	function GetProductFromId($params) {
	
		/* Authenticate*/
		$result = onAdminAuthenticate($params->loginInfo->login, $params->loginInfo->password,$params->loginInfo->isEncrypted);
		
		$vmConfig = getVMconfig();
		if ($vmConfig->get('soap_auth_getprod')==0){
			$result = "true";
		}
				
		//Auth OK
		if ($result == "true"){
			
			$_REQUEST['filter_order_Dir'] = "DESC"; // since vm rc2.0.3b
			
			if (!class_exists( 'VirtueMartModelProduct' )) require (JPATH_VM_ADMINISTRATOR.DS.'models'.DS.'product.php');
			$VirtueMartModelProduct = new VirtueMartModelProduct;
			
			$product_id = $params->product_id;
			$ProductDetails = $VirtueMartModelProduct->getProduct($product_id,false,false,false);
			
			unset($prod_prices);//because getprices for each product could be long
			if ($params->include_prices == 'Y' || $params->include_prices == '1' ){
				unset($params->shopper_group_id);
				unset($params->product_currency);
				$params->product_id = $ProductDetails->virtuemart_product_id;
				$prod_prices = GetProductPrices($params);
			}
			if ($ProductDetails->virtuemart_product_id==0){
				return new SoapFault("GetProductFromIdFault", "No result found");
			}
			$img = GetDefaultImages($params,$ProductDetails->virtuemart_product_id,false);
			$imgThumb = GetDefaultImages($params,$ProductDetails->virtuemart_product_id,true);
			$shoppergroups_ids = implode ('|',$ProductDetails->shoppergroups);
			
			$Product = new Product($ProductDetails->virtuemart_product_id ,
									$ProductDetails->virtuemart_vendor_id,
									$ProductDetails->product_parent_id,
									$ProductDetails->product_sku,
									$ProductDetails->product_name,
									$ProductDetails->slug ,
									$ProductDetails->product_s_desc,
									$ProductDetails->product_desc ,
									$ProductDetails->product_weight ,
									$ProductDetails->product_weight_uom,
									$ProductDetails->product_length,
									$ProductDetails->product_width,
									$ProductDetails->product_height,
									$ProductDetails->product_lwh_uom,
									$ProductDetails->product_url,
									$ProductDetails->product_in_stock,
									$ProductDetails->low_stock_notification,
									$ProductDetails->product_available_date,
									$ProductDetails->product_availability,
									$ProductDetails->product_special,
									$ProductDetails->ship_code_id,
									$ProductDetails->product_sales,
									$ProductDetails->product_unit,
									$ProductDetails->product_packaging,
									$ProductDetails->product_ordered,
									$ProductDetails->hits,
									$ProductDetails->intnotes,
									$ProductDetails->metadesc, 
									$ProductDetails->metakey, 
									$ProductDetails->metarobot,
									$ProductDetails->metaauthor,
									$ProductDetails->layout,
									$ProductDetails->published, 
									getCategoriesIds($ProductDetails->virtuemart_product_id),
									getManufacturerId($ProductDetails->virtuemart_product_id),
									$ProductDetails->product_params,
									$img,
									$imgThumb,
									$ProductDetails->shared,
									$ProductDetails->ordering,
									$ProductDetails->customtitle,
									$shoppergroups_ids,
									$prod_prices
									);
				
			return $Product;
		
			$errMsg=  $VirtueMartModelProduct->getError();
			//return new SoapFault("GetProductFromIdError", "DB message : \n".$ProductDetails->product_name);
			if ($errMsg==null){
				//return $Produit;
			} else {
				return new SoapFault("GetProductFromIdError", "DB message : \n".$errMsg);
			}
			
			
			
		}else if ($result == "false"){
			return new SoapFault("JoomlaServerAuthFault", "Authentication KO for : ".$params->loginInfo->login);
		}else if ($result == "no_admin"){
			return new SoapFault("JoomlaServerAuthFault", "User is not a Super Administrator : ".$params->loginInfo->login);
		}else{
			return new SoapFault("JoomlaServerAuthFault", "User does not exist : ".$params->loginInfo->login);
		}
	}
	
	/**
    * This function get Product for a SKU
	* (not expose as WS)
    * @param string product SKU
    * @return Product_id
   */
	function GetProductIDFromSKU($product_sku) {
	
		$db = JFactory::getDBO();	
		$query  = "SELECT virtuemart_product_id  FROM #__virtuemart_products WHERE product_sku = '$product_sku' ";
		$db->setQuery($query);
		
		$rows = $db->loadObjectList();
		
		$product_id="";
		foreach ($rows as $row){
			$product_id = $row->virtuemart_product_id;
		}
		
		return $product_id;
	
	}
	/**
    * This function get Product for a SKU
	* (expose as WS)
    * @param string The if of the product
    * @return array (Product)
   */
	function GetProductFromSKU($params) {
	
		/* Authenticate*/
		$result = onAdminAuthenticate($params->loginInfo->login, $params->loginInfo->password,$params->loginInfo->isEncrypted);
		
		$vmConfig = getVMconfig();
		if ($vmConfig->get('soap_auth_getprod')==0){
			$result = "true";
		}
		
		//Auth OK
		if ($result == "true"){
			
			$product_sku = $params->product_sku;
		
			$product_id = GetProductIDFromSKU($product_sku);
			if (empty($product_id)){
				return new SoapFault("GetProductFromSKUFault","No SKU found");
			}
			$params->product_id=$product_id;
			
			return GetProductFromId($params);

			
		}else if ($result == "false"){
			return new SoapFault("JoomlaServerAuthFault", "Authentication KO for : ".$params->loginInfo->login);
		}else if ($result == "no_admin"){
			return new SoapFault("JoomlaServerAuthFault", "User is not a Super Administrator : ".$params->loginInfo->login);
		}else{
			return new SoapFault("JoomlaServerAuthFault", "User does not exist : ".$params->loginInfo->login);
		}
	}
	
	
	
	/**
    * This function get All Childs product for a product ID
	* (expose as WS)
    * @param string The if of the product
    * @return array of Products
   */
	function GetChildsProduct($params) {
	
		/* Authenticate*/
		$result = onAdminAuthenticate($params->loginInfo->login, $params->loginInfo->password,$params->loginInfo->isEncrypted);
		
		$vmConfig = getVMconfig();
		if ($vmConfig->get('soap_auth_getprod')==0){
			$result = "true";
		}
		
		//Auth OK
		if ($result == "true"){
			
			$product_id = $params->product_id;
			
			$db = JFactory::getDBO();	
			$query  = "SELECT virtuemart_product_id FROM #__virtuemart_products WHERE product_parent_id = '$product_id' ";
			$db->setQuery($query);
			
			$rows = $db->loadObjectList();
			
			foreach ($rows as $row){
							
				$params->product_id = $row->virtuemart_product_id;
				$Product = GetProductFromId($params);
				$ProductArray[] = $Product;
				
			}
			return $ProductArray;
			
			
		}else if ($result == "false"){
			return new SoapFault("JoomlaServerAuthFault", "Authentication KO for : ".$params->loginInfo->login);
		}else if ($result == "no_admin"){
			return new SoapFault("JoomlaServerAuthFault", "User is not a Super Administrator : ".$params->loginInfo->login);
		}else{
			return new SoapFault("JoomlaServerAuthFault", "User does not exist : ".$params->loginInfo->login);
		}
		
	}
	
	/**
    * This function get All Products for a category ID
	* (expose as WS)
    * @param string The if of the product
    * @return array of attribute and value
   */
	function GetProductsFromCategory($params) {
	
		/* Authenticate*/
		$result = onAdminAuthenticate($params->loginInfo->login, $params->loginInfo->password,$params->loginInfo->isEncrypted);

		$vmConfig = getVMconfig();
		if ($vmConfig->get('soap_auth_getprod')==0){
			$result = "true";
		}
		
		//Auth OK
		if ($result == "true"){
			
			$_REQUEST['filter_order_Dir'] = "DESC"; // since vm rc2.0.3b
			
			$limite_start = $params->limite_start;
			if (empty($limite_start)){
				$limite_start = "0";
			}
			$limite_end = $params->limite_end;
			if (empty($limite_end)){
				$limite_end = "500";
			}
			$maxNumber = $vmConfig->get('absMaxProducts',700);
			//we want more results than max define in VM core
			if ($limite_end >$maxNumber){
				storeVmConfig('absMaxProducts',intval($limite_end) );
			}
			
			$categorie_id = $params->catgory_id;
			
			if (!class_exists( 'VirtueMartModelProduct' )) require (JPATH_VM_ADMINISTRATOR.DS.'models'.DS.'product.php');
			$VirtueMartModelProduct = new VirtueMartModelProduct;
			
			
			$_REQUEST['virtuemart_category_id'] = $categorie_id;
			$_POST['virtuemart_category_id'] = $categorie_id;
			$_GET['virtuemart_category_id'] = $categorie_id;
			
			//$products = $VirtueMartModelProduct->getProductsInCategory($categorie_id); //not working fine : missing product
				
			$db = JFactory::getDBO();
			$query  = "SELECT virtuemart_product_id FROM `#__virtuemart_product_categories` ref WHERE virtuemart_category_id = '$categorie_id'  ";
			
			$db->setQuery($query);
			$rows = $db->loadObjectList();
			
			foreach ($rows as $row){
				$ids[] = $row->virtuemart_product_id;
			}
			
			$products = $VirtueMartModelProduct->getProducts($ids);
			
			foreach ($products as $ProductDetails){
			
				unset($prod_prices);
				if ($params->include_prices == 'Y' || $params->include_prices == '1' ){
					unset($params->shopper_group_id);
					unset($params->product_currency);
					$params->product_id = $ProductDetails->virtuemart_product_id;
					$prod_prices = GetProductPrices($params);
				}
				
				$img = GetDefaultImages($params,$ProductDetails->virtuemart_product_id,false);
				$imgThumb = GetDefaultImages($params,$ProductDetails->virtuemart_product_id,true);
				$shoppergroups_ids = implode ('|',$ProductDetails->shoppergroups);
				
				$Product = new Product($ProductDetails->virtuemart_product_id ,
									$ProductDetails->virtuemart_vendor_id,
									$ProductDetails->product_parent_id,
									$ProductDetails->product_sku,
									$ProductDetails->product_name,
									$ProductDetails->slug ,
									$ProductDetails->product_s_desc,
									$ProductDetails->product_desc ,
									$ProductDetails->product_weight ,
									$ProductDetails->product_weight_uom,
									$ProductDetails->product_length,
									$ProductDetails->product_width,
									$ProductDetails->product_height,
									$ProductDetails->product_lwh_uom,
									$ProductDetails->product_url,
									$ProductDetails->product_in_stock,
									$ProductDetails->low_stock_notification,
									$ProductDetails->product_available_date,
									$ProductDetails->product_availability,
									$ProductDetails->product_special,
									$ProductDetails->ship_code_id,
									$ProductDetails->product_sales,
									$ProductDetails->product_unit,
									$ProductDetails->product_packaging,
									$ProductDetails->product_ordered,
									$ProductDetails->hits,
									$ProductDetails->intnotes,
									$ProductDetails->metadesc, 
									$ProductDetails->metakey, 
									$ProductDetails->metarobot,
									$ProductDetails->metaauthor,
									$ProductDetails->layout,
									$ProductDetails->published, 
									getCategoriesIds($ProductDetails->virtuemart_product_id),
									getManufacturerId($ProductDetails->virtuemart_product_id),
									$ProductDetails->product_params,
									$img,
									$imgThumb,
									$ProductDetails->shared,
									$ProductDetails->ordering,
									$ProductDetails->customtitle,
									$shoppergroups_ids,
									$prod_prices
									);

				$ProductArray[] = $Product;
			
			}
			return $ProductArray;
			
				
			
		}else if ($result == "false"){
			return new SoapFault("JoomlaServerAuthFault", "Authentication KO for : ".$params->loginInfo->login);
		}else if ($result == "no_admin"){
			return new SoapFault("JoomlaServerAuthFault", "User is not a Super Administrator : ".$params->loginInfo->login);
		}else{
			return new SoapFault("JoomlaServerAuthFault", "User does not exist : ".$params->loginInfo->login);
		}		
	}
	
	
	
	/**
    * This function Get RelatedProducts
	* (expose as WS)
    * @param string The if of the product
    * @return array of attribute and value
   */
	function GetRelatedProducts($params) {

		/* Authenticate*/
		$result = onAdminAuthenticate($params->loginInfo->login, $params->loginInfo->password,$params->loginInfo->isEncrypted);
		
		$vmConfig = getVMconfig();
		if ($vmConfig->get('soap_auth_getprod')==0){
			$result = "true";
		}
		
		//Auth OK
		if ($result == "true"){
			
			$prod_id = $params->product_id;
			
			$db = JFactory::getDBO();	
			$query   = "SELECT CF.custom_value, custom_price, field_type, CF.virtuemart_custom_id, CF.ordering  FROM `#__virtuemart_product_customfields` CF ";
			$query  .= "JOIN `#__virtuemart_customs` C ON C.virtuemart_custom_id = CF.virtuemart_custom_id AND field_type = 'R'    ";
			$query  .= "WHERE CF.virtuemart_product_id = '$prod_id'  ";

			$db->setQuery($query);
			
			$rows = $db->loadObjectList();

			foreach ($rows as $row){
				$relatedProdId = $row->custom_value;
				$params->product_id = $relatedProdId;
				$product = GetProductFromId($params);
				$productArray[] = $product;
			}
			
			return $productArray;
			
		
		}else if ($result == "false"){
			return new SoapFault("JoomlaServerAuthFault", "Authentication KO for : ".$params->loginInfo->login);
		}else if ($result == "no_admin"){
			return new SoapFault("JoomlaServerAuthFault", "User is not a Super Administrator : ".$params->loginInfo->login);
		}else{
			return new SoapFault("JoomlaServerAuthFault", "User does not exist : ".$params->loginInfo->login);
		}		
	}

	/**
    * This function Search Products from params 
	* (expose as WS)
    * @param 
    * @return array of products
   */
	function SearchProducts($params) {
	
		/* Authenticate*/
		$result = onAdminAuthenticate($params->loginInfo->login, $params->loginInfo->password,$params->loginInfo->isEncrypted);
	
		$vmConfig = getVMconfig();
		if ($vmConfig->get('soap_auth_getprod')==0){
			$result = "true";
		}
		
		//Auth OK
		if ($result == "true"){
	
			$limite_start = $params->limite_start;
			if (empty($limite_start)){
				$limite_start = "0";
			}
			$limite_end = $params->limite_end;
			if (empty($limite_end)){
				$limite_end = "500";
			}
			
			//AND or OR between fields for criteria 
			$operator = "AND";
			if (!empty($params->operator) && ($params->operator == "OR" || $params->operator == "or")  ){
				$operator = "OR";
			}
			
			//more or less for criteria 
			$operator_more_less_equal = "=";
			if (!empty($params->operator_more_less_equal) && $params->operator_more_less_equal == "more" ){
				$operator_more_less_equal=">";
			} else if (!empty($params->operator_more_less_equal) && $params->operator_more_less_equal == "less" ){
				$operator_more_less_equal="<";
			} else if (!empty($params->operator_more_less_equal) && $params->operator_more_less_equal == "equal" ){
				$operator_more_less_equal="=";
			}else if (!empty($params->operator_more_less_equal) && $params->operator_more_less_equal == "moreequal" ){
				$operator_more_less_equal=">=";
			}else if (!empty($params->operator_more_less_equal) && $params->operator_more_less_equal == "lessequal" ){
				$operator_more_less_equal="<=";
			}
			
			//categories IDS
			if(!empty($params->product_categories)){
				$cat_ids = explode('|', $params->product_categories);
				$cat_ids_in_sql=""; // make sql request like : in (2,5,8,10)
				if (is_array($cat_ids)){
					$cat_ids_in_sql .="(";
					$count = count($cat_ids);
					for ($i = 0; $i < $count; $i++) {
						if ($i==$count-1){
							$cat_ids_in_sql.= " ".$cat_ids[$i]." )";
						}else {
							$cat_ids_in_sql.= " ".$cat_ids[$i].",";
						}
					}
				}else {
					$cat_ids_in_sql .= "(".$cat_ids.")";
				}
			}
			$categorie_id = $cat_ids[0];
			
			if ($params->product_publish == "N"){
				$product_publish = "0";
			}
			if ($params->product_publish == "Y"){
				$product_publish = "1";
			}
			
			$with_childs = $params->with_childs;
			if ($with_childs == "N"){
				$with_childs = "N";
			}else {
				$with_childs = "Y";
			}
			
			$db = JFactory::getDBO();	
			$query   = "SELECT *  FROM #__virtuemart_products p ";
			$query  .= "LEFT JOIN #__virtuemart_products_".VMLANG." plang  ON plang.virtuemart_product_id = p.virtuemart_product_id    ";
			$query  .= "LEFT JOIN #__virtuemart_product_categories c ON p.virtuemart_product_id=c.virtuemart_product_id ";
			$query  .= "LEFT JOIN #__virtuemart_product_manufacturers mf ON p.virtuemart_product_id=mf.virtuemart_product_id ";
			$query  .= "LEFT JOIN #__virtuemart_product_prices pr ON pr.virtuemart_product_id=p.virtuemart_product_id ";
			$query  .= "WHERE 1 ";
			
			
			if ($product_publish == "0" || $product_publish == "1" ){
				$query .= "AND p.published=".$product_publish." ";
			}			
			
			if ($with_childs == "N"){
				$query .= "AND p.product_parent_id = '0' ";
			}
			
			if ($operator == "OR"){
				$query .= "AND ( 0 ";
			}else {
				$query .= "AND ( 1 ";
			}
			
			if(!empty($params->product_categories)){
				$query .=$operator." c.virtuemart_category_id in ".$cat_ids_in_sql ." " ; // p.category_id in (10,12,2,0,33)
			}
			if(!empty($params->product_id)){
				$query .=$operator." p.virtuemart_product_id = '$params->product_id' " ;
			}
			if(!empty($params->product_sku)){
				$query .=$operator." p.product_sku = '$params->product_sku' " ;
			}
			if(!empty($params->product_name)){
				$query .=$operator." plang.product_name like '%$params->product_name%' " ;
			}
			if(!empty($params->product_desc)){
				$query .=$operator." plang.product_desc like '%$params->product_desc%' " ;
			}
			if(!empty($params->product_sdesc)){
				$query .=$operator." plang.product_s_desc like '%$params->product_sdesc%' " ;
			}
			if(!empty($params->product_sales)){
				$query .=$operator." p.product_sales $operator_more_less_equal '$params->product_sales' " ;
			}
			if(!empty($params->price)){
				$query .=$operator." pr.product_price $operator_more_less_equal '$params->price' " ;
			}
			if(!empty($params->quantity)){
				$query .=$operator." p.product_in_stock $operator_more_less_equal '$params->quantity' " ;
			}
			if(!empty($params->product_currency)){
				$query .=$operator." pr.product_currency = '$params->product_currency' " ;
			}
			if(!empty($params->manufacturer_id)){
				$query .=$operator." mf.virtuemart_manufacturer_id = '$params->manufacturer_id' " ;
			}
			if(!empty($params->vendor_id)){
				$query .=$operator." p.virtuemart_vendor_id = '$params->vendor_id' " ;
			}
			if(!empty($params->product_weight)){
				$query .=$operator." p.product_weight $operator_more_less_equal '$params->product_weight' " ;
			}
			if(!empty($params->product_weight_uom)){
				$query .=$operator." p.product_weight_uom $operator_more_less_equal '$params->product_weight_uom' " ;
			}
			if(!empty($params->product_width)){
				$query .=$operator." p.product_width $operator_more_less_equal '$params->product_width' " ;
			}
			if(!empty($params->product_height)){
				$query .=$operator." p.product_height $operator_more_less_equal '$params->product_height' " ;
			}
			if(!empty($params->product_length)){
				$query .=$operator." p.product_length $operator_more_less_equal '$params->product_length' " ;
			}
			if(!empty($params->product_lwh_uom)){
				$query .=$operator." p.product_lwh_uom $operator_more_less_equal '$params->product_lwh_uom' " ;
			}
			if(!empty($params->product_unit)){
				$query .=$operator." p.product_unit = '$params->product_unit' " ;
			}
			if(!empty($params->product_packaging)){
				$query .=$operator." p.product_packaging = '$params->product_packaging' " ;
			}
			if(!empty($params->product_url)){
				$query .=$operator." p.product_url like  '%$params->product_url%' " ;
			}
			if(!empty($params->product_special)){
				$query .=$operator." p.product_special =  '$params->product_special' " ;
			}
			if(!empty($params->parent_product_id)){
				$query .=$operator." p.product_parent_id =  '$params->parent_product_id' " ;
			}
			
			$query .= " ) ";
			
			$query .= "GROUP BY p.virtuemart_product_id ";
			$query .= "ORDER BY p.virtuemart_product_id ASC ";
			$query .= "LIMIT $limite_start,$limite_end "; 

			$db->setQuery($query);
			//var_dump($query);die;
			$rows = $db->loadObjectList();
			
			foreach ($rows as $ProductDetails){
				
				unset($prod_prices);
				if ($params->include_prices == 'Y' || $params->include_prices == '1' ){
					unset($params->shopper_group_id);
					unset($params->product_currency);
					$params->product_id = $ProductDetails->virtuemart_product_id;
					$prod_prices = GetProductPrices($params);
				}
				
				$img = GetDefaultImages($params,$ProductDetails->virtuemart_product_id,false);
				$imgThumb = GetDefaultImages($params,$ProductDetails->virtuemart_product_id,true);
				$shoppergroups_ids = implode ('|',$ProductDetails->shoppergroups);
				
				$Product = new Product($ProductDetails->virtuemart_product_id/*$ProductDetails->prices[0]*/ ,
									$ProductDetails->virtuemart_vendor_id,
									$ProductDetails->product_parent_id,
									$ProductDetails->product_sku,
									$ProductDetails->product_name,
									$ProductDetails->slug ,
									$ProductDetails->product_s_desc,
									$ProductDetails->product_desc ,
									$ProductDetails->product_weight ,
									$ProductDetails->product_weight_uom,
									$ProductDetails->product_length,
									$ProductDetails->product_width,
									$ProductDetails->product_height,
									$ProductDetails->product_lwh_uom,
									$ProductDetails->product_url,
									$ProductDetails->product_in_stock,
									$ProductDetails->low_stock_notification,
									$ProductDetails->product_available_date,
									$ProductDetails->product_availability,
									$ProductDetails->product_special,
									$ProductDetails->ship_code_id,
									$ProductDetails->product_sales,
									$ProductDetails->product_unit,
									$ProductDetails->product_packaging,
									$ProductDetails->product_ordered,
									$ProductDetails->hits,
									$ProductDetails->intnotes,
									$ProductDetails->metadesc, 
									$ProductDetails->metakey, 
									$ProductDetails->metarobot,
									$ProductDetails->metaauthor,
									$ProductDetails->layout,
									$ProductDetails->published, 
									getCategoriesIds($ProductDetails->virtuemart_product_id),
									getManufacturerId($ProductDetails->virtuemart_product_id),
									$ProductDetails->product_params,
									$img,
									$imgThumb,
									$ProductDetails->shared,
									$ProductDetails->ordering,
									$ProductDetails->customtitle,
									$shoppergroups_ids,
									$prod_prices
									);
					$ProductArray[] = $Product;
			
			}
			//return $ProduitArray;
		
			
			$errMsg=  $db->getErrorMsg();
			if ($errMsg==null){
				return $ProductArray;
			} else {
				return new SoapFault("SQLError", "Error while searshing product",$errMsg);
			}
			
		
		}else if ($result == "false"){
			return new SoapFault("JoomlaServerAuthFault", "Authentication KO for : ".$params->loginInfo->login);
		}else if ($result == "no_admin"){
			return new SoapFault("JoomlaServerAuthFault", "User is not a Super Administrator : ".$params->loginInfo->login);
		}else{
			return new SoapFault("JoomlaServerAuthFault", "User does not exist : ".$params->loginInfo->login);
		}		
	}
	
	
	

/**
    * This function get Products for a order ID
	* (expose as WS)
    * @param string 
    * @return array (Product)
   */
	function GetProductsFromOrderId($params) {
	
		/* Authenticate*/
		$result = onAdminAuthenticate($params->loginInfo->login, $params->loginInfo->password,$params->loginInfo->isEncrypted);
		
		$vmConfig = getVMconfig();
		if ($vmConfig->get('soap_auth_getprod')==0){
			$result = "true";
		}
		//Auth OK
		if ($result == "true"){
			
			$order_id = $params->order_id;
					
			$db = JFactory::getDBO();	
			$query  = "SELECT *  FROM #__virtuemart_order_items WHERE virtuemart_order_id = '$order_id'  ";
	
			$db->setQuery($query);
			
			$rows = $db->loadObjectList();
			
			foreach ($rows as $row){
				
				$OrderItemInfo = new OrderItemInfo($row->virtuemart_order_id,
									$row->virtuemart_userinfo_id,
									$row->virtuemart_vendor_id,
									$row->virtuemart_product_id,
									$row->order_item_sku,
									$row->order_item_name,
									$row->product_quantity,
									$row->product_item_price,
									$row->product_final_price,
									$row->order_item_currency,
									$row->order_status,
									$row->product_attribute,
									$row->created_on,
									$row->modified_on
									
									);
				  
				$OrderItemInfoArray[] =  $OrderItemInfo;
			
			}

	
			return $OrderItemInfoArray;
			
		}else if ($result == "false"){
			return new SoapFault("JoomlaServerAuthFault", "Authentication KO for : ".$params->loginInfo->login);
		}else if ($result == "no_admin"){
			return new SoapFault("JoomlaServerAuthFault", "User is not a Super Administrator : ".$params->loginInfo->login);
		}else{
			return new SoapFault("JoomlaServerAuthFault", "User does not exist : ".$params->loginInfo->login);
		}
	}	
	
	/**
    * This function get All currency
	* (expose as WS)
    * @param string 
    * @return array of currency
   */
	function GetAllCurrency($params) {
	
		/* Authenticate*/
		$result = onAdminAuthenticate($params->loginInfo->login, $params->loginInfo->password,$params->loginInfo->isEncrypted);
		
		$vmConfig = getVMconfig();
		if ($vmConfig->get('soap_auth_prod_otherget')==0){
			$result = "true";
		}
		
		//Auth OK
		if ($result == "true"){
		
			$product_id = $params->product_id;		
			
			if (!class_exists( 'VirtueMartModelCurrency' )) require (JPATH_VM_ADMINISTRATOR.DS.'models'.DS.'currency.php');
			$VirtueMartModelCurrency = new VirtueMartModelCurrency;
			$VirtueMartModelCurrency->_noLimit = true;
			$currencies = $VirtueMartModelCurrency->getCurrenciesList("");
			
			foreach ($currencies as $currencie){
				$Currency = new Currency($currencie->virtuemart_currency_id,
										$currencie->virtuemart_vendor_id,
										$currencie->currency_name,
										$currencie->currency_code_2,
										$currencie->currency_code_3,
										$currencie->currency_numeric_code,
										$currencie->currency_exchange_rate,
										$currencie->currency_symbol,
										$currencie->currency_decimal_place,
										$currencie->currency_decimal_symbol,
										$currencie->currency_thousands,
										$currencie->currency_positive_style,
										$currencie->currency_negative_style,
										$currencie->ordering,
										$currencie->shared,
										$currencie->published);
				$CurrencyArray[] = $Currency;
			
			}
			return $CurrencyArray;
			
			
		}else if ($result == "false"){
			return new SoapFault("JoomlaServerAuthFault", "Authentication KO for : ".$params->loginInfo->login);
		}else if ($result == "no_admin"){
			return new SoapFault("JoomlaServerAuthFault", "User is not a Super Administrator : ".$params->loginInfo->login);
		}else{
			return new SoapFault("JoomlaServerAuthFault", "User does not exist : ".$params->loginInfo->login);
		}
		
	}
	
	
	/**
    * This function get All currency
	* (expose as WS)
    * @param string 
    * @return array of currency
   */
	function GetProductVote($params) {
			
		$product_id = $params->product_id;
		/* Authenticate*/
		$result = onAdminAuthenticate($params->loginInfo->login, $params->loginInfo->password,$params->loginInfo->isEncrypted);
		
		$vmConfig = getVMconfig();
		if ($vmConfig->get('soap_auth_prod_otherget')==0){
			$result = "true";
		}
	
		//Auth OK
		if ($result == "true"){
		
			$eq = " = ";
			if (!empty($params->moreless)){
				if ($params->moreless == "more"){
					$eq = " >= ";
				}
				if ($params->moreless == "less"){
					$eq = " <= ";
				}
				if ($params->moreless == "equal"){
					$eq = " = ";
				}
			}
		
			$query  = "SELECT rat.virtuemart_rating_id, rat.virtuemart_product_id, rat.rates, rat.ratingcount,rat.rating, rat.published, p.product_sku, plang.product_name ";
			$query .= "FROM #__virtuemart_ratings rat  ";
			$query .= "JOIN #__virtuemart_products p ON p.virtuemart_product_id = rat.virtuemart_product_id ";
			$query .= "JOIN #__virtuemart_products_".VMLANG." plang  ON plang.virtuemart_product_id = p.virtuemart_product_id    ";
			$query .= "WHERE 1 ";
			
			if (!empty($params->product_id)){
				$query .= " AND rat.virtuemart_product_id  = ".(int)$product_id." ";
			}
			if (!empty($params->rates)){
				$query .= " AND rat.rates $eq '$params->rates' ";
			}
			if (!empty($params->ratingcount)){
				$query .= " AND rat.ratingcount $eq '$params->ratingcount' ";
			}
			if (!empty($params->rating)){
				$query .= " AND rat.rating $eq '$params->rating' ";
			}
			
			$db = JFactory::getDBO();
			$db->setQuery($query);
			
			$rows = $db->loadObjectList();
			
			foreach ($rows as $row){
				
				$ProductVote = new ProductVote($row->virtuemart_rating_id,
												$row->virtuemart_product_id,
												$row->product_name,
												$row->product_sku,
												$row->rates,
												$row->ratingcount,
												$row->rating,
												$row->published
												);
				$ProductVoteArray[] = $ProductVote;
				
				
			}
			
			
			$errMsg=  $db->getErrorMsg();
			
			if ($errMsg==null){
				return $ProductVoteArray;
			} else {
				return new SoapFault("GetProductVoteFault", "cannot GetProductVote  "." | ERRLOG : ".$errMsg);				
			}
			
		}else if ($result == "false"){
			return new SoapFault("JoomlaServerAuthFault", "Authentication KO for : ".$params->loginInfo->login);
		}else if ($result == "no_admin"){
			return new SoapFault("JoomlaServerAuthFault", "User is not a Super Administrator : ".$params->loginInfo->login);
		}else{
			return new SoapFault("JoomlaServerAuthFault", "User does not exist : ".$params->loginInfo->login);
		}
		
	}
	
	
	
	/**
    * This function Get Product Reviews
	* (expose as WS)
    * @param string 
    * @return array of currency
   */
	function GetProductReviews($params) {
			
		$product_id = $params->product_id;
		/* Authenticate*/
		$result = onAdminAuthenticate($params->loginInfo->login, $params->loginInfo->password,$params->loginInfo->isEncrypted);
		
		$vmConfig = getVMconfig();
		if ($vmConfig->get('soap_auth_prod_otherget')==0){
			$result = "true";
		}
		
		//Auth OK
		if ($result == "true"){
		
			$eq = " = ";
			if (!empty($params->moreless)){
				if ($params->moreless == "more"){
					$eq = " >= ";
				}
				if ($params->moreless == "less"){
					$eq = " <= ";
				}
				if ($params->moreless == "equal"){
					$eq = " = ";
				}
			}
		
			$query  = "SELECT * FROM #__virtuemart_rating_reviews ";
			$query .= "WHERE 1 ";
			
			if (!empty($params->review_id)){
				$query .= " AND virtuemart_rating_review_id = '$params->review_id' ";
			}
			if (!empty($params->product_id)){
				$query .= " AND virtuemart_product_id $eq '$params->product_id' ";
			}
			if (!empty($params->published)){
				$query .= " AND published = '$params->published' ";
			}
			
			$db = JFactory::getDBO();
			$db->setQuery($query);
			
			$rows = $db->loadObjectList();
			
			foreach ($rows as $row){
				
				$ProductReview = new ProductReview($row->virtuemart_rating_review_id,
									$row->virtuemart_product_id,
									$row->comment,
									$row->review_ok,
									$row->review_rates,
									$row->review_ratingcount,
									$row->review_rating,
									$row->lastip,
									$row->published
									);
				$ProductReviewArray[] = $ProductReview;
				
			}

			
			$errMsg=  $db->getErrorMsg();
			
			if ($errMsg==null){
				return $ProductReviewArray;
			} else {
				return new SoapFault("GetProductReviewsFault", "cannot GetProductReviews  "." | ERRLOG : ".$errMsg);				
			}
			
		}else if ($result == "false"){
			return new SoapFault("JoomlaServerAuthFault", "Authentication KO for : ".$params->loginInfo->login);
		}else if ($result == "no_admin"){
			return new SoapFault("JoomlaServerAuthFault", "User is not a Super Administrator : ".$params->loginInfo->login);
		}else{
			return new SoapFault("JoomlaServerAuthFault", "User does not exist : ".$params->loginInfo->login);
		}
		
	}
	
	
	/**
    * This function Get Customs List
	* (expose as WS)
    * @param string 
    * @return array of custom
   */
	function GetCustomsList($params) {
			
		$product_id = $params->product_id;
		/* Authenticate*/
		$result = onAdminAuthenticate($params->loginInfo->login, $params->loginInfo->password,$params->loginInfo->isEncrypted);
		
		$vmConfig = getVMconfig();
		if ($vmConfig->get('soap_auth_prod_otherget')==0){
			$result = "true";
		}
		
		//Auth OK
		if ($result == "true"){
		
					
			$query  = "SELECT * FROM #__virtuemart_customs ";
			$query .= "WHERE 1 ";
			
			if (!empty($params->virtuemart_custom_id)){
				$query .= " AND virtuemart_custom_id = '$params->virtuemart_custom_id' ";
			}
			if (!empty($params->custom_parent_id)){
				$query .= " AND custom_parent_id = '$params->custom_parent_id' ";
			}
			if (!empty($params->field_type)){
				$query .= " AND field_type = '$params->field_type' ";
			}
			if (!empty($params->published)){
				$query .= " AND published = '$params->published' ";
			}
			
			$db = JFactory::getDBO();
			$db->setQuery($query);
			
			$rows = $db->loadObjectList();
			
			foreach ($rows as $row){
				
				$custom = new Custom($row->virtuemart_custom_id,
									$row->custom_parent_id,
									$row->admin_only,
									$row->custom_title,
									$row->custom_tip,
									$row->custom_value,
									$row->custom_field_desc,
									$row->field_type,
									$row->is_list,
									$row->is_hidden,
									$row->is_cart_attribute,
									$row->published,
									$row->virtuemart_vendor_id,
									$row->custom_jplugin_id,
									$row->custom_element,
									$row->layout_pos,
									$row->custom_params
					
									);
				$customArray[] = $custom;
				
			}

			
			$errMsg=  $db->getErrorMsg();
			
			if ($errMsg==null){
				return $customArray;
			} else {
				return new SoapFault("GetCustomsListFault", "cannot Get custom List  "." | ERRLOG : ".$errMsg);				
			}
			
		}else if ($result == "false"){
			return new SoapFault("JoomlaServerAuthFault", "Authentication KO for : ".$params->loginInfo->login);
		}else if ($result == "no_admin"){
			return new SoapFault("JoomlaServerAuthFault", "User is not a Super Administrator : ".$params->loginInfo->login);
		}else{
			return new SoapFault("JoomlaServerAuthFault", "User does not exist : ".$params->loginInfo->login);
		}
		
	}
	
	
	
	/**
    * This function Get Customs Fields for a product
	* (expose as WS)
    * @param string 
    * @return array of custom fields
   */
	function GetCustomsFields($params) {
			
		$product_id = $params->product_id;
		/* Authenticate*/
		$result = onAdminAuthenticate($params->loginInfo->login, $params->loginInfo->password,$params->loginInfo->isEncrypted);
		
		$vmConfig = getVMconfig();
		if ($vmConfig->get('soap_auth_prod_otherget')==0){
			$result = "true";
		}
		
		//Auth OK
		if ($result == "true"){
		
					
			$query  = "SELECT * FROM #__virtuemart_product_customfields ";
			$query .= "WHERE 1 ";
			
			if (!empty($params->virtuemart_customfield_id)){
				$query .= " AND virtuemart_customfield_id = '$params->virtuemart_customfield_id' ";
			}
			if (!empty($params->virtuemart_product_id)){
				$query .= " AND virtuemart_product_id = '$params->virtuemart_product_id' ";
			}
			if (!empty($params->virtuemart_custom_id)){
				$query .= " AND virtuemart_custom_id = '$params->virtuemart_custom_id' ";
			}
			if (!empty($params->published)){
				$query .= " AND published = '$params->published' ";
			}
			
			$db = JFactory::getDBO();
			$db->setQuery($query);
			
			$rows = $db->loadObjectList();
			
			foreach ($rows as $row){
				
				$customfield = new CustomField($row->virtuemart_customfield_id,
									$row->virtuemart_product_id,
									$row->virtuemart_custom_id,
									$row->custom_value,
									$row->custom_price,
									$row->custom_param,
									$row->published
									);
				$customfieldArray[] = $customfield;
				
			}

			
			$errMsg=  $db->getErrorMsg();
			
			if ($errMsg==null){
				return $customfieldArray;
			} else {
				return new SoapFault("GetCustomsFieldsFault", "cannot Get custom Fields  "." | ERRLOG : ".$errMsg);				
			}
			
		}else if ($result == "false"){
			return new SoapFault("JoomlaServerAuthFault", "Authentication KO for : ".$params->loginInfo->login);
		}else if ($result == "no_admin"){
			return new SoapFault("JoomlaServerAuthFault", "User is not a Super Administrator : ".$params->loginInfo->login);
		}else{
			return new SoapFault("JoomlaServerAuthFault", "User does not exist : ".$params->loginInfo->login);
		}
		
	}
	

	/**
	*
	**/
	function discountIdExist($discount_id) {
	
		if ($discount_id == 0){
			return true;
		}
		$list  = "SELECT * FROM #__{vm}_product_discount WHERE discount_id = '$discount_id' ";
		$db = new ps_DB;
		$db->query($list);
		$i=0;
		while ($db->next_record()) {
			$i++;
		}
		if ($i ==0){
			return false;
		}
		return true;
	}
	
	function ProductFileIdExist($file_id) {
	
		/*if ($discount_id == 0){
			return true;
		}*/
		$list  = "SELECT * FROM #__{vm}_product_files WHERE file_id = '$file_id' ";
		$db = new ps_DB;
		$db->query($list);
		$i=0;
		while ($db->next_record()) {
			$i++;
		}
		if ($i ==0){
			return false;
		}
		return true;
	}
	
	
	/**
    * This function GetProductFile
	* (expose as WS)
    * @param string 
    * @return result
   */
	function GetProductFile($params) {
			
		/* Authenticate*/
		$result = onAdminAuthenticate($params->loginInfo->login, $params->loginInfo->password,$params->loginInfo->isEncrypted);
		
		$vmConfig = getVMconfig();
		if ($vmConfig->get('soap_auth_prod_otherget')==0){
			$result = "true";
		}
		
		//Auth OK
		if ($result == "true"){
			
			return new SoapFault("GetProductFileFault", "Not available in VM2, use GetMedia ");
		
			/*if (!empty($params->product_id)){
				$product_id = $params->product_id;
				$list  = "SELECT * FROM #__{vm}_product_file WHERE product_id = '$product_id' ";
				$db = new ps_DB;
				$db->query($list);
				while ($db->next_record()) {
					$params->discount->discount_id = $db->f("product_discount_id");
				}
			}*/
			
			$list  = "SELECT * FROM #__{vm}_product_files WHERE 1";
			
			
			if (!empty($params->file_id)){
				$file_id = $params->file_id;
				$list  .= " AND file_id = '$file_id' ";
			}
			if (!empty($params->file_product_id)){
				$file_product_id = $params->file_product_id;
				$list  .= " AND file_product_id = '$file_product_id' ";
			}
			if (!empty($params->file_name)){
				$file_name = $params->file_name;
				$list  .= " AND file_name like '%$file_name%' ";
			}
			if (!empty($params->file_published)){
				$file_published = $params->file_published;
				$list  .= " AND file_published = '$file_published' ";
			}
			if (!empty($params->file_extension)){
				$file_extension = $params->file_extension;
				$list  .= " AND file_extension = '$file_extension' ";
			}
			if (!empty($params->file_is_image)){
				$file_is_image = $params->file_is_image;
				$list  .= " AND file_is_image = '$file_is_image' ";
			}
			
			
			$db = new ps_DB;
			$db->query($list);
			while ($db->next_record()) {
				$ProductFile = new ProductFile($db->f("file_id"),$db->f("file_product_id"),$db->f("file_name"),$db->f("file_title"),$db->f("file_description"),$db->f("file_extension"),$db->f("file_mimetype"),$db->f("file_url"),$db->f("file_published"),$db->f("file_is_image"),$db->f("file_image_height"),$db->f("file_image_width"),$db->f("file_image_thumb_height"),$db->f("file_image_thumb_width"));
				$ProductFileArray[] = $ProductFile;
			}

			$errMsg=  $db->getErrorMsg();
			
			if ($errMsg==null){
				return $ProductFileArray;
			} else {
				return new SoapFault("GetProductFileFault", "cannot Get ProductFile  "." | ERRLOG : ".$errMsg);				
			}
			//return $ProductPriceArray;
			
		}else if ($result == "false"){
			return new SoapFault("JoomlaServerAuthFault", "Authentication KO for : ".$params->loginInfo->login);
		}else if ($result == "no_admin"){
			return new SoapFault("JoomlaServerAuthFault", "User is not a Super Administrator : ".$params->loginInfo->login);
		}else{
			return new SoapFault("JoomlaServerAuthFault", "User does not exist : ".$params->loginInfo->login);
		}
		
	}	
	

	/**
    * This function GetDiscount
	* (expose as WS)
    * @param string 
    * @return result
   */
	function GetDiscount($params) {
	
		/* Authenticate*/
		$result = onAdminAuthenticate($params->loginInfo->login, $params->loginInfo->password,$params->loginInfo->isEncrypted);
		
		$vmConfig = getVMconfig();
		if ($vmConfig->get('soap_auth_prod_otherget')==0){
			$result = "true";
		}
		
		//Auth OK
		if ($result == "true"){
			
			if (!class_exists( 'VirtueMartModelCalc' )) require (JPATH_VM_ADMINISTRATOR.DS.'models'.DS.'calc.php');
			$modelCalc = new VirtueMartModelCalc;
			
			//unset($modelCalc->_id);
			
			if ($params->calc_kind=="TAX"){
				$rows=$modelCalc->getTaxes();
			}
			if ($params->calc_kind=="DISCOUNT"){
				$rows=$modelCalc->getDiscounts();
			}
			if ($params->calc_kind=="DB-DISCOUNT"){
				$rows=$modelCalc->getDBDiscounts();
			}
			if ($params->calc_kind=="DA-DISCOUNT"){
				$rows=$modelCalc->getDADiscounts();
			}
			
			if ($params->calc_kind==""){
			
				if (!empty($params->discount_id)){
					$modelCalc->_id=$params->discount_id;
					$rows[] = $modelCalc->getCalc();
				}else if (!empty($params->calc_name)) {
					$rows = $modelCalc->getCalcs(false,true,$params->calc_name);
				} else {
					$rows = $modelCalc->getCalcs(false,true);
				}
			
			}
			
			
			
			foreach ($rows as $row){
				
				
				$modelCalcDetail = new VirtueMartModelCalc;
				$modelCalcDetail->_id=$row->virtuemart_calc_id;
				$calcDetail = $modelCalcDetail->getCalc();
				
			
				$discount_cat_ids = implode ('|',$calcDetail->calc_categories);
				$discount_countries_ids = implode ('|',$calcDetail->calc_countries);
				$discount_shoppergroups_ids = implode ('|',$calcDetail->virtuemart_shoppergroup_ids);
				$discount_states_ids = implode ('|',$calcDetail->virtuemart_state_ids);
				
				

				$Discount = new Discount($row->virtuemart_calc_id,
								$row->virtuemart_vendor_id,
								$row->calc_name,
								$row->calc_descr,
								$row->calc_kind,
								$row->calc_value_mathop,
								$row->calc_value,
								$row->calc_currency,
								$row->calc_shopper_published,
								$row->calc_vendor_published,
								$row->publish_up,
								$row->publish_down,
								$row->calc_qualify,
								$row->calc_affected,
								$row->calc_amount_cond,
								$row->calc_amount_dimunit,
								$row->for_override,
								$row->ordering,
								$row->shared,
								$row->published,
								$discount_cat_ids,
								$discount_countries_ids,
								$discount_shoppergroups_ids,
								$discount_states_ids
								);
				$DiscountArray[] = $Discount;

			}
			return $DiscountArray;
			
			
		}else if ($result == "false"){
			return new SoapFault("JoomlaServerAuthFault", "Authentication KO for : ".$params->loginInfo->login);
		}else if ($result == "no_admin"){
			return new SoapFault("JoomlaServerAuthFault", "User is not a Super Administrator : ".$params->loginInfo->login);
		}else{
			return new SoapFault("JoomlaServerAuthFault", "User does not exist : ".$params->loginInfo->login);
		}
		
	}	
	

	/**
    * This function GetProductPrices
	* (expose as WS)
    * @param string 
    * @return result
   */
	function GetProductPrices($params) {

		/* Authenticate*/
		$result = onAdminAuthenticate($params->loginInfo->login, $params->loginInfo->password,$params->loginInfo->isEncrypted);
		
		$vmConfig = getVMconfig();
		if ($vmConfig->get('soap_auth_prod_otherget')==0){
			$result = "true";
		}
		//Auth OK
		if ($result == "true"){
		
			/*used to get final price*/
			if (isSupEqualVmVersion('2.0.3')){
				if (!class_exists( 'calculationHelper' )) require (JPATH_VM_ADMINISTRATOR.DS.'helpers'.DS.'calculationh.php');
				$calculationHelper = calculationHelper::getInstance();
			}
				
			$db = JFactory::getDBO();	
			$query  = "SELECT *  FROM #__virtuemart_product_prices WHERE 1 ";
			if (!empty($params->product_id)){
				$query  .= " AND virtuemart_product_id = $params->product_id ";
			}
			if (!empty($params->shopper_group_id)){
				$query  .= " AND virtuemart_shoppergroup_id = $params->shopper_group_id ";
			}
			if (!empty($params->product_currency)){
				$query  .= " AND product_currency = '$params->product_currency' ";
			}
			
			$db->setQuery($query);
			
			$rows = $db->loadObjectList();
			
			foreach ($rows as $row){
				if (isSupEqualVmVersion('2.0.3')){
					$finalPrice = $calculationHelper->getProductPrices($row->virtuemart_product_id);
					$price_info = array_implode('=','|',$finalPrice);
					
					/************ 2.0.7 ************/
					if (isSupEqualVmVersion('2.0.7')){ // since 2.0.7d getProductPrices take object and not id
						if (!class_exists( 'VirtueMartModelProduct' )) require (JPATH_VM_ADMINISTRATOR.DS.'models'.DS.'product.php');
						$VirtueMartModelProduct = new VirtueMartModelProduct;
						$productsIds[] = $params->product_id;
						$products = $VirtueMartModelProduct->getProducts($productsIds,false,false,false); //false for back end
						$finalPrice = $calculationHelper->getProductPrices($products[0]);
						$price_info = array_implode('=','|',$finalPrice);
					}
				}
			
				$ProductPrice = new ProductPrice($row->virtuemart_product_price_id,
													$row->virtuemart_product_id,
													$row->product_price,
													$row->product_currency,
													$row->product_price_vdate,
													$row->product_price_edate ,
													$row->created_on,
													$row->modified_on,
													$row->virtuemart_shoppergroup_id,
													$row->price_quantity_start,
													$row->price_quantity_end,
													$row->override,
													$row->product_override_price,
													$row->product_tax_id,
													$row->product_discount_id,
													$finalPrice['basePriceWithTax'],
													$price_info
													);
				$ProductPriceArray[] = $ProductPrice;
			}
			return $ProductPriceArray;
			/*
			$list  = "SELECT * FROM #__{vm}_product_price WHERE 1";
			
			
			if (!empty($params->product_id)){
				$list  .= " AND product_id = $params->product_id ";
			}
			if (!empty($params->shopper_group_id)){
				$list  .= " AND shopper_group_id = $params->shopper_group_id ";
			}
			if (!empty($params->product_currency)){
				$list  .= " AND product_currency = '$params->product_currency' ";
			}
			
			$db = new ps_DB;
			$db->query($list);
			while ($db->next_record()) {
				$ProductPrice = new ProductPrice($db->f("product_price_id"),$db->f("product_id"),$db->f("product_price"),$db->f("product_currency"),$db->f("product_price_vdate"),$db->f("product_price_edate") ,$db->f("cdate"),$db->f("mdate"),$db->f("shopper_group_id"),$db->f("price_quantity_start"),$db->f("price_quantity_end"));
				$ProductPriceArray[] = $ProductPrice;
			}

			$errMsg=  $db->getErrorMsg();
			
			if ($errMsg==null){
				return $ProductPriceArray;
			} else {
				return new SoapFault("JoomlaGetProductPricesFault", "cannot execute SQL Select Query  ".$list." | ERRLOG : ".$errMsg);				
			}*/
			
			
		}else if ($result == "false"){
			return new SoapFault("JoomlaServerAuthFault", "Authentication KO for : ".$params->loginInfo->login);
		}else if ($result == "no_admin"){
			return new SoapFault("JoomlaServerAuthFault", "User is not a Super Administrator : ".$params->loginInfo->login);
		}else{
			return new SoapFault("JoomlaServerAuthFault", "User does not exist : ".$params->loginInfo->login);
		}
		
	}


	
	/**
    * This function Get All Tax
	* (expose as WS)
    * @param string 
    * @return result
   */
	function GetAllTax($params) {
	
		/* Authenticate*/
		$result = onAdminAuthenticate($params->login, $params->password);
		
		$vmConfig = getVMconfig();
		if ($vmConfig->get('soap_auth_prod_otherget')==0){
			$result = "true";
		}
		
		//Auth OK
		if ($result == "true"){
		
			//A REVOIR COMPLETEMENT MODIFIER DANS VM2////
			$list  = "SELECT * FROM #__{vm}_tax_rate WHERE 1";
			
			$db = new ps_DB;
			$db->query($list);
			while ($db->next_record()) {
				$Tax = new Tax($db->f("tax_rate_id"),$db->f("vendor_id"),$db->f("tax_state"),$db->f("tax_country"),$db->f("mdate"),$db->f("tax_rate"));
				$TaxArray[] = $Tax;
			}

			return $TaxArray;
			
		}else if ($result == "false"){
			return new SoapFault("JoomlaServerAuthFault", "Authentication KO for : ".$params->login);
		}else if ($result == "no_admin"){
			return new SoapFault("JoomlaServerAuthFault", "User is not a Super Administrator : ".$params->login);
		}else{
			return new SoapFault("JoomlaServerAuthFault", "User does not exist : ".$params->login);
		}
		
	}
	
	/**
    * This function Add Tax
	* (expose as WS)
    * @param string 
    * @return result
   */
	function AddTax($params) {

		$product_id = $params->product_id;
		/* Authenticate*/
		$result = onAdminAuthenticate($params->loginInfo->login, $params->loginInfo->password,$params->loginInfo->isEncrypted);
		
		$vmConfig = getVMconfig();
		if ($vmConfig->get('soap_auth_prod_otheradd')==0){
			$result = "true";
		}
		//Auth OK
		if ($result == "true"){
		
			return new SoapFault("AddTaxFault", "Not IN VM2 : Use Discount");
			
				
		}else if ($result == "false"){
			return new SoapFault("JoomlaServerAuthFault", "Authentication KO for : ".$params->loginInfo->login);
		}else if ($result == "no_admin"){
			return new SoapFault("JoomlaServerAuthFault", "User is not a Super Administrator : ".$params->loginInfo->login);
		}else{
			return new SoapFault("JoomlaServerAuthFault", "User does not exist : ".$params->loginInfo->login);
		}
		
	}
	
	/**
    * This function get All Products
	* (expose as WS)
    * @param string
    * @return array of products
   */
	function GetAllProducts($params) {
	
		/* Authenticate*/
		$result = onAdminAuthenticate($params->loginInfo->login, $params->loginInfo->password,$params->loginInfo->isEncrypted);
		
		$vmConfig = getVMconfig();
		if ($vmConfig->get('soap_auth_getprod')==0){
			$result = "true";
		}
		
		//Auth OK
		if ($result == "true"){
			
			setToken();
			
			$_REQUEST['filter_order_Dir'] = "DESC"; // since vm rc2.0.3b
			
			$limite_start = $params->limite_start;
			if (empty($limite_start)){
				$limite_start = "0";
			}
			$limite_end = $params->limite_end;
			if (empty($limite_end)){
				$limite_end = "500";
			}
			$maxNumber = $vmConfig->get('absMaxProducts',700);
			//we want more results than max define in VM core
			if ($limite_end >$maxNumber){
				storeVmConfig('absMaxProducts',intval($limite_end) );
			}
			
			$db = JFactory::getDBO();	
			$query  = "SELECT virtuemart_product_id  ";
			$query .= "FROM #__virtuemart_products ";
			$query .= " LIMIT $limite_start,$limite_end "; 
			$db->setQuery($query);
			
			$rows = $db->loadObjectList();
			
			$productIds;
			foreach ($rows as $row){
					$productIds[] = $row->virtuemart_product_id;
			}
			
			if (!class_exists( 'VirtueMartModelProduct' )) require (JPATH_VM_ADMINISTRATOR.DS.'models'.DS.'product.php');
			$VirtueMartModelProduct = new VirtueMartModelProduct;
			
			$products = $VirtueMartModelProduct->getProducts($productIds,false,false,false); //false for back end
			
			foreach ($products as $ProductDetails){
				
				unset($prod_prices);
				if ($params->include_prices == 'Y' || $params->include_prices == '1' ){
					unset($params->shopper_group_id);
					unset($params->product_currency);
					$params->product_id = $ProductDetails->virtuemart_product_id;
					$prod_prices = GetProductPrices($params);
				}
				
				$img = GetDefaultImages($params,$ProductDetails->virtuemart_product_id,false);
				$imgThumb = GetDefaultImages($params,$ProductDetails->virtuemart_product_id,true);
				$shoppergroups_ids = implode ('|',$ProductDetails->shoppergroups);
				
				$Product = new Product($ProductDetails->virtuemart_product_id/*$ProductDetails->prices[0]*/ ,
								$ProductDetails->virtuemart_vendor_id,
								$ProductDetails->product_parent_id,
								$ProductDetails->product_sku,
								$ProductDetails->product_name,
								$ProductDetails->slug ,
								$ProductDetails->product_s_desc,
								$ProductDetails->product_desc ,
								$ProductDetails->product_weight ,
								$ProductDetails->product_weight_uom,
								$ProductDetails->product_length,
								$ProductDetails->product_width,
								$ProductDetails->product_height,
								$ProductDetails->product_lwh_uom,
								$ProductDetails->product_url,
								$ProductDetails->product_in_stock,
								$ProductDetails->low_stock_notification,
								$ProductDetails->product_available_date,
								$ProductDetails->product_availability,
								$ProductDetails->product_special,
								$ProductDetails->ship_code_id,
								$ProductDetails->product_sales,
								$ProductDetails->product_unit,
								$ProductDetails->product_packaging,
								$ProductDetails->product_ordered,
								$ProductDetails->hits,
								$ProductDetails->intnotes,
								$ProductDetails->metadesc, 
								$ProductDetails->metakey, 
								$ProductDetails->metarobot,
								$ProductDetails->metaauthor,
								$ProductDetails->layout,
								$ProductDetails->published, 
								getCategoriesIds($ProductDetails->virtuemart_product_id),
								getManufacturerId($ProductDetails->virtuemart_product_id),
								$ProductDetails->product_params,
								$img,
								$imgThumb,
								$ProductDetails->shared,
								$ProductDetails->ordering,
								$ProductDetails->customtitle,
								$shoppergroups_ids,
								$prod_prices
								);
				$ProductArray[] = $Product;
					
			}
			return $ProductArray;
	
		 
		
		}else if ($result == "false"){
			return new SoapFault("JoomlaServerAuthFault", "Authentication KO for : ".$params->loginInfo->login);
		}else if ($result == "no_admin"){
			return new SoapFault("JoomlaServerAuthFault", "User is not a Super Administrator : ".$params->loginInfo->login);
		}else{
			return new SoapFault("JoomlaServerAuthFault", "User does not exist : ".$params->loginInfo->login);
		}		
	}
	
	/**
    * This function get Get Available Images on server (dir components/com_virtuemart/shop_image/product)
	* (expose as WS)
    * @param string
    * @return array of products
   */
	function GetAvailableImages($params) {
	
		/* Authenticate*/
		$result = onAdminAuthenticate($params->loginInfo->login, $params->loginInfo->password,$params->loginInfo->isEncrypted);
		
		$vmConfig = getVMconfig();
		if ($vmConfig->get('soap_auth_prod_otherget')==0){
			$result = "true";
		}
		
		//Auth OK
		if ($result == "true"){
		
			if (!class_exists( 'VmConfig' )) require(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_virtuemart'.DS.'helpers'.DS.'config.php');
			$vmConfig = VmConfig::loadConfig();
			
			$media_category_path = $vmConfig->get('media_product_path');
			
			$uri = JURI::base();
			$uri = str_replace('administrator/components/com_vm_soa/services/', "", $uri);
			
			$INSTALLURL = '';
			if (empty($conf['BASESITE']) && empty($conf['URL'])){
				$INSTALLURL = $uri;
			} else if (!empty($conf['BASESITE'])){
				$INSTALLURL = 'http://'.$conf['URL'].'/'.$conf['BASESITE'].'/';
			} else {
				$INSTALLURL = 'http://'.$conf['URL'].'/';
			}
			
			if ($params->img_type == "full" || $params->img_type == "all" || $params->img_type == ""){
			
				$dir = JPATH.DS.$media_category_path.'';		
				// Ouvre un dossier bien connu, et liste tous les fichiers
				if (is_dir($dir)) {
					if ($dh = opendir($dir)) {
						while (($file = readdir($dh)) !== false) {
							//echo "fichier : $file : type : " . filetype($dir . $file) . "\n";
							if ($file =="." || $file ==".." || $file =="index.html"){
								
							} else {
								$AvalaibleImage = new AvalaibleImage($file,$INSTALLURL.$media_category_path.$file,$dir,$media_category_path.$file);
								$AvalaibleImageArray[] = $AvalaibleImage;
							}
						}
						closedir($dh);
					}
				}
			}
			if ($params->img_type == "thumb" || $params->img_type == "all" || $params->img_type == ""){
				
				$dir = JPATH.DS.$media_category_path.'resized';
				
				// Ouvre un dossier bien connu, et liste tous les fichiers
				if (is_dir($dir)) {
					if ($dh = opendir($dir)) {
						while (($file = readdir($dh)) !== false) {
							
							if ($file =="." || $file ==".." || $file =="index.html"){
								
							} else {
							$AvalaibleImage = new AvalaibleImage($file,$INSTALLURL.$media_category_path.'resized/'.$file,$dir,$media_category_path.'resized/'.$file);
							$AvalaibleImageArray[] = $AvalaibleImage;
							}
						}
						closedir($dh);
					}
				}
			}
			return $AvalaibleImageArray;

			
		}else if ($result == "false"){
			return new SoapFault("JoomlaServerAuthFault", "Authentication KO for : ".$params->login);
		}else if ($result == "no_admin"){
			return new SoapFault("JoomlaServerAuthFault", "User is not a Super Administrator : ".$params->login);
		}else{
			return new SoapFault("JoomlaServerAuthFault", "User does not exist : ".$params->login);
		}		
	}
	
	/**
    * This function get Get Available Images on server (dir components/com_virtuemart/shop_image/product)
	* (expose as WS)
    * @param string
    * @return array of products
   */
	function GetAvailableFiles($params) {
	
		/* Authenticate*/
		$result = onAdminAuthenticate($params->login, $params->password);
		
		$vmConfig = getVMconfig();
		if ($vmConfig->get('soap_auth_prod_otherget')==0){
			$result = "true";
		}
		
		//Auth OK
		if ($result == "true"){
		
			return new SoapFault("JoomlaServerAuthFault", "Not available in VM2, use Media");
			
			if (!class_exists( 'VmConfig' )) require(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_virtuemart'.DS.'helpers'.DS.'config.php');
			$vmConfig = VmConfig::loadConfig();
			
			$media_category_path = $vmConfig->get('media_product_path');
			
			$uri = JURI::base();
			$uri = str_replace('administrator/components/com_vm_soa/services/', "", $uri);
			
			$INSTALLURL = '';
			if (empty($conf['BASESITE']) && empty($conf['URL'])){
				$INSTALLURL = $uri;
			} else if (!empty($conf['BASESITE'])){
				$INSTALLURL = 'http://'.$conf['URL'].'/'.$conf['BASESITE'].'/';
			} else {
				$INSTALLURL = 'http://'.$conf['URL'].'/';
			}
			
			
			$dir = realpath( dirname(__FILE__).'/../../../../media' );
			$dirname = $dir;
			//$dir = "/tmp/php5";
			// Ouvre un dossier bien connu, et liste tous les fichiers
			if (is_dir($dir)) {
				if ($dh = opendir($dir)) {
					while (($file = readdir($dh)) !== false) {
						//echo "fichier : $file : type : " . filetype($dir . $file) . "\n";
						if (!empty($conf['BASESITE'])){
							$AvalaibleFile = new AvalaibleFile($file,'http://'.$conf['URL'].'/'.$conf['BASESITE'].'/media/'.$file,$dirname);
						}else {
							$AvalaibleFile = new AvalaibleFile($file,'http://'.$conf['URL'].'/media/'.$file,$dirname);
						}
						
						$AvalaibleFileArray[] = $AvalaibleFile;
					}
					closedir($dh);
				}
			}
			
			
			return $AvalaibleFileArray;

			
		}else if ($result == "false"){
			return new SoapFault("JoomlaServerAuthFault", "Authentication KO for : ".$params->login);
		}else if ($result == "no_admin"){
			return new SoapFault("JoomlaServerAuthFault", "User is not a Super Administrator : ".$params->login);
		}else{
			return new SoapFault("JoomlaServerAuthFault", "User does not exist : ".$params->login);
		}		
	}
	
		/**
    * This function get default img cat
	* insternal use
    * @param Object
    * @return array of Categories
   */
	function GetDefaultImages($params,$prod_id,$thumb = false) {
	
		$params->product_id = $prod_id;
		$medias = GetMediaProduct($params);
		
		$img_cat = "";
		foreach ($medias as $media){
			$img_cat = $media->file_url;
			if ($thumb){
				$img_cat = $media->file_url_thumb; 
			}
			return $img_cat;//return first one
		}
		return $img_cat;
	
	}
	/**
    * This function get All medias for product
	* (expose as WS)
    * @param string The id of the product
    * @return array of Media
   */
	function GetMediaProduct($params) {
	
		/* Authenticate*/
		$result = onAdminAuthenticate($params->loginInfo->login, $params->loginInfo->password,$params->loginInfo->isEncrypted);
		
		$vmConfig = getVMconfig();
		if ($vmConfig->get('soap_auth_prod_otherget')==0){
			$result = "true";
		}
		
		//Auth OK
		if ($result == "true"){
		
			if(!class_exists('VirtueMartModelMedia')) require(JPATH_VM_ADMINISTRATOR.DS.'models'.DS.'media.php');
			$mediaModel = new VirtueMartModelMedia();
			if (isSupEqualVmVersion('2.0.3')){
				$mediaModel->_selectedOrdering="virtuemart_media_id"; //update for vm rc2.0.3b bug
				$mediaModel->addvalidOrderingFieldName(array('virtuemart_media_id')); //update for vm rc2.0.3b bug
			}
			
			$prod_id = $params->product_id;
			$files = $mediaModel->getFiles(false,true,$prod_id,null);

			foreach ($files as $file){
				
	
				$media = new Media($file->virtuemart_media_id,
											$file->virtuemart_vendor_id,
											$file->file_title,
											$file->file_description,
											$file->file_meta,
											$file->file_mimetype,
											$file->file_type,
											$file->file_url,
											$file->file_url_thumb,
											$file->file_is_product_image,
											$file->file_is_downloadable,
											$file->file_is_forSale,
											$file->file_params,
											$file->ordering,
											$file->shared,
											$file->published,
											""
											);
				$mediaArray[] = $media;
			}
			return $mediaArray;
			
		
		}else if ($result == "false"){
			return new SoapFault("JoomlaServerAuthFault", "Authentication KO for : ".$params->loginInfo->login);
		}else if ($result == "no_admin"){
			return new SoapFault("JoomlaServerAuthFault", "User is not a Super Administrator : ".$params->loginInfo->login);
		}else{
			return new SoapFault("JoomlaServerAuthFault", "User does not exist : ".$params->loginInfo->login);
		}
	}
	
	
		/**
    * This function Get World Zones
	* (expose as WS)
    * @param Object
    * @return WorldZones
    */
	function GetWorldZones($params) {
		
		/* Authenticate*/
		$result = onAdminAuthenticate($params->loginInfo->login, $params->loginInfo->password,$params->loginInfo->isEncrypted);

		$vmConfig = getVMconfig();
		if ($vmConfig->get('soap_auth_prod_otherget')==0){
			$result = "true";
		}
		
		//Auth OK
		if ($result == "true"){
			
			setToken();
			
			$db = JFactory::getDBO();	
			$query  = "SELECT * FROM #__virtuemart_worldzones WHERE 1 ";
			
			
			if (!empty($params->Worldzone->virtuemart_worldzone_id)){
				$virtuemart_worldzone_id = $params->Worldzone->virtuemart_worldzone_id;
				$query  .= " AND virtuemart_worldzone_id = '$virtuemart_worldzone_id' "; 
			}
			if (!empty($params->Worldzone->virtuemart_vendor_id)){
				$virtuemart_vendor_id = $params->Worldzone->virtuemart_vendor_id;
				$query  .= " AND virtuemart_vendor_id = '$virtuemart_vendor_id' "; 
			}
			if (!empty($params->Worldzone->zone_name)){
				$zone_name = $params->Worldzone->zone_name;
				$query  .= " AND zone_name LIKE '%$zone_name%' "; 
			}
			if (!empty($params->Worldzone->zone_cost)){
				$zone_cost = $params->Worldzone->zone_cost;
				$query  .= " AND zone_cost = '$zone_cost' "; 
			}
			if (!empty($params->Worldzone->zone_limit)){
				$zone_limit = $params->Worldzone->zone_limit;
				$query  .= " AND zone_limit = '$zone_limit' "; 
			}
			if (!empty($params->Worldzone->zone_description)){
				$zone_description = $params->Worldzone->zone_description;
				$query  .= " AND zone_description LIKE '%$zone_description%' "; 
			}
			if (!empty($params->Worldzone->zone_tax_rate)){
				$zone_tax_rate = $params->Worldzone->zone_tax_rate;
				$query  .= " AND zone_tax_rate = '$zone_tax_rate' "; 
			}
			if (!empty($params->Worldzone->ordering)){
				$ordering = $params->Worldzone->ordering;
				$query  .= " AND ordering = '$ordering' "; 
			}
			if (!empty($params->Worldzone->shared)){
				$shared = $params->Worldzone->shared;
				$query  .= " AND shared = '$shared' "; 
			}
			if (!empty($params->Worldzone->published)){
				$published = $params->Worldzone->published;
				$query  .= " AND published = '$published' "; 
			}

			$db->setQuery($query);
			
			$rows = $db->loadObjectList();
			
			foreach ($rows as $row){
					
				$worldzone = new Worldzone($row->virtuemart_worldzone_id,
											$row->virtuemart_vendor_id,
											$row->zone_name,
											$row->zone_cost,
											$row->zone_limit,
											$row->zone_description,
											$row->zone_tax_rate,
											$row->ordering,
											$row->shared,
											$row->published
											);
				
				$worldzoneArray[] = $worldzone;
			}
			
			return $worldzoneArray;
			
		//Auth KO
		}else if ($result == "false"){
			return new SoapFault("JoomlaServerAuthFault", "Authentication KO for : ".$params->loginInfo->login);
		}else if ($result == "no_admin"){
			return new SoapFault("JoomlaServerAuthFault", "User is not a Super Administrator : ".$params->loginInfo->login);
		}else{
			return new SoapFault("JoomlaServerAuthFault", "User does not exist : ".$params->loginInfo->login);
		}
	}	
	

	/* SOAP SETTINGS */
	
	if ($vmConfig->get('soap_ws_prod_on')==1){
	
		/* SOAP SETTINGS */
		ini_set("soap.wsdl_cache_enabled", $vmConfig->get('soap_ws_prod_cache_on')); // wsdl cache settings
		
		$options = array('soap_version' => SOAP_1_2);

		
		/** SOAP SERVER **/
		$uri = str_replace("/free", "", JURI::root(false));
		if (empty($conf['BASESITE']) && empty($conf['URL'])){
			$server = new SoapServer('..'.DS.'VM_Product.wsdl');
			//$server = new SoapServer($uri.'/VM_ProductWSDL.php');
		}else if (!empty($conf['BASESITE'])){
			$server = new SoapServer('http://'.$conf['URL'].'/'.$conf['BASESITE'].'/administrator/components/com_virtuemart/services/VM_ProductWSDL.php');
		}else {
			$server = new SoapServer('http://'.$conf['URL'].'/administrator/components/com_virtuemart/services/VM_ProductWSDL.php');
		}
		
		/* Add Functions */
		$server->addFunction("GetProductsFromCategory");
		$server->addFunction("GetChildsProduct");
		$server->addFunction("GetProductFromId");
		$server->addFunction("GetProductFromSKU");
		$server->addFunction("GetProductsFromOrderId");
		$server->addFunction("GetAllCurrency");	
		$server->addFunction("GetAllTax");	
		$server->addFunction("GetAllProducts");
		$server->addFunction("GetAvailableImages");
		$server->addFunction("GetProductPrices");
		$server->addFunction("GetDiscount");
		$server->addFunction("GetProductFile");
		$server->addFunction("GetAvailableFiles");
		$server->addFunction("SearchProducts");
		$server->addFunction("GetProductVote");
		$server->addFunction("GetProductReviews");
		$server->addFunction("GetRelatedProducts");
		$server->addFunction("GetMediaProduct");
		$server->addFunction("GetCustomsList");
		$server->addFunction("GetCustomsFields");
		
		$server->addFunction("GetWorldZones");
		
			
		$server->handle();

	}else{
		echoXmlMessageWSDisabled('Product');
	}
	
?> 