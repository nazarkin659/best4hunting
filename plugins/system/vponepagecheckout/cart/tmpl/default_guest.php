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

$CheckoutGuestFields = array('email');
$url = JRoute::_('index.php?view=cart',$this->useXHTML,$this->useSSL);
?>
<form method="post" id="GuestUser" action="<?php echo $url ?>" autocomplete="off">
	<?php foreach($this->EditBTaddress['fields'] as $BTRegFields) : 
		if(in_array($BTRegFields['name'], $CheckoutGuestFields)) :
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
				<button class="proopc-btn<?php if($this->params->get('color', 1) == 2) echo ' proopc-btn-danger'; else echo ' proopc-btn-inverse'; ?>" type="submit" onclick="return ProOPC.guestcheckout();"><i id="proopc-guest-process" class="proopc-button-process"></i><?php echo JText::_('COM_VIRTUEMART_CHECKOUT_AS_GUEST') ?></button>		
			</div>			
		</div>
		<input type="hidden" name="option" value="com_virtuemart" />
		<input type="hidden" name="ctask" value="savebtaddress" />
		<input type="hidden" name="address_type" value="BT">
		<input type="hidden" name="task" value="saveUser">
		<input type="hidden" name="controller" value="user" />
		<?php echo JHTML::_( 'form.token' ); ?>	
	</div>	
</form>