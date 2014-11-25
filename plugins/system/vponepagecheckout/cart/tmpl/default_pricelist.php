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

if($this->params->get('responsive', 1)) {	 
	$document = JFactory::getDocument();
	if (VmConfig::get('show_tax')) { 
		$document->addStyleDeclaration("
			@media (max-width: 767px) {
				.cart-p-list td:nth-of-type(1):before { content: '".JText::_('COM_VIRTUEMART_CART_NAME')."'; }
				.cart-p-list td:nth-of-type(2):before { content: '".JText::_('COM_VIRTUEMART_CART_SKU')."'; }
				.cart-p-list td:nth-of-type(3):before { content: '".JText::_('COM_VIRTUEMART_CART_PRICE')."'; }
				.cart-p-list td:nth-of-type(4):before { content: '".JText::_('COM_VIRTUEMART_CART_QUANTITY'). '/' .JText::_('COM_VIRTUEMART_CART_ACTION')."'; }
				.cart-p-list td:nth-of-type(5):before { content: '".JText::_('COM_VIRTUEMART_CART_SUBTOTAL_TAX_AMOUNT')."'; }
				.cart-p-list td:nth-of-type(6):before { content: '".JText::_('COM_VIRTUEMART_CART_SUBTOTAL_DISCOUNT_AMOUNT')."'; }
				.cart-p-list td:nth-of-type(7):before { content: '".JText::_('COM_VIRTUEMART_CART_TOTAL')."'; }
				.cart-sub-total td:nth-of-type(1):before { content: ''; }
				.cart-sub-total td:nth-of-type(2):before { content: '".JText::_('COM_VIRTUEMART_CART_SUBTOTAL_TAX_AMOUNT')."'; }
				.cart-sub-total td:nth-of-type(3):before { content: '".JText::_('COM_VIRTUEMART_CART_SUBTOTAL_DISCOUNT_AMOUNT')."'; }
				.cart-sub-total td:nth-of-type(4):before { content: '".JText::_('COM_VIRTUEMART_CART_TOTAL')."'; }
				.tax-per-bill td:nth-of-type(1):before { content: ''; }
				.tax-per-bill td:nth-of-type(2):before { content: '".JText::_('COM_VIRTUEMART_CART_SUBTOTAL_TAX_AMOUNT')."'; }
				.tax-per-bill td:nth-of-type(3):before { content: '".JText::_('COM_VIRTUEMART_CART_TOTAL')."'; }	
				.grand-total td:nth-of-type(1):before { content: ''; }
				.grand-total td:nth-of-type(2):before { content: '".JText::_('COM_VIRTUEMART_CART_SUBTOTAL_TAX_AMOUNT')."'; }
				.grand-total td:nth-of-type(3):before { content: '".JText::_('COM_VIRTUEMART_CART_SUBTOTAL_DISCOUNT_AMOUNT')."'; }
				.grand-total td:nth-of-type(4):before { content: '".JText::_('COM_VIRTUEMART_CART_TOTAL')."'; }			
				.grand-total-p-currency td:nth-of-type(1):before { content: ''; }
				.grand-total-p-currency td:nth-of-type(2):before { content: '".JText::_('COM_VIRTUEMART_CART_TOTAL')."'; }						
			}	
		");
		$document->addStyleDeclaration("
			@media (max-width: 767px) {
				.cart-coupon-row td:nth-of-type(1):before { content: ''; }
				.cart-coupon-row td:nth-of-type(2):before { content: '".JText::_('COM_VIRTUEMART_CART_SUBTOTAL_TAX_AMOUNT')."'; }
				.cart-coupon-row td:nth-of-type(3):before { content: '".JText::_('COM_VIRTUEMART_CART_TOTAL')."'; }
			}	
		");
		$document->addStyleDeclaration("
			@media (max-width: 767px) {
				.shipping-row td:nth-of-type(1):before { content: ''; }
				.shipping-row td:nth-of-type(2):before { content: '".JText::_('COM_VIRTUEMART_CART_SUBTOTAL_TAX_AMOUNT')."'; }
				.shipping-row td:nth-of-type(3):before { content: '".JText::_('COM_VIRTUEMART_CART_TOTAL')."'; }
			}	
		");	
		
		$document->addStyleDeclaration("
			@media (max-width: 767px) {
				.payment-row td:nth-of-type(1):before { content: ''; }
				.payment-row td:nth-of-type(2):before { content: '".JText::_('COM_VIRTUEMART_CART_SUBTOTAL_TAX_AMOUNT')."'; }
				.payment-row td:nth-of-type(3):before { content: '".JText::_('COM_VIRTUEMART_CART_TOTAL')."'; }
			}	
		");	

	} else {
		$document->addStyleDeclaration("
			@media (max-width: 767px) {
				.cart-p-list td:nth-of-type(1):before { content: '".JText::_('COM_VIRTUEMART_CART_NAME')."'; }
				.cart-p-list td:nth-of-type(2):before { content: '".JText::_('COM_VIRTUEMART_CART_SKU')."'; }
				.cart-p-list td:nth-of-type(3):before { content: '".JText::_('COM_VIRTUEMART_CART_PRICE')."'; }
				.cart-p-list td:nth-of-type(4):before { content: '".JText::_('COM_VIRTUEMART_CART_QUANTITY'). '/' .JText::_('COM_VIRTUEMART_CART_ACTION')."'; }
				.cart-p-list td:nth-of-type(5):before { content: '".JText::_('COM_VIRTUEMART_CART_SUBTOTAL_DISCOUNT_AMOUNT')."'; }
				.cart-p-list td:nth-of-type(6):before { content: '".JText::_('COM_VIRTUEMART_CART_TOTAL')."'; }
				.cart-sub-total td:nth-of-type(1):before { content: ''; }
				.cart-sub-total td:nth-of-type(2):before { content: '".JText::_('COM_VIRTUEMART_CART_SUBTOTAL_DISCOUNT_AMOUNT')."'; }
				.cart-sub-total td:nth-of-type(3):before { content: '".JText::_('COM_VIRTUEMART_CART_TOTAL')."'; }
				.tax-per-bill td:nth-of-type(1):before { content: ''; }
				.tax-per-bill td:nth-of-type(2):before { content: '".JText::_('COM_VIRTUEMART_CART_TOTAL')."'; }	
				.grand-total td:nth-of-type(1):before { content: ''; }
				.grand-total td:nth-of-type(2):before { content: '".JText::_('COM_VIRTUEMART_CART_SUBTOTAL_DISCOUNT_AMOUNT')."'; }
				.grand-total td:nth-of-type(3):before { content: '".JText::_('COM_VIRTUEMART_CART_TOTAL')."'; }			
				.grand-total-p-currency td:nth-of-type(1):before { content: ''; }
				.grand-total-p-currency td:nth-of-type(2):before { content: '".JText::_('COM_VIRTUEMART_CART_TOTAL')."'; }						
			}	
		");
		$document->addStyleDeclaration("
			@media (max-width: 767px) {
				.cart-coupon-row td:nth-of-type(1):before { content: ''; }
				.cart-coupon-row td:nth-of-type(2):before { content: '".JText::_('COM_VIRTUEMART_CART_TOTAL')."'; }
			}	
		");

		$document->addStyleDeclaration("
			@media (max-width: 767px) {
				.shipping-row td:nth-of-type(1):before { content: ''; }
				.shipping-row td:nth-of-type(2):before { content: '".JText::_('COM_VIRTUEMART_CART_TOTAL')."'; }
			}	
		");	

		$document->addStyleDeclaration("
			@media (max-width: 767px) {
				.payment-row td:nth-of-type(1):before { content: ''; }
				.payment-row td:nth-of-type(2):before { content: '".JText::_('COM_VIRTUEMART_CART_TOTAL')."'; }
			}	
		");	
	}
}
?>
<fieldset>
	<table class="cart-summary proopc-table-striped" >		
		<thead>
			<tr>
				<th class="col-name" align="left"><?php echo JText::_('COM_VIRTUEMART_CART_NAME') ?></th>
				<th class="col-sku" align="left"><?php echo JText::_('COM_VIRTUEMART_CART_SKU') ?></th>
				<th class="col-price" align="center"><?php echo JText::_('COM_VIRTUEMART_CART_PRICE') ?></th>
				<th class="col-qty" align="right"><?php echo JText::_('COM_VIRTUEMART_CART_QUANTITY') ?> / <?php echo JText::_('COM_VIRTUEMART_CART_ACTION') ?></th>
				<?php if ( VmConfig::get('show_tax')) { ?>
	      <th class="col-tax" align="right"><?php  echo JText::_('COM_VIRTUEMART_CART_SUBTOTAL_TAX_AMOUNT') ?></th>
				<?php } ?>
				<th class="col-discount" align="right"><?php echo JText::_('COM_VIRTUEMART_CART_SUBTOTAL_DISCOUNT_AMOUNT') ?></th>
				<th class="col-total" align="right"><?php echo JText::_('COM_VIRTUEMART_CART_TOTAL') ?></th>
			</tr>
		</thead>
		<tbody>
		<?php
		$i=1;
		foreach( $this->cart->products as $pkey =>$prow ) { ?>
			<tr valign="top" class="sectiontableentry<?php echo $i ?> cart-p-list">
				<td class="col-name" align="left" >
					<?php if ( $prow->virtuemart_media_id) {  ?>
						<div class="proopc-row">
							<?php if(!empty($prow->image)) { ?>
							<div class="cart-images">
								<?php echo $prow->image->displayMediaThumb('',false); ?>
							</div>
							<div class="cart-product-description">
								<div><?php echo JHTML::link($prow->url, $prow->product_name).$prow->customfields; ?></div>
							</div>
							<?php } else { ?>
							<div class="cart-product-description-full">
								<?php echo JHTML::link($prow->url, $prow->product_name).$prow->customfields; ?>
							</div>								
							<?php } ?>
						</div>
					<?php } else { 
						echo JHTML::link($prow->url, $prow->product_name).$prow->customfields; 
					}?>
				</td>
				<td class="col-sku" align="left" ><?php  echo $prow->product_sku ?></td>
				<td class="col-price" align="center" >
					<?php if ($this->cart->pricesUnformatted[$pkey]['discountedPriceWithoutTax']) {
						echo $this->currencyDisplay->createPriceDiv ('discountedPriceWithoutTax', '', $this->cart->pricesUnformatted[$pkey], FALSE, FALSE);
					} else if($this->cart->pricesUnformatted[$pkey]['basePriceVariant'] !== $this->cart->pricesUnformatted[$pkey]['basePrice']) {
						echo $this->currencyDisplay->createPriceDiv ('basePriceVariant', '', $this->cart->pricesUnformatted[$pkey], FALSE, FALSE); 
					}  else {
						echo $this->currencyDisplay->createPriceDiv ('basePrice', '', $this->cart->pricesUnformatted[$pkey], FALSE, FALSE); 
					}?>
				</td>
				<td class="col-qty cart-p-qty" align="right" >
					<div class="proopc-input-append">
						<input type="text" title="<?php echo  JText::_('COM_VIRTUEMART_CART_UPDATE') ?>" class="inputbox input-ultra-mini" size="3" maxlength="4" name="quantity" value="<?php echo $prow->quantity ?>" data-vpid="<?php echo $prow->cart_item_id  ?>" />
						<button class="proopc-btn<?php if($this->params->get('color', 1) == 2) echo ' proopc-btn-danger'; ?>" name="update" title="<?php echo  JText::_('COM_VIRTUEMART_CART_UPDATE') ?>" onclick="return ProOPC.updateproductqty(this);"><i class="proopc-icon-refresh"></i></button>
					</div>
					<button class="remove_from_cart proopc-btn<?php if($this->params->get('color', 1) == 2) echo ' proopc-btn-danger'; ?>" title="<?php echo JText::_('COM_VIRTUEMART_CART_DELETE') ?>" data-vpid="<?php echo $prow->cart_item_id  ?>" onclick="return ProOPC.deleteproduct(this);"><i class="proopc-icon-trash"></i></button>
				</td>
				<?php if ( VmConfig::get('show_tax')) { ?>
				<td class="col-tax" align="right">
					<?php echo "<span class='priceColor2'>".$this->currencyDisplay->createPriceDiv('taxAmount','', $this->cart->pricesUnformatted[$pkey],false,false,$prow->quantity)."</span>" ?>
				</td>
        <?php } ?>
				<td class="col-discount" align="right">
					<?php echo "<span class='priceColor2'>".$this->currencyDisplay->createPriceDiv('discountAmount','', $this->cart->pricesUnformatted[$pkey],false,false,$prow->quantity)."</span>" ?>
				</td>
				<td class="col-total" colspan="1" align="right">
				<?php
				if (VmConfig::get('checkout_show_origprice',1) && !empty($this->cart->pricesUnformatted[$pkey]['basePriceWithTax']) && $this->cart->pricesUnformatted[$pkey]['basePriceWithTax'] != $this->cart->pricesUnformatted[$pkey]['salesPrice'] ) {
					echo '<span class="line-through">'.$this->currencyDisplay->createPriceDiv('basePriceWithTax','', $this->cart->pricesUnformatted[$pkey],true,false,$prow->quantity) .'</span><br />' ;
				}
				echo $this->currencyDisplay->createPriceDiv('salesPrice','', $this->cart->pricesUnformatted[$pkey],false,false,$prow->quantity) ?>
				</td>
			</tr>
		<?php
			$i = 1 ? 2 : 1;
		} ?>
		
		<!--Begin of SubTotal, Tax, Shipment, Coupon Discount and Total listing -->
    	<?php if ( VmConfig::get('show_tax')) { $colspan=3; } else { $colspan=2; } ?>
			<tr class="blank-row">
				<td colspan="4">&nbsp;</td>
				<td colspan="<?php echo $colspan ?>"></td>
			</tr>
			<tr class="cart-sub-total">
				<td class="sub-headings" colspan="4" align="right"><?php echo JText::_('COM_VIRTUEMART_ORDER_PRINT_PRODUCT_PRICES_TOTAL'); ?></td>
				<?php if ( VmConfig::get('show_tax')) { ?>
				<td class="col-tax" align="right"><?php echo $this->currencyDisplay->createPriceDiv('taxAmount','', $this->cart->pricesUnformatted,false)?></td>
				<?php } ?>
				<td class="col-discount" align="right"><?php echo $this->currencyDisplay->createPriceDiv('discountAmount','', $this->cart->pricesUnformatted,false) ?></td>
				<td class="col-total" align="right"><?php echo $this->currencyDisplay->createPriceDiv('salesPrice','', $this->cart->pricesUnformatted,false) ?></td>
			</tr>
			<?php	
			$checkoutAdvertises = array();
			JPluginHelper::importPlugin('vmpayment');
			$dispatcher = JDispatcher::getInstance();
			$returnValues = $dispatcher->trigger('plgVmOnCheckoutAdvertise', array( $this->cart, &$checkoutAdvertises));
			if(!empty($checkoutAdvertises)) { ?>
			<tr class="payment-advertise">
				<td class="col-advertisement"  <?php echo VmConfig::get('show_tax') ? 'colspan="7"' : 'colspan="6"'; ?>>
					<div id="proopc-payment-advertise-table">
					  <?php foreach($checkoutAdvertises as $checkoutAdvertise) { ?>
							<div class="checkout-advertise">
								<?php echo $checkoutAdvertise; ?>
							</div>
						<?php } ?>
					</div>
				</td>
			</tr>
			<?php } ?>				
		</tbody>
	</table>
</fieldset>
