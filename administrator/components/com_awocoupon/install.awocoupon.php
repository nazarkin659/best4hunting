<?php
/**
 * @component AwoCoupon for Virtuemart
 * @copyright Copyright (C) Seyi Awofadeju - All rights reserved.
 * @license : GNU/GPL
 * @Website : http://awodev.com
 **/


global $install_manifest, $install_source;
$install_manifest = $this->manifest;
$install_source = $this->parent->getPath('source');


function com_install(){

	if(version_compare( JVERSION, '1.6.0', 'ge' )) awocouponinstall_tableupdatedj2_db();

	global $install_manifest, $install_source;
	
	require_once JPATH_ADMINISTRATOR.'/components/com_awocoupon/awocoupon.config.php';

	awocouponinstall_updates();
	awocouponinstall_migrate_vmcoupons();
	awocouponinstall_install_plugins($install_manifest,$install_source);
		
	// Clear Caches
	$cache = JFactory::getCache();
	$cache->clean('com_awocoupon');

}
function com_uninstall(){

	global $install_manifest, $install_source;

	echo '<div><b>Database Tables Uninstallation: <font color="green">Successful</font></b></div>';

	awocouponinstall_uninstall_plugins($install_manifest);
}
function awocouponinstall_tableupdatedj2_db() {
	// run install.mysql.sql file
	$db = JFactory::getDBO();
	$sqlfile = JPATH_ADMINISTRATOR.'/components/com_awocoupon/install.mysql.sql';
	// Don't modify below this line
	$buffer = file_get_contents($sqlfile);
	if ($buffer !== false) {
		jimport('joomla.installer.helper');
		$queries = JInstallerHelper::splitSql($buffer);
		if (count($queries) != 0) {
			foreach ($queries as $query) {
				$query = trim($query);
				if ($query != '' && $query{0} != '#') {
					$db->setQuery($query);
					if (!$db->query()) {
						JError::raiseWarning(1, JText::sprintf('JLIB_INSTALLER_ERROR_SQL_ERROR', $db->stderr(true)));
						return false;
					}
				}
			}
		}
	}
}


function awocouponinstall_updates() {
	
	$dbupgrades = array();
	if(!awocouponinstall_column_exists('#__'.AWOCOUPON,'function_type2')) {
	// upgrade to 2.0.9
		$dbupgrades[] = "ALTER TABLE #__".AWOCOUPON." ADD COLUMN `function_type2` enum('product','category') AFTER `function_type`;";
		$dbupgrades[] = "UPDATE #__".AWOCOUPON." SET `function_type2`='product';";
	}
	
	if(!empty($dbupgrades)) {
	$db			= JFactory::getDBO();
	//Apply Upgrades
	foreach ($dbupgrades AS $query) {
		$db->setQuery( $query );
		if(!$db->query()) {
			//Upgrade failed
			echo "<font color=red>".$dbupdate['message']." failed! SQL error:" . $db->stderr()."</font><br />";
		}
	}
	//Upgrade was successful
	echo "<div><b>Database Updates:</b> <font color=green>Upgrade Applied Successfully.</font></div>";			
	}
	

}

function awocouponinstall_tableupdatedj2() {
	// run install.mysql.sql file
	$db = JFactory::getDBO();
	$sqlfile = JPATH_ADMINISTRATOR.'/components/com_awocoupon/helpers/mysql.install.sql';
	// Don't modify below this line
	$buffer = file_get_contents($sqlfile);
	if ($buffer !== false) {
		jimport('joomla.installer.helper');
		$queries = JInstallerHelper::splitSql($buffer);
		if (count($queries) != 0) {
			foreach ($queries as $query) {
				$query = trim($query);
				if ($query != '' && $query{0} != '#') {
					$db->setQuery($query);
					if (!$db->query()) {
						JError::raiseWarning(1, JText::sprintf('JLIB_INSTALLER_ERROR_SQL_ERROR', $db->stderr(true)));
						return false;
					}
				}
			}
		}
	}
}

