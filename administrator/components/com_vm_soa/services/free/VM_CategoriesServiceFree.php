<?php
define( '_VALID_MOS', 1 );
define( '_JEXEC', 1 );

/**
 * Virtuemart Category SOA Connector
 *
 * Virtuemart Category SOA Connector (Provide functions GetCategoryFromId, GetCategoryFromId, GetChildsCategory, GetCategorysFromCategory)
 * The return classe is a "Category" classe with attribute : id, name, description, price, quantity, image, fulliamage ,
 * attributes, parent produit, child id)
 *
 * @package    com_vm_soa
 * @subpackage components
 * @author     Mickael cabanas (cabanas.mickael|at|gmail.com)
 * @copyright  2012 Mickael Cabanas
 * @license    http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version    $Id: VM_CategoriesService.php 3821 2011-08-09 10:08:04Z mike75 $
 */
 
/** loading framework **/
include_once('../VM_Commons.php');

/**
 * Class Categorie
 *
 * Class "Categorie" with attribute : id, name, description,  image, fulliamage , parent category
 * attributes, parent produit, child id)
 *
 * @author     Mickael cabanas (cabanas.mickael|at|gmail.com)
 * @copyright  Mickael cabanas
 * @license    http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version    Release:
 */
	class Categorie {
		public $id="";
		public $vendor_id="";
		public $name="";
		public $slug="";
		public $description="";
		public $category_parent_id="";
		public $category_template="";
		public $category_layout="";
		public $category_product_layout="";
		public $products_per_row="";	
		public $limit_list_start="";
		public $limit_list_step="";
		public $limit_list_max="";	
		public $limit_list_initial="";
		public $hits="";
		public $published="";
		public $numberofproducts="";
		public $metarobot="";
		public $metaauthor="";
		public $metadesc="";
		public $metakey="";
		public $img_uri="";
		public $img_thumb_uri="";
		public $shared="";
		public $ordering="";
		public $customtitle="";
			
		//constructeur
		function __construct($id, $vendor_id, $name, $slug, $description, $category_parent_id, $category_template, $category_layout, $category_product_layout,
								$products_per_row,$limit_list_start,$limit_list_step,$limit_list_max,$limit_list_initial,$hits,$published,
								$numberofproducts,$metarobot,$metaauthor,$metadesc,$metakey,$img_uri,$img_thumb_uri,$shared,$ordering,$customtitle) {
								
			$this->id = $id;
			$this->vendor_id = $vendor_id;
			$this->name = $name;
			$this->slug = $slug;
			$this->description = $description;
			$this->category_parent_id = $category_parent_id;
			$this->category_template = $category_template;
			$this->category_layout = $category_layout;
			$this->category_product_layout = $category_product_layout;
			$this->products_per_row = $products_per_row;
			$this->limit_list_start = $limit_list_start;
			$this->limit_list_step = $limit_list_step;
			$this->limit_list_max = $limit_list_max;
			$this->limit_list_initial = $limit_list_initial;
			$this->hits = $hits;
			$this->published = $published;
			$this->numberofproducts = $numberofproducts;
			$this->metarobot = $metarobot;
			$this->metaauthor = $metaauthor;
			$this->metadesc = $metadesc;
			$this->metakey = $metakey;
			$this->img_uri = $img_uri;
			$this->img_thumb_uri = $img_thumb_uri;
			$this->shared = $shared;
			$this->ordering = $ordering;
			$this->customtitle = $customtitle;
			
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
 * Class Media
 *
 * Class "Media" with attribute : $virtuemart_media_id, $file_title, $file_description ...
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
		public $attachValue="";//only used in input
		
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
 * Class Template
 *
 * Class "Template" with attribute : $virtuemart_media_id, $file_title, $file_description ...
 *
 * @author     Mickael cabanas (cabanas.mickael|at|gmail.com)
 * @copyright  Mickael cabanas
 * @license    http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version    Release:
 */
	class Template {
		public $id="";
		public $template="";
		public $client_id="";
		public $home="";
		public $title="";
		public $params="";
			
		//constructeur
		function __construct($id, $template, $client_id, $home, $title, $params) {
								
			$this->id = $id;
			$this->template = $template;
			$this->client_id = $client_id;
			$this->home = $home;
			$this->title = $title;
			$this->params = $params;
		
		}
	}
/**
 * Class CommonReturn
 *
 * Class "CommonReturn" with attribute : returnCode, message, $returnData, 
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
    * This function get Childs of a category for a category ID
	* (expose as WS)
    * @param Object
    * @return array of Categories
   */
	function GetChildsCategories($params) {
	
		/* Authenticate*/
		$result = onAdminAuthenticate($params->loginInfo->login, $params->loginInfo->password,$params->loginInfo->isEncrypted);
	
		$vmConfig = getVMconfig();
		if ($vmConfig->get('soap_auth_getcat')==0){
			$result = "true";
		}

		//Auth OK
		if ($result == "true"){
		
			$forceLang = false;
			$WSlang = $params->loginInfo->lang;
			if ($WSlang != ""){
				if (validateLang($WSlang)){
					$forceLang = true;
				}else{
					return new SoapFault("LanguageFault","can't force language for lang code : ".$WSlang );
				}
			}
		
			if (!class_exists( 'VirtueMartModelCategory' )) require (JPATH_VM_ADMINISTRATOR.DS.'models'.DS.'category.php');
			$VirtueMartModelCategory = new VirtueMartModelCategory;
			
			$p_category_id = !empty($params->categoryId) ? $params->categoryId : "0";
			
			$db = JFactory::getDBO();
			
		
			$query  = "SELECT * FROM `#__virtuemart_categories` cat ";
			if ($forceLang){
				$query .= "join `#__virtuemart_categories_".$WSlang."` cat_lang ";
			}else{
				$query .= "join `#__virtuemart_categories_".VMLANG."` cat_lang ";
			}
			$query .= "on cat.virtuemart_category_id = cat_lang.virtuemart_category_id ";
			$query .= "JOIN `#__virtuemart_category_categories` REF ON cat.virtuemart_category_id=REF.category_child_id ";
			$query .= "WHERE category_parent_id = '$p_category_id'  ";
			
			if (!empty($params->category_publish)){
				if ($params->category_publish == "Y"){
					$query .= "AND published = 1 ";
				} else {
					$query .= "AND published = 0 ";
				}
			}
			
			$db->setQuery($query);
			$rows = $db->loadObjectList();
			
			foreach ($rows as $row)	{
			
				$parent_cat 	= $VirtueMartModelCategory->getParentCategory($row->virtuemart_category_id);
				$nbProd 		= $VirtueMartModelCategory->countProducts($row->virtuemart_category_id);
				
				$img = GetDefaultImages($params,$row->virtuemart_category_id,false); 
				$imgThumb = GetDefaultImages($params,$row->virtuemart_category_id,true); 

				$Categorie = new Categorie( $row->virtuemart_category_id,
											$row->virtuemart_vendor_id,
											$row->category_name,
											$row->slug,
											$row->category_description,
											!empty($parent_cat->virtuemart_category_id) ? $parent_cat->virtuemart_category_id : 0,
											$row->category_template,
											$row->category_layout,
											$row->category_product_layout,
											$row->products_per_row,
											$row->limit_list_start,
											$row->limit_list_step,
											$row->limit_list_max,
											$row->limit_list_initial,
											$row->hits,
											$row->published,
											$nbProd,
											$row->metarobot,
											$row->metaauthor,
											$row->metadesc,
											$row->metakey,
											$img,
											$imgThumb,
											$row->shared,
											$row->ordering,
											$row->customtitle
											);
				$catArray[] = $Categorie;
			
			}
			return $catArray;
			
			
		}else if ($result == "false"){
			return new SoapFault("JoomlaServerAuthFault", "Authentication KO for : ".$params->loginInfo->login);
		}else if ($result == "no_admin"){
			return new SoapFault("JoomlaServerAuthFault", "User is not a Super Administrator : ".$params->loginInfo->login);
		}else{
			return new SoapFault("JoomlaServerAuthFault", "User does not exist : ".$params->loginInfo->login);
		}
		
	}
	
	/**
    * This function get All the categories
	* (expose as WS)
    * @param Object
    * @return array of Categories
   */
	function GetAllCategories($params) {
			
		/* Authenticate*/
		$result = onAdminAuthenticate($params->loginInfo->login, $params->loginInfo->password,$params->loginInfo->isEncrypted);
		
		$vmConfig = getVMconfig();
		if ($vmConfig->get('soap_auth_getcat')==0){
			$result = "true";
		}
		
		//Auth OK
		if ($result == "true"){
		
			//return new SoapFault("JoomlaServerAuthFault", "lang".$langs['tag']);
			
			$forceLang = false;
			$WSlang = $params->loginInfo->lang;
			if ($WSlang != ""){
				if (validateLang($WSlang)){
					$forceLang = true;
				}else{
					return new SoapFault("LanguageFault","can't force language for lang code : ".$WSlang );
				}
			}
			
		
			if (!class_exists( 'VirtueMartModelCategory' )) require (JPATH_VM_ADMINISTRATOR.DS.'models'.DS.'category.php');
			$VirtueMartModelCategory = new VirtueMartModelCategory;
						
			$db = JFactory::getDBO();
			$query = "SELECT * FROM `#__virtuemart_categories` cat  ";
			
			if ($forceLang){
				$query .= "join `#__virtuemart_categories_".$WSlang."` cat_lang ";
			}else{
				$query .= "join `#__virtuemart_categories_".VMLANG."` cat_lang ";
			}
			
			$query .= "on cat.virtuemart_category_id = cat_lang.virtuemart_category_id ";
			$query .= "WHERE 1 ";
			if (!empty($params->category_publish)){
				if ($params->category_publish == "Y"){
					$query .= "AND published = 1 ";
				} else {
					$query .= "AND published = 0 ";
				}
			}
			
			if (!empty($params->category_id)){
				$query .= "AND virtuemart_category_id = '$params->category_id' ";
			}
			
			$db->setQuery($query);
			//return new SoapFault("JoomlaServerAuthFault", "query : ".$query);
			$rows = $db->loadObjectList();
			foreach ($rows as $row){
				
				$parent_cat 	= $VirtueMartModelCategory->getParentCategory($row->virtuemart_category_id);
				$nbProd 		= $VirtueMartModelCategory->countProducts($row->virtuemart_category_id);
				
				$img = GetDefaultImages($params,$row->virtuemart_category_id,false); 
				$imgThumb = GetDefaultImages($params,$row->virtuemart_category_id,true); 
				$Categorie = new Categorie($row->virtuemart_category_id,
											$row->virtuemart_vendor_id,
											$row->category_name,
											$row->slug,
											$row->category_description,
											!empty($parent_cat->virtuemart_category_id) ? $parent_cat->virtuemart_category_id : 0,
											$row->category_template,
											$row->category_layout,
											$row->category_product_layout,
											$row->products_per_row,
											$row->limit_list_start,
											$row->limit_list_step,
											$row->limit_list_max,
											$row->limit_list_initial,
											$row->hits,
											$row->published,
											$nbProd,
											$row->metarobot,
											$row->metaauthor,
											$row->metadesc,
											$row->metakey,
											$img,
											$imgThumb,
											$row->shared,
											$row->ordering,
											$row->customtitle
											);
				$catArray[] = $Categorie;
			}
			return $catArray;
			
		
		}else if ($result == "false"){
			return new SoapFault("JoomlaServerAuthFault", "Authentication KO for : ".$params->loginInfo->login);
		}else if ($result == "no_admin"){
			return new SoapFault("JoomlaServerAuthFault", "User is not a Super Administrator : ".$params->loginInfo->login);
		}else{
			return new SoapFault("JoomlaServerAuthFault", "User does not exist : ".$params->loginInfo->login);
		}
	}
	
		/**
    * This function get default img cat
	* insternal use
    * @param Object
    * @return array of Categories
   */
	function GetDefaultImages($params,$cat_id,$thumb = false) {
	
		$params->category_id = $cat_id;
		$medias = GetMediaCategory($params);
		
		$img_cat = "";
		if (is_array($medias)){
			foreach ($medias as $media){
				$img_cat = $media->file_url;
				if ($thumb){
					$img_cat = $media->file_url_thumb; 
				}
			}
		}
		return $img_cat;
	
	}



	/**
    * This function get All medias for category
	* (expose as WS)
    * @param Object
    * @return array of Media
   */
	function GetMediaCategory($params) {
	
		/* Authenticate*/
		$result = onAdminAuthenticate($params->loginInfo->login, $params->loginInfo->password,$params->loginInfo->isEncrypted);
		
		$vmConfig = getVMconfig();
		if ($vmConfig->get('soap_auth_cat_otherget')==0){
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
			
			//warning vm RC2.0.3  _getOrdering() in vmmodel may cause ambigus column on ordering
			$cat_id = $params->category_id;
			$files = $mediaModel->getFiles(false,true,null,$cat_id);
			
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
    * This function get Get Available Images on server 
    * (dir images/stories/virtuemart/category/)
	* (expose as WS)
    * @param Object
    * @return Array
   */
	function GetAvailableImages($params) {

		/* Authenticate*/
		$result = onAdminAuthenticate($params->loginInfo->login, $params->loginInfo->password,$params->loginInfo->isEncrypted);
		
		$vmConfig = getVMconfig();
		if ($vmConfig->get('soap_auth_cat_otherget')==0){
			$result = "true";
		}
		
		//Auth OK
		if ($result == "true"){
					
			$media_category_path = $vmConfig->get('media_category_path');
			if (empty($media_category_path)){
				return new SoapFault("GetAvailableImagesFault","media_category_path is not set, please check your virtuemart settings");
			}
			
			$uri = JURI::base();
			$uri = str_replace('administrator/components/com_vm_soa/services/free/', "", $uri);
			
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
    * This function get  Templates
	* (expose as WS)
    * @param Object
    * @return array of Templates
   */
	function GetTemplates($params) {
	
		/* Authenticate*/
		$result = onAdminAuthenticate($params->loginInfo->login, $params->loginInfo->password,$params->loginInfo->isEncrypted);
		
		$vmConfig = getVMconfig();
		if ($vmConfig->get('soap_auth_cat_otherget')==0){
			$result = "true";
		}
		
		//Auth OK
		if ($result == "true"){
		
			$db = JFactory::getDBO();
		
			$query  = "SELECT * FROM `#__template_styles` med WHERE 1 ";
			
			if (!empty($params->template->id)){
				$id = $params->template->id;
				$query .= " AND id = '$id' ";
			}
			if (!empty($params->template->template)){
				$template = $params->template->template;
				$query .= " AND template like '%$template%' ";
			}
			if (isset($params->template->client_id)){
				$client_id = $params->template->client_id;
				$query .= " AND client_id = '$client_id' ";
			}
			if (!empty($params->template->home)){
				$home = $params->template->home;
				$query .= " AND home like '%$home%' ";
				
			}if (!empty($params->template->title)){
				$title = $params->template->title;
				$query .= " AND title = '$title' ";
			}
			if (!empty($params->template->params)){
				$params = $params->template->params;
				$query .= " AND params = '$params' ";
			}
			
			
			//var_dump($query);die;
			$db->setQuery($query);
			$rows = $db->loadObjectList();
			
			foreach ($rows as $file){
				
				$template = new Template($file->id,
											$file->template,
											$file->client_id,
											$file->home,
											$file->title,
											$file->params
																						
											);
				$templateArray[] = $template;
			}
			return $templateArray;
			$errMsg=  $db->getErrorMsg();
			
			//return new SoapFault("GetChildsCategoriesFault", "debug : ",$query);
			if ($errMsg != null){
				return new SoapFault("GetTemplatesFault", "Error : ",$errMsg);
			}
			
		}else if ($result == "false"){
			return new SoapFault("JoomlaServerAuthFault", "Authentication KO for : ".$params->loginInfo->login);
		}else if ($result == "no_admin"){
			return new SoapFault("JoomlaServerAuthFault", "User is not a Super Administrator : ".$params->loginInfo->login);
		}else{
			return new SoapFault("JoomlaServerAuthFault", "User does not exist : ".$params->loginInfo->login);
		}
	}	
	
	
	/** SOAP SETTINGS **/
		
	if ($vmConfig->get('soap_ws_cat_on')==1){
		
		/* SOAP SETTINGS */
		
		ini_set("soap.wsdl_cache_enabled", $vmConfig->get('soap_ws_cat_cache_on')); // wsdl cache settings
		$options = array('soap_version' => SOAP_1_2);
		
		/** SOAP SERVER **/
		$uri = str_replace("/free", "", JURI::root(false));
		if (empty($conf['BASESITE']) && empty($conf['URL'])){
			$server = new SoapServer('..'.DS.'VM_Categories.wsdl');
			//$server = new SoapServer($uri.'/VM_CategoriesWSDL.php');
		}else if (!empty($conf['BASESITE'])){
			$server = new SoapServer('http://'.$conf['URL'].'/'.$conf['BASESITE'].'/administrator/components/com_virtuemart/services/VM_CategoriesWSDL.php');
		}else {
			$server = new SoapServer('http://'.$conf['URL'].'/administrator/components/com_virtuemart/services/VM_CategoriesWSDL.php');
		}
		
		/* Add Functions */
		$server->addFunction("GetAllCategories");
		$server->addFunction("GetChildsCategories");
		$server->addFunction("GetAvailableImages");
		$server->addFunction("GetMediaCategory");
		$server->addFunction("GetTemplates"); 
		
		$server->handle();
		
	}else{
		echoXmlMessageWSDisabled('Categories');
	}
?> 