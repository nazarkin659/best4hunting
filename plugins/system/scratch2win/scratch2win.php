<?php
/*----------------------------------------------------------------------
# Scratch2Win - Joomla System Plugin 
# ----------------------------------------------------------------------
# Copyright © 2014 VirtuePlanet Services LLP. All rights reserved.
# License - GNU General Public License version 2. http://www.gnu.org/licenses/gpl-2.0.html
# Author: VirtuePlanet Services LLP
# Email: info@virtueplanet.com
# Website:  http://www.virtueplanet.com
----------------------------------------------------------------------*/
defined('_JEXEC') or die('Restricted access');

class plgSystemScratch2win extends JPlugin
{
	
	private $config;
	
	public function __construct( &$subject, $params )
	{
		parent::__construct( $subject, $params );	 
		$this->loadLanguage();
	}	
	
	function onAfterDispatch()
	{
		$app = JFactory::getApplication();
		
		// No show for admin
		if ($app->isAdmin()) 
		{
			return;
		}
		
		$user = JFactory::getUser();
		$userlevels = JAccess::getAuthorisedViewLevels($user->id);		
		$this->getConfig();
		$loadScratchpad = false;
		
		$jconfig = JFactory::getConfig();
		$cookie_domain = $jconfig->get('cookie_domain', '');
		$cookie_path = $jconfig->get('cookie_path', '/');
		$time = array();
		$time[3] = 86400; // One Day in seconds
		$time[4] = 345600; // One Week in seconds
		$time[5] = 2592000; // One Month in seconds
		
		foreach($this->config as $number=>$item) {
			
			if(!in_array($item->access, $userlevels)) 
			{
				// User do not have access to see this item
				continue;
			}
			// Check if menu specific
			if(!$item->page && !in_array(JRequest::getInt('Itemid'), $item->menu))
			{
				continue;
			}
			// Build Unique Key
			$itemKey = array();
			$itemKey['item'] = 'SCRATCH_2_WIN';			
			foreach($item as $key=>$param) 
			{
				if($key == 'menu' && is_array($param)) {
					$param = implode('.', $param);
				}
				$itemKey['item'] .= '.'.$key.'-'.$param;
			}
			$itemKey['item'] = md5($itemKey['item']);
			$itemKey['displayed'] = 1;
			
			if($item->time != 1 && $item->time != 2) 
			{				
				jimport('joomla.utilities.simplecrypt');
				
				$hash = JApplication::getHash('SCRATCH_2_WIN_REMEMBER_'.$number);	
				// Create the encryption key, apply extra hardening using the user agent string.
				// Since we're decoding, no UA validity check is required.
				$privateKey = JApplication::getHash(@$_SERVER['HTTP_USER_AGENT']);

				$key = new JCryptKey('simple', $privateKey, $privateKey);
				$crypt = new JCrypt(new JCryptCipherSimple, $key);				
				
				if ($str = JRequest::getString($hash, '', 'cookie', JREQUEST_ALLOWRAW | JREQUEST_NOTRIM))
				{
					$str = $crypt->decrypt($str);
					$cookieData = @unserialize($str);
					// Deserialized cookie could be any object structure, so make sure the
					// credentials are well structured and only have item and display count.
					$credentials = array();
					$filter = JFilterInput::getInstance();
					
					if (is_array($credentials)) {
					    if (isset($cookieData['item']) && is_string($cookieData['item'])) {
					        $credentials['item'] = $filter -> clean($cookieData['item'], 'item');
					    }
					    if (isset($cookieData['displayed']) && is_int($cookieData['displayed'])) {
					        $credentials['displayed'] = $filter -> clean($cookieData['displayed'], 'displayed');
					    } 							 
					}		
					if(isset($credentials['item']) && isset($credentials['displayed']) && $credentials['item'] == $itemKey['item'])	
					{
						// Good Cookie exists						
						if($credentials['displayed'] >= $item->display_limit) 
						{
							continue;
						} 
						else 
						{
							$itemKey['displayed'] = $credentials['displayed'] + 1;
							setcookie($hash, $crypt->encrypt(serialize($itemKey)), time() + (int)$time[$item->time], $cookie_path, $cookie_domain);
						}						
					} 
					else 
					{							
						// Set cookies for the item being displayed
						setcookie($hash, $crypt->encrypt(serialize($itemKey)), time() + (int)$time[$item->time], $cookie_path, $cookie_domain);	
					}						
				}		
				else 
				{	
					// Set cookies for the item being displayed
					setcookie($hash, $crypt->encrypt(serialize($itemKey)), time() + (int)$time[$item->time], $cookie_path, $cookie_domain);	
				}			
			}
			
			// Once per session
			elseif($item->time == 2)
			{
				$session = JFactory::getSession();
				$sessionData = $session->get('SCRATCH_2_WIN_REMEMBER_'.$number, array(), 'PLG_SCRATCH_2_WIN');
				if(isset($sessionData['item']) && isset($sessionData['displayed']) && $sessionData['item'] == $itemKey['item'])
				{
					if($sessionData['displayed'] >= $item->display_limit) 
					{
						continue;
					} 
					else 
					{
						$itemKey['displayed'] = $sessionData['displayed'] + 1;
						$session->set('SCRATCH_2_WIN_REMEMBER_'.$number, $itemKey, 'PLG_SCRATCH_2_WIN');
					}					
				}
				else 
				{
					$session->set('SCRATCH_2_WIN_REMEMBER_'.$number, $itemKey, 'PLG_SCRATCH_2_WIN');
				}
			}			
			
			if($item->style == 'modal')
			{
				$this->loadDependencies((bool)$item->coupon, true);
				$this->autoShowModal($number, $item);
			}
			else
			{
				$this->loadDependencies((bool)$item->coupon, false);
				$this->autoShowSlidebox($number, $item);
			}
			
		}		
		
	}
	
