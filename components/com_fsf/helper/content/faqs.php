<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;

/*
Generic editor and list class

List:
	Fields to list
	Main Field for link
	always has published
	always has author
	sometimes has ordering
	sometimes has added date
	sometimes has modifed date
	
	optional lookup fields for category
	
	optional split fields based on page break (annoucne + faq)
	
	*/

require_once (JPATH_SITE.DS.'components'.DS.'com_fsf'.DS.'helper'.DS.'content.php');
require_once (JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_fsf'.DS.'adminhelper.php');

class FSF_ContentEdit_FAQs extends FSF_ContentEdit
{
	function __construct()
	{
		$this->id = "faqs";
		$this->descs = JText::_("FAQS");
		$this->has_ordering = 1;
		
		$this->table = "#__fsf_faq_faq";
		
		$this->fields = array();

		$field = new FSF_Content_Field("question",JText::_("QUESTION"),"long");
		$field->link = 1;
		$field->search = 1;
		$this->AddField($field);

		$field = new FSF_Content_Field("faq_cat_id",JText::_("CATEGORY"),"lookup");
		$field->lookup_table = "#__fsf_faq_cat";
		$field->lookup_required = 1;
		$field->lookup_id = "id";
		$field->lookup_order = "title";
		$field->lookup_title = "title";
		$field->lookup_select_msg = JText::_("PLEASE_SELECT_A_CATEGORY");
		$this->AddField($field);
		
		$field = new FSF_Content_Field("answer",JText::_("ANSWER"),"text");
		$field->search = 1;
		$field->more = "fullanswer";
		$this->AddField($field);
		
		$field = new FSF_Content_Field("featured",JText::_("Featured"),"checkbox");
		$field->required = false;
		$this->AddField($field);
		
		$field = new FSF_Content_Field("tags",JText::_("Tags"),"tags");
		$field->tags_table = "#__fsf_faq_tags";
		$field->tags_key = "faq_id";
		$field->required = 0;
		
		$this->AddField($field);
		
		if (FSFAdminHelper::Is16())
		{
			if (empty(FSFAdminHelper::$langs))
			{
				FSFAdminHelper::LoadLanguages();
				FSFAdminHelper::LoadAccessLevels();
			}

			$filter_langs = array();
			$filter_access = array();
			
			$field = new FSF_Content_Field("language",JText::_("LANGUAGE"),"lookup","lang_art");
			$field->lookup_required = 1;
			$field->lookup_id = "id";
			$field->lookup_title = "title";
			foreach (FSFAdminHelper::$langs as $lang)
			{
				$filter_langs[$lang->value] = $lang->text;
				$field->lookup_extra[$lang->value] = $lang->text;
			}
			$this->AddField($field);

			$field = new FSF_Content_Field("access",JText::_("Access"),"lookup");
			$field->lookup_required = 1;
			$field->lookup_id = "id";
			$field->lookup_title = "title";
			foreach (FSFAdminHelper::$access_levels as $lang)
			{
				$filter_access[$lang->value] = $lang->text;
				$field->lookup_extra[$lang->value] = $lang->text;
			}
			$this->AddField($field);
		}
		
		$this->list = array();
		$this->list[] = "question";
		$this->list[] = "faq_cat_id";
		$this->list[] = "featured";
		if (FSFAdminHelper::Is16())
		{
			$this->list[] = "language";
			$this->list[] = "access";
		}
		
		$this->edit = array();
		$this->edit[] = "faq_cat_id";
		$this->edit[] = "question";
		$this->edit[] = "featured";
		if (FSFAdminHelper::Is16())
		{
			$this->edit[] = "language";
			$this->edit[] = "access";
		}
		$this->edit[] = "answer";
		$this->edit[] = "tags";
		
		$this->order = "ordering DESC";
		
		$this->link = "index.php?option=com_fsf&view=faq&faqid=%ID%";

		$filter = new FSF_Content_Filter("faq_cat_id","id","title","#__fsf_faq_cat","title","CATEGORY");
		$this->AddFilter($filter);
		
		if (FSFAdminHelper::Is16())
		{
			$filter = new FSF_Content_Filter("language","id","title","","","SELECT_LANGUAGE", "lang_filter", $filter_langs);
			$this->AddFilter($filter);
			
			$filter = new FSF_Content_Filter("access","id","title","","","SELECT_ACCESS", "", $filter_access);
			$this->AddFilter($filter);
		}
	}
}