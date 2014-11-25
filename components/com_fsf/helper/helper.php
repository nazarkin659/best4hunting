<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;

global $fsjjversion;
require_once( JPATH_SITE.DS.'components'.DS.'com_fsf'.DS.'helper'.DS.'j3helper.php' );
require_once( JPATH_SITE.DS.'components'.DS.'com_fsf'.DS.'helper'.DS.'settings.php' );
require_once (JPATH_SITE.DS.'components'.DS.'com_fsf'.DS.'helper'.DS.'tickethelper.php');

jimport( 'joomla.utilities.date' );

define('FSF_DATE_SHORT',0);
define('FSF_DATE_MID',1);
define('FSF_DATE_LONG',2);

define('FSF_TIME_SHORT',3);
define('FSF_TIME_LONG',4);

define('FSF_DATETIME_SHORT',5);
define('FSF_DATETIME_MID',6);
define('FSF_DATETIME_LONG',7);

define('FSF_DATETIME_MYSQL',8);

$FSFRoute_menus = array();
global $FSFRoute_menus;

class FSF_Helper
{
	static $jquery_incl = false;
	static function IncludeJQuery($force = false)
	{		
		if (FSF_Helper::$jquery_incl)
			return;
		
		FSF_Helper::$jquery_incl = true;
		
		$document = JFactory::getDocument();
		
		$include = FSF_Settings::get('jquery_include');
		if ($include == "")
			$include = "auto";
		$url = JURI::root().'components/com_fsf/assets/js/jquery.1.8.3.min.js';
		$ncurl = JURI::root().'components/com_fsf/assets/js/jquery.noconflict.js';

		if ($force)
			$include = "yes";

		if ($include == "no")
		{
			
		} else if ($include == "yes")
		{
			if (FSFJ3Helper::IsJ3())
			{
				JHtml::_('jquery.framework');
				$document->addScript( JURI::root().'components/com_fsf/assets/js/main.js' );
				return;	
			}

			$document->addScript( $url );
			$document->addScript( $ncurl );
			
		} else if ($include == "yesnonc") // yes, include it, but not with noconflict
		{
			if (FSFJ3Helper::IsJ3())
			{
				JHtml::_('jquery.framework');
				$document->addScript( JURI::root().'components/com_fsf/assets/js/main.js' );
				return;	
			}

			$document->addScript( $url );
			
			//$document->addScript( $ncurl );
		} else /*if ($include == "auto")*/ // auto detect mode
		{
		
			if (FSFJ3Helper::IsJ3())
			{
				JHtml::_('jquery.framework');
				$document->addScript( JURI::root().'components/com_fsf/assets/js/main.js' );
				return;	
			}
		
			$found = false;
			
			foreach ($document->_scripts as $jsurl => $script)
			{
				if (strpos(strtolower($jsurl), "jquery") > 0)
				{
					$found = true;
					break;
				}
			}
			
			if (!$found)
			{
				$document->addScript( $url );
				$document->addScript( $ncurl );
			}
		}
		
		$document->addScript( JURI::root().'components/com_fsf/assets/js/main.js' );
	}
	
	static function IsTests()
	{
		if (JRequest::getVar('option') == "com_fst")
			return true;
		return false;	
	}
	
	static function GetRouteMenus()
	{
		global $FSFRoute_menus;

		if (empty($FSFRoute_menus))
		{
			$FSFRoute_menus = array();
			$db = JFactory::getDBO();
			$qry = "SELECT id, link FROM #__menu WHERE link LIKE '%option=com_fsf%' AND published = 1";
			
			if (FSF_Helper::Is16())
				$qry .= ' AND language in (' . $db->Quote(JFactory::getLanguage()->getTag()) . ',' . $db->Quote('*') . ')';
			
			$db->setQuery($qry);
			$menus = $db->loadObjectList('id');
			foreach($menus as $menu)
			{
				$FSFRoute_menus[$menu->id] = FSFRoute::SplitURL($menu->link);
			}
		}
	}
	
	static function GetBaseURL() 
	{
		$uri =& JURI::getInstance();
		return $uri->toString( array('scheme', 'host', 'port'));
	}
		
	static function isValidURL($url)
	{
		return preg_match('|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i', $url);
	}

	static function Is16()
	{
		global $fsjjversion;
		if (empty($fsjjversion))
		{
			$version = new JVersion;
			$fsjjversion = 1;
			if ($version->RELEASE == "1.5")
				$fsjjversion = 0;
		}
		return $fsjjversion;
	}

	static function PageStyle()
	{
		echo "<style>\n";
		echo FSF_Settings::get('display_style');
		echo "</style>\n";
		echo FSF_Settings::get('display_head');
		$class = '';
		if (FSFJ3Helper::IsJ3())
			$class = "fsf_main_j3";
		echo "<div class='fsf_main $class'>\n";
	}

	static function PageStyleEnd()
	{
		echo "</div>\n";
		echo FSF_Settings::get('display_foot');
	}

	static function PageStylePopup()
	{
		echo "<style>\n";
		echo FSF_Settings::get('display_popup_style');
		echo "</style>\n";
		echo "<div class='fsf_popup'>\n";
	}

	static function PageStylePopupEnd()
	{
		echo "</div>\n";
	}

	static function PageTitlePopup($title,$subtitle = "")
	{
		return FSF_Helper::PageTitle($title, $subtitle, 'display_popup');
	}

	static function TitleString($title,$subtitle)
	{
		if (!FSF_Settings::get('title_prefix'))
		{
			if ($subtitle)
				return $subtitle;
			
			return $title;
		} else {		
			if ($subtitle)
				return JText::sprintf('FSF_PAGE_HEAD', $title, $subtitle);
			
			return $title;
		}
	}
		
	static function PageTitle($title,$subtitle = "",$template = 'display_h1')
	{
		//echo "Page Title : $title - $subtitle<br>";
		$title = JText::_($title);
		$subtitle = JText::_($subtitle);
		$mainframe = JFactory::getApplication();
		$pageparams = $mainframe->getPageParameters('com_fsf');			
		
		
		$document = JFactory::getDocument();
		if (FSF_Helper::Is16())
		{
			$ptitle = $pageparams->get('page_title', $title);
			$browsertitle = FSF_Helper::TitleString($ptitle, $subtitle);
			if ($mainframe->getCfg('sitename_pagetitles', 0) == 1) {
				$browsertitle = JText::sprintf('JPAGETITLE', $mainframe->getCfg('sitename'), $browsertitle);
			}
			elseif ($mainframe->getCfg('sitename_pagetitles', 0) == 2) {
				$browsertitle = JText::sprintf('JPAGETITLE', $browsertitle, $mainframe->getCfg('sitename'));
			}
			$document->setTitle($browsertitle);
		} else{
			$ptitle = $pageparams->get('page_title',$title);
			$document->setTitle(FSF_Helper::TitleString($ptitle, $subtitle));
		}

		if (FSF_Settings::get('use_joomla_page_title_setting'))
		{

			$show_title = 1;
			//print_p($pageparams);
			
			if (FSF_Helper::Is16())
			{
				// in j1.6/7 can override both browser title, and
				// page title, and optionally show heading
				if ($pageparams)
					$show_title = $pageparams->get('show_page_heading',1);
				$title = $pageparams->get('page_heading',$title);
				if ($show_title)
					return str_replace("$1",FSF_Helper::TitleString($title, $subtitle),FSF_Settings::get($template));
			
				return "";
			} else {
				if ($pageparams)
					$show_title = $pageparams->get('show_page_title',1);
				$title = $pageparams->get('page_title',$title);
				if ($show_title)
					return str_replace("$1",FSF_Helper::TitleString($title, $subtitle),FSF_Settings::get($template));
			
				return "";
			}
			
			
		} else {
			return str_replace("$1",FSF_Helper::TitleString($title, $subtitle),FSF_Settings::get($template));
		}
	}

