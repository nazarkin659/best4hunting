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

// Include the latest functions only once
require_once dirname(__FILE__).'/helper.php';

$current_url 		= modFbLikeVirtuemartHelper::getCurrentUrl();

$discount_type		= $params->get('discount_type');
$discount			= $params->get('discount');
$discount_days		= (int)$params->get('discount_days');
$discount_days		= $discount_days>0 ? $discount_days : 1;
$facebook_url		= $params->get('facebook_url', $current_url);
$twitter_url		= $params->get('twitter_url', $current_url);
$twitter_username	= $params->get('twitter_username');
$google_plus_url	= $params->get('google_plus_url', $current_url);
$facebook 			= (int)$params->get('facebook', 1);
$twitter 			= (int)$params->get('twitter', 1);
$google_plus 		= (int)$params->get('google_plus', 1);

$layout				= htmlspecialchars($params->get('layout', 'default'));
$moduleclass_sfx	= htmlspecialchars($params->get('moduleclass_sfx'));

$callback_url 		= $current_url;
$twitter_data_text	= JText::_('MOD_FB_LIKE_VIRTUEMART_TWITTER_DATA_TEXT');
$tweet_label		= JText::_('MOD_FB_LIKE_VIRTUEMART_TWEET');

$lang_tag			= modFbLikeVirtuemartHelper::getLangTag();

$virtuemart_found = modFbLikeVirtuemartHelper::isVirtueMartInstalled();
if($virtuemart_found) {
	modFbLikeVirtuemartHelper::createTableSocialDiscount();
}

require JModuleHelper::getLayoutPath('mod_fb_like_virtuemart', $layout);