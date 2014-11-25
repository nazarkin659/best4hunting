<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;

jimport( 'joomla.application.component.view' );



class FsfsViewGlossary extends JViewLegacy
{

	function display($tpl = null)
	{
		$mainframe = JFactory::getApplication();
		$glossary		=& $this->get('Data');
		$isNew		= ($glossary->id < 1);

		$text = $isNew ? JText::_("NEW") : JText::_("EDIT");
		JToolBarHelper::title(   JText::_("GLOSSARY_ITEM").': <small><small>[ ' . $text.' ]</small></small>', 'fsf_glossary' );
		JToolBarHelper::save();
		if ($isNew)  {
			JToolBarHelper::cancel();
		} else {
			// for existing items the button is renamed `close`
			JToolBarHelper::cancel( 'cancel', 'Close' );
		}
		FSFAdminHelper::DoSubToolbar();

		$this->assignRef('glossary',		$glossary);

		parent::display($tpl);
	}
}


