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
defined ( '_JEXEC' ) or die ( 'Restricted access' );
if(!empty($this->checkoutAdvertise) and $this->params->get('checkout_advertisement', 1)) { ?>
<div id="proopc-advertise-box">
  <?php foreach($this->checkoutAdvertise as $checkoutAdvertise) { ?>
		<div class="checkout-advertise">
			<?php echo $checkoutAdvertise; ?>
		</div>
	<?php } ?>
</div>
<?php } ?>