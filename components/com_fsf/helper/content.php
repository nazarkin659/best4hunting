<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;

jimport( 'joomla.html.pagination');
require_once (JPATH_SITE.DS.'components'.DS.'com_fsf'.DS.'helper'.DS.'paginationajax.php');
require_once( JPATH_SITE.DS.'components'.DS.'com_fsf'.DS.'helper'.DS.'tickethelper.php' );

class FSF_ContentEdit
{
	var $has_added = 0;
	var $has_ordering = 0;
	var $has_modified = 0;
	var $has_created = 0;
	var $has_products = 0;
	
	var $list_published = 1;
	var $list_added = 0;
	var $list_user = 1;
	
	var $rel_lookup_join = array();
	var $rel_lookup_filter = array();
	
	// stuff for JView::escape
	var $_escape = 'htmlspecialchars';
	var $_charset = 'UTF-8';
	
	var $added_related_js = 0;
	var $filters = array();
	
	function __construct()
	{
		
	}
	
	function Init()
	{
		if (empty($this->permission))
		{
			$this->tmplpath = JPATH_SITE.DS.'components'.DS.'com_fsf'.DS.'tmpl'.DS.'content';	
			$this->permission = FSF_Ticket_Helper::getAdminPermissions();		
			$user = JFactory::getUser();
			$userid = $user->get('id');
			$this->userid = $userid;

			$document = JFactory::getDocument();
			$document->addStyleSheet(JURI::root().'components/com_fsf/assets/css/popup.css'); 
			$document->addScript( JURI::base().'components/com_fsf/assets/js/popup.js' );
		}
	}
	
	function Display()
	{
		$this->Init();

		$db = JFactory::getDBO();
		$this->what = JRequest::getVar('what','');
		
		$user = JFactory::getUser();
		$userid = $user->get('id');
		
		$this->viewurl = "";
		
		$return = JRequest::getVar('return','');
		if ($return == 1)
		{
			JRequest::setVar('return',$_SERVER['HTTP_REFERER']);
		}	
		
		if ($this->what == "pick")
			return $this->HandlePick();
		
		if ($this->what == "author")
			return $this->HandleAuthor();
		
		if ($this->what == "publish" || $this->what == "unpublish")
			return $this->HandlePublish();
		
		if ($this->what == "cancel")
		{
			$mainframe = JFactory::getApplication();
			$link = FSFRoute::x('index.php?option=com_fsf&view=admin&layout=content&type=' . $this->id,false);
			$return = JRequest::getVar('return','');
			if ($return && $return != 1)
				$link = $return;
			$mainframe->redirect($link);
		}
		
		if ($this->what == "save" || $this->what == "apply" || $this->what == "savenew")
		{
			return $this->Save();
		}
		
		if ($this->what == "new")
		{
			return $this->Create();
		}
		
		if ($this->what == "edit")
		{
			$this->item = $this->getSingle();
			$this->viewurl = $this->getArtLink();
			
			if ($this->permission['artperm'] > 2)
				$this->authorselect = $this->AuthorSelect($this->item);
			
			$this->Output("form");
			return;
		}
		
		$this->GetListFilter();
		$this->data = $this->getList();

		$this->Output("list");
	}
	
	function Create()
	{
		$db = JFactory::getDBO();
		$item = array();
		$item['id'] = 0;
		
		$user = JFactory::getUser();
		$userid = $user->get('id');
		foreach ($this->edit as $edit)
		{
			$field = $this->GetField($edit);
			$item[$field->field] = $field->default;
				
			if ($field->more)
				$item[$field->more] = "";
				
			if ($field->type == "related")
			{
				$field->rel_ids = array();
				$field->rel_id_list = "";
					
				if (!$this->added_related_js)
					$this->AddRelatedJS();	
			} elseif ($field->type == "products")
			{
				$this->GetProducts();
					
				$field->products = array();
					
				$prodcheck = "";
				foreach($this->products as $product)
				{
					$prodcheck .= "<input type='checkbox' name='{$field->field}_prod_" . $product->id . "' />" . $product->title . "<br>";
				}
				$field->products_check = $prodcheck;
				$field->products_yesno = JHTML::_('select.booleanlist', $field->field, 
					array('class' => "inputbox",
							'size' => "1", 
							'onclick' => "DoAllProdChange('{$field->field}');"), $item[$field->field]);

			} 
		}
		$this->item = $item;
			
		if ($this->permission['artperm'] > 2)
		{
			$this->item['published'] = 0;
			$this->item['author'] = $userid;
			$this->authorselect = $this->AuthorSelect($this->item);
		}

		$this->Output("form");
		return;	
	}
	
	function AuthorSelect(&$item)
	{
		$curauthor = $item['author'];
		
		$user = JFactory::getUser($curauthor);
		$authorname = $user->get('name');
		
		$result = "<b id='content_authname'>".$authorname."</b>&nbsp;&nbsp;<button class='button' id='change_author'>".JText::_('CHANGE')."</button>";
		$result .= "<input name='author' type='hidden' id='content_author' value='{$curauthor}' />";
		return $result;	
	}
	
