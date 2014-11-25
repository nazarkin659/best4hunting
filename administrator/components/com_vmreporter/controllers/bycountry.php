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

jimport('joomla.application.component.controllerform');

/**
 * Byproduct controller class.
 */
class VmreporterControllerBycountry extends JControllerForm
{

    function __construct() {
        $this->view_list = 'bycountry';
        parent::__construct();
    }
	function cancel()
	{
		$mainframe = JFactory::getApplication();
		$mainframe->redirect('index.php?option=com_vmreporter&view=bycountries');
	}

}