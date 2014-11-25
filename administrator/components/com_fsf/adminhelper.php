<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;

require_once (JPATH_SITE.DS.'components'.DS.'com_fsf'.DS.'helper'.DS.'settings.php');

class FSFAdminHelper
{
	static function PageSubTitle2($title,$usejtext = true)
	{
		if ($usejtext)
			$title = JText::_($title);
		
		return str_replace("$1",$title,FSF_Settings::get('display_h3'));
	}
	
	static function IsFAQs()
	{
		if (JRequest::getVar('option') == "com_fsf")
			return true;
		return false;	
	}
	
	static function IsTests()
	{
		if (JRequest::getVar('option') == "com_fst")
			return true;
		return false;	
	}
	
	static function GetVersion($path = "")
	{
		
		global $fsj_version;
		if (empty($fsj_version))
		{
			if ($path == "") $path = JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_fsf';
			$file = $path.DS.'fsf.xml';
			
			if (!file_exists($file))
				return FSF_Settings::get('version');
			
			$xml = simplexml_load_file($file);
			
			$fsj_version = $xml->version;
		}

		if ($fsj_version == "[VERSION]")
			return FSF_Settings::get('version');
			
		return $fsj_version;
	}	

	static function GetInstalledVersion()
	{
		return FSF_Settings::get('version');
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

	static function DoSubToolbar()
	{
		if (!FSF_Helper::Is16())
		{
			JToolBarHelper::divider();
			JToolBarHelper::help("help.php?help=admin-view-" . JRequest::getVar('view'),true);
			return;
		}

		
		if (JFactory::getUser()->authorise('core.admin', 'com_fsf'))    
		{        
			JToolBarHelper::preferences('com_fsf');
		}
		JToolBarHelper::divider();
		JToolBarHelper::help("",false,"http://www.freestyle-joomla.com/comhelp/fsf/admin-view-" . JRequest::getVar('view'));

		$vName = JRequest::getCmd('view', 'fsfs');
			
		JSubMenuHelper::addEntry(
			JText::_('COM_FSF_OVERVIEW'),
			'index.php?option=com_fsf&view=fsfs',
			$vName == 'fsfs' || $vName == ""
			);
			
		JSubMenuHelper::addEntry(
			JText::_('COM_FSF_SETTINGS'),
			'index.php?option=com_fsf&view=settings',
			$vName == 'settings'
			);

		JSubMenuHelper::addEntry(
			JText::_('COM_FSF_TEMPLATES'),
			'index.php?option=com_fsf&view=templates',
			$vName == 'templates'
			);

		JSubMenuHelper::addEntry(
			JText::_('COM_FSF_VIEW_SETTINGS'),
			'index.php?option=com_fsf&view=settingsview',
			$vName == 'settingsview'
			);

// ##NOT_TEST_START##
// 

		JSubMenuHelper::addEntry(
			JText::_('COM_FSF_FAQS'),
			'index.php?option=com_fsf&view=faqs',
			$vName == 'faqs'
			);

		JSubMenuHelper::addEntry(
			JText::_('COM_FSF_FAQ_CATEGORIES'),
			'index.php?option=com_fsf&view=faqcats',
			$vName == 'faqcats'
			);

// 
		
		JSubMenuHelper::addEntry(
			JText::_('COM_FSF_GLOSSARY'),
			'index.php?option=com_fsf&view=glossarys',
			$vName == 'glossarys'
			);

// 

		JSubMenuHelper::addEntry(
			JText::_('COM_FSF_ADMIN'),
			'index.php?option=com_fsf&view=backup',
			$vName == 'backup'
			);

	}	
	
	
	static function IncludeHelp($file)
	{
		$lang =& JFactory::getLanguage();
		$tag = $lang->getTag();
		
		$path = JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_fsf'.DS.'help'.DS.$tag.DS.$file;
		if (file_exists($path))
			return file_get_contents($path);
		
		$path = JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_fsf'.DS.'help'.DS.'en-GB'.DS.$file;
		
		return file_get_contents($path);
	}
	
	static $langs;
	static $lang_bykey;
	static function DisplayLanguage($language)
	{
		if (empty(FSFAdminHelper::$langs))
		{
			FSFAdminHelper::LoadLanguages();
		}
		
		if (array_key_exists($language, FSFAdminHelper::$lang_bykey))
			return FSFAdminHelper::$lang_bykey[$language]->text;
		
		return "";
	}
	
	static function LoadLanguages()
	{		
		$deflang = new stdClass();
		$deflang->value = "*";
		$deflang->text = JText::_('JALL');
		
		FSFAdminHelper::$langs = array_merge(array($deflang) ,JHtml::_('contentlanguage.existing'));
		
		foreach (FSFAdminHelper::$langs as $lang)
		{
			FSFAdminHelper::$lang_bykey[$lang->value] = $lang;	
		}		
	}
	
	static function GetLanguagesForm($value)
	{
		if (empty(FSFAdminHelper::$langs))
		{
			FSFAdminHelper::LoadLanguages();
		}
		
		return JHTML::_('select.genericlist',  FSFAdminHelper::$langs, 'language', 'class="inputbox" size="1" ', 'value', 'text', $value);
	}
	
	static $access_levels;
	static $access_levels_bykey;
	
	static function DisplayAccessLevel($access)
	{
		if (empty(FSFAdminHelper::$access_levels))
		{
			FSFAdminHelper::LoadAccessLevels();
		}
		
		if (array_key_exists($access, FSFAdminHelper::$access_levels_bykey))
			return FSFAdminHelper::$access_levels_bykey[$access];
		
		return "";
		
	}
	
	static function LoadAccessLevels()
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->select('a.id AS value, a.title AS text');
		$query->from('#__viewlevels AS a');
		$query->group('a.id, a.title, a.ordering');
		$query->order('a.ordering ASC');
		$query->order($query->qn('title') . ' ASC');

		// Get the options.
		$db->setQuery($query);
		FSFAdminHelper::$access_levels = $db->loadObjectList();

		// Check for a database error.
		if ($db->getErrorNum())
		{
			JError::raiseWarning(500, $db->getErrorMsg());
			return null;
		}

		foreach (FSFAdminHelper::$access_levels as $al)
		{
			FSFAdminHelper::$access_levels_bykey[$al->value] = $al->text;
		}	
	}
	