	function Save()
	{
		$db = JFactory::getDBO();
		$this->item = array();
		$this->item['id'] = JRequest::getVar('id',0);
		$user = JFactory::getUser();
		$userid = $user->get('id');

		$this->errors = array();
		$ok = true;
			
		foreach ($this->edit as $edit)
		{	
			$field = $this->GetField($edit);
				
			$this->item[$field->field] = JRequest::getVar($field->input_name,'');
			if ($field->type == "text")
				$this->item[$field->field] = JRequest::getVar($field->input_name, '', 'post', 'string', JREQUEST_ALLOWRAW);	
				
			if ($field->more)
			{
				if (strpos($this->item[$field->field],"system-readmore") > 0)
				{
					$pos = strpos($this->item[$field->field],"system-readmore");
					$top = substr($this->item[$field->field], 0, $pos);
					$top = substr($top,0, strrpos($top,"<"));
						
					$bottom = substr($this->item[$field->field], $pos);
					$bottom = substr($bottom, strpos($bottom,">")+1);
						
					$this->item[$field->field] = $top;
					$this->item[$field->more] = $bottom;                           
				} else {
					$this->item[$field->more] = '';
				}
			}
				
			if ($field->required)
			{
				if ($this->item[$field->field] == "")
				{
					$ok = false;
					$this->errors[$field->field] = $field->required;	
				}	
			}
		}
		
		$now = FSF_Helper::CurDate();	
		// if errors
		if ($ok)
		{
				
			if ($this->item['id'])
			{
				$qry = "UPDATE " . $this->table . " SET ";
					
				$sets = array();
			
				foreach ($this->edit as $edit)
				{
					$field = $this->GetField($edit);
					
					if ($field->type != "related" && $field->type != "tags")
						$sets[] = $field->field . " = '" . FSFJ3Helper::getEscaped($db, $this->item[$field->field]) . "'";
					if ($field->more)
						$sets[] = "`".$field->more . "` = '" . FSFJ3Helper::getEscaped($db, $this->item[$field->more]) . "'";
				}

				if ($this->permission['artperm'] > 2)
				{
					$sets[] = "published = " .JRequest::getInt('published',0);	
					$sets[] = "author = " . JRequest::getInt('author',0);
				}
					
				if ($this->has_modified)
					$sets[] = "modified = '{$now}'";
					
				$qry .= implode(", ", $sets);
					
				$qry .= " WHERE id = '". FSFJ3Helper::getEscaped($db, $this->item['id']) . "'";
			} else {
				$fieldlist = array();
				$fieldlist[] = "author";
				if ($this->has_added)
					$fieldlist[] = "added";
					
				$setlist = array();
					
				foreach($this->edit as $edit)
				{
					$field = $this->GetField($edit);
						
					if ($field->type == "related" || $field->type == "tags")
						continue;

					$fieldlist[] = $field->field;	
					$setlist[] = "'" . FSFJ3Helper::getEscaped($db, $this->item[$field->field]) . "'";
					if ($field->more)
					{
						$fieldlist[] = "`".$field->more."`";	
						$setlist[] = "'" . FSFJ3Helper::getEscaped($db, $this->item[$field->more]) . "'";
					}
						
				}
					
				if ($this->has_modified)
				{
					$fieldlist[] = "modified";
					$setlist[] = "'{$now}'";	
					$fieldlist[] = "created";
					$setlist[] = "'{$now}'";	
				}
				$fieldlist[] = "published";
				if ($this->permission['artperm'] > 2)
				{
					$setlist[] = JRequest::getInt('published',0);	
				} else {
					$setlist[] = "0";	
				}
					
				if ($this->has_ordering)
				{
					// need to get ordering value
					$order = $this->GetOrderValue();
					if ($order < 1)
						$order = 1;
					$fieldlist[] = "ordering";
					$setlist[] = $order;
				}

				$qry = "INSERT INTO " . $this->table . " (" . implode(", ",$fieldlist) . ") VALUES ('$userid', ";
					
				if ($this->has_added)
					$qry .= "'{$now}', ";
					
				$qry .= implode(", ", $setlist) . ")";
			}

			$db->setQuery($qry);
			$db->query($qry);
				
			if (!$this->item['id'])
			{
				$this->item['id'] = $db->insertid();
			}
			$this->articleid = $this->item['id'];
				
				
			foreach($this->edit as $edit)
			{
				$field = $this->GetField($edit);
					
				// save any products fields
				if ($field->type == "products")
				{
					$this->GetProducts();
						
					$qry = "DELETE FROM {$field->prod_table} WHERE {$field->prod_artid} = '".FSFJ3Helper::getEscaped($db, $this->item['id'])."'";
					$db->setQuery($qry);
					//echo $qry."<br>";
					$db->query($qry);
						
					if (!$this->item[$field->field])
					{
						foreach ($this->products as &$product)
						{
							$pid = $product->id;
							$name = $field->field."_prod_" . $pid;
							$val = JRequest::getVar($name);
							if ($val == "on")
							{
								$qry = "INSERT INTO {$field->prod_table} ({$field->prod_prodid}, {$field->prod_artid}) VALUES
									($pid, '".FSFJ3Helper::getEscaped($db, $this->item['id'])."')";
								$db->setQuery($qry);
								//echo $qry."<br>";
								$db->query($qry);
							}
						}
						//echo "Saving products<br>";
					}
					//echo "Prod Field";	
				} elseif ($field->type == "related")
				{
					// save related field	
					$relids = explode(":",$this->item[$field->field]);
						
					$qry1 = "DELETE FROM {$field->rel_table} WHERE {$field->rel_id} = '". FSFJ3Helper::getEscaped($db, $this->item['id']) . "'";
					$db->setQuery($qry1);
					//echo $qry1."<br>";
					$db->query();
						
					foreach($relids as $id)
					{
						$id = FSFJ3Helper::getEscaped($db, $id);
						$qry1 = "REPLACE INTO {$field->rel_table} ({$field->rel_id}, {$field->rel_relid}) VALUES ('". FSFJ3Helper::getEscaped($db, $this->item['id']) . "', '$id')";
						$db->setQuery($qry1);
						//echo $qry1."<br>";
						$db->query();
					}
						
				} else if ($field->type == "tags")
				{
					//print_p($field);
					//print_p($this->item);	
					
					$qry1 = "DELETE FROM {$field->tags_table} WHERE {$field->tags_key} = '". FSFJ3Helper::getEscaped($db, $this->item['id']) . "'";
					//echo $qry1 . "<br>";
					$db->setQuery($qry1);
					$db->query();
					
					$tags = explode("\n", $this->item[$field->field]);
					
					foreach ($tags as $tag)
					{
						$tag = trim($tag);
						if (!$tag) continue;
						$qry1 = "REPLACE INTO {$field->tags_table} ({$field->tags_key}, tag, language) VALUES (	'". FSFJ3Helper::getEscaped($db, $this->item['id']) . "', ";
						$qry1 .= "'". FSFJ3Helper::getEscaped($db, $tag) . "', '". FSFJ3Helper::getEscaped($db, $this->item['language']) . "')";
						//echo $qry1 . "<br>";
						$db->setQuery($qry1);
						$db->query();
					
					}
					
					//exit;
				}	
			}
				
			// need to check for a redirect field here
			$mainframe = JFactory::getApplication();
			if ($this->what == "apply")
			{
				$link = FSFRoute::x("index.php?option=com_fsf&view=admin&layout=content&type={$this->id}&what=edit&id={$this->articleid}",false);
			} elseif ($this->what == "savenew")
			{
				$link = FSFRoute::x("index.php?option=com_fsf&view=admin&layout=content&type={$this->id}&what=new",false);
			} else {
				$link = FSFRoute::x('index.php?option=com_fsf&view=admin&layout=content&type=' . $this->id,false);
				$return = JRequest::getVar('return','');
				if ($return && $return != 1)
					$link = $return;
			}
			$mainframe->redirect($link,JText::_('ARTICLE_SAVED'));	
			return;		
				
		} else {
			// need to put onto the form the field stuff for related and products fields
			foreach($this->edit as $edit)
			{
				$field = $this->GetField($edit);
				if ($field->type == "related")
				{
					$field->rel_ids = array();
						
					$relids = JRequest::getVar($field->field);
					$relateds = explode(":",$relids);
					foreach ($relateds as $related)
					{
						if ($related == 0) continue;
						$field->rel_ids[$related] = $related;
					}
						
					$field->rel_id_list = implode(":", $field->rel_ids);
						
					if (count($field->rel_ids) > 0)
					{
						$ids = array();
						foreach ($field->rel_ids as $id)
							$ids[] = FSFJ3Helper::getEscaped($db, $id);
						$qry = "SELECT {$field->rel_lookup_id}, {$field->rel_display} FROM {$field->rel_lookup_table} WHERE {$field->rel_lookup_id} IN (" . implode(", ", $ids) . ")";
	///					$qry = "SELECT {$field->rel_lookup_id}, {$field->rel_lookup_display} FROM {$field->rel_lookup_table} WHERE {$field->rel_lookup_id} IN (" . implode(", ", $field->rel_ids) . ")";
						$db->setQuery($qry);
						$relateds = $db->loadAssocList($field->rel_lookup_id);
						foreach ($relateds as $id => &$related)
							$field->rel_ids[$id] = $related[$field->rel_lookup_display];
					}
						
					if (!$this->added_related_js)
						$this->AddRelatedJS();							
				} else if ($field->type == "products")
				{
					$this->GetProducts();
						
					$field->products = array();
						
					$prodcheck = "";
					foreach($this->products as $product)
					{
						$prodform = JRequest::getVar($field->field . "_prod_" . $product->id);
						if ($prodform == "on")
						{
							$prodcheck .= "<input type='checkbox' name='{$field->field}_prod_" . $product->id . "' checked />" . $product->title . "<br>";
						} else {
							$prodcheck .= "<input type='checkbox' name='{$field->field}_prod_" . $product->id . "' />" . $product->title . "<br>";
						}
					}
					$field->products_check = $prodcheck;
					$field->products_yesno = JHTML::_('select.booleanlist', $field->field, 
						array('class' => "inputbox",
								'size' => "1", 
								'onclick' => "DoAllProdChange('{$field->field}');"), $this->item[$field->field]);		
				}	
			}
				
			if ($this->permission['artperm'] > 2)
			{
				$this->item['published'] = JRequest::getVar('published',0);
				$this->item['author'] = JRequest::getVar('author',0);
				$this->authorselect = $this->AuthorSelect($this->item);
			}
			$this->Output("form");
		}
			
		// if no errors, forward to list
		return;	
	}
	
