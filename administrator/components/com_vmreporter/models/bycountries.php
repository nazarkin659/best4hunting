<?php

/**
 * @version     1.0.0
 * @package     com_vmreporter
 * @copyright   Copyright (C) 2013 VirtuePlanet Services LLP. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      VirtuePlanet Services LLP <info@virtueplanet.com> - http://www.virtueplanet.com
 */
defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');

/**
 * Methods supporting a list of Vmreporter records.
 */
class VmreporterModelbycountries extends JModelList {

    /**
     * Constructor.
     *
     * @param    array    An optional associative array of configuration settings.
     * @see        JController
     * @since    1.6
     */
    public function __construct($config = array()) {
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = array(
                                'id', 'a.id',
                'report_query', 'a.report_query',
                'report_details', 'a.report_details',
                'created_on', 'a.created_on',
                'created_by', 'a.created_by',
                'state', 'a.state',

            );
        }

        parent::__construct($config);
    }

    /**
     * Method to auto-populate the model state.
     *
     * Note. Calling getState in this method will result in recursion.
     */
    protected function populateState($ordering = null, $direction = null) {
        // Initialise variables.
        $app = JFactory::getApplication('administrator');

        // Load the filter state.
        $search = $app->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
        $this->setState('filter.search', $search);

        $published = $app->getUserStateFromRequest($this->context . '.filter.state', 'filter_published', '', 'string');
        $this->setState('filter.state', $published);

        

        // Load the parameters.
        $params = JComponentHelper::getParams('com_vmreporter');
        $this->setState('params', $params);

        // List state information.
        parent::populateState('a.id', 'asc');
    }

    /**
     * Method to get a store id based on model configuration state.
     *
     * This is necessary because the model is used by the component and
     * different modules that might need different sets of data or different
     * ordering requirements.
     *
     * @param	string		$id	A prefix for the store id.
     * @return	string		A store id.
     * @since	1.6
     */
    protected function getStoreId($id = '') {
        // Compile the store id.
        $id.= ':' . $this->getState('filter.search');
        $id.= ':' . $this->getState('filter.state');

        return parent::getStoreId($id);
    }

    /**
     * Build an SQL query to load the list data.
     *
     * @return	JDatabaseQuery
     * @since	1.6
     */
    protected function getListQuery() {
        // Create a new query object.
        $db = $this->getDbo();
        $query = $db->getQuery(true);

        // Select the required fields from the table.
        $query->select(
                $this->getState(
                        'list.select', 'a.*'
                )
        );
        $query->from('`#__vmreporter_bycountry` AS a');

        
		// Join over the user field 'created_by'
		$query->select('created_by.name AS created_by');
		$query->join('LEFT', '#__users AS created_by ON created_by.id = a.created_by');

        
    // Filter by published state
    $published = $this->getState('filter.state');
    if (is_numeric($published)) {
        $query->where('a.state = '.(int) $published);
    } else if ($published === '') {
        $query->where('(a.state IN (0, 1))');
    }
    

        // Filter by search in title
        $search = $this->getState('filter.search');
        if (!empty($search)) {
            if (stripos($search, 'id:') === 0) {
                $query->where('a.id = ' . (int) substr($search, 3));
            } else {
                $search = $db->Quote('%' . $db->escape($search, true) . '%');
                
            }
        }

        


        // Add the list ordering clause.
        $orderCol = $this->state->get('list.ordering');
        $orderDirn = $this->state->get('list.direction');
        if ($orderCol && $orderDirn) {
            $query->order($db->escape($orderCol . ' ' . $orderDirn));
        }

        return $query;
    }

    public function getItems() {
        $items = parent::getItems();
        
        return $items;
    }
	function getCountries()
	{
		$db = JFactory::getDBO();
		$sql = "SELECT virtuemart_country_id,country_name FROM #__virtuemart_countries";
		$db->setQuery($sql);
		$Countries = $db->loadObjectList();
		return $Countries;
	}
	function getOrderStatus()
	{
		$db = JFactory::getDBO();
		$sql = "SELECT order_status_code,order_status_name from #__virtuemart_orderstates";
		$db->setQuery($sql);
		$orders = $db->loadObjectList();
		return $orders;
	}
	function getCondition($post)
	{
		$db = JFactory::getDBO();
		$selectFields = array();
		$countries = array();
		$OrderStatus = array();
		$mainTable = '';
		$joinTables = array();
		$joinedTables = ''; 
		$where = array();
		
		
		$selectFields[] = ' o.*';
    $selectFields[] = " replace(concat_ws(' ', u.title, u.first_name, u.middle_name, u.last_name), '  ', ' ') AS customer_name";
		$selectFields[] = ' s.order_status_name';
		$selectFields[] = ' i.virtuemart_product_id';
		$selectFields[] = ' i.order_item_name';
		$selectFields[] = ' i.product_attribute';
		$selectFields[] = ' i.product_quantity';
		$selectFields[] = ' i.product_item_price';
		$selectFields[] = ' i.product_tax';
		$selectFields[] = ' i.product_basePriceWithTax';
		$selectFields[] = ' i.product_final_price';
		$selectFields[] = ' i.product_subtotal_discount';
		$selectFields[] = ' i.product_subtotal_with_tax';		
		$mainTable = '`#__virtuemart_orders` as o';
    $userInfoTable = ' LEFT JOIN `#__virtuemart_order_userinfos` as u on o.virtuemart_order_id = u.virtuemart_order_id';
		$dates = 'DATE( o.created_on ) BETWEEN "' . $post['start'] . '" AND "' . $post['end'].'"';
		$joinTables['order_items'] = ' LEFT JOIN `#__virtuemart_order_items` as i ON o.virtuemart_order_id = i.virtuemart_order_id';
		$joinTables['countries'] = ' LEFT JOIN `#__virtuemart_order_userinfos` as u ON o.virtuemart_order_id = u.virtuemart_order_id';
		$joinTables['orderstates'] = ' LEFT JOIN `#__virtuemart_orderstates` as s ON i.order_status = s.order_status_code';
		if (count ($selectFields) > 0) {

			$select = implode (', ', $selectFields) . ' FROM ' . $mainTable . $userInfoTable;
			if (count ($joinTables) > 0) {
				foreach ($joinTables as $table) {
					$joinedTables .= $table;
				}
			}
	}
	if(!in_array(0,$post['countries'])){
		foreach($post['countries'] as $country){
			$countries[] = ' `u`.`virtuemart_country_id` = "'. $country .'"';
		}
	}
	if($countries){
		$where[] = '(' . implode (' OR ',$countries) . ')';
	}
	if(!in_array('0',$post['status'])){
			foreach($post['status'] as $statusList){
			$OrderStatus[] = ' `o`.`order_status` = "'. $statusList .'"';
		}
	}
	if($OrderStatus){
		$where[] = '(' . implode (' OR ', $OrderStatus) . ')';
	}
	if (count ($where) > 0) {
			$where = ' WHERE ' . implode (' AND ', $where) . ' AND ';
		}
		else {
			$where = ' WHERE ';
		}
		$whereStatement = $where.$dates.' AND u.address_type = "BT"';
		$reports = $this->generateReports($post,$select,$joinedTables,$whereStatement);
	}
	function generateReports($post,$select,$joinedTables,$whereStatement)
	{		
		if (!class_exists( 'VmConfig' )) 
		   require(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_virtuemart'.DS.'helpers'.DS.'config.php');
		   
		VmConfig::loadConfig();
		VmConfig::loadJLang('com_virtuemart',true);
		VmConfig::loadJLang('com_virtuemart_orders',true); 	
			
		$db = JFactory::getDBO();
		$sql = 'SELECT '.$select.$joinedTables.$whereStatement;
		$db->setQuery($sql);
		$reports = $db->loadObjectList();
		$user = JFactory::getUser();
		$userid = $user->id;
		$sql = "SELECT country_name FROM #__virtuemart_countries \n";
		$sql .= "WHERE virtuemart_country_id IN (".implode(",",$post['countries']).")";
		$db->setQuery($sql);
		$country_name = $db->loadObjectList();
		$sql = "SELECT order_status_name FROM #__virtuemart_orderstates \n";
		$sql .= "WHERE order_status_code IN ('".implode("','",$post['status'])."')";
		$db->setQuery($sql);
		$status_name = $db->loadObjectList();
		$PostDatas = array(
							"country" => $country_name,
							"status" =>	$status_name,
							"from" => $post['start'],
							"to" => $post['end']		
						  );
		$PostDatas = json_encode($PostDatas);
		$json_reports = json_encode($reports);
		if(strlen($json_reports) >  1431655765) {
			JError::raiseWarning(100, 'The size of the report is very big and could not be saved in the database. Please reduce the query period and try again.');
			return FALSE;
		}				
		$InsrtQuery = array(
							 "created_on"=>"created_on = NOW()",
							 "report_query"=>"report_query =".$db->quote($PostDatas),
							 "report_details" => "report_details = ".$db->quote($json_reports),
							 "created_by"=>"created_by = ".$db->quote($userid),
							 "state" => "state = 1"
		                   );
		$InsrtQuery = implode(' , ', $InsrtQuery);
		if(count($reports)):
		$sql = "INSERT into #__vmreporter_bycountry SET ".$InsrtQuery;
		//print($sql);exit;
		$db->setQuery($sql);
		$db->query();
		JFactory::getApplication()->enqueueMessage(JText::_('COM_VMREPORTER_REPORT_GENERATED_AND_SAVED'));
		else:
		JError::raiseNotice('100',JText::_('JLIB_FORM_ERROR_NO_DATA'));
		endif;
	}
	function countReports()
	{
		$db = JFactory::getDBO();
		$sql = "SELECT count(*) from #__vmreporter_bycountry WHERE state = 1";
		$db->setQuery($sql);
		$count = $db->loadResult();
		return $count;
	}
	function getReports($pageNav)
	{
		$db = JFactory::getDBO();
		$sql = "SELECT * from #__vmreporter_bycountry WHERE state = 1 ORDER BY id DESC";
		$db->setQuery($sql,$pageNav->limitstart,$pageNav->limit);
		$reports = $db->loadObjectList();
		return $reports;
	}
	function createCSV($id)
	{
		if (!class_exists ('getNames')) {
			require(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_vmreporter' . DS .'helpers' . DS . 'getname.php');
		}		
		$cache = & JFactory::getCache();
		$cache->setCaching( 1 );
				
		$db = JFactory::getDBO();
		$sql = "SELECT report_details from #__vmreporter_bycountry WHERE id =".$id;
		$db->setQuery($sql);
		$reports = $db->loadObjectList();
		$reports = $reports[0];
		$reports = json_decode($reports->report_details);
		$data = "" ;
			$sep = ",";
			$data .= JText::_('COM_VMREPORT_ORDER_NUMBER').$sep;
			$data .= JText::_('COM_VMREPORT_VIRTUEMART_USER_ID').$sep;
			$data .= JText::_('COM_VMREPORT_VIRTUEMART_VENDOR_ID').$sep;
			$data .= JText::_('COM_VMREPORT_ORDER_ITEM_NAME').$sep;
			$data .= JText::_('COM_VMREPORT_PRODUCT_ATTRIBUTE').$sep;
			$data .= JText::_('COM_VMREPORT_PRODUCT_QUANTITY').$sep;
			$data .= JText::_('COM_VMREPORT_PRODUCT_ITEM_PRICE').$sep;
			$data .= JText::_('COM_VMREPORT_PRODUCT_TAX').$sep;
			$data .= JText::_('COM_VMREPORT_PRODUCT_BASEPRICEWITHTAX').$sep;
			$data .= JText::_('COM_VMREPORT_PRODUCT_FINAL_PRICE').$sep;
			$data .= JText::_('COM_VMREPORT_PRODUCT_SUBTOTAL_DISCOUNT').$sep;
			$data .= JText::_('COM_VMREPORT_PRODUCT_SUBTOTAL_WITH_TAX').$sep;
			$data .= JText::_('COM_VMREPORT_ORDER_TOTAL').$sep;
			$data .= JText::_('COM_VMREPORT_ORDER_SALESPRICE').$sep;
			$data .= JText::_('COM_VMREPORT_ORDER_BILLTAXAMOUNT').$sep;
			$data .= JText::_('COM_VMREPORT_ORDER_BILLDISCOUNTAMOUNT').$sep;
			$data .= JText::_('COM_VMREPORT_ORDER_DISCOUNTAMOUNT').$sep;
			$data .= JText::_('COM_VMREPORT_ORDER_SUBTOTAL').$sep;
			$data .= JText::_('COM_VMREPORT_ORDER_TAX').$sep;
			$data .= JText::_('COM_VMREPORT_ORDER_SHIPMENTMETHOD').$sep;			
			$data .= JText::_('COM_VMREPORT_ORDER_SHIPMENT').$sep;
			$data .= JText::_('COM_VMREPORT_ORDER_SHIPMENT_TAX').$sep;
			$data .= JText::_('COM_VMREPORT_VIRTUEMART_PAYMENTMETHOD_ID').$sep;			
			$data .= JText::_('COM_VMREPORT_ORDER_PAYMENT').$sep;
			$data .= JText::_('COM_VMREPORT_ORDER_PAYMENT_TAX').$sep;
			$data .= JText::_('COM_VMREPORT_COUPON_DISCOUNT').$sep;
			$data .= JText::_('COM_VMREPORT_COUPON_CODE').$sep;
			$data .= JText::_('COM_VMREPORT_ORDER_DISCOUNT').$sep;
			$data .= JText::_('COM_VMREPORT_ORDER_CURRENCY').$sep;
			$data .= JText::_('COM_VMREPORT_ORDER_STATUS').$sep;
			$data .= JText::_('COM_VMREPORT_USER_CURRENCY_ID').$sep;
			$data .= JText::_('COM_VMREPORT_USER_CURRENCY_RATE').$sep;
			//$data .= JText::_('COM_VMREPORT_CUSTOMER_NOTE').$sep;
			$data .= JText::_('COM_VMREPORT_IP_ADDRESS').$sep;
			$data .= JText::_('COM_VMREPORT_ORDER_CREATED_ON').$sep;
			$data .= JText::_('COM_VMREPORT_ORDER_CREATED_BY').$sep;
			$data .= "\n";
			
			foreach($reports as $report){
			$data .= $report->order_number.$sep;
			$data .= $report->customer_name.$sep;
			$data .= $report->virtuemart_vendor_id.$sep;
			$data .= $report->order_item_name.$sep;
			$attrs = json_decode('['.$report->product_attribute.']');
			$dataAttribute = '';
			if(count($attrs)) {
				foreach($attrs[0] as $attr) {
					if(is_object($attr)) {
						foreach($attr as $atr) {
							if(is_object($atr)) {
								foreach($atr as $opt) {
									$p_attributes = str_replace('</p>', '', str_replace('<p>','', str_replace('<span class="costumValue" >',': ', str_replace('<span class="costumTitle">','', str_replace('</span>','', $opt))))).'; ';
									$dataAttribute .= str_replace(',', '.', $p_attributes);
								}
							}
						}
					} else {
						$p_attributes = str_replace('</p>', '', str_replace('<p>','', str_replace('<span class="costumValue" >',': ', str_replace('<span class="costumTitle">','', str_replace('</span>','', $attr))))).'; ';
						$dataAttribute .= str_replace(',', '.', $p_attributes);
					}	
				}
			}
			$data .= $dataAttribute.$sep;
			$data .= $report->product_quantity.$sep;
			$data .= $report->product_item_price.$sep;
			$data .= $report->product_tax.$sep;
			$data .= $report->product_basePriceWithTax.$sep;
			$data .= $report->product_final_price.$sep;
			$data .= $report->product_subtotal_discount.$sep;
			$data .= $report->product_subtotal_with_tax.$sep;
			$data .= $report->order_total.$sep;
			$data .= $report->order_salesPrice.$sep;
			$data .= $report->order_billTaxAmount.$sep;
			$data .= $report->order_billDiscountAmount.$sep;
			$data .= $report->order_discountAmount.$sep;
			$data .= $report->order_subtotal.$sep;
			$data .= $report->order_tax.$sep;
			$data .= $cache->call(array('getNames','shipment'),$report->virtuemart_shipmentmethod_id).$sep;
			$data .= $report->order_shipment.$sep;
			$data .= $report->order_shipment_tax.$sep;
			$data .= $cache->call(array('getNames','payment'),$report->virtuemart_paymentmethod_id).$sep;
			$data .= $report->order_payment.$sep;
			$data .= $report->order_payment_tax.$sep;
			$data .= $report->coupon_discount.$sep;
			$data .= $report->coupon_code.$sep;
			$data .= $report->order_discount.$sep;
			$data .= $cache->call(array('getNames','currency'),$report->order_currency).$sep;
			$data .= JText::_($report->order_status_name).$sep;
			$data .= $cache->call(array('getNames','currency'),$report->user_currency_id).$sep;		
			$data .= $report->user_currency_rate.$sep;
			//$data .= $report->customer_note.$sep;
			$data .= $report->ip_address.$sep;
			$data .= $report->created_on.$sep;
			$data .= $cache->call(array('getNames','user'),$report->created_by).$sep."\n";
			}
			 header("Content-type: application/octet-stream");
		  	 header("Content-Disposition: attachment; filename=download.csv");
		  	 header("Pragma: no-cache");
		  	 header("Expires: 0");
		  	 //header("Lacation: excel.htm?id=yes");
		  	 print $data ;
		     die();

	}

}
