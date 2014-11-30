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
defined ('_JEXEC') or die('Restricted access');
JHTML::_('behavior.tooltip');
vmJsApi::jPrice();
$this->loadPlgScripts();
$user = JFactory::getUser();

$BT_STATE_ID = !empty($this->cart->BT['virtuemart_state_id']) ? $this->cart->BT['virtuemart_state_id'] : 0;
$ST_STATE_ID = !empty($this->cart->ST['virtuemart_state_id']) ? $this->cart->ST['virtuemart_state_id'] : 0;

$totalProduct = 0;
if(!empty($this->cart->products)) 
{
	foreach($this->cart->products as $product) 
  {
		$totalProduct = ($totalProduct + $product->quantity);
	}
}

if($totalProduct == 0) { ?>
	<div id="ProOPC" class="cart-view proopc-row<?php if($this->params->get('color', 1) == 2) echo ' dark'; ?>">
		<?php echo $this->loadTemplate ('module'); ?>
		<h1 class="cart-page-title"><?php echo JText::_('COM_VIRTUEMART_EMPTY_CART') ?></h1>
			<?php if(!empty($this->continue_link_html)) : ?>
			<div class="proopc-empty-continue-link">
				<span class="proopc-btn <?php if($this->params->get('color', 1) == 2) echo ' proopc-btn-danger'; ?>"><?php echo $this->continue_link_html ?></span>
			</div>
			<?php endif; ?>		
	</div>	
<?php }
elseif(JRequest::getCmd('task') == 'procheckout' or ($user->id > 0 and $totalProduct > 0) or $this->params->get('only_guest', 0)) { ?>
	<div id="ProOPC" class="cart-view proopc-row<?php echo (($this->params->get('color', 1) == 2) ? ' dark' : '') ?>">
	  <?php echo $this->loadTemplate('module') ?>
	  <?php echo $this->loadTemplate('checkout') ?>
	  <div id="formToken"><?php echo JHTML::_( 'form.token' ) ?></div>
	  <input type="hidden" id="BTStateID" name="BTStateID" value="<?php echo $BT_STATE_ID ?>" />
	  <input type="hidden" id="STStateID" name="STStateID" value="<?php echo $ST_STATE_ID ?>" />
	</div>
<?php } 
else { ?>	
	<div id="ProOPC" class="cart-view proopc-row<?php echo (($this->params->get('color', 1) == 2) ? ' dark' : '') ?>">	
		<?php echo $this->loadTemplate ('module'); ?>	
		<div class="proopc-row">
			<h1 class="cart-page-title"><?php echo JText::_ ('COM_VIRTUEMART_CART_TITLE'); ?>&nbsp;<span class="septa">/</span>&nbsp;<span><?php echo JText::sprintf('COM_VIRTUEMART_CART_X_PRODUCTS', '<span id="proopc-cart-totalqty">'.$totalProduct.'</span>'); ?></span></h1> 
		</div>		
		<div class="proopc-row">
			<?php if ($this->continue_link_html != '') { ?>
			<div class="proopc-continue-link">					
				<?php echo $this->continue_link_html ?>							
			</div>
			<?php } ?>
		</div>		
		<input type="hidden" id="proopc-cart-summery" name="proopc-cart-summery" value="1" />
		<div id="proopc-pricelist" class="first-page">			
			<?php echo $this->loadTemplate ('pricelist'); ?>
		</div>		
		<div id="proopc-system-message"></div>
		<?php if($this->cart->cartData['totalProduct'] > 0) {
			if($user->id == 0) { ?>
			<div class="proopc-register-login">
				<div class="proopc-register">
					<?php if(VmConfig::get('oncheckout_show_register') == 0 and VmConfig::get('oncheckout_only_registered') == 0) { ?>	
					<h3><?php echo JText::_('COM_VIRTUEMART_CHECKOUT_AS_GUEST')?></h3>
					<?php } else if(VmConfig::get('oncheckout_only_registered')) { ?>
					<h3><?php echo JText::_('COM_VIRTUEMART_CART_ONLY_REGISTERED')?></h3>
					<?php } else { ?>
					<h3><?php echo JText::_('PLG_VPONEPAGECHECKOUT_CHECKOUT_AS_GUEST_REGISTER')?></h3>	
					<?php } ?>
					<div class="proopc-inner">						
						<?php if(VmConfig::get('oncheckout_show_register') == 0 and VmConfig::get('oncheckout_only_registered') == 0) { ?>	
						<h4 class="proopc-subtitle"><?php echo JText::_('COM_VIRTUEMART_ENTER_A_VALID_EMAIL_ADDRESS')?></h4>						
						<div class="proopc-guest-form">
							<div class="proopc-inner">
								<?php echo $this->loadTemplate ('guest'); ?>
							</div>
						</div>							
						<?php } else if(VmConfig::get('oncheckout_only_registered')) { ?>
						<h4 class="proopc-subtitle"><?php echo JText::_('PLG_VPONEPAGECHECKOUT_REGISTER_CONVINIENCE')?></h4>
						<div class="proopc-reg-form show">
							<div class="proopc-inner">
								<?php echo $this->loadTemplate ('register'); ?>
							</div>
						</div>
						<?php } else { ?>			
						<h4 class="proopc-subtitle"><?php echo JText::_('PLG_VPONEPAGECHECKOUT_REGISTER_CONVINIENCE')?></h4>			
						<label class="proopc-switch">
							<input type="radio" name="proopc-method" value="guest" onchange="ProOPC.opcmethod();" <?php echo $this->params->get('registration_by_default', 0) ? '' : 'checked'; ?> autocomplete="off"/> 
							<?php echo JText::_('COM_VIRTUEMART_CHECKOUT_AS_GUEST') ?>
						</label>						
						<div class="proopc-guest-form <?php echo $this->params->get('registration_by_default', 0) ? 'hide' : ''; ?>">
							<div class="proopc-inner with-switch">
								<?php echo $this->loadTemplate ('guest'); ?>
							</div>
						</div>						
						<label class="proopc-switch">
							<input type="radio" name="proopc-method" value="register" onchange="ProOPC.opcmethod();" <?php echo $this->params->get('registration_by_default', 0) ? 'checked' : ''; ?> autocomplete="off"/>
							<?php echo JText::_('COM_VIRTUEMART_REGISTER') ?>
						</label>
						<div class="proopc-reg-form <?php echo $this->params->get('registration_by_default', 0) ? '' : 'hide'; ?>">
							<div class="proopc-inner with-switch">
								<?php echo $this->loadTemplate ('register'); ?>
							</div>
						</div>				
						<div class="proopc-reg-advantages <?php echo $this->params->get('registration_by_default', 0) ? 'hide' : ''; ?>">
							<?php if(trim($this->params->get('registration_message', '')) == '') { 
								echo JText::_('PLG_VPONEPAGECHECKOUT_DEFAULT_REGISTRATION_ADVANTAGE_MSG');
							} else {
								echo trim($this->params->get('registration_message', ''));
							} ?>
						</div>	
						<?php } ?>				
					</div>		
				</div>
				<div class="proopc-login">
					<h3><?php echo JText::_('PLG_VPONEPAGECHECKOUT_LOGIN_AND_CHECKOUT') ?></h3>
					<div class="proopc-inner">						
						<?php echo $this->loadTemplate ('login'); ?>
					</div>	
				</div>	
			</div>
			<?php } 		
		} ?>
	</div>	
<?php } ?>