	static function PageSubTitle($title,$usejtext = true)
	{
		if ($usejtext)
			$title = JText::_($title);
	
		return str_replace("$1",$title,FSF_Settings::get('display_h2'));
	}

	static function PageSubTitle2($title,$usejtext = true)
	{
		if ($usejtext)
			$title = JText::_($title);
	
		return str_replace("$1",$title,FSF_Settings::get('display_h3'));
	}


	static function Date($date,$format = FSF_DATE_LONG)
	{
		if (FSF_Helper::Is16())
		{
			/*$setting = FSF_Settings::get('datetime_'.$format);
			if ($setting)
			{
				$ft = $setting;
			} else {*/
			switch($format)
			{
				case FSF_DATE_SHORT:	
					$ft = JText::_('DATE_FORMAT_LC4');
					break;
				case FSF_DATE_MID:	
					$ft = JText::_('DATE_FORMAT_LC3');
					break;
				case FSF_DATE_LONG:	
					$ft = JText::_('DATE_FORMAT_LC1');
					break;
				case FSF_TIME_SHORT:	
					$ft = 'H:i';
					break;
				case FSF_TIME_LONG:	
					$ft = 'H:i:s';
					break;
				case FSF_DATETIME_SHORT:	
					$ft = JText::_('DATE_FORMAT_LC4') . ', H:i';
					break;
				case FSF_DATETIME_MID:	
					$ft = JText::_('DATE_FORMAT_LC3') . ', H:i';
					break;
				case FSF_DATETIME_LONG:	
					$ft = JText::_('DATE_FORMAT_LC1') . ', H:i';
					break;
				case FSF_DATETIME_MYSQL:	
					$ft = 'Y-m-d H:i:s';
					break;
				default:
					$ft = JText::_('DATE_FORMAT_LC');
			}
		
			if ($format == FSF_DATETIME_SHORT && FSF_Settings::Get('date_dt_short') != "")
				$ft = FSF_Settings::Get('date_dt_short');
		
			if ($format == FSF_DATETIME_MID && FSF_Settings::Get('date_dt_long') != "")
				$ft = FSF_Settings::Get('date_dt_long');
		
			if ($format == FSF_DATE_SHORT && FSF_Settings::Get('date_d_short') != "")
				$ft = FSF_Settings::Get('date_d_short');
		
			if ($format == FSF_DATE_MID && FSF_Settings::Get('date_d_long') != "")
				$ft = FSF_Settings::Get('date_d_long');
	
		
			$date = new JDate($date, new DateTimeZone("UTC"));
			$date->setTimezone(FSF_Helper::getTimezone());
			return $date->format($ft, true);
		} else {
			/*$setting = FSF_Settings::get('datetime_'.$format);
			if ($setting)
			{
				$ft = $setting;
			} else {*/
				switch($format)
				{
				case FSF_DATE_SHORT:	
					$ft = JText::_('DATE_FORMAT_LC4');
					break;
				case FSF_DATE_MID:	
					$ft = JText::_('DATE_FORMAT_LC3');
					break;
				case FSF_DATE_LONG:	
					$ft = JText::_('DATE_FORMAT_LC1');
					break;
				case FSF_TIME_SHORT:	
					$ft = '%H:%M';
					break;
				case FSF_TIME_LONG:	
					$ft = '%H:%M:%S';
					break;
				case FSF_DATETIME_SHORT:	
					$ft = JText::_('DATE_FORMAT_LC4') . ', %H:%M';
					break;
				case FSF_DATETIME_MID:	
					$ft = JText::_('DATE_FORMAT_LC3') . ', %H:%M';
					break;
				case FSF_DATETIME_LONG:	
					$ft = JText::_('DATE_FORMAT_LC1') . ', %H:%M';
					break;
				default:
					$ft = JText::_('DATE_FORMAT_LC');
				}
			//}
			//echo "Format : $ft, Requested: $format<br>";
			$date = new JDate($date);
			return $date->toFormat($ft);
		}
		return $date;	
	}
	
	static function getTimeZone() {
		$userTz = JFactory::getUser()->getParam('timezone');
		if (FSFJ3Helper::IsJ3())
		{
			$timeZone = JFactory::getConfig()->get('offset');
		} else {
			$timeZone = JFactory::getConfig()->getValue('offset');
		}
		if($userTz) {
			$timeZone = $userTz;
		}
		if ((int)$timeZone == $timeZone && is_numeric($timeZone))
		{
			$timeZone += FSF_Settings::Get('timezone_offset');
			
			$offset = $timeZone * 3600;
			$tz = DateTimeZone::listAbbreviations();
			if (count($tz) > 0)
			{
				foreach ($tz as $zone => $things)
				{
					if ($things[0]['offset'] == $offset)
					{
						$timeZone = $things[0]['timezone_id'];
						break;	
					}
				}
			}
		}
		if ((string)$timeZone == "" || (string)$timeZone == "0") $timeZone = "UTC";
		return new DateTimeZone($timeZone);
	}

	static function CurDate()
	{
		if (FSF_Helper::Is16())
		{
			$myTimezone = FSF_Helper::getTimezone();
			$myDate = null;
			$date = new JDate($myDate, $myTimezone);
			$formatted = $date->format('Y-m-d H:i:s', false, false);
			
			return $formatted;
		} else {
			$myTimezone = FSF_Helper::getTimezone();
			$date = null;
			$date = new JDate($date, $myTimezone);
			if (FSFJ3Helper::IsJ3())
			{
				return $date->toSql();
			} else { 
				return $date->toMySQL();
			}
		}	
	}

	static function ToJText($string)
	{
		return strtoupper(str_replace(" ","_",$string));	
	}

	static function escapeJavaScriptText($string)
	{
		return str_replace("\n", '\n', str_replace('"', '\"', addcslashes(str_replace("\r", '', (string)$string), "\0..\37'\\")));
	}
	
	static function escapeJavaScriptTextForAlert($string)
	{
		if (function_exists("mb_convert_encoding"))
			return mb_convert_encoding(FSF_Helper::escapeJavaScriptText($string), 'UTF-8', 'HTML-ENTITIES');
		
		return FSF_Helper::escapeJavaScriptText($string);
	}

	/*$FSFRoute_debug = array();
	global $FSFRoute_debug;*/

	static function display_filesize($filesize){
   
		if (stripos($filesize,"k") > 0)
			$filesize = $filesize * 1024;
		if (stripos($filesize,"m") > 0)
			$filesize = $filesize * 1024 * 1024;
		if (stripos($filesize,"g") > 0)
			$filesize = $filesize * 1024 * 1024;
		$filesize = $filesize * 1;
	
		if(is_numeric($filesize)){
			$decr = 1024; $step = 0;
			$prefix = array('Byte','KB','MB','GB','TB','PB');
		   
			while(($filesize / $decr) > 0.9){
				$filesize = $filesize / $decr;
				$step++;
			}
			return round($filesize,2).' '.$prefix[$step];
		} else {
			return 'NaN';
		}
	}

