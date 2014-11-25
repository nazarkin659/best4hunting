<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;

class FSF_Ticket_Helper
{
	static $counts;
	static function &getTicketCount()
	{
		FSF_Ticket_Helper::getAdminPermissions();
			
		if (empty(FSF_Ticket_Helper::$counts))
		{
			$db = JFactory::getDBO();
			$query = "SELECT count( * ) AS count, ticket_status_id FROM #__fsf_ticket_ticket WHERE 1 ";
			$query .= FSF_Ticket_Helper::$_perm_where;
			$query .= " GROUP BY ticket_status_id";
			
			$db->setQuery($query);
			$rows = $db->loadAssocList();
				
			$out = array();
			FSF_Ticket_Helper::GetStatusList();
			foreach (FSF_Ticket_Helper::$status_list as $status)
			{
				$out[$status->id] = 0;
			}
			
			if (count($rows) > 0)
			{
				foreach ($rows as $row)
				{
					$out[$row['ticket_status_id']] = $row['count'];
				}
			}
			
			// work out counts for allopen, closed, all, archived
			
			$archived = FSF_Ticket_Helper::GetStatusID("def_archive");
			$out['archived'] = 0;
			if (array_key_exists($archived, $out))
				$out['archived'] = $out[$archived];


			$allopen = FSF_Ticket_Helper::GetStatusIDs("is_closed", true);
			$out['allopen'] = 0;
			foreach ($allopen as $id)
			{
				if (array_key_exists($id, $out))
					$out['allopen'] += $out[$id];
			}
		
			
			$allclosed = FSF_Ticket_Helper::GetClosedStatus();
			$out['allclosed'] = 0;
			foreach ($allclosed as $id)
			{
				if (array_key_exists($id, $out))
					$out['allclosed'] += $out[$id];
			}

			
			$all = FSF_Ticket_Helper::GetStatusIDs("def_archive", true);
			$out['all'] = 0;
			foreach ($all as $id)
			{
				if (array_key_exists($id, $out))
					$out['all'] += $out[$id];
			}
			
			
			FSF_Ticket_Helper::$counts = $out;
		}
		return FSF_Ticket_Helper::$counts;	
	}
	
	static function ListHandlers($prodid, $deptid, $catid, $allownoauto = false, $assign_ticket = true)
	{
		$db = JFactory::getDBO();

		//echo "ListHandlers($prodid, $deptid, $catid, $allownoauto)<br>";

		// assign to any available user
		$qry = "SELECT user_id FROM #__fsf_user_prod WHERE prod_id = '".FSFJ3Helper::getEscaped($db, $prodid)."'";
		$db->setQuery($qry);
		$produsers = $db->loadAssocList('user_id');

		$qry = "SELECT user_id FROM #__fsf_user_prod_a WHERE prod_id = '".FSFJ3Helper::getEscaped($db, $prodid)."'";
		$db->setQuery($qry);
		$produsersa = $db->loadAssocList('user_id');

		$qry = "SELECT user_id FROM #__fsf_user_dept WHERE ticket_dept_id = '".FSFJ3Helper::getEscaped($db, $deptid)."'";
		$db->setQuery($qry);
		$deptusers = $db->loadAssocList('user_id');

		$qry = "SELECT user_id FROM #__fsf_user_dept_a WHERE ticket_dept_id = '".FSFJ3Helper::getEscaped($db, $deptid)."'";
		$db->setQuery($qry);
		$deptusersa = $db->loadAssocList('user_id');

		$qry = "SELECT user_id FROM #__fsf_user_cat WHERE ticket_cat_id = '".FSFJ3Helper::getEscaped($db, $catid)."'";
		$db->setQuery($qry);
		$catusers = $db->loadAssocList('user_id');

		$qry = "SELECT user_id FROM #__fsf_user_cat_a WHERE ticket_cat_id = '".FSFJ3Helper::getEscaped($db, $catid)."'";
		$db->setQuery($qry);
		$catusersa = $db->loadAssocList('user_id');

		$qry = "SELECT * FROM #__fsf_user";
		$db->setQuery($qry);
		$users = $db->loadAssocList();
		
		//print_p($users);

		$okusers = array();
		
		$count = 0;
		
		foreach ($users as $admin)
		{
			$juser = JFactory::getUser($admin['user_id']);
			//echo "User : {$juser->username}<br>";
			
			if ($admin['assignperms'] && $assign_ticket) // set up permissions when user has separate assign permissions
			{
				//echo "Assign Permission<br>";
				if ($admin['allprods_a'] == 0)
				{
					if (empty($produsersa[$admin['id']]))
					{
						//echo "Skip A : Not product<br>";
						continue;	
					}
				}

				if ($admin['alldepts_a'] == 0)
				{
					if (empty($deptusersa[$admin['id']]))
					{
						//echo "Skip A : Not Dept<br>";
						continue;	
					}
				}

				if ($admin['allcats_a'] == 0)
				{
					if (empty($catusersa[$admin['id']]))
					{
						//echo "Skip A : Not Cat<br>";
						continue;	
					}
				}
			} else { // assign permissions combined with view permissions (old way, default)
				//echo "View Permissions<br>";
				
				if ($admin['allprods'] == 0)
				{
					if (empty($produsers[$admin['id']]))
					{
						//echo "Skip : Not product<br>";
						continue;	
					}
				}

				if ($admin['alldepts'] == 0)
				{
					if (empty($deptusers[$admin['id']]))
					{
						//echo "Skip : Not Dept<br>";
						continue;	
					}
				}

				if ($admin['allcats'] == 0)
				{
					if (empty($catusers[$admin['id']]))
					{
						//echo "Skip : Not Cat<br>";
						continue;	
					}
				}
			}
			
			if ($admin['autoassignexc'] > 0 && !$allownoauto)
			{
				continue;	
			}

			$okusers[] = $admin['id'];
		}
		
		//print_p($okusers);
		//exit;
		
		return $okusers;
	}
	