	function Header()
	{
		// output the header stuff
		echo FSF_Helper::PageStyle();
		echo FSF_Helper::PageTitle("SUPPORT_ADMIN","CONTENT_MANAGEMENT");

		include JPATH_SITE.DS.'components'.DS.'com_fsf'.DS.'views'.DS.'admin'.DS.'snippet'.DS.'_tabbar.php';
		include JPATH_SITE.DS.'components'.DS.'com_fsf'.DS.'views'.DS.'admin'.DS.'snippet'.DS.'_contentbar.php';
		//include "components/com_fsf/views/admin/snippet/_tabbar.php";
		//include "components/com_fsf/views/admin/snippet/_contentbar.php";	
	}
	
	function Footer()
	{
		include JPATH_SITE.DS.'components'.DS.'com_fsf'.DS.'_powered.php';
		//include JPATH_SITE.DS.'components'.DS.'com_fsf'.DS.'_powered.php';;
		echo FSF_Helper::PageStyleEnd();	
	}
	
	function AddField(&$field)
	{
		$this->fields[$field->field] = $field;	
	}

	function AddFilter(&$filter)
	{
		$this->filters[$filter->field] = $filter;	
	}
	
	function Output($file)
	{
		$this->Header();	
		include $this->tmplpath . DS . $file . ".php";
		$this->Footer();
	}
	