	static function escape($in)
	{
		return htmlspecialchars($in, ENT_COMPAT);
	}

	static function encode($in)
	{
		$out = $in;
		//$out = str_replace("'","&apos;",$out);
		//$out = str_replace('&#039;','&apos;',$out);
		$out = htmlspecialchars($out,ENT_QUOTES);
		//$out = htmlentities($out,ENT_COMPAT);
	
		return $out;		
	}

	static function createRandomPassword() {
		$chars = "abcdefghijkmnopqrstuvwxyz023456789";
		srand((double)microtime()*1000000);
		$i = 0;
		$pass = '' ;

		while ($i <= 7) {
			$num = rand() % 33;
			$tmp = substr($chars, $num, 1);
			$pass = $pass . $tmp;
			$i++;
		}
		return $pass;
	}

	static function datei_mime($filetype) {
	
		switch ($filetype) {
			case "ez":  $mime="application/andrew-inset"; break;
			case "hqx": $mime="application/mac-binhex40"; break;
			case "cpt": $mime="application/mac-compactpro"; break;
			case "doc": $mime="application/msword"; break;
			case "bin": $mime="application/octet-stream"; break;
			case "dms": $mime="application/octet-stream"; break;
			case "lha": $mime="application/octet-stream"; break;
			case "lzh": $mime="application/octet-stream"; break;
			case "exe": $mime="application/octet-stream"; break;
			case "class": $mime="application/octet-stream"; break;
			case "dll": $mime="application/octet-stream"; break;
			case "oda": $mime="application/oda"; break;
			case "pdf": $mime="application/pdf"; break;
			case "ai":  $mime="application/postscript"; break;
			case "eps": $mime="application/postscript"; break;
			case "ps":  $mime="application/postscript"; break;
			case "xls": $mime="application/vnd.ms-excel"; break;
			case "ppt": $mime="application/vnd.ms-powerpoint"; break;
			case "wbxml": $mime="application/vnd.wap.wbxml"; break;
			case "wmlc": $mime="application/vnd.wap.wmlc"; break;
			case "wmlsc": $mime="application/vnd.wap.wmlscriptc"; break;
			case "vcd": $mime="application/x-cdlink"; break;
			case "pgn": $mime="application/x-chess-pgn"; break;
			case "csh": $mime="application/x-csh"; break;
			case "dvi": $mime="application/x-dvi"; break;
			case "spl": $mime="application/x-futuresplash"; break;
			case "gtar": $mime="application/x-gtar"; break;
			case "hdf": $mime="application/x-hdf"; break;
			case "js":  $mime="application/x-javascript"; break;
			case "nc":  $mime="application/x-netcdf"; break;
			case "cdf": $mime="application/x-netcdf"; break;
			case "swf": $mime="application/x-shockwave-flash"; break;
			case "tar": $mime="application/x-tar"; break;
			case "tcl": $mime="application/x-tcl"; break;
			case "tex": $mime="application/x-tex"; break;
			case "texinfo": $mime="application/x-texinfo"; break;
			case "texi": $mime="application/x-texinfo"; break;
			case "t":   $mime="application/x-troff"; break;
			case "tr":  $mime="application/x-troff"; break;
			case "roff": $mime="application/x-troff"; break;
			case "man": $mime="application/x-troff-man"; break;
			case "me":  $mime="application/x-troff-me"; break;
			case "ms":  $mime="application/x-troff-ms"; break;
			case "ustar": $mime="application/x-ustar"; break;
			case "src": $mime="application/x-wais-source"; break;
			case "zip": $mime="application/zip"; break;
			case "au":  $mime="audio/basic"; break;
			case "snd": $mime="audio/basic"; break;
			case "mid": $mime="audio/midi"; break;
			case "midi": $mime="audio/midi"; break;
			case "kar": $mime="audio/midi"; break;
			case "mpga": $mime="audio/mpeg"; break;
			case "mp2": $mime="audio/mpeg"; break;
			case "mp3": $mime="audio/mpeg"; break;
			case "aif": $mime="audio/x-aiff"; break;
			case "aiff": $mime="audio/x-aiff"; break;
			case "aifc": $mime="audio/x-aiff"; break;
			case "m3u": $mime="audio/x-mpegurl"; break;
			case "ram": $mime="audio/x-pn-realaudio"; break;
			case "rm":  $mime="audio/x-pn-realaudio"; break;
			case "rpm": $mime="audio/x-pn-realaudio-plugin"; break;
			case "ra":  $mime="audio/x-realaudio"; break;
			case "wav": $mime="audio/x-wav"; break;
			case "pdb": $mime="chemical/x-pdb"; break;
			case "xyz": $mime="chemical/x-xyz"; break;
			case "bmp": $mime="image/bmp"; break;
			case "gif": $mime="image/gif"; break;
			case "ief": $mime="image/ief"; break;
			case "jpeg": $mime="image/jpeg"; break;
			case "jpg": $mime="image/jpeg"; break;
			case "jpe": $mime="image/jpeg"; break;
			case "png": $mime="image/png"; break;
			case "tiff": $mime="image/tiff"; break;
			case "tif": $mime="image/tiff"; break;
			case "wbmp": $mime="image/vnd.wap.wbmp"; break;
			case "ras": $mime="image/x-cmu-raster"; break;
			case "pnm": $mime="image/x-portable-anymap"; break;
			case "pbm": $mime="image/x-portable-bitmap"; break;
			case "pgm": $mime="image/x-portable-graymap"; break;
			case "ppm": $mime="image/x-portable-pixmap"; break;
			case "rgb": $mime="image/x-rgb"; break;
			case "xbm": $mime="image/x-xbitmap"; break;
			case "xpm": $mime="image/x-xpixmap"; break;
			case "xwd": $mime="image/x-xwindowdump"; break;
			case "msh": $mime="model/mesh"; break;
			case "mesh": $mime="model/mesh"; break;
			case "silo": $mime="model/mesh"; break;
			case "wrl": $mime="model/vrml"; break;
			case "vrml": $mime="model/vrml"; break;
			case "css": $mime="text/css"; break;
			case "asc": $mime="text/plain"; break;
			case "txt": $mime="text/plain"; break;
			case "gpg": $mime="text/plain"; break;
			case "rtx": $mime="text/richtext"; break;
			case "rtf": $mime="text/rtf"; break;
			case "wml": $mime="text/vnd.wap.wml"; break;
			case "wmls": $mime="text/vnd.wap.wmlscript"; break;
			case "etx": $mime="text/x-setext"; break;
			case "xsl": $mime="text/xml"; break;
			case "flv": $mime="video/x-flv"; break;
			case "mpeg": $mime="video/mpeg"; break;
			case "mpg": $mime="video/mpeg"; break;
			case "mpe": $mime="video/mpeg"; break;
			case "qt":  $mime="video/quicktime"; break;
			case "mov": $mime="video/quicktime"; break;
			case "mxu": $mime="video/vnd.mpegurl"; break;
			case "avi": $mime="video/x-msvideo"; break;
			case "movie": $mime="video/x-sgi-movie"; break;
			case "asf": $mime="video/x-ms-asf"; break;
			case "asx": $mime="video/x-ms-asf"; break;
			case "wm":  $mime="video/x-ms-wm"; break;
			case "wmv": $mime="video/x-ms-wmv"; break;
			case "wvx": $mime="video/x-ms-wvx"; break;
			case "ice": $mime="x-conference/x-cooltalk"; break;
			case "rar": $mime="application/x-rar"; break;
			default:    $mime="application/octet-stream"; break; 
		}
		return $mime;
	}
	
