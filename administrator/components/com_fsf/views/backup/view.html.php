<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;

jimport( 'joomla.application.component.view' );
jimport('joomla.filesystem.file');
require_once (JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_fsf'.DS.'updatedb.php');

class FsfsViewBackup extends JViewLegacy
{
    function display($tpl = null)
    {
        JToolBarHelper::title( JText::_("ADMINISTRATION"), 'fsf_admin' );
        JToolBarHelper::cancel('cancellist');
		FSFAdminHelper::DoSubToolbar();
		
		$this->log = "";
		
		$task = JRequest::getVar('task');
		$updater = new FSFUpdater();
			
		if ($task == "saveapi")
		{
			return $this->SaveAPI();
				
		}
		if ($task == "cancellist")
		{
			$mainframe = JFactory::getApplication();
			$link = FSFRoute::x('index.php?option=com_fsf&view=fsfs',false);
			$mainframe->redirect($link);
			return;			
		}
		if ($task == "update")
		{
			$this->assignRef('log',$updater->Process());
			parent::display();
			return;
		}
				
		if ($task == "backup")
		{
			$this->assignRef('log',$updater->BackupData('fsf'));
		}
		
		if ($task == "restore")
		{
			// process any new file uploaded
			$file = JRequest::getVar('filedata', '', 'FILES', 'array');
			if (array_key_exists('error',$file) && $file['error'] == 0)
			{
				$data = file_get_contents($file['tmp_name']);
				$data = unserialize($data);
				
				global $log;
				$log = "";
				$log = $updater->RestoreData($data);
				$this->assignRef('log',$log);
				parent::display();
				return;
			}
			
		}
		
        parent::display($tpl);
    }
	
	function SaveAPI()
	{
		$username = JRequest::getVar('username');
		$apikey = JRequest::getVar('apikey');

		$db = & JFactory::getDBO();
		
		$qry = "REPLACE INTO #__fsf_settings (setting, value) VALUES ('fsj_username','".FSFJ3Helper::getEscaped($db, $username)."')";
		$db->setQuery($qry);
		$db->Query();
		
		$qry = "REPLACE INTO #__fsf_settings (setting, value) VALUES ('fsj_apikey','".FSFJ3Helper::getEscaped($db, $apikey)."')";
		$db->setQuery($qry);
		$db->Query();
		
		// update url links
		if (FSFAdminHelper::Is16())
		{
			$updater = new FSFUpdater();
			$updater->SortAPIKey($username, $apikey);
		}
		
		$mainframe = JFactory::getApplication();
		$link = FSFRoute::x('index.php?option=com_fsf&view=backup',false);
		$mainframe->redirect($link);
	}
}