	function GetListFilter()
	{
		$db = JFactory::getDBO();
		$mainframe = JFactory::getApplication();

		// search - normal text search on set of fields
		$this->filter_values['search'] = $mainframe->getUserStateFromRequest($this->id."search","search","");
		$this->filter_values['order'] = $mainframe->getUserStateFromRequest($this->id."order","order","");
		$this->filter_values['order_dir'] = $mainframe->getUserStateFromRequest($this->id."order_dir","order_dir","ASC");
		
		$this->filter_html = array();
		
		// filters	
		
		// user
		$this->filter_values['userid'] = 0;
		if ($this->permission['artperm'] > 1)
		{
			$this->filter_values['userid'] = $mainframe->getUserStateFromRequest($this->id."userid","userid","");
			$qry = "SELECT id, username, name FROM #__users WHERE id IN (SELECT author FROM {$this->table}) ORDER BY name";
			$db->setQuery($qry);
			$users = array();
			$users[] = JHTML::_('select.option', '0', JText::_("SELECT_USER"), 'id', 'name');
			$users = array_merge($users, $db->loadObjectList());
			$this->filter_html['userid'] = JHTML::_('select.genericlist',  $users, 'userid', 'class="inputbox" size="1" onchange="document.adminForm.submit( );"', 'id', 'name', $this->filter_values['userid']);
		}
		// published
		$this->filter_values['published'] = $mainframe->getUserStateFromRequest($this->id."ispublished","ispublished","");
		
		$published = array();
		$published[] = JHTML::_('select.option', '0', JText::_("IS_PUBLISHED"), 'id', 'title');
		$published[] = JHTML::_('select.option', '2', JText::_("PUBLISHED"), 'id', 'title');
		$published[] = JHTML::_('select.option', '1', JText::_("UNPUBLISHED"), 'id', 'title');
		$this->filter_html['published'] = JHTML::_('select.genericlist',  $published, 'ispublished', 'class="inputbox" size="1" onchange="document.adminForm.submit( );"', 'id', 'title', $this->filter_values['published']);
		
		// optional fields such as category
		foreach ($this->filters as $filter)
		{
			$this->filter_values[$filter->field] = $mainframe->getUserStateFromRequest($this->id.$filter->input_name,$filter->input_name,"");
			
			$filtervalues = array();
			
			$filtervalues[] = JHTML::_('select.option', '', JText::_($filter->source_header), $filter->source_id, $filter->source_display);
			if ($filter->source_table)
			{
				$qry = "SELECT {$filter->source_id}, {$filter->source_display} FROM {$filter->source_table} ORDER BY {$filter->source_order}";
				$db->setQuery($qry);
				$filtervalues = array_merge($filtervalues, $db->loadObjectList());
			}
			
			if ($filter->extra)
			{
				foreach ($filter->extra as $key => $value)
				{
					$filtervalues[] = JHTML::_('select.option', $key, $value, $filter->source_id, $filter->source_display);
				}
			}
			
			$this->filter_html[$filter->field] = JHTML::_('select.genericlist',  $filtervalues, $filter->input_name, 'width="100" style="width: 100px" class="inputbox" size="1" onchange="document.adminForm.submit( );"', $filter->source_id, $filter->source_display, $this->filter_values[$filter->field]);
		}
	}
	