	/*
	static $_permissions;
	static $_permissions_all = false;
	static $_perm_only;
	static $_perm_prods;	
	static $_perm_depts;
	static $_perm_cats;	
	static $_perm_where;	
	*/
	//static $order = 0;
	
	/*static function getPermissions($all = true)
	{
		//FSF_Helper::$order++;
		if ($all)
		{
			//echo "<h1>Getting Permissions ALL ".FSF_Helper::$order."</h1>";
			if (!FSF_Helper::$_permissions_all)
			{
				//echo "<h1>RESET</h1>";
				FSF_Helper::$_permissions = null;
			}
			FSF_Helper::$_permissions_all = true;
		} else {
			//echo "<h1>Getting Permissions Partial ".FSF_Helper::$order."</h1>";
		}
	
		if (empty(FSF_Helper::$_permissions)) {
			$mainframe = JFactory::getApplication(); global $option;
			$user = JFactory::getUser();
			$userid = $user->id;
			
			$db = JFactory::getDBO();
			$query = "SELECT * FROM #__fsf_user WHERE user_id = '".FSFJ3Helper::getEscaped($db, $userid)."'";
			$db->setQuery($query);
			FSF_Helper::$_permissions = $db->loadAssoc();

			if (!FSF_Helper::$_permissions)
			{
				FSF_Helper::$_permissions['mod_kb'] = 0;
				FSF_Helper::$_permissions['mod_test'] = 0;
				FSF_Helper::$_permissions['support'] = 0;
				FSF_Helper::$_permissions['seeownonly'] = 1;
				FSF_Helper::$_permissions['autoassignexc'] = 1;
				FSF_Helper::$_permissions['allprods'] = 1;
				FSF_Helper::$_permissions['allcats'] = 1;
				FSF_Helper::$_permissions['alldepts'] = 1;
				FSF_Helper::$_permissions['artperm'] = 0;
				FSF_Helper::$_permissions['id'] = 0;
				FSF_Helper::$_permissions['groups'] = 0;
			}
			FSF_Helper::$_permissions['userid'] = $userid;
			
			FSF_Helper::$_perm_only = '';
			FSF_Helper::$_perm_prods = '';	
			FSF_Helper::$_perm_depts = '';
			FSF_Helper::$_perm_cats = '';	
			FSF_Helper::$_permissions['perm_where'] = '';
			
// 

			// check for permission overrides for Joomla 1.6
			if (FSF_Settings::get('perm_article_joomla') || FSF_Settings::get('perm_mod_joomla'))
			{
				if (FSF_Helper::Is16())
				{
					$newart = 0;
					$newmod = 0;
					$user = JFactory::getUser();
					if ($user->authorise('core.edit.own','com_fsf'))
					{
						$newart = 1;
					}
					if ($user->authorise('core.edit','com_fsf'))
					{
						$newart = 2;
						$newmod = 1;
					}
					if ($user->authorise('core.edit.state','com_fsf'))
					{
						$newart = 3;	
						$newmod = 1;
					}
						
					if (FSF_Settings::get('perm_article_joomla') && $newart > FSF_Helper::$_permissions['artperm'])
						FSF_Helper::$_permissions['artperm'] = $newart;
					if (FSF_Settings::get('perm_mod_joomla') && $newmod > FSF_Helper::$_permissions['mod_kb'])
						FSF_Helper::$_permissions['mod_kb'] = $newmod;
					//
				} else {
					$newart = 0;
					$newmod = 0;
					$user = JFactory::getUser();
					if ($user->authorize('com_fsf', 'create', 'content', 'own'))
					{
						$newart = 1;
					}
					if ($user->authorize('com_fsf', 'edit', 'content', 'own'))
					{
						$newart = 2;
						$newmod = 1;
					}
					if ($user->authorize('com_fsf', 'publish', 'content', 'all'))
					{
						$newart = 3;
						$newmod = 1;
					}
						
					if (FSF_Settings::get('perm_article_joomla') && $newart > FSF_Helper::$_permissions['artperm'])
						FSF_Helper::$_permissions['artperm'] = $newart;
					if (FSF_Settings::get('perm_mod_joomla') && $newmod > FSF_Helper::$_permissions['mod_kb'])
						FSF_Helper::$_permissions['mod_kb'] = $newmod;
				}
			}
		}
		
		
		return FSF_Helper::$_permissions;			
	}*/
	
	static $user_defaults;
	static function getUserSetting($setting)
	{
		if (empty(FSF_Helper::$_permissions))
			FSF_Ticket_Helper::getAdminPermissions();
			
		if (empty(FSF_Helper::$user_defaults))
			FSF_Helper::getUserDefaults();
		
		if (array_key_exists('settings',FSF_Ticket_Helper::$_permissions) && is_array(FSF_Ticket_Helper::$_permissions['settings']) &&
			array_key_exists($setting,FSF_Ticket_Helper::$_permissions['settings']))
		{
			return FSF_Ticket_Helper::$_permissions['settings'][$setting];
		}
			
		if (array_key_exists($setting,FSF_Helper::$user_defaults))
			return FSF_Helper::$user_defaults[$setting];
			
		return 0;
	}
	
	static function getUserDefaults()
	{
		if (empty(FSF_Helper::$user_defaults))
		{
			FSF_Helper::$user_defaults = array();
			FSF_Helper::$user_defaults['per_page'] = 10;
			FSF_Helper::$user_defaults['group_products'] = 0;
			FSF_Helper::$user_defaults['group_departments'] = 0;
			FSF_Helper::$user_defaults['group_cats'] = 0;
			FSF_Helper::$user_defaults['group_group'] = 0;
			FSF_Helper::$user_defaults['group_pri'] = 0;
			FSF_Helper::$user_defaults['return_on_reply'] = 0;
			FSF_Helper::$user_defaults['return_on_close'] = 0;
			FSF_Helper::$user_defaults['reverse_order'] = 0;
		}
		return FSF_Helper::$user_defaults;
	}
	
	static function NeedBaseBreadcrumb($pathway, $aparams)
	{
		global $FSFRoute_menus;
		// need to determine if a base pathway item needs adding or not
		
		// get any menu items for fsf
		FSF_Helper::GetRouteMenus();

		$lastpath = $pathway->getPathway();
		// no pathway, so must have to add
		if (count($lastpath) == 0)
			return true;
			
		$lastpath = $lastpath[count($lastpath)-1];
		$link = $lastpath->link;
		
		$parts = FSFRoute::SplitURL($link);
		
		if (!array_key_exists('Itemid', $parts))
			return true;
			
		//print_p($parts);
		if (!array_key_exists($parts['Itemid'],$FSFRoute_menus))
		{
			//echo "Item ID not found<br>";
			return true;		
		}
		
		$ok = true;
		
		/*foreach($FSFRoute_menus[$parts['Itemid']] as $key => $value)
		{
			if ($value != "")
			{
				if (!array_key_exists($key,$aparams))
				{
					$ok = false;
					break;
				}
			
				if ($aparams[$key] != $value)
				{
					$ok = false;
					break;		
				}
			}
		}*/
		
		foreach($aparams as $key => $value)
		{
			if ($value != "")
			{
				if (!array_key_exists($key,$FSFRoute_menus[$parts['Itemid']]))
				{
					$ok = false;
					break;
				}
			
				if ($FSFRoute_menus[$parts['Itemid']][$key] != $value)
				{
					$ok = false;
					break;		
				}
			}
		}
		
		if ($ok)
			return false;
		/*print_p($aparams);
		print_p($FSFRoute_menus[$parts['Itemid']]);*/
		
		return true;	
	}
	
	
	static function GetPublishedText($ispub,$notip = false)
	{
		$img = 'save_16.png';
		$alt = JText::_("PUBLISHED");

		if ($ispub == 0)
		{
			$img = 'cancel_16.png';
			$alt = JText::_("UNPUBLISHED");
		}
	
		if ($notip)
			return '<img src="components/com_fsf/assets/images/' . $img . '" width="16" height="16" border="0" alt="' . $alt .'" />';	
			
		return '<img class="fsj_tip" src="components/com_fsf/assets/images/' . $img . '" width="16" height="16" border="0" alt="' . $alt .'" title="'.$alt.'" />';	

	}

