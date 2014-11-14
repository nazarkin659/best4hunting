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

jimport('joomla.application.component.controllerform');

/**
 * Form controller class.
 */
class Improved_ajax_loginControllerForm extends JControllerForm
{

  function __construct()
  {
    $this->view_list = 'forms';
    parent::__construct();
  }

	public function save($key = null, $urlVar = null)
  {
    $saved = parent::save($key, $urlVar);
    // generate xml to User - Improved Profile plugin
    if ($saved)
    {
      $data = JRequest::getVar('jform', array(), 'array');
      // fix for magic quotes
      if (get_magic_quotes_gpc())
      {
        $props = json_decode($data['props']);
        if ($props == null) foreach ($data as $key=>$value)
        {
          $data[$key] = stripslashes($value);
        }
      }
      $fileds = json_decode($data['fields']);
      if ($data['state'] && isset($fileds->page))
      {
        function getAttr($obj, $name)
        {
          $name = 'jform[elem_'.$name.']';
          return isset($obj->{$name})? $obj->{$name} : null;
        }

        $captcha = 0;
        $pathXML = JPATH_PLUGINS.'/user/improved_profile/profiles/improved_profile.xml';
        $profile = JFactory::getXML($pathXML);
        unset($profile->fields->fieldset->field);
        foreach ($fileds->page as $page)
        {
        	foreach ($page->elem as $elem)
          {
            $type = getAttr($elem, 'type');
            if ($type->value == 'captcha') $captcha = 1;
            if (!isset($type->profile)) continue;
            $field = $profile->fields->fieldset->addChild('field');
            $field->addAttribute('name', getAttr($elem, 'name')->value);
            $field->addAttribute('id', getAttr($elem, 'id')->value);
            $field->addAttribute('type', isset($type->defaultValue)? $type->defaultValue : $type->value);
            $field->addAttribute('required', getAttr($elem, 'required')->checked? 'true' : 'false');
            $label = getAttr($elem, 'label');
            if ($label) $field->addAttribute('label', $label->value? $label->value : $label->defaultValue);
            $title = getAttr($elem, 'title');
            if ($label) $field->addAttribute('description', $title->value? $title->value : $title->defaultValue);
            $error = getAttr($elem, 'error');
            if ($error) $field->addAttribute('message', $error->value? $error->value : $error->defaultValue);
            $article = getAttr($elem, 'article');
            if ($article)
            {
              $field->addAttribute('article', $article->value);
              $option = $field->addChild('option');
              $option->addAttribute('value', 'on');
              $option[0] = 'JYES';
            }
          }
        }
        $profile->asXML($pathXML);
        // init recaptcha
        $db = JFactory::getDBO();
        $db->setQuery("UPDATE #__extensions SET custom_data = ".($captcha? "'IALR'" : "''")." WHERE name = 'plg_captcha_recaptcha'");
        $db->query();
      }
    }
    return $saved;
  }

}