	function getList()
	{
		$db = JFactory::getDBO();
		$mainframe = JFactory::getApplication();
		
		$fields = array();
		$fields[] = "a.id";
		foreach($this->list as $list)
		{
			$field = $this->GetField($list);
			$fields[] = "a." . $field->field;
		}
		if ($this->has_added)
			$fields[] = "a.added";
		if ($this->has_ordering)
			$fields[] = "a.ordering";
		if ($this->has_modified)
			$fields[] = "a.modified";
		if ($this->has_created)
			$fields[] = "a.created";

		$fields[] = "a.published";
		$fields[] = "u.name";
		$fields[] = "u.username";
		$fields[] = "u.id as userid";

		$qry = "SELECT " . implode(", ", $fields) . " FROM {$this->table} as a LEFT JOIN #__users as u ON a.author = u.id ";
		
		$where = array();
		
		if ($this->permission['artperm'] == 1)
			$where[] = "a.author = {$this->userid}";
		
		if ($this->filter_values['published'] > 0)
			$where[] = "a.published = ".FSFJ3Helper::getEscaped($db, $this->filter_values['published']-1);
		
		if ($this->filter_values['userid'] > 0)
			$where[] = "a.author = ".FSFJ3Helper::getEscaped($db, $this->filter_values['userid']);
		
		/*print_p($this->filter_values);
		print_p($this->fields);
		*/
		if ($this->filter_values['search'] != "")
		{
			$search = array();
			foreach($this->fields as $field)
			{
				if ($field->search)
				{
					$search[] = "{$field->field} LIKE '%".FSFJ3Helper::getEscaped($db, $this->filter_values['search'])."%'";	
				}	
			}	
			
			if (count($search) > 0)
			{
				$where[] = "( " . implode(" OR ", $search) . " )";	
			}
		}
		
		
		foreach ($this->filters as $filter)
		{
			$value = $this->filter_values[$filter->field];
			if ($value)
			{
				$where[] = "a.{$filter->field} = '".FSFJ3Helper::getEscaped($db, $value) . "'";
			}
		}
		
		if (count($where) > 0)
			$qry  .= " WHERE " . implode(" AND ", $where);
		
		if ($this->filter_values['order'])
		{
			$qry .= " ORDER BY " . FSFJ3Helper::getEscaped($db, $this->filter_values['order']) . " " . FSFJ3Helper::getEscaped($db, $this->filter_values['order_dir']);
		} else {
			$qry .= " ORDER BY " . $this->order;
		}
		
		$this->filter_values['limitstart'] = JRequest::getVar("limit_start",0);
		$this->filter_values['limit'] = $mainframe->getUserStateFromRequest($this->id."limit_base","limit_base","20");
		
		$this->_pagination = new JPaginationAjax($this->_getListCount($qry), $this->filter_values['limitstart'], $this->filter_values['limit'] );

		$db->setQuery($qry, $this->filter_values['limitstart'], $this->filter_values['limit']);
		$this->data = $db->loadAssocList();
		
		return $this->data;
	}
	
	function _getListCount($query)
	{
		$db = JFactory::getDBO();
		$db->setQuery($query);
		$db->query();

		return $db->getNumRows();
	}

	function getSingle()
	{
		if ($this->has_products)
			$this->GetProducts();
		
		// get a list of announcements, including pagination and filter
		$id = JRequest::getVar('id',0);
		
		$db = JFactory::getDBO();
		$qry = "SELECT a.*, u.name, u.username, u.id as userid FROM {$this->table} as a LEFT JOIN #__users as u ON a.author = u.id ";
		
		$qry .= "WHERE a.id = '".FSFJ3Helper::getEscaped($db, $id)."'";
		
		$db->setQuery($qry);
		$row = $db->loadAssoc();		
		
		if ($this->permission['artperm'] < 2 && $this->userid != $row['userid'])
		{
			return null;	
		}
		
		foreach($this->edit as $edit)
		{
			$field = $this->GetField($edit);
			
			if ($field->type == "products")
			{
				$qry = "SELECT {$field->prod_prodid} FROM {$field->prod_table} WHERE {$field->prod_artid} = '".FSFJ3Helper::getEscaped($db, $id)."'";
				$db->setQuery($qry);
				$field->products = $db->loadAssocList($field->prod_prodid);
				
				$prodcheck = "";
				foreach($this->products as $product)
				{
					$checked = false;
					if (array_key_exists($product->id,$field->products))
					{
						$prodcheck .= "<input type='checkbox' name='{$field->field}_prod_" . $product->id . "' checked />" . $product->title . "<br>";
					} else {
						$prodcheck .= "<input type='checkbox' name='{$field->field}_prod_" . $product->id . "' />" . $product->title . "<br>";
					}
				}
				$field->products_check = $prodcheck;
				$field->products_yesno = JHTML::_('select.booleanlist', $field->field, 
					array('class' => "inputbox",
							'size' => "1", 
							'onclick' => "DoAllProdChange('{$field->field}');"),
						intval($row[$field->field]));

			} else if ($field->type == "related")
			{
				$qry = "SELECT {$field->rel_relid} FROM {$field->rel_table} WHERE {$field->rel_id} = '".FSFJ3Helper::getEscaped($db, $id)."'";
				$db->setQuery($qry);
				$field->rel_ids = array();
				
				$relateds = $db->loadAssocList($field->rel_relid);
				foreach ($relateds as $id => &$related)
				{
					if ($id == 0) continue;
					$field->rel_ids[$id] = $id;
				}
				
				$field->rel_id_list = implode(":", $field->rel_ids);
				
				if (count($field->rel_ids) > 0)
				{
					$qry = "SELECT {$field->rel_lookup_id}, {$field->rel_display} FROM {$field->rel_lookup_table} WHERE {$field->rel_lookup_id} IN (" . implode(", ", $field->rel_ids) . ")";
					$db->setQuery($qry);
					$relateds = $db->loadAssocList($field->rel_lookup_id);
					foreach ($relateds as $id => &$related)
						$field->rel_ids[$id] = $related[$field->rel_display];
				}
				
				if (!$this->added_related_js)
					$this->AddRelatedJS();
			} else if ($field->type == "tags")
			{
				$qry = "SELECT * FROM {$field->tags_table} WHERE {$field->tags_key} = '".FSFJ3Helper::getEscaped($db, $id)."'"; 
				$db->setQuery($qry);
				$row['tags'] = array();
				$taglist = $db->loadAssocList();
				foreach ($taglist as $id => $tag)
				{
					$row['tags'][] = $tag['tag'];
				}
				
				$row['tags'] = implode("\n", $row['tags']);
			}
		}
		
		return $row;
	}
	
	function getArtLink()
	{
		$link = $this->link;
		$link = str_replace("%ID%",$this->item['id'],$link);
		return FSFRoute::_($link);
	}
	
