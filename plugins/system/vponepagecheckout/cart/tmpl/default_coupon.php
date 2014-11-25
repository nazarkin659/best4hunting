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
?>
<div class="inner-wrap">
	<div class="proopc-input-append proopc-row">
		<input type="text" name="coupon_code" size="20" maxlength="50" id="proopc-coupon-code" alt="<?php echo $this->coupon_text ?>" value="<?php echo $this->coupon_text; ?>" onblur="if(this.value=='') this.value='<?php echo $this->coupon_text; ?>';" onfocus="if(this.value=='<?php echo $this->coupon_text; ?>') this.value='';" data-default="<?php echo $this->coupon_text; ?>" />
		<button class="proopc-btn<?php if($this->params->get('color', 1) == 2) echo ' proopc-btn-danger'; ?>" title="<?php echo JText::_('COM_VIRTUEMART_SAVE'); ?>" onclick="return ProOPC.savecoupon(this);"><?php echo JText::_('COM_VIRTUEMART_SAVE'); ?></button>
	</div>
</div>

