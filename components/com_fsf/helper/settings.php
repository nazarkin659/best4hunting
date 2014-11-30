<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;

/*
			<param name="kb_rate" default="1" />
			<param name="kb_comments" default="1" />
			<param name="kb_comments_captcha" default="0" />
			<param name="kb_comments_moderate" default="0" />
			<param name="test_moderate" default="0" />
			<param name="test_moderate_captcha" default="0" />
*/

define("FSF_IT_KB",1);
define("FSF_IT_FAQ",2);
define("FSF_IT_TEST",3);
define("FSF_IT_NEWTICKET",4);
define("FSF_IT_VIEWTICKETS",5);
define("FSF_IT_ANNOUNCE",6);
define("FSF_IT_LINK",7);
define("FSF_IT_GLOSSARY",8);
define("FSF_IT_ADMIN",9);
define("FSF_IT_GROUPS",10);

jimport( 'joomla.version' );
require_once( JPATH_SITE.DS.'components'.DS.'com_fsf'.DS.'helper'.DS.'helper.php' );

class FSF_Settings 
{
	static $fsf_view_settings;
	
	static function _GetSettings()
	{
		global $fsf_settings;
		
		if (empty($fsf_settings))
		{
			FSF_Settings::_GetDefaults();
			
			$db = JFactory::getDBO();
			$query = 'SELECT * FROM #__fsf_settings';
			$db->setQuery($query);
			$row = $db->loadAssocList();
			
			if (count($row) > 0)
			{
				foreach ($row as $data)
				{
					$fsf_settings[$data['setting']] = $data['value'];
				}
			}

			$query = 'SELECT * FROM #__fsf_settings_big';
			$db->setQuery($query);
			$row = $db->loadAssocList();
			
			if (count($row) > 0)
			{
				foreach ($row as $data)
				{
					$fsf_settings[$data['setting']] = $data['value'];
				}
			}
		}	
	}
	
	function _Get_View_Settings()
	{
		if (empty(FSF_Settings::$fsf_view_settings))
		{
			FSF_Settings::_View_Defaults();
			
			$db = JFactory::getDBO();
			$query = 'SELECT * FROM #__fsf_settings_view';
			$db->setQuery($query);
			$row = $db->loadAssocList();
			
			if (count($row) > 0)
			{
				foreach ($row as $data)
				{
					FSF_Settings::$fsf_view_settings[$data['setting']] = $data['value'];
				}
			}
		}	
	}
	
