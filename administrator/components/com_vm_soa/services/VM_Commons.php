<?php
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Virtuemart Commons method SOA Connector
 *
 * Virtuemart Product SOA Connector : File for upload file into components/com_virtuemart/shop_image/product,
 * components/com_virtuemart/shop_image/category, components/com_virtuemart/shop_image/vendor
 * and other commons method , constants
 *
 * @package    com_vm_soa
 * @subpackage component
 * @author     Mickael cabanas (cabanas.mickael|at|gmail.com)
 * @copyright  2011 Mickael Cabanas
 * @license    http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version    $Id:$
 */

ob_start();//to prevent some bad users change codes 

define('DS', DIRECTORY_SEPARATOR);

$soa_dir 	= dirname(__FILE__);
$jpath 		= realpath( dirname(__FILE__).DS.'..'.DS.'..'.DS.'..'.DS.'..'.DS.'' );
$jadminpath = realpath( dirname(__FILE__).DS.'..'.DS.'..'.DS.'..'.DS.'' );

define('JPATH_BASE',$jadminpath );
define('JPATH',$jpath );
//define('JPATH_COMPONENT',	JPATH_BASE . '/components/' . 'com_vm_soa');

if (file_exists(JPATH_BASE . DS.'includes'.DS.'defines.php')) {
	include_once JPATH_BASE . DS.'includes'.DS.'defines.php';
}
require_once JPATH_BASE.DS.'includes'.DS.'framework.php';
require_once JPATH_BASE.DS.'includes'.DS.'helper.php';
require_once JPATH_BASE.DS.'includes'.DS.'toolbar.php';
require_once JPATH_ADMINISTRATOR.DS.'components'.DS.'com_vm_soa'.DS.'conf.php';


// Mark afterLoad in the profiler.
JDEBUG ? $_PROFILER->mark('afterLoad') : null;

// Instantiate the application.
$app = JFactory::getApplication('administrator');

// Initialise the application.
$app->initialise();

