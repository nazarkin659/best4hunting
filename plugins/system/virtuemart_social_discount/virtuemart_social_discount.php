<?php
/* ------------------------------------------------------------------------------------------------------*
 # @package				: virtuemart_social_discount - Social Discount for VirtueMart.
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

jimport('joomla.plugin.plugin');
jimport('joomla.application.module.helper');

class plgSystemVirtuemart_Social_Discount extends JPlugin
{
	var $isAdmin;
	function plgSystemVirtuemart_Social_Discount(&$subject, $config) 
	{
		$app	= JFactory::getApplication();
		$user 	= JFactory::getUser();
				
		parent::__construct($subject, $config);
				
		$this->isAdmin	= $app->isAdmin() ? true : false;
		if($this->isAdmin) {
			return;
		}
	}
	
	function onAfterInitialise()
	{
		$module_helper = JPATH_SITE.DS.'modules'.DS.'mod_fb_like_virtuemart'.DS.'helper.php';
		if(file_exists($module_helper)) {
			require_once $module_helper;
		}
		else {
			return;
		}
		$this->processAjaxSocialDiscount();
		$this->analyzeVMSocialDiscounts();
		$this->checkVMSocialDiscount();
		$this->validateVMSocialDiscount();
	}
	
	function processAjaxSocialDiscount() 
	{
		$action = JRequest::getVar('action');
		$social_network = JRequest::getVar('social_network');
		$social_networks = array('facebook', 'twitter', 'google_plus');
		$actions = array('add_discount', 'remove_discount');
		if(in_array($social_network, $social_networks) && in_array($action, $actions))
		{
			$ob_active = ob_get_length () !== FALSE;
			if($ob_active)
			{
				while (@ ob_end_clean());
					if(function_exists('ob_clean'))
					{
						@ob_clean();
					}
			}
			ob_start();
			$cache =& JFactory::getCache('mod_fb_like_virtuemart');
			$cache->clean();
				
			$action			= JRequest::getVar('action');
			$social_network = JRequest::getVar('social_network');
			
			$social_networks 	= array('facebook', 'twitter', 'google_plus');
			$actions 			= array('add_discount', 'remove_discount');
			if(!in_array($social_network, $social_networks) || !in_array($action, $actions)) {
				return;
			}
			
			$language = JFactory::getLanguage();
			$language->load('mod_fb_like_virtuemart');
			
			$ret = modFbLikeVirtuemartHelper::processSocialDiscount($action, $social_network);
			
			$failed = 'MOD_FB_LIKE_VIRTUEMART_DISCOUNT_FAILED';
			if(isset($ret['message'])) {
				if(isset($ret['coupon_code'])) {
					$coupon_info = modFbLikeVirtuemartHelper::getVMCoupon($ret['coupon_code']);
					$coupon_date = isset($coupon_info['coupon_expiry_date']) && $coupon_info['coupon_expiry_date'] != '0000-00-00 00:00:00' ? strtotime($coupon_info['coupon_expiry_date']) : '';
					$date = JFactory::getDate($coupon_date);
					$coupon_date = $coupon_date ? $date->format('Y-m-d H:i:s') : JText::_('MOD_FB_LIKE_VIRTUEMART_DISCOUNT_NEVER');
					if(isset($ret['coupon_days'])) {
						$ret['message'] = JText::sprintf($ret['message'], $ret['coupon_code'], $ret['coupon_code'], $coupon_date, $ret['coupon_days']);
					}
					else {
						$ret['message'] = JText::sprintf($ret['message'], $ret['coupon_code']);
					}
				}
				else {
						$ret['message'] = JText::_($ret['message']);
					}
			}
			else {
				$ret['message'] = JText::_($failed);
			}
			$ret['message_type'] 	= isset($ret['message_type']) ? $ret['message_type'] : 'message';
			
			$message =  $ret['message'];
			if(isset($ret['cart_url'])) {
				$cart_link 	= JRoute::_('index.php?option=com_virtuemart&view=cart');
				$cart_link 	= str_replace("/modules/mod_fb_like_virtuemart/helpers/", "/", $cart_link);
				$message 	.= '<br><a class="vmsdMsg" href="'.$cart_link.'">'.JText::_('MOD_FB_LIKE_VIRTUEMART_SHOW_CART').'</a>';
			}
			
			$ob_active = ob_get_length() !== FALSE;
			if($ob_active)
			{
				while(@ob_end_clean()); {
					if(function_exists('ob_clean'))
					{
						@ob_clean();
					}
				}
			}
			
			$json['message'] 		= $message;
			$json['message_type'] 	= $ret['message_type'];
			$json 					= modFbLikeVirtuemartHelper::json_encode($json);
			echo $json;
			jexit();
		}
	}
	
	function analyzeVMSocialDiscounts() {
		modFbLikeVirtuemartHelper::analyzeVMSocialDiscounts();
	}
	
	function checkVMSocialDiscount() {
		modFbLikeVirtuemartHelper::checkVMSocialDiscount();
	}
	
	function validateVMSocialDiscount() {
		modFbLikeVirtuemartHelper::validateVMSocialDiscount();
	}	
}
?>