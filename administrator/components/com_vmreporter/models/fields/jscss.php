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
class JFormFieldJscss extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since	1.6
	 */
	protected $type = 'jscss';

	/**
	 * Method to get the field input markup.
	 *
	 * @return	string	The field input markup.
	 * @since	1.6
	 */
	protected function getInput() {
		
		$document = JFactory::getDocument();
		$document->addCustomTag('
		<!--[if lte IE 8]><script language="javascript" type="text/javascript" src="'.JURI::base(true).'/components/com_vmreporter/assets/js/excanvas.min.js"></script><![endif]-->');
		$document->addStyleSheet(JURI::base(true).'/components/com_vmreporter/assets/css/chosen.css');
		$document->addStyleSheet(JURI::base(true).'/components/com_vmreporter/assets/css/vmreporter.css');
		$document->addScript(JURI::base(true).'/components/com_vmreporter/assets/js/jquery.min.js');
		$document->addScript(JURI::base(true).'/components/com_vmreporter/assets/js/chosen.jquery.min.js');
		$document->addScript(JURI::base(true).'/components/com_vmreporter/assets/js/strtotime.js');
		$document->addScript(JURI::base(true).'/components/com_vmreporter/assets/js/jquery.flot.js');
		$document->addScript(JURI::base(true).'/components/com_vmreporter/assets/js/jquery.flot.time.js');
		$document->addScript(JURI::base(true).'/components/com_vmreporter/assets/js/jquery.flot.pie.js');
		$document->addScript(JURI::base(true).'/components/com_vmreporter/assets/js/jquery.flot.tooltip.js');
		$document->addScript(JURI::base(true).'/components/com_vmreporter/assets/js/component.js');		

    }
}