	static function AssignHandler($prodid, $deptid, $catid)
	{
		//echo "Assigning hander for $prodid, $deptid, $catid<br>";
		$admin_id = 0;
		
		$assignuser = FSF_Settings::get('support_autoassign');
		if ($assignuser == 1)
		{
			$okusers = FSF_Ticket_Helper::ListHandlers($prodid, $deptid, $catid);

			if (count($okusers) > 0)
			{
				$count = count($okusers);
				$picked = mt_rand(0,$count-1);
				$admin_id = $okusers[$picked];
			}
		}

		return $admin_id;
	}

	static function createRef($ticketid,$format = "",$depth = 0)
	{
		if ($format == "")
			$format = FSF_Settings::get('support_reference');

		if ($depth > 4)
			$format = "4L-4L-4L";

		preg_match_all("/(\d[LNX])/i",$format,$out);
		if (strpos($format, "{") !== false)
		{
			preg_match_all("/{([A-Za-z0-9]+)}/i",$format,$out);
			
			$key = $format;
			foreach($out[1] as $match)
			{
				$count = substr($match,0,1);
				$type = strtoupper(substr($match,1,1));
				if ($type == "" && (int)$count < 1)
				{
					$type = $count;
					$count = 1;
				}
				$replace = "";

				if ($type == "X")
				{
					$replace = sprintf("%0".$count."d",$ticketid);		
				} else if ($type == "N")
				{
					for ($i = 0; $i < $count; $i++)
					{
						$replace .= rand(0,9);	
					}		
				} else if ($type == "L")
				{
					for ($i = 0; $i < $count; $i++)
					{
						$replace .= chr(rand(0,25)+ord('A'));	
					}								
				} else if ($type == "D")
				{
					$replace = date("Y-m-d");	
				}
				
				$pos = strpos($key,"{".$match."}");
				if ($pos !== false)
				{
					$key = substr($key,0,$pos) . $replace . substr($key,$pos+strlen($match)+2);	
				}

			}
		} elseif (count($out) > 0)
		{
			$key = $format;
			foreach($out[0] as $match)
			{
				$count = substr($match,0,1);
				$type = strtoupper(substr($match,1,1));
				$replace = "";

				if ($type == "X")
				{
					$replace = sprintf("%0".$count."d",$ticketid);
						
				} else if ($type == "N")
				{
					for ($i = 0; $i < $count; $i++)
					{
						$replace .= rand(0,9);	
					}		
				} else if ($type == "L")
				{
					for ($i = 0; $i < $count; $i++)
					{
						$replace .= chr(rand(0,25)+ord('A'));	
					}								
				}
				
				$pos = strpos($key,$match);
				if ($pos !== false)
				{
					$key = substr($key,0,$pos) . $replace . substr($key,$pos+strlen($match));	
				}

			}
		} else {
			$key = FSF_Ticket_Helper::createRef($ticketid,"4L-4L-4L",$depth + 1);	
		}	
				
		$db = JFactory::getDBO();
		
		$query = "SELECT id FROM #__fsf_ticket_ticket WHERE reference = '".FSFJ3Helper::getEscaped($db, $key)."'";
		$db->setQuery($query);
		$rows = $db->loadAssoc();
		
		if ($rows)
		{
			$key = FSF_Ticket_Helper::createRef($ticketid,$format,$depth + 1);
		}		
		
		return $key;
	}

