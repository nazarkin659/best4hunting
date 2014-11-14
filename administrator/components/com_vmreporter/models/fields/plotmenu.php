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
class JFormFieldPlotmenu extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since	1.6
	 */
	protected $type = 'plotmenu';

	/**
	 * Method to get the field input markup.
	 *
	 * @return	string	The field input markup.
	 * @since	1.6
	 */
	protected function getInput() {
		
		$ReportID = JRequest::getInt('id',0);
		$ReportView = JRequest::getVar('view', 'byproduct');
		
		$html  = '<ul>';
		$html .= '<li class="name">';
		$html .= '<span>'.JText::_('COM_VMREPORT_PLOT_MENU_NAME').'</span>';
		$html .= '</li>';
		$html .= '<li class="item default-active">';
		$html .= '<a href="Javascript:void(0);" onclick="orderTotal(\''.$ReportID.'\', \''.$ReportView.'\', \''.JText::_('COM_VMREPORT_ORDER_TOTAL').'\');">'.JText::_('COM_VMREPORT_ORDER_TOTAL').'</a>';
		$html .= '</li>';		
		$html .= '<li class="item">';
		$html .= '<a href="Javascript:void(0);" onclick="ProductsPerformance(\''.$ReportID.'\', \''.$ReportView.'\', \''.JText::_('COM_VMREPORT_PRODUCT_PERFORMANCE').'\');">'.JText::_('COM_VMREPORT_PRODUCT_PERFORMANCE').'</a>';
		$html .= '</li>';
		$html .= '</ul>';

		return $html;
    }
}