	static function _GetDefaults()
	{
		global $fsf_settings;
		
		if (empty($fsf_settings))
		{
			$fsf_settings = array();
			
			$fsf_settings['version'] = 0;
			$fsf_settings['fsj_username'] = '';
			$fsf_settings['fsj_apikey'] = '';
	
			$fsf_settings['jquery_include'] = "auto";
	
			$fsf_settings['perm_mod_joomla'] = 0;
			$fsf_settings['perm_article_joomla'] = 0;
			
			$fsf_settings['captcha_type'] = 'none';

			$fsf_settings['recaptcha_public'] = '';
			$fsf_settings['recaptcha_private'] = '';
			$fsf_settings['recaptcha_theme'] = 'red';
			$fsf_settings['comments_moderate'] = 'none';
			$fsf_settings['comments_hide_add'] = 1;
			$fsf_settings['email_on_comment'] = '';
			$fsf_settings['comments_who_can_add'] = 'anyone';
			
			$fsf_settings['test_use_email'] = 1;
			$fsf_settings['test_use_website'] = 1;
			$fsf_settings['commnents_use_email'] = 1;
			$fsf_settings['commnents_use_website'] = 1;
		
			$fsf_settings['hide_powered'] = 0;
			$fsf_settings['announce_use_content_plugins'] = 0;
			$fsf_settings['announce_use_content_plugins_list'] = 0;
			$fsf_settings['announce_comments_allow'] = 1;
			$fsf_settings['announce_comments_per_page'] = 0;
			$fsf_settings['announce_per_page'] = 10;
			
			$fsf_settings['kb_rate'] = 1;
			$fsf_settings['kb_comments'] = 1;
			$fsf_settings['kb_view_top'] = 0;
			
			$fsf_settings['kb_show_views'] = 1;
			$fsf_settings['kb_show_recent'] = 1;
			$fsf_settings['kb_show_recent_stats'] = 1;
			$fsf_settings['kb_show_viewed'] = 1;
			$fsf_settings['kb_show_viewed_stats'] = 1;
			$fsf_settings['kb_show_rated'] = 1;
			$fsf_settings['kb_show_rated_stats'] = 1;
			$fsf_settings['kb_show_dates'] = 1;
			$fsf_settings['kb_use_content_plugins'] = 0;
			$fsf_settings['kb_show_art_related'] = 1;
			$fsf_settings['kb_show_art_products'] = 1;
			$fsf_settings['kb_show_art_attach'] = 1;
			$fsf_settings['kb_contents'] = 1;
			$fsf_settings['kb_smaller_subcat_images'] = 0;
			$fsf_settings['kb_comments_per_page'] = 0;
			$fsf_settings['kb_prod_per_page'] = 5;
			$fsf_settings['kb_art_per_page'] = 10;
			$fsf_settings['kb_print'] = 1;
			
			
			$fsf_settings['test_moderate'] = 'none';
			$fsf_settings['test_email_on_submit'] = '';
			$fsf_settings['test_allow_no_product'] = 1;
			$fsf_settings['test_who_can_add'] = 'anyone';
			$fsf_settings['test_hide_empty_prod'] = 1;
			$fsf_settings['test_comments_per_page'] = 0;

			$fsf_settings['skin_style'] = 0;
			$fsf_settings['support_entire_row'] = 0;
			$fsf_settings['support_autoassign'] = 0;
			$fsf_settings['support_assign_open'] = 0;
			$fsf_settings['support_assign_reply'] = 0;
			$fsf_settings['support_user_attach'] = 1;
			$fsf_settings['support_lock_time'] = 30;
			$fsf_settings['support_show_msg_counts'] = 1;
			$fsf_settings['support_reference'] = "4L-4L-4L";
			$fsf_settings['support_list_template'] = "classic";
			$fsf_settings['support_custom_register'] = "";
			$fsf_settings['support_no_logon'] = 0;
			$fsf_settings['support_no_register'] = 0;
			$fsf_settings['support_info_cols'] = 1;
			$fsf_settings['support_actions_as_buttons'] = 0;
			$fsf_settings['support_choose_handler'] = 'none';
			$fsf_settings['support_dont_check_dupe'] = 1;
			$fsf_settings['support_admin_refresh'] = 0;
			$fsf_settings['support_only_admin_open'] = 0;
			
			$fsf_settings['support_user_reply_width'] = 56;
			$fsf_settings['support_user_reply_height'] = 10;
			$fsf_settings['support_admin_reply_width'] = 56;
			$fsf_settings['support_admin_reply_height'] = 10;
			$fsf_settings['ticket_label_width'] = 100;
			$fsf_settings['support_next_prod_click'] = 1;			
			$fsf_settings['support_subject_size'] = 35;			
			$fsf_settings['support_subject_message_hide'] = '';			
			$fsf_settings['support_filename'] = 0;			
			
			$fsf_settings['support_subject_at_top'] = 0;
			
			$fsf_settings['support_tabs_allopen'] = 0;	
			$fsf_settings['support_tabs_allclosed'] = 0;
			$fsf_settings['support_tabs_all'] = 0;			
			$fsf_settings['ticket_prod_per_page'] = 5;
			$fsf_settings['ticket_per_page'] = 10;
			
			$fsf_settings['support_restrict_prod'] = 0;
			
			$fsf_settings['css_hl'] = '#f0f0f0';
			$fsf_settings['css_tb'] = '#ffffff';
			$fsf_settings['css_bo'] = '#e0e0e0';
			
			$fsf_settings['display_head'] = '';
			$fsf_settings['display_foot'] = '';
			$fsf_settings['use_joomla_page_title_setting'] = 0;
			$fsf_settings['title_prefix'] = 1;
			
			$fsf_settings['content_unpublished_color'] = '#FFF0F0';

			if (FSF_Helper::Is16())
			{
				$fsf_settings['display_h1'] = '<h1>$1</h1>';
				$fsf_settings['display_h2'] = '<h2>$1</h2>';
				$fsf_settings['display_h3'] = '<h3>$1</h3>';
				$fsf_settings['display_popup'] = '<h2>$1</h2>';
				$fsf_settings['display_style'] = '.fsf_main tr, td 
{
	border: none;
	padding: 1px;
}';
				$fsf_settings['display_popup_style'] = '.fsf_popup tr, td 
{
	border: none;
	padding: 1px;
}';
			} else {
				$fsf_settings['display_h1'] = '<div class="component-header"><div class="componentheading">$1</div></div>';
				$fsf_settings['display_h2'] = '<div class="fsf_spacer contentheading">$1</div>';
				$fsf_settings['display_h3'] = '<div class="fsf_admin_create">$1</div>';
				$fsf_settings['display_popup'] = '<div class="component-header"><div class="componentheading">$1</div></div>';
				$fsf_settings['display_style'] = '';
				$fsf_settings['display_popup_style'] = '';
			}

			$fsf_settings['support_email_on_create'] = 0;
			$fsf_settings['support_email_handler_on_create'] = 0;
			$fsf_settings['support_email_on_reply'] = 0;
			$fsf_settings['support_email_handler_on_reply'] = 0;
			$fsf_settings['support_email_handler_on_forward'] = 0;
			$fsf_settings['support_email_on_close'] = 0;
			
			$fsf_settings['support_email_all_admins'] = 0;
			$fsf_settings['support_email_all_admins_only_unassigned'] = 0;
			$fsf_settings['support_email_all_admins_ignore_auto'] = 0;
			$fsf_settings['support_email_all_admins_can_view'] = 0;
			
			$fsf_settings['support_user_can_close'] = 1;
			$fsf_settings['support_user_can_reopen'] = 1;
			$fsf_settings['support_advanced'] = 1;
			$fsf_settings['support_allow_unreg'] = 0;
			$fsf_settings['support_delete'] = 1;
			$fsf_settings['support_advanced_default'] = 0;
			$fsf_settings['support_sceditor'] = 1;
			$fsf_settings['support_altcat'] = 0;

			$fsf_settings['support_cronlog_keep'] = 5;

			$fsf_settings['support_hide_priority'] = 0;
			$fsf_settings['support_hide_handler'] = 0;
			$fsf_settings['support_hide_users_tickets'] = 0;
			$fsf_settings['support_hide_tags'] = 0;
			$fsf_settings['support_email_unassigned'] = '';
			$fsf_settings['support_email_admincc'] = '';

			$fsf_settings['support_email_from_name'] = '';
			$fsf_settings['support_email_from_address'] = '';
			$fsf_settings['support_email_site_name'] = '';
			
			$fsf_settings['support_ea_check'] = 0;
			$fsf_settings['support_ea_all'] = 0;
			$fsf_settings['support_ea_reply'] = 0;
			$fsf_settings['support_ea_type'] = 0;
			$fsf_settings['support_ea_host'] = '';
			$fsf_settings['support_ea_port'] = '';
			$fsf_settings['support_ea_username'] = '';
			$fsf_settings['support_ea_password'] = '';
			$fsf_settings['support_ea_mailbox'] = '';

			$fsf_settings['support_user_message'] = '#c0c0ff';
			$fsf_settings['support_admin_message'] = '#c0ffc0';
			$fsf_settings['support_private_message'] = '#ffc0c0';
			
			$fsf_settings['support_basic_name'] = '';
			$fsf_settings['support_basic_username'] = '';
			$fsf_settings['support_basic_email'] = '';
			$fsf_settings['support_basic_messages'] = '';

			$fsf_settings['glossary_faqs'] = 1;
			$fsf_settings['glossary_kb'] = 1;
			$fsf_settings['glossary_announce'] = 1;
			$fsf_settings['glossary_link'] = 1;
			$fsf_settings['glossary_title'] = 0;
			$fsf_settings['glossary_use_content_plugins'] = 0;
			$fsf_settings['glossary_ignore'] = '';
			$fsf_settings['glossary_exclude'] = "a,script,pre,h1,h2,h3,h4,h5,h6";
			$fsf_settings['glossary_all_letters'] = 0;

			$fsf_settings['faq_popup_width'] = 650;
			$fsf_settings['faq_popup_height'] = 375;
			$fsf_settings['faq_popup_inner_width'] = 0;
			$fsf_settings['faq_use_content_plugins'] = 0;
			$fsf_settings['faq_use_content_plugins_list'] = 0;
			$fsf_settings['faq_per_page'] = 10;
			$fsf_settings['faq_cat_prefix'] = 1;
		
			// 1.9 comments stuff
			$fsf_settings['comments_announce_use_custom'] = 0;
			$fsf_settings['comments_kb_use_custom'] = 0;
			$fsf_settings['comments_test_use_custom'] = 0;	
			$fsf_settings['comments_general_use_custom'] = 0;		
			$fsf_settings['comments_testmod_use_custom'] = 0;	
				
			$fsf_settings['announce_use_custom'] = 0;		
			$fsf_settings['announcemod_use_custom'] = 0;		
			$fsf_settings['announcesingle_use_custom'] = 0;		
			
			// date format stuff
			$fsf_settings['date_dt_short'] = '';
			$fsf_settings['date_dt_long'] = '';
			$fsf_settings['date_d_short'] = '';
			$fsf_settings['date_d_long'] = '';
			$fsf_settings['timezone_offset'] = 0;
			
			$fsf_settings['mainmenu_moderate'] = 1;
			$fsf_settings['mainmenu_support'] = 1;
			
		}	
	}

