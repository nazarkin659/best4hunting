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

if(!function_exists('com_install')) {
    function com_install() {
        return installMagiczoomForVirtueMart();
    }
}

if(!function_exists('com_uninstall')) {
    function com_uninstall() {
        return uninstallMagiczoomForVirtueMart();
    }
}

function installMagiczoomForVirtueMart() {

    $database = JFactory::getDBO();
    $database->setQuery("SELECT COUNT(*) as `count` FROM `#__virtuemart_magiczoom_config` LIMIT 1");
    $results = $database->loadObject();
    if($results->count) {
        $database->setQuery("DROP TABLE IF EXISTS `#__virtuemart_magiczoom_config_bak`;");
        $database->query();
        $database->setQuery("RENAME TABLE `#__virtuemart_magiczoom_config` TO `#__virtuemart_magiczoom_config_bak`;");
        if($database->query()) {
            $database->setQuery("CREATE TABLE `#__virtuemart_magiczoom_config` LIKE `#__virtuemart_magiczoom_config_bak`;");
            if($database->query()) {
                $results->count = 0;
            }
        }
    }
    if($results->count == 0) {
        $query = <<<SQL
INSERT INTO `#__virtuemart_magiczoom_config` (`profile`, `name`, `value`, `default`) VALUES
 ('default', 'enable-effect', 'No', 'No'),
 ('default', 'template', 'bottom', 'bottom'),
 ('default', 'magicscroll', 'No', 'No'),
 ('default', 'thumb-max-width', '200', '200'),
 ('default', 'thumb-max-height', '200', '200'),
 ('default', 'zoom-width', '300', '300'),
 ('default', 'zoom-height', '300', '300'),
 ('default', 'zoom-position', 'right', 'right'),
 ('default', 'zoom-align', 'top', 'top'),
 ('default', 'zoom-distance', '15', '15'),
 ('default', 'square-images', 'disable', 'disable'),
 ('default', 'opacity', '50', '50'),
 ('default', 'opacity-reverse', 'No', 'No'),
 ('default', 'zoom-fade', 'Yes', 'Yes'),
 ('default', 'zoom-window-effect', 'shadow', 'shadow'),
 ('default', 'zoom-fade-in-speed', '200', '200'),
 ('default', 'zoom-fade-out-speed', '200', '200'),
 ('default', 'fps', '25', '25'),
 ('default', 'smoothing', 'Yes', 'Yes'),
 ('default', 'smoothing-speed', '40', '40'),
 ('default', 'selector-max-width', '50', '50'),
 ('default', 'selector-max-height', '50', '50'),
 ('default', 'selectors-margin', '5', '5'),
 ('default', 'use-individual-titles', 'Yes', 'Yes'),
 ('default', 'selectors-change', 'click', 'click'),
 ('default', 'selectors-class', '', ''),
 ('default', 'preload-selectors-small', 'Yes', 'Yes'),
 ('default', 'preload-selectors-big', 'No', 'No'),
 ('default', 'selectors-effect', 'fade', 'fade'),
 ('default', 'selectors-effect-speed', '400', '400'),
 ('default', 'selectors-mouseover-delay', '60', '60'),
 ('default', 'initialize-on', 'load', 'load'),
 ('default', 'click-to-activate', 'No', 'No'),
 ('default', 'click-to-deactivate', 'No', 'No'),
 ('default', 'show-loading', 'Yes', 'Yes'),
 ('default', 'loading-msg', 'Loading zoom...', 'Loading zoom...'),
 ('default', 'loading-opacity', '75', '75'),
 ('default', 'loading-position-x', '-1', '-1'),
 ('default', 'loading-position-y', '-1', '-1'),
 ('default', 'entire-image', 'No', 'No'),
 ('default', 'show-title', 'top', 'top'),
 ('default', 'link-to-product-page', 'Yes', 'Yes'),
 ('default', 'use-original-vm-thumbnails', 'No', 'No'),
 ('default', 'show-message', 'Yes', 'Yes'),
 ('default', 'message', 'Move your mouse over image', 'Move your mouse over image'),
 ('default', 'right-click', 'No', 'No'),
 ('default', 'imagemagick', 'auto', 'auto'),
 ('default', 'image-quality', '100', '100'),
 ('default', 'use-original-file-names', 'Yes', 'Yes'),
 ('default', 'disable-zoom', 'No', 'No'),
 ('default', 'always-show-zoom', 'No', 'No'),
 ('default', 'drag-mode', 'No', 'No'),
 ('default', 'move-on-click', 'Yes', 'Yes'),
 ('default', 'x', '-1', '-1'),
 ('default', 'y', '-1', '-1'),
 ('default', 'preserve-position', 'No', 'No'),
 ('default', 'fit-zoom-window', 'Yes', 'Yes'),
 ('default', 'watermark', '', ''),
 ('default', 'watermark-max-width', '50%', '50%'),
 ('default', 'watermark-max-height', '50%', '50%'),
 ('default', 'watermark-opacity', '50', '50'),
 ('default', 'watermark-position', 'center', 'center'),
 ('default', 'watermark-offset-x', '0', '0'),
 ('default', 'watermark-offset-y', '0', '0'),
 ('default', 'hint', 'Yes', 'Yes'),
 ('default', 'hint-text', 'Zoom', 'Zoom'),
 ('default', 'hint-position', 'top left', 'top left'),
 ('default', 'hint-opacity', '75', '75'),
 ('default', 'scroll-style', 'default', 'default'),
 ('default', 'show-image-title', 'Yes', 'Yes'),
 ('default', 'loop', 'continue', 'continue'),
 ('default', 'speed', '0', '0'),
 ('default', 'width', '0', '0'),
 ('default', 'height', '0', '0'),
 ('default', 'item-width', '0', '0'),
 ('default', 'item-height', '0', '0'),
 ('default', 'step', '3', '3'),
 ('default', 'items', '3', '3'),
 ('default', 'arrows', 'outside', 'outside'),
 ('default', 'arrows-opacity', '60', '60'),
 ('default', 'arrows-hover-opacity', '100', '100'),
 ('default', 'slider-size', '10%', '10%'),
 ('default', 'slider', 'false', 'false'),
 ('default', 'duration', '1000', '1000'),
 ('browse', 'enable-effect', 'No', 'No'),
 ('browse', 'thumb-max-width', '200', '200'),
 ('browse', 'thumb-max-height', '200', '200'),
 ('browse', 'zoom-width', '300', '300'),
 ('browse', 'zoom-height', '300', '300'),
 ('browse', 'zoom-position', 'inner', 'inner'),
 ('browse', 'zoom-align', 'top', 'top'),
 ('browse', 'zoom-distance', '15', '15'),
 ('browse', 'square-images', 'disable', 'disable'),
 ('browse', 'opacity', '50', '50'),
 ('browse', 'opacity-reverse', 'No', 'No'),
 ('browse', 'zoom-fade', 'Yes', 'Yes'),
 ('browse', 'zoom-window-effect', 'shadow', 'shadow'),
 ('browse', 'zoom-fade-in-speed', '200', '200'),
 ('browse', 'zoom-fade-out-speed', '200', '200'),
 ('browse', 'fps', '25', '25'),
 ('browse', 'smoothing', 'Yes', 'Yes'),
 ('browse', 'smoothing-speed', '40', '40'),
 ('browse', 'initialize-on', 'load', 'load'),
 ('browse', 'click-to-activate', 'Yes', 'Yes'),
 ('browse', 'click-to-deactivate', 'No', 'No'),
 ('browse', 'show-loading', 'Yes', 'Yes'),
 ('browse', 'loading-msg', 'Loading zoom...', 'Loading zoom...'),
 ('browse', 'loading-opacity', '75', '75'),
 ('browse', 'loading-position-x', '-1', '-1'),
 ('browse', 'loading-position-y', '-1', '-1'),
 ('browse', 'entire-image', 'No', 'No'),
 ('browse', 'show-title', 'top', 'top'),
 ('browse', 'link-to-product-page', 'Yes', 'Yes'),
 ('browse', 'use-original-vm-thumbnails', 'No', 'No'),
 ('browse', 'show-message', 'No', 'No'),
 ('browse', 'message', 'Click to zoom', 'Click to zoom'),
 ('browse', 'right-click', 'No', 'No'),
 ('browse', 'imagemagick', 'auto', 'auto'),
 ('browse', 'image-quality', '100', '100'),
 ('browse', 'use-original-file-names', 'Yes', 'Yes'),
 ('browse', 'disable-zoom', 'No', 'No'),
 ('browse', 'always-show-zoom', 'No', 'No'),
 ('browse', 'drag-mode', 'No', 'No'),
 ('browse', 'move-on-click', 'Yes', 'Yes'),
 ('browse', 'x', '-1', '-1'),
 ('browse', 'y', '-1', '-1'),
 ('browse', 'preserve-position', 'No', 'No'),
 ('browse', 'fit-zoom-window', 'Yes', 'Yes'),
 ('browse', 'watermark', '', ''),
 ('browse', 'watermark-max-width', '50%', '50%'),
 ('browse', 'watermark-max-height', '50%', '50%'),
 ('browse', 'watermark-opacity', '50', '50'),
 ('browse', 'watermark-position', 'center', 'center'),
 ('browse', 'watermark-offset-x', '0', '0'),
 ('browse', 'watermark-offset-y', '0', '0'),
 ('browse', 'hint', 'Yes', 'Yes'),
 ('browse', 'hint-text', 'Zoom', 'Zoom'),
 ('browse', 'hint-position', 'top left', 'top left'),
 ('browse', 'hint-opacity', '75', '75'),
 ('details', 'enable-effect', 'Yes', 'Yes'),
 ('details', 'template', 'bottom', 'bottom'),
 ('details', 'magicscroll', 'No', 'No'),
 ('details', 'thumb-max-width', '200', '200'),
 ('details', 'thumb-max-height', '200', '200'),
 ('details', 'zoom-width', '300', '300'),
 ('details', 'zoom-height', '300', '300'),
 ('details', 'zoom-position', 'right', 'right'),
 ('details', 'zoom-align', 'top', 'top'),
 ('details', 'zoom-distance', '15', '15'),
 ('details', 'square-images', 'disable', 'disable'),
 ('details', 'opacity', '50', '50'),
 ('details', 'opacity-reverse', 'No', 'No'),
 ('details', 'zoom-fade', 'Yes', 'Yes'),
 ('details', 'zoom-window-effect', 'shadow', 'shadow'),
 ('details', 'zoom-fade-in-speed', '200', '200'),
 ('details', 'zoom-fade-out-speed', '200', '200'),
 ('details', 'fps', '25', '25'),
 ('details', 'smoothing', 'Yes', 'Yes'),
 ('details', 'smoothing-speed', '40', '40'),
 ('details', 'selector-max-width', '50', '50'),
 ('details', 'selector-max-height', '50', '50'),
 ('details', 'selectors-margin', '5', '5'),
 ('details', 'use-individual-titles', 'Yes', 'Yes'),
 ('details', 'selectors-change', 'click', 'click'),
 ('details', 'selectors-class', '', ''),
 ('details', 'preload-selectors-small', 'Yes', 'Yes'),
 ('details', 'preload-selectors-big', 'No', 'No'),
 ('details', 'selectors-effect', 'fade', 'fade'),
 ('details', 'selectors-effect-speed', '400', '400'),
 ('details', 'selectors-mouseover-delay', '60', '60'),
 ('details', 'initialize-on', 'load', 'load'),
 ('details', 'click-to-activate', 'No', 'No'),
 ('details', 'click-to-deactivate', 'No', 'No'),
 ('details', 'show-loading', 'Yes', 'Yes'),
 ('details', 'loading-msg', 'Loading zoom...', 'Loading zoom...'),
 ('details', 'loading-opacity', '75', '75'),
 ('details', 'loading-position-x', '-1', '-1'),
 ('details', 'loading-position-y', '-1', '-1'),
 ('details', 'entire-image', 'No', 'No'),
 ('details', 'show-title', 'top', 'top'),
 ('details', 'use-original-vm-thumbnails', 'No', 'No'),
 ('details', 'show-message', 'Yes', 'Yes'),
 ('details', 'message', 'Move your mouse over image', 'Move your mouse over image'),
 ('details', 'right-click', 'No', 'No'),
 ('details', 'imagemagick', 'auto', 'auto'),
 ('details', 'image-quality', '100', '100'),
 ('details', 'use-original-file-names', 'Yes', 'Yes'),
 ('details', 'disable-zoom', 'No', 'No'),
 ('details', 'always-show-zoom', 'No', 'No'),
 ('details', 'drag-mode', 'No', 'No'),
 ('details', 'move-on-click', 'Yes', 'Yes'),
 ('details', 'x', '-1', '-1'),
 ('details', 'y', '-1', '-1'),
 ('details', 'preserve-position', 'No', 'No'),
 ('details', 'fit-zoom-window', 'Yes', 'Yes'),
 ('details', 'watermark', '', ''),
 ('details', 'watermark-max-width', '50%', '50%'),
 ('details', 'watermark-max-height', '50%', '50%'),
 ('details', 'watermark-opacity', '50', '50'),
 ('details', 'watermark-position', 'center', 'center'),
 ('details', 'watermark-offset-x', '0', '0'),
 ('details', 'watermark-offset-y', '0', '0'),
 ('details', 'hint', 'Yes', 'Yes'),
 ('details', 'hint-text', 'Zoom', 'Zoom'),
 ('details', 'hint-position', 'top left', 'top left'),
 ('details', 'hint-opacity', '75', '75'),
 ('details', 'scroll-style', 'default', 'default'),
 ('details', 'show-image-title', 'Yes', 'Yes'),
 ('details', 'loop', 'continue', 'continue'),
 ('details', 'speed', '0', '0'),
 ('details', 'width', '0', '0'),
 ('details', 'height', '0', '0'),
 ('details', 'item-width', '0', '0'),
 ('details', 'item-height', '0', '0'),
 ('details', 'step', '3', '3'),
 ('details', 'items', '3', '3'),
 ('details', 'arrows', 'outside', 'outside'),
 ('details', 'arrows-opacity', '60', '60'),
 ('details', 'arrows-hover-opacity', '100', '100'),
 ('details', 'slider-size', '10%', '10%'),
 ('details', 'slider', 'false', 'false'),
 ('details', 'duration', '1000', '1000'),
 ('default', 'version', '4.7.3', '4.7.3');
SQL;
        $database->setQuery($query);
        if(!$database->query()) {
            return JError::raiseWarning(500, $database->getError());
        }
    }

    $url = 'index.php?option=com_virtuemart_magiczoom&task=install';
?>
<style>
.magictoolbox-message-container h1 {
    color: #468847;
}
.magictoolbox-message-container {
    color: #468847;   
    background-color: #DFF0D8;
    border: 1px solid #D6E9C6;
    border-radius: 4px;
    margin-bottom: 18px;
    padding: 8px 35px 8px 14px;
    text-shadow: 0 1px 0 rgba(255, 255, 255, 0.5);
}
</style>
<div class="magictoolbox-message-container">
<h1>Please wait...</h1>
<h2>The frontend module will be installed automatically...</h2>
<h2>Please click <a href="<?php echo $url; ?>" style="color: black;">here</a> if you are not automatically redirected within <span id="redirect_timer">3</span> seconds</h2>
<script language="javascript" type="text/javascript">
var intervalCounter = 3;
var intervalID = setInterval(function() {
    if(intervalCounter) {
        intervalCounter--;
        document.getElementById('redirect_timer').innerHTML = intervalCounter;
    }
    if(!intervalCounter) {
        clearInterval(intervalID);
        document.location.href = '<?php echo $url; ?>';
    }
}, 1000);
</script>
</div>
<?php
    sendVirtueMartMagiczoomModuleStat('install');
    return true;
}