	static function GetYesNoText($ispub)
	{
		$img = 'tick.png';
		$alt = JText::_("YES");

		if ($ispub == 0)
		{
			$img = 'cross.png';
			$alt = JText::_("NO");
		}
		$src = JURI::base() . "/components/com_fsf/assets/images";
		return '<img src="' . $src . '/' . $img . '" width="16" height="16" border="0" alt="' . $alt .'" />';	
	}

	static function ShowError(&$errors, $key)
	{
		if (empty($errors))
			return "";
			
		if (!array_key_exists($key, $errors))
			return "";
			
		if ($errors[$key] == "")	
			return "";
		
		return "<div class='fsf_ticket_error'>" . $errors[$key] . "</div>";
	}
	
	static function sort($title, $order, $direction = 'asc', $selected = 0, $task = null, $new_direction = 'asc')
	{
		$direction = strtolower($direction);
		$images = array('sort_asc.png', 'sort_desc.png');
		$index = intval($direction == 'desc');

		if ($order != $selected)
		{
			$direction = $new_direction;
		}
		else
		{
			$direction = ($direction == 'desc') ? 'asc' : 'desc';
		}

		$html = '<a href="#" onclick="Joomla.tableOrdering(\'' . $order . '\',\'' . $direction . '\',\'' . $task . '\');return false;" title="'
			. JText::_('JGLOBAL_CLICK_TO_SORT_THIS_COLUMN') . '">';
		$html .= JText::_($title);

		if ($order == $selected)
		{
			$html .= JHtml::_('image', 'system/' . $images[$index], '', null, true);
		}

		$html .= '</a>';

		return $html;
	}

	static function TrF($field, $current, $trdata)
	{
		$data = json_decode($trdata, true);
			
		if (!is_array($data))
			return $current;
		
		if (!array_key_exists($field, $data))
			return $current;
		
		$curlang = str_replace("-","",JFactory::getLanguage()->getTag());
		
		if (!array_key_exists($curlang, $data[$field]))
			return $current;
		
		return $data[$field][$curlang];	
	}
	
	static function Tr(&$data)
	{
		foreach ($data as &$item)
		{
			if (is_array($item))
				FSF_Helper::TrA($item);		
			if (is_object($item))
				FSF_Helper::TrO($item);	
		}
		return;	
	}
	
	static function TrSingle(&$data)
	{
		if (is_array($data))
			FSF_Helper::TrA($data);		
		if (is_object($data))
			FSF_Helper::TrO($data);	
		return;	
	}
	
	static function TrA(&$data)
	{
		// translate all fields in data that are found in the translation field
		$curlang = str_replace("-","",JFactory::getLanguage()->getTag());
		
		if (!array_key_exists("translation", $data))
			return;
		
		$translation = json_decode($data['translation'], true);
		if (!$translation)
			return;
		
		foreach ($translation as $field => $langs)
		{
			foreach ($langs as $lang => $text)
			{
				if ($lang == $curlang)
					$data[$field] = $text;
			}
		}
	}	
	
	static function TrO(&$data)
	{
		// translate all fields in data that are found in the translation field
		$curlang = str_replace("-","",JFactory::getLanguage()->getTag());
		
		if (!property_exists($data, "translation"))
			return;
		
		$translation = json_decode($data->translation, true);
		if (!$translation)
			return;
		
		foreach ($translation as $field => $langs)
		{
			foreach ($langs as $lang => $text)
			{
				if ($lang == $curlang)
					$data->$field = $text;
			}
		}
	}
	
	static $sceditor = false;
	static function AddSCEditor()
	{
		if (!FSF_Helper::$sceditor)
		{
			if (FSF_Settings::Get('support_sceditor'))
			{
				$document = JFactory::getDocument();
				$document->addScript(JURI::root().'components/com_fsf/assets/js/sceditor/jquery.sceditor.bbcode.js'); 
				$document->addScript(JURI::root().'components/com_fsf/assets/js/sceditor/include.sceditor.js'); 
				$document->addScriptDeclaration("var sceditor_emoticons_root = '" . JURI::root( true ) . "/components/com_fsf/assets/';");
				$document->addScriptDeclaration("var sceditor_style_root = '" . JURI::root( true ) . "/components/com_fsf/assets/js/sceditor/';");
				$document->addStyleSheet(JURI::root().'components/com_fsf/assets/js/sceditor/themes/default.css'); 
			}
			FSF_Helper::$sceditor = true;
		}
	}
	
	static $bbcode_loaded = false;
	
	static function ParseBBCode($text)
	{
		require_once(JPATH_SITE.DS.'components'.DS.'com_fsf'.DS.'helper'.DS.'bbcode.php');
		$output = bbcode::tohtml($text);
		return "<div class='bbcode'>$output</div>";
	}
	
	static function base64url_decode($data)
	{
		return base64_decode(str_pad(strtr($data, '-_.', '+/='), strlen($data) % 4, '=', STR_PAD_RIGHT));
	}
}

class FSFRoute
{
	static function _createURI($router,$url)
	{
		// Create full URL if we are only appending variables to it
		if (substr($url, 0, 1) == '&') {
			$vars = array();
			if (strpos($url, '&amp;') !== false) {
				$url = str_replace('&amp;','&',$url);
			}

			parse_str($url, $vars);

			$vars = array_merge($router->getVars(), $vars);

			foreach($vars as $key => $var) {
				if ($var == "") {
					unset($vars[$key]);
				}
			}

			$url = 'index.php?'.JURI::buildQuery($vars);
		}

		// Decompose link into url component parts
		return new JURI($url);
	}
	
