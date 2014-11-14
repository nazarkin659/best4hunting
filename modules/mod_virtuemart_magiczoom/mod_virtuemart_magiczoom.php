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

//NOTE: this file is included in JModuleHelper::renderModule function

//ini_set('display_errors', true );
//error_reporting(E_ALL & ~E_NOTICE);

defined( '_JEXEC' ) or die( 'Restricted access' );

defined('DS') or define('DS', DIRECTORY_SEPARATOR);

//NOTE: is this VirtueMart component page?
if(!class_exists('vmVersion')) return;

//$vmXML = file_get_contents(JPATH_ADMINISTRATOR.'/components/com_virtuemart/virtuemart.xml');
//$vmVersion = preg_replace('#^.*?<version[^>]*>(.*?)<\/version>.*#', '$1', $vmXML);

JDispatcher::getInstance()->register('onAfterRender', 'onAfterRenderMagicZoomForVirtueMartHandler');

global $magiczoomSupportedBlocks;
$magiczoomSupportedBlocks = array('default', 'browse', 'details');

if(!defined('MAGICTOOLBOX_JURI_BASE')) {
    $url = JURI::base(true);//NOTE: without / at the end
    //NOTE: JURI::base() return URI according to $live_site variable in configuration
    //      this leads to problem with wrong protocol prefix (http/https)
    //      so this is a fix
    if(empty($_SERVER['HTTPS']) || (strtolower($_SERVER['HTTPS']) == 'off')) {
        $url = preg_replace('/^https:/i', 'http:', $url);
    } else {
        $url = preg_replace('/^http:/i', 'https:', $url);
    }
    define('MAGICTOOLBOX_JURI_BASE', $url);
}