	// return a list of settings that are used on the templates section
	function GetTemplateList()
	{
		$template = array();
		$template[] = "display_style";
		$template[] = "display_popup_style";
		$template[] = "display_h1";
		$template[] = "display_h2";
		$template[] = "display_h3";
		$template[] = "display_head";
		$template[] = "display_foot";
		$template[] = "display_popup";
		$template[] = "support_list_template";
		
		$template[] = "comments_announce_use_custom";
		$template[] = "comments_test_use_custom";
		$template[] = "comments_kb_use_custom";
		$template[] = "comments_general_use_custom";
		$template[] = "comments_testmod_use_custom";
		$template[] = "announce_use_custom";
		$template[] = "announcemod_use_custom";
		$template[] = "announcesingle_use_custom";
		
		$res = array();
		foreach($template as $setting)
		{
			$res[$setting] = $setting;
		}
		return $res;	
	}
	
	function StoreInTemplateTable()
	{
		$intpl = array();
		$intpl[] = "comments_general";	
		$intpl[] = "comments_announce";	
		$intpl[] = "comments_kb";	
		$intpl[] = "comments_test";	
		$intpl[] = "comments_testmod";	
		$intpl[] = "announce";	
		$intpl[] = "announcemod";	
		$intpl[] = "announcesingle";	
		
		$res = array();
		foreach($intpl as $setting)
		{
			$res[$setting] = $setting;
		}
		return $res;	
	}
		
