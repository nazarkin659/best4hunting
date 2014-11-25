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

class FsfViewGlossary extends JViewLegacy
{
    function display($tpl = null)
    {
        $mainframe = JFactory::getApplication();
        
		JHTML::_('behavior.modal', 'a.fsf_modal');
		//JHTML::_('behavior.mootools');
        $db = JFactory::getDBO();

        $aparams = FSF_Settings::GetViewSettingsObj('glossary');
		$this->use_letter_bar = $aparams->get('use_letter_bar',0);
		
		if ($this->use_letter_bar)
		{
			$this->letters = array();
			if (FSF_Settings::get('glossary_all_letters'))
			{
				$letters = array(
					'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M',
					'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'
					);
				foreach ($letters as $letter)
					$this->letters[$letter] = 0;				
			}
			
			$qry = "SELECT UPPER(SUBSTR(word,1,1)) as letter FROM #__fsf_glossary";
			$where = array();
		
			$where[] = "published = 1";
			if (FSF_Helper::Is16())
			{
				$where[] = 'language in (' . $db->Quote(JFactory::getLanguage()->getTag()) . ',' . $db->Quote('*') . ')';
				$user = JFactory::getUser();
				$where[] = 'access IN (' . implode(',', $user->getAuthorisedViewLevels()) . ')';				
			}	
			if (count($where) > 0)
				$qry .= " WHERE " . implode(" AND ",$where);
			
			$qry .= " GROUP BY letter ORDER BY letter";
			$db->setQuery($qry);
			$letters = $db->loadObjectList();
			
			foreach ($letters as $letter)
				$this->letters[$letter->letter] = 1;
			
			if (count($this->letters) == 0)
			{
				return parent::display("empty");	
			}
		}
				
		$this->curletter = "";
		
		// if we are showing on a per letter basis only
		if ($this->use_letter_bar == 2)
		{
			$this->curletter = JRequest::getVar('letter',$this->letters[0]->letter);	
		}
		
		$where = array();
		$where[] = "published = 1";
        $query = "SELECT * FROM #__fsf_glossary";
		if ($this->curletter)
		{
			$where[] = "SUBSTR(word,1,1) = '".FSFJ3Helper::getEscaped($db, $this->curletter)."'";
		}
		
		if (FSF_Helper::Is16())
		{
			$where[] = 'language in (' . $db->Quote(JFactory::getLanguage()->getTag()) . ',' . $db->Quote('*') . ')';
			$user = JFactory::getUser();
			$where[] = 'access IN (' . implode(',', $user->getAuthorisedViewLevels()) . ')';				
		}	

		if (count($where) > 0)
			$query .= " WHERE " . implode(" AND ",$where);
	
		$query .= " ORDER BY word";
        $db->setQuery($query);
        $this->rows = $db->loadObjectList();
  
        $pathway =& $mainframe->getPathway();
		if (FSF_Helper::NeedBaseBreadcrumb($pathway, array( 'view' => 'glossary' )))
			$pathway->addItem("Glossary");

		if (FSF_Settings::get('glossary_use_content_plugins'))
		{
			// apply plugins to article body
			$dispatcher	=& JDispatcher::getInstance();
			JPluginHelper::importPlugin('content');
			$art = new stdClass;
			if (FSF_Helper::Is16())
			{
				//$aparams = new JParameter(null);
			} else {
				$aparams = new stdClass();	
			}
			$this->params =& $mainframe->getParams('com_fsf');
			foreach ($this->rows as &$row)
			{
				if ($row->description)
				{
					$art->text = $row->description;
					$art->noglossary = 1;
					
					if (FSF_Helper::Is16())
					{
						$results = $dispatcher->trigger('onContentPrepare', array ('com_fsf.glossary', &$art, &$this->params, 0));
					} else {
						$results = $dispatcher->trigger('onPrepareContent', array (&$art, &$this->params, 0));
					} 
					$row->description = $art->text;
				}
				if ($row->longdesc)
				{
					$art->text = $row->longdesc;
					$art->noglossary = 1;
					if (FSF_Helper::Is16())
					{
						$results = $dispatcher->trigger('onContentPrepare', array ('com_fsf.glossary.long', & $art, &$this->params, 0));
					} else {
						$results = $dispatcher->trigger('onPrepareContent', array (& $art, &$this->params, 0));
					} 
					$row->longdesc = $art->text;
				}
			}
		}     
		   	
  		parent::display($tpl);
    }
}