	function GetProducts()
	{
		if (empty($this->products))
		{
			$db = JFactory::getDBO();
			$query = "SELECT * FROM #__fsf_prod WHERE published = 1 ORDER BY title";
			$db->setQuery($query);
			$this->products = $db->loadObjectList();
		}
	}
	
	function GetField($name)
	{
		return $this->fields[$name];	
	}
	
	function LookupInput($field, $item)
	{
		if (property_exists($field, "lookup_table") && $field->lookup_table)
		{
			$query = "SELECT {$field->lookup_id}, {$field->lookup_title}
				 FROM {$field->lookup_table} 
				 ORDER BY {$field->lookup_order}";
			$db	= & JFactory::getDBO();
			$db->setQuery($query);

			$sections = $db->loadObjectList();
		} else {
			$sections = array();
		}

		if (property_exists($field, 'lookup_extra'))
		{
			$extra = array();
			foreach ($field->lookup_extra as $key => $value)
			{
				$obj = new stdClass();
				$id = $field->lookup_id;
				$title = $field->lookup_title;
				
				$obj->$id = $key;
				$obj->$title = $value;
				
				$extra[] = $obj;
			}
			
			$sections = array_merge($extra, $sections);
		}

		return JHTML::_('select.genericlist',  $sections, $field->input_name, 'class="inputbox" size="1" ', $field->lookup_id, $field->lookup_title, $item[$field->field]);
	}
	
	function GetLookupValues($field)
	{
		$ids = array();
		
		$db	= & JFactory::getDBO();
		foreach($this->data as &$item)
		{
			$ids[$item[$field->field]] = FSFJ3Helper::getEscaped($db, $item[$field->field]);	
		}
		
		$this->lookup_values[$field->field] = array();
		
		if (property_exists($field, "lookup_table") && $field->lookup_table)
		{
			$query = "SELECT {$field->lookup_id}, {$field->lookup_title}
				 FROM {$field->lookup_table} 
				 WHERE {$field->lookup_id} IN ('" . implode("', '", $ids) . "')
				 ORDER BY {$field->lookup_order}";
			$db->setQuery($query);

			$rows = $db->loadAssocList();
			
			$this->lookup_values[$field->field] = array();
			
			foreach ($rows as $row)
			{
				$this->lookup_values[$field->field][$row[$field->lookup_id]] = $row;
			}
		}
		if (property_exists($field, 'lookup_extra'))
		{
			$extra = array();
			foreach ($field->lookup_extra as $key => $value)
			{
				$obj = array();
				$obj[$field->lookup_id] = $key;
				$obj[$field->lookup_title] = $value;
				
				$this->lookup_values[$field->field][$key] = $obj;
			}
		}
	}
	
	function GetLookupText($field, $id)
	{
		if (empty($this->lookup_values))
			$this->lookup_values = array();
		
		if (!array_key_exists($field->field, $this->lookup_values))
			$this->GetLookupValues($field);
		
		if (array_key_exists($id, $this->lookup_values[$field->field]))
			return $this->lookup_values[$field->field][$id][$field->lookup_title];

		return $id;
	}
	
	function HandleAuthor()
	{
		$db	= & JFactory::getDBO();
		$mainframe = JFactory::getApplication();
		if ($this->permission['artperm'] < 3)
			exit;
			
		// build query
		$qry = "SELECT * FROM #__fsf_user as fsu LEFT JOIN #__users as u ON fsu.user_id = u.id";
		$where = array();
		$where[] = "fsu.artperm > 0";
		
		$limitstart = JRequest::getInt('limitstart',0);
		$mainframe = JFactory::getApplication();
		$limit = $mainframe->getUserStateFromRequest('users.limit', 'limit', 10, 'int');
		$search = JRequest::getString('search','');
		
		$db	=& JFactory::getDBO();
		
		if ($search != "")
		{
			$where[] = "(u.username LIKE '%".FSFJ3Helper::getEscaped($db, $search)."%' OR u.name LIKE '%".FSFJ3Helper::getEscaped($db, $search)."%' OR u.email LIKE '%".FSFJ3Helper::getEscaped($db, $search)."%')";
		}
				
		if (count($where) > 0)
		{
			$qry .= " WHERE " . implode(" AND ", $where);	
		}

		
		// Sort ordering
		$qry .= " ORDER BY u.name ";
		
		
		// get max items
		//echo $qry."<br>";
		$db->setQuery( $qry );
		$db->query();
		$maxitems = $db->getNumRows();
			
		
		// select picked items
		$db->setQuery( $qry, $limitstart, $limit );
		$this->users = $db->loadObjectList();

		
		// build pagination
		$this->pagination = new JPagination($maxitems, $limitstart, $limit );
		$this->search = $search;
				
		include $this->tmplpath . DS . "user.php";		
	}
	
