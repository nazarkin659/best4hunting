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
if ($this->found_payment_method) {
	$db = JFactory::getDBO();
	$query = $db->getQuery(true);
	$query->select(array('virtuemart_paymentmethod_id', 'payment_element', 'payment_params'));
	$query->from('#__virtuemart_paymentmethods');
	$query->where('published = 1');
	$db->setQuery($query);
	$PMs = $db->loadObjectList();
	$paypalExpress = false;
	foreach($PMs as $key=>$PM) {		
		$moredata = 'data-paypalproduct="false"';
		if($PM->payment_element == 'paypal') {
			$params = array();
			foreach(explode('|', $PM->payment_params) as $param) {
				if(strpos($param, 'paypalproduct') !== false) {
					list ($cKey, $cValue) = explode('=', $param, 2);
					$params[$cKey] = $cValue;				
				}
			}
			if(isset($params['paypalproduct']) && !empty($params['paypalproduct'])) {
				$moredata = 'data-paypalproduct='.$params['paypalproduct'];
			}			
			if(isset($params['paypalproduct']) && str_replace('"', '', $params['paypalproduct']) == 'exp') {
				$paypalExpress = true;
			}
		}
		$pmElement[] = 'id="payment_id_'.$PM->virtuemart_paymentmethod_id.'"';	
		$newElement[] = 'id="payment_id_'.$PM->virtuemart_paymentmethod_id.'" data-pmtype="'.$PM->payment_element.'" '.$moredata;
		$newInfo[] = 'vmpayment_cardinfo additional-payment-info methodid-'.$PM->virtuemart_paymentmethod_id.' '.$PM->payment_element;
		$methodEIDs[$key]['virtuemart_paymentmethod_id'] = $PM->virtuemart_paymentmethod_id;
		$methodEIDs[$key]['payment_element'] = $PM->payment_element;
	}

	$find = array(
					'<table', 
					'</table', 
					'border="0" cellspacing="0" cellpadding="2" width="100%"', 
					'class="wrapper_paymentdetails"',
					'<tr valign="top"', 
					'<tr valign="middle"',
					'<tr>',
					'<tr',
					'</tr', 
					'<td nowrap width="10%" align="right"', 
					'<td>',
					'<td',
					'</td', 
					'<br />', 
					'hasTip');
	$find = array_merge($pmElement, $find);
	$replace = array(
					'<div', 
					'</div', 
					'class="proopc-creditcard-info"',
					'class="wrapper_paymentdetails proopc-creditcard-info"',
					'<div class="proopc-row"', 
					'<div class="proopc-row"',
					'<div class="proopc-row">',
					'<div class="proopc-row"', 
					'</div', 
					'<div class="creditcard-label"', 
					'<div>',
					'<div', 
					'</div', 
					'', 
					'hover-tootip');
	$replace = array_merge($newElement, $replace);
	
	// Now start HTML layout
	echo '<div class="inner-wrap">';
	echo "<form id=\"proopc-payment-form\"><fieldset>";
	foreach ($this->paymentplugins_payments as $paymentplugin_payments) {
		if (is_array($paymentplugin_payments)) {
			foreach ($paymentplugin_payments as $paymentplugin_payment) {
				$pos = strpos($paymentplugin_payment, '<input');
				$output = substr_replace($paymentplugin_payment,'<input onclick="return ProOPC.setpayment(this);"',$pos,strlen('<input'));		
				foreach($methodEIDs as $methodEID) {
					if(strstr($output, 'id="payment_id_'.$methodEID['virtuemart_paymentmethod_id'])) {
						$output = str_replace('vmpayment_cardinfo', 'vmpayment_cardinfo additional-payment-info '.$methodEID['payment_element'], $output);
						$output = str_replace($find, $replace, $output);
						if(strstr($output, 'checked="checked"')) {
							$output = str_replace('vmpayment_cardinfo additional-payment-info '.$methodEID['payment_element'], 'vmpayment_cardinfo additional-payment-info '.$methodEID['payment_element'].' show', $output);
						} else {
							$output = str_replace('vmpayment_cardinfo additional-payment-info '.$methodEID['payment_element'], 'vmpayment_cardinfo additional-payment-info '.$methodEID['payment_element'].' hide', $output);
						}
					}
				}
        $dom = new domDocument;
  		  $dom->loadHTML($output);        
        $klarna_classname = 'klarnaPayment';
        $finder = new DomXPath($dom);
        $klarna = $finder->query("//*[contains(concat(' ', normalize-space(@class), ' '), ' $klarna_classname ')]");        
        if($klarna->length) {
          $klarna_tables = $dom->getElementsByTagName('table');
          if(!empty($klarna_tables)) {
            foreach($klarna_tables as $klarna_table) {
              $klarna_table->setAttribute('class', 'proopc-klarna-payment');
              break;
            }            
          }
        $output = $dom->saveHTML();  
        }
				echo $output;
				echo '<div class="clear"></div>';
			}
		}
	}
	echo '<input type="hidden" name="proopc-savedPayment" id="proopc-savedPayment" value="'.$this->cart->virtuemart_paymentmethod_id.'" />';
	echo "</fieldset></form>";
	echo "</div>";
	if($paypalExpress) {
		$checkoutAdvertises = array();
		JPluginHelper::importPlugin('vmcoupon');
		JPluginHelper::importPlugin('vmpayment');
		JPluginHelper::importPlugin('vmshipment');
		$dispatcher = JDispatcher::getInstance();
		$returnValues = $dispatcher->trigger('plgVmOnCheckoutAdvertise', array( $this->cart, &$checkoutAdvertises));
		if(!empty($checkoutAdvertises)) { ?>
		<div id="proopc-payment-advertise">
		  <?php foreach($checkoutAdvertises as $checkoutAdvertise) { ?>
				<div class="checkout-advertise">
					<?php echo $checkoutAdvertise; ?>
				</div>
			<?php } ?>
		</div>
		<?php				
		}
	}
} 
else 
{
	echo '<div class="proopc-alert-error payment">' . $this->payment_not_found_text . '</div>';  
} ?>
