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
if (!class_exists("JPaginationJS"))
{
	class JPaginationJS extends JPaginationEx
	{
		function _item_active(&$item)
		{
			if($item->base>0)
				return "<a href='' title=\"".$item->text."\" onclick=\"javascript: document.adminForm.limitstart.value=".$item->base.";submitform();return false;\" class=\"pagenav\">".$item->text."</a>";
			else
				return "<a href='' title=\"".$item->text."\" onclick=\"javascript: document.adminForm.limitstart.value=0;submitform();return false;\" class=\"pagenav\">".$item->text."</a>";	
		}
	}
}