function onAfterRenderMagicZoomForVirtueMartHandler() {

    global $magiczoomSupportedBlocks;

    $view = trim(JRequest::getVar('view', ''));
    if(empty($view)) $view = trim(JRequest::getVar('view', '', 'get'));
    //category, productdetails

    //$page = trim(JRequest::getVar('page', ''));
    //if(empty($page)) $page = trim(JRequest::getVar('page', '', 'get'));

    $option = trim(JRequest::getVar('option', '', 'post'));
    if(empty($option)) $option = trim(JRequest::getVar('option', '', 'get'));

    $tool = getToolMagicZoomForVirtueMart();

    $page = '';
    if($view == 'category' || $option == 'com_customfilters') $page = 'browse';
    //if($view == 'productdetails'/* || $view == 'shop.cart'*/) $page = 'details';
    if($view == 'productdetails' || !empty($_REQUEST['virtuemart_product_id'])/* || $view == 'shop.cart'*/) $page = 'details';

    if(!in_array($page, $magiczoomSupportedBlocks) || $tool->params->checkValue('enable-effect', 'No', $page)) {
        return true;
    }
    $tool->params->setProfile($page);

    $contents = JResponse::getBody();//JResponse::toString();
    $showHeaders = false;

    switch($page) {
        case 'details'://product

            $virtuemart_product_id = JRequest::getVar('virtuemart_product_id', 0);
            if(is_array($virtuemart_product_id) and count($virtuemart_product_id) > 0) {
                $virtuemart_product_id = (int)$virtuemart_product_id[0];
            } else {
                $virtuemart_product_id = (int)$virtuemart_product_id;
            }

            if(method_exists('VmModel', 'getModel')) {
                //NOTE: for VirtueMart 2.0.2 and above
                $productModel = VmModel::getModel('product');
            } else {
                //NOTE: for VirtueMart 2.0.0
                $productModel = new VirtueMartModelProduct();
            }
            $product = $productModel->getProduct($virtuemart_product_id, true, true, true, 1);

            if(empty($product->images)) {
                $productModel->addImages($product);
            }

            if(empty($product->images)) return true;
            $image = $product->images[0];

            $useOriginal = $tool->params->checkValue('use-original-vm-thumbnails', 'Yes');

            if($useOriginal) {
                $img = MAGICTOOLBOX_JURI_BASE.'/'.$image->file_url;
                $thumb = MAGICTOOLBOX_JURI_BASE.'/'.$image->file_url;
            } else {
                $thumb = getImageMagicZoomForVirtueMart($image->file_url, 'thumb', $virtuemart_product_id);
                if(empty($thumb)) return true;
                $img = getImageMagicZoomForVirtueMart($image->file_url, 'original', $virtuemart_product_id);
            }


            $title = $product->product_name;
            //$title = empty($product->product_name) ? $image->file_title : $product->product_name;
            $alt = $image->file_title;

            $mainHtml = $tool->getMainTemplate(array(
                'id' => "Product{$virtuemart_product_id}",
                'group' => "Product{$virtuemart_product_id}",
                'img' => $img,
                'thumb' => $thumb,
                'title' => $title,
                'alt' => $alt,
                'description' => $product->product_desc,
                'shortDescription' => $product->product_s_desc,

            ));
            $selectorsHtml = array();

            if(count($product->images) > 1) {
                foreach($product->images as $image) {
                    if($useOriginal) {
                        $img = MAGICTOOLBOX_JURI_BASE.'/'.$image->file_url;
                        $thumb = MAGICTOOLBOX_JURI_BASE.'/'.$image->file_url;
                        $selector = MAGICTOOLBOX_JURI_BASE.'/'.$image->file_url;
                    } else {
                        $thumb = getImageMagicZoomForVirtueMart($image->file_url, 'thumb', $virtuemart_product_id);
                        if(empty($thumb)) continue;
                        $img = getImageMagicZoomForVirtueMart($image->file_url, 'original', $virtuemart_product_id);
                        $selector = getImageMagicZoomForVirtueMart($image->file_url, 'selector', $virtuemart_product_id);
                    }
                    $title = empty($image->file_title) ? $product->product_name : $image->file_title;
                    $title = $tool->params->checkValue('use-individual-titles', 'Yes') ? $title : $product->product_name;

                    $selectorsHtml[] = $tool->getSelectorTemplate(array(
                        'id' => "Product{$virtuemart_product_id}",
                        'img' => $img,
                        'medium' => $thumb,
                        'thumb' => $selector,
                        'title' => $title,
                        'width' => $useOriginal ? $tool->params->getValue('selector-max-width') : '',
                        'height' => $useOriginal ? $tool->params->getValue('selector-max-height') : '',
                    ));
                }
            }


            $html = MagicToolboxTemplateHelperClass::render(array(
                'main' => $mainHtml,
                'thumbs' => $selectorsHtml,
                'pid' => $virtuemart_product_id,
            ));

            //$pattern = '<img\b[^>]*?src="[^"]*?(?:'.$product->images[0]->file_url.'|'.$product->images[0]->file_url_thumb.')"[^>]*+>';
            $file_url = preg_quote($product->images[0]->file_url,'-()/');
            $pattern = '<img\b[^>]*?src="[^"]*?'.$file_url.'"[^>]*+>';

            $pattern = '(<a\b[^>]*+>[^<]*+)?'.
                        $pattern.
                       '(?(1)[^<]*+</a>)';

            // $matches = array();
            // preg_match_all("#{$pattern}#is", $contents, $matches, PREG_SET_ORDER);
            // debug_log($matches);

            $matched = false;
            if(preg_match("#{$pattern}#is", $contents)) {
                $contents = preg_replace('#'.$pattern.'#is', $html, $contents, 1);
                $matched = true;
            } else {
                $pattern = '<img\b[^>]*?src="[^"]*?'.$product->images[0]->file_url_thumb.'"[^>]*+>';
                $pattern = '(<a\b[^>]*+>[^<]*+)?'.
                            $pattern.
                           '(?(1)[^<]*+</a>)';
                $matches = array();
                preg_match_all("#{$pattern}#is", $contents, $matches, PREG_SET_ORDER);
                foreach($matches as $match) {
                    if(strpos($match[0], 'featuredProductImage') !== false) {
                        continue;
                    }
                    $contents = str_replace($match[0], $html, $contents);
                    $matched = true;
                    break;
                }
            }

            if(!$matched) break;

            //NOTE: cut selectors
            $pattern =  '<div\b[^>]*?\bclass="[^"]*?\b(?:additional(?:-|_)images|thumbnailListContainer)\b[^"]*+"[^>]*+>'.
                        '('.
                        '(?:'.
                           '[^<]++'.
                           '|'.
                           '<(?!/?div\b|!--)'.
                           '|'.
                           '<!--.*?-->'.
                           '|'.
                           '<div\b[^>]*+>'.
                               '(?1)'.
                           '</div\s*+>'.
                        ')*+'.
                        ')'.
                        '</div\s*+>';
            $contents = preg_replace('#'.$pattern.'#is', '', $contents, 1);

            $showHeaders = true;

        break;
        case 'browse'://category

            backupBlocksmagiczoomForVirtueMart($contents);

            $virtuemart_category_id = (int)(JRequest::getVar('virtuemart_category_id', 0));

            if(method_exists('VmModel', 'getModel')) {
                //NOTE: for VirtueMart 2.0.2 and above
                $productModel = VmModel::getModel('product');
            } else {
                //NOTE: for VirtueMart 2.0.0
                $productModel = new VirtueMartModelProduct();
            }
            $products = $productModel->getProductsInCategory($virtuemart_category_id);
            //$productModel->addImages($products, 1);

            $useOriginal = $tool->params->checkValue('use-original-vm-thumbnails', 'Yes');

            $useLink = $tool->params->checkValue('link-to-product-page', 'Yes');

            foreach($products as $product) {

                $virtuemart_product_id = $product->virtuemart_product_id;

                if(empty($product->images)) continue;
                $image = $product->images[0];

                if($useOriginal) {
                    $img = MAGICTOOLBOX_JURI_BASE.'/'.$image->file_url;
                    $thumb = MAGICTOOLBOX_JURI_BASE.'/'.$image->file_url_thumb;
                } else {
                    $thumb = getImageMagicZoomForVirtueMart($image->file_url, 'thumb', $virtuemart_product_id);
                    if(empty($thumb)) continue;
                    $img = getImageMagicZoomForVirtueMart($image->file_url, 'original', $virtuemart_product_id);
                }

                $title = $product->product_name;
                //$title = empty($product->product_name) ? $image->file_title : $product->product_name;
                $alt = $image->file_title;

                $html = $tool->getMainTemplate(array(
                    'id' => "Product{$virtuemart_product_id}",
                    'group' => "Category{$virtuemart_category_id}",
                    'img' => $img,
                    'thumb' => $thumb,
                    'title' => $title,
                    'alt' => $alt,
                    'description' => $product->product_desc,
                    'shortDescription' => $product->product_s_desc,
                    'link' => $useLink ? $product->link : false,
                ));

                //$pattern = '<img\b[^>]*?src="[^"]*?(?:'.$product->images[0]->file_url.'|'.$product->images[0]->file_url_thumb.')"[^>]*+>';
                $pattern = '<img\b[^>]*?src="[^"]*?'.$product->images[0]->file_url_thumb.'"[^>]*+>';
                $pattern = '(<a\b[^>]*+>[^<]*+)?'.
                            $pattern.
                           '(?(1)[^<]*+</a>)';

                // $matches = array();
                // preg_match_all("#{$pattern}#is", $contents, $matches, PREG_SET_ORDER);
                // debug_log($matches);

                $contents = preg_replace('#'.$pattern.'#is', $html, $contents, 1);

            }

            backupBlocksmagiczoomForVirtueMart($contents);

            $showHeaders = true;

        break;

    }

    if(!$showHeaders) return true;

    $tool->params->resetProfile();
    $url = JURI::base(true).'/media/mod_virtuemart_magiczoom';
    $headers = array();
    $headers[] = "\n".$tool->getHeadersTemplate($url);
    if($page == 'details') {
        if($tool->params->checkValue('magicscroll', 'Yes', 'details')) {
            $srollTool = getToolMagicZoomForVirtueMart(true);
            $headers[] = "\n".$srollTool->getHeadersTemplate($url);
        }


    }
    $headers[] = "\n<link type=\"text/css\" href=\"{$url}/module.css\" rel=\"stylesheet\" media=\"screen\" />\n";
    $contents = preg_replace('/<\/head>/is', implode($headers).'</head>', $contents, 1);

    JResponse::setBody($contents);

    return true;
}