	static $status_list;
	static function GetStatusList()
	{
		// get a list of all status
		if (empty(FSF_Ticket_Helper::$status_list))
		{
			$db = JFactory::getDBO();
			$db->setQuery("SELECT * FROM #__fsf_ticket_status ORDER BY ordering");
			FSF_Ticket_Helper::$status_list = $db->loadObjectList();
		}
	}
	
	static function GetStatusByID($id)
	{
		FSF_Ticket_Helper::GetStatusList();
		
		if ($id == "open")
		{
			$ids = FSF_Ticket_Helper::GetStatusIDs("def_open");
			if (count($ids) > 0)
			{
				return FSF_Ticket_Helper::GetStatusByID($ids[0]);		
			}
		}

		foreach (FSF_Ticket_Helper::$status_list as $status)
		{
			if ($status->id == $id)
				return $status;
		}	
		
		return null;
	}
	
	static function GetStatus($type)
	{
		FSF_Ticket_Helper::GetStatusList();
		// returns the object of the status row with field $type set as 1	
	}
		
	static function GetStatuss($type, $not = false)
	{
		// returns the object of the status row with field $type set as 1	
		FSF_Ticket_Helper::GetStatusList();
		
		$rows = array();
		
		foreach (FSF_Ticket_Helper::$status_list as $status)
		{
			if ($not)
			{
				if ($status->$type == 0)
					$rows[] = $status;
			} else {
				if ($status->$type > 0)
					$rows[] = $status;
			}
		}

		return $rows;
	}
	
	static function GetStatusID($type)
	{
		FSF_Ticket_Helper::GetStatusList();
		foreach (FSF_Ticket_Helper::$status_list as $status)
		{
			if ($status->$type > 0)
				return (int)$status->id;
		}
		
		return 0;	
	}
	
	static function GetStatusIDs($type, $not = false)
	{
		FSF_Ticket_Helper::GetStatusList();
		
		$ids = array();
		
		foreach (FSF_Ticket_Helper::$status_list as $status)
		{
			if ($not)
			{
				if ($status->$type == 0)
					$ids[] = (int)$status->id;
			} else {
				if ($status->$type > 0)
					$ids[] = (int)$status->id;
			}
		}
		
		if (count($ids) == 0)
			$ids[] = 0;
		
		return $ids;
	}	
	
	static function GetClosedStatus()
	{
		FSF_Ticket_Helper::GetStatusList();
		
		$ids = array();
		
		foreach (FSF_Ticket_Helper::$status_list as $status)
		{
			if ($status->is_closed && ! $status->def_archive)
					$ids[(int)$status->id] = (int)$status->id;
		}
		
		if (count($ids) == 0)
			$ids[] = 0;
		
		return $ids;
	}
	
	static $_permissions;
	static $_perm_prods;
	static $_perm_depts;
	static $_perm_cats;
	static $_perm_where;
	static $_perm_only;
	
