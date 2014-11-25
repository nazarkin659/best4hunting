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

jimport('joomla.application.component.view');

/**
 * View class for a list of Vmreporter.
 */
class VmreporterViewBycountries extends JView
{
	protected $items;
	protected $pagination;
	protected $state;

	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
		$this->state		= $this->get('State');
		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			throw new Exception(implode("\n", $errors));
		}
        
		$this->addToolbar();
        
        $input = JFactory::getApplication()->input;
        $view = $input->getCmd('view', '');
        VmreporterHelper::addSubmenu($view);
		$model = JModel::getInstance("bycountries","VmreporterModel");
		
		$mainframe = JFactory::getApplication();
		$SelectedCountries = $mainframe->getUserStateFromRequest('countries','countries',array('0'),'array' );
		$SelectedStauses = $mainframe->getUserStateFromRequest('status','status',array('0'),'array' );
		$this->assignRef('SelectedCountries',$SelectedCountries);		
		$this->assignRef('SelectedStauses',$SelectedStauses);		

		$getCountries = $model->getCountries();
		$this->assignRef('getCountries', $getCountries);
		$getOrders = $model->getOrderStatus();
		$this->assignRef('getOrders',$getOrders);
		jimport('joomla.html.pagination');
		$mainframe = JFactory::getApplication();
		$limit		= $mainframe->getUserStateFromRequest( 'global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int' );
		$limitstart = JRequest::getInt('limitstart',0);
		$post = JRequest::get('post');
		if(isset($post['generate'])):
		$getCondition = $model->getCondition($post);
		endif;
		$CountofReports = $model->countReports();
		$pageNav = new JPagination( $CountofReports, $limitstart, $limit );
		$reports = $model->getReports($pageNav);
		$this->assignRef('pageNav',$pageNav);
		$this->assignRef('reports',$reports);       
		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @since	1.6
	 */
	protected function addToolbar()
	{
		require_once JPATH_COMPONENT.'/helpers/vmreporter.php';

		$state	= $this->get('State');
		$canDo	= VmreporterHelper::getActions($state->get('filter.category_id'));

		JToolBarHelper::title(JText::_('COM_VMREPORTER').': '.JText::_('COM_VMREPORTER_TITLE_BYCOUNTRIES'), 'by-countries.png');

		JToolBarHelper::deleteList('' , 'bycountries.delete');		

	}
}
