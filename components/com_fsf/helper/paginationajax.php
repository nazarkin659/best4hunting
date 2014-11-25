<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;

jimport( 'joomla.html.pagination');
require_once (JPATH_SITE.DS.'components'.DS.'com_fsf'.DS.'helper'.DS.'paginationex.php');

/**
 * Pagination Class.  Provides a common interface for content pagination for the
 * Joomla! Framework
 *
 * @package 	Joomla.Framework
 * @subpackage	HTML
 * @since		1.5
 */

if (!class_exists('JPaginationAjax'))
{
	class JPaginationAjax extends JPaginationEx
	{
		function _buildDataObject()
		{
			if (empty($this->_viewall)) $this->_viewall = false;
		
			// Initialize variables
			$data = new stdClass();

			$data->all	= new JPaginationObject(JText::_("VIEW_ALL"));
			if (!$this->_viewall) {
				$data->all->base	= '0';
				$data->all->link	= FSFRoute::x("&start=");
			}

			// Set the start and previous data objects
			$data->start	= new JPaginationObject(JText::_("START"));
			$data->previous	= new JPaginationObject(JText::_("PREV"));

			if ($this->get('pages.current') > 1)
			{
				$page = ($this->get('pages.current') -2) * $this->limit;

				$page = $page == 0 ? '' : $page; //set the empty for removal from route

				$data->start->base	= '0';
				$data->start->link	= "javascript:ChangePage(0);";
				$data->previous->base	= $page;
				$data->previous->link	= "javascript:ChangePage($page);";
			}

			// Set the next and end data objects
			$data->next	= new JPaginationObject(JText::_("NEXT"));
			$data->end	= new JPaginationObject(JText::_("END"));

			if ($this->get('pages.current') < $this->get('pages.total'))
			{
				$next = $this->get('pages.current') * $this->limit;
				$end  = ($this->get('pages.total') -1) * $this->limit;

				$data->next->base	= $next;
				$data->next->link	= "javascript:ChangePage($next);";
				$data->end->base	= $end;
				$data->end->link	= "javascript:ChangePage($end);";
			}

			$data->pages = array();
			$stop = $this->get('pages.stop');
			for ($i = $this->get('pages.start'); $i <= $stop; $i ++)
			{
				$offset = ($i -1) * $this->limit;

				$offset = $offset == 0 ? '' : $offset;  //set the empty for removal from route

				$data->pages[$i] = new JPaginationObject($i);
				if ($i != $this->get('pages.current') || $this->_viewall)
				{
					$data->pages[$i]->base	= $offset;
					$data->pages[$i]->link	= "javascript:ChangePage($offset);";
				}
			}
			return $data;
		}
	
		function getLimitBox()
		{
			if (empty($this->_viewall)) $this->_viewall = false;
			$mainframe = JFactory::getApplication();

			// Initialize variables
			$limits = array ();

			// Make the option list
			for ($i = 5; $i <= 30; $i += 5) {
				$limits[] = JHTML::_('select.option', "$i");
			}
			$limits[] = JHTML::_('select.option', '50');
			$limits[] = JHTML::_('select.option', '100');
			$limits[] = JHTML::_('select.option', '0', JText::_("ALL"));

			$selected = $this->_viewall ? 0 : $this->limit;

			// Build the select list
			$html = JHTML::_('select.genericlist',  $limits, 'limit_base', 'class="inputbox" size="1" onchange="ChangePageCount(this.value)"', 'value', 'text', $selected);
		
			return $html;
		}
	}
}