function uninstallMagiczoomForVirtueMart() {

    if(version_compare(JVERSION, '1.6.0', '<')) {
        //NOTE: need to load lang file for uninstall string
        $lang = JFactory::getLanguage();
        $lang->load('com_virtuemart_magiczoom', JPATH_ADMINISTRATOR, null, false);
    }

    //NOTE: uninstall module
    $module = 'mod_virtuemart_magiczoom';
    $database = JFactory::getDBO();
    $query = "SELECT `id` FROM `#__modules` WHERE module='{$module}'";
    $database->setQuery($query);
    $modIDs = version_compare(JVERSION, '1.7.0', '<') ? $database->loadResultArray() : $database->loadColumn();
    if(count($modIDs)) {
        $modID = implode(',', $modIDs);
        $query = 'DELETE FROM #__modules_menu WHERE moduleid IN ('.$modID.')';
        $database->setQuery($query);
        $database->query();
        $query = "DELETE FROM `#__modules` WHERE module='{$module}'";
        $database->setQuery($query);
        $database->query();
        if(version_compare(JVERSION, '1.6.0', '>=')) {
            $query = "DELETE FROM `#__extensions` WHERE element='{$module}'";
            $database->setQuery($query);
            $database->query();
        }
        //$query = "DELETE FROM `#__menu` WHERE link LIKE '%{$module}%'";
    }

    $manifest = file_exists(JPATH_SITE.DS.'modules'.DS.$module.DS."{$module}.xml") ? simplexml_load_file(JPATH_SITE.DS.'modules'.DS.$module.DS."{$module}.xml") : false;//SimpleXMLElement
    if($manifest) {
        $elements = array('media', 'languages');
        foreach($elements as $elementPath) {
            $element = $manifest->$elementPath;//SimpleXMLElement
            if(is_a($element, 'SimpleXMLElement') && count($element->children())) {
                switch($elementPath) {
                    case 'media':
                        $source = JPATH_SITE.DS.'media';
                        $destination = $element->attributes()->destination;
                        if($destination) {
                            $source = $source.DS.$destination;
                        }
                        break;
                    case 'languages':
                        $source = JPATH_SITE.DS.'language';
                        break;
                }
                foreach($element->children() as $child) {
                    if($child->getName() == 'language' && $child->attributes()->tag) {
                        $path = $source.DS.$child->attributes()->tag;
                        if(!JFolder::exists($path)) continue;
                        $path = $path.DS.$child;
                    } else {
                        $path = $source.DS.$child;
                    }
                    if(is_file($path)) {
                        JFile::delete($path);
                    } else if(is_dir($path)) {
                        $val = JFolder::delete($path);
                    }
                }
                //if($elementPath == 'media' && $destination) {
                //    JFolder::delete($source);
                //}
            }
        }
    }

    if(is_dir(JPATH_SITE.DS.'modules'.DS.$module)) {
        JFolder::delete(JPATH_SITE.DS.'modules'.DS.$module);
    }

    echo '<div style="background-color: #C3D2E5;">
          <p style="color: #0055BB;font-weight: bold;">'.JText::_('COM_VIRTUEMART_MAGICZOOM_UNINSTALL_TEXT').'</p>
          </div>';

    sendVirtueMartMagiczoomModuleStat('uninstall');
    return true;

}

