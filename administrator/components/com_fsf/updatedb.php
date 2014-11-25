<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;
?>
<?php
jimport('joomla.filesystem.file');
jimport( 'joomla.version' );
jimport( 'joomla.installer.installer' );
jimport('joomla.filesystem.path');
jimport('joomla.filesystem.folder');

require_once (JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_fsf'.DS.'adminhelper.php');
require_once (JPATH_SITE.DS.'components'.DS.'com_fsf'.DS.'helper'.DS.'j3helper.php');

global $fsjjversion;

class FSFUpdater
{
 	function DBIs16()
	{
		global $fsjjversion;
		if (empty($fsjjversion))
		{
			$version = new JVersion;
			$fsjjversion = 1;
			if ($version->RELEASE == "1.5")
				$fsjjversion = 0;
		}
		return $fsjjversion;
	}
	
	function GetExistingTables()
	{
		if (empty($this->existingtables))
		{
			$this->existingtables = array();
			$db	= JFactory::getDBO();

			$qry = "SHOW TABLES";
			$db->setQuery($qry);
			$existingtables_ = $db->loadAssocList();
			//print_r($existingtables_);
			$existingtables = array();
			foreach($existingtables_ as $existingtable_2)
			{
				foreach ($existingtable_2 as $existingtable)
				{
					$existingtable = str_replace($db->getPrefix(),'#__',$existingtable);
					$this->existingtables[$existingtable] = $existingtable;
				}
			}
		}
		
		return $this->existingtables;
	}
	
	function RestoreTableData($table, &$stuff, $checkexisting = true)
	{
		//print_p($stuff['data']);
		//exit;
		$db	= JFactory::getDBO();

		$log = "inserting data where missing\n";
		$prikeys = array();
		if (array_key_exists('index',$stuff))
		{
			foreach ($stuff['index'] as $index)
			{
				if ($index['Key_name'] == "PRIMARY")
				{
					$prikeys[$index['Seq_in_index']] = $index['Column_name'];		
				}
			}
		}
	
		if (array_key_exists('data',$stuff))
		{
			foreach($stuff['data'] as $id => $data)
			{
				if ($checkexisting)
				{
					$qry = "SELECT * FROM `$table` WHERE ";
			
					$where = array();
			
					foreach($prikeys as $prikey)
					{
						$where[] = "`$prikey` = '" . FSFJ3Helper::getEscaped($db, $data[$prikey]) ."'";		
					}
			
					if (count($where) > 0)
					{
						$qry .= implode(" AND ",$where);
						$db->setQuery($qry);
						$existing = $db->loadAssocList();
					} else {
						$existing = array();
					}
				} else {
					$existing = array();
				}
			
				if (count($existing) == 0)
				{
					$fields = array();
					$values = array();
			
					foreach ($data as $field => $value)
					{
						$fields[] = "`" . $field ."`";
						$values[] = "'" . FSFJ3Helper::getEscaped($db, $value) . "'";
					}
			
					$fieldlist = implode(", ", $fields);
					$valuelist = implode(", ", $values);
			
					$qry = "INSERT INTO `$table` ($fieldlist) VALUES ($valuelist)";
					$log .= $qry."\n";
					$db->setQuery($qry);$db->Query();
			
				}				
			}
		}
	
		return $log;
	}

	function CompareTable($table, &$stuff)
	{
		global $log;
		$db	= JFactory::getDBO();

		$log .= "Existing table\n";
	
		// COMPARE FIELDS
		{
			$qry = "DESCRIBE $table";
			$db->setQuery($qry);
			$existing_ = $db->loadAssocList();
			$existing = array();
		
			foreach ($existing_ as $field)
			{
				$existing[$field['Field']] = $field;	
			}
		
			foreach ($stuff['fields'] as $field)
			{
				$fieldname = $field['Field'];
				if (array_key_exists($fieldname,$existing))
				{
					$log .= "Compare field $fieldname\n";	
					$existingfield = $existing[$fieldname];
					$same = true;
				
					if ($existingfield['Type'] != $field['Type'])
						$same = false;
					if ($existingfield['Null'] != $field['Null'])
						$same = false;
					if ($existingfield['Default'] != $field['Default'])
						$same = false;
					if ($existingfield['Extra'] != $field['Extra'])
						$same = false;

					if (!$same)
					{
						$change = "ALTER TABLE `$table` CHANGE `$fieldname` `$fieldname` " . $field['Type'];
						if ($field['Null'] == "NO")
							$change .= " NOT NULL ";
						if ($field['Extra'] == "auto_increment")
							$change .= " AUTO_INCREMENT ";
						$log .= $change . "\n";
						$db->SetQuery($change);
						$db->Query();
					}

					//ALTER TABLE `jos_fsf_ticket_field` CHANGE `gfda` `iuytoiuyt` INT( 8 ) NOT NULL 
				} else {
					$log .= "New field $fieldname\n";	
				
					$change = "ALTER TABLE `$table` ADD `$fieldname` " . $field['Type'];
					if ($field['Null'] == "NO")
						$change .= " NOT NULL ";
					if ($field['Extra'] == "auto_increment")
						$change .= " AUTO_INCREMENT ";
					$log .= $change . "\n";
					$db->SetQuery($change);
					$db->Query();
					//ALTER TABLE `jos_fsf_ticket_field` ADD `gfda` INT( 4 ) NOT NULL 
				}
			}
		}
	
		// COMPARE INDEXES
		{
			$indexs = array();
			if (array_key_exists('index', $stuff))
			{
				foreach ($stuff['index'] as $index)
				{
					$indexs[$index['Key_name']][$index['Seq_in_index']] = $index;
				}
			}
			/*echo "<pre>NEW\n";
			print_r($indexs);
			echo "</pre>";*/
		
	
			$qry = "SHOW INDEX FROM $table";
			$db->setQuery($qry);
			$existing_ = $db->loadAssocList();
			$existing = array();
			foreach ($existing_ as $index)
			{
				$existing[$index['Key_name']][$index['Seq_in_index']] = $index;
			}
		
			/*echo "<pre>EXISTING\n";
			print_r($existing);
			echo "</pre>";*/
		
			foreach ($indexs as $index)
			{
				$createindex = false;
				$name = $index[1]['Key_name'];
				$log .= "Compare index " . $name . " - ";
				if (array_key_exists($name,$existing))
				{
					$log .= "exists\n";
					// compare indexes and their fields. BORING
					$same = true;
					foreach ($index as $id => $field)
					{
						if (!array_key_exists($id,$existing[$name]))
						{
							$log .= "index offset $id not exist\n";
							$same = false;
						} else {
							if ($field['Non_unique'] != $existing[$name][$id]['Non_unique'])
							{
								$log .= "Non_unique different\n";
								$same = false;
							}
							if ($field['Column_name'] != $existing[$name][$id]['Column_name'])
							{
								$log .= "Column_name different\n";
								$same = false;
							}
						}
					}
				
					if (count($existing[$name]) != count($index))
						$same = false;
				
					if (!$same)
					{
						$log .= "Index different.. dropping\n";
						$drop = "ALTER TABLE `$table` DROP INDEX `" . $name . "`";
						$log .= $drop . "\n";
						$db->SetQuery($drop);
						$db->Query();
						$createindex = true;
					}
				
				} else {
					$log .= "new\n";
					$createindex = true;
				}
			
				if ($createindex)
				{
					$log .= "Creating index $name\n";
				
					$fieldlist = array();
					foreach ($index as $id => $field)
					{
						$fieldlist[] = "`" . $field['Column_name'] . "`";	
					}
				
					$fieldlist = implode(", ",$fieldlist);
				
					$create = "ALTER TABLE `$table` ADD ";
				
					if ($index[1]['Key_name'] == "PRIMARY")
					{
						$create .= " PRIMARY KEY ";					
					} else if ($index[1]['Non_unique'] == 1)
					{
						$create .= " INDEX `$name` ";	
					} else {
						$create .= " UNIQUE `$name` ";	
					}
				
					$create .= "( " . $fieldlist . ")";
					$db->SetQuery($create);
					$db->Query();
				
					$log .= $create;
				}
			}
		}

	}

	function CreateTable($table, &$stuff)
	{
		global $log;
		$db	= JFactory::getDBO();

		$log .= "New table\n";
	
		$create = "CREATE TABLE IF NOT EXISTS `$table` (\n";
	
		$parts = array();
	
		foreach ($stuff['fields'] as $field)
		{
			$part = "`" . $field['Field'] . "` " . $field['Type'];
			if ($field['Null'] == "NO")
				$part .= " NOT NULL ";
			if ($field['Extra'] == "auto_increment")
				$part .= " AUTO_INCREMENT ";
			$parts[] = $part;
		}
	
	
		$indexs = array();
		foreach ($stuff['index'] as $index)
		{
			$indexs[$index['Key_name']][$index['Seq_in_index']] = $index;
		}
	
		if (array_key_exists("PRIMARY",$indexs))
		{
			$fields = "";
			foreach ($indexs['PRIMARY'] as $index)
			{
				$fields[] = "`" . $index['Column_name'] . "`";	
			}
			$fields = implode(", ",$fields);
		
			$part = "PRIMARY KEY (" . $fields . ")";
			$parts[] = $part;
		}
	
		foreach ($indexs as $name => $index)
		{
			if ($name == "PRIMARY")
				continue;
			
			$part = "UNIQUE KEY ";
		
			if ($index[1]['Non_unique'])
				$part = "KEY ";
		
			$part .= "`" . $index[1]['Key_name'] . "` (";
		
			$fields = array();
		
			foreach ($index as $field)
			{
				$fields[] = "`" . $field['Column_name'] . "`";	
			}
		
			$part .= implode(", ",$fields) . ")";
			$parts[] = $part;
		}
	
	
		$create = $create . implode(",\n",$parts) . "\n) ENGINE=MyISAM DEFAULT CHARSET=utf8";
		$db->SetQuery($create);
		$db->Query();
		
		//echo $create."<br>";
		
		$log .= $create."\n\n";
	}
	
	function LangAccess()
	{
		$log = "";
		$db	= JFactory::getDBO();
		
		$tables = array(
			
// ##NOT_TEST_START##	
			'#__fsf_faq_cat' => 1,
			'#__fsf_faq_faq' => 1,
// ##NOT_TEST_END##


// 


// 

		);
		
		foreach ($tables as $table => $langs)
		{
			if ($langs)
			{
				$query = "UPDATE $table SET language = '*' WHERE language = ''";
				$db->setQuery($query);
				$db->Query();
				$count = $db->getAffectedRows();
			
				if ($count > 0)
					$log .= "Set language for $count items in $table\n";		
			}
			$query = "UPDATE $table SET access = 1 WHERE access = 0";
			$db->setQuery($query);
			$db->Query();
			$count = $db->getAffectedRows();
			
			if ($count > 0)
				$log .= "Set access for $count items in $table\n";	
		}	
		
		
// ##NOT_TEST_START##	
		// fix faq tag languages
		$qry = "UPDATE #__fsf_faq_tags SET language = (SELECT language FROM #__fsf_faq_faq as f WHERE f.id = #__fsf_faq_tags.faq_id) WHERE language = ''";
		$db->setQuery($qry);
		$db->Query();
// ##NOT_TEST_END##
			
		if ($log == "")
			$log = "All data has valid language and access data";
		
		return $log;	
	}
	
	function Process($path = "")
	{
		$log = array();
		
		$log[] = array('name' => 'Updating database', 'log' => $this->UpdateDatabase($path));

		$log[] = array('name' => 'Copy Category Images', 'log' => $this->CopyImages());

// 

		$log[] = array('name' => 'Sort any missing db entries', 'log' => $this->DataEntries($path));
		$log[] = array('name' => 'Sort missing Language and access entries', 'log' => $this->LangAccess());
		//$log[] = array('name' => 'Validate Joomla Admin Menu Entries', 'log' => $this->ValidateMenus());

// 


		$log[] = array('name' => 'Registering new modules', 'log' => $this->RegisterModules($path));
		$log[] = array('name' => 'Check content author', 'log' => $this->AddArticlesToAuthor());
		$log[] = array('name' => 'Update version number', 'log' => $this->UpdateVersion($path));
		
// 
		return $log;	
	}

	function Misc()
	{
		$log = "";
		
		if (FSF_Helper::Is16())
		{
			// update #__updates table to have longer version field
			$qry = "ALTER TABLE #__updates CHANGE version version VARCHAR( 20 ) DEFAULT NULL";		
			$db = JFactory::getDBO();
			$db->setQuery($qry);
			$db->Query();
		}
		
		return $log;
	}

	// copy product, category, and department images
	function CopyImages()
	{
		$log = "";

		$sourcepath = JPATH_SITE.DS.'components'.DS.'com_fsf'.DS.'assets'.DS.'icons';
		$destbase = JPATH_SITE.DS.'images'.DS.'fsf';
		if (!JFolder::exists($sourcepath))
		{
			$log .= "Source path doesnt exist";
			return $log;
		}
		
		if (!JFolder::exists($destbase))
		{
			if (!JFolder::create($destbase))
			{
				$log .= "Unable to create $destbase<br>";
				return $log;
			}
		}
	
		$destpaths = array('faqcats', 'kbcats', 'products', 'departments');
	
		foreach ($destpaths as $destpath)
		{			
			$path = $destbase.DS.$destpath;
			if (JFolder::exists($path))
			{
				// destination exists, so images must already be copied, dont do it again
				$log .= "Skipping $destpath, images aleady copied\n";
				continue;
			}
			
			if (!JFolder::exists($path))
			{
				if (!JFolder::create($path))
				{
					$log .= "Unable to create $path\n";
					continue;
				}
			}
		
			$files = JFolder::files($sourcepath);
		
			foreach ($files as $file)
			{
				$destfile = $path.DS.$file;
				$sourcefile = $sourcepath.DS.$file;
			
				if (!JFile::exists($destfile))
				{
					JFile::copy($sourcefile,$destfile);	
					$log .= "Copied image $sourcefile to $destfile<br>";
				} else {
					$log .= "Image $sourcefile already exists in $path<br>";	
				}
			}
		}

		return $log;
	}
	
	// copy menu images. TODO: add overwrite options to this
	function CopyMenuImages()
	{
		$log = "";
		$sourcepath = JPATH_SITE.DS.'components'.DS.'com_fsf'.DS.'assets'.DS.'mainicons';
		$destbase = JPATH_SITE.DS.'images'.DS.'fsf';
		if (!JFolder::exists($sourcepath))
		{
			$log .= "Source path doesnt exist";
			return $log;
		}

		if (!JFolder::exists($destbase))
		{
			if (!JFolder::create($destbase))
			{
				$log .= "Unable to create $destbase";
				return $log;
			}
		}
	
	
		$destpaths = array('menu');
	
		foreach ($destpaths as $destpath)
		{			
			$path = $destbase.DS.$destpath;
	
			if (JFolder::exists($path))
			{
				// destination exists, so images must already be copied, dont do it again
				$log .= "Skipping, images aleady copied\n";
				continue;
			}

			if (!JFolder::exists($path))
			{
				if (!JFolder::create($path))
				{
					$log .= "Unable to create $path";
				}
			}
		
			$files = JFolder::files($sourcepath);
		
			foreach ($files as $file)
			{
				$destfile = $path.DS.$file;
				$sourcefile = $sourcepath.DS.$file;
			
				if (!JFile::exists($destfile))
				{
					JFile::copy($sourcefile,$destfile);	
					$log .= "Copied image $sourcefile to $destfile<br>";
				} else {
					$log .= "Image $sourcefile already exists in $path<br>";	
				}
			}
		}

		return $log;
	}

	// sort some settings for 1.9 upgrade	
	function SortSettings()
	{
		$db	= JFactory::getDBO();
	
		$db->SetQuery("DELETE FROM #__fsf_settings WHERE setting = 'datetime_0'");
		$db->Query();
		
		$db->SetQuery("DELETE FROM #__fsf_settings WHERE setting = 'datetime_1'");
		$db->Query();
		
		$db->SetQuery("DELETE FROM #__fsf_settings WHERE setting = 'datetime_2'");
		$db->Query();
		
		$db->SetQuery("DELETE FROM #__fsf_settings WHERE setting = 'datetime_3'");
		$db->Query();
		
		$db->SetQuery("DELETE FROM #__fsf_settings WHERE setting = 'datetime_4'");
		$db->Query();
		
		$db->SetQuery("DELETE FROM #__fsf_settings WHERE setting = 'datetime_5'");
		$db->Query();
		
		$db->SetQuery("DELETE FROM #__fsf_settings WHERE setting = 'datetime_6'");
		$db->Query();
		
		$db->SetQuery("DELETE FROM #__fsf_settings WHERE setting = 'datetime_7'");
		$db->Query();
		
		$db->SetQuery("DELETE FROM #__fsf_settings WHERE setting = 'LICKEY'");
		$db->Query();
	
		$db->Setquery("REPLACE INTO #__fsf_settings_big (setting, value) SELECT * FROM #__fsf_settings WHERE setting = 'display_popup'");	
		$db->Query();
		
		$db->SetQuery("DELETE FROM #__fsf_settings WHERE setting = 'display_popup'");
		$db->Query();
		
		return "Done<br>";
	}

	// convert comments to 1.9 comments
	function ConvertComments()
	{
		$log = "";
		// process KB Comments
		$db	= JFactory::getDBO();

		$existingtables = $this->GetExistingTables();
		
		// copy old kb comments table into new general comments table
		if (array_key_exists('#__fsf_kb_comment',$existingtables))
		{
			$qry = "INSERT INTO #__fsf_comments (ident, itemid, name, email, website, body, created, published) SELECT 1 as ident, kb_art_id as itemid, name, email, website, body, created, published FROM #__fsf_kb_comment";
			$db->SetQuery($qry);
			$db->Query();

			$count = $db->getAffectedRows();
			$qry = "DROP TABLE #__fsf_kb_comment";
			$db->SetQuery($qry);
			$db->Query();
			
			$log .= "Converting $count KB Comments to new combined comments<br>";
		} else {
			$log .= "KB Comments ok<br>";	
		}
	
		// copy old kb comments table into new general comments table
		if (array_key_exists('#__fsf_test',$existingtables))
		{
			$qry = "INSERT INTO #__fsf_comments (ident, itemid, name, email, website, body, created, published) SELECT 5 as ident, prod_id as itemid, name, email, website, body, added as created, published FROM #__fsf_test";
			$db->SetQuery($qry);
			$db->Query();

			$count = $db->getAffectedRows();
			$qry = "DROP TABLE #__fsf_test";
			$db->SetQuery($qry);
			$db->Query();
			
			$log .= "Converting $count Testimonials to new combined comments<br>";
		} else {
			$log .= "Testimonials ok<br>";	
		}
	
		$qry = "UPDATE #__fsf_comments SET published = 2 WHERE published = -1";
		$db->SetQuery($qry);
		$db->Query();
		
		return $log;
	}

	// convert templates table to 1.9 style
	function ConvertTemplatesTable()
	{
		$log = "";
		$db	= JFactory::getDBO();
		$existingtables = $this->GetExistingTables();
		
		// alter templates table
		if (array_key_exists('#__fsf_ticket_templates',$existingtables) && !array_key_exists('#__fsf_templates',$existingtables))
		{
			$qry = "RENAME TABLE #__fsf_ticket_templates TO #__fsf_templates;";
			$db->SetQuery($qry);
			$db->Query();
		
			$qry = "ALTER TABLE #__fsf_templates CHANGE `head` `tpltype` INT( 11 ) NOT NULL";
			$db->SetQuery($qry);
			$db->Query();
			
			$log .= "Converting on Ticket templates to templates table<br>";
		} else {
			$log .= "Templates table is ok<br>";
		}
		
		return $log;	
	}
	
	// remove any old unused tables
	function RemoveOldTables()
	{
		$existingtables = $this->GetExistingTables();
		
		$db	= JFactory::getDBO();

		$log = "";	
		// delete old fields table
		if (array_key_exists('#__fsf_ticket_fields',$existingtables))
		{
			$qry = "DROP TABLE #__fsf_ticket_fields";
			$db->SetQuery($qry);
			$db->Query();
			
			$log .= "Removing _fsf_ticket_fields<br>";
		}	
	
		// delete old values table
		if (array_key_exists('#__fsf_ticket_values',$existingtables))
		{
			$qry = "DROP TABLE #__fsf_ticket_values";
			$db->SetQuery($qry);
			$db->Query();
		
			$log .= "Removing _fsf_ticket_values<br>";
		}	
		
		if ($log == "")
			$log = "No old tables to remove<br>";
			
		$qry = "DELETE FROM #__fsf_emails WHERE tmpl IN ('kb_comment_mod', 'kb_comment_unmod', 'test_mod', 'test_unmod')";
		$db->setQuery($qry);$db->Query();
			
		return $log;
	}
	
	// update database structure
	function UpdateDatabase($path = "")
	{
		$log = "";
	
		if ($path)
		{
			$updatefile = $path . DS . 'admin' . DS . 'database_fsf.dat';
		} else {
			$updatefile = JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_fsf'.DS.'database_fsf.dat';
		}

		if (!file_exists($updatefile))
		{
			$log .= "Unable to open update file $updatefile\n";
			return;	
		}
		$db	= JFactory::getDBO();
	
		$data = file_get_contents($updatefile);
		$data = unserialize($data);
	
		$log = "";
	
		$qry = "SHOW TABLES";
		$db->setQuery($qry);
		$existingtables_ = $db->loadAssocList();
		$existingtables = array();
		foreach($existingtables_ as $existingtable)
		{
			foreach ($existingtable as $table)
				$existingtables[$table] = $table;
		}
	
		//print_p($existingtables);
		//print_p($data);
		//exit;
		
		foreach ($data as $table => $stuff)
		{
			$tabler = str_replace('jos_',$db->getPrefix(),$table);
			$log .= "\n\nProcessing table $table as $tabler\n";

			

			if (array_key_exists($tabler,$existingtables))
			{
				$this->CompareTable($tabler, $stuff);
			} else {
				$this->CreateTable($tabler, $stuff);
			}
		
			if (array_key_exists('data',$stuff))
			{
				$log .= $this->RestoreTableData($tabler,$stuff);
			}
		}
	
		//echo $log."<br>";
		return $log;
	}
	
	// validate joomla menu entries
	function ValidateMenus()
	{
		$log = "";
	
		if (FSFJ3Helper::IsJ3())
		{
			
		} elseif ($this->DBIs16())
		{
			// no need at moment, as no added items for a 1.6 install
			$db	= JFactory::getDBO();
			$db->setQuery("SELECT * FROM #__menu WHERE link = 'index.php?option=com_fsf' AND menutype = 'main'");
			$component = $db->loadObjectList();
	
			$componentid = $component[0]->id;
			$componentid16 = $component[0]->component_id;


			if (file_exists(JPATH_COMPONENT.DS.'fsf.xml'))
			{
				//echo "<pre>";
				$order = 1;
				$xml = simplexml_load_file(JPATH_COMPONENT.DS.'fsf.xml');
				foreach ($xml->administration->submenu->menu as $item)
				{
					$name = (string)$item;
					//echo $name."<br>";
					$arr = $item->attributes();
					$link = $arr['link'];
					//echo $link."<br>";
					$alias = strtolower(str_replace("_","",$name));
		
					$qry = "SELECT * FROM #__menu WHERE link = 'index.php?$link' AND menutype = 'main'";
					//echo $qry."<br>";
					$db->setQuery($qry);
					$componentitem = $db->loadObject();
		
					if (!$componentitem)
					{
						//echo "Missing<br>";
						// item missing, create it
						$qry = "INSERT INTO #__menu (menutype, title, alias, path, link, type, parent_id, level, component_id, ordering, img, client_id) VALUES (";
						$qry .= " 'main', '$name', '$alias', 'freestylesupportportal/$alias', 'index.php?$link', 'component', $componentid, 2, $componentid16, $order, 'images/blank.png', 1)";
						$db->setQuery($qry);$db->Query();
						$log .= "Adding menu item $name<Br>";
					} else {
						//print_r($componentitem);
						$qry = "UPDATE #__menu SET title = '$name', ordering = $order WHERE id = " . $componentitem->id;
						//echo $qry."<br>";
						$db->setQuery($qry);$db->Query();
					}
		
					$order++;
				}

				//echo "</pre>";
		
				jimport( 'joomla.database.table.menu' );
				require JPATH_SITE.DS."libraries".DS."joomla".DS."database".DS."table".DS."menu.php";
		
				$table = new JTableMenu($db);
				$table->rebuild();
			}
		} else {
			// find base item
			$db	= JFactory::getDBO();
			$db->setQuery("SELECT * FROM #__components WHERE link = 'option=com_fsf' AND parent = 0");
			$component = $db->loadObjectList();
	
			$componentid = $component[0]->id;


			if (file_exists(JPATH_COMPONENT.DS.'fsf.xml'))
			{
				//echo "<pre>";
				$order = 1;
				$xml = simplexml_load_file(JPATH_COMPONENT.DS.'fsf.xml');
				foreach ($xml->administration->submenu->menu as $item)
				{
					$name = (string)$item;
					//$log .= $name."\n";
					$arr = $item->attributes();
					$link = $arr['link'];
					//$log .= $link."\n";
		
					$db->setQuery("SELECT * FROM #__components WHERE admin_menu_link = '$link'");
					$componentitem = $db->loadObject();
		
					if (!$componentitem)
					{
						// item missing, create it
						//echo "MISSING<br>";	
						$qry = "INSERT INTO #__components (name, parent, admin_menu_link, admin_menu_alt, `option`, ordering, admin_menu_img, iscore, enabled) VALUES (";
						$qry .= " '$name', $componentid, '$link', '$name', 'com_fsf', $order, 'images/blank.png', 0, 1)";
						$db->setQuery($qry);$db->Query();
						$log .= "Adding menu item $name<Br>";
					} else {
						//print_r($componentitem);
						$qry = "UPDATE #__components SET name = '$name', ordering = $order WHERE id = " . $componentitem->id;
						//$log .= $qry."<br>";
						$db->setQuery($qry);$db->Query();
					}
		
					$order++;
				}

				//echo "</pre>";
				$log .= "Base component id : $componentid<br>" . $log;
			}
		}
		
		if ($log == "")
			$log = "All admin menu items are ok<br>";
	
		return $log;
	}

	// if no products that have support, kb or testimonials active (upgrading from early version) sort this
	function SortInProd() 
	{
		$log = "";
		
		$db	= JFactory::getDBO();
		$db->setQuery("SELECT COUNT(*) as cnt FROM #__fsf_prod WHERE inkb > 0 OR intest > 0 OR insupport > 0");
		$count = $db->loadObject();
		if ($count->cnt == 0)
		{
			$db->setQuery("UPDATE #__fsf_prod SET inkb = 1, intest = 1, insupport = 1");
			$db->Query();	
			$log .= "Updating products to be shown on all sections<br>";
		} else {
			$log .= "Products assigned ok<br>";
		}
		
		return $log;
	}

	// relink any front end menu items to the correct component ids
	function RelinkMenuItems()
	{
		$log = "";

		// find new component id
		if ($this->DBIs16())
		{
			$db	= JFactory::getDBO();
			$db->setQuery("SELECT * FROM #__extensions WHERE type = 'component' AND element = 'com_fsf'");
			$component = $db->loadObjectList();
	
			$componentid = $component[0]->extension_id;
			if ($componentid)
			{
				$qry = "UPDATE #__menu SET component_id = $componentid WHERE link LIKE '%option=com_fsf%'";
				$db->setQuery($qry);$db->Query();
				$count = $db->getAffectedRows();
				$log .= "Relinked $count menu items<br>";
			} else {
				echo "No component def yet!<br>";	
			}
		} else {
			$db	= JFactory::getDBO();
			$db->setQuery("SELECT * FROM #__components WHERE link = 'option=com_fsf' AND parent = 0");
			$component = $db->loadObjectList();
	
			$componentid = $component[0]->id;
	
			$qry = "UPDATE #__menu SET componentid = $componentid WHERE link LIKE '%option=com_fsf%'";
			$db->setQuery($qry);$db->Query();
			$count = $db->getAffectedRows();
			$log .= "Relinked $count menu items<br>";
		}

		return $log;
	}

	// move any plugin files on upgrade of joomla >= 1.6
	function MovePluginFiles()
	{
		$log = "";
		if ($this->DBIs16())
		{
			$path[JPATH_SITE.DS.'plugins'.DS.'search'.DS][] = "fsf_announce.php";
			$path[JPATH_SITE.DS.'plugins'.DS.'search'.DS][] = "fsf_announce.xml";
			$path[JPATH_SITE.DS.'plugins'.DS.'search'.DS][] = "fsf_faqs.php";
			$path[JPATH_SITE.DS.'plugins'.DS.'search'.DS][] = "fsf_faqs.xml";
			$path[JPATH_SITE.DS.'plugins'.DS.'search'.DS][] = "fsf_kb.php";
			$path[JPATH_SITE.DS.'plugins'.DS.'search'.DS][] = "fsf_kb.xml";
			$path[JPATH_SITE.DS.'plugins'.DS.'system'.DS][] = "fsf_cron.php";
			$path[JPATH_SITE.DS.'plugins'.DS.'system'.DS][] = "fsf_cron.xml";
		
			foreach ($path as $spath => $files)
			{
				foreach($files as $file)
				{
					$folder = substr($file,0,strpos($file,"."));
				
					if (!JFolder::exists($spath.$folder))
						JFolder::create($spath.$folder);

					if (JFile::exists($spath.$file))
					{
						if (JFile::exists($spath.$folder.DS.$file))
							JFile::delete($spath.$folder.DS.$file);

						$log .= "Moving plugin file from J1.5 location to J1.6 location => $spath$file to $spath$folder".DS."$file<br>";
						JFile::move($spath.$file,$spath.$folder.DS.$file);
					}
				}	
			}
		
		}
			
		if ($log == "")
			$log .= "Plugins in correct location<br>";	
		
		return $log;
	}
	
	// setup plugin db entries if incorrect
	function SetupPluginDBEntries()
	{
		$db	= JFactory::getDBO();
		$log = "";

		if (!$this->DBIs16())
		{
		
			$qry = "SELECT * FROM #__plugins WHERE folder = 'search' AND element = 'fsf_announce'";
			$db->setQuery($qry);
			$item = $db->loadObject();
			if (!$item)
			{
				$qry = "INSERT INTO #__plugins (name, element, folder, iscore) VALUES ('Search - Freestyle Announcements','fsf_announce','search',0)";
				$db->setQuery($qry);$db->Query();
				$log .= "Adding Announcements Search plugin<br>";
			}

			$qry = "SELECT * FROM #__plugins WHERE folder = 'search' AND element = 'fsf_faqs'";
			$db->setQuery($qry);
			$item = $db->loadObject();
			if (!$item)
			{
				$qry = "INSERT INTO #__plugins (name, element, folder, iscore) VALUES ('Search - Freestyle FAQs','fsf_faqs','search',0)";
				$db->setQuery($qry);$db->Query();
				$log .= "Adding FAQs Search plugin<br>";
			}

			$qry = "SELECT * FROM #__plugins WHERE folder = 'search' AND element = 'fsf_kb'";
			$db->setQuery($qry);
			$item = $db->loadObject();
			if (!$item)
			{
				$qry = "INSERT INTO #__plugins (name, element, folder, iscore) VALUES ('Search - Freestyle Knowledge Base','fsf_kb','search',0)";
				$db->setQuery($qry);$db->Query();
				$log .= "Adding Knowledge Base Search plugin<br>";
			}

			$qry = "SELECT * FROM #__plugins WHERE folder = 'system' AND element = 'fsf_cron'";
			$db->setQuery($qry);
			$item = $db->loadObject();
			if (!$item)
			{
				$qry = "INSERT INTO #__plugins (name, element, folder, iscore) VALUES ('System - Freestyle CRON Plugin','fsf_cron','system',0)";
				$db->setQuery($qry);$db->Query();
				$log .= "Adding Knowledge Base Search plugin<br>";
			}

			if (!$log)
				$log = "All search plugins registered<Br>";

		} else {
			$installer = JInstaller::getInstance();

			$qry = "SELECT * FROM #__extensions WHERE type= 'plugin' AND folder = 'search' AND element = 'fsf_announce'";
			$db->setQuery($qry);
			$item = $db->loadObject();
			if (!$item)
			{
				$qry = "INSERT INTO #__extensions (name, type, element, folder, enabled) VALUES ('Search - Freestyle Announcements','plugin','fsf_announce','search', 0)";
				$db->setQuery($qry);$db->Query();
				$log .= "Adding Announcements Search plugin<br>";
				$installer->refreshManifestCache($db->insertId());
			}

			$qry = "SELECT * FROM #__extensions WHERE type= 'plugin' AND folder = 'search' AND element = 'fsf_faqs'";
			$db->setQuery($qry);
			$item = $db->loadObject();
			if (!$item)
			{
				$qry = "INSERT INTO #__extensions (name, type, element, folder, enabled) VALUES ('Search - Freestyle FAQs','plugin','fsf_faqs','search', 0)";
				$db->setQuery($qry);$db->Query();
				$log .= "Adding FAQs Search plugin<br>";
				$installer->refreshManifestCache($db->insertId());
			}

			$qry = "SELECT * FROM #__extensions WHERE type= 'plugin' AND folder = 'search' AND element = 'fsf_kb'";
			$db->setQuery($qry);
			$item = $db->loadObject();
			if (!$item)
			{
				$qry = "INSERT INTO #__extensions (name, type, element, folder, enabled) VALUES ('Search - Freestyle Knowledge Base','plugin','fsf_cron','search', 0)";
				$db->setQuery($qry);$db->Query();
				$log .= "Adding Knowledge Base Search plugin<br>";
				$installer->refreshManifestCache($db->insertId());
			}

			$qry = "SELECT * FROM #__extensions WHERE type= 'plugin' AND folder = 'system' AND element = 'fsf_cron'";
			$db->setQuery($qry);
			$item = $db->loadObject();
			if (!$item)
			{
				$qry = "INSERT INTO #__extensions (name, type, element, folder, enabled) VALUES ('System - Freestyle CRON Plugin','plugin','fsf_cron','system', 0)";
				$db->setQuery($qry);$db->Query();
				$log .= "Adding Freestyle CRON Plugin plugin<br>";

				$installer->refreshManifestCache($db->insertId());
			}

			if (!$log)
				$log = "All search plugins registered<Br>";
		
		}
		return $log;
	}

	function AddArticlesToAuthor()
	{
		$log = "";
		$db	= JFactory::getDBO();
		
		$qry = "SELECT id FROM #__users WHERE username = 'admin'";
		$db->setQuery($qry);
		//echo $qry."<br>";
		$row = $db->LoadObject();

		
		if (!$row || $row->id < 1)
		{
			$log .= "Unable to find admin user\n";
			return $log;	
		}
		
		$id = $row->id;
		
// 
			
		$qry = "UPDATE #__fsf_faq_faq SET author = $id WHERE author = 0";
		$db->setQuery($qry);
		$db->query();
		$updated = $db->getAffectedRows();
		if ($updated > 0)
			$log .= "Linked $updated FAQs to user amdin<br>";
// ##NOT_TEST_END##
			
			
		if ($log == "")
			$log = "All content linked to users<br>";
			
		return $log;
	}

	// add support admin user
	function AddAdminUser()
	{
		$log = "";
		$db	= JFactory::getDBO();
		
		$qry = "SELECT id FROM #__users WHERE username = 'admin'";
		$db->setQuery($qry);
		//echo $qry."<br>";
		$row = $db->LoadObject();

		if (!$row)
		{
			$log .= "Unable to find user 'admin'\n";
			return $log;
		}
		
		$id = $row->id;
		if (! ($id > 0))
		{
			$log .= "Unable to find user 'admin'\n";
			return $log;
		}
		$qry = "SELECT * FROM #__fsf_user WHERE user_id = '$id'";
		//echo $qry."<br>";
		$db->SetQuery($qry);
		$row = $db->LoadObjectList();

		if (count ($row) == 0)
		{
			$qry = "INSERT INTO #__fsf_user (mod_kb, mod_test, support, user_id, autoassignexc, allprods, allcats, alldepts, artperm, groups) VALUES ";
			$qry .= "(1,1,1,$id,1,1,1,1,3,1)";
			//echo $qry."<br>";
			$db->SetQuery($qry);
			$db->Query();
			
			$log .= "Adding user $id as support admin<br>";
		}
		
		if ($log == "")
			$log .= "Admin user already added<br>";	
		
		return $log;
	}
	
	// add any missing data entries to tables
	function DataEntries($path = "")
	{
		$log = "";
		
		if ($path)
		{
			$updatefile = $path . DS . 'admin' . DS . 'data_fsf.xml';
		} else {
			$updatefile = JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_fsf'.DS.'data_fsf.xml';
		}

		if (!file_exists($updatefile))
		{
			$log .= "Unable to open data file $updatefile\n";
			return;	
		}
		$db	= JFactory::getDBO();
	
		$xmldata = file_get_contents($updatefile);
		$xmldata = preg_replace('~\s*(<([^>]*)>[^<]*</\2>|<[^>]*>)\s*~','$1',$xmldata);
		//$xmldata = $this->uncdata($xmldata);
		$xml = simplexml_load_string($xmldata,'SimpleXMLElement', LIBXML_NOCDATA);
		
		$sql = "SELECT * FROM #__fsf_data";
		$once_data = array();
		$db->setQuery($sql);
		$rows = $db->loadObjectList();
	
		foreach ($rows as $row)
		{
			$once_data[$row->table][$row->prikey] = 1;	
		}
	
		foreach($xml->table as $table)
		{
			$tablename = (string)$table->attributes()->name;
			$alwaysreplace = (string)$table->attributes()->alwaysreplace;
			$once = (int)$table->attributes()->once;
			
			if (!$alwaysreplace) $alwaysreplace = 0;
			$tablename = str_replace("jos_","#__",$tablename);
			$log .= "$tablename ($alwaysreplace)<br>";
			
			$keyfields = array();
			foreach ($table->keyfields->field as $field)
			{
				$keyfields[] = (string)$field;
			}
			
			foreach ($table->rows->row as $row)
			{
				$rowdata = array();
				
				foreach ($row->children() as $child)
				{	
					/*print_p($child);
					$cdata = $child->getCData();
					print_p($cdata);*/
					$rowdata[$child->getName()] = (string)$child;
					if ($child->attributes()->decode)
					{
						$rowdata[$child->getName()] = html_entity_decode((string)$child);	
					}
						
					/*if (strpos($rowdata[$child->getName()], "\n") > 0)
					{
						$rowdata[$child->getName()] = str_replace("\n",'\n', $rowdata[$child->getName()]);		
					}*/
				}
				//print_p($rowdata);
				
				if ($once)
				{
					// check to see if we have added the row or not
					// if we have the row in the fsf_data table, then skip it
					$prikey = array();
					foreach ($keyfields as $keyfield)
					{
						$prikey[] = $rowdata[$keyfield];	
					}
					
					$prikey = implode(":", $prikey);
						
					if (array_key_exists($tablename, $once_data) && array_key_exists($prikey, $once_data[$tablename]))
					{
						continue;
					}
										
					// not skipped, add to fsf_data table
					$qry = "INSERT INTO #__fsf_data (`table`, prikey) VALUES ('$tablename', '$prikey')";	
					$db->setQuery($qry);
					$db->Query();
				}				
				
				$replace = 0;
				
				//$log .= "Always Replace : $alwaysreplace<br>";
				
				if ($alwaysreplace)
				{
					$replace = 1;	
				} else if (count($keyfields) == 0) {
					$replace = 1;
				} else {
					$qry = "SELECT count(*) as cnt FROM $tablename WHERE ";
					$where = array();
					foreach ($keyfields as $keyfield)
					{
						$value = $rowdata[$keyfield];
						$where[] = "`$keyfield` = '$value'";
					}
					$qry .= implode(" AND ", $where);
					$db->setQuery($qry);
					//$log .= $qry."<br>";
					$result = $db->loadObject();
					if ($result->cnt == 0)
						$replace = 1;
				}
				
				if ($replace)
				{
					$qry = "REPLACE INTO $tablename (";
					
					$fieldnames = array();
					foreach ($rowdata as $fieldname => $value)
						$fieldnames[] = "`".$fieldname."`";
					$qry .= implode(", ", $fieldnames);
					
					$qry .= ") VALUES (";
					
					$values = array();
					foreach ($rowdata as $fieldname => $value)
						$values[] = "'".FSFJ3Helper::getEscaped($db, $value)."'";
					$qry .= implode(", ", $values);

					$qry .= ")";
					
					$log .= htmlentities($qry)."<br>";
					$db->setQuery($qry);
					$db->Query();

				}
			}
		}
		// load data_fsf.xml and put data entries into database
		return $log;
	}

	// update manifest cache for >= 1.6
	function RefreshManifest()
	{
		$log = "";
		if ($this->DBIs16())
		{
			// Attempt to refresh manifest caches
			$db = JFactory::getDbo();
			$query = "SELECT * FROM #__extensions WHERE element LIKE '%fsf%' OR name LIKE '%fsf%'";
			$db->setQuery($query);
			
			$extensions = $db->loadObjectList();
			
			$installer = new JInstaller();
			// Check for a database error.
			if ($db->getErrorNum())
			{
				$log .= JText::sprintf('JLIB_DATABASE_ERROR_FUNCTION_FAILED', $db->getErrorNum(), $db->getErrorMsg()).'<br />';
				return $log;
			}
			foreach ($extensions as $extension) {
				if (!$installer->refreshManifestCache($extension->extension_id)) {
					$log .= "ERROR updating manifest for {$extension->element} updated ok<br>";
				} else {
					$log .= "Manifest for {$extension->element} updated ok<br>";	
				}
			}
		} else {
			$log .= "No needed for Joomla 1.5<br>";	
		}
		
		return $log;
	}	
	
	function LinkTicketAttach()
	{
		$db = 	& JFactory::getDBO();
		$log = "";
		$qry = "SELECT * FROM #__fsf_ticket_attach WHERE message_id = 0";
		$db->setQuery($qry);
		
		$attachments = $db->loadObjectList();
		
		if (count($attachments) == 0)
			return "No orphaned attachments<br>";
			
		$attachids = array();
		
		foreach($attachments as &$attach)
		{
			$attachids[$attach->ticket_ticket_id] = $attach->ticket_ticket_id;
		}
		
		
		$qry = "SELECT * FROM #__fsf_ticket_messages WHERE ticket_ticket_id IN (" . implode(", ",$attachids) . ")";
		
		$db->setQuery($qry);
		
		$messages = $db->loadObjectList();
		
		$ticketlist = array();
		
		foreach($messages as &$message)
		{
			$ticketid = $message->ticket_ticket_id;
			if (!array_key_exists($ticketid,$ticketlist))
				$ticketlist[$ticketid] = array();
				
			$ticketlist[$ticketid][] = &$message;
		}
		
		foreach($attachments as &$attach)
		{
			$attachid = $attach->id;
			$ticketid = $attach->ticket_ticket_id;
			$time = strtotime($attach->added);
			//echo "$ticketid -> time : $time<br>";
			$best = 0;	
			$bestdiff = 99999999999;
			$besttime = "";
			if (array_key_exists($ticketid, $ticketlist))
			{
				foreach ($ticketlist[$ticketid] as &$message)
				{
					$msgtime = strtotime($message->posted);
					$diff = abs($msgtime - $time);
					
					if ($diff < $bestdiff)
					{
						$besttime = $message->posted;
						$best = $message->id;
						$bestdiff = $diff;		
					}
				}
					
				if ($best > 0)
				{
					//echo "Found Match - {$attach->added} ~= $besttime, $best, $bestdiff<br>";
					$qry = "UPDATE 	#__fsf_ticket_attach SET message_id = " . FSFJ3Helper::getEscaped($db, $best) . " WHERE id = " . FSFJ3Helper::getEscaped($db, $attachid);
					$db->setQuery($qry);
					//echo $qry."<br>";
					$log .= "Assigning attachment $attachid to message $best<br>";
					$db->Query($qry);
				} else {
					//echo "No match found<br>";
					$qry = "UPDATE 	#__fsf_ticket_attach SET message_id = -1 WHERE id = ".FSFJ3Helper::getEscaped($db, $attachid);
					$db->setQuery($qry);
					//echo $qry."<br>";
					$log .= "Unable to match attachment $attachid<br>";
					//$db->Query($qry);
				}
			} else {
				$qry = "UPDATE 	#__fsf_ticket_attach SET message_id = -1 WHERE id = ".FSFJ3Helper::getEscaped($db, $attachid);
				$db->setQuery($qry);
				//echo $qry."<br>";
				$db->Query($qry);
				$log .= "Unable to match attachment $attachid<br>";
			}
		}		
		
		return $log;
	}
	// backup database
	function BackupData($type)
	{
		$db	= JFactory::getDBO();

		$tablematch = $db->getPrefix() . $type . "_";

		$tables = array();
	
		$db->setQuery("SHOW TABLES");
		$existing = $db->loadAssocList();

		foreach ($existing as $row)
		{
			foreach($row as $field)
				$table = $field;	
		
			$tablex = str_replace($db->getPrefix(),"jos_",$table);
		
			if (substr($table,0, strlen($tablematch)) != $tablematch)
			{
				//echo "Skipping $table<br>";
				continue;
			} else {
				//echo "Backup $table as $tablex<br>";
			}

			$getdata[] = $table;		

			echo "Processing table $table as $tablex<br>";
			$field = 0;
		
			$res2 = mysql_query("DESCRIBE $table");
			while ($row2 = mysql_fetch_assoc($res2))
			{
				$tables[$tablex]['fields'][$field++] = $row2; 	
			}
		
			$res2 = mysql_query("SHOW INDEX FROM $table");
			$index = 0;
			while ($row2 = mysql_fetch_assoc($res2))
			{
				$row2['Table'] = str_replace($db->getPrefix(),"jos_",$row2['Table']);
				$tables[$tablex]['index'][$index++] = $row2; 	
			}
		}

		foreach ($getdata as $table)
		{
			$tablex = str_replace($db->getPrefix(),"jos_",$table);
			echo "Exporting table $table as $tablex<br>";
			$db->setQuery("SELECT * FROM $table");
			$existing = $db->loadAssocList();
			$rowno = 0;
			foreach ($existing as $row)
			{
				foreach ($row as $key => $value)
				{
					$tables[$tablex]['data'][$rowno][$key] = $value;
				}
				$rowno = $rowno + 1;
			}	
		}

		/*ob_end_clean();
    
		echo "<pre>";
		print_r($tables);
		echo "<pre>";*/
	
		$data = serialize($tables);
	
		ob_end_clean();
		header("Cache-Control: public, must-revalidate");
		header('Cache-Control: pre-check=0, post-check=0, max-age=0');
		header("Pragma: no-cache");
		header("Expires: 0"); 
		header("Content-Description: File Transfer");
		header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
		header("Content-Type: application/octet-stream");
		//header("Content-Length: ".(string)strlen($data));
		header('Content-Disposition: attachment; filename="fsf data backup.dat"');
		header("Content-Transfer-Encoding: binary\n");
		echo $data;
		exit;
	}

	// restore database
	function RestoreData(&$data)
	{
		global $log;
		$db	= JFactory::getDBO();

		/*echo "<pre>";
		print_r($data);
		echo "<pre>";  
	
		exit;*/

		foreach ($data as $table => $stuff)
		{
			$tabler = str_replace('jos_',$db->getPrefix(),$table);
			//$table = 
			// auto import of lite module stuff		
			if (strpos($tabler,"fsf") > 0)
			{
				$tabler = str_replace("fsf","fsf",$tabler);
			}
			if (strpos($tabler,"fst") > 0)
			{
				$tabler = str_replace("fst","fsf",$tabler);
			}
		
			if (array_key_exists('data',$stuff))
			{
				$qry = "TRUNCATE `$tabler`";
				$log .= $qry."\n";
				$db->setQuery($qry);$db->Query();
			
				$log .= "\n\nProcessing table " .$table ." as $tabler\n";

				$log .= $this->RestoreTableData($tabler,$stuff,true);
			}
		}
	}
	
	// REgister new modules
	function RegisterModules($path)
	{
		if (!FSFUpdater::DBIs16())
			return "Not needed for Joomla 1.5";		
			
		$log = "";
		$db = JFactory::getDBO();
		$qry = "SELECT * FROM #__extensions WHERE element = 'mod_fsf_support'";
		$db->setQuery($qry);
		$rows = $db->loadObjectList();
		
		if (count($rows) == 0)
		{
			$filename = JPATH_SITE.DS.'modules'.DS.'mod_fsf_support'.DS.'mod_fsf_support.xml';

			if (file_exists($filename))
			{
				//echo "<pre>";
				$order = 1;
				$xml = simplexml_load_file($filename);
				
				$name = $xml->name;
				//echo $name."<br>";
				$qry = "INSERT INTO #__extensions (name, type, element, client_id, enabled, access) VALUES ('".FSFJ3Helper::getEscaped($db, $name)."', 'module', 'mod_fsf_support', 0, 1, 0)";
				$db->setQuery($qry);
				$db->Query($qry);
				//exit;
				
				$log .= "Registering module $name\n";
				
				$installer = new JInstaller();
				// Check for a database error.
				if ($db->getErrorNum())
				{
					$log .= JText::sprintf('JLIB_DATABASE_ERROR_FUNCTION_FAILED', $db->getErrorNum(), $db->getErrorMsg()).'<br />';
					return $log;
				}
				$id = $db->insertid();
				if (!$installer->refreshManifestCache($id)) {
					$log .= "ERROR updating manifest for {$id} - $name updated ok<br>";
				} else {
					$log .= "Manifest for {$id} - $name updated ok<br>";	
				}
			
			} else {
				$log .= "XML file missing\n";		
			}		
		} else {
			$log .= "Support module already registered\n";	
		}
		
		return $log;
	}
	
	function UpdateVersion($path)
	{
		$version = FSFAdminHelper::GetVersion($path);
		
		$db = JFactory::getDBO();
		$qry = "REPLACE INTO #__fsf_settings (setting, value) VALUES ('version', '$version')";
		$db->SetQuery($qry);
		$db->Query();
		//echo $qry."<br>";
		$log = "Updating version to $version\n"; 	
		
		return $log;
	}
	
	function SortAPIKey($username = "", $apikey = "")
	{
		$db = JFactory::getDBO();
		
		$log = "";
		if ($username == "")
		{
			$qry = "SELECT * FROM #__fsf_settings WHERE setting = 'fsj_username'";
			$db->setQuery($qry);
			$row = $db->loadObject();
			if ($row)
			{
				$username = $row->value;	
			}
			$qry = "SELECT * FROM #__fsf_settings WHERE setting = 'fsj_apikey'";
			$db->setQuery($qry);
			$row = $db->loadObject();
			if ($row)
			{
				$apikey = $row->value;	
			}
		}
		
		if ($apikey == "" || $username == "")
		{
			$log = "No API key set\n";
			return $log;	
		}
		
		// find current component id
		$qry = "SELECT * FROM #__extensions WHERE element = 'com_fsf'";
		$db->setQuery($qry);
		$comp = $db->loadObject();
			
		if ($comp)
		{
			// delete from update sites where component is me
			$qry = "SELECT * FROM #__update_sites_extensions WHERE extension_id = {$comp->extension_id}";
			$db->setQuery($qry);
			$sites = $db->loadObjectList();
			foreach ($sites as $site)
			{
				$siteid = $site->update_site_id;
				$qry = "DELETE FROM #__update_sites WHERE update_site_id = {$siteid}";
				$db->setQuery($qry);
				$db->Query($qry);
			}
				
			$qry = "DELETE FROM #__update_sites_extensions WHERE extension_id = {$comp->extension_id}";
			$db->setQuery($qry);
			$db->Query($qry);
				
			// insert new record in to site
			$qry = "INSERT INTO #__update_sites (name, type, location, enabled) VALUES ('Freestyle FAQs Updates', 'collection', 'http://www.freestyle-joomla.com/update/list.php?username=".FSFJ3Helper::getEscaped($db, $username)."&apikey=".FSFJ3Helper::getEscaped($db, $apikey)."', 1)";
			$db->setQuery($qry);
			$db->Query();
				
			$site_id = $db->insertid();
				
			$qry = "INSERT INTO #__update_sites_extensions (update_site_id, extension_id) VALUES ($site_id, {$comp->extension_id})";
			$db->setQuery($qry);
			$db->Query();
			
			$log .= "Updater link appended with api information\n";
		} else {
			$log .= "Unable to find component\n";
		}
		return $log;	
	}
}

