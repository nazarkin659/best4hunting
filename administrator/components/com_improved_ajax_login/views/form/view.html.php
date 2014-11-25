<?php
/*-------------------------------------------------------------------------
# com_improved_ajax_login - com_improved_ajax_login
# -------------------------------------------------------------------------
# @ author    Balint Polgarfi
# @ copyright Copyright (C) 2013 Offlajn.com  All Rights Reserved.
# @ license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# @ website   http://www.offlajn.com
-------------------------------------------------------------------------*/
?><?php
// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

/**
 * View to edit
 */
class Improved_ajax_loginViewForm extends JViewLegacy
{
	protected $state;
	protected $item;
	protected $form;

  protected $profile;

	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
		$this->state	= $this->get('State');
		$this->item		= $this->get('Item');
		$this->form		= $this->get('Form');

    $this->initLanguage();

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
            throw new Exception(implode("\n", $errors));
		}

		$this->addToolbar();
    $this->initTheme();
		parent::display($GLOBALS['j25']? '25' : $tpl);
	}

  protected function initLanguage()
  {
    // load language files
    $lang = JFactory::getLanguage();
    $lang->load('plg_user_profile');
    $lang->load('com_users', JPATH_SITE);
    $lang->load('mod_login', JPATH_SITE);
    $lang->load('mod_improved_ajax_login', JPATH_SITE.'/modules/mod_improved_ajax_login');
  }

  protected function initTheme()
  {
    // Load module
    $db = JFactory::getDBO();
    $db->setQuery("SELECT id, module, params FROM #__modules WHERE module LIKE 'mod_improved_ajax_login'");
    $module = $db->loadObject();
    if (!$module) die('mod_improved_ajax_login not found!');
    $modPath = JPATH_SITE.'/modules/mod_improved_ajax_login';
    require_once($modPath.'/params/offlajndashboard/library/flatArray.php');
    $modParams = new JRegistry();
    $modParams->loadString($module->params);
    $modParams->loadArray(offflat_array($modParams->toArray()));
    $theme = $modParams->get('theme');
    // If module not saved then set default param values
    if (!$theme) {
    	$theme = $modParams->set('theme', 'elegant');
    	$xml = JFactory::getXML("$modPath/themes/$theme/theme.xml");
    	foreach ($xml->params->param as $p) {
    	  $modParams->set((string)$p['name'], (string)$p['default']);
    	}
    }
    // Build module CSS
    require_once($modPath.'/classes/ImageHelper.php');
    require_once($modPath.'/classes/cache.class.php');
    require_once($modPath.'/helpers/font.php');
    require_once($modPath.'/helpers/parser.php');
    $cache = new OfflajnMenuThemeCache('default', $module, $modParams);
    $cache->addCss($modPath.'/themes/clear.css.php');
    $cache->addCss($modPath.'/themes/'.$theme.'/theme.css.php');
    $cache->assetsAdded();
    // Set up enviroment variables for the cache generation
    $themeurl = JURI::root(true)."/modules/mod_improved_ajax_login/themes/$theme/";
    $cache->addCssEnvVars('themeurl', $themeurl);
    $cache->addCssEnvVars('helper', new OfflajnHelper7($cache->cachePath, $cache->cacheUrl));
    $cacheFiles = $cache->generateCache();

    $this->theme = $theme;
    $this->themeCSS = $cacheFiles[0];
  }

	/**
	 * Add the page title and toolbar.
	 */
	protected function addToolbar()
	{
		JFactory::getApplication()->input->set('hidemainmenu', true);

		$user		= JFactory::getUser();
		$isNew		= ($this->item->id == 0);
    if (isset($this->item->checked_out)) {
		  $checkedOut	= !($this->item->checked_out == 0 || $this->item->checked_out == $user->get('id'));
    } else {
      $checkedOut = false;
    }
		$canDo		= Improved_ajax_loginHelper::getActions();

		JToolBarHelper::title(JText::_('COM_IMPROVED_AJAX_LOGIN_TITLE_FORM'), 'form.png');

		// If not checked out, can save the item.
		if (!$checkedOut && ($canDo->get('core.edit')||($canDo->get('core.create'))))
		{
			JToolBarHelper::apply('form.apply', 'JTOOLBAR_APPLY');
			JToolBarHelper::save('form.save', 'JTOOLBAR_SAVE');
		}
/*
		if (!$checkedOut && ($canDo->get('core.create'))){
			JToolBarHelper::custom('form.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
		}
		// If an existing item, can save to a copy.
		if (!$isNew && $canDo->get('core.create')) {
			JToolBarHelper::custom('form.save2copy', 'save-copy.png', 'save-copy_f2.png', 'JTOOLBAR_SAVE_AS_COPY', false);
		}
*/
		if (empty($this->item->id)) {
			JToolBarHelper::cancel('form.cancel', 'JTOOLBAR_CANCEL');
		}
		else {
			JToolBarHelper::cancel('form.cancel', 'JTOOLBAR_CLOSE');
		}
	}

}
