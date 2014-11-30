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

jimport('joomla.application.component.modeladmin');

/**
 * Vmreporter model.
 */
class VmreporterModelbycustomer extends JModelAdmin
{
	/**
	 * @var		string	The prefix to use with controller messages.
	 * @since	1.6
	 */
	protected $text_prefix = 'COM_VMREPORTER';


	/**
	 * Returns a reference to the a Table object, always creating it.
	 *
	 * @param	type	The table type to instantiate
	 * @param	string	A prefix for the table class name. Optional.
	 * @param	array	Configuration array for model. Optional.
	 * @return	JTable	A database object
	 * @since	1.6
	 */
	public function getTable($type = 'Bycustomer', $prefix = 'VmreporterTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	/**
	 * Method to get the record form.
	 *
	 * @param	array	$data		An optional array of data for the form to interogate.
	 * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
	 * @return	JForm	A JForm object on success, false on failure
	 * @since	1.6
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Initialise variables.
		$app	= JFactory::getApplication();

		// Get the form.
		$form = $this->loadForm('com_vmreporter.bycustomer', 'bycustomer', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form)) {
			return false;
		}

		return $form;
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return	mixed	The data for the form.
	 * @since	1.6
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_vmreporter.edit.bycustomer.data', array());

		if (empty($data)) {
			$data = $this->getItem();
            
		}

		return $data;
	}

	/**
	 * Method to get a single record.
	 *
	 * @param	integer	The id of the primary key.
	 *
	 * @return	mixed	Object on success, false on failure.
	 * @since	1.6
	 */
	public function getItem($pk = null)
	{
		if ($item = parent::getItem($pk)) {

			//Do any procesing on fields here if needed

		}

		return $item;
	}

	/**
	 * Prepare and sanitise the table prior to saving.
	 *
	 * @since	1.6
	 */
	protected function prepareTable(&$table)
	{
		jimport('joomla.filter.output');

		if (empty($table->id)) {

			// Set ordering to the last item if not set
			if (@$table->ordering === '') {
				$db = JFactory::getDbo();
				$db->setQuery('SELECT MAX(ordering) FROM #__vmreporter_bycustomer');
				$max = $db->loadResult();
				$table->ordering = $max+1;
			}

		}
	}
	
	public function getDataofReport($id,$SelectedColoumns)
	{
		$db = JFactory::getDBO();
		$FilterBy = JRequest::getVar('filterby', 'order_number');
		$FilterOrder = JRequest::getVar('dir', 'asc');
		$ordering = array();
		$sql = "SELECT * FROM  #__vmreporter_bycustomer WHERE id = ".$id;
		$db->setQuery($sql);
		$reports = $db->loadObjectList();
		$mixData = $reports[0];
		$reports = json_decode($mixData->report_details);
		$query = json_decode($mixData->report_query);
	    $sorter=array();
	    $ret=array();
	    reset($reports);
	    foreach ($reports as $ii => $va) {
	        $sorter[$ii]=$va->$FilterBy;
	    }
		if($FilterOrder == 'asc'):
	    asort($sorter);
		else:
		arsort($sorter);
		endif;
	    foreach ($sorter as $ii => $va) {
	        $ret[]=$reports[$ii];
	    }
	    $reports=$ret;
		$pColoums = array(	
				'order_item_name',
				'product_attribute',
				'product_quantity',
				'product_item_price',
				'product_tax',
				'product_basePriceWithTax',
				'product_final_price',
				'product_subtotal_discount',
				'product_subtotal_with_tax'
				);
		$pColExist = false;
		foreach($SelectedColoumns as $th): 	
	 		if(in_array($th, $pColoums)) {
				$pColExist = true;
				break;			
			}
		endforeach;
		if($pColExist == false) {
		$OrderID = array();
		$Data = array();
		foreach ($reports as $report) :
			if(in_array($report->virtuemart_order_id,$OrderID)) {
				continue;
			} else {
				$Data[] = $report;
				$OrderID[] = $report->virtuemart_order_id;
			}
			endforeach;
		}else{
			$Data = $reports;
		}
		$output = array(
					   "reports" => $Data,
					   "query" => $query,
					   "name" =>$mixData->created_on					
					   );
		return $output;
		
	}
	
	public function getJsonReport($id)
	{
		$db = JFactory::getDBO();
		$FilterBy = JRequest::getVar('filterby', 'order_number');
		$FilterOrder = JRequest::getVar('dir', 'asc');
		$ordering = array();
		$sql = "SELECT * FROM  #__vmreporter_bycustomer WHERE id = ".$id;
		$db->setQuery($sql);
		$reports = $db->loadObjectList();
		$mixData = $reports[0];
		$reports = json_decode($mixData->report_details);
		$query = json_decode($mixData->report_query);
		$output = array(
					   "reports" => $reports,
					   "query" => $query,
					   "name"=>$mixData->created_on
					   );
		return $output;			
	}	
}