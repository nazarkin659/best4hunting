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
 * View to edit
 */
class VmreporterViewBymanufacturer extends JView
{
	protected $state;
	protected $item;
	protected $form;

	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
		$this->state	= $this->get('State');
		$this->item		= $this->get('Item');
		$this->form		= $this->get('Form');
		$list = array();
		

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
            throw new Exception(implode("\n", $errors));
		}

		
        $input = JFactory::getApplication()->input;
        $view = $input->getCmd('view', '');
        VmreporterHelper::addSubmenu($view);
		$id = JRequest::getInt('id');
		$mainframe = JFactory::getApplication();
		$model = JModel::getInstance("bymanufacturer","VmreporterModel");	
		$limit		= $mainframe->getUserStateFromRequest( 'global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int' );
		$limitstart = JRequest::getInt('limitstart',0);
		$getColoums = array(
						'order_number',
						'customer_name',
						'order_item_name',
						'product_attribute',
						'product_quantity',
						'product_subtotal_with_tax',
						'order_total',
						'order_currency',
						'created_on'
					   );
		$SelectedColoumns	   = $mainframe->getUserStateFromRequest('coloums','coloums',$getColoums,'array' );
		$report = $model->getDataofReport($id,$SelectedColoumns);
		$Count = count($report['reports']);
		$pageNav = new JPagination( $Count, $limitstart, $limit );
		$this->assignRef('SelectedColoumns',$SelectedColoumns);
		$this->assignRef('report',$report);
		$this->assignRef('pageNav',$pageNav);
		
		$this->addToolbar();
		
		if(JRequest::getVar('json')):
			$json_report = $model->getJsonReport($id);
			header('Content-type: application/json');
			echo json_encode($json_report);
			JFactory::getApplication()->close();
		endif;
		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 */
	protected function addToolbar()
	{
		
		$report = $this->report['reports'];
		JToolBarHelper::title(JText::_('COM_VMREPORTER').': '.JText::_('COM_VMREPORTER_TITLE_BYMANUFACTURER').': '.$this->report["name"], 'by-manufacturer.png');
		if (empty($this->item->id)) {
			JToolBarHelper::cancel('bymanufacturer.cancel', 'JTOOLBAR_CANCEL');
		}
		else {
			JToolBarHelper::cancel('bymanufacturer.cancel', 'JTOOLBAR_CLOSE');
		}

	}
	
}
