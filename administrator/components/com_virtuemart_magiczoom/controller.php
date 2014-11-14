<?php

/*------------------------------------------------------------------------
# mod_virtuemart_magiczoom - Magic Zoom for Joomla with VirtueMart
# ------------------------------------------------------------------------
# Magic Toolbox
# Copyright 2011 MagicToolbox.com. All Rights Reserved.
# @license - http://www.opensource.org/licenses/artistic-license-2.0  Artistic License 2.0 (GPL compatible)
# Website: http://www.magictoolbox.com/magiczoom/modules/joomla/
# Technical Support: http://www.magictoolbox.com/contact/
/*-------------------------------------------------------------------------*/

// no direct access
defined('_JEXEC') or die('Restricted access.');

defined('DS') or define('DS', DIRECTORY_SEPARATOR);

require_once JPATH_COMPONENT.DS.'helpers'.DS.'helper.php';

//NOTE: Import joomla controller library
jimport('joomla.application.component.controller');

if(!defined('MAGICTOOLBOX_LEGACY_CONTROLLER_DEFINED')) {
    define('MAGICTOOLBOX_LEGACY_CONTROLLER_DEFINED', true);
    if(JVERSION_256) {
        class MagicToolboxLegacyController extends JControllerLegacy {}
    } else {
        class MagicToolboxLegacyController extends JController {}
    }
}

class Virtuemart_MagiczoomController extends MagicToolboxLegacyController {

    public function display($cachable = false, $urlparams = false) {
        JRequest::setVar('view', JRequest::getCmd('view', 'default'));
        parent::display($cachable, $urlparams);
        return $this;
    }

    public function displayConfig($cachable = false, $urlparams = false) {
        JRequest::setVar('view', 'default');
        parent::display($cachable, $urlparams);
        return $this;
    }

    public function displayProducts($cachable = false, $urlparams = false) {
        JRequest::setVar('view', 'products');
        parent::display($cachable, $urlparams);
        return $this;
    }

    public function install() {
        jimport('joomla.installer.installer');
        $installer = new JInstaller();//JInstaller::getInstance();
        $installer->setOverwrite(true);
        $packagDir = dirname(__FILE__).DS.'virtuemart_module';
        if(!JVERSION_16) {
            //NOTE: it is important, that XML file name matches module name. Otherwise, Joomla wouldn't show parameters and additional info stored in XML.
            copy($packagDir.DS.'mod_virtuemart_magiczoom_j15.xml', $packagDir.DS.'mod_virtuemart_magiczoom.xml');
        }

        //NOTE: to fix URL's in css files
        $path = $packagDir.DS.'media'.DS.'magiczoom.css';
        if(file_exists($path) && is_writable($path)) {
            $contents = file_get_contents($path);
            $uri = JURI::root(true).'/media/mod_virtuemart_magiczoom';
            $pattern = '/url\(\s*(?:\'|")?(?!'.preg_quote($uri, '/').')\/?([^\)\s]+?)(?:\'|")?\s*\)/is';
            $replace = 'url('.$uri.'/$1)';
            $fixedContents = preg_replace($pattern, $replace, $contents);
            if($fixedContents != $contents) {
                file_put_contents($path, $fixedContents);
            }
        }

        $message = '';
        if($installer->install($packagDir)) {
            $database = JFactory::getDBO();

            //NOTE: update 'Details'
            $title = JText::_('COM_VIRTUEMART_MAGICZOOM_MODULE_TITLE');
            $position = JVERSION_16 ? 'position-7' : 'left';
            $database->setQuery("UPDATE `#__modules` SET `title`='{$title}', `ordering`=0, `position`='{$position}', `published`=1, `showtitle`=0 WHERE `module`='mod_virtuemart_magiczoom'");
            if(!$database->query()) {
                $message = JText::_($database->getErrorMsg());
            }

            //NOTE: update 'Menu Assignment'
            $database->setQuery("INSERT IGNORE INTO `#__modules_menu` (`moduleid`, `menuid`) SELECT `m`.`id`, 0 FROM `#__modules`  AS `m` WHERE `m`.`module`='mod_virtuemart_magiczoom'");
            if(!$database->query()) {
                $message = JText::_($database->getErrorMsg());
            }

        } else {
            $message = JText::_('COM_VIRTUEMART_MAGICZOOM_INSTALL_ERROR');
        }

        if(empty($message)) {
            $this->setMessage(JText::_('COM_VIRTUEMART_MAGICZOOM_INSTALL_SUCCESS'), 'message');
        } else {
            $this->setMessage($message, 'error');
        }


        $this->setRedirect(JRoute::_('index.php?option=com_virtuemart_magiczoom', false));

        return $this;
    }

