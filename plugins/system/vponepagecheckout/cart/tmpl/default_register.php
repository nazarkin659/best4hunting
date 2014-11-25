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

$url = JRoute::_('index.php?view=cart',$this->useXHTML,$this->useSSL);
$RegistrationFields = array_merge($this->RegistrationFields, $this->customregfields);
?>
<form method="post" id="UserRegistration" name="userForm" action="<?php echo $url ?>" autocomplete="off">
	<?php foreach($this->EditBTaddress['fields'] as $BTRegFields) : 
		if(in_array($BTRegFields['name'], $RegistrationFields)) :
			echo '<div class="proopc-group">';
			echo '<div class="proopc-input-group-level">';
		    echo '<label class="' . $BTRegFields['name'] . ' full-input" for="' . $BTRegFields['name'] . '_field">';
		    echo JText::_($BTRegFields['title']);			
		    echo '</label>';
			echo '</div>';
			echo '<div class="proopc-input proopc-input-append">';
			echo $BTRegFields['formcode'];
			echo '<i class="status hover-tootip"></i>';
			if($BTRegFields['name'] == 'password') {
				echo '<div class="password-stregth">'.JText::_('PLG_VPONEPAGECHECKOUT_PASSWORD_STRENGTH').'<span id="password-stregth"></span></div>';
				echo '<div class="strength-meter"><div id="meter-status"></div></div>';
			}
			echo '</div>';
			echo '</div>';			
		endif; 
	endforeach ?>	
	<div class="proops-login-inputs">	
		<div class="proopc-group">
			<div class="proopc-input proopc-input-prepend">		
				<button class="proopc-btn<?php if($this->params->get('color', 1) == 2) echo ' proopc-btn-danger'; else echo ' proopc-btn-inverse'; ?>" type="submit" onclick="return ProOPC.registerCheckout();"><i id="proopc-register-process" class="proopc-button-process"></i><?php echo JText::_('COM_VIRTUEMART_REGISTER_AND_CHECKOUT') ?></button>		
			</div>			
		</div>
		<?php echo JHTML::_( 'form.token' ); ?>	
	</div>	
</form>