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

//NOTE: Import joomla view library
jimport('joomla.application.component.view');

if(!defined('MAGICTOOLBOX_LEGACY_VIEW_DEFINED')) {
    define('MAGICTOOLBOX_LEGACY_VIEW_DEFINED', true);
    if(JVERSION_256) {
        class MagicToolboxLegacyView extends JViewLegacy {}
    } else {
        class MagicToolboxLegacyView extends JView {}
    }
}

class Virtuemart_MagiczoomViewDefault extends MagicToolboxLegacyView {

    function display($tpl = null) {

        //JRequest::setVar('hidemainmenu', true);

        $this->profile = JRequest::getVar('profile', false, 'get');
        $this->profiles = array('default' => 'Default values', 'browse' => 'Category browse page', 'details' => 'Product details page');

        $title = JText::_('COM_VIRTUEMART_MAGICZOOM_MANAGER_SETTINGS');
        if($this->profile) {
            $title .= ' - '.$this->profiles[$this->profile];
        }
        JToolBarHelper::title($title, 'magiczoom.png');

        if($this->profile) {
            JToolBarHelper::save('save');//Save & Close
            JToolBarHelper::apply('apply');//Save
        }
        JToolBarHelper::cancel('cancel', 'Close');//Close

        require_once(JPATH_COMPONENT.DS.'virtuemart_module'.DS.'site'.DS.'classes'.DS.'magiczoom.module.core.class.php');
        $this->tool = new MagicZoomModuleCoreClass();
        $database = JFactory::getDBO();
        $database->setQuery("SELECT `profile`, `name`, `value` FROM `#__virtuemart_magiczoom_config`");
        $results = $database->loadAssocList();
        if(!empty($results)) {
            foreach($results as $row) {
                $this->tool->params->setValue($row['name'], $row['value'], $row['profile']);
            }
        }

        $this->imageUrl = JURI::root().'media/com_virtuemart_magiczoom/images/';
        $this->paramsMap = array(
			'default' => array(
				'General' => array(
					'enable-effect',
					'template',
					'magicscroll',
				),
				'Positioning and Geometry' => array(
					'thumb-max-width',
					'thumb-max-height',
					'zoom-width',
					'zoom-height',
					'zoom-position',
					'zoom-align',
					'zoom-distance',
					'square-images',
				),
				'Effects' => array(
					'opacity',
					'opacity-reverse',
					'zoom-fade',
					'zoom-window-effect',
					'zoom-fade-in-speed',
					'zoom-fade-out-speed',
					'fps',
					'smoothing',
					'smoothing-speed',
				),
				'Multiple images' => array(
					'selector-max-width',
					'selector-max-height',
					'selectors-margin',
					'use-individual-titles',
					'selectors-change',
					'selectors-class',
					'preload-selectors-small',
					'preload-selectors-big',
					'selectors-effect',
					'selectors-effect-speed',
					'selectors-mouseover-delay',
				),
				'Initialization' => array(
					'initialize-on',
					'click-to-activate',
					'click-to-deactivate',
					'show-loading',
					'loading-msg',
					'loading-opacity',
					'loading-position-x',
					'loading-position-y',
					'entire-image',
				),
				'Title and Caption' => array(
					'show-title',
				),
				'Miscellaneous' => array(
					'link-to-product-page',
					'use-original-vm-thumbnails',
					'show-message',
					'message',
					'right-click',
					'imagemagick',
					'image-quality',
					'use-original-file-names',
				),
				'Zoom mode' => array(
					'disable-zoom',
					'always-show-zoom',
					'drag-mode',
					'move-on-click',
					'x',
					'y',
					'preserve-position',
					'fit-zoom-window',
				),
				'Watermark' => array(
					'watermark',
					'watermark-max-width',
					'watermark-max-height',
					'watermark-opacity',
					'watermark-position',
					'watermark-offset-x',
					'watermark-offset-y',
				),
				'Hint' => array(
					'hint',
					'hint-text',
					'hint-position',
					'hint-opacity',
				),
				'Scroll' => array(
					'scroll-style',
					'show-image-title',
					'loop',
					'speed',
					'width',
					'height',
					'item-width',
					'item-height',
					'step',
					'items',
				),
				'Scroll Arrows' => array(
					'arrows',
					'arrows-opacity',
					'arrows-hover-opacity',
				),
				'Scroll Slider' => array(
					'slider-size',
					'slider',
				),
				'Scroll effect' => array(
					'duration',
				),
			),
			'browse' => array(
				'General' => array(
					'enable-effect',
				),
				'Positioning and Geometry' => array(
					'thumb-max-width',
					'thumb-max-height',
					'zoom-width',
					'zoom-height',
					'zoom-position',
					'zoom-align',
					'zoom-distance',
					'square-images',
				),
				'Effects' => array(
					'opacity',
					'opacity-reverse',
					'zoom-fade',
					'zoom-window-effect',
					'zoom-fade-in-speed',
					'zoom-fade-out-speed',
					'fps',
					'smoothing',
					'smoothing-speed',
				),
				'Initialization' => array(
					'initialize-on',
					'click-to-activate',
					'click-to-deactivate',
					'show-loading',
					'loading-msg',
					'loading-opacity',
					'loading-position-x',
					'loading-position-y',
					'entire-image',
				),
				'Title and Caption' => array(
					'show-title',
				),
				'Miscellaneous' => array(
					'link-to-product-page',
					'use-original-vm-thumbnails',
					'show-message',
					'message',
					'right-click',
					'imagemagick',
					'image-quality',
					'use-original-file-names',
				),
				'Zoom mode' => array(
					'disable-zoom',
					'always-show-zoom',
					'drag-mode',
					'move-on-click',
					'x',
					'y',
					'preserve-position',
					'fit-zoom-window',
				),
				'Watermark' => array(
					'watermark',
					'watermark-max-width',
					'watermark-max-height',
					'watermark-opacity',
					'watermark-position',
					'watermark-offset-x',
					'watermark-offset-y',
				),
				'Hint' => array(
					'hint',
					'hint-text',
					'hint-position',
					'hint-opacity',
				),
			),
			'details' => array(
				'General' => array(
					'enable-effect',
					'template',
					'magicscroll',
				),
				'Positioning and Geometry' => array(
					'thumb-max-width',
					'thumb-max-height',
					'zoom-width',
					'zoom-height',
					'zoom-position',
					'zoom-align',
					'zoom-distance',
					'square-images',
				),
				'Effects' => array(
					'opacity',
					'opacity-reverse',
					'zoom-fade',
					'zoom-window-effect',
					'zoom-fade-in-speed',
					'zoom-fade-out-speed',
					'fps',
					'smoothing',
					'smoothing-speed',
				),
				'Multiple images' => array(
					'selector-max-width',
					'selector-max-height',
					'selectors-margin',
					'use-individual-titles',
					'selectors-change',
					'selectors-class',
					'preload-selectors-small',
					'preload-selectors-big',
					'selectors-effect',
					'selectors-effect-speed',
					'selectors-mouseover-delay',
				),
				'Initialization' => array(
					'initialize-on',
					'click-to-activate',
					'click-to-deactivate',
					'show-loading',
					'loading-msg',
					'loading-opacity',
					'loading-position-x',
					'loading-position-y',
					'entire-image',
				),
				'Title and Caption' => array(
					'show-title',
				),
				'Miscellaneous' => array(
					'use-original-vm-thumbnails',
					'show-message',
					'message',
					'right-click',
					'imagemagick',
					'image-quality',
					'use-original-file-names',
				),
				'Zoom mode' => array(
					'disable-zoom',
					'always-show-zoom',
					'drag-mode',
					'move-on-click',
					'x',
					'y',
					'preserve-position',
					'fit-zoom-window',
				),
				'Watermark' => array(
					'watermark',
					'watermark-max-width',
					'watermark-max-height',
					'watermark-opacity',
					'watermark-position',
					'watermark-offset-x',
					'watermark-offset-y',
				),
				'Hint' => array(
					'hint',
					'hint-text',
					'hint-position',
					'hint-opacity',
				),
				'Scroll' => array(
					'scroll-style',
					'show-image-title',
					'loop',
					'speed',
					'width',
					'height',
					'item-width',
					'item-height',
					'step',
					'items',
				),
				'Scroll Arrows' => array(
					'arrows',
					'arrows-opacity',
					'arrows-hover-opacity',
				),
				'Scroll Slider' => array(
					'slider-size',
					'slider',
				),
				'Scroll effect' => array(
					'duration',
				),
			),
		);
        $this->groups = array();
        foreach($this->paramsMap as $profileId => $groups) {
            foreach($groups as $groupName => $params) {
                if(!isset($this->groups[$groupName])) $this->groups[$groupName] = array();
                $_params = array();
                foreach($params as $param) {
                    $_params[$param] = '';
                }
                $this->groups[$groupName] = array_merge($this->groups[$groupName], $_params);
            }
        }

        if($this->profile) {
            $tpl = 'edit';
        }

        parent::display($tpl);

    }

}