	function GetLargeList()
	{
		$large = array();
		$large[] = "display_style";
		$large[] = "display_popup_style";
		$large[] = "display_h1";
		$large[] = "display_h2";
		$large[] = "display_h3";
		$large[] = "display_head";
		$large[] = "display_foot";
		$large[] = "display_popup";
		
		$res = array();
		foreach($large as $setting)
		{
			$res[$setting] = $setting;
		}
		return $res;	
	}
	
	static function get($setting)
	{
		global $fsf_settings;
		FSF_Settings::_GetSettings();
		return $fsf_settings[$setting];	
	}
	
	function &GetAllSettings()
	{
		global $fsf_settings;
		FSF_Settings::_GetSettings();
		return $fsf_settings;	
	}
	
	function &GetAllViewSettings()
	{
		FSF_Settings::_Get_View_Settings();
		return FSF_Settings::$fsf_view_settings;	
	}
	
	function _View_Defaults()
	{
		// FAQS
		
		// When Showing list of Categories
		FSF_Settings::$fsf_view_settings['faqs_always_show_faqs'] = 0;
		FSF_Settings::$fsf_view_settings['faqs_hide_allfaqs'] = 0;
		FSF_Settings::$fsf_view_settings['faqs_hide_tags'] = 0;
		FSF_Settings::$fsf_view_settings['faqs_hide_search'] = 0;
		FSF_Settings::$fsf_view_settings['faqs_show_featured'] = 0;
		FSF_Settings::$fsf_view_settings['faqs_num_cat_colums'] = 1;
		FSF_Settings::$fsf_view_settings['faqs_view_mode_cat'] = 'accordian';
		FSF_Settings::$fsf_view_settings['faqs_view_mode_incat'] = 'accordian';
		
		// When Showing list of FAQs
		FSF_Settings::$fsf_view_settings['faqs_always_show_cats'] = 0;
		FSF_Settings::$fsf_view_settings['faqs_view_mode'] = 'accordian';
		FSF_Settings::$fsf_view_settings['faqs_enable_pages'] = 1;
		
		// Glossary
		FSF_Settings::$fsf_view_settings['glossary_use_letter_bar'] = 0;
		
		// Testimonials
		FSF_Settings::$fsf_view_settings['test_test_show_prod_mode'] = 'accordian';
		FSF_Settings::$fsf_view_settings['test_test_pages'] = 1;
		FSF_Settings::$fsf_view_settings['test_test_always_prod_select'] = 0;
		
		
		// KB
		
		// Main Page
		FSF_Settings::$fsf_view_settings['kb_main_show_prod'] = 1;
		FSF_Settings::$fsf_view_settings['kb_main_show_cat'] = 0;
		FSF_Settings::$fsf_view_settings['kb_main_show_sidebyside'] = 0;
		FSF_Settings::$fsf_view_settings['kb_main_show_search'] = 0;
		
		// Main Page - Products List Settings		
		FSF_Settings::$fsf_view_settings['kb_main_prod_colums'] = 1;
		FSF_Settings::$fsf_view_settings['kb_main_prod_search'] = 1;
		FSF_Settings::$fsf_view_settings['kb_main_prod_pages'] = 0;
		
		// Main Page - Category List Settings
		FSF_Settings::$fsf_view_settings['kb_main_cat_mode'] = 'normal';
		FSF_Settings::$fsf_view_settings['kb_main_cat_arts'] = 'normal';
		FSF_Settings::$fsf_view_settings['kb_main_cat_colums'] = 1;
		
		// When Product Selected
		FSF_Settings::$fsf_view_settings['kb_prod_cat_mode'] = 'accordian';
		FSF_Settings::$fsf_view_settings['kb_prod_cat_arts'] = 'normal';
		FSF_Settings::$fsf_view_settings['kb_prod_cat_colums'] = 1;
		FSF_Settings::$fsf_view_settings['kb_prod_search'] = 1;
		
		// When Product and Category Selected
		FSF_Settings::$fsf_view_settings['kb_cat_cat_mode'] = 'accordian';
		FSF_Settings::$fsf_view_settings['kb_cat_cat_arts'] = 'normal';
		FSF_Settings::$fsf_view_settings['kb_cat_art_pages'] = 0;
		FSF_Settings::$fsf_view_settings['kb_cat_search'] = 0;		
	}
	
