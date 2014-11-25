<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;

jimport( 'joomla.application.component.view' );


class FsfsViewFaqs extends JViewLegacy
{
 
    function display($tpl = null)
    {
        JToolBarHelper::title( JText::_("FAQ_MANAGER"), 'fsf_faqs' );
		
		JToolBarHelper::custom('autosort','autosort', 'autosort', 'Auto Sort', false);
		JToolBarHelper::divider();
        JToolBarHelper::deleteList();
        JToolBarHelper::editList();
        JToolBarHelper::addNew();
        JToolBarHelper::cancel('cancellist');
		FSFAdminHelper::DoSubToolbar();

        $lists =  $this->get('Lists');
        $this->assignRef( 'data', $this->get('Data') );
        $this->assignRef( 'pagination', $this->get('Pagination'));

		$query = 'SELECT id, title FROM #__fsf_faq_cat ORDER BY ordering';

		$db	= & JFactory::getDBO();
		$categories[] = JHTML::_('select.option', '0', JText::_("SELECT_CATEGORY"), 'id', 'title');
		$db->setQuery($query);
		$categories = array_merge($categories, $db->loadObjectList());

		$lists['cats'] = JHTML::_('select.genericlist',  $categories, 'faq_cat_id', 'class="inputbox" size="1" onchange="document.adminForm.submit( );"', 'id', 'title', $lists['faq_cat_id']);

		$categories = array();
		$categories[] = JHTML::_('select.option', '-1', JText::_("IS_PUBLISHED"), 'id', 'title');
		$categories[] = JHTML::_('select.option', '1', JText::_("PUBLISHED"), 'id', 'title');
		$categories[] = JHTML::_('select.option', '0', JText::_("UNPUBLISHED"), 'id', 'title');
		$lists['published'] = JHTML::_('select.genericlist',  $categories, 'ispublished', 'class="inputbox" size="1" onchange="document.adminForm.submit( );"', 'id', 'title', $lists['ispublished']);


		$this->assignRef( 'lists', $lists );

        parent::display($tpl);
    }
}


