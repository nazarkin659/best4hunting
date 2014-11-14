<?php

/**
 * Support for install component extensions such modules or plugins.
 * 
 * @version		$Id$
 * @package		ARTIO JoomLIB
 * @subpackage  helpers 
 * @copyright	Copyright (C) 2010 ARTIO s.r.o.. All rights reserved.
 * @author 		ARTIO s.r.o., http://www.artio.net
 * @license     GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * @link        http://www.artio.net Official website
 */

defined('_JEXEC') or die('Restricted access');

define('AINSTALLER_INSTALL', 1);
define('AINSTALLER_UNINSTALL', 2);

if (file_exists(($filepath = JPATH_SITE . '/libraries/joomla/database/table.php')))
	include_once ($filepath);
if (file_exists(($filepath = JPATH_SITE . '/libraries/joomla/database/table/module.php')))
	include_once ($filepath);
if (file_exists(($filepath = JPATH_SITE . '/libraries/joomla/database/table/plugin.php')))
    include_once ($filepath);
if (file_exists(($filepath = JPATH_SITE . '/libraries/joomla/database/table/extension.php')))
    include_once ($filepath);

define('AINSTALLER_J15', class_exists('JTablePlugin'));
define('AINSTALLER_J16', class_exists('JTableExtension'));

class AInstaller
{

    /**
     * Proccess extensions installation.
     * 
     * @return void
     */
    function install()
    {
        if (($data = AInstaller::browsePackages(AINSTALLER_INSTALL)))
            AInstaller::setMsg('Install', $data);
    }

    /**
     * Proccess extensions uninstallation.
     * 
     * @return void
     */
    function uninstall()
    {
        if (($data = AInstaller::browsePackages(AINSTALLER_UNINSTALL)))
            AInstaller::setMsg('Uninstall', $data);
    }

    /**
     * Set result messages
     *
     * @param string $operation use Install or Uninstall
     * @param array $datas string title => extension name, string extType => extension type module/plugin , boolean outcome => success/unsuccess
     * @return void
     */
    function setMsg($operation, $datas)
    {
        $mainframe = JFactory::getApplication();
        /* @var $mainframe JApplication */
        foreach ($datas as $data)
            if (is_array($data)) {
                if ($data['outcome']) {
                    $outcome = JText::_('success');
                    $msgType = 'message';
                } else {
                    $outcome = JText::_('success');
                    $msgType = 'error';
                }
                $mainframe->enqueueMessage(JText::_($operation) . ' ' . ucfirst($data['extType']) . ' ' . $data['title'] . ' ' . JText::_($outcome), $msgType);
            }
    }

    /**
     * Browse all component extension and make selected operation.
     * 
     * @param int $type use constant AINSTALLER_INSTALL or AINSTALLER_UNINSTALL
     * @return array
     */
    function browsePackages($type)
    {
        foreach (JFolder::folders(dirname(__FILE__) . '/'. '../extensions', '.', false, true) as $package)
            switch ($type) {
                case AINSTALLER_INSTALL:
                    $outcome[] = AInstaller::installPackage($package);
                    break;
                case AINSTALLER_UNINSTALL:
                    $outcome[] = AInstaller::uninstallPackage($package);
                    break;
            }
        return isset($outcome) ? $outcome : array();
    }

    /**
     * Install concrete extension package.
     * 
     * @param string $package filepath to folder with extension
     * @return mixed false if unsuccess
     * or array with output data string title => extension name, string extType => extension type module/plugin , boolean outcome => success/unsuccess
     */
    function installPackage($package)
    {
        $installer = new JInstaller();
        $db = JFactory::getDBO();
        /* @var $db JDatabaseMySQL */
        if ($installer->install($package)) {
            if (is_object(($extension = AInstaller::loadExtension($package)))) {
                $extType = $extension->extType;
                unset($extension->extType);
                $succes = $extension->store();
                $mainframe = JFactory::getApplication();
                /* @var $mainframe JApplication */
                if (($manifest = AInstaller::getManifest($package)) !== false) {
                    foreach (JFolder::files($package, '.', false, true) as $file)
                        if ($file != $manifest->source)
                            JFile::delete($file);
                    foreach (JFolder::folders($package, '.', false, true) as $folder)
                        JFolder::delete($folder);
                } else
                    JFolder::delete($package);
                return array('title' => AInstaller::getExtensionTitle($extension) , 'extType' => $extType , 'outcome' => true);
            }
        }
        return false;
    }