function backupBlocksMagicZoomForVirtueMart(&$contents) {
    //NOTE: backup latest/featured/random blocks
    static $backups = null;
    $modules = array('virtuemart_latestprod', 'virtuemart_featureprod', 'virtuemart_randomprod');
    if($backups == null) {
        $backups = array();
        foreach($modules as $name) {
            $module = JModuleHelper::getModule($name);
            if(!$module) continue;
            $placeholder = 'MAGICTOOLBOX_'.strtoupper($name).'_PLACEHOLDER';
            if(empty($module->content)) continue;
            $backups[$placeholder] = $module->content;
            $contents = str_replace($module->content, $placeholder, $contents);
        }
    } else {
        if(!empty($backups)) {
            foreach($backups as $placeholder => $value) {
                $contents = str_replace($placeholder, $value, $contents);
            }
        }
        $backups = null;
    }
}

function getToolMagicZoomForVirtueMart($getScrollTool = false) {
    static $mainCoreClass = null;
    static $scrollCoreClass = null;
    global $magiczoomSupportedBlocks;
    if($mainCoreClass === null) {
        require_once(dirname(__FILE__).DS.'classes'.DS.'magiczoom.module.core.class.php');
        $mainCoreClass = new MagicZoomModuleCoreClass();
        $database = JFactory::getDBO();
        $database->setQuery("SELECT profile, name, value FROM `#__virtuemart_magiczoom_config`");
        $results = $database->loadAssocList();
        if(!empty($results)) {
            foreach($results as $row) {
                $mainCoreClass->params->setValue($row['name'], $row['value'], $row['profile']);
            }
        }
        if($mainCoreClass->params->checkValue('magicscroll', 'Yes', 'details')) {
            require_once(dirname(__FILE__).DS.'classes'.DS.'magicscroll.module.core.class.php');
            $scrollCoreClass = new MagicScrollModuleCoreClass();
            $scrollCoreClass->params->setScope('MagicScroll');
            //$scrollCoreClass->params->appendParams($mainCoreClass->params->getParams('details'));
            foreach($scrollCoreClass->params->getParams() as $id => $param) {
                $value = $mainCoreClass->params->getValue($id, 'details');
                if($value !== null) {
                    $scrollCoreClass->params->setValue($id, $value);
                }
            }
            $scrollCoreClass->params->setValue('direction', $mainCoreClass->params->checkValue('template', array('left', 'right')) ? 'bottom' : 'right');
        }

        require_once(dirname(__FILE__).DS.'classes'.DS.'magictoolbox.templatehelper.class.php');
        MagicToolboxTemplateHelperClass::setPath(dirname(__FILE__).DIRECTORY_SEPARATOR.'templates');
        MagicToolboxTemplateHelperClass::setOptions($mainCoreClass->params);
    }
    return $getScrollTool ? $scrollCoreClass : $mainCoreClass;
}

