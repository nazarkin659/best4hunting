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

class Virtuemart_MagiczoomViewProducts extends MagicToolboxLegacyView {

    function display($tpl = null) {

        //JRequest::setVar('hidemainmenu', true);

        $task = JRequest::getVar('task', false, 'get');

        $title = JText::_('COM_VIRTUEMART_MAGICZOOM_MANAGER_SETTINGS');
        switch($task) {
            case 'alternates':
                $title .= ' - Alternates settings';
                break;
            case 'hotspots':
                $title .= ' - Hotspots settings';
                break;
            default:
                $title .= ' - VirtueMart Products list';
                break;
        }
        JToolBarHelper::title($title, 'magiczoom.png');

        if($task == 'alternates') {
            JToolBarHelper::save('saveAlternates');
            JToolBarHelper::apply('applyAlternates');
        } else if($task == 'hotspots') {
            JToolBarHelper::addNew('addHotspot', 'New Hotspot');
            JToolBarHelper::save('saveHotspots');
            JToolBarHelper::apply('applyHotspots');
        } else {
            JToolBarHelper::custom('displayProducts', 'magiczoom-products', 'magiczoom-products', 'Products', false);
            JToolBarHelper::custom('displayConfig', 'magiczoom-config', 'magiczoom-config', 'Config', false);
            JToolBarHelper::custom('cleanUpProducts', 'delete', 'delete', 'Clean up', false);
            //Useful, when you delete images or products. This deletes unneeded information from Magic Zoom Plus tables.
        }
        JToolBarHelper::cancel('cancel', 'Close');

        $vm202 = false;
        if(file_exists(JPATH_COMPONENT_ADMINISTRATOR.DS.'..'.DS.'com_virtuemart'.DS.'version.php')) {
            include_once JPATH_COMPONENT_ADMINISTRATOR.DS.'..'.DS.'com_virtuemart'.DS.'version.php';
            if(!isset($shortversion)) {
                $vmVersion = new vmVersion();
                $shortversion = $vmVersion::$shortversion;
            }
            if(preg_match('#\s(\d+(?:\.\d+)*)#is', $shortversion, $matches)) {
                if(version_compare($matches[1], '2.0.2', '>=')) {
                    $vm202 = true;
                }
            }
        }

        $this->category = JRequest::getVar('category', false, 'post');
        $this->productId = JRequest::getVar('id', false, 'get');
        $this->targetImageId = JRequest::getVar('target', false, 'get');

        $lang = JComponentHelper::getParams('com_languages')->get('site', 'en-GB');
        $lang = strtolower(strtr($lang, '-', '_'));

        $database = JFactory::getDBO();

        $table = ($vm202 ? 'l' : 'p');

        if(!$this->productId) {
            $query = "SELECT c.virtuemart_category_id, c.category_name, cc.category_parent_id ".
                     "FROM #__virtuemart_categories c, #__virtuemart_category_categories cc ".
                     "WHERE c.virtuemart_category_id = cc.category_child_id";

            if($vm202) {
                $query = "SELECT c.`virtuemart_category_id`, l.`category_name`, c.`ordering` ".
                          "FROM `#__virtuemart_categories_{$lang}` l ".
                          "JOIN `#__virtuemart_categories` AS c using (`virtuemart_category_id`) ".
                          "LEFT JOIN `#__virtuemart_category_categories` AS cc ON l.`virtuemart_category_id` = cc.`category_child_id`";
            }

            $database->setQuery($query);
            $database->query();
            $categories = $database->loadObjectList();

            $this->categories = array(0 => 'All');
            if(!empty($categories))
            foreach($categories as $cat) {
                $this->categories[$cat->virtuemart_category_id] = $cat->category_name;
            }

            $perPage = 10;
            $this->from = JRequest::getVar('from', 0, 'get');
            $this->from = max(array(0, floor($this->from/$perPage)*$perPage));
            $this->what = JRequest::getVar('what', '', 'post');
        }

        if($this->productId) {
            $query =
                "FROM #__virtuemart_products AS p ".
                "LEFT JOIN #__virtuemart_product_medias AS pm ON p.virtuemart_product_id = pm.virtuemart_product_id ".
                "LEFT JOIN #__virtuemart_medias AS m ON pm.virtuemart_media_id = m.virtuemart_media_id ".
                "LEFT JOIN #__virtuemart_product_categories AS c ON p.virtuemart_product_id = c.virtuemart_product_id ".
                "LEFT JOIN #__virtuemart_magiczoom_product_files as mpf ON mpf.file_id = m.virtuemart_media_id ";
        } else {
            $query =
                "FROM #__virtuemart_products AS p ".
                "LEFT JOIN #__virtuemart_product_medias AS pm ON p.virtuemart_product_id = pm.virtuemart_product_id ".
                "JOIN (SELECT p_temp.virtuemart_product_id, MIN(pm_temp.ordering) AS min_ordering, COUNT(pm_temp.virtuemart_media_id) AS images_count ".
                        "FROM #__virtuemart_products AS p_temp ".
                        "JOIN #__virtuemart_product_medias AS pm_temp ON p_temp.virtuemart_product_id = pm_temp.virtuemart_product_id ".
                        "GROUP BY p_temp.virtuemart_product_id) AS t_temp ".
                    "ON p.virtuemart_product_id = t_temp.virtuemart_product_id AND pm.ordering = t_temp.min_ordering ".
                "LEFT JOIN #__virtuemart_medias AS m ON pm.virtuemart_media_id = m.virtuemart_media_id ".
                "LEFT JOIN #__virtuemart_product_categories AS c ON p.virtuemart_product_id = c.virtuemart_product_id ";
        }

        if($vm202) {
            $query .= "LEFT JOIN #__virtuemart_products_{$lang} AS l ON p.virtuemart_product_id = l.virtuemart_product_id ";
        }

        if($this->productId) {
            if($vm202) {
                $query .= "WHERE p.virtuemart_product_id = {$this->productId} ORDER BY pm.ordering ";
            } else {
                $query .= "WHERE p.virtuemart_product_id = {$this->productId} AND m.file_is_product_image = 1 ORDER BY pm.ordering";
            }
        } else {
            $query .= "WHERE p.product_parent_id = 0 ";
            if($this->what) {
                $query .= "AND {$table}.product_name LIKE '%".$database->getEscaped($this->what)."%' ";
            }
            if($this->category) {
                $query .= "AND c.virtuemart_category_id = ".intval($this->category)." ";
            }
            $query .= "GROUP BY p.virtuemart_product_id";
        }

        if(!$this->productId) {
            $database->setQuery("SELECT count(p.virtuemart_product_id) {$query}");
            $database->query();
            $this->productCount = $database->getNumRows();
            if(!$this->productCount) {
                $app = JFactory::getApplication();
                $app->enqueueMessage(JText::_('No record found'), 'message');
            }
        }

        if($this->productId) {
            $database->setQuery("SELECT m.file_url_thumb as thumb, m.file_url as image, p.virtuemart_product_id as id, ".
                                "{$table}.product_name as name, {$table}.product_s_desc as description, m.file_title, m.file_description, mpf.*, m.virtuemart_media_id {$query}");
            $database->query();
            if(!$database->getNumRows()) {
                $app = JFactory::getApplication();
                $app->enqueueMessage(JText::_('Have no alternates'), 'message');
            }
            
        } else {
            $database->setQuery("SELECT c.virtuemart_category_id, m.file_url_thumb as thumb, m.file_url as image, p.virtuemart_product_id as id, ".
                                "{$table}.product_name as name, {$table}.product_s_desc as description, t_temp.images_count as images_count {$query} LIMIT {$this->from},{$perPage}");
            $database->query();
        }
        $this->items = $database->loadObjectList();

        foreach($this->items as &$item) {
            if(!empty($item->thumb)) {
                $item->imageUrl = $item->thumb;
                if(!preg_match('#^https?://#ui', $item->imageUrl)) {
                    $item->imageUrl = JURI::base().'..'.DS.$item->imageUrl;
                }
            } elseif(!empty($item->image)) {
                $item->imageUrl = $item->image;
                if(!preg_match('#^https?://#ui', $item->imageUrl)) {
                    $item->imageUrl = JURI::base().'..'.DS.$item->imageUrl;
                }
            } else {
                $item->imageUrl = false;
            }
        }

        if($this->productId) {

            $this->mainImage = $this->items[0];
            if($this->targetImageId) {
                for($i = 0; $i < count($this->items); $i++) {
                    if($this->items[$i]->virtuemart_media_id == $this->targetImageId) {
                        $this->mainImage = $this->items[$i];
                        break;
                    }
                }
            }

            $mainImagePath = false;
            if(!empty($this->mainImage->thumb)) {
                $mainImagePath = $this->mainImage->thumb;
                if(!preg_match('#^https?://#ui', $this->mainImage->thumb)) {
                    $mainImagePath = JPATH_ROOT.DS.$this->mainImage->thumb;
                }
            } elseif(!empty($this->mainImage->image)) {
                $mainImagePath = $this->mainImage->image;
                if(!preg_match('#^https?://#ui', $this->mainImage->image)) {
                    $mainImagePath = JPATH_ROOT.DS.$this->mainImage->image;
                }
            }

            $maxWidth = 300;
            $maxHeight = 300;
            if(extension_loaded('gd')) {
                $dimentions = getimagesize($mainImagePath);
                if($dimentions[0] > $maxWidth) {
                    $coef = $maxWidth/$dimentions[0];
                    $dimentions[0] *= $coef;
                    $dimentions[1] *= $coef;
                }
                if($dimentions[1] > $maxHeight){
                    $coef = $maxHeight/$dimentions[1];
                    $dimentions[0] *= $coef;
                    $dimentions[1] *= $coef;
                }
                $dimentions[3] = 'width="'.$dimentions[0].'" height="'.$dimentions[1].'"';
            } else {
                $dimentions = array($maxWidth,$maxWidth,2,'width="'.$maxWidth.'" height="'.$maxHeight.'"',24,'image/jpeg');
            }
            $this->dimentions = $dimentions;

            if($task == 'alternates') {
                //NOTE: check if new images exist in vm database and create links in our base
                $insertValues = array();
                for($i = 0; $i < count($this->items); $i++) {
                    if($this->items[$i]->is_alternate === null) {
                        $insertValues[] = '('.$this->items[$i]->virtuemart_media_id.', 1)';
                        $this->items[$i]->is_alternate = '1';
                    }
                }
                if(count($insertValues)) {
                    $query = 'INSERT INTO #__virtuemart_magiczoom_product_files (file_id, is_alternate) VALUES '.join(', ',$insertValues);
                    $database->setQuery($query);
                    $database->query();
                }
            }

            if($task == 'hotspots') {
                $this->imageFiles = array();
                for($i = 0; $i < count($this->items); $i++) {
                    $this->imageFiles[$this->items[$i]->virtuemart_media_id] = $this->items[$i]->file_title.'('.basename($this->items[$i]->image).')';
                }
                $database->setQuery("SELECT * FROM #__virtuemart_magiczoom_product_hotspots WHERE product_id = {$this->productId} AND file_id ".($this->targetImageId ? "= {$this->targetImageId}" : " IS NULL"));
                $database->query();
                $this->productHotspots = $database->loadObjectList();
            }

        }

        if($task) {
            $tpl = $task;
        }

        parent::display($tpl);

    }


