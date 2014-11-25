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

$document = JFactory::getDocument ();
if ($this->checkout_task) {
	$taskRoute = '&task=' . $this->checkout_task;
}
else {
	$taskRoute = '';
} ?>
<div class="inner-wrap">
	<form method="post" id="checkoutForm" name="checkoutForm" action="<?php echo JRoute::_ ('index.php?option=com_virtuemart&view=cart' . $taskRoute, $this->useXHTML, $this->useSSL); ?>">	
		<div class="customer-comment">
			<span class="comment"><?php echo JText::_ ('COM_VIRTUEMART_COMMENT_CART'); ?></span><br/>
			<textarea class="customer-comment proopc-customer-comment" name="customer_comment" cols="60" rows="3"><?php echo $this->cart->customer_comment; ?></textarea>
		</div>
		<div class="checkout-button-top">
			<?php if (!class_exists ('VirtueMartModelUserfields')) {
				require(JPATH_VM_ADMINISTRATOR . DS . 'models' . DS . 'userfields.php');
			}
			$userFieldsModel = VmModel::getModel ('userfields');
			if ($userFieldsModel->getIfRequired ('agreed') && VmConfig::get ('oncheckout_show_legal_info', 1)) {	?>
			<label for="tosAccepted" class="checkbox prooopc-tos-label proopc-row">
				<?php if (!class_exists ('VmHtml')) {
						require(JPATH_VM_ADMINISTRATOR . DS . 'helpers' . DS . 'html.php');
					}
					echo VmHtml::checkbox ('tosAccepted', $this->cart->tosAccepted, 1, 0, 'class="terms-of-service"');
					if (VmConfig::get ('oncheckout_show_legal_info', 1)) { 
						if($this->params->get('tos_fancybox', 0)) { ?>
							<div class="terms-of-service-cont">
								<a href="#proopc-tos-fancy" class="terms-of-service" data-tos="fancybox">
									<?php echo JText::_ ('COM_VIRTUEMART_CART_TOS_READ_AND_ACCEPTED'); ?>										
								</a>
							</div>
							<div class="hide">
								<div id="proopc-tos-fancy" class="fancy-tos-container">
									<div class="fancy-tos-head">
										<button type="button" class="fancy-close">×</button>
										<h3 class="fancy-tos-title"><?php echo JText::_ ('COM_VIRTUEMART_CART_TOS'); ?></h3>										
									</div>
									<div class="fancy-tos-body">
										<p><?php echo $this->cart->vendor->vendor_terms_of_service; ?></p>
									</div>
								</div>
							</div>						
						<?php } else { ?>
							<div class="terms-of-service-cont">
								<a href="#proopc-tos-fancy" class="terms-of-service" data-toggle="modal"><?php echo JText::_ ('COM_VIRTUEMART_CART_TOS_READ_AND_ACCEPTED'); ?></a>
							</div>
							<div class="boot-modal fade" id="proopc-tos-fancy" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
								<div class="modal-header">
									<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
									<h3 id="myModalLabel"><?php echo JText::_ ('COM_VIRTUEMART_CART_TOS'); ?></h3>
								</div>
								<div class="modal-body">
									<p><?php echo $this->cart->vendor->vendor_terms_of_service; ?></p>
								</div>
							</div>
						<?php } ?>					
				<?php } ?>
			</label>
			<?php } else if ($userFieldsModel->getIfRequired ('agreed') && VmConfig::get ('oncheckout_show_legal_info', 1) == 0) { ?>
				<input type="hidden" name="tosAccepted" id="tosAccepted" value="1" />				
			<?php } 
			
			if (!VmConfig::get('use_as_catalog')) { ?>
			<div class="proopc-row proopc-checkout-box">			
				<button type="submit" class="proopc-btn n<?php if($this->params->get('color', 1) == 2) echo ' proopc-btn-danger'; else echo ' proopc-btn-info'; ?>" id="proopc-order-submit" onclick="return ProOPC.submitOrder();"><?php echo JText::_('COM_VIRTUEMART_ORDER_CONFIRM_MNU') ?></button>							
			</div>	
			<?php } ?>			
		</div>
		<input type="hidden" name="order_language" value="<?php echo $this->order_language; ?>"/>
		<input type="hidden" name="task" value="confirm"/>
		<input type="hidden" name="option" value="com_virtuemart"/>
		<input type="hidden" name="view" value="cart"/>
	</form>
</div>