    /**
     * Unistall concrete package.
     * 
     * @param string $package filepath to folder with extension
     * @return mixed false if unsuccess
     * or array with output data string title => extension name, string extType => extension type module/plugin , boolean outcome => success/unsuccess
     */
    function uninstallPackage($package)
    {
        if (is_object(($extension = AInstaller::loadExtension($package)))) {
            $installer = new JInstaller();
            $success = $installer->uninstall($extension->extType, (AINSTALLER_J15 ? $extension->id : $extension->extension_id), $extension->client_id);
            return array('title' => AInstaller::getExtensionTitle($extension) , 'extType' => $extension->extType , 'outcome' => $success);
        }
        return false;
    }

    /**
     * Load extension from specify folder.
     * 
     * @param string $package filepath to folder with extension
     * @return mixed
     */
    function loadExtension($package)
    {
        $db = JFactory::getDBO();
        /* @var $db JDatabaseMySQL */
        if (($manifest = AInstaller::getManifest($package)) !== false) {
            $root = $manifest->parser;
            $elements = $root->files;
            if ($elements) {
                $type = (string)$root['type'];
                foreach ($elements[0]->children() as $file)
                    if (($name = (string)@$file[$type])) {
                        if (class_exists(($classname = 'AInstaller' . ucfirst($type)))) {
                            $model = new $classname();
                            $extension = $model->getTable();
                            $db->setQuery($model->getQuery($name, $root));
                            if (($id = (int) $db->loadResult())) {
                                $extension->load($id);
                                if (isset($extension->iscore) && (int) $extension->iscore != 0) {
                                    $extension->iscore = 0;
                                    $extension->store();
                                }
                            }
                            if ($root->setting)
                                foreach ($root->setting[0]->attributes() as $name => $value)
                                    if (isset($extension->$name))
                                        $extension->$name = $value;
                            $model->extra($extension);
                            $extension->extType = $type;
                            return $extension;
                        }
                    }
            }
        }
        return null;
    }

    /**
     * Get extension title from given data object.
     * 
     * @param mixed $extension
     * @return string
     */
    function getExtensionTitle($extension)
    {
        if (isset($extension->title))
            return $extension->title;
        elseif (isset($extension->name))
            return $extension->name;
    }

    /**
     * Get path to XML source and prepared object to parse XML data.
     * 
     * @param string $package filepath to folder with extension
     * @return stdClass string source => filepath to XML, JSimpleXML parser => object to parse XML 
     */
    function getManifest($package)
    {
        if (($source = reset(JFolder::files($package, '.xml$', false, true))) !== false) {
            $manifest = new stdClass();
            $manifest->source = $source;
            if ($manifest->parser = new SimpleXmlElement($source,null,true))
                return $manifest;
        }
        return false;
    }

    /**
     * Get Joomla! object table.
     * 
     * @return JTableExtension
     */
    function getTable()
    {
        $db = JFactory::getDBO();
        /* @var $db JDatabaseMySQL */
        if (AINSTALLER_J16)
            return new JTableExtension($db);
        return null;
    }

    /**
     * Extra install operation.
     * 
     * @return void
     */
    function extra(&$extension)
    {}
}

/**
 * Helper object for installing module.
 */


class AInstallerModule extends AInstaller
{

    /**
     * Get Joomla! object table.
     * 
     * @return mixed in Joomla! 1.5.x JTablePlugin, in 1.6.x JTableExtension
     */
    function getTable()
    {
        $db = JFactory::getDBO();
        /* @var $db JDatabaseMySQL */
        if (AINSTALLER_J15)
            return new JTableModule($db);
        return parent::getTable();
    }