    function getPopupLinkHTML($link, $text, $popupWidth = 640, $popupHeight = 480, $target = '_blank', $title = '', $windowAttributes = '') {
        if($windowAttributes) {
            $windowAttributes = ','.$windowAttributes;
        }
        $attributes = "onclick=\"void window.open('{$link}', '{$target}', 'status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width={$popupWidth},height={$popupHeight},directories=no,location=no{$windowAttributes}');return false;\"";
        if($target) {
            $target = ' target="'.$target.'"';
        }
        if($title) {
            $title = ' title="'.$title.'"';
        }
        if($attributes) {
            $attributes = ' '.$attributes;
        }
        $link = str_replace('&&', '*--*', $link);
        $link = str_replace('&#', '*-*', $link);
        $link = str_replace('&amp;', '&', $link);
        $link = preg_replace('|&(?![\w]+;)|', '&amp;', $link);
        $link = str_replace('*-*', '&#', $link);
        $link = str_replace('*--*', '&&', $link);
        return '<a href="'.$link.'"'.$target.$title.$attributes.'>'.$text.'</a>';
    }

    function getPaginationHTML($count, $from = 0, $url = '', $perpage = 10, $maxpages = 5) {
        $maxpages >= 3 or $maxpages = 3;

        $from = floor($from/$perpage);

        $pages = ceil($count/$perpage);
        $pages = min(array($pages, $maxpages));

        $start = $from - floor($maxpages/2);
        $start = max(array(0, $start));

        $end = ceil($count/$perpage);
        $end = min(array($start + $pages, $end));

        //$start = $start - ($maxpages - ($end - $start));
        $start = $end - $maxpages;
        $start = max(array(0, $start));

        $onclick = 'this.blur()';

        $html = array();
        $html[] = '<ul id="pagination">';

        if($from != 0) {
            $html[] = '<li><a onclick="'.$onclick.'" href="'.$this->getPageUrl().'">&lt;&lt;</a></li>';
            $html[] = '<li><a onclick="'.$onclick.'" href="'.$this->getPageUrl(($from-1)*$perpage).'">&lt;</a></li>';
        } else {
            $html[] = '<li><span>&lt;&lt;</span></li>';
            $html[] = '<li><span>&lt;</span></li>';
        }

        for($i = $start; $i < $end;$i++)
            if($i == $from) {
                $html[] = '<li><span class="selected">'.($i+1).'</span></li>';
            } else {
                $html[] = '<li><a onclick="'.$onclick.'" href="'.$this->getPageUrl($i*$perpage).'">'.($i+1).'</a></li>';
            }

        if($from != $end-1) {
            $html[] = '<li><a onclick="'.$onclick.'" href="'.$this->getPageUrl(($from+1)*$perpage).'">&gt;</a></li>';
            $html[] = '<li><a onclick="'.$onclick.'" href="'.$this->getPageUrl((ceil($count/$perpage)-1)*$perpage).'">&gt;&gt;</a></li>';
        } else {
            $html[] = '<li><span>&gt;</span></li>';
            $html[] = '<li><span>&gt;&gt;</span></li>';
        }
        $html[] = '</ul>';

        return join("\n", $html);
    }

    function getPageUrl($from = 0, $url = '') {
        return "{$url}?option=com_virtuemart_magiczoom&view=products&from={$from}";
    }

}
