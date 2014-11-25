<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;

class FSF_Glossary
{
	static $fsf_glossary;
	static $replaced = array();
	static $inuse = array();
	static $offset = 0;
	static $cdom = null;
	
	static $all_replaced = array();
	
	static function GetGlossary()
	{
		if (empty(FSF_Glossary::$fsf_glossary))
		{
			$db = JFactory::getDBO();
			$query = 'SELECT * FROM #__fsf_glossary';
			
			$where = array();
			$where[] = " published = 1 ";
			if (FSF_Helper::Is16())
			{
				$where[] = 'language in (' . $db->Quote(JFactory::getLanguage()->getTag()) . ',' . $db->Quote('*') . ')';
				$user = JFactory::getUser();
				$where[] = 'access IN (' . implode(',', $user->getAuthorisedViewLevels()) . ')';				
			}	
			if (count($where) > 0)
				$query .= " WHERE " . implode(" AND ",$where);

			$query .= ' ORDER BY LENGTH(word) DESC';
			
			$db->setQuery($query);
			$rows = $db->loadAssocList();
			
			$fsf_glossary = array();
			
			foreach ($rows as $data)
			{
				FSF_Glossary::$fsf_glossary[$data['word']] = $data['description'];
			}
		}
	}
	
	static function preg_replace_dom(DOMNode $dom, array $exclude = array()) {
		if (!empty($dom->childNodes)) {
			
			foreach ($dom->childNodes as $node) {
				
				// if the node is in the exclude list, skip it and its children
				if (in_array($node->nodeName, $exclude))
					continue;
				
				if ($node instanceof DOMText) // only process text elements
				{
					$totalcount = 0;
					$offset = 0;
					
					$found = array();
					
					// try and match all glossary words we have in the 
					foreach (FSF_Glossary::$inuse as $data)
					{
						$word = $data->word;
						
						//$node->nodeValue = preg_replace($data->regex, $data->replace, $node->nodeValue, -1, $count);

						$replace = "XXX_GLOSSARY_".$offset."_$1_XXX";
						$count = 0;
						//$node->nodeValue = preg_replace($data->regex, $replace, $node->nodeValue, -1, $count);
						$node->nodeValue = preg_replace($data->regex, $replace, $node->nodeValue, -1, $count);
						if ($count)
						{
							// store the info about the item we just found
							$found[$offset] = $data;
							
							// increase the total found count in this element
							$totalcount += $count;
							
							// add this word to the global replaced array so the data can be added to the footer
							FSF_Glossary::$all_replaced[$word] = 1;

							// increase the offset
							$offset++;
						}
						
					}
					
					if ($totalcount)
					{
						$runs = 0; // run counter incase it goes wrong and gets carried away
						
						// temp node that we are working on
						$temp = $node;
						
						// find tag in node text
						if (function_exists("mb_strpos"))
						{
							$pos = mb_strpos($temp->nodeValue, "XXX_GLOSSARY_");							
						} else {
							$pos = strpos($temp->nodeValue, "XXX_GLOSSARY_");
						}
						
						while ($pos !== FALSE && $runs++ < 50) // while we have found an instance of 
						{
							// split the text node around the match
							$new = $temp->splitText($pos);
							
							// remove match text from split text and retrieve info
							list($elem, $new->nodeValue) = explode("_XXX", $new->nodeValue, 2);
							
							// parse the info found
							$elem = substr($elem, 13);
							list ($elem_offset, $text) = explode("_", $elem, 2);
							
							// lookup info we saved earlier
							$info = $found[$elem_offset];
							
							// build link element
							$link_elem = FSF_Glossary::$cdom->createElement('a', $text);					
							$link_elem->setAttribute("href", $info->href);
							$link_elem->setAttribute("class", $info->class);
							
							// insert link element before 2nd part of text split
							$temp->parentNode->insertBefore($link_elem, $new);
							
							// copy the new node over the old temp one
							$temp = $new;
							
							// find the text again
							if (function_exists("mb_strpos"))
							{
								$pos = mb_strpos($temp->nodeValue, "XXX_GLOSSARY_");							
							} else {
								$pos = strpos($temp->nodeValue, "XXX_GLOSSARY_");
							}
						}
					}
				} 
				else
				{
					FSF_Glossary::preg_replace_dom($node, $exclude);
				}
			}
		}
	}
	