	static function _16($url, $xhtml, $ssl)
	{
		global $FSFRoute_debug;
		global $FSFRoute_menus;
		
		// get any menu items for fsf
		FSF_Helper::GetRouteMenus();

		//$FSFRoute_debug[] = "<h1>Start URL : $url</h1>";

		// Get the router
		$app	= JFactory::getApplication();
		$router = $app->getRouter();

		// Make sure that we have our router
		if (! $router) {
			return null;
		}

		if ( (strpos($url, '&') !== 0 ) && (strpos($url, 'index.php') !== 0) ) {
			return $url;
		}

		/* Into JRouter::build */

		//Create the URI object
		$uri = FSFRoute::_createURI($router,$url);

		//Process the uri information based on custom defined rules
		//$router->_processBuildRules($uri);

		// Build RAW URL
		/*if($router->getMode() == JROUTER_MODE_RAW) {
			$router->_buildRawRoute($uri);
		}*/

		/* Custom part of JRouter::build */

		// split out parts of the url for
		//$parts = FSFRoute::SplitURL($menu->link);
		//$FSFRoute_debug[] = "URI : <pre>" . print_r($uri,true). "</pre>";

		// work out is we are in an Itemid already, if so, set it as the best match
		if ($uri->hasVar('Itemid'))
		{
			$bestmatch = $uri->getVar('Itemid');
		} else {
			$bestmatch = '';	
		}
		$bestcount = 0;
		
		$uriquery = $uri->toString(array('query'));
		
		$urivars = FSFRoute::SplitURL($uriquery);
		// find all the vars in the new url
		$sourcevars = FSFRoute::SplitURL($url);
		//$addedvars = array();
		//$FSFRoute_debug[] = "Source Vars from calling url, to save and emptied fields : <pre>" . print_r($sourcevars,true). "</pre>";
		//$FSFRoute_debug[] = "Initial URI Vars : <pre>" . print_r($urivars,true). "</pre>";

		// check through the menu item for the current url, and add any items to the new url that are missing
		if ($bestmatch && array_key_exists($bestmatch,$FSFRoute_menus))
		{
			foreach($FSFRoute_menus[$bestmatch] as $key => $value)
			{
				if (!array_key_exists($key,$urivars) && !array_key_exists($key,$sourcevars))
				{
					//$FSFRoute_debug[] = "<span style='color:red; font-size:150%'>Adding source var $key => $value</span><br>";
					$urivars[$key] = $value;
					//$addedvars[$key] = $value;
				}
			}
		}

		//$FSFRoute_debug[] = "Source Vars Added : <pre>" . print_r($addedvars,true). "</pre>";
		//$FSFRoute_debug[] = "URI Vars after adding any missing bits : <pre>" . print_r($urivars,true). "</pre>";

		foreach($FSFRoute_menus as $id => $vars)
		{
			$count = FSFRoute::MatchVars($urivars,$vars);
			//$FSFRoute_debug[] = "Match against $id => $count<br>";
			if ($count > $bestcount)
			{
				$bestcount = $count;
				$bestmatch = $id;	
			}
		}
		
		// if no match found, and we are in groups, try to link to admin
		if ($bestcount == 0 && array_key_exists('view',$sourcevars) && $sourcevars['view'] == "groups")
		{
			// no match found, try to fallback on the main support menu id
			foreach($FSFRoute_menus as $id => $item)
			{
			
				if ($item['view'] == "admin" && (!array_key_exists('layout',$item) || $item['layout'] == "default"))
				{
					$bestcount = 1;
					$bestmatch = $id;					
				}
			}
		}
		
		if ($bestcount == 0)
		{
			// no match found, try to fallback on the main support menu id
			foreach($FSFRoute_menus as $id => $item)
			{
				if ($item['view'] == "main")
				{
					$bestcount = 1;
					$bestmatch = $id;					
				}
			}
		}
		
		if ($bestcount == 0)
		{
			// still no match found, use any fsf menu
			if (count($FSFRoute_menus) > 0)
			{
				foreach($FSFRoute_menus as $id => $item)
				{
					$bestcount = 1;
					$bestmatch = $id;					
					break;
				}				
			}
		}

		if ($bestcount > 0)
		{
			//$FSFRoute_debug[] = "Best Match $bestmatch => $bestcount<br>";
			$uri->setVar('Itemid',$bestmatch);
			
			/*foreach($addedvars as $key => $value)
				unset($uri->_vars[$key]);*/

			if ($bestmatch && array_key_exists($bestmatch,$FSFRoute_menus))
			{
				foreach($FSFRoute_menus[$bestmatch] as $key => $value)
				{
					if ($uri->hasVar($key) && $uri->getVar($key) == $value)
					{
						if ($router->getMode() == JROUTER_MODE_SEF)
						{
							//$FSFRoute_debug[] = "<span style='color:red; font-size:150%'>Removing var $key, its part of the menu definition</span><br>";
							$uri->delVar($key);
						}
					}
				}
			}
		} else {
			//echo "No Match found, leaving as is - $url<br>";
		}

		/* End custom part */
		
		// Build SEF URL : mysite/route/index.php?var=x
		//$FSFRoute_debug[] = "Pre SEF URL : {$uri->toString(array('path', 'query', 'fragment'))}<Br>";
		if ($router->getMode() == JROUTER_MODE_SEF) {
			FSFRoute::_buildSefRoute($router, $uri);
		}
		//$FSFRoute_debug[] = "Post SEF URL : {$uri->toString(array('path', 'query', 'fragment'))}<Br>";

		/* End JRoute::build */




		/* Stuff From JRouterSite */

		// Get the path data
		$route = $uri->getPath();

		//Add the suffix to the uri
		if($router->getMode() == JROUTER_MODE_SEF && $route)
		{
			$app =& JFactory::getApplication();

			if($app->getCfg('sef_suffix') && !(substr($route, -9) == 'index.php' || substr($route, -1) == '/'))
			{
				if($format = $uri->getVar('format', 'html'))
				{
					$route .= '.'.$format;
					$uri->delVar('format');
				}
			}

			if($app->getCfg('sef_rewrite'))
			{
				//Transform the route
				$route = str_replace('index.php/', '', $route);
			}
		}

		//Add basepath to the uri
		$uri->setPath(JURI::base(true).'/'.$route);

		/* End Stuff From JRouterSite */



		/* Back into FSFRoute::x */

		$url = $uri->toString(array('path', 'query', 'fragment'));

		// Replace spaces
		$url = preg_replace('/\s/u', '%20', $url);
		//$FSFRoute_debug[] = "pre ssl $url</br>";

		/*
			* Get the secure/unsecure URLs.

			* If the first 5 characters of the BASE are 'https', then we are on an ssl connection over
			* https and need to set our secure URL to the current request URL, if not, and the scheme is
			* 'http', then we need to do a quick string manipulation to switch schemes.
			*/
		$ssl	= (int) $ssl;
		if ( $ssl )
		{
			$uri =& JURI::getInstance();

			// Get additional parts
			static $prefix;
			if ( ! $prefix ) {
				$prefix = $uri->toString( array('host', 'port'));
				//$prefix .= JURI::base(true);
			}

			// Determine which scheme we want
			$scheme	= ( $ssl === 1 ) ? 'https' : 'http';

			// Make sure our url path begins with a slash
			if ( ! preg_match('#^/#', $url) ) {
				$url	= '/' . $url;
			}

			// Build the URL
			$url	= $scheme . '://' . $prefix . $url;
		}

		if($xhtml) {
			$url = str_replace( '&', '&amp;', $url );
		}

		/* End FSFRoute::x */
		//$FSFRoute_debug[] = "returning $url<Br>";
		return $url;
	}