    public function apply() {
        $this->saveParamsToDB();
        $this->setMessage(JText::_('COM_VIRTUEMART_MAGICZOOM_SAVE_TEXT'), 'message');
        $profile = JRequest::getVar('profile', false, 'post');
        $profile = ($profile ? '&profile='.$profile : '');
        $this->setRedirect(JRoute::_('index.php?option=com_virtuemart_magiczoom'.$profile, false));
        return $this;
    }

    public function save() {
        $this->saveParamsToDB();
        $this->setMessage(JText::_('COM_VIRTUEMART_MAGICZOOM_SAVE_TEXT'), 'message');
        $this->setRedirect(JRoute::_('index.php?option=com_virtuemart_magiczoom', false));
        return $this;
    }

    public function cancel() {
        $view = JRequest::getVar('view', false, 'post');
        $profile = JRequest::getVar('profile', false, 'post');
        $id = JRequest::getVar('productId', false, 'post');
        $target = JRequest::getVar('target', false, 'post');
        if($profile) {
            $this->setRedirect(JRoute::_('index.php?option=com_virtuemart_magiczoom', false));
        } else if($view == 'products' && $target) {
            $this->setRedirect(JRoute::_('index.php?option=com_virtuemart_magiczoom&view=products&task=alternates&id='.$id, false));
        } else if($view == 'products' && $id) {
            $this->setRedirect(JRoute::_('index.php?option=com_virtuemart_magiczoom&view=products', false));
        } else {
            $this->setRedirect(JRoute::_('index.php', false));
        }
        return $this;
    }

    public function saveParamsToDB() {
        $post = JRequest::get('post');
        $database = JFactory::getDBO();
        $profile = JRequest::getVar('profile', false, 'post');
        if(!empty($post) && !empty($post['config']) && is_array($post['config']) && !empty($profile)) {
            //require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_magiczoom'.DS.'virtuemart_module'.DS.'site'.DS.'classes'.DS.'magiczoom.module.core.class.php');
            //$tool = new MagicZoomModuleCoreClass();
            $cases = array();
            $names = array();
            foreach($post['config'] as $name => $value) {
                    //$database->setQuery("UPDATE `#__virtuemart_magiczoom_config` SET `value`='{$value}', `disabled`='0' WHERE profile='{$profile}' AND name='{$name}'");
                    //$database->query();
                    $cases[] = "WHEN '{$name}' THEN '{$value}'";
                    $names[] = "'{$name}'";
            }
            $database->setQuery("UPDATE `#__virtuemart_magiczoom_config` SET `value` = CASE `name` ".implode(' ', $cases)." END WHERE `name` IN (".implode(', ', $names).") AND profile='{$profile}'");
            $database->query();
        }
    }

    public function cleanUpProducts() {
        $database = JFactory::getDBO();
        $database->setQuery("DELETE FROM #__virtuemart_magiczoom_product_files WHERE file_id NOT IN (SELECT virtuemart_media_id FROM #__virtuemart_medias)");
        $database->query();
        $database->setQuery("DELETE FROM #__virtuemart_magiczoom_product_hotspots WHERE product_id NOT IN (SELECT virtuemart_product_id FROM #__virtuemart_products)");
        $database->query();
        $this->setMessage('Clean!', 'message');
        $this->setRedirect(JRoute::_('index.php?option=com_virtuemart_magiczoom&view=products', false));
        return $this;
    }

    public function saveAlternates() {
        $this->saveAlternatesToDB();
        $this->setMessage(JText::_('Successfully saved'), 'message');
        $id = JRequest::getVar('productId', false, 'post');
        $this->setRedirect(JRoute::_('index.php?option=com_virtuemart_magiczoom&view=products', false));
        return $this;
    }

    public function applyAlternates() {
        $this->saveAlternatesToDB();
        $this->setMessage(JText::_('Successfully saved'), 'message');
        $id = JRequest::getVar('productId', false, 'post');
        if($id) {
            $this->setRedirect(JRoute::_('index.php?option=com_virtuemart_magiczoom&view=products&task=alternates&id='.$id, false));
        } else {
            $this->setRedirect(JRoute::_('index.php?option=com_virtuemart_magiczoom', false));
        }
        return $this;
    }

    public function saveAlternatesToDB() {
        $alternates = JRequest::getVar('alts', array(), 'post');
        $productId = JRequest::getVar('productId', false, 'post');
        if(!empty($alternates)) {
            $database = JFactory::getDBO();
            $values = array();
            foreach($alternates as $id => $params) {
                $w = array();
                $w[] = intval($id);
                $w[] = isset($params['checked']) ? 1 : 0;
                $w[] = "'".$database->getEscaped($params['advanced'])."'";
                $values[] = "(".join(', ', $w).")";
            }
            $query = "INSERT INTO #__virtuemart_magiczoom_product_files (file_id, is_alternate, advanced_option) VALUES ".join(',', $values)." ON DUPLICATE KEY UPDATE is_alternate = VALUES(is_alternate),advanced_option = VALUES(advanced_option)";
            $database->setQuery($query);
            $database->query();
        }
    }