	static function MakeAnchor($word)
	{
		return strtolower(preg_replace("/[^A-Za-z0-9]/", '-', $word));	
	}

	static function ReplaceGlossary($text)
	{
		FSF_Glossary::GetGlossary();
		
		if (count(FSF_Glossary::$fsf_glossary) == 0)
			return $text;

		// build a rough list of terms in the document in question. This means less stuff for the preg to check later on
		FSF_Glossary::$inuse = array();
		foreach (FSF_Glossary::$fsf_glossary as $word => $tip)
		{
			if (stripos($text, $word) !== FALSE) // found a word
			{
				// build an object containing the data about the word we have possibly in the doc
				$o = new stdClass();
				
				$word = strtolower($word);
				$o->word = $word;
				$o->regex = "/\b($word)\b/i";
				
				$o->href = "#";
				
				$anc = FSF_Glossary::MakeAnchor($word);
				
				if (FSF_Settings::get('glossary_link'))
					$o->href = FSFRoute::_( 'index.php?option=com_fsf&view=glossary&letter='.strtolower(substr($word,0,1)).'#' . $anc );
				
				$o->class = "fsj_tip fsf_glossary_word";
				
				FSF_Glossary::$inuse[] = $o;	
			}
		}
		
		// setup empty dom object
		libxml_use_internal_errors(TRUE);
		$dom=new DOMDocument('1.0','UTF-8');
		
		FSF_Glossary::$cdom =& $dom;

		$dom->substituteEntities=false;
		$dom->recover=true;
		$dom->strictErrorChecking=false;
		$dom->resolveExternals = false;

		//$text = str_replace("&","&amp;", $text);

		// load the xml file. Add padding tags as the dom adds crap to the start of the output
		$dom->loadHTML('<?xml version="1.0" encoding="UTF-8"?><meta http-equiv="content-type" content="text/html; charset=utf-8"><xxxglossaryxxx>' . $text . "</xxxglossaryxxx>");
		
		// get list of html tags to ignore
		$tag_ignore = FSF_Settings::get('glossary_exclude');
		$tag_ignore = explode(",", $tag_ignore);
		
		$tags = array();
		$tags[] = "a";
		foreach ($tag_ignore as $tag)
		{
			$tag = trim($tag);
			if (!$tag) continue;
			$tags[] = $tag;
		}
		
		// replace all glossary terms
		FSF_Glossary::preg_replace_dom($dom->documentElement, $tags);

		// get resultant html
		$result = $dom->saveHTML();

		//$result = str_replace("&amp;","&", $result);
		// use padding added earlier to remove appended content
		$pos1 = strpos($result, "<xxxglossaryxxx>") + 16;
		$pos2 = strrpos($result, "</xxxglossaryxxx>");
		$result = substr($result, $pos1, $pos2-$pos1);

		return $result;
	}

	static function Footer()
	{
		FSF_Glossary::GetGlossary();
		
		if (count(FSF_Glossary::$fsf_glossary) == 0)
			return "";
	
		$tail = "<div id='glossary_words' style='display:none;'>";

		foreach(FSF_Glossary::$fsf_glossary as $word => $tip)
		{
			$wordl = strtolower($word);
			
			// skip any entries that we havent found a match for
			if (!array_key_exists($wordl, FSF_Glossary::$all_replaced))
				continue;
			
			$wordl = preg_replace("/[^a-z0-9]/", "", $wordl);
			if (FSF_Settings::get('glossary_title'))
			{
				$tail .= "<div id='glossary_$wordl'><h4>$word</h4><div class='fsj_gt_inner'>$tip</div></div>";
			} else {
				$tail .= "<div id='glossary_$wordl'><div class='fsj_gt_inner'>$tip</div></div>";
			}
		}
		
		$tail .= "</div>";
		
		return $tail;
	}
}