	private function loadDependencies($coupon=false, $fancybox=false) 
	{
		$app = JFactory::getApplication();
		$doc = JFactory::getDocument();
		$assetPath = JURI::root(true).'/plugins/system/scratch2win/assets/';		
		$template = $app->getTemplate(true);
		
		if($this->params->get('load_jquery', 1) && strpos($template->template, 'vp_') === false) 
		{
			$doc->addScript($assetPath.'js/jquery-1.7.2.min.js');
		}
		
		if($fancybox) 
		{			
			$vmFancyBoxJSFile = '/components/com_virtuemart/assets/js/fancybox/jquery.fancybox-1.3.4.pack.js';				
			$vmFancyBoxJS = str_replace('/', DS, $vmFancyBoxJSFile);
			if(file_exists(JPATH_SITE.$vmFancyBoxJS)) 
			{
				$doc->addScript(JURI::root(true).$vmFancyBoxJSFile);
			} 
			else 
			{
				$doc->addScript($assetPath.'js/jquery.fancybox-1.3.4.pack.js');
			}
			
			$vmFancyBoxCSSFile = '/components/com_virtuemart/assets/css/jquery.fancybox-1.3.4.css';
			$vmFancyBoxCSS = str_replace('/', DS, $vmFancyBoxCSSFile);
			if(file_exists(JPATH_SITE.$vmFancyBoxCSS)) 
			{
				$doc->addStyleSheet(JURI::root(true).$vmFancyBoxCSSFile);
			} 
			else 
			{
				$doc->addStyleSheet($assetPath.'css/jquery.fancybox-1.3.4.css');
			}
		}	
		
		if($coupon)
		{
			$doc->addScript($assetPath.'js/wScratchPad.js');
		}
		
		$doc->addStyleSheet($assetPath.'css/scratch2win.css');
		$doc->addScript($assetPath.'js/scratch2win.js');		
	
	}
	
	
	private function autoShowModal($number, $item)
	{
		$doc = JFactory::getDocument();
		$triggerScratchPad = $item->coupon ? 'scratch2win.triggerScratchPad('.$number.');' : '';
		$delay = $item->delay;
		$close = $item->close;
		
		JPluginHelper::importPlugin('content');
		$dispatcher = JDispatcher::getInstance();
		$article = new stdClass;		
		$article->text = $item->description;  
		$article->params = array();		
		$return = $dispatcher->trigger('onContentPrepare', array ('plg_system_scratch2win.display', &$article, &$article->params, 0));
		$item->description = $article->text; 
		$autoCloseMessage = ((string)JText::_('PLG_SYSTEM_SCRATCH2WIN_AUTO_CLOSE_MESSAGE') == (string)'PLG_SYSTEM_SCRATCH2WIN_AUTO_CLOSE_MESSAGE') ? 'This message box will be closed in %s seconds' : JText::_('PLG_SYSTEM_SCRATCH2WIN_AUTO_CLOSE_MESSAGE');
		
		$js = "(function() {
						var strings_".$number." =	{\"ITEM_".$number."\":\"".$number."\",\"HEADER_".$number."\":\"".addslashes($item->header)."\",\"DESCRIPTION_".$number."\":\"".addslashes($item->description)."\",\"COUPON_".$number."\":\"".$item->coupon."\",\"FOOTER_".$number."\":\"".addslashes($item->footer)."\",\"BACKGROUNDCOLOR_".$number."\":\"".$item->backgroundcolor."\",\"BORDERCOLOR_".$number."\":\"".$item->bordercolor."\",\"TEXTCOLOR_".$number."\":\"".$item->textcolor."\",\"COUPONBORDERCOLOR_".$number."\":\"".$item->couponbordercolor."\",\"SCRATCHPAD_WIDTH_".$number."\":\"".$item->scratchpad_width."\",\"SCRATCHPAD_HEIGHT_".$number."\":\"".$item->scratchpad_height."\",\"SCRATCHPAD_IMAGE_".$number."\":\"".$item->scratchpad_image."\",\"SCRATCHPAD_IMAGE2_".$number."\":\"".$item->scratchpad_image2."\",\"SCRATCHPAD_COLOR_".$number."\":\"".$item->scratchpad_color."\",\"SCRATCHPAD_SIZE_".$number."\":\"".$item->scratchpad_size."\",\"SCRATCHPAD_CURSOR_".$number."\":\"".$item->scratchpad_cursor."\",\"AUTOCLOSE_MESSAGE\":\"".addslashes($autoCloseMessage)."\",\"SHOW_AUTOCLOSE\":\"".$this->params->get('show_counter', 1)."\"};
						if (typeof S2W == 'undefined') 
						{
							S2W = {};
							S2W.SWText = strings_".$number.";
						}
						else 
						{
							S2W.SWText.load(strings_".$number.");
						}
					})();				
					jQuery(document).ready(function(){
						scratch2win.initiateModal($number);
						scratch2win.initiateFacybox($number);
						$triggerScratchPad
					});		
					jQuery(window).load(function(){
						scratch2win.triggerFancybox($number, $delay, $close);
					});";
		$js = "\n" . preg_replace('/\s+/', ' ',$js);
		$doc->addScriptDeclaration($js);	
	}
	
	
	private function autoShowSlidebox($number, $item)
	{
		$doc = JFactory::getDocument();
		$triggerScratchPad = $item->coupon ? 'scratch2win.triggerScratchPad('.$number.');' : '';
		$delay = $item->delay;
		$close = $item->close;
		
		JPluginHelper::importPlugin('content');
		$dispatcher = JDispatcher::getInstance();
		$article = new stdClass;		
		$article->text = $item->description;  
		$article->params = array();		
		$return = $dispatcher->trigger('onContentPrepare', array ('plg_system_scratch2win.display', &$article, &$article->params, 0));
		$item->description = $article->text;
		$autoCloseMessage = ((string)JText::_('PLG_SYSTEM_SCRATCH2WIN_AUTO_CLOSE_MESSAGE') == (string)'PLG_SYSTEM_SCRATCH2WIN_AUTO_CLOSE_MESSAGE') ? 'This message box will be closed in %s seconds' : JText::_('PLG_SYSTEM_SCRATCH2WIN_AUTO_CLOSE_MESSAGE');
				
		$js = "(function() {
						var strings_".$number." =	{\"ITEM_".$number."\":\"".$number."\",\"HEADER_".$number."\":\"".addslashes($item->header)."\",\"DESCRIPTION_".$number."\":\"".addslashes($item->description)."\",\"COUPON_".$number."\":\"".$item->coupon."\",\"FOOTER_".$number."\":\"".addslashes($item->footer)."\",\"BACKGROUNDCOLOR_".$number."\":\"".$item->backgroundcolor."\",\"BORDERCOLOR_".$number."\":\"".$item->bordercolor."\",\"TEXTCOLOR_".$number."\":\"".$item->textcolor."\",\"COUPONBORDERCOLOR_".$number."\":\"".$item->couponbordercolor."\",\"SCRATCHPAD_WIDTH_".$number."\":\"".$item->scratchpad_width."\",\"SCRATCHPAD_HEIGHT_".$number."\":\"".$item->scratchpad_height."\",\"SCRATCHPAD_IMAGE_".$number."\":\"".$item->scratchpad_image."\",\"SCRATCHPAD_IMAGE2_".$number."\":\"".$item->scratchpad_image2."\",\"SCRATCHPAD_COLOR_".$number."\":\"".$item->scratchpad_color."\",\"SCRATCHPAD_SIZE_".$number."\":\"".$item->scratchpad_size."\",\"SCRATCHPAD_CURSOR_".$number."\":\"".$item->scratchpad_cursor."\",\"AUTOCLOSE_MESSAGE\":\"".addslashes($autoCloseMessage)."\",\"SHOW_AUTOCLOSE\":\"".$this->params->get('show_counter', 1)."\"};
						if (typeof S2W == 'undefined') 
						{
							S2W = {};
							S2W.SWText = strings_".$number.";
						}
						else 
						{
							S2W.SWText.load(strings_".$number.");
						}
					})();				
					jQuery(document).ready(function(){
						scratch2win.initiateSlidebox($number);
						$triggerScratchPad
					});		
					jQuery(window).load(function(){
						scratch2win.triggerSlidebox($number, $delay, $close);
					});";
		$js = "\n" . preg_replace('/\s+/', ' ',$js);
		$doc->addScriptDeclaration($js);	
	}	
	
	
	
	private function getConfig()
	{
		$name = '';
		$username = '';
		$user = JFactory::getUser();
		if (!$user->guest) {
			$name = '&nbsp;'.$user->name;
			$username = '&nbsp;'.$user->username;
		}
		$find = array(' {name}', ' {username}', '{name}', '{username}');
		$replace = array('{name}', '{username}', $name, $username);
		
		$this->config = array();
		for ($i = 1; $i <= 3; $i++) {
			if($this->params->get('active_'.$i, 0)) {
				$this->config[$i] = new stdClass;
				$this->config[$i]->access = $this->params->get('access_'.$i, 1);
				$this->config[$i]->display_limit = $this->params->get('display_limit_'.$i, 2);
				$this->config[$i]->time = $this->params->get('time_'.$i, 1);
				$this->config[$i]->page = $this->params->get('page_'.$i, 1);
				$this->config[$i]->menu = $this->params->get('menu_'.$i);
				$this->config[$i]->style = $this->params->get('style_'.$i, 'modal');
				$this->config[$i]->delay = $this->params->get('delay_'.$i, 1000);
				$this->config[$i]->close = $this->params->get('close_'.$i, 10000);
				$this->config[$i]->coupon = $this->params->get('coupon_'.$i, 1);
				$this->config[$i]->scratchpad_width = $this->params->get('scratchpad_width_'.$i, 210);
				$this->config[$i]->scratchpad_height = $this->params->get('scratchpad_height_'.$i, 100);
				$this->config[$i]->scratchpad_image = $this->params->get('scratchpad_image_'.$i) ? JURI::root().$this->params->get('scratchpad_image_'.$i) : null;
				$this->config[$i]->scratchpad_image2 = $this->params->get('scratchpad_image2_'.$i) ? JURI::root().$this->params->get('scratchpad_image2_'.$i) : null;
				$this->config[$i]->scratchpad_color = $this->params->get('scratchpad_color_'.$i);
				$this->config[$i]->scratchpad_size = $this->params->get('scratchpad_size_'.$i, 10);
				$this->config[$i]->scratchpad_cursor = $this->params->get('scratchpad_cursor_'.$i) ? JURI::root().$this->params->get('scratchpad_cursor_'.$i) : null;
				$this->config[$i]->bordercolor = $this->params->get('bordercolor_'.$i, null);
				$this->config[$i]->backgroundcolor = $this->params->get('backgroundcolor_'.$i, null);
				$this->config[$i]->textcolor = $this->params->get('textcolor_'.$i, null);
				$this->config[$i]->couponbordercolor = $this->params->get('couponbordercolor_'.$i, null);
				$this->config[$i]->header = (string) $this->params->get('header_'.$i);
				$this->config[$i]->header = str_replace($find, $replace, $this->config[$i]->header);
				$this->config[$i]->description = (string) $this->params->get('description_'.$i);
				$this->config[$i]->description = str_replace($find, $replace, $this->config[$i]->description);
				$this->config[$i]->footer = (string) $this->params->get('footer_'.$i);
				$this->config[$i]->footer = str_replace($find, $replace, $this->config[$i]->footer);
			}
		}		
	}
	
	public function onBeforeRender()
	{
		$plugin_id = $this->getPluginID();		
		$app = JFactory::getApplication();
		
		if($app->isAdmin() && JRequest::getInt('extension_id') == $plugin_id && JRequest::getVar('option') == 'com_plugins' && JRequest::getVar('view') == 'plugin')
		{
			$this->loadAdminInterface();
			return;
		}		
	}
	
	private function getPluginID() 
	{
		$db = JFactory::getDBO();
		$sql = $db->getQuery(true)
					->select('extension_id')
					->from('#__extensions')
					->where('element = '.$db->quote('scratch2win'));
		$db->setQuery($sql);
		return $db->loadResult();
	}
	
	private function loadAdminInterface()
	{
		$doc = JFactory::getDocument();
		$assetPath = JURI::root(true).'/plugins/system/scratch2win/assets/admin/';
		
		$doc->addScript($assetPath.'js/jquery-1.7.2.min.js');
		$doc->addScript($assetPath.'js/jquery-ui-1.10.3.custom.min.js');
		$doc->addScript($assetPath.'js/chosen.jquery.min.js');
		$doc->addScript($assetPath.'js/admin.js');
		$doc->addStyleSheet($assetPath.'css/ui-lightness/jquery-ui-1.10.3.custom.min.css');
		$doc->addStyleSheet($assetPath.'css/chosen.min.css');
		$doc->addStyleSheet($assetPath.'css/admin.css');

	}

}
