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

require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'magictoolbox.attributeshelper.class.php');

if(!defined('MagicToolboxTemplateHelperClassLoaded')) {

    define('MagicToolboxTemplateHelperClassLoaded', true);

    class MagicToolboxTemplateHelperClass {

        static $path;
        static $options;

        static function setPath($path) {
            self::$path = $path;
        }

        static function setOptions($options) {
            self::$options = $options;
        }

        static function img($options) {
            extract($options);
            return '<img />';
        }

        static function prepareMagicScrollClass() {
            $magicscroll = self::$options->checkValue('magicscroll', 'Yes') ? ' MagicScroll' : '';
            if(!empty($magicscroll)) {
                $additionalClasses = array(
                    'default' => '',
                    'with-borders' => 'msborder'
                );
                $additionalClass = $additionalClasses[self::$options->getValue('scroll-style')];
                if(!empty($additionalClass)) $magicscroll = $magicscroll . ' ' . $additionalClass;
            }
            return $magicscroll;
        }

        static function render($name, $options = null) {
            if(func_num_args() == 1) {
                $options = $name;
                $name = self::$options->getValue('template');
            }
            extract($options);

            if(count($thumbs) > self::$options->getValue('items')) {
                $magicscroll = self::prepareMagicScrollClass();
            } else {
                $magicscroll = '';
            }

            ob_start();
            require(self::$path . DIRECTORY_SEPARATOR . preg_replace('/[^a-zA-Z0-9_]/is', '-', $name) . '.tpl.php');
            return str_replace("\n", ' ', str_replace("\r", ' ', ob_get_clean()));
        }

        static function renderStyle($css){
            $style = array();

            foreach($css as $attr => $value){
                $style[] = "$attr: $value";
            }
            return join('; ',$style);
        }

    }

}
?>