class com_virtuemart_magiczoomInstallerScript {

    function preflight($type, $parent) {
        return true;
    }

    function install($parent) {
        return installMagiczoomForVirtueMart();
    }

    function update($parent) {
        return installMagiczoomForVirtueMart();
    }

    function uninstall($parent) {
        return uninstallMagiczoomForVirtueMart();
    }

    function postflight($type, $parent) {
        return true;
    }

}

function sendVirtueMartMagiczoomModuleStat($action = '') {

    //NOTE: don't send from working copy
    if('working' == 'v4.7.3' || 'working' == 'v4.5.29') {
        return;
    }
    $hostname = 'www.magictoolbox.com';

    $url = $_SERVER['HTTP_HOST'].JURI::root(true);
    $url = urlencode(urldecode($url));

    $platformVersion = '';
    if(file_exists(dirname(dirname(__FILE__)).DS.'com_virtuemart'.DS.'version.php')) {
        include dirname(dirname(__FILE__)).DS.'com_virtuemart'.DS.'version.php';
        $platformVersion = preg_replace('/^[a-zA-Z]+\s+(\d(?:\.\d)*).*$/is', '$1', $shortversion);
    }

    $path = "api/stat/?action={$action}&tool_name=magiczoom&license=trial&tool_version=v4.5.29&module_version=v4.7.3&platform_name=virtuemart2&platform_version={$platformVersion}&url={$url}";

    $handle = @fsockopen($hostname, 80, $errno, $errstr, 30);
    if($handle) {
        $headers = "GET /{$path} HTTP/1.1\r\n";
        $headers .= "Host: {$hostname}\r\n";
        $headers .= "Connection: Close\r\n\r\n";
        fwrite($handle, $headers);
        fclose($handle);
    }

}