if (!class_exists( 'VmConfig' )) require(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_virtuemart'.DS.'helpers'.DS.'config.php');
$vmConfig = VmConfig::loadConfig(true);

JTable::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_virtuemart'.DS.'tables');

$_REQUEST['filter_order_Dir'] = "DESC"; // since vm rc2.0.3b (Cannot access empty property)

//error_reporting(E_ALL ^ E_STRICT);
//error_reporting(0); // rien

define ("URI_TO_SOA_COMP"	, '/administrator/components/com_virtuemart/services');

//default services files name
define ("WSDL_CAT" 			, 'VM_Categories.wsdl');
define ("WSDL_CUSTOM" 		, 'VM_Customized.wsdl');
define ("WSDL_ORDER" 		, 'VM_Orders.wsdl');
define ("WSDL_PROD" 		, 'VM_Product.wsdl');
define ("WSDL_SQL" 			, 'VM_SQLQueries.wsdl');
define ("WSDL_USER" 		, 'VM_Users.wsdl');
define ("FSOAP" 		, 1);
define ("SERVICE_CAT" 		, 'free/VM_CategoriesServiceFree.php');
define ("SERVICE_ORDER" 	, 'free/VM_OrdersServiceFree.php');
define ("SERVICE_PROD" 		, 'free/VM_ProductServiceFree.php');
define ("SERVICE_SQL" 		, 'free/VM_SQLQueriesServiceFree.php');
define ("SERVICE_USER" 		, 'free/VM_UsersServiceFree.php');


define ("OK" , "0");
define ("KO" , "1");
define ("ADD" , 2);
define ("UP" , 3);
define ("DEL" , 4);
define ("NOTALLOK" , 5);
define ("ADDKO" , 6);
define ("UPKO" , 7);
define ("DELKO" , 8);
define ("ALLOK" , 9);

$confSoa['URL']='';
$confSoa['BASESITE']='';//let empty for now

	/**
    * This function return string message for WS
	* (NOT expose as WS)
    * @param string
    * @return result
    */

	function getWSMsg($nameObj,$type) {
	
		$msg = "Undefined";
		
		if ($type == ADD){
			$msg = $nameObj.' successfully added ';
		}else if ($type == UP) {
			$msg = $nameObj.' successfully Updated ';
		}else if ($type == DEL) {
			$msg = $nameObj.' successfully deleted ';
		}else if ($type == NOTALLOK) {
			$msg = 'Not all '.$nameObj.' processed successfully ';
		}else if ($type == ADDKO) {
			$msg = "Cannot add ".$nameObj.' ';
		}else if ($type == UPKO) {
			$msg = "Cannot update ".$nameObj.' ';
		}else if ($type == DELKO) {
			$msg = "Cannot delete ".$nameObj.' ';
		}else if ($type == ALLOK) {
			$msg = "All ".$nameObj.' processed successfully ';
		}
		return $msg;
	
	}
	
	/**
    * This function Set Post var with token
	* (NOT expose as WS)
    * @param 
    * @return 
    */
	function setToken() {
	
		$token  = JUtility::getToken();
		$_REQUEST[$token] = $token;
		$_POST[$token] = $token;
		
		$_REQUEST['filter_order_Dir'] = "DESC"; // since vm rc2.0.3b (Cannot access empty property)
	
	}


	/**
    *  function write a file ($data must be enocoded in base64Binary )
	* (not expose as WS)
    * @param login/pass
    * @return False/fileuurl 
	*/
	function writeMedia($data,$filename,$type,$isImg=false,$tmp=false,$replaceThumb=false){
	
		if (!class_exists( 'VmConfig' )) require(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_virtuemart'.DS.'helpers'.DS.'config.php');
		$vmConfig = VmConfig::loadConfig();
		
		$img_width = $vmConfig->get('img_width');
		$img_height = $vmConfig->get('img_height');
		
		if ($type == 'category'){
			$media_path = $vmConfig->get('media_category_path');
		} else if ($type == 'product'){
			$media_path = $vmConfig->get('media_product_path');
		}else if ($type == 'manufacturer'){
			$media_path = $vmConfig->get('media_manufacturer_path');
		}else if ($type == 'vendor'){
			$media_path = $vmConfig->get('media_vendor_path');
		}else {
			return false;
		}
		if ($tmp){
			$media_path = 'tmp'.DS;
		}
		if (!$isImg){
			//not an img // file to Sale // path outside joomla (get conf)
			$media_path = $vmConfig->get('forSale_path');
			$dir =  $vmConfig->get('forSale_path');
		}else{
			//img -> path in Joomla
			$dir = JPATH.DS.$media_path.'';	
		}
		
		$fileServerPath = $dir.$filename; // eg : /httpdocs/www/joomla/images/stories/virtuemart/category/toto.jpg
		$fileURL = $media_path.$filename; //eg : images/stories/virtuemart/category/toto.jpg
		
		//check if just for replace thumb
		if ($replaceThumb){
			$fileServerPath .= $fileServerPath.DS.'resized';
			$fileURL .= $fileURL.DS.'resized';
			$isImg = false; //do not create authoThumb
		}
		
		//SAVE FILE 
		$ifp = fopen( $fileServerPath, "wb" );
		//$data must be enocoded in base64Binary 
		fwrite( $ifp,  $data  );
		fclose( $ifp ); 
		
		if (!file_exists($fileServerPath)) {
			return false;
		}
		
		if ($isImg){
			//if is image then create thumb image
			$ret = createThumb($fileServerPath,$dir,$dir.DS.'resized',$img_width,$img_height);
			if (!$ret){
				return false;
			}
			$filesurls[0] = $fileURL; // full
			$filesurls[1] = $media_path.'resized/'.$ret; //thumb
			return $filesurls;
			
		} else {
			$filesurls[0] = $fileURL; // full
			return $filesurls; 
		}
		
	}
	
	/**
    *  function write a file ($data must be enocoded in base64Binary )
	* (not expose as WS)
    * @param login/pass
    * @return False/fileuurl 
	*/
	function writeTempMedia($data,$filename){
		
				
		$media_path = 'tmp'.DS;
		$dir = JPATH.DS.$media_path.'';	
		
		$fileServerPath = $dir.$filename; // eg : /httpdocs/www/joomla/images/stories/virtuemart/category/toto.jpg
		//$fileURL = $media_path.$filename; //eg : images/stories/virtuemart/category/toto.jpg
		
		
		//SAVE FILE 
		$ifp = fopen( $fileServerPath, "wb" );
		//$data must be enocoded in base64Binary 
		fwrite( $ifp,  $data  );
		fclose( $ifp ); 
		
		if (!file_exists($fileServerPath)) {
			return false;
		}else{
			return $fileServerPath;
		}
	}
	
	/**
    *  function create Thumb image 
	* (not expose as WS)
    * @param file /dir/dir thumb /w /h
    * @return False/true
   */
	function createThumb($file,$dirFull,$dirThumb,$thumb_widht,$thumb_height){

		// set folder for saving uploaded images
		//$save_to="/httpdocs/images";
		$save_to=$dirFull;

		// set folder for saving thumbnails of uploaded images
		//$thumb_save_to="/httpdocs/images/thumbs";
		$thumb_save_to=$dirThumb;
		// default width of thumbnails (in pixels)
		if (empty($thumb_widht)){
			$thumb_w=90;
		} else {
			$thumb_w=$thumb_widht;
		}

		if (empty($thumb_height)){
			
		} else {
			$h=$thumb_height;
		}

		if (true) {
			$file_name=$file;
			// get file extension   
			$ex= strrchr($file_name, '.');
			
			//$ex=strtolower(substr($file['name'], strrpos($file['name'], ".")+1, strlen($file['name'])));
			if ($ex==".jpg" || $ex==".JPG") {
				// read the uploaded JPG file
				$img=imagecreatefromjpeg($save_to.DS.basename($file_name));

				// get dimension of the image
				$ow=imagesx($img);
				$oh=imagesy($img);

				// keep aspect ratio
				$scale=$thumb_w/$ow;
				if (empty($h))
				$h=round($oh*$scale);

				$newimg=imagecreatetruecolor($thumb_w,$h);
				imagecopyresampled($newimg,$img,0,0,0,0,$thumb_w,$h,$ow,$oh);

				// saving the JPG thumbnail
				$FileNameTosave = pathinfo($file_name, PATHINFO_FILENAME); 
				$FileNameTosave .= "_".$h."x".$thumb_w.$ex;
				//imagejpeg($newimg, $thumb_save_to."/".$file_name, 90);
				imagejpeg($newimg, $thumb_save_to.DS.$FileNameTosave, 90);
				
				return $FileNameTosave;
				
			} else if ($ex==".png" || $ex==".PNG") {
				// read the uploaded JPG file
				$img=imagecreatefrompng($save_to.DS.basename($file_name));

				// get dimension of the image
				$ow=imagesx($img);
				$oh=imagesy($img);

				// keep aspect ratio
				$scale=$thumb_w/$ow;
				if (empty($h))
				$h=round($oh*$scale);

				$newimg=imagecreatetruecolor($thumb_w,$h);
				imagecopyresampled($newimg,$img,0,0,0,0,$thumb_w,$h,$ow,$oh);

				// saving the JPG thumbnail
				$FileNameTosave = pathinfo($file_name, PATHINFO_FILENAME); 
				$FileNameTosave .= "_".$h."x".$thumb_w.$ex;
				//imagejpeg($newimg, $thumb_save_to."/".$file_name, 90);
				imagepng($newimg, $thumb_save_to.DS.$FileNameTosave, 9);
				
				return $FileNameTosave;
				
			} else if ($ex==".gif" || $ex==".GIF") {
				// read the uploaded JPG file
				$img=imagecreatefromgif($save_to.DS.basename($file_name));

				// get dimension of the image
				$ow=imagesx($img);
				$oh=imagesy($img);

				// keep aspect ratio
				$scale=$thumb_w/$ow;
				if (empty($h))
				$h=round($oh*$scale);

				$newimg=imagecreatetruecolor($thumb_w,$h);
				imagecopyresampled($newimg,$img,0,0,0,0,$thumb_w,$h,$ow,$oh);

				// saving the JPG thumbnail
				$FileNameTosave = pathinfo($file_name, PATHINFO_FILENAME); 
				$FileNameTosave .= "_".$h."x".$thumb_w.$ex;
				//imagejpeg($newimg, $thumb_save_to."/".$file_name, 90);
				imagegif($newimg, $thumb_save_to.DS.$FileNameTosave, 90);
				
				return $FileNameTosave;
				
			} else {
				//echo "No thumbnail was generated - only JPG|PNG|GIF files are supported. your extention : ".$ex;
				return false;
			}
		} else {
			//echo "Error was occured while uploading!";
			return false;
		}    
		
		return true;

	}
	
	/**
	*
	* Mime type is Image ?
	*/
	function isMimeTypeImg($mimeType){
	
		$tab = explode('/',$mimeType);
		$mt = $tab[1];
		
		if ($mt=='jpg' || $mt=='jpeg' || $mt=='pjpeg' || $mt=='pjpeg' || $mt=='pjpeg' || $mt=='png' || $mt=='gif' ){
			return true;
		}else {
			return false;
		}
	
	}
	
	/**
	* mimetpe -> image/jpeg to .jpg
	*
	*/
	function mimeTypeToExtention($mimeType){
	
		$tab = explode('/',$mimeType);
		$mt = $tab[1];
		
		if ($mt=='jpg' || $mt=='jpeg' || $mt=='pjpeg' ){
			return '.jpg';
		}
		if ($mt=='png'){
			return '.png';
		}
		if ($mt=='gif'){
			return '.gif';
		}
		if ($mt=='x-gzip' || $mt=='x-gzip'){
			return '.gzip';
		}
		if ($mt=='x-zip' || $mt=='zip' || $mt=='x-zip-compressed' || $mt=='x-compressed'){
			return '.zip';
		}
		if ($mt=='xml'){
			return '.xml';
		}
		if ($mt=='x-excel'){
			return '.xls';
		}
		if ($mt=='msword'){
			return '.doc';
		}
		if ($mt=='pdf'){
			return '.pdf';
		}
		
		
		return '';
	
	}
	
	/**
	* mimetpe -> image/jpeg to .jpg
	*
	*/
	function extentionToMimeType($extention){
		//todo
		//$tab = explode('.',$mimeType);
		$mt = $extention;
		
		if ($mt=='jpg' || $mt=='jpeg' || $mt=='pjpeg' ){
			return 'image/jpg';
		}
		if ($mt=='png'){
			return 'image/png';
		}
		if ($mt=='gif'){
			return 'image/gif';
		}
		if ($mt=='gzip' || $mt=='zip'){
			return 'application/zip';
		}
		if ($mt=='x-zip' || $mt=='zip' || $mt=='x-zip-compressed' || $mt=='x-compressed'){
			return 'application/zip';
		}
		if ($mt=='xml'){
			return 'application/xml';
		}
		if ($mt=='xsl'){
			return 'application/x-excel';
		}
		if ($mt=='doc'){
			return 'application/msword';
		}
		if ($mt=='pdf'){
			return 'application/pdf';
		}
		
		
		return '';
	
	}
	
	/**
    * This function binb object to data
	* (NOT expose as WS)
    * @param string The Object
    * @return array of key and value
   */
   
	function bindObject($obj,&$data) {
		
		foreach ($obj as $key => $value){
			$data[$key]= $value;
		}
	}
	
	/**
    * This function binb object to data
	* (NOT expose as WS)
    * @param string The Object
    * @return array of key and value
   */
   
	function bindArray($obj,&$data) {
		
		foreach ($obj as $key => $value){
			$data[$key]= $value;
		}
	}
	
	/**
	*
	*
	*/
	function array_implode( $glue, $separator, $array ) {
		if ( ! is_array( $array ) ) return $array;
		$string = array();
		foreach ( $array as $key => $val ) {
			if ( is_array( $val ) )
				$val = implode( ',', $val );
			$string[] = "{$key}{$glue}{$val}";
		   
		}
		return implode( $separator, $string );
	   
	}
	
	/**
	*
	*
	*/
	function array_explode_bind($glue, $separator,$extra_fields_str,&$data) {
	
		$extra_fields  =  explode($separator, $extra_fields_str);
		
		foreach ($extra_fields as $field){
			$parval=  explode($glue, $field);
			$data[$parval[0]]= $parval[1];
			//ex make $data['basePrice'] = '0';
		}
		return $data;
	   
	}
	
	/**
	*
	* DEBUG
	*
	**/
	function debugInfile($data) {
		$on = false;
		if ($on){
			ob_start();
			print_r(PHP_EOL.'------------------'.date('l jS \of F Y h:i:s A').'-----------------'.PHP_EOL);
			print_r( $data );
			$output = ob_get_clean();
			file_put_contents( 'debug.txt', file_get_contents( 'debug.txt' ) . $output );
		}

	}
	

	/**
    *  function onAuthenticate
    *  $isEncrypted for MD5 passwd
	* (not expose as WS)
    * @param login/pass
    * @return true/false
   */
	function onAdminAuthenticate($login,$passwd,$isEncrypted=false){
		
		
		jimport('joomla.user.helper');
		
		$credentials['password']=$passwd;
		$credentials['username']=$login;
		
		
		//SINCE JOOMLA 2.5.18 STRONG ENCRYPTION //WE TEST IT BEFORE
		if (isSupEqualJoomlaVersion("2.5.18")){
			$testStrongOK = onAdminAuthenticateStrong($login,$passwd,$isEncrypted);
			if ($testStrongOK == "true"){
				return "true" ;
			}
		}
		
		if ($isEncrypted == "true" || $isEncrypted == "Y" || $isEncrypted == "1"  ){
			$isEncrypted = true;
		}else {
			$isEncrypted = false;
		}

		// Joomla does not like blank passwords
		if (empty($credentials['password'])) {
			return false;
		}
		
		// Initialise variables.
		$conditions = '';

		// Get a database object
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);

		$query->select('id, password');
		$query->from('#__users');
		$query->where('username=' . $db->Quote($credentials['username']));

		$db->setQuery($query);
		$result = $db->loadObject();
		
		

		if ($result) {
			$parts	= explode(':', $result->password);
			$crypt	= $parts[0];
			$salt	= @$parts[1];
			
			if ($isEncrypted){
				$testcrypt = $credentials['password'];
			}else {
				$testcrypt = JUserHelper::getCryptedPassword($credentials['password'], $salt);
			}
			//var_dump($testcrypt);die;

			if ($crypt == $testcrypt) {
				$user = JUser::getInstance($result->id); // Bring this in line with the rest of the system
				$autorGroups =$user->getAuthorisedGroups();
				$session =& JFactory::getSession();
				$session->set('user', $user); 
				
				$grpOK = checkAuthGroupIds($autorGroups);//check autorise IDS
				
				if ($grpOK){ 
					return "true";
				} else {
					return "no_admin";
				}
				
			} else {
				$ret= "false";
				
			}
		} else {
			$ret= "false";
		}
		return $ret;
	}
	
	/**
    *  function onAuthenticateStrong 
	* STRONG MODE SINCE JOOMLA 2.5.18
    *  $isEncrypted for MD5 passwd
	* (not expose as WS)
    * @param login/pass
    * @return true/false
	*/
	function onAdminAuthenticateStrong($login,$passwd,$isEncrypted=false){
		
		
		jimport('joomla.user.helper');
		
		$credentials['password']=$passwd;
		$credentials['username']=$login;
		
		if ($isEncrypted == "true" || $isEncrypted == "Y" || $isEncrypted == "1"  ){
			$isEncrypted = true;
		}else {
			$isEncrypted = false;
		}

		// Joomla does not like blank passwords
		if (empty($credentials['password'])) {
			return false;
		}
		
		// Initialise variables.
		$conditions = '';

		// Get a database object
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);

		$query->select('id, password');
		$query->from('#__users');
		$query->where('username=' . $db->Quote($credentials['username']));

		$db->setQuery($query);
		$result = $db->loadObject();
		
		

		if ($result) {
		
			$dbPW = $result->password;
			
			$user = JUser::getInstance($result->id); // Bring this in line with the rest of the system
			$autorGroups =$user->getAuthorisedGroups();
			$session =& JFactory::getSession();
			$session->set('user', $user); 
			$id = JUserHelper::getUserId($credentials['username']);
			
			$authOK = JUserHelper::verifyPassword($credentials['password'], $dbPW, $id);
			$grpOK = checkAuthGroupIds($autorGroups);//check autorise IDS
			
			if ($isEncrypted){ //password allready crypted in request
				if ($dbPW == $credentials['password'] ){
					$authOK = true;
				}
			}
			
			if ($authOK){ //AUTH OK
				
				if ($grpOK){ 
					return "true"; //AUTH AND GROUP OK
				} else {
					return "no_admin";//AUTH OK BUT NOT AN ADMIN
				}

			} else {
				$ret= "false";//AUTH KO
			}
			
		} else {//NOT FOUND IN DB
			$ret= "false";
		}
		return $ret;
	}
	
	/**
    *  function get group id
	* (not expose as WS)
    * @param login/pass
    * @return true/false
   */
	function checkAuthGroupIds($autorGroups){
		
		if (!class_exists( 'VmConfig' )) require(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_virtuemart'.DS.'helpers'.DS.'config.php');
		$vmConfig = VmConfig::loadConfig();
		
		$allow_admin=false;
		$allow_manager=false;
		$allow_admin = $vmConfig->get('soap_allow_admin')== "1" ? true : false;
		$allow_manager = $vmConfig->get('soap_allow_manager')== "1" ? true : false;
		
		foreach ($autorGroups as $group_id){
			if ($group_id == 8){ // /  8 	is 	Super Users 
				return true;
			}
			if ($allow_admin && $group_id == 7 ) { //7 admin
				return true;
			}
			if ($allow_manager && $group_id == 6 ) { //6 manager
				return true;
			}
		}
		return false;
		
		//var_dump($test);die;
	}
	
	/**
    *  function set user in session
	* (not expose as WS)
    * @param login/pass
    * @return true/false
   */
	function setUserInSession($user_id){
	
		$user = JUser::getInstance($user_id); // Bring this in line with the rest of the system
		//var_dump($user);die;
		//$autorGroups =$user->getAuthorisedGroups();
		$session =& JFactory::getSession();
		$session->set('user', $user); 
	
	}
	
	/**
	 * 
	 * 404
	 */
	function exit404() {
		global $_SERVER;
		header ("HTTP/1.1 404 Not Found");
		exit();
	}
	
	/**
	 * Echo SOAP/xml message when WS is disabled
	 * @param service name
	 * @return xml
	 */
	function echoXmlMessageWSDisabled($servicename) {
		
		$xml 	 = '<?xml version="1.0" encoding="UTF-8"?>';
		$xml 	.= '<SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/">';
		$xml 	.= '<SOAP-ENV:Body>';
		$xml 	.= '<SOAP-ENV:Fault>';
		$xml 	.= '<faultcode>';
		$xml 	.= 'SOAP-ENV:Server';
		$xml 	.= '</faultcode>';
		$xml 	.= '<faultstring>';
		$xml 	.= 'Virtuemart webservice ('.$servicename.') is disabled';
		$xml 	.= '</faultstring>';
		$xml 	.= '</SOAP-ENV:Fault>';
		$xml 	.= '</SOAP-ENV:Body>';
		$xml 	.= '</SOAP-ENV:Envelope>';
		
		header('Content-type: text/xml; charset=UTF-8'); 
		header("Content-Length: ".strlen($xml));
		
		echo $xml;
		exit();
	}
	
	/**
	 * i must call this in soap function handler
	 * @param force
	 * @return conf
	 */
	function deleteRecord($table,$pk,$id) {
		$db		= JFactory::getDbo();
		
		$query = "DELETE FROM ".$table." WHERE ".$pk." = ".$id." ";
		
		$db->setQuery($query);
		
		$result = $db->query();
		$errMsg=  $db->getErrorMsg();

		if ($errMsg==null){
			return true;
		}
		return $errMsg;
		
	}
	
	
	/**
	 * i must call this in soap function handler
	 * @param force
	 * @return conf
	 */
	function getVMconfig($force=false) {
		
		if (!class_exists( 'VmConfig' )) require(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_virtuemart'.DS.'helpers'.DS.'config.php');
		$vmConfig = VmConfig::loadConfig($force);
		return $vmConfig;
	}
	
	/**
	 * not working now
	 * @param force
	 * @return conf
	 */
	function setVMLangConfig($lang) {
		setToken();
		if(!class_exists('VirtueMartModelConfig'))require(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_virtuemart'.DS.'models'.DS.'config.php');
		$model = new VirtueMartModelConfig();
		
		$data['vmlang']=$lang;
		$model->store($data);
		
		if (!class_exists( 'VmConfig' )) require(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_virtuemart'.DS.'helpers'.DS.'config.php');
		$vmConfig = VmConfig::loadConfig(true);
		
		//return true;
	}
	
	/**
	 * 
	 * @param 
	 * @return langs
	 */
	function getLanguages() {
	
		$activeLangs = array() ;
		$language =& JFactory::getLanguage();
		$jLangs = $language->getKnownLanguages(JPATH_BASE);

		
		foreach ($jLangs as $jLang) {
			$jlangTag = strtolower(strtr($jLang['tag'],'-','_'));
			$activeLangs[] = $jlangTag;
		}
		return $activeLangs;
	
	}
	
	/**
	 * check if langcode exist
	 * @param 
	 * @return langs
	 */
	function validateLang($langCode) {
	
		$activeLangs = array() ;
		$language =& JFactory::getLanguage();
		$jLangs = $language->getKnownLanguages(JPATH_BASE);

		
		foreach ($jLangs as $jLang) {
			$jlangTag = strtolower(strtr($jLang['tag'],'-','_'));
			if ($langCode == $jlangTag){
				return true;
			}
		}
		return false;
	
	}
	
	/**
	* Get Virtuemart version 
	*/
	function getVMVersion(){
				
		$VMVERSION = new vmVersion();
		return vmVersion::$RELEASE;
	
	}
	
	/**
	* check if verison if sup or equal
	*/
	function isSupEqualVmVersion($version){
	
		$VMVERSION = new vmVersion();
		
		
		if (version_compare(vmVersion::$RELEASE,$version,'>=') ){
			return true;
		}else {
			return false;
		}
		
	}
	
	/**
	* isSupEqualJoomlaMinorVersion(18) check if joomla sup or equal 2.5.18
	* check if verison if sup or equal
	*/
	function isSupEqualJoomlaVersion($version){
	
		$_VERSION = new JVersion();
		
		if (version_compare($_VERSION->getShortVersion(),$version,'>=') ){
			return true;
		}else {
			return false;
		}
		
	}
	
	/**
	* Get component version 
	*/
	function getSOAVersion(){
	
			$version ="";
			$db = JFactory::getDBO();
			
			$query  = "SELECT manifest_cache FROM `#__extensions` ext ";
			$query .= "WHERE element ='com_vm_soa'  ";
			
			$db->setQuery($query);
			$rows = $db->loadObjectList();
			
			foreach ($rows as $row)	{

				$data = explode(',', $row->manifest_cache);
				$count = count($data);
				for ($i = 0; $i < $count; $i++) {
					//echo "data ".$data[$i];
					$data2 = explode(':',$data[$i]);
					//echo "data :".$data2[0].' -> '.$data2[1].'\n';
					if ($data2[0]=='"version"'){
					
						$version = $data2[1];
					}
				}
			
			}
			$version = str_replace('"', '', $version);
			
			return $version;

	}
	
	/**
	* internal use 
	* check if media exist
	**/
	function isMediaExist($media_id) {
		
		$db = JFactory::getDBO();
		$query = "SELECT virtuemart_media_id FROM `#__virtuemart_medias` ";
		$query .= "WHERE virtuemart_media_id = $media_id ";
		
		$db->setQuery($query);
		//return new SoapFault("JoomlaServerAuthFault", "query : ".$query);
		$rows = $db->loadObjectList();
		$find = false;
		foreach ($rows as $row){
			$find = true;
		}
		return $find;
	}
	
	/**
	* internal use 
	* check if media is linked
	**/
	function isMediaLinked($media_id,$obj_id,$type) {
		
		$tableName = "";
		$columnName = "";
		switch ($type){
			case "category":
				$tableName = "`#__virtuemart_category_medias`";
				$columnName = "virtuemart_category_id";
				break;
			case "product":
				$tableName = "`#__virtuemart_product_medias`";
				$columnName = "virtuemart_product_id";
				break;
			case "vendor":
				$tableName = "`#__virtuemart_vendor_medias`";
				$columnName = "virtuemart_vendor_id";
				break;
			case "manufacturer":
				$tableName = "`#__virtuemart_manufacturer_medias`";
				$columnName = "virtuemart_manufacturer_id";
				break;
			default:
				return false;
			break;
		
		}
		
		$db = JFactory::getDBO();
		$query = "SELECT id FROM $tableName ";
		$query .= "WHERE virtuemart_media_id = $media_id AND $columnName = $obj_id  ";
		
		$db->setQuery($query);
		
		$rows = $db->loadObjectList();
		
		$find = false;
		foreach ($rows as $row){
			$find = true;
		}
		return $find;
	}
	
	/**
	* $data media array
	* $obj_id : prod_id for example
	* type : product / category ...
	* internal use 
	* link media to
	**/
	function storeAndLinkMedia($data, $obj_id, $type) {
	
		//store in media table
		$media_id = storeMedia($data);
		
		if ($media_id == false){
			return false;
		}else {
			$ret = linkMedia($media_id, $obj_id, $type );
		}
		return $ret;
	
	}
	
	/**
	* internal use 
	* link media to
	**/
	function linkMedia($media_id,$obj_id,$type) {
		
		$tableName = "";
		$columnName = "";
		switch ($type){
			case "category":
				$tableName = "`#__virtuemart_category_medias`";
				$columnName = "virtuemart_category_id";
				break;
			case "product":
				$tableName = "`#__virtuemart_product_medias`";
				$columnName = "virtuemart_product_id";
				break;
			case "vendor":
				$tableName = "`#__virtuemart_vendor_medias`";
				$columnName = "virtuemart_vendor_id";
				break;
			case "manufacturer":
				$tableName = "`#__virtuemart_manufacturer_medias`";
				$columnName = "virtuemart_manufacturer_id";
				break;
			default:
				return false;
			break;
		
		}
		
		$db = JFactory::getDBO();
		$query = "INSERT INTO $tableName (virtuemart_media_id,$columnName) ";
		$query .= "VALUES  ($media_id ,$obj_id )  ";
		
		$db->setQuery($query);
		$result = $db->query();
		$errMsg=  $db->getErrorMsg();

		if ($errMsg==null){
			return true;
		}else{
			return false;
		}
		
	}
	
	function storeMedia($data) {
		
		$id_before = getLastId('virtuemart_media_id',"#__virtuemart_medias");
		/*
		$db = JFactory::getDBO();
		$query = "INSERT INTO `#__virtuemart_medias` (file_lang, virtuemart_vendor_id, file_title, file_description, file_meta, file_mimetype, file_type, file_url, file_url_thumb, file_is_product_image , file_is_downloadable, file_is_forSale, file_params, shared, published 	) ";
		$query .= "VALUES  ('', '1', $data['file_title'], $data['file_description'], $data['file_meta'] , $data['file_mimetype'], $data['file_type'] , $data['file_url'] , $data['file_url_thumb'] , $data['file_is_product_image'], $data['file_is_downloadable'], $data['file_is_forSale'], $data['file_params'], $data['shared'] , $data['media_published']          )  ";
		
		$db->setQuery($query);
		$result = $db->query();
		$errMsg=  $db->getErrorMsg();*/
		
		if (!class_exists( 'TableMedias' )) require (JPATH_VM_ADMINISTRATOR.DS.'tables'.DS.'medias.php');
		$db = JFactory::getDBO();
		$tableMedias = new TableMedias($db);
		
		//$tableMedias->virtuemart_media_id = $file_id;
		$tableMedias->file_title = $data['file_title'];
		$tableMedias->file_url = $data['file_url'];
		$tableMedias->file_url_thumb = $data['file_url_thumb'];
		$tableMedias->file_description = $data['file_description'];
		$tableMedias->file_meta = $data['file_meta'];
		$tableMedias->file_mimetype = $data['file_mimetype'];
		$tableMedias->published = $data['media_published'] ;
		$tableMedias->file_type = !empty($data['file_type']) ? $data['file_type'] : 'category';
		$tableMedias->file_is_product_image = $data['file_is_product_image'];
		$tableMedias->file_is_downloadable = $data['file_is_downloadable'];
		$tableMedias->file_is_forSale = $data['file_is_forSale'];
		$tableMedias->file_params = $data['file_params'];
		$tableMedias->shared = $data['shared'];		
		
		if ($tableMedias->check()){
			$tableMedias->store();
		}else{
			return false;
		}
		
		$id = getLastId('virtuemart_media_id',"#__virtuemart_medias");
		
		if ($id_before == $id){
			return false;
		}
		return $id;
		/*if ($errMsg == null){
			
		}else{
			return false;
		}*/
	}
	
	/**
	* internal use 
	* link media to
	**/
	function mediaIsLinkable($params) {
		if ($params->media->file_is_forSale == "1"){
			return false;
		}
		return true;
		
	
	}
	
	/**
	* internal use 
	* store vm conf
	**/
	function storeVmConfig($var_name,$value) {
		
		if(!class_exists('VirtueMartModelConfig'))require(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_virtuemart'.DS.'models'.DS.'config.php');
		$model = new VirtueMartModelConfig();
		
		$dataConf[$var_name]=intval($value);
		$model->store($dataConf);
	}
	
	/**
	* internal use
	* check mandatory field
	* return true false
	*/
	function checkRequiredField($params,$methodType) {
		
		$valid = true;
		
		if ($methodType == "AddShippingAdress"){
			if ($params->address_type_name == "" || $params->user_id == "" || $params->first_name == "" /*|| $params->last_name == "" || $params->address_1 == "" || $params->virtuemart_state_id == "" || $params->city == "" || $params->virtuemart_country_id == "" || $params->zip == "" || $params->address_type_name == "" */){
				$valid = false;
			}
		}
		
		if ($methodType == "UpdateShippingAdress"){
			if ($params->user_info_id == ""){
				$valid = false;
			}
		}
		
		if ($methodType == "DeleteShippingAdress"){
			if ($params->user_info_id == "" || $params->user_id == "" ){
				$valid = false;
			}
		}
		
		
		return $valid;
	}
	
	/**
	* internal use 
	* check if rows exist
	**/
	function rowExist($table,$id_name,$id) {
		
		$db = JFactory::getDBO();
		$query = "SELECT $id_name FROM `$table` ";
		$query .= "WHERE $id_name = $id ";
		
		$db->setQuery($query);
		//return new SoapFault("JoomlaServerAuthFault", "query : ".$query);
		$rows = $db->loadObjectList();
		$find = false;
		foreach ($rows as $row){
			$find = true;
		}
		return $find;
	}
	
	/**
	* getLastId('tax_rate_id','#__virtuemart_tax_rate')
	* generic getLastID
	*/
	function getLastId($idname,$table) {
			$db = JFactory::getDBO();
			$query = "SELECT $idname as id FROM $table ";
			$query .= "order by $idname desc limit  1 ";
			
			$db->setQuery($query);
			//return new SoapFault("JoomlaServerAuthFault", "query : ".$query);
			$rows = $db->loadObjectList();
			foreach ($rows as $row){
				return $row->id;
			}
	}
	
	/**
	 * 
	 * @param force
	 * @return conf
	 */
	function getNotInFreeSoap() {
		return new SoapFault("NotInFreeVersionFault", "Function not available in Free Version");
	}

	ob_end_clean();//to prevent some bad users change code 

?>