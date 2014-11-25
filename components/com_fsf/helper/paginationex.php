<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;

jimport( 'joomla.html.pagination');

/**
 * Pagination Class.  Provides a common interface for content pagination for the
 * Joomla! Framework
 *
 * @package 	Joomla.Framework
 * @subpackage	HTML
 * @since		1.5
 */

if (!class_exists('JPaginationEx'))
{
	class JPaginationEx extends JPagination
	{
		var $skinstyle = 0;
	
		function __construct($total, $limitstart, $limit)
		{
			$this->skinstyle = FSF_Settings::get('skin_style');
		
			parent::__construct($total, $limitstart, $limit);	
		}
	
		function getPagesLinks()
		{
			$mainframe = JFactory::getApplication();
		 
			$lang =& JFactory::getLanguage();

			// Build the page navigation list
			$data = $this->_buildDataObject();

			$list = array();

			$itemOverride = false;
			$listOverride = false;

			$chromePath = JPATH_THEMES.DS.$mainframe->getTemplate().DS.'html'.DS.'pagination.php';
			if ($this->skinstyle == 1 && file_exists($chromePath))
			{
				require_once ($chromePath);
				if (function_exists('pagination_item_active') && function_exists('pagination_item_inactive')) {
					$itemOverride = true;
				}
				if (function_exists('pagination_list_render')) {
					$listOverride = true;
				}
			}

			// Build the select list
			if ($data->all->base !== null) {
				$list['all']['active'] = true;
				$list['all']['data'] = ($itemOverride) ? pagination_item_active($data->all) : $this->_item_active($data->all);
			} else {
				$list['all']['active'] = false;
				$list['all']['data'] = ($itemOverride) ? pagination_item_inactive($data->all) : $this->_item_inactive($data->all);
			}

			if ($data->start->base !== null) {
				$list['start']['active'] = true;
				$list['start']['data'] = ($itemOverride) ? pagination_item_active($data->start) : $this->_item_active($data->start);
			} else {
				$list['start']['active'] = false;
				$list['start']['data'] = ($itemOverride) ? pagination_item_inactive($data->start) : $this->_item_inactive($data->start);
			}
			if ($data->previous->base !== null) {
				$list['previous']['active'] = true;
				$list['previous']['data'] = ($itemOverride) ? pagination_item_active($data->previous) : $this->_item_active($data->previous);
			} else {
				$list['previous']['active'] = false;
				$list['previous']['data'] = ($itemOverride) ? pagination_item_inactive($data->previous) : $this->_item_inactive($data->previous);
			}

			$list['pages'] = array(); //make sure it exists
			foreach ($data->pages as $i => $page)
			{
				if ($page->base !== null) {
					$list['pages'][$i]['active'] = true;
					$list['pages'][$i]['data'] = ($itemOverride) ? pagination_item_active($page) : $this->_item_active($page);
				} else {
					$list['pages'][$i]['active'] = false;
					$list['pages'][$i]['data'] = ($itemOverride) ? pagination_item_inactive($page) : $this->_item_inactive($page);
				}
			}

			if ($data->next->base !== null) {
				$list['next']['active'] = true;
				$list['next']['data'] = ($itemOverride) ? pagination_item_active($data->next) : $this->_item_active($data->next);
			} else {
				$list['next']['active'] = false;
				$list['next']['data'] = ($itemOverride) ? pagination_item_inactive($data->next) : $this->_item_inactive($data->next);
			}
			if ($data->end->base !== null) {
				$list['end']['active'] = true;
				$list['end']['data'] = ($itemOverride) ? pagination_item_active($data->end) : $this->_item_active($data->end);
			} else {
				$list['end']['active'] = false;
				$list['end']['data'] = ($itemOverride) ? pagination_item_inactive($data->end) : $this->_item_inactive($data->end);
			}

			if($this->total > $this->limit){
				return ($listOverride) ? pagination_list_render($list) : $this->_list_render($list);
			}
			else{
				return '';
			}
		}
	
		function getListFooter()
		{
			$mainframe = JFactory::getApplication();

			$list = array();
			$list['limit']			= $this->limit;
			$list['limitstart']		= $this->limitstart;
			$list['total']			= $this->total;
			$list['limitfield']		= $this->getLimitBox();
			$list['pagescounter']	= $this->getPagesCounter();
			$list['pageslinks']		= $this->getPagesLinks();
	
			if ($this->total < 5)
				return "";
		
			$chromePath		= JPATH_THEMES.DS.$mainframe->getTemplate().DS.'html'.DS.'pagination.php';
			if ($this->skinstyle == 1 && file_exists( $chromePath ))
			{
				require_once( $chromePath );
				if (function_exists( 'pagination_list_footer' )) {
					return pagination_list_footer( $list );
				}
			}
			return $this->_list_footer($list);
		}

		function _list_footer($list)
		{
			// Initialize variables
			$html = "<div class=\"fsf_list-footer\">\n";

			$html .= "\n<div class=\"fsf_limit\">".JText::_("DISPLAY_NUM").$list['limitfield']."</div>";
			$html .= "\n<div class=\"fsf_pagination\">&nbsp;".$list['pageslinks']."</div>";
			$html .= "\n<div class=\"fsf_counter\">&nbsp;".$list['pagescounter']."</div>";

			$html .= "\n<input type=\"hidden\" name=\"limitstart\" value=\"".$list['limitstart']."\" />";
			$html .= "\n</div>";

			return $html;
		}

		function _list_render($list)
		{
			// Initialize variables
			$html = null;

			// Reverse output rendering for right-to-left display
			$html .= '&lt;&lt; ';
			$html .= $list['start']['data'];
			$html .= ' &lt; ';
			$html .= $list['previous']['data'];
			foreach( $list['pages'] as $page ) {
				$html .= ' '.$page['data'];
			}
			$html .= ' '. $list['next']['data'];
			$html .= ' &gt;';
			$html .= ' '. $list['end']['data'];
			$html .= ' &gt;&gt;';

			return $html;
		}

		function _item_active(&$item)
		{
			$mainframe = JFactory::getApplication();
			if ($mainframe->isAdmin())
			{
				if($item->base>0)
					return "<a title=\"".$item->text."\" onclick=\"javascript: document.adminForm.limitstart.value=".$item->base."; submitform();return false;\">".$item->text."</a>";
				else
					return "<a title=\"".$item->text."\" onclick=\"javascript: document.adminForm.limitstart.value=0; submitform();return false;\">".$item->text."</a>";
			} else {
				
				$item->link = str_replace("limitstart", "start", $item->link);
				
				return "<a title=\"".$item->text."\" href=\"".$item->link."\" class=\"pagenav\" limit='{$item->base}'>".$item->text."</a>";
			}
		}

		function _item_inactive(&$item)
		{
			$mainframe = JFactory::getApplication();
			if ($mainframe->isAdmin()) {
				return "<span>".$item->text."</span>";
			} else {
				return "<span class=\"pagenav\">".$item->text."</span>";
			}
		}

	}

}