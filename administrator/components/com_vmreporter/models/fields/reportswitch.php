<?php
/**
 * @version     1.0.0
 * @package     com_vmreporter
 * @copyright   Copyright (C) 2013 VirtuePlanet Services LLP. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      VirtuePlanet Services LLP <info@virtueplanet.com> - http://www.virtueplanet.com
 */

defined('JPATH_BASE') or die;

jimport('joomla.form.formfield');

/**
 * Supports an HTML select list of categories
 */
class JFormFieldReportswitch extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since	1.6
	 */
	protected $type = 'reportswitch';

	/**
	 * Method to get the field input markup.
	 *
	 * @return	string	The field input markup.
	 * @since	1.6
	 */
	protected function getInput() {
		
		$ReportID = JRequest::getInt('id',0);
		$ReportView = JRequest::getVar('view', 'byproduct');
		$ReportLabel = JText::_('COM_VMREPORT_ORDER_TOTAL');
		
		$html  = '<ul>';
		$html .= '<li class="show-table active">';
		$html .= '<a href="#">'.JText::_('COM_VMREPORT_SHOW_TABLE').'</a>';
		$html .= '</li>';		
		$html .= '<li class="show-chart">';
		$html .= '<a href="#" onclick="orderTotal(\''.$ReportID.'\', \''.$ReportView.'\', \''.$ReportLabel.'\');">'.JText::_('COM_VMREPORT_SHOW_CHART').'</a>';
		$html .= '</li>';
		$html .= '</ul>';

		return $html;
    }
}