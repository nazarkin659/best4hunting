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
if ($this->found_shipment_method) {
echo '<form id="proopc-shipment-form"><div class="inner-wrap">';
echo "<fieldset>\n";
foreach ($this->shipments_shipment_rates as $shipment_shipment_rates) {
		if (is_array($shipment_shipment_rates)) {
			foreach ($shipment_shipment_rates as $shipment_shipment_rate) {
				echo str_replace('name="virtuemart_shipmentmethod_id"', 'name="virtuemart_shipmentmethod_id" onclick="return ProOPC.setshipment(this);"', $shipment_shipment_rate);
				echo '<div class="clear"></div>';
	    }
		}
}
echo '<input type="hidden" name="proopc-savedShipment" id="proopc-savedShipment" value="'.$this->cart->virtuemart_shipmentmethod_id.'" />';
echo "</fieldset>\n";	
echo "</div></form>";
} 
else {
	echo '<div class="proopc-alert-error">'.$this->shipment_not_found_text.'</div>'; 
} ?>

