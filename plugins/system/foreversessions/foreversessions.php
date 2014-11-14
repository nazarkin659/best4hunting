<?php
/*
 * @version   2.0.15 Sun Mar 2 17:49:06 2014 -0800
 * @package   yoonique foreversessions
 * @author    yoonique[.]net
 * @copyright Copyright (C) yoonique[.]net All rights reserved
 * @license   GNU General Public License version 3
 */

defined('_JEXEC') or die('Restricted access');

jimport('joomla.plugin.plugin');

class  plgSystemForeversessions extends JPlugin {

	function plgSystemForeversessions(& $subject, $config) {
		parent::__construct($subject, $config);
	}

	function onBeforeCompileHead() {
		$user = JFactory::getUser();

		if ($user->guest)
			return;
		if (JFactory::getDocument()->getType() != 'html')
			return;
		$user_id = $user->get('id');
		$usergroups = JAccess::getGroupsByUser($user_id, $this->params->get('inherit', FALSE));
		if (array_intersect($usergroups, $this->params->get('usergroups', array()))) {
			JHtml::_('behavior.keepalive');
		}
		return;
	}

}