	function HandlePick()
	{
		$db	= & JFactory::getDBO();
		$mainframe = JFactory::getApplication();

		$f = JRequest::getVar('field');
		$field = $this->GetField($f);
		
		$this->HandlePickFilter($field);
		
		$this->pick_field = $field->field;
		//print_p($field);
		
		// get data for form
		$qry = "SELECT ";
		$fields = array();
		foreach($field->rel_lookup_display as $fieldname => $finfo)
		{
			
			$fields[] = $fieldname . " as " . $finfo['alias'];	
		}
		$fields[] = $field->rel_lookup_table_alias . '.' . $field->rel_lookup_id;
		
		$qry .= implode(", ", $fields);
		
		$qry .= " FROM " . $field->rel_lookup_table . " AS " . $field->rel_lookup_table_alias;
		
		foreach($field->rel_lookup_join as $join)
		{
			$qry .= " LEFT JOIN {$join['table']} AS {$join['alias']} ON {$field->rel_lookup_table_alias}.{$join['source']} = {$join['alias']}.{$join['dest']} ";
		}
		
		$where = array();
		
		if ($this->filter_values['published'] > 0)
			$where[] = "a.published = ".FSFJ3Helper::getEscaped($db, $this->filter_values['published']-1);
		
		if ($this->filter_values['userid'] > 0)
			$where[] = "a.author = ".FSFJ3Helper::getEscaped($db, $this->filter_values['userid']);
		
		if ($this->filter_values['search'] != "")
		{
			$search = array();
			foreach($field->rel_lookup_search as $searchfield)
			{
				$search[] = "{$searchfield} LIKE '%".FSFJ3Helper::getEscaped($db, $this->filter_values['search'])."%'";	
			}	
			
			if (count($search) > 0)
			{
				$where[] = "( " . implode(" OR ", $search) . " )";	
			}
		}
		
		foreach ($this->filters as $filter)
		{
			$value = JRequest::getVar($filter->field);
			if ($value > 0)
			{
				$where[] = "a.{$filter->field} = ".FSFJ3Helper::getEscaped($db, $value);
			}
		}

		if (count($where) > 0)
			$qry .= "WHERE " . implode(" AND ",$where);

		if ($this->filter_values['order'])
		{
			$qry .= " ORDER BY " . FSFJ3Helper::getEscaped($db, $this->filter_values['order']) . " " . FSFJ3Helper::getEscaped($db, $this->filter_values['order_dir']);
		} else {
			$qry .= " ORDER BY " . $this->order;
		}
		
		//echo $qry."<br>";
		$this->filter_values['limitstart'] = JRequest::getVar("limit_start",0);
		$this->filter_values['limit'] = $mainframe->getUserStateFromRequest($field->field."limit_base","limit_base","10");
		
		$this->_pagination = new JPaginationAjax($this->_getListCount($qry), $this->filter_values['limitstart'], $this->filter_values['limit'] );

		$db->setQuery($qry, $this->filter_values['limitstart'], $this->filter_values['limit']);
		$this->pick_data = $db->loadAssocList();

		$this->field = $field;
		
		include $this->tmplpath . DS . "related.php";
	}
	
	function HandlePickFilter($field)
	{
		$db = JFactory::getDBO();
		$mainframe = JFactory::getApplication();

		// search - normal text search on set of fields
		$this->filter_values['search'] = $mainframe->getUserStateFromRequest($field->field."search","search","");
		$this->filter_values['order'] = $mainframe->getUserStateFromRequest($field->field."order","order","");
		$this->filter_values['order_dir'] = $mainframe->getUserStateFromRequest($field->field."order_dir","order_dir","ASC");
		
		$this->filter_html = array();
		
		// filters	
		
		// user
		$this->filter_values['userid'] = 0;
		if ($this->permission['artperm'] > 1)
		{
			$this->filter_values['userid'] = $mainframe->getUserStateFromRequest($field->field."userid","userid","");
			$qry = "SELECT id, username, name FROM #__users WHERE id IN (SELECT author FROM {$this->table}) ORDER BY name";
			$db->setQuery($qry);
			$users = array();
			$users[] = JHTML::_('select.option', '0', JText::_("SELECT_USER"), 'id', 'name');
			$users = array_merge($users, $db->loadObjectList());
			$this->filter_html['userid'] = JHTML::_('select.genericlist',  $users, 'userid', 'class="inputbox" size="1" onchange="document.adminForm.submit( );"', 'id', 'name', $this->filter_values['userid']);
		}
		// published
		$this->filter_values['published'] = $mainframe->getUserStateFromRequest($field->field."ispublished","ispublished","");
		
		$published = array();
		$published[] = JHTML::_('select.option', '0', JText::_("IS_PUBLISHED"), 'id', 'title');
		$published[] = JHTML::_('select.option', '2', JText::_("PUBLISHED"), 'id', 'title');
		$published[] = JHTML::_('select.option', '1', JText::_("UNPUBLISHED"), 'id', 'title');
		$this->filter_html['published'] = JHTML::_('select.genericlist',  $published, 'ispublished', 'class="inputbox" size="1" onchange="document.adminForm.submit( );"', 'id', 'title', $this->filter_values['published']);
		
		// optional fields such as category
		foreach ($field->filters as $filter)
		{
			$this->filter_values[$filter->field] = $mainframe->getUserStateFromRequest($field->field.$filter->field,$filter->field,"");
			
			$qry = "SELECT {$filter->source_id}, {$filter->source_display} FROM {$filter->source_table} ORDER BY {$filter->source_order}";
			$db->setQuery($qry);
			$filtervalues = array();
			
			$filtervalues[] = JHTML::_('select.option', '', JText::_($filter->source_header), $filter->source_id, $filter->source_display);
			$filtervalues = array_merge($filtervalues, $db->loadObjectList());
			
			$this->filter_html[$filter->field] = JHTML::_('select.genericlist',  $filtervalues, $filter->field, 'width="100" style="width: 100px" class="inputbox" size="1" onchange="document.adminForm.submit( );"', $filter->source_id, $filter->source_display, $this->filter_values[$filter->field]);
		}
		
	}
	
