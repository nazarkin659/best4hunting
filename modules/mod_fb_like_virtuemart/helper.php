<?php
/* ------------------------------------------------------------------------------------------------------*
 # @package				: mod_fb_like_virtuemart - Social Discount for VirtueMart
 # ------------------------------------------------------------------------------------------------------*
 # @author				: Thakker Technologies
 # @copyright			: Copyright (C) 2012 Thakker Technologies. All rights reserved.
 # @license				: http://www.gnu.org/copyleft/gpl.html GNU/GPL, see license.txt
 # Demo	url				: http://joomla.thakkertech.com
 # Technical support	: http://www.thakkertech.com/forum/4-joomla-extensions-bug-report.html
 # ------------------------------------------------------------------------------------------------------*
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class modFbLikeVirtuemartHelper
{
	static function getParams()
	{
		$db		= JFactory::getDBO();
		$registry = new JRegistry;
		$query	= "SELECT params FROM #__modules WHERE module='mod_fb_like_virtuemart' ORDER BY id ASC LIMIT 1";
		$db->setQuery($query);
		$params = $db->loadResult();
		if(empty($params)) {
			return NULL;
		}
		$registry->loadString($params);
		$params = $registry->toArray();
		return $params;
	}
	
	static function getModuleId()
	{
		$db		= JFactory::getDBO();
		$query	= "SELECT id FROM #__modules WHERE module='mod_fb_like_virtuemart' ORDER BY id ASC LIMIT 1";
		$db->setQuery($query);
		$ret = (int)$db->loadResult();
		return $ret;
	}
	
	static function getCurrentUrl()
	{
		$juri	= JURI::getInstance();
		$base	= $juri->toString( array('scheme', 'host', 'port'));
		$url 	= $base.$_SERVER['REQUEST_URI'];
		return $url;
	}
	
	static function isVirtueMartInstalled()
	{
		$db		= JFactory::getDBO();
		$query	= $db->getQuery(true);
		$query->select('extension_id');
		$query->from('#__extensions');
		$query->where('type = "component"');
		$query->where('element = "com_virtuemart"');
		$query->where('enabled = "1"');
		$db->setQuery($query, 0);
		$installed = (int)$db->loadResult()>0 ? true : false;
		return $installed;
	}
		
	static function createTableSocialDiscount($new='')
	{
		$db		= JFactory::getDBO();
		$query	= "DELETE FROM `#__virtuemart_coupons` WHERE `coupon_code` LIKE '%SocialDiscount-%' AND `coupon_type`='gift' AND `coupon_expiry_date`<NOW()";
		$db->setQuery($query);
		$db->query();
		
		if($new) {
			$query	= "DROP TABLE IF EXISTS `#__virtuemart_social_discount`";
			$db->setQuery($query);
			$db->query();
		}
		
		$query	= "CREATE TABLE IF NOT EXISTS `#__virtuemart_social_discount` (
						`id` INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
						`coupon_code` VARCHAR( 32 ) NOT NULL ,
						`user_id` INT( 11 ) NOT NULL DEFAULT '0',
						`social_network` VARCHAR( 20 ) NOT NULL ,
						`email` VARCHAR( 255 ) NOT NULL,
						`module_id` INT( 11 ) NOT NULL DEFAULT '0',
						`indentifier` VARCHAR( 255 ) NOT NULL,
						`order_id` INT( 11 ) NOT NULL DEFAULT '0', 
						`date` DATETIME NOT NULL
					)";
		$db->setQuery($query);
		$db->query();
		return true;
	}
	
	static function createVitueMartCoupon($social_network)
	{
		$db		= JFactory::getDBO();
		$params = modFbLikeVirtuemartHelper::getParams();
		
		$coupon_code = modFbLikeVirtuemartHelper::generateCoupon($social_network);
		$discount_type = isset($params['discount_type']) ? strtolower($params['discount_type']) : '';
		$discount_type = $discount_type=='f' ? 'total' : 'percent';
		$coupon_type = 'gift';
		$coupon_value = isset($params['discount']) ? (float)$params['discount'] : '';
		$params = modFbLikeVirtuemartHelper::getParams();
		$coupon_days = isset($params['discount_days']) && (int)$params['discount_days']>0 ? (int)$params['discount_days'] : 1;
		
		$query	= "INSERT INTO `#__virtuemart_coupons` SET `coupon_code`='".$coupon_code."', `percent_or_total`='".$discount_type."', `coupon_type`='".$coupon_type."', `coupon_value`='".$coupon_value."', `coupon_start_date`=NOW(), `coupon_expiry_date`=DATE_ADD(NOW(), INTERVAL ".$coupon_days." DAY), `coupon_value_valid`='0', `published`='1', `created_on`=NOW(), `created_by`='0'";
		$db->setQuery($query);
		if($db->query()) {
			return $coupon_code;
		}
		return NULL;
	}
	
	static function processSocialDiscount($action, $social_network)
	{
		$ret = array();
		$db	= JFactory::getDBO();
		$app   = JFactory::getApplication('site');
		if($action=='add_discount') {
			$params = modFbLikeVirtuemartHelper::getParams();
			$coupon_days = isset($params['discount_days']) && (int)$params['discount_days']>0 ? (int)$params['discount_days'] : 1;
			$valid = modFbLikeVirtuemartHelper::validateDiscount($social_network);
			if(!$valid) {
				$ret['message'] = 'MOD_FB_LIKE_VIRTUEMART_DISCOUNT_ALREADY_USED';
				$ret['message_type'] = 'warning';
				return $ret;
			}
			$bApplied = modFbLikeVirtuemartHelper::isVMCouponApplied($social_network);
			if($bApplied) {
				$ret['message'] = 'MOD_FB_LIKE_VIRTUEMART_DISCOUNT_ALREADY_APPLIED';
				$ret['cart_url'] = true;
				$ret['message_type'] = 'message';
				$ret['coupon_code'] = modFbLikeVirtuemartHelper::getCouponCode($social_network);
				return $ret;
			}
			
			$coupon_code = modFbLikeVirtuemartHelper::createVitueMartCoupon($social_network);
			if(!$coupon_code) {
				$ret['message_type'] = 'error';
				return $ret;
			}
			
			if(!class_exists( 'VmConfig' )) {
				require(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_virtuemart'.DS.'helpers'.DS.'config.php');
			}
			
			if(!class_exists('VirtueMartCart')) {
				require(JPATH_BASE.DS.'components'.DS.'com_virtuemart'.DS.'helpers'.DS.'cart.php');
			}
			$cart = VirtueMartCart::getCart(false);
			$data = $cart->prepareAjaxData();
			$lang = JFactory::getLanguage();
			$extension = 'com_virtuemart';
			$lang->load($extension);
			if($cart) {
				$msg = $cart->setCouponCode($coupon_code);
				if(!empty($msg)) {
					$ret['message'] = $msg;
					$ret['message_type'] = 'error';
					return $ret;
				}
			}
			
			$user 	= JFactory::getUser();
			$coupon_code 	= $coupon_code;
			$user_id 		= (int)$user->get('id');
			$email 			= $user_id>0 ? $user->get('email') : '';
			$module_id 		= (int)modFbLikeVirtuemartHelper::getModuleId();
			$indentifier 	= $email.'-'.$social_network.'-'.$module_id;
			$indentifier 	= strtolower($indentifier);
			$data = array();
			$data['coupon_code'] = $coupon_code;
			$data['user_id'] = $user_id;
			$data['email'] = $email;
			$data['indentifier'] = $indentifier;
			$data['social_network'] = $social_network;
			modFbLikeVirtuemartHelper::createEntryVMSocialDiscount($data);

			$ret['message'] = 'MOD_FB_LIKE_VIRTUEMART_DISCOUNT_APPLIED';
			$ret['coupon_code'] = $coupon_code;
			$ret['coupon_days'] = $coupon_days;
						
			$ret['coupon_code'] = $coupon_code;
			$ret['cart_url'] = true;
			$ret['message_type'] = 'message';
			return $ret;
		}
		else {
			$bApplied = modFbLikeVirtuemartHelper::isVMCouponApplied($social_network);
			if(!$bApplied) {
				$ret['message'] = '';
				$ret['return'] = true;
				return $ret;
			}
			
			$couponCode = modFbLikeVirtuemartHelper::getCouponCode();
			if(!$couponCode) {
				$ret['message'] = '';
				$ret['return'] = true;
				return $ret;
			}
						
			if(!class_exists( 'VmConfig' )) {
				require(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_virtuemart'.DS.'helpers'.DS.'config.php');
			}
			
			if(!class_exists('VirtueMartCart')) {
				require(JPATH_BASE.DS.'components'.DS.'com_virtuemart'.DS.'helpers'.DS.'cart.php');
			}
			$cart = VirtueMartCart::getCart(false);
			$cart->couponCode = '';
			$cart->setCartIntoSession();
			
			modFbLikeVirtuemartHelper::removeVMCoupon($couponCode);
			modFbLikeVirtuemartHelper::removeRowVMSocialDiscount($couponCode);
						
			$ret['message'] = 'MOD_FB_LIKE_VIRTUEMART_DISCOUNT_REMOVED';
			$ret['cart_url'] = true;
			$ret['message_type'] = 'message';
			return $ret;
		}
	}
	
	static function removeRowVMSocialDiscount($coupon_code)
	{
		$db		= JFactory::getDBO();
		$query	= "DELETE FROM `#__virtuemart_social_discount` WHERE `coupon_code` LIKE '%SocialDiscount-%' AND `coupon_code`='".$coupon_code."' AND order_id=0";
		$db->setQuery($query);
		return $db->query();
	}
	
	static function removeVMCoupon($coupon_code)
	{
		$db		= JFactory::getDBO();
		$query	= "DELETE FROM `#__virtuemart_coupons` WHERE `coupon_code` LIKE '%SocialDiscount-%' AND `coupon_code`='".$coupon_code."'";
		$db->setQuery($query);
		return $db->query();
	}
		
	static function isVMCouponApplied($social_network)
	{
		$couponCode = modFbLikeVirtuemartHelper::getCouponCode();
		if(!$couponCode)
		{
			return false;
		}
		$couponCode = strtolower($couponCode);
		$social_network = $social_network ? strtolower($social_network) : '';
		switch($social_network)
		{
			case 'facebook':
			case 'twitter':
				if(strpos($couponCode, $social_network.'socialdiscount-')!==false) {
					return true;
				}
			break;
			case 'google_plus':
				if(strpos($couponCode, 'gplussocialdiscount-')!==false) {
					return true;
				}
			break;
			default:
				if(strpos($couponCode, 'socialdiscount-')!==false) {
					return true;
				}
			break;
		}
		return false;
	}
	
	static function getCouponCode()
	{
		$session = JFactory::getSession();
		$cartSession = $session->get('vmcart', 0, 'vm');
		$sessionCart = !empty($cartSession) ? unserialize( $cartSession ) : NULL;
		$couponCode = isset($sessionCart->couponCode) ? $sessionCart->couponCode : NULL;
		return $couponCode;
	}
		
	static function generateCoupon($social_network='')
	{
		$social_network = $social_network ? strtolower($social_network) : '';
		switch($social_network)
		{
			case 'facebook':
				$coupon_code = 'FacebookSocialDiscount-'.strtolower(substr(md5(time()), 0, 6));
			break;
			case 'twitter':
				$coupon_code = 'TwitterSocialDiscount-'.strtolower(substr(md5(time()), 0, 6));
			break;
			case 'google_plus':
				$coupon_code = 'GPlusSocialDiscount-'.strtolower(substr(md5(time()), 0, 6));
			break;
			default:
				$coupon_code = 'VMSocialDiscount-'.strtolower(substr(md5(time()), 0, 10));
			break;
		}
		return $coupon_code;
	}
	
	static function createEntryVMSocialDiscount($data=array())
	{
		$db		= JFactory::getDBO();
		$user 	= JFactory::getUser();
				
		$coupon_code 	= $data['coupon_code'];
		$user_id 		= (int)$user->get('id');
		$email 			= $user_id>0 ? $user->get('email') : $data['email'];
		$social_network = strtolower($data['social_network']);
		$module_id 		= (int)modFbLikeVirtuemartHelper::getModuleId();
		$indentifier 	= $email.'-'.$social_network.'-'.$module_id;
		$indentifier 	= strtolower($indentifier);
			
		$query	= "INSERT INTO `#__virtuemart_social_discount` SET `coupon_code`='".$coupon_code."', `user_id`='".$user_id."', `email`='".$email."', `social_network`='".$social_network."', `module_id`='".$module_id."', `indentifier`='".$indentifier."', `date`=NOW()";
		$db->setQuery($query);
		$db->query();
	}
	
	static function validateDiscount($social_network)
	{
		$db	= JFactory::getDBO();
		$user = JFactory::getUser();
		$user_id = (int)$user->get('id');
		if(!$user_id>0)
		{
			return true;
		}
		$email 			= $user_id>0 ? $user->get('email') : $data['email'];
		$social_network = $social_network;
		$module_id 		= (int)modFbLikeVirtuemartHelper::getModuleId();
		
		$indentifier 	= $email.'-'.$social_network.'-'.$module_id;
		$indentifier 	= strtolower($indentifier);
			
		$query	= "SELECT id FROM `#__virtuemart_social_discount` WHERE `indentifier`='".$indentifier."' AND order_id>0 LIMIT 1";
		$db->setQuery($query);
		$ret = (int)$db->loadResult()>0 ? false : true;
		return $ret;
	}
	
	static function analyzeVMSocialDiscounts()
	{
		$db	= JFactory::getDBO();
		
		$query = "DELETE FROM `#__virtuemart_social_discount` WHERE order_id=0 AND DATE_ADD(`date`, INTERVAL 2 DAY)<NOW()";
		$db->setQuery($query);
		$db->query();
		
		$query = "SELECT * FROM `#__virtuemart_social_discount` WHERE !order_id>0";
		$db->setQuery($query);
		$rows = $db->loadAssocList();
		if(!empty($rows)) {
			foreach($rows as $row) {
				$coupon_code = $row['coupon_code'];
				$order = modFbLikeVirtuemartHelper::getOrderByCouponCode($coupon_code);
				if(!empty($order)) {
					$data = array();
					$order_id = $order['order_id'];
					$user_id = (int)$order['user_id'];
					$email = $order['email'];
					if($user_id>0) {
						$query = "SELECT email FROM `#__users` WHERE id='".$user_id."' LIMIT 1";
						$db->setQuery($query);
						$result = $db->loadResult();
						$email = $result ? $result : $email;
					}
					$data['coupon_code'] = $coupon_code;
					$data['user_id'] = $user_id;
					$data['email'] = $email;
					$data['order_id'] = $order_id;
					modFbLikeVirtuemartHelper::updateVMSocialDiscount($data);
				}
			}
		}
		return true;
	}
	
	static function getOrderByCouponCode($coupon_code)
	{
		$db	= JFactory::getDBO();
		
		$query = "SELECT DISTINCT vmo.virtuemart_order_id AS order_id, vmo.virtuemart_user_id AS user_id, vmou.email FROM `#__virtuemart_orders` AS vmo LEFT JOIN #__virtuemart_order_userinfos AS vmou ON ( vmou.virtuemart_order_id=vmo.virtuemart_order_id AND vmou.virtuemart_user_id=vmo.virtuemart_user_id ) WHERE vmo.coupon_code LIKE '".$coupon_code."' AND vmo.coupon_code!='' ORDER BY vmo.virtuemart_order_id DESC,vmou.virtuemart_order_userinfo_id DESC LIMIT 1";
		$db->setQuery($query);
		$row = $db->loadAssoc();
		return $row;
	}
	
	static function updateVMSocialDiscount($data)
	{
		$db	= JFactory::getDBO();
		
		$query = "UPDATE #__virtuemart_social_discount SET user_id='".$data['user_id']."', email='".$data['email']."', order_id='".$data['order_id']."', indentifier=CONCAT('".$data['email']."','-',social_network,'-',module_id) WHERE coupon_code LIKE '".$data['coupon_code']."' LIMIT 1";
		$db->setQuery($query);
		return $db->query();
	}
	
	static function checkVMSocialDiscount()
	{
		$db		= JFactory::getDBO();
		$user 	= JFactory::getUser();
		
		$coupon_code = modFbLikeVirtuemartHelper::getCouponCode();
		if($coupon_code=='')
		{
			return;
		}
		
		$coupon_info = modFbLikeVirtuemartHelper::getVMSocialDiscountInfo($coupon_code);
		if(empty($coupon_info))
		{
			return;
		}
		
		$user_id = (int)$user->get('id');
		if($coupon_info['user_id']==$user_id)
		{
			return;
		}
		
		$data = array();
		$data['coupon_code']= $coupon_code;
		$data['order_id'] 	= $coupon_info['order_id'];
		if($user_id>0)
		{
			$data['user_id']	= $user_id;
			$data['email'] 		= $user->get('email');
		}
		else
		{
			$data['user_id'] 	= 0;
			$data['email'] 		= '';
		}
		modFbLikeVirtuemartHelper::updateVMSocialDiscount($data);
		return;
	}
	
	static function getVMSocialDiscountInfo($coupon_code)
	{
		$db		= JFactory::getDBO();
		
		$query	= "SELECT * FROM `#__virtuemart_social_discount` WHERE `coupon_code` LIKE '".$coupon_code."' LIMIT 1";
		$db->setQuery($query);
		return $db->loadAssoc();
	}
	
	static function validateVMSocialDiscount()
	{
		$db		= JFactory::getDBO();
		$user 	= JFactory::getUser();
		
		$coupon_code = modFbLikeVirtuemartHelper::getCouponCode();
		if($coupon_code=='')
		{
			return;
		}
		
		$coupon_info = modFbLikeVirtuemartHelper::getVMSocialDiscountInfo($coupon_code);
		if(empty($coupon_info))
		{
			return;
		}
		
		$user_id 	= (int)$user->get('id');
		$email 		= (int)$user_id>0 ? $user->get('email') : '';
		
		if(!$user_id>0) {
			$session 		= JFactory::getSession();
			$cartSession 	= $session->get('vmcart', 0, 'vm');
			$cartSession 	= !empty($cartSession) ? unserialize($cartSession) : array();
			$email 			= !empty($cartSession) && isset($cartSession->BT['email']) ? $cartSession->BT['email'] : '';
		}
		
		if($email!='')
		{
			$query = "SELECT id FROM #__virtuemart_social_discount WHERE indentifier LIKE CONCAT('".$email."','-',social_network,'-',module_id) AND module_id='".$coupon_info['module_id']."' AND social_network='".$coupon_info['social_network']."' AND order_id>0 AND coupon_code!='".$coupon_code."' LIMIT 1";
			$db->setQuery($query);
			$found = (int)$db->loadResult();
			if($found>0)
			{
				modFbLikeVirtuemartHelper::removeCurrentVMDiscount($coupon_code);
			}
		}
	}
	
	static function removeCurrentVMDiscount($coupon_code)
	{
		$app   = JFactory::getApplication('site');
		$language = JFactory::getLanguage();
		$language->load('mod_fb_like_virtuemart');
		
		if(!class_exists( 'VmConfig' )) {
			require(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_virtuemart'.DS.'helpers'.DS.'config.php');
		}
		
		if(!class_exists('VirtueMartCart')) {
			require(JPATH_BASE.DS.'components'.DS.'com_virtuemart'.DS.'helpers'.DS.'cart.php');
		}
		$cart = VirtueMartCart::getCart(false);
		$cart->couponCode = '';
		$cart->setCartIntoSession();
			
		modFbLikeVirtuemartHelper::removeVMCoupon($coupon_code);
		modFbLikeVirtuemartHelper::removeRowVMSocialDiscount($coupon_code);
		
		$cart_link 	= JRoute::_('index.php?option=com_virtuemart&view=cart');
		
		$notice 	= JText::_('MOD_FB_LIKE_VIRTUEMART_DISCOUNT_ALREADY_USED_REMOVE');
		$notice 	.= ' <a href="'.$cart_link.'">'.JText::_('MOD_FB_LIKE_VIRTUEMART_SHOW_CART').'</a>';
		JHTML::_('behavior.modal');
		$app	= JFactory::getDocument();
		//<script type="text/javascript">
ob_start();
?>
sysMsg();
function sysMsg(){
if(document.addEventListener){
document.addEventListener("DOMContentLoaded",function(){
document.removeEventListener("DOMContentLoaded",arguments.callee,false);
showVmW('message','<?php echo addslashes($notice)?>');},false);}
else if(document.attachEvent){
document.attachEvent("onreadystatechange",function(){
if(document.readyState==="complete"){
document.detachEvent("onreadystatechange",arguments.callee);
showVmW('message','<?php echo addslashes($notice)?>');}});
if(document.documentElement.doScroll& window==window.top)(function(){
try{document.documentElement.doScroll("left");}
catch(error){setTimeout(arguments.callee,0);return;}
showVmW('message','<?php echo addslashes($notice)?>');
})();}}
function showVmW(typ, msg) {
	var typ = typ ? typ : 'notice';
	var msg = '<dd class="'+typ+' message"><ul><li>'+msg+'</li></ul></dd>';
	if(document.getElementById('system-message')) {
		if(document.getElementById('vm_social_discount_msg')) {
			document.getElementById('vm_social_discount_msg').innerHTML='';
		}
		document.getElementById('system-message').innerHTML=msg;
	}
	else {
		msg = '<dl id="system-message">'+msg+'</dl>';
		if(document.getElementById('vm_social_discount_msg')) {
			document.getElementById('vm_social_discount_msg').innerHTML=msg;
		}
	}
}
<?php
		$message = ob_get_clean();
		/*</script>*/
		$document = JFactory::getDocument();
		$document->addScriptDeclaration($message);
		//echo $message;
		return;
	}
	
	function json_encode($data) {
		if(function_exists('json_encode'))  
		{
			$return = json_encode($data);
		}
		else {
			require_once( JPATH_ROOT.DS.'modules'.DS.'mod_fb_like_virtuemart'.DS.'helpers'.DS.'JSON.php');
			$json = new Services_JSON();
			$return = $json->encode($data);
		}
		return $return;
	}
	
	function json_decode($data) {
		if(function_exists('json_decode'))  
		{
			$return = json_decode($data);
		}
		else {
			require_once( JPATH_ROOT.DS.'modules'.DS.'mod_fb_like_virtuemart'.DS.'helpers'.DS.'JSON.php');
			$json = new Services_JSON();
			$return = $json->decode($data);
		}
		return $return;
	}
	
	function getLangTag() {
		$lang = JFactory::getLanguage();
		$tag = $lang->getTag();
		$tag = str_replace('-', '_', $tag);
		$lang_tags = array('ca_ES', 'cs_CZ', 'cy_GB', 'da_DK', 'de_DE', 'eu_ES', 'en_PI', 'en_UD', 'ck_US', 'en_US', 'es_LA', 'es_CL', 'es_CO', 'es_ES', 'es_MX', 'es_VE', 'fb_FI', 'fi_FI', 'fr_FR', 'gl_ES', 'hu_HU', 'it_IT', 'ja_JP', 'ko_KR', 'nb_NO', 'nn_NO', 'nl_NL', 'pl_PL', 'pt_BR', 'pt_PT', 'ro_RO', 'ru_RU', 'sk_SK', 'sl_SI', 'sv_SE', 'th_TH', 'tr_TR', 'ku_TR', 'zh_CN', 'zh_HK', 'zh_TW', 'fb_LT', 'af_ZA', 'sq_AL', 'hy_AM', 'az_AZ', 'be_BY', 'bn_IN', 'bs_BA', 'bg_BG', 'hr_HR', 'nl_BE', 'en_GB', 'eo_EO', 'et_EE', 'fo_FO', 'fr_CA', 'ka_GE', 'el_GR', 'hi_IN', 'is_IS', 'id_ID', 'ga_IE', 'jv_ID', 'kn_IN', 'kk_KZ', 'la_VA', 'lv_LV', 'li_NL', 'lt_LT', 'mk_MK', 'mg_MG', 'ms_MY', 'mt_MT', 'mr_IN', 'mn_MN', 'ne_NP', 'pa_IN', 'rm_CH', 'sa_IN', 'sr_RS', 'so_SO', 'sw_KE', 'tl_PH', 'ta_IN', 'tt_RU', 'te_IN', 'ml_IN', 'uk_UA', 'uz_UZ', 'vi_VN', 'xh_ZA', 'zu_ZA', 'km_KH', 'tg_TJ', 'ar_AR', 'he_IL', 'ur_PK', 'fa_IR', 'sy_SY', 'yi_DE', 'gn_PY', 'qu_PE', 'ay_BO', 'se_NO', 'ps_AF', 'tl_ST');
		$lang_tag = 'en_US';
		if(in_array($tag, $lang_tags)) {
			$lang_tag = $tag;
		}
		return $lang_tag;
	}
	
	static function getVMCoupon($coupon_code)
	{
		$db		= JFactory::getDBO();
		$query	= "SELECT * FROM `#__virtuemart_coupons` WHERE `coupon_code` LIKE '%SocialDiscount-%' AND `coupon_code`='".$coupon_code."'";
		$db->setQuery($query);
		return $db->loadAssoc();
	}
	
}		