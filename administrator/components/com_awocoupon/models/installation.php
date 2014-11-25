<?php
/**
 * @component AwoCoupon for Virtuemart
 * @copyright Copyright (C) Seyi Awofadeju - All rights reserved.
 * @license : GNU/GPL
 * @Website : http://awodev.com
 **/
 
// Disallow direct access to this file
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

class AwoCouponModelInstallation extends JModel
{
	var $_pagination = null;
	var $_id = null;

	function __construct() {
		parent::__construct();

		$limit		= JFactory::getApplication()->getUserStateFromRequest( AWOCOUPON_OPTION.'.limit', 'limit', JFactory::getApplication()->getCfg('list_limit'), 'int');
		$limitstart = JFactory::getApplication()->getUserStateFromRequest( AWOCOUPON_OPTION.'.limitstart', 'limitstart', 0, 'int' );

		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);

		$this->_data = null;

	}


	/**
	 * Method to get data
	 **/
	function getData() {
		// Lets load the files if it doesn't already exist
		if (empty($this->_data)) {
			$query = $this->_buildQuery();
			$this->_data = $this->_getList($query, $this->getState('limitstart'), $this->getState('limit'));
		}
		return $this->_data;
	}

	/**
	 * Method to get the total
	 **/
	function getTotal() {
		// Lets load the files if it doesn't already exist
		if (empty($this->_total)) {
			$query = $this->_buildQuery();
			$this->_total = $this->_getListCount($query);
		}

		return $this->_total;
	}
	
	/**
	 * Method to get a pagination object
	 **/
	function getPagination() {
		// Lets load the files if it doesn't already exist
		if (empty($this->_pagination)) {
			jimport('joomla.html.pagination');
			$this->_pagination = new JPagination( $this->getTotal(), $this->getState('limitstart'), $this->getState('limit') );
		}

		return $this->_pagination;
	}

	/**
	 * Method to build the query
	 **/
	function _buildQuery() {
	
		if(version_compare( JVERSION, '1.6.0', 'ge' )) {
			$sql = 'SELECT extension_id as id,name,element,folder,client_id,enabled,
							access,params,checked_out,checked_out_time,ordering
					  FROM #__extensions
					 WHERE type="plugin" AND element="awocoupon" AND folder="vmcoupon"';
		}
		else {
			$sql = 'SELECT id,name,element,folder,client_id,published as enabled,
							access,params,checked_out,checked_out_time,ordering
					  FROM #__plugins 
					 WHERE element="awocoupon" AND folder="vmcoupon"';
		}
	
		return $sql;
	}


	function publish($cid = array(), $publish = 1) {
		$user 	=& JFactory::getUser();

		if (count( $cid )) {
			$cids = implode( ',', $cid );

			$sql = version_compare( JVERSION, '1.6.0', 'ge' )
						? 'UPDATE #__extensions SET enabled='.(int)$publish.' WHERE extension_id IN ('.$cids.')'
						: 'UPDATE #__plugins SET published='.(int)$publish.' WHERE id IN ('.$cids.')';
			$this->_db->setQuery( $sql );
		
			if (!$this->_db->query()) {
				$this->setError($this->_db->getErrorMsg());
				return false;
			}
		}
		return true;
		
		
		
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
	

}