	function AddRelatedJS()
	{
		if ($this->added_related_js)
			return;
			
		$document = JFactory::getDocument();
		$document->addStyleSheet( JURI::base().'components/com_fsf/assets/css/popup.css' ); 
		$document->addScript( JURI::base().'components/com_fsf/assets/js/popup.js' );
		$this->added_related_js = 1;	
	}
	
	function GetOrderValue()
	{
		$db	= & JFactory::getDBO();
		$qry = "SELECT MAX(ordering)+1 as nextorder FROM {$this->table}";
		$db->setQuery($qry);
		$data = $db->loadObject();
		return $data->nextorder;		
	}
	
	function HandlePublish()
	{
		$id = JRequest::getInt('id',0);
		if ($id < 1)
			exit;
		
		if ($this->permission['artperm'] > 2)
		{
			$db	= & JFactory::getDBO();
			
			$pub = 0;
			if ($this->what == "publish")	
				$pub = 1;
			
			$qry = "UPDATE {$this->table} SET published = $pub WHERE id = '".FSFJ3Helper::getEscaped($db, $id)."'";
			
			$db->setquery($qry);
			$db->query();
		}
		
		exit;
	}
	
	function GetCounts()
	{
		$db	= & JFactory::getDBO();
		$this->Init();
		
		$counts = array();
		$counts['user_pub'] = 0;
		$counts['user_unpub'] = 0;
		$counts['user_total'] = 0;
		$counts['pub'] = 0;
		$counts['unpub'] = 0;
		$counts['total'] = 0;
		
		// return published, unpublished and total counts for the user
		$qry = "SELECT count(*) as cnt, published FROM {$this->table} WHERE author = {$this->userid} GROUP BY published";
		$db->setQuery($qry);
		$c1 = $db->loadObjectList("published");
		
		if (array_key_exists(0, $c1))
		{
			$counts['user_unpub'] += $c1[0]->cnt;
			$counts['user_total'] += $c1[0]->cnt;
		}
		if (array_key_exists(1, $c1))
		{
			$counts['user_pub'] += $c1[1]->cnt;
			$counts['user_total'] += $c1[1]->cnt;
		}
		
		if ($this->permission['artperm'] > 1)
		{
			$qry = "SELECT count(*) as cnt, published FROM {$this->table} GROUP BY published";
			$db->setQuery($qry);
			$c1 = $db->loadObjectList("published");
			
			if (array_key_exists(0, $c1))
			{
				$counts['unpub'] += $c1[0]->cnt;
				$counts['total'] += $c1[0]->cnt;
			}
			if (array_key_exists(1, $c1))
			{
				$counts['pub'] += $c1[1]->cnt;
				$counts['total'] += $c1[1]->cnt;
			}
		}
		
		// if has mod perm, then return total unpublished	
		
		return $counts;
	}
	
	function EditPanel($item,$changeclass = true)
	{
		$this->Init();
		$this->changeclass = $changeclass;
		if ($this->permission['artperm'] > 1 || ($this->permission['artperm'] == 1 && $item['author'] == $this->userid)) {
			echo "<div class='fsf_content_edit_article'>";
			echo "<a href='".FSFRoute::_("index.php?option=com_fsf&view=admin&layout=content&type={$this->id}&what=edit&id={$item['id']}&option=com_fsf&return=1")."' class='fsj_tip' title='".JText::_('EDIT_ARTICLE')."'><img src='" . JURI::root( true ). "/components/com_fsf/assets/images/edit.png' alt='Edit' /></a>";	
		
			if ($item['published'])
			{
				$tip = JText::_("CONTENT_PUB_TIP");;	
			} else {
				$tip = JText::_('CONTENT_UNPUB_TIP');
			}
			
			if ($this->permission['artperm'] > 2) :
				?>
					<a href="#" id="publish_<?php echo $item['id']; ?>" class="fsf_publish_button fsj_tip" state="<?php echo $item['published']; ?>" title="<?php echo $tip; ?>">
						<?php echo FSF_Helper::GetPublishedText($item['published'],true); ?>
					</a>
				<?php
			else:
				?>
					<?php echo str_replace("_16.png","_g_16.png",FSF_Helper::GetPublishedText($item['published'])); ?>
				<?php
			endif;
		
			echo "</div>";
		}
	}
}

class FSF_Content_Field
{
	var $field = "";
	var $type = "string";
	var $desc = "";
	var $link = 0;
	var $required = "";
	var $more = "";
	var $default = "";
	var $search = 0;
	var $filters = array();
	
	var $show_pagebreak = 0;
	
	function __construct($field, $desc, $type = "string", $input_name = "")
	{
		$this->field = $field;
		$this->desc = $desc;
		$this->type = $type;
		$this->input_name = $input_name;
		if (!$this->input_name)
			$this->input_name = $field;
		
		$this->required = "You must enter a " . strtolower($desc);	
	}
	
	function AddFilter(&$filter)
	{
		$this->filters[$filter->field] = $filter;	
	}

}

class FSF_Content_Filter
{
	function __construct($field,$sourcd_id,$source_display,$source_table,$source_order,$header, $input_name = "", $extra = "")
	{
		$this->field = $field;
		$this->source_id = $sourcd_id;
		$this->source_display = $source_display;
		$this->source_table = $source_table;
		$this->source_order = $source_order;
		$this->source_header = JText::_($header);
		$this->input_name = $input_name;
		if (!$this->input_name)
			$this->input_name = $field;
		$this->extra = $extra;
	}
}