	static function GetAccessForm($value)
	{
		return JHTML::_('access.level',	'access',  $value, 'class="inputbox" size="1"', false);
	}
	
	static $filter_lang;
	static $filter_access;
	static function LA_GetFilterState()
	{
		$mainframe = JFactory::getApplication();
		FSFAdminHelper::$filter_lang	= $mainframe->getUserStateFromRequest( 'la_filter.'.'fsf_filter_language', 'fsf_filter_language', '', 'string' );
		FSFAdminHelper::$filter_access	= $mainframe->getUserStateFromRequest( 'la_filter.'.'fsf_filter_access', 'fsf_filter_access', 0, 'int' );
	}
	
	static function LA_Filter($nolangs = false)
	{
		if (!FSFAdminHelper::Is16()) return;
		
		if (empty(FSFAdminHelper::$access_levels))
		{
			FSFAdminHelper::LoadAccessLevels();
		}
		
		if (!$nolangs && empty(FSFAdminHelper::$langs))
		{
			FSFAdminHelper::LoadLanguages();
		}
	
		if (empty(FSFAdminHelper::$filter_lang))
		{
			FSFAdminHelper::LA_GetFilterState();
		}
		
		$options = FSFAdminHelper::$access_levels;		
		array_unshift($options, JHtml::_('select.option', 0, JText::_('JOPTION_SELECT_ACCESS')));
		echo JHTML::_('select.genericlist',  $options, 'fsf_filter_access', 'class="inputbox" size="1"  onchange="document.adminForm.submit( );"', 'value', 'text', FSFAdminHelper::$filter_access);
		
		if (!$nolangs)
		{
			$options = FSFAdminHelper::$langs;		
			array_unshift($options, JHtml::_('select.option', '', JText::_('JOPTION_SELECT_LANGUAGE')));
			echo JHTML::_('select.genericlist',  $options, 'fsf_filter_language', 'class="inputbox" size="1"  onchange="document.adminForm.submit( );"', 'value', 'text', FSFAdminHelper::$filter_lang);
		}
	}
	
	static function LA_Header($obj, $nolangs = false)
	{
		if (FSFAdminHelper::Is16())
		{
			if (!$nolangs)
			{
				?>
 				<th width="1%" nowrap="nowrap">
					<?php echo JHTML::_('grid.sort',   'LANGUAGE', 'fsf_filter_language', @$obj->lists['order_Dir'], @$obj->lists['order'] ); ?>
				</th>
				<?php
			}
			
			?>
 			<th width="1%" nowrap="nowrap">
				<?php echo JHTML::_('grid.sort',   'ACCESS_LEVEL', 'fsf_filter_access', @$obj->lists['order_Dir'], @$obj->lists['order'] ); ?>
			</th>
			<?php
		}
	}
	
	static function LA_Row($row, $nolangs = false)
	{
		if (FSFAdminHelper::Is16())
		{
			if (!$nolangs)
			{
				?>
				<td>
					<?php echo FSFAdminHelper::DisplayLanguage($row->language); ?></a>
				</td>
				<?php
			}
			
			?>
			<td>
			    <?php echo FSFAdminHelper::DisplayAccessLevel($row->access); ?></a>
			</td>
			<?php
		}
	}
	
	static function LA_Form($item, $nolangs = false)
	{
		if (FSFAdminHelper::Is16())
		{
			?>
			<tr>
				<td width="135" align="right" class="key">
					<label for="title">
						<?php echo JText::_("JFIELD_ACCESS_LABEL"); ?>:
					</label>
				</td>
				<td>
					<?php echo FSFAdminHelper::GetAccessForm($item->access); ?>
				</td>
			</tr>
			
			<?php
			if (!$nolangs)
			{
			?>

				<tr>
					<td width="135" align="right" class="key">
						<label for="title">
							<?php echo JText::_("JFIELD_LANGUAGE_LABEL"); ?>:
						</label>
					</td>
					<td>
						<?php echo FSFAdminHelper::GetLanguagesForm($item->language); ?>
					</td>
				</tr>
				
			<?php
			}
		}
	}
	
}