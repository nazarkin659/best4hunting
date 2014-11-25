<?php 

/**
 * PDF Invoicing Solution for VirtueMart & Joomla!
 * 
 * @package   VM Invoice
 * @version   2.0.31
 * @author    ARTIO http://www.artio.net
 * @copyright Copyright (C) 2011 ARTIO s.r.o. 
 * @license   GNU/GPLv3 http://www.artio.net/license/gnu-general-public-license
 */

defined('_JEXEC') or die('Restricted access');

jimport('joomla.filesystem.file'); //?

if (!class_exists('com_vminvoiceInstallerScript', false)) //for all cases (J2.5 can use com_install and also install script)
{
class com_vminvoiceInstallerScript
{
	//for com_install and com_uninstall
	var $pathAdmin; 
	var $pathSource;
	
	//called also by 1.5 installfile
	function install($parent = null)
	{
		JFactory::getLanguage()->load('com_vminvoice', JPATH_ADMINISTRATOR);
		
		//install plugins etc
		include_once (JPATH_SITE . '/administrator/components/com_vminvoice/helpers/installer.php');
		AInstaller::install();

		//copy changelog etc to admin
		if (!$parent) //J1.5
			$this->copyRootFilesToAdmin();
		//J1.6 will call it in postflight
		
		//TODO: also install ALL languages?
	}


	function update($adapter = null)
	{
	
	
	}
	
	//called also by 1.5 uninstallfile
	public function uninstall($parent = null)
	{
		JFactory::getLanguage()->load('com_vminvoice', JPATH_ADMINISTRATOR);
		include_once (JPATH_SITE . '/administrator/components/com_vminvoice/helpers/installer.php');
	
		AInstaller::uninstall();
	}
	
	//called only for > 1.6(7?) after install or update
	function postflight($type = null, $adapter = null)
	{
		$return = true;
		
		//http://michaelgilkes.info/2012/12/04/key-tip-to-making-a-single-installer-for-joomla-extension/
		//we have manifest for J1.5 and for 1.6 and more with _j3 suffix
		//we need to rename _j3 to normal component name after install
		
		if ($adapter AND (stripos($type, 'install') !== false OR stripos($type, 'update') !== false))
		{
			$this->pathSource = $adapter->get('parent')->getPath('source');
			$this->pathAdmin = $adapter->get('parent')->getPath('extension_root');

			if (!$this->copyRootFilesToAdmin()) //changelog and license...
				$return = false;
			
			//TODO: tohle do ainstalleru ?

			if (version_compare(JVERSION, '1.6.0')>=0){ //for all cases ... (J1.5 does not call postflight)
				$rename = array(
					array($this->pathSource.'/vminvoice_j3.xml',  //base admin file (in package root)
						$this->pathAdmin.'/vminvoice.xml', 
						$this->pathAdmin.'/vminvoice_j3.xml'), 
					array($this->pathSource.'/admin/extensions/plg_system_vminvoiceautorun/vminvoiceautorun_j3.xml',  //plugin (copy) in admin folder
						$this->pathAdmin.'/extensions/plg_system_vminvoiceautorun/vminvoiceautorun.xml',
						$this->pathAdmin.'/extensions/plg_system_vminvoiceautorun/vminvoiceautorun_j3.xml'), 
					array($this->pathSource.'/admin/extensions/plg_system_vminvoiceautorun/vminvoiceautorun_j3.xml',  //also inside joomla plugin folder. J1.5 is not here.
						JPATH_PLUGINS.'/system/vminvoiceautorun/vminvoiceautorun.xml',
						JPATH_PLUGINS.'/system/vminvoiceautorun/vminvoiceautorun_j3.xml')
						
										);
	
				foreach ($rename as $files){
				
					//copy J3 file from package instead base manifest file
					if (!self::copy($files[0], $files[1]))
						$return  = false;
					
					//ok, now delete left over _j3. so it will not remain.
					if (!empty($files[2]) AND !self::delete($files[2]))
						$return  = false;
				}
			}
			
			
			return $return;
		}
	}
	
	//copy files from package root to admin folder
	//why? why it cannot be in manifets file as another <files> node? becuse bug in joomla 3.2, it scans only first <files> node
	private function copyRootFilesToAdmin()
	{
		$return = true;
		if (!self::copy($this->pathSource.'/changelog.txt', $this->pathAdmin.'/changelog.txt')) //changelog
			$return  = false;
		if (!self::copy($this->pathSource.'/license.txt', $this->pathAdmin.'/license.txt')) //license
			$return  = false;
		
		return $return;
	}
	
	private static function copy($source, $destination)
	{
		if (!JFile::copy($source, $destination)) //file can be already there (in subfolder install copies all)
		{
			if (method_exists('JLog', 'add'))
				JLog::add(JText::sprintf('JLIB_INSTALLER_ERROR_FAIL_COPY_FILE', $source, $destination), JLog::WARNING, 'jerror');
		
			if (class_exists('JError'))
				JError::raiseWarning(1, 'JInstaller::install: '.JText::sprintf('Failed to copy file to', $source, $destination));
			else
				throw new Exception('JInstaller::install: '.JText::sprintf('Failed to copy file to', $source, $destination));
				
			return false;
		}
		
		return true;
	}
	
	private static function delete($file)
	{
		if (JFile::exists($file) AND !JFile::delete($file))
		{
			if (method_exists('JLog', 'add'))
				JLog::add(JText::sprintf('JLIB_FILESYSTEM_DELETE_FAILED', $file), JLog::WARNING, 'jerror');
	
			if (class_exists('JError'))
				JError::raiseWarning(1, 'JInstaller::install: Failed to delete file _J3 '.basename($file), $file);
			else
				throw new Exception('JInstaller::install: Failed to delete file _J3 '.basename($file), $file);
					
			return false;
		}
	
		return true;
	}
	
	function preflight($type = null, $adapter = null)
	{
	
	
	}
}
}