<?php
/*------------------------------------------------------------------------------------------------------------
# VP One Page Checkout! Joomla 2.5 Plugin for VirtueMart 2.0 / VirtueMart 2.6
# ------------------------------------------------------------------------------------------------------------
# Copyright (C) 2012 - 2014 VirtuePlanet Services LLP. All Rights Reserved.
# License - GNU General Public License version 2. http://www.gnu.org/licenses/gpl-2.0.html
# Author: VirtuePlanet Services LLP
# Email: info@virtueplanet.com
# Websites:  http://www.virtueplanet.com
------------------------------------------------------------------------------------------------------------*/
defined('_JEXEC') or die('Restricted access');

$user = JFactory::getUser();
$RegistrationFields = $this->RegistrationFields;
foreach($RegistrationFields as $field_name) {
	if($field_name == 'email') {
		continue;
	}
	unset($this->EditBTaddress['fields'][$field_name]);
}
$url = JRoute::_('index.php?view=cart',$this->useXHTML,$this->useSSL);
?>
<div class="inner-wrap">
	<div class="edit-address">
		<form id="EditBTAddres" autocomplete="off">
			<?php
			foreach($this->EditBTaddress['fields'] as $BTAddFields) : 
				echo '<div class="'.$BTAddFields['name'].'-group"><div class="inner">';
					if(strpos($BTAddFields['formcode'],'<select') === false) {
						echo '<label class="' . $BTAddFields['name'] . '" for="' . $BTAddFields['name'] . '_field">';
					} else {
						echo '<label class="' . $BTAddFields['name'] . '" for="' . $BTAddFields['name'] . '">';
					}			    
			    echo JText::_($BTAddFields['title']);
					echo (strpos($BTAddFields['formcode'],'required') || $BTAddFields['required'])  ? ' *' : '';			
			    echo '</label>';
				if($BTAddFields['name'] == 'zip' || $BTAddFields['name'] == 'city') {
					echo str_replace('<input', '<input onchange="return ProOPC.updateBTaddress(this);"', $BTAddFields['formcode']);
				} else if($BTAddFields['name'] == 'virtuemart_country_id' || $BTAddFields['name'] == 'virtuemart_state_id') {
					echo str_replace('<select', '<select onchange="return ProOPC.updateBTaddress(this);"', $BTAddFields['formcode']);
				} else if((strpos($BTAddFields['formcode'],'required') === false && $BTAddFields['required']) && strpos($BTAddFields['formcode'], 'class="vm-chzn-select"')) {
					echo str_replace('class="vm-chzn-select"', 'class="required vm-chzn-select"', $BTAddFields['formcode']);
				} else if((strpos($BTAddFields['formcode'],'required') === false && $BTAddFields['required']) && strpos($BTAddFields['formcode'], '<select')) {
					echo str_replace('<select', 'class="required"', $BTAddFields['formcode']);									
				} else {
					echo $BTAddFields['formcode'];
				}				
				echo '</div></div>';					
			endforeach; ?>			
		</form>	
	</div>
	<input type="hidden" name="billto" value="<?php echo $this->cart->lists['billTo']; ?>"/>
</div>