	static function _buildSefRoute($router, &$uri)
	{
		// Get the route
		$route = $uri->getPath();

		// Get the query data
		$query = $uri->getQuery(true);

		if (!isset($query['option'])) {
			return;
		}

		$app	= JFactory::getApplication();
		$menu	= $app->getMenu();

		/*
		 * Build the component route
		 */
		$component	= preg_replace('/[^A-Z0-9_\.-]/i', '', $query['option']);
		$tmp		= '';

		// Use the component routing handler if it exists
		$path = JPATH_SITE.DS.'components'.DS.$component.DS.'router.php';

		// Use the custom routing handler if it exists
		if (file_exists($path) && !empty($query)) {
			require_once $path;
			$function	= substr($component, 4).'BuildRoute';
			$function   = str_replace(array("-", "."), "", $function);
			$parts		= $function($query);

			// encode the route segments
			if ($component != 'com_search') {
				// Cheep fix on searches
				//$parts = $this->_encodeSegments($parts);
			} else {
				// fix up search for URL
				$total = count($parts);
				for ($i = 0; $i < $total; $i++)
				{
					// urlencode twice because it is decoded once after redirect
					$parts[$i] = urlencode(urlencode(stripcslashes($parts[$i])));
				}
			}

			$result = implode('/', $parts);
			$tmp	= ($result != "") ? $result : '';
		}

		/*
		 * Build the application route
		 */
		$built = false;
		if (isset($query['Itemid']) && !empty($query['Itemid'])) {
			$item = $menu->getItem($query['Itemid']);
			if (is_object($item) && $query['option'] == $item->component) {
				if (!$item->home || $item->language!='*') {
					$tmp = !empty($tmp) ? $item->route.'/'.$tmp : $item->route;
				}
				$built = true;
			}
		}

		if (!$built) {
			$tmp = 'component/'.substr($query['option'], 4).'/'.$tmp;
		}

		if ($tmp) {
			$route .= '/'.$tmp;
		}
		elseif ($route=='index.php') {
			$route = '';
		}

		// Unset unneeded query information
		if (isset($item) && $query['option'] == $item->component) {
			unset($query['Itemid']);
		}
		unset($query['option']);

		//Set query again in the URI
		$uri->setQuery($query);
		$uri->setPath($route);
	}
	
	static function _15($url, $xhtml, $ssl)
	{
		
		global $FSFRoute_debug;
		global $FSFRoute_menus;
		
		// get any menu items for fsf
		FSF_Helper::GetRouteMenus();
		
		$FSFRoute_debug[] = "<h1>Start URL : $url</h1>";

		// Get the router
		$app	= &JFactory::getApplication();
		$router = &$app->getRouter();

		// Make sure that we have our router
		if (! $router) {
			return null;
		}

		if ( (strpos($url, '&') !== 0 ) && (strpos($url, 'index.php') !== 0) ) {
			return $url;
		}

		/* Into JRouter::build */

		//Create the URI object
		$uri =& $router->_createURI($url);

		//Process the uri information based on custom defined rules
		$router->_processBuildRules($uri);

		// Build RAW URL
		if($router->_mode == JROUTER_MODE_RAW) {
			$router->_buildRawRoute($uri);
		}

		/* Custom part of JRouter::build */

		// split out parts of the url for
		//$parts = FSFRoute::SplitURL($menu->link);
		//$FSFRoute_debug[] = "URI : <pre>" . print_r($uri,true). "</pre>";

		// work out is we are in an Itemid already, if so, set it as the best match
		if (array_key_exists('Itemid',$uri->_vars))
		{
			$bestmatch = $uri->_vars['Itemid'];
		} else {
			$bestmatch = '';	
		}
		$bestcount = 0;
		
		$urivars = $uri->_vars;
		// find all the vars in the new url
		$sourcevars = FSFRoute::SplitURL($url);
		//$addedvars = array();
		//$FSFRoute_debug[] = "Source Vars from calling url, to save and emptied fields : <pre>" . print_r($sourcevars,true). "</pre>";
		//$FSFRoute_debug[] = "Initial URI Vars : <pre>" . print_r($urivars,true). "</pre>";

		// check through the menu item for the current url, and add any items to the new url that are missing
		if ($bestmatch && array_key_exists($bestmatch,$FSFRoute_menus))
		{
			foreach($FSFRoute_menus[$bestmatch] as $key => $value)
			{
				if (!array_key_exists($key,$urivars) && !array_key_exists($key,$sourcevars))
				{
					//$FSFRoute_debug[] = "<span style='color:red; font-size:150%'>Adding source var $key => $value</span><br>";
					$urivars[$key] = $value;
					//$addedvars[$key] = $value;
				}
			}
		}

		//$FSFRoute_debug[] = "Source Vars Added : <pre>" . print_r($addedvars,true). "</pre>";
		//$FSFRoute_debug[] = "URI Vars after adding any missing bits : <pre>" . print_r($urivars,true). "</pre>";

		foreach($FSFRoute_menus as $id => $vars)
		{
			$count = FSFRoute::MatchVars($urivars,$vars);
			//$FSFRoute_debug[] = "Match against $id => $count<br>";
			if ($count > $bestcount)
			{
				$bestcount = $count;
				$bestmatch = $id;	
			}
		}
		
		// if no match found, and we are in groups, try to link to admin
		if ($bestcount == 0 && array_key_exists('view',$sourcevars) && $sourcevars['view'] == "groups")
		{
			// no match found, try to fallback on the main support menu id
			foreach($FSFRoute_menus as $id => $item)
			{
			
				if ($item['view'] == "admin" && (!array_key_exists('layout',$item) || $item['layout'] == "default"))
				{
					$bestcount = 1;
					$bestmatch = $id;					
				}
			}
		}

		if ($bestcount == 0)
		{
			// no match found, try to fallback on the main support menu id
			foreach($FSFRoute_menus as $id => $item)
			{
				if ($item['view'] == "main")
				{
					$bestcount = 1;
					$bestmatch = $id;					
				}
			}
		}
		
		if ($bestcount == 0)
		{
			// still no match found, use any fsf menu
			if (count($FSFRoute_menus) > 0)
			{
				foreach($FSFRoute_menus as $id => $item)
				{
					$bestcount = 1;
					$bestmatch = $id;					
					break;
				}				
			}
		}

		if ($bestcount > 0)
		{
			//$FSFRoute_debug[] = "Best Match $bestmatch => $bestcount<br>";
			$uri->setVar('Itemid',$bestmatch);
			
			/*foreach($addedvars as $key => $value)
				unset($uri->_vars[$key]);*/

			if ($bestmatch && array_key_exists($bestmatch,$FSFRoute_menus))
			{
				foreach($FSFRoute_menus[$bestmatch] as $key => $value)
				{
					if (array_key_exists($key,$uri->_vars) && $uri->_vars[$key] == $value)
					{
						if ($router->_mode == JROUTER_MODE_SEF)
						{
							//$FSFRoute_debug[] = "<span style='color:red; font-size:150%'>Removing var $key, its part of the menu definition</span><br>";
							$uri->delVar($key);
						}
					}
				}
			}
		} else {
			//$FSFRoute_debug[] = "No Match found, leaving as is<br>";
		}

		/* End custom part */
		
		// Build SEF URL : mysite/route/index.php?var=x
		//$FSFRoute_debug[] = "Pre SEF URL : {$uri->toString(array('path', 'query', 'fragment'))}<Br>";
		if ($router->_mode == JROUTER_MODE_SEF) {
			$router->_buildSefRoute($uri);
		}
		//$FSFRoute_debug[] = "Post SEF URL : {$uri->toString(array('path', 'query', 'fragment'))}<Br>";

		/* End JRoute::build */




		/* Stuff From JRouterSite */

		// Get the path data
		$route = $uri->getPath();

		//Add the suffix to the uri
		if($router->_mode == JROUTER_MODE_SEF && $route)
		{
			$app =& JFactory::getApplication();

			if($app->getCfg('sef_suffix') && !(substr($route, -9) == 'index.php' || substr($route, -1) == '/'))
			{
				if($format = $uri->getVar('format', 'html'))
				{
					$route .= '.'.$format;
					$uri->delVar('format');
				}
			}

			if($app->getCfg('sef_rewrite'))
			{
				//Transform the route
				$route = str_replace('index.php/', '', $route);
			}
		}

		//Add basepath to the uri
		$uri->setPath(JURI::base(true).'/'.$route);

		/* End Stuff From JRouterSite */



		/* Back into FSFRoute::x */

		$url = $uri->toString(array('path', 'query', 'fragment'));

		// Replace spaces
		$url = preg_replace('/\s/u', '%20', $url);
		//$FSFRoute_debug[] = "pre ssl $url</br>";

		/*
			* Get the secure/unsecure URLs.

			* If the first 5 characters of the BASE are 'https', then we are on an ssl connection over
			* https and need to set our secure URL to the current request URL, if not, and the scheme is
			* 'http', then we need to do a quick string manipulation to switch schemes.
			*/
		$ssl	= (int) $ssl;
		if ( $ssl )
		{
			$uri =& JURI::getInstance();

			// Get additional parts
			static $prefix;
			if ( ! $prefix ) {
				$prefix = $uri->toString( array('host', 'port'));
				//$prefix .= JURI::base(true);
			}

			// Determine which scheme we want
			$scheme	= ( $ssl === 1 ) ? 'https' : 'http';

			// Make sure our url path begins with a slash
			if ( ! preg_match('#^/#', $url) ) {
				$url	= '/' . $url;
			}

			// Build the URL
			$url	= $scheme . '://' . $prefix . $url;
		}

		if($xhtml) {
			$url = str_replace( '&', '&amp;', $url );
		}

		/* End FSFRoute::x */
		//$FSFRoute_debug[] = "returning $url<Br>";
		return $url;

	}
	
