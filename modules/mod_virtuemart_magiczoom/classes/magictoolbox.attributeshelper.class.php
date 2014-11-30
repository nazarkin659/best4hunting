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

if(!defined('MagicToolboxAttributesHelperLoaded')) {

    define('MagicToolboxAttributesHelperLoaded', true);

    class MagicToolboxAttributesHelper {

        function check($attributes) {
            foreach($attributes as $name => $value) {
                $value = htmlspecialchars(htmlspecialchars_decode($value));
                switch($key) {
                    case 'width':
                    case 'height':
                        $value = intval($value) . 'px';
                        break;
                }
            }
        }

        function output($attributes, $autoCheck = true) {
            if($autoCheck) {
                $attributes = self::check($attributes);
            }
            $output = array();
            foreach($attributes as $name => $value) {
                $output[] = $name . '"' . $value . '"';
            }
            return implode(' ', $output);
        }

    }

}

?>
