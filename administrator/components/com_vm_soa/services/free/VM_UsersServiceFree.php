<?php

define( '_VALID_MOS', 1 );
define( '_JEXEC', 1 );

/**
 * Virtuemart User SOA Connector
 *
 * Virtuemart User SOA Connector (Provide functions GetUsers, Authentification ...)
 *
 * @package    com_vm_soa
 * @subpackage modules
 * @author     Mickael cabanas (cabanas.mickael|at|gmail.com)
 * @copyright  2012 Mickael Cabanas
 * @license    http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version    $Id:$
 */

 /** loading framework **/
include_once('../VM_Commons.php');

/**
 * Class User
 *
 * Class "User" with attribute : id, name, code,
 * 
 *
 * @author     Mickael cabanas (cabanas.mickael|at|gmail.com)
 * @copyright  Mickael cabanas
 * @license    http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version    Release:
 */
	class User {
	
		public $user_id="";
		public $email="";
		public $username="";
		public $password="";
		public $userinfo_id="";
		public $address_type="";
		public $address_type_name="";
		public $name="";
		public $company="";
		public $title="";
		public $last_name="";
		public $first_name="";
		public $middle_name="";
		public $phone_1="";
		public $phone_2="";
		public $fax="";
		public $address_1="";
		public $address_2="";
		public $city="";
		public $virtuemart_state_id="";
		public $virtuemart_country_id="";
		public $zip="";
		public $extra_field_1="";
		public $extra_field_2="";
		public $extra_field_3="";
		public $extra_field_4="";
		public $extra_field_5="";
		public $created_on="";
		public $modified_on="";
		public $user_is_vendor="";
		public $customer_number="";
		public $perms="";
		public $virtuemart_paymentmethod_id="";
		public $virtuemart_shippingcarrier_id="";
		public $agreed="";
		public $shoppergroup_id="";
		public $extra_fields_data="";
		
		
		
		
		function __construct($user_id,$email,$username,$password,$userinfo_id,$address_type,$address_type_name,$name,$company,$title,$last_name,
							$first_name,$middle_name,$phone_1,$phone_2,$fax,$address_1,$address_2,$city,$virtuemart_state_id,$virtuemart_country_id,$zip,
							$extra_field_1,$extra_field_2,$extra_field_3,$extra_field_4,$extra_field_5,$created_on,$modified_on
							,$user_is_vendor,$customer_number,$perms,$virtuemart_paymentmethod_id,$virtuemart_shippingcarrier_id,$agreed,$shoppergroup_id,$extra_fields_data){
		
			$this->user_id					=$user_id;
			$this->email					=$email;
			$this->username					=$username;
			$this->password					=$password;
			$this->userinfo_id				=$userinfo_id;
			$this->address_type				=$address_type;
			$this->address_type_name		=$address_type_name;
			$this->name						=$name;
			$this->company					=$company;
			$this->title					=$title;
			$this->last_name				=$last_name;
			$this->first_name				=$first_name;
			$this->middle_name				=$middle_name;
			$this->phone_1					=$phone_1;
			$this->phone_2					=$phone_2;
			$this->fax						=$fax;
			$this->address_1				=$address_1;
			$this->address_2				=$address_2;
			$this->city						=$city;
			$this->virtuemart_state_id		=$virtuemart_state_id;
			$this->virtuemart_country_id	=$virtuemart_country_id;
			$this->zip						=$zip;
			$this->extra_field_1			=$extra_field_1;
			$this->extra_field_2			=$extra_field_2;
			$this->extra_field_3			=$extra_field_3;
			$this->extra_field_4			=$extra_field_4;
			$this->extra_field_5			=$extra_field_5;
			$this->created_on				=$created_on;
			$this->modified_on				=$modified_on;
			$this->user_is_vendor			=$user_is_vendor;
			$this->customer_number			=$customer_number;
			$this->perms					=$perms;
			$this->virtuemart_paymentmethod_id		=$virtuemart_paymentmethod_id;
			$this->virtuemart_shippingcarrier_id 	=$virtuemart_shippingcarrier_id;
			$this->agreed					=$agreed;
			$this->shoppergroup_id			=$shoppergroup_id;
			$this->extra_fields_data		=$extra_fields_data;
			
			
			
		}
	
	}
	
	/**
	 * Class Country
	 *
	 * Class "Country" with attribute : country_id, zone_id, country_name,country_3_code, country_2_code
	 * 
	 *
	 * @author     Mickael cabanas (cabanas.mickael|at|gmail.com)
	 * @copyright  Mickael cabanas
	 * @license    http://www.gnu.org/copyleft/gpl.html GNU/GPL
	 * @version    Release:
	 */
	class Country {
	
		public $country_id="";
		public $virtuemart_worldzone_id="";
		public $country_name="";		
		public $country_3_code="";	
		public $country_2_code="";
		public $published="";
		
		
		
		function __construct($country_id, $virtuemart_worldzone_id,$country_name,$country_3_code,$country_2_code,$published){
		
			$this->country_id				=$country_id;
			$this->virtuemart_worldzone_id	=$virtuemart_worldzone_id;
			$this->country_name				=$country_name;		
			$this->country_3_code			=$country_3_code;	
			$this->country_2_code			=$country_2_code;
			$this->published				=$published;				
			
		}
	}
  
  	/**
	 * Class AuthGroup (Permsgroup in VM2)
	 *
	 * Class "AuthGroup" with attribute : group_id ...
	 * 
	 *
	 * @author     Mickael cabanas (cabanas.mickael|at|gmail.com)
	 * @copyright  Mickael cabanas
	 * @license    http://www.gnu.org/copyleft/gpl.html GNU/GPL
	 * @version    Release:
	 */
	class AuthGroup {
	
		public $group_id="";
		public $vendor_id="";
		public $group_name="";		
		public $group_level="";	
		public $ordering="";	
		public $shared="";	
		public $published="";	
		
		function __construct($group_id, $vendor_id,$group_name, $group_level, $ordering, $shared, $published){
		
			$this->group_id		=$group_id;
			$this->vendor_id	=$vendor_id;
			$this->group_name	=$group_name;
			$this->group_level	=$group_level;
			$this->ordering		=$ordering;
			$this->shared		=$shared;
			$this->published	=$published;			
		}
	}
  
  	/**
	 * Class State
	 *
	 * Class "State" with attribute : state_id ...
	 * 
	 *
	 * @author     Mickael cabanas (cabanas.mickael|at|gmail.com)
	 * @copyright  Mickael cabanas
	 * @license    http://www.gnu.org/copyleft/gpl.html GNU/GPL
	 * @version    Release:
	 */
	class State {
	
		public $state_id="";
		public $virtuemart_vendor_id="";
		public $virtuemart_country_id="";		
		public $virtuemart_worldzone_id="";	
		public $state_name="";
		public $state_3_code="";
		public $state_2_code="";
		public $published="";
		
		
		function __construct($state_id, $virtuemart_vendor_id,$virtuemart_country_id,$virtuemart_worldzone_id,$state_name,$state_3_code,$state_2_code,$published){
		
			$this->state_id					=$state_id;
			$this->virtuemart_vendor_id		=$virtuemart_vendor_id;
			$this->virtuemart_country_id	=$virtuemart_country_id;		
			$this->virtuemart_worldzone_id	=$virtuemart_worldzone_id;	
			$this->state_name				=$state_name;	
			$this->state_3_code				=$state_3_code;
			$this->state_2_code				=$state_2_code;
			$this->published				=$published;
			
		}
	}
  
    /**
	 * Class ShopperGroup
	 *
	 * Class "ShopperGroup" with attribute :  shopper_group_id ...
	 * 
	 *
	 * @author     Mickael cabanas (cabanas.mickael|at|gmail.com)
	 * @copyright  Mickael cabanas
	 * @license    http://www.gnu.org/copyleft/gpl.html GNU/GPL
	 * @version    Release:
	 */
	class ShopperGroup {
	
		public $shopper_group_id="";
		public $vendor_id="";
		public $shopper_group_name="";		
		public $shopper_group_desc="";	
		public $custom_price_display="";
		public $price_display="";
		public $default="";
		public $ordering="";
		public $shared="";
		public $published="";
		
		function __construct($shopper_group_id, $vendor_id,$shopper_group_name,$shopper_group_desc,$custom_price_display,$price_display,$default,$ordering,$shared,$published){
		
			$this->shopper_group_id		=$shopper_group_id;
			$this->vendor_id			=$vendor_id;
			$this->shopper_group_name	=$shopper_group_name;		
			$this->shopper_group_desc	=$shopper_group_desc;	
			$this->custom_price_display	=$custom_price_display;	
			$this->price_display		=$price_display;	
			$this->default				=$default;
			$this->ordering				=$ordering;
			$this->shared				=$shared;
			$this->published			=$published;			
		}
	}
  
 /**
 * Class User
 *
 * Class "Vendor" with attribute : vendor_id...
 * 
 *
 * @author     Mickael cabanas (cabanas.mickael|at|gmail.com)
 * @copyright  Mickael cabanas
 * @license    http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version    Release:
 */
	class Vendor {
	
		public $vendor_id="";
		public $vendor_name="";
		public $vendor_phone="";	
		public $vendor_store_name="";	
		public $vendor_store_desc="";	
		public $vendor_currency="";
		public $vendor_image_path="";	
		public $vendor_terms_of_service="";	
		public $vendor_url="";	
		public $slug ="";	
		public $vendor_freeshipping="";
		public $vendor_accepted_currencies="";	
		public $vendor_address_format="";
		public $vendor_date_format ="";
		public $vendor_params="";	
		public $img_uri="";	
		public $img_thumb_uri="";
		public $userInfo;
		
		
		function __construct($vendor_id, $vendor_name,$vendor_phone,$vendor_store_name,$vendor_store_desc,$vendor_currency,$vendor_image_path,$vendor_terms_of_service,$vendor_url,$slug ,$vendor_freeshipping,
		$vendor_accepted_currencies,$vendor_address_format,$vendor_date_format,$vendor_params,$img_uri,$img_thumb_uri,$userInfo){
		
			$this->vendor_id					=$vendor_id;
			$this->vendor_name					=$vendor_name;
			$this->vendor_phone					=$vendor_phone;		
			$this->vendor_store_name			=$vendor_store_name;	
			$this->vendor_store_desc			=$vendor_store_desc;
			$this->vendor_currency				=$vendor_currency;
			$this->vendor_image_path			=$vendor_image_path;
			$this->vendor_terms_of_service		=$vendor_terms_of_service;
			$this->vendor_url					=$vendor_url;
			$this->slug 						=$slug ;
			$this->vendor_freeshipping			=$vendor_freeshipping;
			$this->vendor_accepted_currencies	=$vendor_accepted_currencies;
			$this->vendor_address_format		=$vendor_address_format;
			$this->vendor_date_format			=$vendor_date_format;
			$this->vendor_params				=$vendor_params;
			$this->img_uri						=$img_uri;
			$this->img_thumb_uri				=$img_thumb_uri;
			$this->userInfo						=$userInfo;
			
		}
	
	} 
	
	  	/**
	 * Class VendorCategory
	 *
	 * Class "VendorCategory" with attribute : vendor_category_id ...
	 * 
	 *
	 * @author     Mickael cabanas (cabanas.mickael|at|gmail.com)
	 * @copyright  Mickael cabanas
	 * @license    http://www.gnu.org/copyleft/gpl.html GNU/GPL
	 * @version    Release:
	 */
	class VendorCategory {
	
		public $vendor_category_id="";
		public $vendor_category_name="";
		public $vendor_category_desc="";		
		
		function __construct($vendor_category_id, $vendor_category_name,$vendor_category_desc){
		
			$this->vendor_category_id	=$vendor_category_id;
			$this->vendor_category_name	=$vendor_category_name;
			$this->vendor_category_desc	=$vendor_category_desc;		
		}
	}
	 /**
	 * Class Manufacturer
	 *
	 * Class "Manufacturer" with attribute :  manufacturer_id ...
	 * 
	 *
	 * @author     Mickael cabanas (cabanas.mickael|at|gmail.com)
	 * @copyright  Mickael cabanas
	 * @license    http://www.gnu.org/copyleft/gpl.html GNU/GPL
	 * @version    Release:
	 */
	class Manufacturer {
	
		public $manufacturer_id="";
		public $mf_name="";
		public $slug="";
		public $mf_email="";		
		public $mf_desc="";	
		public $mf_category_id="";
		public $mf_url="";
		public $hits="";
		public $published="";
		public $img_uri="";
		public $img_thumb_uri="";
		
		
		function __construct($manufacturer_id, $mf_name,$slug,$mf_email,$mf_desc,$mf_category_id,$mf_url,$hits,$published,$img_uri,$img_thumb_uri){
		
			$this->manufacturer_id		=$manufacturer_id;
			$this->mf_name				=$mf_name;
			$this->slug					=$slug;
			$this->mf_email				=$mf_email;		
			$this->mf_desc				=$mf_desc;	
			$this->mf_category_id		=$mf_category_id;	
			$this->mf_url				=$mf_url;	
			$this->hits					=$hits;	
			$this->published			=$published;	
			$this->img_uri				=$img_uri;	
			$this->img_thumb_uri		=$img_thumb_uri;	
			
		}
	}
	
		 /**
	 * Class ManufacturerCat
	 *
	 * Class "ManufacturerCat" with attribute :  mf_category_id ...
	 * 
	 *
	 * @author     Mickael cabanas (cabanas.mickael|at|gmail.com)
	 * @copyright  Mickael cabanas
	 * @license    http://www.gnu.org/copyleft/gpl.html GNU/GPL
	 * @version    Release:
	 */
	class ManufacturerCat {
	
		public $mf_category_id="";
		public $mf_category_name="";
		public $mf_category_desc="";
		public $published="";

		

		
		function __construct($mf_category_id, $mf_category_name,$mf_category_desc,$published){
		
			$this->mf_category_id=$mf_category_id;
			$this->mf_category_name=$mf_category_name;
			$this->mf_category_desc=$mf_category_desc;	
			$this->published=$published;				
			
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
	 * Class Session
	 *
	 * Class "Session" with attribute :  username ...
	 * 
	 *
	 * @author     Mickael cabanas (cabanas.mickael|at|gmail.com)
	 * @copyright  Mickael cabanas
	 * @license    http://www.gnu.org/copyleft/gpl.html GNU/GPL
	 * @version    Release:
	 */
	class Session {
	
		public $username="";
		public $time="";
		public $session_id="";		
		public $guest="";	
		public $userid="";
		public $usertype="";
		public $gid="";
		public $client_id="";
		public $data="";
		
		function __construct($username, $time,$session_id,$guest,$userid,$usertype,$gid,$client_id,$data){
		
			$this->username=$username;
			$this->time=$time;
			$this->session_id=$session_id;		
			$this->guest=$guest;	
			$this->userid=$userid;	
			$this->usertype=$usertype;	
			$this->gid=$gid;	
			$this->client_id=$client_id;	
			$this->data=$data;	
			
		}
	}
	
	 /**
	 * Class WaitingList
	 *
	 * Class "WaitingList" with attribute :  username ...
	 * 
	 *
	 * @author     Mickael cabanas (cabanas.mickael|at|gmail.com)
	 * @copyright  Mickael cabanas
	 * @license    http://www.gnu.org/copyleft/gpl.html GNU/GPL
	 * @version    Release:
	 */
	class WaitingList {
	
		public $waiting_list_id="";
		public $product_id="";
		public $user_id="";		
		public $notify_email="";	
		public $notified="";	
		public $notify_date="";
		
		function __construct($waiting_list_id, $product_id,$user_id,$notify_email,$notified,$notify_date){
		
			$this->waiting_list_id=$waiting_list_id;
			$this->product_id=$product_id;
			$this->user_id=$user_id;		
			$this->notify_email=$notify_email;	
			$this->notified=$notified;	
			$this->notify_date=$notify_date;	
		
		}
	}
	
	 /**
	 * Class Userfield 
	 *
	 * Class "Userfield" with attribute : virtuemart_userfield_id	 ...
	 * 
	 *
	 * @author     Mickael cabanas (cabanas.mickael|at|gmail.com)
	 * @copyright  Mickael cabanas
	 * @license    http://www.gnu.org/copyleft/gpl.html GNU/GPL
	 * @version    Release:
	 */
	class Userfield {
	
		public $virtuemart_userfield_id="";
		public $vendor_id="";
		public $name="";		
		public $title="";	
		public $description="";	
		public $type="";	
		public $size="";	
		public $required="";
		public $cols="";
		public $value="";
		public $default="";
		public $registration="";
		public $shipping="";
		public $account="";
		public $calculated="";
		public $sys="";
		public $params="";
		public $ordering="";
		public $shared="";
		public $published="";
		
		function __construct($virtuemart_userfield_id, $vendor_id,$name, $title, $description, $type, $size
							, $required, $cols, $value, $default, $registration, $shipping, $account, $calculated, $sys, $params, $ordering, $shared,$published){
		
			$this->virtuemart_userfield_id 	= $virtuemart_userfield_id;
			$this->vendor_id 				= $vendor_id;
			$this->name						= $name;
			$this->title					= $title;
			$this->description				= $description;
			$this->type						= $type;
			$this->size						= $size;		
			$this->required					= $required;	
			$this->cols						= $cols;	
			$this->value					= $value;	
			$this->default					= $default;	
			$this->registration				= $registration;	
			$this->shipping					= $shipping;	
			$this->account					= $account;	
			$this->calculated				= $calculated;	
			$this->sys						= $sys;	
			$this->params					= $params;	
			$this->ordering					= $ordering;	
			$this->shared					= $shared;	
			$this->published				= $published;		
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
	 * Class authOutput
	 *
	 * Class "authOutput" with attribute : code ...
	 * 
	 *
	 * @author     Mickael cabanas (cabanas.mickael|at|gmail.com)
	 * @copyright  Mickael cabanas
	 * @license    http://www.gnu.org/copyleft/gpl.html GNU/GPL
	 * @version    Release:
	 */
	class AuthOutput {
	
		public $type="";
		public $perms="";
		public $code="";		
		
		function __construct($type, $perms,$code){
		
			$this->type=$type;
			$this->perms=$perms;
			$this->code=$code;		
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
    * This function GetSessions return all sessions 
	* (expose as WS)
    * @param 
    * @return array of Users
	*/
	function GetSessions($params) {
	
		/* Authenticate*/
		$result = onAdminAuthenticate($params->loginInfo->login, $params->loginInfo->password,$params->loginInfo->isEncrypted);
		
		$vmConfig = getVMconfig();
		if ($vmConfig->get('soap_auth_user_otherget')==0){
			$result = "true";
		}
		//Auth OK
		if ($result == "true"){
			
			$db = JFactory::getDBO();	
				
			$query  = "SELECT * FROM #__session WHERE 1 ";
			
			$limite_start = $params->limite_start;
			$limite_end = $params->limite_end;
			if (empty($params->limite_start)){
				$limite_start = "0";
			}
			if (empty($params->limite_end)){
				$limite_end = "100";
			}
			
			if ($params->guest != ""){
				$query .= " AND guest = '$params->guest' " ;
			}
			if ($params->usertype != ""){
				$query .= " AND usertype = '$params->usertype' " ;
			}
			if ($params->gid != ""){
				$query .= " AND gid = '$params->gid' " ;//not in VM2 
			}
			if ($params->client_id != ""){
				$query .= " AND client_id = '$params->client_id' " ;
			}
			$query .= " ORDER BY time DESC  ";
			$query .= " LIMIT $limite_start,$limite_end ";
			
			$db->setQuery($query);
			
			$rows = $db->loadObjectList();
			
			foreach ($rows as $row){
				$data="";
				if ($params->with_data == "Y"){
					$data = $row->data;
				}
				$Session = new Session($row->username,$row->time,$row->session_id, $row->guest, 
							$row->userid,$row->usertype, 'Not in VM2', $row->client_id, $data  );
				$arraySession[]= $Session;
			
			}
			return $arraySession;
			
			
		}else if ($result == "false"){
			return new SoapFault("JoomlaServerAuthFault", "Autification KO for : ".$params->loginInfo->login);
		}else if ($result == "no_admin"){
			return new SoapFault("JoomlaServerAuthFault", "User is not a Super Administrator : ".$params->loginInfo->login);
		}else{
			return new SoapFault("JoomlaServerAuthFault", "User does not exist : ".$params->loginInfo->login);
		}
		

	}
	
	/**
    * This function GetWaitingList return waiting list 
	* (expose as WS)
    * @param 
    * @return array of Users
	*/
	function GetWaitingList($params) {
	
		/* Authenticate*/
		$result = onAdminAuthenticate($params->loginInfo->login, $params->loginInfo->password,$params->loginInfo->isEncrypted);
		
		$vmConfig = getVMconfig();
		if ($vmConfig->get('soap_auth_user_otherget')==0){
			$result = "true";
		}
		
		//Auth OK
		if ($result == "true"){
			
			$db = JFactory::getDBO();	
				
			$query  = "SELECT * FROM `#__virtuemart_waitingusers` WHERE 1 ";
			
			$limite_start = $params->limite_start;
			$limite_end = $params->limite_end;
			if (empty($params->limite_start)){
				$limite_start = "0";
			}
			if (empty($params->limite_end)){
				$limite_end = "100";
			}
			  			
			if ($params->waiting_list_id != ""){
				$query .= "AND virtuemart_waitinguser_id = $params->waiting_list_id";
			}
			if ($params->product_id != ""){
				$query .= " AND virtuemart_product_id = '$params->product_id' " ;
			}
			if ($params->user_id != ""){
				$query .= " AND virtuemart_user_id = '$params->user_id' " ;
			}
			if ($params->notify_email != ""){
				$query .= " AND notify_email = '$params->notify_email' " ;
			}
			if ($params->notified != ""){
				$query .= " AND notified = '$params->notified' " ;
			}
			if ($params->notify_date != ""){
				$query .= " AND notify_date > '$params->notify_date' " ;
			}
			
			$db->setQuery($query);
			
			$rows = $db->loadObjectList();
			
			foreach ($rows as $row){
				$WaitingList = new WaitingList($row->virtuemart_waitinguser_id,$row->virtuemart_product_id,$row->virtuemart_user_id, $row->notify_email, 
							$row->notified, $row->notify_date  );
				$arrayWaitingList[]= $WaitingList;
			}
			$errMsg=  $db->getErrorMsg();	
			if ($errMsg==null){
				return $arrayWaitingList;
			} else {
				return new SoapFault("GetWaitingListFault", "SQL Error \n ".$errMsg);
			}
			
		}else if ($result == "false"){
			return new SoapFault("JoomlaServerAuthFault", "Autification KO for : ".$params->loginInfo->login);
		}else if ($result == "no_admin"){
			return new SoapFault("JoomlaServerAuthFault", "User is not a Super Administrator : ".$params->loginInfo->login);
		}else{
			return new SoapFault("JoomlaServerAuthFault", "User does not exist : ".$params->loginInfo->login);
		}
		

	}
	
	
	
	/**
    * This function GetUsers return all users 
	* (NOT expose as WS)
    * @param 
    * @return array of Users
	*/
	function GetUsersGeneric($params) {
	
			$limite_start = !empty($params->limite_start) ? $params->limite_start : 0;
			$limite_end = !empty($params->limite_end) ? $params->limite_end : 50;
			

			$db = JFactory::getDBO();	
				
			$query   = "SELECT * FROM `#__users` JU ";
			$query  .= "JOIN `#__virtuemart_vmusers` VU on JU.id = VU.virtuemart_user_id ";
			$query  .= "JOIN `#__virtuemart_userinfos` UI on JU.id = UI.virtuemart_user_id ";
			$query  .= "LEFT JOIN `#__virtuemart_vmuser_shoppergroups` SG on VU.virtuemart_user_id = SG.virtuemart_user_id WHERE 1 ";
			
			if (!empty($params->searchtype)){
				if ($params->searchtype=="email"){
					$query  .= "AND JU.email  like '%$params->email%' ";
				} else if ($params->searchtype=="user_id"){ //request for user_ids
					$query  .= "AND JU.id = '".$params->user_ids->user_id."' ";
				} else if ($params->searchtype=="username"){ //request for USERNAME
					$query  .= "AND JU.username like '%$params->username%' ";
				} else {
					$query  .= "AND (JU.username like '%$params->username%' OR JU.id = $params->user_id OR JU.username like '%$params->username%' ) )";
				}
			}
			$query  .= "LIMIT $limite_start,$limite_end ";
						
			$db->setQuery($query);
			
			$rows = $db->loadObjectList();
			
			$errMsg=  $db->getErrorMsg();
		
			//return new SoapFault("JoomlaServerAuthFault", "err : ".$query."\n".$params->user_ids->user_id);
			
			foreach ($rows as $row){
			
				$User = new User($row->virtuemart_user_id,
									$row->email,
									$row->username,
									"*******",
									$row->virtuemart_userinfo_id,
									$row->address_type,
									$row->address_type_name,
									$row->name,
									$row->company,
									$row->title,
									$row->last_name,
									$row->first_name,
									$row->middle_name,
									$row->phone_1,
									$row->phone_2,
									$row->fax,
									$row->address_1,
									$row->address_2,
									$row->city,
									$row->virtuemart_state_id,
									$row->virtuemart_country_id,
									$row->zip ,
									$row->extra_field_1,
									$row->extra_field_2,
									$row->extra_field_3,
									$row->extra_field_4,
									$row->extra_field_5,
									$row->created_on,
									$row->modified_on,
									$row->user_is_vendor,
									$row->customer_number,
									$row->perms,
									$row->virtuemart_paymentmethod_id,
									$row->virtuemart_shippingcarrier_id,
									$row->agreed,
									$row->virtuemart_shoppergroup_id,
									GetExtraUserFieldsData($row->virtuemart_user_id)
									
									);
				$arrayUser[]= $User;
			}
			return $arrayUser;
	
	
	}
	
	
	
  	/**
    * This function GetUsers return all users 
	* (expose as WS)
    * @param 
    * @return array of Users
	*/
	function GetUsers($params) {
	
		/* Authenticate*/
		$result = onAdminAuthenticate($params->loginInfo->login, $params->loginInfo->password,$params->loginInfo->isEncrypted);
		
		$vmConfig = getVMconfig();
		if ($vmConfig->get('soap_auth_getuser')==0){
			$result = "true";
		}
		
		//Auth OK
		if ($result == "true"){
		
			setToken();
			
			//var_dump($prepareUserFields[1]['fields']);die;
			
			/*if (!class_exists( 'VirtueMartModelProduct' )) require (JPATH_VM_ADMINISTRATOR.DS.'models'.DS.'user.php');
			$VirtueMartModelUser = new VirtueMartModelUser;
			
			$users = $VirtueMartModelUser->getUserList();*/ //NOT ENOUGHT DATA -> I make my query
			
		
			return GetUsersGeneric($params);
			
			
		}else if ($result == "false"){
			return new SoapFault("JoomlaServerAuthFault", "Autification KO for : ".$params->loginInfo->login);
		}else if ($result == "no_admin"){
			return new SoapFault("JoomlaServerAuthFault", "User is not a Super Administrator : ".$params->loginInfo->login);
		}else{
			return new SoapFault("JoomlaServerAuthFault", "User does not exist : ".$params->loginInfo->login);
		}
		

	}
		/**
    * This function GetExtraUserFields
	* (NOT expose as WS)
    * @param 
    * @return array of Users
	*/
	function GetExtraUserFieldsData($user_id, $addrType = 'BT' ) {
	
		setToken();
		
		$db = JFactory::getDBO();	
		
		$extraFieldsNames = GetExtraUserFields();
		$query   = "SELECT ";
		$query  .= implode(",",$extraFieldsNames);
		$query  .= " FROM `#__virtuemart_userinfos` ui ";
		$query   .= "WHERE virtuemart_user_id = '$user_id' AND address_type = '$addrType' ";
		$db->setQuery($query);
		
		$rows = $db->loadObjectList();
		
		$str = "";
		foreach ($rows as $row){
			bindArray($row,$data);//only 1row
		}
		$extraFieldsOnlyStr = array_implode('=','|',$data);
		//var_dump($extraFieldsOnlyStr);die;
		return $extraFieldsOnlyStr;
		
	
	}
	
	/**
    * This function GetExtraUserFields
	* (NOT expose as WS)
    * @param 
    * @return array of Users
	**/
	function GetExtraUserFields() {
	
		$db = JFactory::getDBO();	
		//29 first are vm core fields
		$query   = "SELECT name FROM `#__virtuemart_userfields` uf WHERE virtuemart_userfield_id > 29 ";
			
		$db->setQuery($query);
		
		$rows = $db->loadObjectList();
		
		$names;
		foreach ($rows as $row){
			$names[] = $row->name;
		}
		return $names;
	
	}
  	
  	

	/**
    * This function to research an user (By email or by username)
	* (expose as WS)
    * @param 
    * @return result
	*/
	function GetUserFromEmailOrUsername($params) {
			
		/* Authenticate*/
		$result = onAdminAuthenticate($params->loginInfo->login, $params->loginInfo->password,$params->loginInfo->isEncrypted);
		
		$vmConfig = getVMconfig();
		if ($vmConfig->get('soap_auth_getuser')==0){
			$result = "true";
		}
		//Auth OK
		if ($result == "true"){
			
			//problem by id
			return GetUsersGeneric($params);
			

		}else if ($result == "false"){
			return new SoapFault("JoomlaServerAuthFault", "Autification KO for : ".$params->loginInfo->login);
		}else if ($result == "no_admin"){
			return new SoapFault("JoomlaServerAuthFault", "User is not a Super Administrator : ".$params->loginInfo->login);
		}else{
			return new SoapFault("JoomlaServerAuthFault", "User does not exist : ".$params->loginInfo->login);
		}
	}	
	
	
	
	/**
    * This function to Get User fields
	* (expose as WS)
    * @param 
    * @return result
	*/
	function GetUserfields($params) {
	
		/* Authenticate*/
		$result = onAdminAuthenticate($params->loginInfo->login, $params->loginInfo->password,$params->loginInfo->isEncrypted);
		
		$vmConfig = getVMconfig();
		if ($vmConfig->get('soap_auth_getuser')==0){
			$result = "true";
		}
		//Auth OK
		if ($result == "true"){
			
			$db = JFactory::getDBO();	
				
			
			$query   = "SELECT * FROM `#__virtuemart_userfields` uf WHERE 1 ";
			
			
			if (!empty($params->id)){
				$query .= " AND uf.virtuemart_userfield_id = '$params->id'";
			}
			
			
			$query .= " LIMIT 0,100 ";
			
						
			$db->setQuery($query);
			
			$rows = $db->loadObjectList();
			
			foreach ($rows as $row){
			
				$Userfield = new Userfield($row->virtuemart_userfield_id,
									$row->virtuemart_vendor_id,
									$row->name,
									$row->title,
									$row->description,
									$row->type,
									$row->size,
									$row->required,
									$row->cols,
									$row->value,
									$row->default,
									$row->registration,
									$row->shipping,
									$row->account,
									$row->calculated,
									$row->sys,
									$row->params,
									$row->ordering,
									$row->shared,
									$row->published
									
									
									);
				$arrayUserfield[]= $Userfield;
				
			
			}
			
			
			$errMsg=  $db->getErrorMsg();	
			if ($errMsg==null){
				return $arrayUserfield;
			}else {
				return new SoapFault("GetAdditionalUserInfoFault", "Error in GetUserfields ",$errMsg);
			}
			

		}else if ($result == "false"){
			return new SoapFault("JoomlaServerAuthFault", "Autification KO for : ".$params->loginInfo->login);
		}else if ($result == "no_admin"){
			return new SoapFault("JoomlaServerAuthFault", "User is not a Super Administrator : ".$params->loginInfo->login);
		}else{
			return new SoapFault("JoomlaServerAuthFault", "User does not exist : ".$params->loginInfo->login);
		}
	}	
	
	/**
    * This function to Get Additional User Info
	* (expose as WS)
    * @param 
    * @return result
	*/
	function GetAdditionalUserInfo($params) {
	
		/* Authenticate*/
		$result = onAdminAuthenticate($params->loginInfo->login, $params->loginInfo->password,$params->loginInfo->isEncrypted);
		
		$vmConfig = getVMconfig();
		if ($vmConfig->get('soap_auth_getuser')==0){
			$result = "true";
		}
		//Auth OK
		if ($result == "true"){
		
			$db = JFactory::getDBO();	
				
			
			$query   = "SELECT * FROM `#__virtuemart_userinfos` ui ";
			$query  .= "JOIN #__users u ON ui.virtuemart_user_id=u.id WHERE 1 ";
			
			if (!empty($params->user_id)){
				$query .= " AND ui.virtuemart_user_id = '$params->user_id'";
			}
			if (!empty($params->login)){
				$query .= " AND u.username  = '$params->login'";
			}
			if (!empty($params->email)){
				$query .= " AND u.email = '$params->email'";
			}
			
			$query .= " LIMIT 0,100 ";
			
						
			$db->setQuery($query);
			
			$rows = $db->loadObjectList();
			
			foreach ($rows as $row){
			
				$User = new User($row->virtuemart_user_id,
									$row->email,
									$row->username,
									"*******",
									$row->virtuemart_userinfo_id,
									$row->address_type,
									$row->address_type_name,
									$row->name,
									$row->company,
									$row->title,
									$row->last_name,
									$row->first_name,
									$row->middle_name,
									$row->phone_1,
									$row->phone_2,
									$row->fax,
									$row->address_1,
									$row->address_2,
									$row->city,
									$row->virtuemart_state_id,
									$row->virtuemart_country_id,
									$row->zip ,
									$row->extra_field_1,
									$row->extra_field_2,
									$row->extra_field_3,
									$row->extra_field_4,
									$row->extra_field_5,
									$row->created_on,
									$row->modified_on,
									$row->user_is_vendor,
									$row->customer_number,
									$row->perms,
									$row->virtuemart_paymentmethod_id,
									$row->virtuemart_shippingcarrier_id,
									$row->agreed,
									$row->virtuemart_shoppergroup_id		
									
									);
				$arrayUser[]= $User;
				
			
			}
			
			
			$errMsg=  $db->getErrorMsg();	
			if ($errMsg==null){
				return $arrayUser;
			}else {
				return new SoapFault("GetAdditionalUserInfoFault", "Error in GetAdditionalUserInfo ",$errMsg);
			}
			

		}else if ($result == "false"){
			return new SoapFault("JoomlaServerAuthFault", "Autification KO for : ".$params->loginInfo->login);
		}else if ($result == "no_admin"){
			return new SoapFault("JoomlaServerAuthFault", "User is not a Super Administrator : ".$params->loginInfo->login);
		}else{
			return new SoapFault("JoomlaServerAuthFault", "User does not exist : ".$params->loginInfo->login);
		}
	}	
	
	

	/**
    * This function  GetUserInfo From OrderID
	* (expose as WS)
    * @param 
    * @return result
	*/
	function GetUserInfoFromOrderID($params) {
	
		/* Authenticate*/
		$result = onAdminAuthenticate($params->loginInfo->login, $params->loginInfo->password,$params->loginInfo->isEncrypted);
		
		$vmConfig = getVMconfig();
		if ($vmConfig->get('soap_auth_getuser')==0){
			$result = "true";
		}
		//Auth OK
		if ($result == "true"){
		
			return getNotInFreeSoap();


		}else if ($result == "false"){
			return new SoapFault("JoomlaServerAuthFault", "Autification KO for : ".$params->loginInfo->login);
		}else if ($result == "no_admin"){
			return new SoapFault("JoomlaServerAuthFault", "User is not a Super Administrator : ".$params->loginInfo->login);
		}else{
			return new SoapFault("JoomlaServerAuthFault", "User does not exist : ".$params->loginInfo->login);
		}
	}	


	
  	/**
    * This function GetAllCountryCode return all country code 
	* (expose as WS)
    * @param 
    * @return array of Country
	*/
	function GetAllCountryCode($params2) {
	
		/* Authenticate*/
		$result = onAdminAuthenticate($params2->login, $params2->password);
		
		$vmConfig = getVMconfig();
		if ($vmConfig->get('soap_auth_user_otherget')==0){
			$result = "true";
		}
		
		//Auth OK
		if ($result == "true"){
								
			if (!class_exists( 'VirtueMartModelCountry' )) require (JPATH_VM_ADMINISTRATOR.DS.'models'.DS.'country.php');
			$modelCountry = new VirtueMartModelCountry;
			
			$rows = $modelCountry->getCountries(false,true);
			
			foreach ($rows as $row){
				$Country = new Country($row->virtuemart_country_id,
										$row->virtuemart_worldzone_id,
										$row->country_name,
										$row->country_3_code,
										$row->country_2_code,
										$row->published);
				$arrayCountry[]= $Country;
			}
			return $arrayCountry;
			

		}else if ($result == "false"){
			return new SoapFault("JoomlaServerAuthFault", "Autification KO for : ".$params2->login);
		}else if ($result == "no_admin"){
			return new SoapFault("JoomlaServerAuthFault", "User is not a Super Administrator : ".$params2->login);
		}else{
			return new SoapFault("JoomlaServerAuthFault", "User does not exist : ".$params2->login);
		}
	}
	
	/**
    * This function get Get AuthGroup
	* (expose as WS)
    * @param string
    * @return result
    */
	function GetAuthGroup($params) {
		
		/* Authenticate*/
		$result = onAdminAuthenticate($params->login, $params->password);
		
		$vmConfig = getVMconfig();
		if ($vmConfig->get('soap_auth_user_otherget')==0){
			$result = "true";
		}
		
		//Auth OK
		if ($result == "true"){
		
			if (isSupEqualVmVersion('2.0.26d')){
				if (!class_exists( 'Permissions' )) require (JPATH_VM_ADMINISTRATOR.DS.'helpers'.DS.'permissions.php');
				$permissions = new Permissions;
				$userGroups = $permissions->getUsergroups();
			} else {
				if (!class_exists( 'VirtueMartModelUsergroups' )) require (JPATH_VM_ADMINISTRATOR.DS.'models'.DS.'usergroups.php');
				$modelUsergroups = new VirtueMartModelUsergroups;
				$userGroups = $modelUsergroups->getUsergroups(false,true);
			}
			
			foreach ($userGroups as $row){
				$AuthGroup = new AuthGroup($row->virtuemart_permgroup_id,
											$row->virtuemart_vendor_id,
											$row->group_name,
											$row->group_level,
											$row->ordering,
											$row->shared,
											$row->published
											);
				$arrayAuthGroup[]=$AuthGroup;
			}
			return $arrayAuthGroup;
			
				
		}else if ($result == "false"){
			return new SoapFault("JoomlaServerAuthFault", "Autification KO for : ".$params->login);
		}else if ($result == "no_admin"){
			return new SoapFault("JoomlaServerAuthFault", "User is not a Super Administrator : ".$params->login);
		}else{
			return new SoapFault("JoomlaServerAuthFault", "User does not exist : ".$params->login);
		}
	}
	

	
	
		/**
    * This function GetAllStates
	* (expose as WS)
    * @param string
    * @return result
    */
	function GetAllStates($params) {
			
		/* Authenticate*/
		$result = onAdminAuthenticate($params->loginInfo->login, $params->loginInfo->password,$params->loginInfo->isEncrypted);
		
		$vmConfig = getVMconfig();
		if ($vmConfig->get('soap_auth_user_otherget')==0){
			$result = "true";
		}
		
		
		//Auth OK
		if ($result == "true"){
			
			$db = JFactory::getDBO();	
				
			if (!empty($params->country_id)){
				$query  = "SELECT * FROM `#__virtuemart_states` WHERE virtuemart_country_id = '".$params->country_id."'";
			} else {
				$query  = "SELECT * FROM `#__virtuemart_states` WHERE 1 ";
			}
						
			$db->setQuery($query);
			
			$rows = $db->loadObjectList();
			
			foreach ($rows as $row){
				$State = new State($row->virtuemart_state_id,
									$row->virtuemart_vendor_id,
									$row->virtuemart_country_id,
									$row->virtuemart_worldzone_id,
									$row->state_name,
									$row->state_3_code,
									$row->state_2_code,
									$row->published
									);
				$arrayState[]=$State;
			}
			return $arrayState;
			
				
		}else if ($result == "false"){
			return new SoapFault("JoomlaServerAuthFault", "Autification KO for : ".$params->loginInfo->login);
		}else if ($result == "no_admin"){
			return new SoapFault("JoomlaServerAuthFault", "User is not a Super Administrator : ".$params->loginInfo->login);
		}else{
			return new SoapFault("JoomlaServerAuthFault", "User does not exist : ".$params->loginInfo->login);
		}
	}
	
	
	
	/**
    * This function get shopperGroup
	* (expose as WS)
    * @param string
    * @return result
    */
	function GetShopperGroup($params) {
			
		/* Authenticate*/
		$result = onAdminAuthenticate($params->login, $params->password);
		
		$vmConfig = getVMconfig();
		if ($vmConfig->get('soap_auth_user_otherget')==0){
			$result = "true";
		}
		
		
		//Auth OK
		if ($result == "true"){
		
			/*$db = JFactory::getDBO();	
			$query  = "SELECT * FROM `#__virtuemart_shoppergroups` WHERE 1 ";
			$db->setQuery($query);
			$rows = $db->loadObjectList();*/
			
			if (!class_exists( 'VirtueMartModelShopperGroup' )) require (JPATH_VM_ADMINISTRATOR.DS.'models'.DS.'shoppergroup.php');
			$modelShopperGroup = new VirtueMartModelShopperGroup;
			
			$rows = $modelShopperGroup->getShopperGroups();
			
			foreach ($rows as $row){
			
				$price_display = unserialize($row->price_display);
				
				$ShopperGroup = new ShopperGroup($row->virtuemart_shoppergroup_id,
													$row->virtuemart_vendor_id,
													$row->shopper_group_name,
													$row->shopper_group_desc,
													$row->custom_price_display,
													/*$row->price_display*/$price_display,
													$row->default,
													$row->ordering,
													$row->shared,
													$row->published);
				$arrayShopperGroup[]=$ShopperGroup;
			}
			return $arrayShopperGroup;
					
				
		}else if ($result == "false"){
			return new SoapFault("JoomlaServerAuthFault", "Autification KO for : ".$params->login);
		}else if ($result == "no_admin"){
			return new SoapFault("JoomlaServerAuthFault", "User is not a Super Administrator : ".$params->login);
		}else{
			return new SoapFault("JoomlaServerAuthFault", "User does not exist : ".$params->login);
		}
	}
	
	
	
	/**
    * This function  Get All Vendor
	* (expose as WS)
    * @param string
    * @return result
    */
	function GetShopInfo($vendor_id) {
	
		$db = JFactory::getDBO();	
		$query   = "SELECT * FROM #__virtuemart_vendors ven ";
		$query  .= "JOIN #__virtuemart_vendors_".VMLANG." lang ";
		$query  .= "on ven.virtuemart_vendor_id =lang.virtuemart_vendor_id ";
		$query  .= "WHERE ven.virtuemart_vendor_id= $vendor_id ";
		$db->setQuery($query);
		$rows = $db->loadObjectList();
		
		foreach ($rows as $row){
			$vendor = new Vendor($row->virtuemart_vendor_id,
										$row->vendor_name,
										$row->vendor_phone,
										$row->vendor_store_name ,
										$row->vendor_store_desc,
										$row->vendor_currency,
										$row->vendor_image_path,
										$row->vendor_terms_of_service,
										$row->vendor_url,
										$row->slug,
										"",
										$row->vendor_accepted_currencies,
										"",
										"",
										$row->vendor_params ,
										"",
										"",
										""
										);
		}
		return $vendor;
	
	}
	/**
    * This function  Get All Vendor
	* (expose as WS)
    * @param string
    * @return result
    */
	function GetAllVendor($params) {
			
		/* Authenticate*/
		$result = onAdminAuthenticate($params->loginInfo->login, $params->loginInfo->password,$params->loginInfo->isEncrypted);
		
		$vmConfig = getVMconfig();
		if ($vmConfig->get('soap_auth_user_otherget')==0){
			$result = "true";
		}
		
		
		//Auth OK
		if ($result == "true"){
						
			if (!class_exists( 'VirtueMartModelVendor' )) require (JPATH_VM_ADMINISTRATOR.DS.'models'.DS.'vendor.php');
			$modelVendor = new VirtueMartModelVendor;
			
			$rows = $modelVendor->getVendors();
			//var_dump($rows);die;
			foreach ($rows as $row){
			
				if ($params->include_user_info=="Y" || $params->include_user_info=="1"){
				
					// get ID of user who is vendor
					$db = JFactory::getDBO();	
					$query   = "SELECT virtuemart_user_id FROM #__virtuemart_vmusers ";
					$query  .= "WHERE user_is_vendor = 1 ";
					$db->setQuery($query);
					$rowsUID = $db->loadObjectList();
					
					foreach ($rowsUID as $rowuid){
						$uid = $rowuid->virtuemart_user_id;
					}
					
					$params->searchtype="user_id";
					$params->user_ids->user_id=$uid;
					$userArr = GetUsersGeneric($params);
					$userInfo = $userArr[0];
					
				}
				
				$img=GetDefaultImages($params,$row->virtuemart_vendor_id,false);
				$imgThumb=GetDefaultImages($params,$row->virtuemart_vendor_id,true);
				
				$Vendor =  GetShopInfo($row->virtuemart_vendor_id);
				$Vendor->img_uri = $img;
				$Vendor->img_thumb_uri = $imgThumb;
				$Vendor->userInfo = $userInfo;
				//$vendor_cur = $modelVendor->getVendorCurrency($row->virtuemart_vendor_id);
				//$vendor_store_name = $modelVendor->getVendorName($row->virtuemart_vendor_id);
				//$vendor_mail = $modelVendor->getVendorEmail($row->virtuemart_vendor_id);
				
				
				
				/*$Vendor = new Vendor($row->virtuemart_vendor_id,
										$row->vendor_name,
										$row->vendor_phone,
										$vendor_store_name,
										$row->vendor_store_desc,
										$row->vendor_currency,
										$row->vendor_image_path,
										$row->vendor_terms_of_service,
										$row->vendor_url,
										$row->vendor_min_pov,
										$row->vendor_freeshipping,
										$row->vendor_accepted_currencies,
										$row->vendor_address_format,
										$row->vendor_date_format,
										$row->config,
										$img,
										$userInfo
										);*/
				$arrayVendor[]=$Vendor;
			}
			return $arrayVendor;
			
				
		}else if ($result == "false"){
			return new SoapFault("JoomlaServerAuthFault", "Autification KO for : ".$params->login);
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
	function GetDefaultImages($params,$vendor_id,$thumb = false) {
	
		$params->vendor_id = $vendor_id;
		$medias = GetMediaVendor($params);
		
		$img_cat = "";
		if (is_array($medias)){
			foreach ($medias as $media){
				$img_cat = $media->file_url;
				if ($thumb){
					$img_cat = $media->file_url_thumb; 
				}
				$media->ordering;
				return $img_cat;//return first one
			}
		}
		return $img_cat;
	
	}
	
	/**
    * This function get default img cat
	* insternal use
    * @param Object
    * @return array of Categories
   */
	function GetDefaultImagesManufacturer($params,$manufacturer_id,$thumb = false) {
	
		$params->manufacturer_id = $manufacturer_id;
		$medias = GetMediaManufacturer($params);
		//var_dump($medias);die;
		$img_cat = "";
		if (is_array($medias)){
			foreach ($medias as $media){
				$img_cat = $media->file_url;
				if ($thumb){
					$img_cat = $media->file_url_thumb; 
				}
				$media->ordering;
				return $img_cat;//return first one
			}
		}
		return $img_cat;
	
	}
	
	/**
    * This function get All medias for product
	* (expose as WS)
    * @param string The id of the product
    * @return array of Media
   */
	function GetMediaVendor($params) {
	
		/* Authenticate*/
		$result = onAdminAuthenticate($params->loginInfo->login, $params->loginInfo->password,$params->loginInfo->isEncrypted);
		
		$vmConfig = getVMconfig();
		if ($vmConfig->get('soap_auth_prod_otherget')==0){
			$result = "true";
		}
		
		//Auth OK
		if ($result == "true"){
		
			/*if(!class_exists('VirtueMartModelMedia')) require(JPATH_VM_ADMINISTRATOR.DS.'models'.DS.'media.php');
			$mediaModel = new VirtueMartModelMedia();
			
			$files = $mediaModel->getFiles();//no params :default is vendor media*/
			
			$db = JFactory::getDBO();	
				
			$idVendor = !empty($params->vendor_id) ? $params->vendor_id : 1;
			$query   = "SELECT * FROM `#__virtuemart_vendor_medias` vend ";
			$query  .= "JOIN `#__virtuemart_medias` med ON med.virtuemart_media_id=vend.virtuemart_media_id  ";
			$query  .= "WHERE vend.virtuemart_vendor_id = $idVendor  ";
			
			$db->setQuery($query);
			
			$files = $db->loadObjectList();
			
			//var_dump($query);die;
			
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
											$file->published
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
    * This function get All medias for product
	* (expose as WS)
    * @param string The id of the product
    * @return array of Media
   */
	function GetMediaManufacturer($params) {
	
		/* Authenticate*/
		$result = onAdminAuthenticate($params->loginInfo->login, $params->loginInfo->password,$params->loginInfo->isEncrypted);
		
		$vmConfig = getVMconfig();
		if ($vmConfig->get('soap_auth_prod_otherget')==0){
			$result = "true";
		}
		
		//Auth OK
		if ($result == "true"){
		
			/*if(!class_exists('VirtueMartModelMedia')) require(JPATH_VM_ADMINISTRATOR.DS.'models'.DS.'media.php');
			$mediaModel = new VirtueMartModelMedia();
			
			$files = $mediaModel->getFiles();//no params :default is vendor media*/
			
			$db = JFactory::getDBO();	
				
			$idManufacturer = !empty($params->manufacturer_id) ? $params->manufacturer_id : 1;
			$query   = "SELECT * FROM `#__virtuemart_manufacturer_medias` vend ";
			$query  .= "JOIN `#__virtuemart_medias` med ON med.virtuemart_media_id=vend.virtuemart_media_id  ";
			$query  .= "WHERE vend.virtuemart_manufacturer_id  = $idManufacturer  ";
			
			$db->setQuery($query);
			
			$files = $db->loadObjectList();
			
			//var_dump($query);die;
			
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
											$file->published
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
    * This function  Get All Vendor Category
	* (expose as WS)
    * @param string
    * @return result
    */
	function GetAllVendorCategory($params) {
		
		/* Authenticate*/
		$result = onAdminAuthenticate($params->login, $params->password);
		
		$vmConfig = getVMconfig();
		if ($vmConfig->get('soap_auth_user_otherget')==0){
			$result = "true";
		}
		
		//Auth OK
		if ($result == "true"){
		
			return new SoapFault("JoomlaServerAuthFault", "NOT IN VM2");
			$db = new ps_DB;

			$list  = "SELECT * FROM #__{vm}_vendor_category WHERE 1";
			$db->query($list);
			
			while ($db->next_record()) {
			
				$VendorCategory = new VendorCategory($db->f("vendor_category_id"),$db->f("vendor_category_name"),$db->f("vendor_category_desc"));
				$arrayVendorCategory[]=$VendorCategory;
			
			}
			return $arrayVendorCategory;
				
		}else if ($result == "false"){
			return new SoapFault("JoomlaServerAuthFault", "Autification KO for : ".$params->login);
		}else if ($result == "no_admin"){
			return new SoapFault("JoomlaServerAuthFault", "User is not a Super Administrator : ".$params->login);
		}else{
			return new SoapFault("JoomlaServerAuthFault", "User does not exist : ".$params->login);
		}
	}
	
	
	/**
    * This function   Get All Manufacturer
	* (expose as WS)
    * @param string
    * @return result
    */
	function GetAllManufacturer($params) {
			
		/* Authenticate*/
		$result = onAdminAuthenticate($params->login, $params->password);
		
		$vmConfig = getVMconfig();
		if ($vmConfig->get('soap_auth_user_otherget')==0){
			$result = "true";
		}
		
		
		//Auth OK
		if ($result == "true"){
		
			if (!class_exists( 'VirtueMartModelManufacturer' )) require (JPATH_VM_ADMINISTRATOR.DS.'models'.DS.'manufacturer.php');
			$modelManufacturer = new VirtueMartModelManufacturer;
			
			$rows = $modelManufacturer->getManufacturers(false,true);
						
			
			foreach ($rows as $row){
				//set for getMediaManufac
				$params->loginInfo->login=$params->login;
				$params->loginInfo->password=$params->password;
				
				$img= GetDefaultImagesManufacturer($params,$row->virtuemart_manufacturer_id,false);
				$imgThumb = GetDefaultImagesManufacturer($params,$row->virtuemart_manufacturer_id,true); 
				$Manufacturer = new Manufacturer($row->virtuemart_manufacturer_id,
													$row->mf_name,
													$row->slug,
													$row->mf_email,
													$row->mf_desc,
													$row->virtuemart_manufacturercategories_id ,
													$row->mf_url,
													$row->hits,
													$row->published,
													$img,
													$imgThumb
													);
				$arrayManufacturer[]=$Manufacturer;
			}
			return $arrayManufacturer;
		
		}else if ($result == "false"){
			return new SoapFault("JoomlaServerAuthFault", "Autification KO for : ".$params->login);
		}else if ($result == "no_admin"){
			return new SoapFault("JoomlaServerAuthFault", "User is not a Super Administrator : ".$params->login);
		}else{
			return new SoapFault("JoomlaServerAuthFault", "User does not exist : ".$params->login);
		}
	}
	
	
	/**
    * This function   Get All Manufacturer cat
	* (expose as WS)
    * @param string
    * @return result
    */
	function GetAllManufacturerCat($params) {
				
		/* Authenticate*/
		$result = onAdminAuthenticate($params->login, $params->password);
		
		$vmConfig = getVMconfig();
		if ($vmConfig->get('soap_auth_user_otherget')==0){
			$result = "true";
		}
			
		
		//Auth OK
		if ($result == "true"){
			
			
			if (!class_exists( 'VirtuemartModelManufacturercategories' )) require (JPATH_VM_ADMINISTRATOR.DS.'models'.DS.'manufacturercategories.php');
			$modelManufacturercategories = new VirtuemartModelManufacturercategories;
			
			$rows = $modelManufacturercategories->getManufacturerCategories();
				
			/*query  = "SELECT * FROM `#__virtuemart_manufacturercategories` WHERE 1 ";	
			$db->setQuery($query);
			$rows = $db->loadObjectList();*/
			
			foreach ($rows as $row){
				$ManufacturerCat = new ManufacturerCat($row->virtuemart_manufacturercategories_id,
														$row->mf_category_name,
														$row->mf_category_desc,
														$row->published);
				$arrayManufacturerCat[]=$ManufacturerCat;
			}
			return $arrayManufacturerCat;
			
				
		}else if ($result == "false"){
			return new SoapFault("JoomlaServerAuthFault", "Autification KO for : ".$params->login);
		}else if ($result == "no_admin"){
			return new SoapFault("JoomlaServerAuthFault", "User is not a Super Administrator : ".$params->login);
		}else{
			return new SoapFault("JoomlaServerAuthFault", "User does not exist : ".$params->login);
		}
	}
	
	

	/**
	* internal use 
	* link media to product
	**/
	function linkMediaManufacturer($media_id,$prod_id) {
	
		$type = "manufacturer";
		if (!isMediaExist($media_id)){
			return false;
		}
		if (isMediaLinked($media_id,$prod_id,$type)){
			return false;
		}
		//now all is ready to link
		
		$linked = linkMedia($media_id,$prod_id,$type);
		return $linked;
	
	}
	
	/**
	* internal use 
	* link media to product
	**/
	function linkMediaVendor($media_id,$prod_id) {
	
		$type = "vendor";
		if (!isMediaExist($media_id)){
			return false;
		}
		if (isMediaLinked($media_id,$prod_id,$type)){
			return false;
		}
		//now all is ready to link
		
		$linked = linkMedia($media_id,$prod_id,$type);
		return $linked;
	
	}
	
	/**
	* internal use 
	**/
	function setDefaultMediaData(&$params,$img,$imgthumb,$desc,$type) {
	
		$tab =  explode('.',$img);
		$ext = $tab[1];
		$mimetype = extentionToMimeType($ext);
		
		$params->media->virtuemart_vendor_id = 1;
		$params->media->file_title = 'image';
		$params->media->file_description = $desc;
		$params->media->file_meta;
		$params->media->file_mimetype = $mimetype;
		$params->media->file_type = $type;
		$params->media->file_url = $img;
		$params->media->file_url_thumb = $imgthumb;
		$params->media->file_is_product_image = "1";
		$params->media->file_is_downloadable ="0";
		$params->media->file_is_forSale ="0";
		$params->media->file_params;
		$params->media->ordering;
		$params->media->shared = 1 ;
		$params->media->published = 1;
		
		$params->filePath = $img;
		$params->fileThumbPath = $imgthumb;
			
		
	}	
		/**
    * This function get Get Available Images on server (dir components/com_virtuemart/shop_image/product)
	* (expose as WS)
    * @param string
    * @return array of products
   */
	function GetAvailableVendorImages($params) {
	
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
			
			$media_category_path = $vmConfig->get('assets_general_path');
			
			$media_category_path .= 'images/vendors/';
			
			$uri = JURI::base();
			$uri = str_replace('administrator/components/com_virtuemart/services/free/', "", $uri);
			
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
    * This function Get Versions
	* (expose as WS)
    * @param string
    * @return 
   */
	function GetVersions($params) {
	
		if (!class_exists( 'vmVersion' )) require(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_virtuemart'.DS.'version.php');
		//$VMVersion = vmVersion::RELEASE;
		
		$result = "true"; //allways allow to make test
		//Auth OK
		if ($result == "true"){
		
			$_VERSION = new JVersion();
			$VMVERSION = new vmVersion();
			//global $database;
			$db = JFactory::getDBO();
			
			$version['SOA_For_Virtuemart_Version'] = getSOAVersion();
			$version['Joomla_Version'] = $_VERSION->getShortVersion();
			$version['Virtuemart_Version'] = vmVersion::$RELEASE;
			$version['Database_Version'] = $db->getVersion();
			$version['Author'] = 'Mickaël Cabanas';
			$version['PHP_Version'] = phpversion();
			$version['URL'] = "http://www.virtuemart-datamanager.com";
			$version['lang'] = VMLANG;
					
			return $version;

			
		}else if ($result == "false"){
			return new SoapFault("JoomlaServerAuthFault", "Autification KO for : ".$params->login);
		}else if ($result == "no_admin"){
			return new SoapFault("JoomlaServerAuthFault", "User is not a Super Administrator : ".$params->login);
		}else{
			return new SoapFault("JoomlaServerAuthFault", "User does not exist : ".$params->login);
		}		
	}
	
	
	/**
    *  function Authentification
	* (expose as WS)
    * @param login/pass
    * @return order details
   */
	function Authentification($params) {
		
		$result = onAdminAuthenticate($params->login, $params->password);
		
		if ($result == "true"){
			$token  = JUtility::getToken();
			jimport('joomla.user.helper');
			
			
			// Get a database object
			$db		= JFactory::getDbo();
			$query	= $db->getQuery(true);

			$query->select('id, password');
			$query->from('#__users');
			$query->where('username=' . $db->Quote($params->login));

			$db->setQuery($query);
			$result = $db->loadObject();
			
			$perms = "noperms";
			$usertype = "user";
			if ($result) {
				
				$user = JUser::getInstance($result->id); // Bring this in line with the rest of the system
				$autorGroups =$user->getAuthorisedGroups();
				$perms = getPermsFormUID($result->id);
				//var_dump($autorGroups);die;
				if ($autorGroups['1'] == '8' ){ // /  8 	is 	Super Users //to ameliorate in future 
					$usertype = "Super Administrator";
				} else if (  ($autorGroups['2'] == '7') ){//7=admin
					$usertype = "Administrator";
				}else if (  ($autorGroups['1'] == '6') ){//6=manager
					$usertype = "Manager";
				}else {
					
				}
				$authOutput = new AuthOutput($usertype,$perms,$token);
				return $authOutput;	
				
			} else {
				return new SoapFault("JoomlaServerAuthFault", "User does not exist : ".$params->login);
			}
			//return $ret;
			
			$authOutput = new AuthOutput($usertype,$perms,$token);
			return $authOutput;
			
			
		}else if ($result== "false"){
			return new SoapFault("JoomlaServerAuthFault", "Autification KO for : ".$params->login);
		}else if ($result == "no_admin"){
			return new SoapFault("JoomlaServerAuthFault", "User is not a Super Administrator : ".$params->login);
		}else{
			return new SoapFault("JoomlaServerAuthFault", "User does not exist : ".$params->login);
		}

	}
	
	/**
    *  function getPermsFormUID
	* (internal use)
    * @param login/pass
    * @return perms
	*/
	function getPermsFormUID($uid) {
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);

		$query->select('perms, customer_number');
		$query->from('#__virtuemart_vmusers');
		$query->where('virtuemart_user_id=' . $db->Quote($uid));

		$db->setQuery($query);
		$result = $db->loadObject();
		
		$perms = "noperms";
		$usertype = "user";
		if ($result) {
			return $result->perms;
		}
	}
	
	
	
	
	/* SOAP SETTINGS */
	if ($vmConfig->get('soap_ws_user_on')==1){

		/* SOAP SETTINGS */
		ini_set("soap.wsdl_cache_enabled", $vmConfig->get('soap_ws_user_cache_on')); // wsdl cache settings
		
		$options = array('soap_version' => SOAP_1_2);
		
		/** SOAP SERVER **/
		$uri = str_replace("/free", "", JURI::root(false));
		if (empty($conf['BASESITE']) && empty($conf['URL'])){
			$server = new SoapServer('..'.DS.'VM_Users.wsdl');
			//$server = new SoapServer($uri.'/VM_UsersWSDL.php');
		}else if (!empty($conf['BASESITE'])){
			$server = new SoapServer('http://'.$conf['URL'].'/'.$conf['BASESITE'].'/administrator/components/com_virtuemart/services/VM_UsersWSDL.php');
		}else {
			$server = new SoapServer('http://'.$conf['URL'].'/administrator/components/com_virtuemart/services/VM_UsersWSDL.php');
		}
				
		/* Add Functions */
		$server->addFunction("GetUsers");
		$server->addFunction("Authentification");
		$server->addFunction("GetUserFromEmailOrUsername");
		$server->addFunction("GetAllCountryCode");
		$server->addFunction("GetAuthGroup");
		$server->addFunction("GetAllStates");
		$server->addFunction("GetShopperGroup");
		$server->addFunction("GetAllVendor");
		$server->addFunction("GetAllVendorCategory");
		$server->addFunction("GetAllManufacturer");
		$server->addFunction("GetAllManufacturerCat");
		$server->addFunction("GetAvailableVendorImages");
		$server->addFunction("GetVersions");
		$server->addFunction("GetAdditionalUserInfo");
		$server->addFunction("GetSessions");
		$server->addFunction("GetWaitingList");
		$server->addFunction("GetUserInfoFromOrderID");
		$server->addFunction("GetUserfields");
		
		$server->handle();
		
	}else{
		echoXmlMessageWSDisabled('User');
	}
?> 