	function GetViewSettingsObj($view)
	{
		// return a view setting object that can be used in place of the getPageParameters object
		// needs info about what view we are in, and access to the view settings
		FSF_Settings::_Get_View_Settings();
			
		return new FSF_View_Settings($view, FSF_Settings::$fsf_view_settings);
	}
}

class FSF_View_Settings
{
	var $view;
	var $settings;
	var $mainframe;
	
	function __construct($view, $settings)
	{
		$this->view = $view;
		$this->settings = $settings;
		
		$this->mainframe = JFactory::getApplication();
		$this->params = $this->mainframe->getPageParameters('com_fsf');
		
		//print_p($this->settings);
		//print_p($this->params);
	}
	
	function get($var, $default = '')
	{
		$key = $this->view . "_" . $var;
		
		//echo "Get : $key (Def: $default) = ";

		$value = $this->params->get($var,"XXXXXXXX");
		if ($value != "XXXXXXXX")
		{
			if (!array_key_exists($key, $this->settings))
			{
				//echo $value . " (missing)<br>";
				return $value;
			}
		
			if ($value != -1)
			{
				//echo $value . " (set)<br>";
				return $value;
			}
		}
		
		//echo $this->settings[$key] . " (global)<br>";
		return $this->settings[$key];
	}
}