	static function _($url, $xhtml = true, $ssl = null)
	{
		if (FSF_Helper::Is16())
		{
			return FSFRoute::_16($url, $xhtml, $ssl);
		} else {
			return FSFRoute::_15($url, $xhtml, $ssl);
		}
	}	

	static function OutputDebug()
	{
		global $FSFRoute_debug;
		if (count($FSFRoute_debug) > 0)
			foreach($FSFRoute_debug as $debug)
				echo $debug;		
	}

	static function SplitURL($link)
	{
		$link = str_ireplace("index.php?","",$link);
		$parts = explode("&",$link);
		$res = array();
		foreach($parts as $part)
		{
			if (strpos($part,"=") > 0)
			{
				list($key,$value) = explode("=",$part,2);
			} else {
				$key = $part;
				$value = "";	
			}
			if ($key == "option") continue;
			if (!$key) continue;
			$res[$key] = $value;	
		}
		return $res;
	}

	static function MatchVars($urivars, $vars)
	{
		//global $FSFRoute_debug;
		/*$FSFRoute_debug[] = "<h3>MatchVars</h3>URI:";
		$FSFRoute_debug[] = "<pre>".print_r($urivars,true)."</pre>";
		$FSFRoute_debug[] = "Vars:";
		$FSFRoute_debug[] = "<pre>".print_r($vars,true)."</pre>";*/

		foreach($vars as $key => $value)
		{
			if (!array_key_exists($key,$urivars))
			{
				//$FSFRoute_debug[] = "Not matching, $key from vars not in uri<br>";
				return 0;
			}
			if ($value != "" && $urivars[$key] != $value)
			{
				//$FSFRoute_debug[] = "Not matching, $key in uri is {$urivars[$key]} and $value in vars<br>";
				return 0;
			}
		}
		$count = 0;
		foreach($urivars as $key => $value)
		{
			//$FSFRoute_debug[] = "Matching $key => $value<br>";
			if (array_key_exists($key,$vars) && $vars[$key] == $value)
			{
				//$FSFRoute_debug[] = "FOUND!<br>";
				$count++;
			}
		}	

		return $count;
	}


	static function x($url, $xhtml = true, $ssl = null)
	{
		static $cur_url;
		if (substr($url,0,9) != "index.php")
		{
			if (empty($cur_url))
			{
				$params = $_SERVER['QUERY_STRING'];
				$parts = explode("&", $params);
				$cur_url = array();
				
				foreach ($parts as $part)
				{
					if (!strpos($part, "=")) continue;
					list($key, $value) = explode("=", $part);
					$cur_url[$key] = $value;	
				}
				
				if (array_key_exists('Itemid', $cur_url))
					unset($cur_url['Itemid']);
				if (array_key_exists('itemid', $cur_url))
					unset($cur_url['itemid']);
			}
			
			$this_url = $cur_url;
			$parts = explode("&", $url);
			foreach ($parts as $part)
			{
				if (!strpos($part, "=")) continue;
				list($key, $value) = explode("=", $part);	
				if ($value == "")
				{
					if (array_key_exists($key, $this_url))
						unset($this_url[$key]);	
				} else {
					$this_url[$key] = $value;
				}
			}
			
			$bits = array();
			foreach ($this_url as $key => $value)
				$bits[] = "$key=$value";
			$url = "index.php?" . implode("&", $bits);
		}
		
		if (strpos($url, "option=") < 1)
		{
			$url .= "&option=com_fsf";
		}
		return JRoute::_($url, $xhtml, $ssl);	
	}
}


if (!function_exists("dumpStack"))
{
	function dumpStack($skip = 0) {
		$trace = debug_backtrace();
		$output = array();
		$pathtrim = $_SERVER['SCRIPT_FILENAME'];
		$pathtrim = str_ireplace("index.php","",$pathtrim);
		$pathtrim = str_ireplace("\\","/",$pathtrim);
		foreach ($trace as $level)
		{
			if ($skip)
			{
				$skip--;
				continue;	
			}
			if (array_key_exists('file', $level))
			{
				$file   = $level['file'];
				$line   = $level['line'];
			
				$func = $level['function'];
				if (array_key_exists("class", $level))
					$func = $level['class'] . "::" . $func;

				$file = str_replace("\\","/",$file);
				$file = str_replace($pathtrim, "", $file);
			
				$output[] = "<tr><td>&nbsp;&nbsp;Line <b>$line</b>&nbsp;&nbsp;</td><td>/$file</td><td>call to $func()</td></tr>";
			}
		}
	
		return "<table width='100%'>" . implode("\n",$output) . "</table>";
	}
}

if (!function_exists("superentities"))
{
	function superentities( $str ){
		$str2 = "";
		// get rid of existing entities else double-escape
		$str = html_entity_decode(stripslashes($str),ENT_QUOTES,'UTF-8');
		$ar = preg_split('/(?<!^)(?!$)/u', $str );  // return array of every multi-byte character
		foreach ($ar as $c){
			$o = ord($c);
			if ( (strlen($c) > 1) || /* multi-byte [unicode] */
				($o <32 || $o > 126) || /* <- control / latin weirdos -> */
				($o >33 && $o < 40) ||/* quotes + ambersand */
				($o >59 && $o < 63) /* html */
			) {
				// convert to numeric entity
				$c = mb_encode_numericentity($c,array (0x0, 0xffff, 0, 0xffff), 'UTF-8');
			}
			$str2 .= $c;
		}
		return $str2;
	}
}