    public function saveHotspots() {
        $this->saveHotspotsToDB();
        $this->setMessage(JText::_('Successfully saved'), 'message');
        $id = JRequest::getVar('productId', false, 'post');
        if($id) {
            $this->setRedirect(JRoute::_('index.php?option=com_virtuemart_magiczoom&view=products&task=alternates&id='.$id, false));
        } else {
            $this->setRedirect(JRoute::_('index.php?option=com_virtuemart_magiczoom', false));
        }
        return $this;
    }

    public function applyHotspots() {
        $this->saveHotspotsToDB();
        $this->setMessage(JText::_('Successfully saved'), 'message');
        $id = JRequest::getVar('productId', false, 'post');
        $target = JRequest::getVar('target', false, 'post');
        if($id && $target) {
            $this->setRedirect(JRoute::_('index.php?option=com_virtuemart_magiczoom&view=products&task=hotspots&id='.$id.'&target='.$target, false));
        } else {
            $this->setRedirect(JRoute::_('index.php?option=com_virtuemart_magiczoom', false));
        }
        return $this;
    }

    public function addHotspot() {
        $this->saveHotspotsToDB(true);
        $this->setMessage(JText::_('Successfully added'), 'message');
        $id = JRequest::getVar('productId', false, 'post');
        $target = JRequest::getVar('target', false, 'post');
        if($id && $target) {
            $this->setRedirect(JRoute::_('index.php?option=com_virtuemart_magiczoom&view=products&task=hotspots&id='.$id.'&target='.$target, false));
        } else {
            $this->setRedirect(JRoute::_('index.php?option=com_virtuemart_magiczoom', false));
        }
        return $this;
    }

    public function saveHotspotsToDB($isNew = false) {

        $productId = JRequest::getVar('productId', false, 'post');
        $targetImageId = JRequest::getVar('target', false, 'post');
        $height = intval(JRequest::getVar('height', 0, 'post'));
        $width = intval(JRequest::getVar('width', 0, 'post'));
        $hotspots = JRequest::getVar('hotspots', false, 'post');

        if(!$height || !$width) return;

        $database = JFactory::getDBO();

        if($isNew) {
            $set = array();
            $set[] = 'product_id = '.$productId;
            $set[] = 'file_id = '.($targetImageId ? $targetImageId : 'NULL');
            $set[] = 'mode = \'magicthumb\'';
            $set[] = 'x1 = 0';
            $set[] = 'y1 = 0';
            $set[] = 'x2 = '.(50/$width);
            $set[] = 'y2 = '.(50/$height);
            $set[] = '`option` = \'Hotspot text\'';
            $set[] = 'active = 1';
            $query = 'INSERT INTO #__virtuemart_magiczoom_product_hotspots SET '.join(', ', $set);
            $database->setQuery($query);
            $database->query();
        }

        if(!$hotspots) return;

        $query = array();
        $delete = array();
        foreach($hotspots as $id => $params) {
            if(isset($params['delete'])) {
                $delete[] = $id;
            } else {
                $set = array();
                $set[] = 'mode = \''.$database->getEscaped($params['mode']).'\'';
                $set[] = 'product_id = '.$productId;
                $set[] = 'file_id = '.($targetImageId ? $targetImageId : 'NULL');
                $set[] = 'active = '.(empty($params['active']) ? 0 : 1);
                switch($params['mode']) {
                    case 'magicthumb':
                    case 'download':
                        $set[] = 'linked_file_id = '.intval($params['file']);
                        break;
                    default:
                        $set[] = '`option` = \''.$database->getEscaped($params['input']).'\'';
                        break;
                }
                $set[] = 'x1 = '.(intval($params['coord']['x1'])/$width);
                $set[] = 'y1 = '.(intval($params['coord']['y1'])/$height);
                $set[] = 'x2 = '.(intval($params['coord']['x2'])/$width);
                $set[] = 'y2 = '.(intval($params['coord']['y2'])/$height);
                $query[] = 'UPDATE #__virtuemart_magiczoom_product_hotspots SET '.join(', ',$set)." WHERE id = {$id}";
            }
        }
        if(count($delete)) {
            $query[] = 'DELETE FROM #__virtuemart_magiczoom_product_hotspots WHERE id IN('.join(', ',$delete).')';
        }

        foreach($query as $q) {
            $database->setQuery($q);
            $database->query();
        }
    }

}