function awocouponinstall_column_exists($table,$column) {
	$db = JFactory::getDBO();
	$db->setQuery('DESC '.$table);
	$columns = $db->loadObjectList('Field');
	return isset($columns[$column]) ? true : false;
}
function awocouponinstall_migrate_vmcoupons() {
	$db			=& JFactory::getDBO();

	$sql = 'INSERT INTO #__'.AWOCOUPON.' (coupon_code,num_of_uses,coupon_value_type,coupon_value,discount_type,function_type,min_value,published,startdate,expiration)
				SELECT coupon_code,IF(coupon_type="gift",1,0),percent_or_total,coupon_value,"overall",
						IF(coupon_type="gift","giftcert","coupon"),coupon_value_valid,IF(published=1,1,-1),
						coupon_start_date,coupon_expiry_date
				  FROM #__virtuemart_coupons';
	$db->setQuery($sql);
	if(!$db->query()) echo "<div><b>Import of Virtuemart coupons: <font color=red>Unsuccessful</font></b></div>";
	else echo "<div><b>Import of Virtuemart coupons: <font color=green>Successful</font></b></div>";

}


function awocouponinstall_install_plugins($manifest, $src) {
	// Install plugins
	$db = & JFactory::getDBO();
	$is_success = false;
	if(version_compare( JVERSION, '1.6.0', 'ge' )) {
		$plugins = $manifest->xpath('plugins/plugin');
		foreach($plugins as $plugin){
			$pname = $plugin->getAttribute('plugin');
			$pgroup = $plugin->getAttribute('group');
			$path = $src.'/plugins/'.$pgroup;
			$installer = new JInstaller;
			$result = $installer->install($path);
			$status->plugins[] = array('name'=>$pname,'group'=>$pgroup, 'result'=>$result);
			$db->setQuery('UPDATE #__extensions SET enabled=1 WHERE type="plugin" AND element='.$db->Quote($pname).' AND folder='.$db->Quote($pgroup));
			$db->query();
			$is_success = true;
		}
	}
	else {
		$plugins = $manifest->getElementByPath('plugins');
		if (is_a($plugins, 'JSimpleXMLElement') && count($plugins->children())) {
			foreach ($plugins->children() as $plugin) {
				$pname = $plugin->attributes('plugin');
				$pgroup = $plugin->attributes('group');
				$path = $src.DS.'plugins'.DS.$pgroup;
				$installer = new JInstaller;
				$result = $installer->install($path);
				$status->plugins[] = array('name'=>$pname,'group'=>$pgroup, 'result'=>$result);

				$db->setQuery('UPDATE #__plugins SET published=1 WHERE element='.$db->Quote($pname).' AND folder='.$db->Quote($pgroup));
				$db->query();
				$is_success = true;
			}
		}
	}
	if($is_success) echo '<div><b>Plugin Installation: <font color="green">Successful</font></b></div>';
}

function awocouponinstall_uninstall_plugins($manifest) {
	$db = & JFactory::getDBO();
	$is_success = false;
	if(version_compare( JVERSION, '1.6.0', 'ge' )) {
		$plugins = $manifest->xpath('plugins/plugin');
		foreach ($plugins as $plugin) {
			$pname = $plugin->getAttribute('plugin');
			$pgroup = $plugin->getAttribute('group');
			$db->setQuery('SELECT `extension_id` FROM #__extensions WHERE `type`="plugin" AND element='.$db->Quote($pname).' AND folder='.$db->Quote($pgroup));
			$ids = $db->loadResultArray();
			if (count($ids)) {
				foreach ($ids as $id) {
					$installer = new JInstaller;
					$result = $installer->uninstall('plugin', $id);
				}
			}
			$is_success = true;
		}
	}
	else {
		$plugins = $manifest->getElementByPath('plugins');
		if (is_a($plugins, 'JSimpleXMLElement') && count($plugins->children())) {
			foreach ($plugins->children() as $plugin) {
				$pname = $plugin->attributes('plugin');
				$pgroup = $plugin->attributes('group');
				$db->setQuery('SELECT `id` FROM #__plugins WHERE element = '.$db->Quote($pname).' AND folder = '.$db->Quote($pgroup));
				$plugins = $db->loadResultArray();
				if (count($plugins)) {
					foreach ($plugins as $plugin) {
						$installer = new JInstaller;
						$result = $installer->uninstall('plugin', $plugin, 0);
					}
				}
				$is_success = true;
			}
		}
	}
	if($is_success) echo '<div><b>Plugin Uninstallation: <font color="green">Successful</font></b></div>';
}

