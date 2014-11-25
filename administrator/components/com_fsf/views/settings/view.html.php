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
// 	
require_once (JPATH_SITE.DS.'components'.DS.'com_fsf'.DS.'helper'.DS.'fields.php');


class FsfsViewSettings extends JViewLegacy
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
		
		$settings = FSF_Settings::GetAllSettings();
		$db	= & JFactory::getDBO();
		
		if ($what == "testref")
		{
			return $this->TestRef();
		} else if ($what == "testdates")
		{
			return $this->testdates();
		} else if ($what == "save")
		{
// 
			
			$large = FSF_Settings::GetLargeList();
			$templates = FSF_Settings::GetTemplateList();
			
			// save any large settings that arent in the templates list				
			foreach($large as $setting)
			{
				// skip any setting that is in the templates list
				if (array_key_exists($setting,$templates))
					continue;
	
				// 
				$value = JRequest::getVar($setting, '', 'post', 'string', JREQUEST_ALLOWRAW);
				$qry = "REPLACE INTO #__fsf_settings_big (setting, value) VALUES ('";
				$qry .= FSFJ3Helper::getEscaped($db, $setting) . "','";
				$qry .= FSFJ3Helper::getEscaped($db, $value) . "')";
				$db->setQuery($qry);$db->Query();

				$qry = "DELETE FROM #__fsf_settings WHERE setting = '".FSFJ3Helper::getEscaped($db, $setting)."'";
				$db->setQuery($qry);$db->Query();

				unset($_POST[$setting]);
			}		
			
			$data = JRequest::get('POST',JREQUEST_ALLOWRAW);

			foreach ($data as $setting => $value)
				if (array_key_exists($setting,$settings))
					$settings[$setting] = $value;
			
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

				$qry = "REPLACE INTO #__fsf_settings (setting, value) VALUES ('";
				$qry .= FSFJ3Helper::getEscaped($db, $setting) . "','";
				$qry .= FSFJ3Helper::getEscaped($db, $value) . "')";
				$db->setQuery($qry);$db->Query();
				//echo $qry."<br>";
			}

			$link = 'index.php?option=com_fsf&view=settings#' . $this->tab;
			
			if (JRequest::getVar('task') == "save")
				$link = 'index.php?option=com_fsf';

			//exit;
			$mainframe = JFactory::getApplication();
			$mainframe->redirect($link, JText::_("Settings_Saved"));		
			exit;
		} else if ($what == "customtemplate") {
			$this->CustomTemplate();
			exit;	
		} else {
		
			$qry = "SELECT * FROM #__fsf_templates WHERE template = 'custom'";
			$db->setQuery($qry);
			$rows = $db->loadAssocList();
			if (count($rows) > 0)
			{	
				foreach ($rows as $row)
				{
					if ($row['tpltype'])
					{
						$settings['support_list_head'] = $row['value'];
					} else {
						$settings['support_list_row'] = $row['value'];
					}
				}
			} else {
				$settings['support_list_head'] = '';
				$settings['support_list_row'] = '';
			}

// 

			$document = JFactory::getDocument();
			$document->addStyleSheet(JURI::root().'administrator/components/com_fsf/assets/css/js_color_picker_v2.css'); 
			$document->addScript(JURI::root().'administrator/components/com_fsf/assets/js/color_functions.js'); 
			$document->addScript(JURI::root().'administrator/components/com_fsf/assets/js/js_color_picker_v2.js'); 

			$this->assignRef('settings',$settings);

			JToolBarHelper::title( JText::_("FREESTYLE_FAQS") .' - '. JText::_("SETTINGS") , 'fsf_settings' );
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

	function CustomTemplate()
	{
		$template = JRequest::getVar('name');
		$db	= & JFactory::getDBO();
		$qry = "SELECT * FROM #__fsf_templates WHERE template = '" . FSFJ3Helper::getEscaped($db, $template) . "'";
		$db->setQuery($qry);
		$rows = $db->loadAssocList();
		$output = array();
		foreach ($rows as $row)
		{
			if ($row['tpltype'])
			{
				$output['head'] = $row['value'];
			} else {
				$output['row'] = $row['value'];
			}
		}
		echo json_encode($output);
		exit;	
	}

	function TestRef()
	{
		$format = JRequest::getVar('ref');
		
		$ref = FSF_Ticket_Helper::createRef(1234,$format);
		echo $ref;
		exit;	
	}
	
	function testdates()
	{
		// test the 4 date formats
		
		$date = time();
		$result = array();
		$result['date_dt_short'] = $this->testdate($date, JRequest::GetVar('date_dt_short'));
		$result['date_dt_long'] = $this->testdate($date, JRequest::GetVar('date_dt_long'));
		$result['date_d_short'] = $this->testdate($date, JRequest::GetVar('date_d_short'));
		$result['date_d_long'] = $this->testdate($date, JRequest::GetVar('date_d_long'));
		$result['timezone_offset'] = $this->testdate($date, 'Y-m-d H:i:s');
		echo json_encode($result);
		exit;
	}
	
	function testdate($date, $format)
	{
		$date = new JDate($date, new DateTimeZone("UTC"));
		$date->setTimezone(FSF_Helper::getTimezone());
		return $date->format($format, true);	
	}

	function PerPage($var)
	{
		echo "<select name='$var'>";
		
		$values = array(0 => JText::_('All'), 1 => '1', 2 => '2', 3 => '3', 4 => '4', 5 => '5', 10 => '10', 15 => '15', 20 => '20', 25 => '25', 30 => '30', 50 => '50', 100 => '100');
		
		foreach ($values as $val => $text)
		{
			echo "<option value='$val' ";
			if ($this->settings[$var] == $val) echo " SELECTED";
			echo ">" . $text . "</option>";
		}
		
		echo "</select>";
	}
}


