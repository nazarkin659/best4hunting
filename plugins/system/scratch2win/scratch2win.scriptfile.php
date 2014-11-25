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

class plgSystemScratch2winInstallerScript
{
  /**
   * method to install the plugin
   *
   * @return void
   */
  function install($parent) 
  {
		$app = JFactory::getApplication();
		
		$source = JPATH_ROOT.DS.'plugins'.DS.'system'.DS.'scratch2win'.DS.'assets'.DS.'scratch2win';
		$destination = JPATH_ROOT .DS. 'images' .DS. 'scratch2win';
		
		$this->createIndexFolder(JPATH_ROOT .DS. 'images' .DS. 'scratch2win');
		$this->createIndexFolder(JPATH_ROOT .DS. 'images' .DS. 'scratch2win' .DS. 'coupons');
		$this->createIndexFolder(JPATH_ROOT .DS. 'images' .DS. 'scratch2win' .DS. 'cursors');
		$this->createIndexFolder(JPATH_ROOT .DS. 'images' .DS. 'scratch2win' .DS. 'overlay');					
		
		$this->recurse_copy($source, $destination);		
		
		if($extensions_id = $this->getPluginID())
		{
			if(!class_exists('PluginsModelPlugin')) require(JPATH_ADMINISTRATOR .DS. 'components' .DS. 'com_plugins' .DS. 'models' .DS. 'plugin.php');
			$model = new PluginsModelPlugin;
			$cid[] = $extensions_id;
			if (!$model->publish($cid, 1))
			{
				$app ->enqueueMessage('Scratch2Win plugin has been installed.');
				$app ->enqueueMessage($model->getError());
				$app ->enqueueMessage('Please remember to remember to enable/publish the plugin.');
			} 
			else
			{
				$app ->enqueueMessage('Scratch2Win plugin has been installed and enabled in your site.');
			}			
			echo '<p style="font-size: 1.2em;text-align:center;">Go to <strong>Scratch2Win</strong> Plugin - <a href="index.php?option=com_plugins&task=plugin.edit&extension_id='.$extensions_id.'" class="s2w-btn" title="Click Here">Click Here</a></p>';
		}	
  }

  /**
   * method to uninstall the plugin
   *
   * @return void
   */
  function uninstall($parent) 
  {
		$app = JFactory::getApplication();
		$app ->enqueueMessage('Scratch2Win plugin has been uninstalled from your site.');
  }
 
  /**
   * method to update the plugin
   *
   * @return void
   */
  function update($parent) 
  {				
		$app = JFactory::getApplication();
		
		$source = JPATH_ROOT.DS.'plugins'.DS.'system'.DS.'scratch2win'.DS.'assets'.DS.'scratch2win';
		$destination = JPATH_ROOT .DS. 'images' .DS. 'scratch2win';
		
		$this->createIndexFolder(JPATH_ROOT .DS. 'images' .DS. 'scratch2win');
		$this->createIndexFolder(JPATH_ROOT .DS. 'images' .DS. 'scratch2win' .DS. 'coupons');
		$this->createIndexFolder(JPATH_ROOT .DS. 'images' .DS. 'scratch2win' .DS. 'cursors');
		$this->createIndexFolder(JPATH_ROOT .DS. 'images' .DS. 'scratch2win' .DS. 'overlay');					
		
		$this->recurse_copy($source, $destination);
		
		$app ->enqueueMessage('Scratch2Win plugin has been successfully updated to Version '.$parent->get('manifest')->version);
						
		if($extensions_id = $this->getPluginID())
		{
			echo '<p style="font-size: 1.2em;text-align:center;">Go to <strong>Scratch2Win</strong> Plugin - <a href="index.php?option=com_plugins&task=plugin.edit&extension_id='.$extensions_id.'" class="s2w-btn" title="Click Here">Click Here</a></p>';
		}								
  }
 
  /**
   * method to run before an install/update/uninstall method
   *
   * @return void
   */
  function preflight($type, $parent) 
  {
          // $parent is the class calling this method
          // $type is the type of change (install, update or discover_install)
  }
 
  /**
   * method to run after an install/update/uninstall method
   *
   * @return void
   */
  function postflight($type, $parent) 
  {
          // $parent is the class calling this method
          // $type is the type of change (install, update or discover_install)
  }
				
	private function getPluginID()
	{
		$db = JFactory::getDBO();
		$query = $db->getQuery(true)
						->select('extension_id')
						->from('#__extensions')
						->where('folder = '.$db->Quote('system'))
						->where('element = '.$db->Quote('scratch2win'));
	  $db->setQuery($query);
	  try {
			$db->Query();
	  }
	  catch(JException $e)
	  {
			return false;
	  }
		if($db->loadResult())
		{
			return $db->loadResult();	
		}
	  return false;	
	}
	
	public function createIndexFolder($path){
		if(!class_exists('JFile')) require(JPATH_VM_LIBRARIES.DS.'joomla'.DS.'filesystem'.DS.'file.php');
		if(JFolder::create($path)) {
			if(!JFile::exists($path .DS. 'index.html')){
				JFile::copy(JPATH_ROOT.DS.'components'.DS.'index.html', $path .DS. 'index.html');
			}
			return true;
		}
		return false;
	}	
	
	private function recurse_copy($src,$dst ) {

		$dir = opendir($src);
		$this->createIndexFolder($dst);

		if(is_resource($dir)){
			while(false !== ( $file = readdir($dir)) ) {
				if (( $file != '.' ) && ( $file != '..' )) {
					if ( is_dir($src .DS. $file) ) {
						$this->recurse_copy($src .DS. $file,$dst .DS. $file);
					}
					else {
						if(JFile::exists($dst .DS. $file)){
							if(!JFile::delete($dst .DS. $file)){
								$app = JFactory::getApplication();
								$app->enqueueMessage('Could not delete '.$dst .DS. $file);
							}
						}
						if(!JFile::move($src .DS. $file,$dst .DS. $file)){
							$app = JFactory::getApplication();
							$app->enqueueMessage('Could not move '.$src .DS. $file.' to '.$dst .DS. $file);
						}
					}
				}
			}
			closedir($dir);
			if (is_dir($src)) JFolder::delete($src);
		} else {
			$app = JFactory::getApplication();
			$app->enqueueMessage('Could not read dir '.$dir.' source '.$src);
		}

	}	
	
}