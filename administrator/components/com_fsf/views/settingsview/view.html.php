<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;

jimport( 'joomla.application.component.view' );
require_once (JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_fsf'.DS.'settings.php');
require_once (JPATH_SITE.DS.'components'.DS.'com_fsf'.DS.'helper'.DS.'parser.php');
require_once (JPATH_SITE.DS.'components'.DS.'com_fsf'.DS.'helper'.DS.'fields.php');


class FsfsViewSettingsView extends JViewLegacy
{
	
	function display($tpl = null)
	{
		JHTML::_('behavior.modal');

		$what = JRequest::getString('what','');
		$this->tab = JRequest::getVar('tab');
		
		if (JRequest::getVar('task') == "cancellist")
		{
			$mainframe = JFactory::getApplication();
			$link = FSFRoute::x('index.php?option=com_fsf&view=fsfs',false);
			$mainframe->redirect($link);
			return;			
		}
		
		$settings = FSF_Settings::GetAllViewSettings(); // CHANGE
		$db	= & JFactory::getDBO();
		
		if ($what == "save")
		{
			$data = JRequest::get('POST',JREQUEST_ALLOWRAW);

			foreach ($data as $setting => $value)
				if (array_key_exists($setting,$settings))
				{
					$settings[$setting] = $value;
				}
			
			foreach ($settings as $setting => $value)
			{
				if (!array_key_exists($setting,$data))
				{
					$settings[$setting] = 0;
					$value = 0;	
				}
				
				// skip any setting that is in the templates list
				if (array_key_exists($setting,$templates))
					continue;

				if (array_key_exists($setting,$large))
					continue;

				$qry = "REPLACE INTO #__fsf_settings_view (setting, value) VALUES ('";
				$qry .= FSFJ3Helper::getEscaped($db, $setting) . "','";
				$qry .= FSFJ3Helper::getEscaped($db, $value) . "')";
				$db->setQuery($qry);$db->Query();
				//echo $qry."<br>";
			}

			$link = 'index.php?option=com_fsf&view=settingsview#' . $this->tab;
			
			if (JRequest::getVar('task') == "save")
				$link = 'index.php?option=com_fsf';

			//exit;
			$mainframe = JFactory::getApplication();
			$mainframe->redirect($link, JText::_("View_Settings_Saved"));		
			exit;
		} else {
		
			$document = JFactory::getDocument();
			$document->addStyleSheet(JURI::root().'administrator/components/com_fsf/assets/css/js_color_picker_v2.css'); 
			$document->addScript(JURI::root().'administrator/components/com_fsf/assets/js/color_functions.js'); 
			$document->addScript(JURI::root().'administrator/components/com_fsf/assets/js/js_color_picker_v2.js'); 

			$this->assignRef('settings',$settings);

			JToolBarHelper::title( JText::_("FREESTYLE_FAQS") .' - '. JText::_("VIEW_SETTINGS") , 'fsf_viewsettings' );
			JToolBarHelper::apply();
			JToolBarHelper::save();
			JToolBarHelper::cancel('cancellist');
			FSFAdminHelper::DoSubToolbar();
			parent::display($tpl);
		}
	}

	function ParseParams(&$aparams)
	{
		$out = array();
		$bits = explode(";",$aparams);
		foreach ($bits as $bit)
		{
			if (trim($bit) == "") continue;
			$res = explode(":",$bit,2);
			if (count($res) == 2)
			{
				$out[$res[0]] = $res[1];	
			}
		}
		return $out;	
	}

}


