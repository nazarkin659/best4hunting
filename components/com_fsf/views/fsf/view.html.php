<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;

jimport( 'joomla.application.component.view');
jimport('joomla.utilities.date');

class FsfViewFsf extends JViewLegacy
{
    function display($tpl = null)
    {
        $mainframe = JFactory::getApplication();
		$option = JRequest::getVar('option');
		if ($option == "com_fst")
		{
			$link = FSFRoute::_('index.php?option=com_fsf&view=test',false);
		} else if ($option == "com_fsf")
		{
			$link = FSFRoute::_('index.php?option=com_fsf&view=faq',false);
		} else {
			$link = FSFRoute::_('index.php?option=com_fsf&view=main',false);
		}
		$mainframe->redirect($link);
    }
}

