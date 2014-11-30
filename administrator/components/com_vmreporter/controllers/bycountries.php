<?php
/**
 * @version     1.0.0
 * @package     com_vmreporter
 * @copyright   Copyright (C) 2013 VirtuePlanet Services LLP. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      VirtuePlanet Services LLP <info@virtueplanet.com> - http://www.virtueplanet.com
 */

// No direct access.
defined('_JEXEC') or die;

jimport('joomla.application.component.controlleradmin');

/**
 * Bymanufacturers list controller class.
 */
class VmreporterControllerBycountries extends JControllerAdmin
{
	/**
	 * Proxy for getModel.
	 * @since	1.6
	 */
	public function getModel($name = 'bycountries', $prefix = 'VmreporterModel')
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));
		return $model;
	}
    
    
	/**
	 * Method to save the submitted ordering values for records via AJAX.
	 *
	 * @return  void
	 *
	 * @since   3.0
	 */
	public function saveOrderAjax()
	{
		// Get the input
		$input = JFactory::getApplication()->input;
		$pks = $input->post->get('cid', array(), 'array');
		$order = $input->post->get('order', array(), 'array');

		// Sanitize the input
		JArrayHelper::toInteger($pks);
		JArrayHelper::toInteger($order);

		// Get the model
		$model = $this->getModel();

		// Save the ordering
		$return = $model->saveorder($pks, $order);

		if ($return)
		{
			echo "1";
		}

		// Close the application
		JFactory::getApplication()->close();
	}
	function createCSV()
	{
		$id = JRequest::getInt('id');
		$model = JModel::getInstance("bycountries","VmreporterModel");
		$reports = $model->createCSV($id);
	}
	function delete()
	{
		$mainframe = JFactory::getApplication();
		$db = JFactory::getDBO();
		$report_ids	= JRequest::getVar('cid', array(), '', 'array');
		$sql = "DELETE from #__vmreporter_bycountry WHERE id IN (".implode(',',$report_ids).")";
		$db->setQuery($sql);
		$db->query();
		$mainframe->redirect('index.php?option=com_vmreporter&view=bycountries','deleted');
	}
    
    
    
}