function getImageMagicZoomForVirtueMart($file, $type = 'original', $id = null) {
    static $imageHelper = null;

    if(empty($file)) return '';

    $file = str_replace(array('%20', '/'), array(' ', DS), $file);

    if(!is_file(JPATH_SITE/*JPATH_ROOT*/.DS.$file)) return '';

    if($imageHelper === null) {
        require_once(dirname(__FILE__).DS.'classes'.DS.'magictoolbox.imagehelper.class.php');
        $tool = getToolMagicZoomForVirtueMart();
        $imageHelper = new MagicToolboxImageHelperClass(JPATH_SITE,
                                                        DS.'media'.DS.'mod_virtuemart_magiczoom'.DS.'magictoolbox_cache',
                                                        $tool->params,
                                                        null,
                                                        MAGICTOOLBOX_JURI_BASE);
    }

    return $imageHelper->create(DS.$file, $type, $id);
}

function getImageSizeMagicZoomForVirtueMart($file, $type = 'thumb') {

    $tool = getToolMagicZoomForVirtueMart();

    $maxWidth = intval(str_replace('px', '', $tool->params->getValue($type.'-max-width')));
    $maxHeight = intval(str_replace('px', '', $tool->params->getValue($type.'-max-height')));

    $file = JPATH_SITE/*JPATH_ROOT*/.DS.str_replace(array('%20', '/'), array(' ', DS), $file);
    $size = getimagesize($file);
    $originalWidth = $size[0];
    $originalHeight = $size[1];

    if(!$maxWidth && !$maxHeight) {
        return (object)array('width' => $originalWidth, 'height' => $originalHeight);
    } elseif(!$maxWidth) {
        $maxWidth = ($maxHeight * $originalWidth) / $originalHeight;
    } elseif(!$maxHeight) {
        $maxHeight = ($maxWidth * $originalHeight) / $originalWidth;
    }
    $sizeDepends = $originalWidth/$originalHeight;
    $placeHolderDepends = $maxWidth/$maxHeight;
    if($sizeDepends > $placeHolderDepends) {
        $newWidth = $maxWidth;
        $newHeight = $originalHeight * ($maxWidth / $originalWidth);
    } else {
        $newWidth = $originalWidth * ($maxHeight / $originalHeight);  
        $newHeight = $maxHeight;
    }
    return (object)array('width' => round($newWidth), 'height' => round($newHeight));

}