function FSF_GetAllMenus()
{
	static $getmenus;
	
	if (empty($getmenus))
	{
		$where = array();
		$where[] = 'menutype != "main"';
		$where[] = 'type = "component"';
		$where[] = 'link LIKE "%option=com_fsf%"';
		$where[] = 'published = 1';
		
		if (FSF_Helper::Is16())
		{
			$query = 'SELECT title, id, link FROM #__menu';
		} else {
			$query = 'SELECT name as title, id, link FROM #__menu';
		}
		$query .= ' WHERE ' . implode(" AND ", $where);
		
		$db    = & JFactory::getDBO();
		$db->setQuery($query);
		$getmenus = $db->loadObjectList();
	}
	//print_p($getmenus);
	
	return $getmenus;
}

function FSF_GetMenus($menutype)
{
	$getmenus = FSF_GetAllMenus();
	
	//echo "<br>Menu Type : $menutype<br>-<br>";
	$have = array();
	$not = array();
	
	switch ($menutype)
	{
	case FSF_IT_KB:
		$have['view'] = "kb";
		$not['layout'] = "";
		break;						
	case FSF_IT_FAQ:
		$have['view'] = "faq";
		$not['layout'] = "";
		break;						
	case FSF_IT_TEST:
		$have['view'] = "test";
		$not['layout'] = "";
		break;						
	case FSF_IT_NEWTICKET:
		$have['view'] = "ticket";
		$have['layout'] = "open";
		break;
	case FSF_IT_VIEWTICKETS:
		$have['view'] = "ticket";
		$not['layout'] = "";
		break;						
	case FSF_IT_ANNOUNCE:
		$have['view'] = "announce";
		$not['layout'] = "";
		break;						
	case FSF_IT_GLOSSARY:
		$have['view'] = "glossary";
		$not['layout'] = "";
		break;						
	case FSF_IT_ADMIN:
		$have['view'] = "admin";
		$not['layout'] = "";
		break;						
	default:
		return array();							
	}
	
	$results = array();
	
	if (count($getmenus) > 0)
	{
		foreach ($getmenus as $object)
		{ 
			$linkok = 1;
		
			$link = strtolower(substr($object->link,strpos($object->link,"?")+1));
			//echo $link."<br>";
			$parts = explode("&",$link);
		
			$inlink = array();
		
			foreach($parts as $part)
			{
				list($key,$value) = explode("=",$part);
				$inlink[$key] = $value;
			
				if (array_key_exists($key,$not))
				{
					//echo "Has ".$key."<br>";
					$linkok = 0;
				}
			}
				
			foreach ($have as $key => $value)
			{		
				if (!array_key_exists($key,$inlink))
				{
					//echo "Doesnt have ".$key."<br>";
					$linkok = 0;	
				} else {
					if ($inlink[$key] != $value)
					{
						//echo "Value mismatch for ".$key." - " . $value . " should be " . $inlink[$key] . "<br>";
						$linkok = 0;
					}
				}				
			}
		
			if ($linkok)
			{
				$results[] = $object;
				//echo "VALID : " . $link . "<br>";	
			}	
		}
	}
	
	return $results;
}
