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
$language = JFactory::getLanguage();
$language_tag = $language->getTag(); // loads the current language-tag
JFactory::getLanguage()->load('com_users',JPATH_SITE,$language_tag,true);
JFactory::getLanguage()->load('plg_vponepagecheckout',JPATH_ADMINISTRATOR,$language_tag,true);
JFactory::getLanguage()->load('com_virtuemart_shoppers',JPATH_SITE,$language_tag,true);
$user = JFactory::getUser();
if($user->id <= 0) {
	unset($this->EditSTaddress['fields']['address_type_name']);
}
?>
<div id="proopc-st-address">
	<div class="inner-wrap">
		<label for="BTasST">
			<input class="inputbox" type="checkbox" name="STsameAsBT" id="STsameAsBT" <?php echo $this->cart->STsameAsBT == 1 ? 'checked="checked"' : '' ; ?> onclick="return ProOPC.setst(this);"/>
			<?php echo JText::_('COM_VIRTUEMART_USER_FORM_ST_SAME_AS_BT') ?>
		</label>
		<div class="edit-address<?php echo $this->cart->STsameAsBT==1? '' : ' show'; ?>">
			<form id="EditSTAddres" autocomplete="off">
				<?php
				if($this->selectSTName and $user->id) {
					echo '<div class="proopc-select-st-group"><div class="inner">';
					echo '<label class="">'.JText::_('PLG_VPONEPAGECHECKOUT_SELECT_ADDRESS').'</label>';
					echo $this->selectSTName;
					echo '</div></div>';	
				}			
				foreach($this->EditSTaddress['fields'] as $STAddFields) : 
					echo '<div class="'.$STAddFields['name'].'-group"><div class="inner">';
						if(strpos($STAddFields['formcode'],'<select') === false) {
							echo '<label class="' . $STAddFields['name'] . '" for="' . $STAddFields['name'] . '_field">';
						} else {
							echo '<label class="' . $STAddFields['name'] . '" for="' . $STAddFields['name'] . '">';
						}	
				    echo JText::_($STAddFields['title']);	
						echo (strpos($STAddFields['formcode'],'required') || $STAddFields['required']) ? ' *' : '';			
				    echo '</label>';
					if($STAddFields['name'] == 'shipto_zip' || $STAddFields['name'] == 'shipto_city') {
						echo str_replace('input', 'input onchange="return ProOPC.updateSTaddress(this);"', $STAddFields['formcode']);
					} else if($STAddFields['name'] == 'shipto_virtuemart_country_id') {
						echo str_replace('<select', '<select onchange="return ProOPC.updateSTaddress(this);"', $STAddFields['formcode']);
					} else if ($STAddFields['name'] == 'shipto_virtuemart_state_id') {
						echo str_replace('<select', '<select onchange="return ProOPC.updateSTaddress(this);"', $STAddFields['formcode']);
					} else if((strpos($STAddFields['formcode'],'required') === false && $STAddFields['required']) && strpos($STAddFields['formcode'], 'class="vm-chzn-select"')) {
						echo str_replace('class="vm-chzn-select"', 'class="required vm-chzn-select"', $STAddFields['formcode']);
					} else if((strpos($STAddFields['formcode'],'required') === false && $STAddFields['required']) && strpos($STAddFields['formcode'], '<select')) {
						echo str_replace('<select', 'class="required"', $STAddFields['formcode']);									
					} else {
						echo $STAddFields['formcode'];
					}					
					echo '</div></div>';					
				endforeach; ?>
				<input type="hidden" name="shipto_virtuemart_userinfo_id" value="<?php echo isset($this->cart->ST['virtuemart_userinfo_id']) ? $this->cart->ST['virtuemart_userinfo_id'] : 0 ?>" id="shipto_virtuemart_userinfo_id" />
			</form>	
		</div>
	</div>
</div>