	static function getAdminPermissions()
	{
		if (empty(FSF_Ticket_Helper::$_permissions)) {
			
			$mainframe = JFactory::getApplication(); 
			global $option;
			$user = JFactory::getUser();
			
			$userid = $user->id;
			
			$db = JFactory::getDBO();
			$query = "SELECT * FROM #__fsf_user WHERE user_id = '".FSFJ3Helper::getEscaped($db, $userid)."'";
			$db->setQuery($query);
			FSF_Ticket_Helper::$_permissions = $db->loadAssoc();
			
			if (!FSF_Ticket_Helper::$_permissions)
			{
				FSF_Ticket_Helper::$_permissions['mod_kb'] = 0;
				FSF_Ticket_Helper::$_permissions['mod_test'] = 0;
				FSF_Ticket_Helper::$_permissions['support'] = 0;
				FSF_Ticket_Helper::$_permissions['seeownonly'] = 1;
				FSF_Ticket_Helper::$_permissions['autoassignexc'] = 1;
				FSF_Ticket_Helper::$_permissions['allprods'] = 1;
				FSF_Ticket_Helper::$_permissions['allcats'] = 1;
				FSF_Ticket_Helper::$_permissions['alldepts'] = 1;
				FSF_Ticket_Helper::$_permissions['artperm'] = 0;
				FSF_Ticket_Helper::$_permissions['id'] = 0;
				FSF_Ticket_Helper::$_permissions['groups'] = 0;
				FSF_Ticket_Helper::$_permissions['reports'] = 0;
				FSF_Ticket_Helper::$_permissions['settings'] = '';
			}
			FSF_Ticket_Helper::$_permissions['userid'] = $userid;
			
			FSF_Ticket_Helper::$_perm_only = '';
			FSF_Ticket_Helper::$_perm_prods = '';	
			FSF_Ticket_Helper::$_perm_depts = '';
			FSF_Ticket_Helper::$_perm_cats = '';	
			FSF_Ticket_Helper::$_permissions['perm_where'] = '';

			
// 


			// check for permission overrides for Joomla 1.6
			if (FSF_Settings::get('perm_article_joomla') || FSF_Settings::get('perm_mod_joomla'))
			{
				if (FSF_Helper::Is16())
				{
					$newart = 0;
					$newmod = 0;
					$user = JFactory::getUser();
					if ($user->authorise('core.edit.own','com_fsf'))
					{
						$newart = 1;
					}
					if ($user->authorise('core.edit','com_fsf'))
					{
						$newart = 2;
						$newmod = 1;
					}
					if ($user->authorise('core.edit.state','com_fsf'))
					{
						$newart = 3;	
						$newmod = 1;
					}
						
					if (FSF_Settings::get('perm_article_joomla') && $newart > FSF_Ticket_Helper::$_permissions['artperm'])
						FSF_Ticket_Helper::$_permissions['artperm'] = $newart;
					if (FSF_Settings::get('perm_mod_joomla') && $newmod > FSF_Ticket_Helper::$_permissions['mod_kb'])
						FSF_Ticket_Helper::$_permissions['mod_kb'] = $newmod;
					//
				} else {
					$newart = 0;
					$newmod = 0;
					$user = JFactory::getUser();
					if ($user->authorize('com_fsf', 'create', 'content', 'own'))
					{
						$newart = 1;
					}
					if ($user->authorize('com_fsf', 'edit', 'content', 'own'))
					{
						$newart = 2;
						$newmod = 1;
					}
					if ($user->authorize('com_fsf', 'publish', 'content', 'all'))
					{
						$newart = 3;
						$newmod = 1;
					}
						
					if (FSF_Settings::get('perm_article_joomla') && $newart > FSF_Ticket_Helper::$_permissions['artperm'])
						FSF_Ticket_Helper::$_permissions['artperm'] = $newart;
					if (FSF_Settings::get('perm_mod_joomla') && $newmod > FSF_Ticket_Helper::$_permissions['mod_kb'])
						FSF_Ticket_Helper::$_permissions['mod_kb'] = $newmod;
				}
			}
	
		}
		
		return FSF_Ticket_Helper::$_permissions;			
	}
	
}
