<?php
/*------------------------------------------------------------------------------------------------------------
# VP One Page Checkout! Joomla 2.5 Plugin for VirtueMart 2.0 / VirtueMart 2.6
# ------------------------------------------------------------------------------------------------------------
# Copyright (C) 2012 - 2014 VirtuePlanet Services LLP. All Rights Reserved.
# License - GNU General Public License version 2. http://www.gnu.org/licenses/gpl-2.0.html
# Author: VirtuePlanet Services LLP
# Email: info@virtueplanet.com
# Websites:  http://www.virtueplanet.com
------------------------------------------------------------------------------------------------------------*/
defined('_JEXEC') or die;

class ProOPCHelper
{
	public function getUsers()
	{
		$app	= JFactory::getApplication();
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		$query->select($db->quoteName('username'));
		$query->select($db->quoteName('email'));
		$query->from($db->quoteName('#__users'));			
		$db->setQuery($query);
		$users = $db->loadObjectList();
		$session = JFactory::getSession();
		$session->set('ProOPC', $users, 'users');
	}
}
