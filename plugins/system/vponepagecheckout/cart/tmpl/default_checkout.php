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

$checkout_step = 1;
$totalProduct = 0;
if(!empty($this->cart->products)) 
{
	foreach($this->cart->products as $product) 
  {
		$totalProduct = ($totalProduct + $product->quantity);
	}
}
if($this->params->get('style', 1) == 1) { ?>
<div id="proopc-system-message"></div>
<div class="proopc-finalpage<?php echo $this->params->get('reload', 0) ? ' proopc-reload' : ''; ?>">
	<div class="proopc-row">
		<h1 class="cart-page-title"><?php echo JText::_ ('COM_VIRTUEMART_CART_TITLE'); ?>&nbsp;<span class="septa">/</span>&nbsp;<span><?php echo JText::sprintf('COM_VIRTUEMART_CART_X_PRODUCTS', '<span id="proopc-cart-totalqty">'.$totalProduct.'</span>'); ?></span></h1> 
	</div>
	<div class="proopc-row">
		<div class="proopc-login-message-cont">
			<?php echo $this->loadTemplate ('logout'); ?>
		</div>	
		<?php if ($this->continue_link_html != '') { ?>
		<div class="proopc-continue-link">					
			<?php echo $this->continue_link_html ?>							
		</div>
		<?php } ?>
	</div>
	<div class="proopc-column3">
		<div class="proopc-bt-address">
			<h3 class="proopc-process-title"><?php echo $this->params->get('oncheckout_show_steps', 1) ? '<div class="proopc-step">'.($checkout_step++).'</div>' : '' ?><?php echo JText::_('COM_VIRTUEMART_USER_FORM_BILLTO_LBL') ?></h3>
			<?php echo $this->loadTemplate ('btaddress'); ?>
		</div>		
	</div>
	<div class="proopc-column3">
		<div class="proopc-st-address">
			<h3 class="proopc-process-title"><?php echo $this->params->get('oncheckout_show_steps', 1) ? '<div class="proopc-step">'.($checkout_step++).'</div>' : '' ?><?php echo JText::_('COM_VIRTUEMART_USER_FORM_SHIPTO_LBL') ?></h3>
			<?php echo $this->loadTemplate ('staddress'); ?>
		</div>	
		<div class="proopc-shipments">
			<h3 class="proopc-process-title"><?php echo $this->params->get('oncheckout_show_steps', 1) ? '<div class="proopc-step">'.($checkout_step++).'</div>' : '' ?><?php echo JText::_('COM_VIRTUEMART_CART_SHIPPING')?></h3>
			<div id="proopc-shipments">
				<?php echo $this->loadTemplate ('shipment'); ?>
			</div>
		</div>
		<div class="proopc-payments">
			<h3 class="proopc-process-title"><?php echo $this->params->get('oncheckout_show_steps', 1) ? '<div class="proopc-step">'.($checkout_step++).'</div>' : '' ?><?php echo JText::_('COM_VIRTUEMART_CART_PAYMENT')?></h3>
			<div id="proopc-payments">
				<?php echo $this->loadTemplate ('payment'); ?>
			</div>
		</div>	
		<?php if (VmConfig::get('coupons_enable')) { ?>
		<div class="proopc-coupon">
			<h3 class="proopc-process-title"><?php echo $this->params->get('oncheckout_show_steps', 1) ? '<div class="proopc-step">'.($checkout_step++).'</div>' : '' ?><?php echo JText::_('COM_VIRTUEMART_COUPON_DISCOUNT')?></h3>
			<div id="proopc-coupon">
				<?php if(!empty($this->layoutName) && $this->layoutName=='default') {
				    echo $this->loadTemplate('coupon');
			    } ?>
			</div>
		</div>
		<?php } ?>		
	</div>	
	<div class="proopc-column3 last">
		<div class="proopc-cartlist">
			<h3 class="proopc-process-title"><?php echo $this->params->get('oncheckout_show_steps', 1) ? '<div class="proopc-step">'.($checkout_step++).'</div>' : '' ?><?php echo JText::_('COM_VIRTUEMART_CART_OVERVIEW')?></h3>
			<div id="proopc-pricelist">
				<?php echo $this->loadTemplate ('cartlist'); ?>
			</div>
		</div>	
		<div class="proopc-confirm-order">
			<h3 class="proopc-process-title"><?php echo $this->params->get('oncheckout_show_steps', 1) ? '<div class="proopc-step">'.($checkout_step++).'</div>' : '' ?><?php echo JText::_('COM_VIRTUEMART_ORDER_CONFIRM_MNU')?></h3>
			<div id="proopc-confirm-order">
				<?php echo $this->loadTemplate ('confirm'); ?>
			</div>
			<?php echo $this->loadTemplate ('advertisement'); ?>
		</div>			
	</div>
</div>
<?php }
else if($this->params->get('style', 1) == 2) { ?>
<div id="proopc-system-message"></div>
<div class="proopc-finalpage">
	<div class="proopc-row">
		<h1 class="cart-page-title"><?php echo JText::_ ('COM_VIRTUEMART_CART_TITLE'); ?>&nbsp;<span class="septa">/</span>&nbsp;<span><?php echo JText::sprintf('COM_VIRTUEMART_CART_X_PRODUCTS', '<span id="proopc-cart-totalqty">'.$totalProduct.'</span>'); ?></span></h1> 
	</div>
	<div class="proopc-row">
		<div class="proopc-login-message-cont">
			<?php echo $this->loadTemplate ('logout'); ?>
		</div>	
		<?php if ($this->continue_link_html != '') { ?>
		<div class="proopc-continue-link">					
			<?php echo $this->continue_link_html ?>							
		</div>
		<?php } ?>
	</div>	
	<div id="proopc-pricelist">
		<?php echo $this->loadTemplate ('cartlist'); ?>
	</div>
	<div class="proopc-column3">
		<div class="proopc-bt-address">
			<h3 class="proopc-process-title"><?php echo $this->params->get('oncheckout_show_steps', 1) ? '<div class="proopc-step">'.($checkout_step++).'</div>' : '' ?><?php echo JText::_('COM_VIRTUEMART_USER_FORM_BILLTO_LBL') ?></h3>
			<?php echo $this->loadTemplate ('btaddress'); ?>
		</div>		
	</div>
	<div class="proopc-column3">
		<div class="proopc-st-address">
			<h3 class="proopc-process-title"><?php echo $this->params->get('oncheckout_show_steps', 1) ? '<div class="proopc-step">'.($checkout_step++).'</div>' : '' ?><?php echo JText::_('COM_VIRTUEMART_USER_FORM_SHIPTO_LBL') ?></h3>
			<?php echo $this->loadTemplate ('staddress'); ?>
		</div>	
		<div class="proopc-shipments">
			<h3 class="proopc-process-title"><?php echo $this->params->get('oncheckout_show_steps', 1) ? '<div class="proopc-step">'.($checkout_step++).'</div>' : '' ?><?php echo JText::_('COM_VIRTUEMART_CART_SHIPPING')?></h3>
			<div id="proopc-shipments">
				<?php echo $this->loadTemplate ('shipment'); ?>
			</div>
		</div>
		<div class="proopc-payments">
			<h3 class="proopc-process-title"><?php echo $this->params->get('oncheckout_show_steps', 1) ? '<div class="proopc-step">'.($checkout_step++).'</div>' : '' ?><?php echo JText::_('COM_VIRTUEMART_CART_PAYMENT')?></h3>
			<div id="proopc-payments">
				<?php echo $this->loadTemplate ('payment'); ?>
			</div>
		</div>		
	</div>	
	<div class="proopc-column3 last">
		<?php if (VmConfig::get('coupons_enable')) { ?>
		<div class="proopc-coupon no-top-margin">
			<h3 class="proopc-process-title"><?php echo $this->params->get('oncheckout_show_steps', 1) ? '<div class="proopc-step">'.($checkout_step++).'</div>' : '' ?><?php echo JText::_('COM_VIRTUEMART_COUPON_DISCOUNT')?></h3>
			<div id="proopc-coupon">
				<?php if(!empty($this->layoutName) && $this->layoutName=='default') {
				    echo $this->loadTemplate('coupon');
			    } ?>
			</div>
		</div>
		<?php 
		$confirm_class = '';
		} else { 
		$confirm_class = 'no-top-margin';
		}?>		
		<div class="proopc-confirm-order <?php echo $confirm_class ?>">
			<h3 class="proopc-process-title"><?php echo $this->params->get('oncheckout_show_steps', 1) ? '<div class="proopc-step">'.($checkout_step++).'</div>' : '' ?><?php echo JText::_('COM_VIRTUEMART_ORDER_CONFIRM_MNU')?></h3>
			<div id="proopc-confirm-order">
				<?php echo $this->loadTemplate ('confirm'); ?>
			</div>
			<?php echo $this->loadTemplate ('advertisement'); ?>
		</div>			
	</div>
</div>
<?php } ?>