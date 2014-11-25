<?php
/**
 * @version     1.0.0
 * @package     com_vmreporter
 * @copyright   Copyright (C) 2013 VirtuePlanet Services LLP. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      VirtuePlanet Services LLP <info@virtueplanet.com> - http://www.virtueplanet.com
 */


// no direct access
defined('_JEXEC') or die;

// Access check.
if (!JFactory::getUser()->authorise('core.manage', 'com_vmreporter')) 
{
	throw new Exception(JText::_('JERROR_ALERTNOAUTHOR'));
}

// Include dependancies
jimport('joomla.application.component.controller');

$controller	= JController::getInstance('Vmreporter');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();