    /**
     * Get SQL query to search installed extension database registration.
     * 
     * @param string $name
     * @return string SQL query
     */
    function getQuery($name)
    {
        $db = JFactory::getDBO();
        /* @var $db JDatabaseMySQL */
        if (AINSTALLER_J15)
            return 'SELECT `id` FROM `#__modules` WHERE `module` = ' . $db->Quote($name) . ' LIMIT 1';
        elseif (AINSTALLER_J16)
            return 'SELECT `extension_id` FROM `#__extensions` WHERE `element` = ' . $db->Quote($name) . ' AND `type` = "module" LIMIT 1';
    }

    /**
     * Extra install operation.
     * 
     * @param JTableExtension $extension
     * @return void
     */
    function extra(&$extension)
    {
        if (AINSTALLER_J16) {
            if (isset($extension->enabled)) {
                $db = JFactory::getDBO();
                /* @var $db JDatabaseMySQL */
                $db->setQuery('UPDATE `#__modules` SET `published` = ' . ((int) $extension->enabled) . ' WHERE `module` = ' . $db->Quote($extension->get('element')));
                $db->query();
            }
        }
    }
}

/**
 * Helper object for installing plugin.
 */
class AInstallerPlugin extends AInstaller
{

    /**
     * Get Joomla! object table.
     * 
     * @return mixed in Joomla! 1.5.x JTablePlugin, in 1.6.x JTableExtension
     */
    function getTable()
    {
        $db = JFactory::getDBO();
        /* @var $db JDatabaseMySQL */
        if (AINSTALLER_J15)
            return new JTablePlugin($db);
        return parent::getTable();
    }

    /**
     * Get SQL query to search installed extension database registration.
     * 
     * @param string $name
     * @return string SQL query
     */
    function getQuery($name, $root)
    {
        $db = JFactory::getDBO();
        /* @var $db JDatabaseMySQL */
        $group = (string)$root['group'];
        if (AINSTALLER_J15)
            return 'SELECT `id` FROM `#__plugins` WHERE `element` = ' . $db->Quote($name) . ' AND `folder` = ' . $db->Quote($group) . ' LIMIT 1';
        elseif (AINSTALLER_J16)
            return 'SELECT `extension_id` FROM `#__extensions` WHERE `element` = ' . $db->Quote($name) . ' AND `folder` = ' . $db->Quote($group) . ' AND `type` = "plugin" LIMIT 1';
    }
}

class AInstallerJoomFish
{

    function install()
    {
        AInstallerJoomFish::init('install');
    }

    function uninstall()
    {
        AInstallerJoomFish::init('uninstall');
    }

    function init($operation = 'install')
    {
        if (! class_exists('JFile')) {
            jimport('joomla.filesystem.file');
        }
        $mainframe = JFactory::getApplication();
        /* @var $mainframe JApplication */
        $ced = JPATH_ADMINISTRATOR . '/components/com_joomfish/contentelements';
        $cld = JPATH_COMPONENT_ADMINISTRATOR . '/'. 'joomfish';
        $feced = JFolder::exists($ced);
        $fecld = JFolder::exists($cld);
        if ($feced && $fecld) {
            $iswced = is_writable($ced);
            $isrcld = is_readable($cld);
            if (! $iswced && IS_ADMIN) {
                $mainframe->enqueueMessage(sprintf(JText::_('COM_VMINVOICE_DIRECTORY %S IS UNWRITABLE. CANNOT ' . $OPERATION . ' JOOM!FISH EXTENSIONS.'), $ced), 'error');
            }
            if (! $isrcld && IS_ADMIN) {
                $mainframe->enqueueMessage(sprintf(JText::_('COM_VMINVOICE_DIRECTORY %S IS UNREADABLE. CANNOT ' . $OPERATION . ' JOOM!FISH EXTENSIONS.'), $cld), 'error');
            }
            if ($iswced && $isrcld) {
                $files = JFolder::files($cld);
                foreach ($files as $file) {
                    $source = $cld . '/'. $file;
                    $target = $ced . '/'. $file;
                    if ($operation == 'install') {
                        if (! JFile::exists($target)) {
                            JFile::copy($source, $target);
                        }
                    } elseif ($operation == 'uninstall') {
                        JFile::delete($target);
                    }
                }
            }
        }
    }
}

?>