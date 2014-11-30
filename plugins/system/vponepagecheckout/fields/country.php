<?php
/*------------------------------------------------------------------------------------------------------------
# VP One Page Checkout! Joomla 2.5 Plugin for VirtueMart 2.0
# ------------------------------------------------------------------------------------------------------------
# Copyright (C) 2012 - 2014 VirtuePlanet Services LLP. All Rights Reserved.
# License - GNU General Public License version 2. http://www.gnu.org/licenses/gpl-2.0.html
# Author: VirtuePlanet Services LLP
# Email: info@virtueplanet.com
# Websites:  http://www.virtueplanet.com
------------------------------------------------------------------------------------------------------------*/
defined('JPATH_PLATFORM') or die;

class JFormFieldCountry extends JFormField
{
	protected $type = 'Country';

	protected function getInput()
	{
		// Initialize variables.
		$html = array();
		$attr = '';

		$attr .= $this->element['class'] ? ' class="' . (string) $this->element['class'] . '"' : '';

		// To avoid user's confusion, readonly="true" should imply disabled="true".
		if ((string) $this->element['readonly'] == 'true' || (string) $this->element['disabled'] == 'true')
		{
			$attr .= ' disabled="disabled"';
		}

		$attr .= $this->element['size'] ? ' size="' . (int) $this->element['size'] . '"' : '';
		$attr .= $this->multiple ? ' multiple="multiple"' : '';

		// Initialize JavaScript field attributes.
		$attr .= $this->element['onchange'] ? ' onchange="' . (string) $this->element['onchange'] . '"' : '';

		// Get the field options.
		$options = (array) $this->getOptions();

		// Create a read-only list (no name) with a hidden input to store the value.
		if ((string) $this->element['readonly'] == 'true')
		{
			$html[] = JHtml::_('select.genericlist', $options, '', trim($attr), 'value', 'text', (empty($this->value) ? $this->getDefault() : $this->value), $this->id);
			$html[] = '<input type="hidden" name="' . $this->name . '" value="' . (empty($this->value) ? $this->getDefault() : $this->value) . '"/>';
		}
		// Create a regular list.
		else
		{
			$html[] = JHtml::_('select.genericlist', $options, $this->name, trim($attr), 'value', 'text', (empty($this->value) ? $this->getDefault() : $this->value), $this->id);
		}		
		
		return implode($html);
	}

	protected function getOptions() {
		$options = array();		
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select(array('a.virtuemart_country_id', 'a.country_name'));
		$query->from('`#__virtuemart_countries` AS a');
		$db->setQuery($query);
		$contries = $db->loadObjectList();	

		foreach ($contries as $country)
		{			
			$tmp = JHtml::_('select.option', $country->virtuemart_country_id,	$country->country_name);
			$options[] = $tmp;
		}
		reset($options);
		return $options;
	}
	
	protected function getDefault() {	
	
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select(array('b.virtuemart_country_id', 'b.virtuemart_state_id'));
		$query->from('`#__virtuemart_vmusers` AS a');
		$query->leftJoin('`#__virtuemart_userinfos` AS b ON a.virtuemart_user_id = b.virtuemart_user_id');
		$query->where('a.virtuemart_vendor_id	= 1');
		$query->where('b.address_type	= '.$db->quote('BT'));
		$db->setQuery($query);
		$vendor = $db->loadObject();
		
		if(!empty($vendor)) {
			$default = $vendor->virtuemart_country_id;
		} else {
			$default = 1;
		}			
		
		return $default;
	}	
	
}
