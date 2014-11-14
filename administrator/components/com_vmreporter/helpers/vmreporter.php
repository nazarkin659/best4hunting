<?php
/**
 * @version     1.0.0
 * @package     com_vmreporter
 * @copyright   Copyright (C) 2013 VirtuePlanet Services LLP. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      VirtuePlanet Services LLP <info@virtueplanet.com> - http://www.virtueplanet.com
 */

// No direct access
defined('_JEXEC') or die;

/**
 * Vmreporter helper.
 */
class VmreporterHelper
{
	/**
	 * Configure the Linkbar.
	 */
	public static function addSubmenu($vName = '')
	{
		JSubMenuHelper::addEntry(
			JText::_('COM_VMREPORTER_TITLE_NAME'),
			'index.php?option=com_vmreporter',
			$vName == 'frontpage'
		);
		JSubMenuHelper::addEntry(
			JText::_('COM_VMREPORTER_TITLE_BYPRODUCTS'),
			'index.php?option=com_vmreporter&view=byproducts',
			$vName == 'byproducts'
		);
		JSubMenuHelper::addEntry(
			JText::_('COM_VMREPORTER_TITLE_BYCATEGORIES'),
			'index.php?option=com_vmreporter&view=bycategories',
			$vName == 'bycategories'
		);		
		JSubMenuHelper::addEntry(
			JText::_('COM_VMREPORTER_TITLE_BYMANUFACTURERS'),
			'index.php?option=com_vmreporter&view=bymanufacturers',
			$vName == 'bymanufacturers'
		);
		JSubMenuHelper::addEntry(
			JText::_('COM_VMREPORTER_TITLE_BYCUSTOMERS'),
			'index.php?option=com_vmreporter&view=bycustomers',
			$vName == 'bycustomers'
		);
		JSubMenuHelper::addEntry(
			JText::_('COM_VMREPORTER_TITLE_BYCOUNTRIES'),
			'index.php?option=com_vmreporter&view=bycountries',
			$vName == 'bycountries'
		);


	}

	/**
	 * Gets a list of the actions that can be performed.
	 *
	 * @return	JObject
	 * @since	1.6
	 */
	public static function getActions()
	{
		$user	= JFactory::getUser();
		$result	= new JObject;

		$assetName = 'com_vmreporter';

		$actions = array(
			'core.admin', 'core.manage', 'core.create', 'core.edit', 'core.edit.own', 'core.edit.state', 'core.delete'
		);

		foreach ($actions as $action) {
			$result->set($action, $user->authorise($action, $assetName));
		}

		return $result;
	}
}
