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

if($this->params->get('style', 1) == 1) { 
?>
<div class="inner-wrap">
	<table class="proopc-cart-summery" width="100%" cellpadding="0" cellspacing="0">
		<thead>
			<tr>
				<th class="col-name" align="left"><?php echo JText::_('COM_VIRTUEMART_CART_NAME') ?></th>
				<th class="col-qty" align="center"><?php echo JText::_('COM_VIRTUEMART_CART_QUANTITY') ?></th>
				<th class="col-total" align="right"><?php echo JText::_('COM_VIRTUEMART_CART_TOTAL') ?></th>			
			</tr>
		</thead>
		<?php
		$i=1;
		foreach( $this->cart->products as $pkey =>$prow ) : ?>	
		<tbody class="proopc-cart-product" data-details="proopc-product-details<?php echo $i ?>">
			<tr valign="top" class="proopc-cart-entry<?php echo $i ?> proopc-p-list" >
				<td class="col-name">
					<?php echo $prow->product_name.$prow->customfields; ?>
					<div class="proopc-p-price">
						<?php echo JText::_('COM_VIRTUEMART_CART_PRICE') ?>
						<?php if ($this->cart->pricesUnformatted[$pkey]['discountedPriceWithoutTax']) {
							echo $this->currencyDisplay->createPriceDiv ('discountedPriceWithoutTax', '', $this->cart->pricesUnformatted[$pkey], FALSE, FALSE);
						} else if($this->cart->pricesUnformatted[$pkey]['basePriceVariant'] !== $this->cart->pricesUnformatted[$pkey]['basePrice']) {
							echo $this->currencyDisplay->createPriceDiv ('basePriceVariant', '', $this->cart->pricesUnformatted[$pkey], FALSE, FALSE); 
						}  else {
							echo $this->currencyDisplay->createPriceDiv ('basePrice', '', $this->cart->pricesUnformatted[$pkey], FALSE, FALSE); 
						}?>	
					</div>
					<?php if( $this->params->get('show_sku', 1)==1) { ?>
					<div class="proopc-p-sku">
						<?php echo JText::_('COM_VIRTUEMART_CART_SKU').': '.$prow->product_sku ?>
					</div>
					<?php } ?>
				</td>
				<td class="col-qty" align="center">
					<?php echo $prow->quantity ?>
				</td>
				<td class="col-total" colspan="1" align="right">
					<?php if (VmConfig::get('checkout_show_origprice',1) && !empty($this->cart->pricesUnformatted[$pkey]['basePriceWithTax']) && $this->cart->pricesUnformatted[$pkey]['basePriceWithTax'] != $this->cart->pricesUnformatted[$pkey]['salesPrice'] ) {
					echo '<span class="line-through">'.$this->currencyDisplay->createPriceDiv('basePriceWithTax','', $this->cart->pricesUnformatted[$pkey],true,false,$prow->quantity) .'</span><br />' ;
					}
					echo $this->currencyDisplay->createPriceDiv('salesPrice','', $this->cart->pricesUnformatted[$pkey],false,false,$prow->quantity) ?>
				</td>
			</tr>
			<?php // Start - Mouse Over Details ?>
			<tr id="proopc-product-details<?php echo $i ?>" class="proopc-product-hover hide">
				<td colspan="4">
					<div class="proopc_arrow_box">
					<table class="proopc-p-info-table">
						<tr>
							<?php if ( $prow->virtuemart_media_id) {  ?>
							<td colspan="2">
								<div class="proopc-product-image">
									<div class="p-info-inner">
										<?php if(!empty($prow->image)) echo $prow->image->displayMediaThumb('',false); ?>
									</div>
								</div>
								<div class="proopc-p-info">
									<div class="p-info-inner">
										<div class="proopc-product-name"><?php echo JHTML::link($prow->url, $prow->product_name).$prow->customfields; ?></div>
										<table class="proopc-price-table" cellspacing="5">
											<tr>
												<td><?php echo JText::_('COM_VIRTUEMART_CART_PRICE') ?></td>
												<td class="col-price" align="right">
													<?php if ($this->cart->pricesUnformatted[$pkey]['discountedPriceWithoutTax']) {
														echo $this->currencyDisplay->createPriceDiv ('discountedPriceWithoutTax', '', $this->cart->pricesUnformatted[$pkey], FALSE, FALSE);
													} else if($this->cart->pricesUnformatted[$pkey]['basePriceVariant'] !== $this->cart->pricesUnformatted[$pkey]['basePrice']) {
														echo $this->currencyDisplay->createPriceDiv ('basePriceVariant', '', $this->cart->pricesUnformatted[$pkey], FALSE, FALSE); 
													}  else {
														echo $this->currencyDisplay->createPriceDiv ('basePrice', '', $this->cart->pricesUnformatted[$pkey], FALSE, FALSE); 
													}?>	
												</td>
											</tr>
											<?php if ( VmConfig::get('show_tax')) { ?>
											<tr>
												<td><?php  echo JText::_('COM_VIRTUEMART_CART_SUBTOTAL_TAX_AMOUNT') ?></td>
												<td class="col-price" align="right"><?php echo $this->currencyDisplay->createPriceDiv('taxAmount','', $this->cart->pricesUnformatted[$pkey],false,false,$prow->quantity) ?></td>
											</tr>
											<?php } ?>
											<tr>
												<td><?php echo JText::_('COM_VIRTUEMART_CART_SUBTOTAL_DISCOUNT_AMOUNT') ?></td>
												<td class="col-price" align="right"><?php echo $this->currencyDisplay->createPriceDiv('discountAmount','', $this->cart->pricesUnformatted[$pkey],false,false,$prow->quantity) ?></td>
											</tr>
											<tr>
												<td><?php echo JText::_('COM_VIRTUEMART_CART_TOTAL') ?></td>
												<td class="col-total-price" align="right"><?php echo $this->currencyDisplay->createPriceDiv('salesPrice','', $this->cart->pricesUnformatted[$pkey],false,false,$prow->quantity) ?></td>
											</tr>																									
										</table>
									</div>							
								</div>
							</td>
							<?php } else { ?>
							<td colspan="2">
								<div class="proopc-p-info noimage">
									<div class="p-info-inner">
										<div class="proopc-product-name"><?php echo JHTML::link($prow->url, $prow->product_name).$prow->customfields; ?></div>
										<table class="proopc-price-table" cellspacing="5">
											<tr>
												<td><?php echo JText::_('COM_VIRTUEMART_CART_PRICE') ?></td>
												<td class="col-price" align="right"><?php echo $this->currencyDisplay->createPriceDiv('basePriceVariant','', $this->cart->pricesUnformatted[$pkey],false);?></td>
											</tr>
											<?php if ( VmConfig::get('show_tax')) { ?>
											<tr>
												<td><?php  echo JText::_('COM_VIRTUEMART_CART_SUBTOTAL_TAX_AMOUNT') ?></td>
												<td class="col-price" align="right"><?php echo $this->currencyDisplay->createPriceDiv('taxAmount','', $this->cart->pricesUnformatted[$pkey],false,false,$prow->quantity) ?></td>
											</tr>
											<?php } ?>
											<tr>
												<td><?php echo JText::_('COM_VIRTUEMART_CART_SUBTOTAL_DISCOUNT_AMOUNT') ?></td>
												<td class="col-price" align="right"><?php echo $this->currencyDisplay->createPriceDiv('discountAmount','', $this->cart->pricesUnformatted[$pkey],false,false,$prow->quantity) ?></td>
											</tr>
											<tr>
												<td><?php echo JText::_('COM_VIRTUEMART_CART_TOTAL') ?></td>
												<td class="col-total-price" align="right"><?php echo $this->currencyDisplay->createPriceDiv('salesPrice','', $this->cart->pricesUnformatted[$pkey],false,false,$prow->quantity) ?></td>
											</tr>																									
										</table>
									</div>							
								</div>
							</td>
							<?php } ?>						
						</tr>
						<tr>
							<td colspan="2" class="proopc-mini-qty-area">
								<div class="proopc-qty-title">
									<?php echo JText::_('COM_VIRTUEMART_CART_QUANTITY') ?>
								</div>
								<div class="proopc-qty-update">
									<div class="proopc-input-append">
										<input type="text" title="<?php echo  JText::_('COM_VIRTUEMART_CART_UPDATE') ?>" class="inputbox input-ultra-mini" size="3" maxlength="4" name="quantity" value="<?php echo $prow->quantity ?>" data-vpid="<?php echo $prow->cart_item_id  ?>" />
										<button class="proopc-btn<?php if($this->params->get('color', 1) == 2) echo ' proopc-btn-danger'; ?>" name="update" title="<?php echo  JText::_('COM_VIRTUEMART_CART_UPDATE') ?>" onclick="return ProOPC.updateproductqty(this);"><i class="proopc-icon-refresh"></i></button>
									</div>	
								</div>	
								<div class="proopc-delete-product">							
									<button class="proopc-btn<?php if($this->params->get('color', 1) == 2) echo ' proopc-btn-danger'; ?>" name="delete" title="<?php echo  JText::_('COM_VIRTUEMART_CART_DELETE') ?>" data-vpid="<?php echo $prow->cart_item_id  ?>" onclick="return ProOPC.deleteproduct(this);"><i class="proopc-icon-trash"></i></button>
								</div>
							</td>
						</tr>
					</table>
					</div>
				</td>
			</tr>
		</tbody>
		<?php 
		$i++;
		endforeach; ?>
		
		<tbody class="proopc-subtotal">
			<tr class="proopc-cart-sub-total">
				<td class="sub-headings" colspan="2" align="left">
					<?php echo JText::_('COM_VIRTUEMART_ORDER_PRINT_PRODUCT_PRICES_TOTAL'); ?>
				</td>
				<td class="col-total" align="right">
					<?php echo $this->currencyDisplay->createPriceDiv('salesPrice','', $this->cart->pricesUnformatted,false); ?>
				</td>
			</tr>		
		</tbody>	
		
		<?php if (VmConfig::get('coupons_enable') and !empty($this->cart->cartData['couponCode'])) { ?>
			<tbody class="proopc-coupon-details">
				<tr class="cart-coupon-row">
					<td class="coupon-form-col" colspan="2" align="left">
					<?php if (!empty($this->cart->cartData['couponCode'])) { 		
						echo '<span>';			 
						echo $this->cart->cartData['couponCode'] ;
						echo $this->cart->cartData['couponDescr'] ? (' (' . $this->cart->cartData['couponDescr'] . ')' ): '';
						echo '</span>';	
					}?>
					</td>
					<?php if (!empty($this->cart->cartData['couponCode'])) { ?>
					<td class="col-total" align="right">
						<?php echo $this->currencyDisplay->createPriceDiv('salesPriceCoupon','', $this->cart->pricesUnformatted['salesPriceCoupon'],false); ?> 
					</td>
					<?php } else { ?>
					<td align="left">&nbsp;</td>
					<?php }	?>
				</tr>
			</tbody>
			<?php } ?>	
			
			<?php if(count($this->cart->cartData['DBTaxRulesBill']) || count($this->cart->cartData['taxRulesBill']) || count($this->cart->cartData['DATaxRulesBill'])) : ?>
			<tbody>
			<?php
			$i = 1;
			foreach($this->cart->cartData['DBTaxRulesBill'] as $rule){ ?>
				<tr class="sectiontableentry<?php echo $i ?> tax-per-bill">
					<td class="sub-headings" colspan="2" align="right">
						<?php echo $rule['calc_name'] ?>
						<?php if ( VmConfig::get('show_tax')) { ?>
						<div><?php  echo JText::_('COM_VIRTUEMART_CART_SUBTOTAL_TAX_AMOUNT').': '; ?><?php echo $this->currencyDisplay->createPriceDiv($rule['virtuemart_calc_id'].'Diff','', $this->cart->pricesUnformatted[$rule['virtuemart_calc_id'].'Diff'],false); ?></div>
						<?php } ?> 
					</td>
					<td class="col-total" align="right">
						<?php echo $this->currencyDisplay->createPriceDiv($rule['virtuemart_calc_id'].'Diff','', $this->cart->pricesUnformatted[$rule['virtuemart_calc_id'].'Diff'],false); ?> 
					</td>
				</tr>
				<?php
				$i++;
			} 
			
			$i = 1;
			foreach($this->cart->cartData['taxRulesBill'] as $rule){ ?>
				<tr class="sectiontableentry<?php echo $i ?> tax-per-bill">
					<td class="sub-headings" colspan="2" align="right">
						<?php echo $rule['calc_name'] ?>
						<?php if ( VmConfig::get('show_tax')) { ?>
						<div><?php  echo JText::_('COM_VIRTUEMART_CART_SUBTOTAL_TAX_AMOUNT').': '; ?><?php echo $this->currencyDisplay->createPriceDiv($rule['virtuemart_calc_id'].'Diff','', $this->cart->pricesUnformatted[$rule['virtuemart_calc_id'].'Diff'],false); ?></div>
						<?php } ?> 
					</td>
					<td class="col-total" align="right">
						<?php echo $this->currencyDisplay->createPriceDiv($rule['virtuemart_calc_id'].'Diff','', $this->cart->pricesUnformatted[$rule['virtuemart_calc_id'].'Diff'],false); ?> 
					</td>
				</tr>
				<?php
				$i++;
			}
			
			$i = 1;
			foreach($this->cart->cartData['DATaxRulesBill'] as $rule){ ?>
				<tr class="sectiontableentry<?php echo $i ?> tax-per-bill">
					<td class="sub-headings" colspan="2" align="right">
						<?php echo $rule['calc_name'] ?>
						<?php if ( VmConfig::get('show_tax')) { ?>
						<div><?php  echo JText::_('COM_VIRTUEMART_CART_SUBTOTAL_TAX_AMOUNT').': '; ?><?php echo $this->currencyDisplay->createPriceDiv($rule['virtuemart_calc_id'].'Diff','', $this->cart->pricesUnformatted[$rule['virtuemart_calc_id'].'Diff'],false); ?></div>
						<?php } ?> 
					</td>
					<td class="col-total" align="right">
						<?php echo $this->currencyDisplay->createPriceDiv($rule['virtuemart_calc_id'].'Diff','', $this->cart->pricesUnformatted[$rule['virtuemart_calc_id'].'Diff'],false); ?> 
					</td>
				</tr>
				<?php
				$i++;
			} ?>		
			</tbody>
			<?php endif; ?>	
			
			<tbody class="poopc-shipment-table">
				<tr>		
					<td class="shipping-heading" colspan="2" align="left">
					<?php echo $this->cart->cartData['shipmentName']; 
					if ( VmConfig::get('show_tax') and $this->cart->pricesUnformatted['shipmentTax']) { ?>
						<div class="proopc-taxcomponent">
							<?php echo JText::_('COM_VIRTUEMART_CART_SUBTOTAL_TAX_AMOUNT').': '; ?><?php echo $this->currencyDisplay->createPriceDiv('shipmentTax','', $this->cart->pricesUnformatted['shipmentTax'],false); ?>
						</div>
					<?php } ?>					
					</td>				
					<td class="col-total" align="right">
					<?php if(!empty($this->cart->pricesUnformatted['salesPriceShipment'])) { 
						echo $this->currencyDisplay->createPriceDiv('salesPriceShipment','', $this->cart->pricesUnformatted['salesPriceShipment'],false); 	
					} ?>	
					</td>	
				</tr>	
			</tbody>
			
			<tbody class="poopc-payment-table">
				<tr>		
					<td class=payment-heading" colspan="2" align="left">
					<?php echo $this->cart->cartData['paymentName']; 
					if ( VmConfig::get('show_tax') and $this->cart->pricesUnformatted['paymentTax']) { ?>
						<div class="proopc-taxcomponent">
							<?php echo JText::_('COM_VIRTUEMART_CART_SUBTOTAL_TAX_AMOUNT').': '; ?><?php echo $this->currencyDisplay->createPriceDiv('paymentTax','', $this->cart->pricesUnformatted['paymentTax'],false); ?>
						</div>
					<?php } ?>					
					</td>				
					<td class="col-total" align="right">
					<?php if(!empty($this->cart->pricesUnformatted['salesPricePayment'])) { 
						echo $this->currencyDisplay->createPriceDiv('salesPricePayment','', $this->cart->pricesUnformatted['salesPricePayment'],false); 	
					} ?>	
					</td>	
				</tr>	
			</tbody>	
			
			<tbody class="proopc-grand-total">
				<tr class="grand-total">
					<td class="sub-headings" colspan="2" align="left"><?php echo JText::_('COM_VIRTUEMART_CART_TOTAL') ?>: 
						<?php if ( VmConfig::get('show_tax') and $this->cart->pricesUnformatted['billTaxAmount']) { ?>
						<div class="proopc-taxcomponent">
							<?php echo JText::_('COM_VIRTUEMART_CART_SUBTOTAL_TAX_AMOUNT').': '; ?><?php echo $this->currencyDisplay->createPriceDiv('billTaxAmount','', $this->cart->pricesUnformatted['billTaxAmount'],false) ?>
						</div>
						<?php } ?>
						<?php if ( $this->cart->pricesUnformatted['billDiscountAmount']) { ?>
							<div class="proopc-p-discount">
								<?php echo JText::_('COM_VIRTUEMART_CART_SUBTOTAL_DISCOUNT_AMOUNT').': '; ?><?php echo $this->currencyDisplay->createPriceDiv('billDiscountAmount','', $this->cart->pricesUnformatted['billDiscountAmount'],false) ?>
							</div>
						<?php } ?>
						
					</td>
					<td class="col-total" align="right"><?php echo $this->currencyDisplay->createPriceDiv('billTotal','', $this->cart->pricesUnformatted['billTotal'],false); ?></td>
				</tr>				
			</tbody>	
			
			<?php if ( $this->totalInPaymentCurrency) { ?>
			<tbody class="proopc-grand-total-p-currency">			
				<tr class="grand-total-p-currency">
					<td class="sub-headings" colspan="2" align="left"><?php echo JText::_('COM_VIRTUEMART_CART_TOTAL_PAYMENT') ?>: </td>
					<td class="col-total" align="right"><span class="PricesalesPrice"><?php echo $this->totalInPaymentCurrency; ?></span></td>
				</tr>				
			</tbody>	
			<?php } ?>		
	</table>
</div>
<?php } 
else if($this->params->get('style', 1) == 2) {
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
		<?php if (VmConfig::get('coupons_enable') and !empty($this->cart->cartData['couponCode'])) { ?>
			<tr class="cart-coupon-row">
				<td class="coupon-form-col" colspan="4" align="left">
					<?php 		
						echo JText::_('COM_VIRTUEMART_COUPON_DISCOUNT').'<div>';			 
						echo $this->cart->cartData['couponCode'] ;
						echo $this->cart->cartData['couponDescr'] ? (' (' . $this->cart->cartData['couponDescr'] . ')' ): '';
						echo '</div>';	
					?>
				</td>
				<?php if (VmConfig::get('show_tax')) { ?>
					<td class="col-tax" align="right">
						<?php echo $this->currencyDisplay->createPriceDiv('couponTax','', $this->cart->pricesUnformatted['couponTax'],false); ?> 
					</td>
				<?php } ?>
					<td colspan="2" class="col-total" align="right">
						<?php echo $this->currencyDisplay->createPriceDiv('salesPriceCoupon','', $this->cart->pricesUnformatted['salesPriceCoupon'],false); ?> 
					</td>
			</tr>
		<?php } ?>
		<?php
		foreach($this->cart->cartData['DBTaxRulesBill'] as $rule){ ?>
			<tr class="sectiontableentry<?php echo $i ?> tax-per-bill">
				<td class="sub-headings" colspan="4" align="right"><?php echo $rule['calc_name'] ?> </td>
				<?php if ( VmConfig::get('show_tax')) { ?>
				<td class="col-tax" align="right"><?php echo $this->currencyDisplay->createPriceDiv($rule['virtuemart_calc_id'].'Diff','', $this->cart->pricesUnformatted[$rule['virtuemart_calc_id'].'Diff'],false); ?></td>				
				<?php } ?>
				<td colspan="2" class="col-total" align="right"><?php echo $this->currencyDisplay->createPriceDiv($rule['virtuemart_calc_id'].'Diff','', $this->cart->pricesUnformatted[$rule['virtuemart_calc_id'].'Diff'],false); ?> </td>
			</tr>
			<?php
			if($i) $i=1; else $i=0;
		} 
		
		foreach($this->cart->cartData['taxRulesBill'] as $rule){ ?>
			<tr class="sectiontableentry<?php echo $i ?> tax-per-bill">
				<td class="sub-headings" colspan="4" align="right"><?php echo $rule['calc_name'] ?> </td>
				<?php if ( VmConfig::get('show_tax')) { ?>
				<td class="col-tax" align="right"><?php echo $this->currencyDisplay->createPriceDiv($rule['virtuemart_calc_id'].'Diff','', $this->cart->pricesUnformatted[$rule['virtuemart_calc_id'].'Diff'],false); ?> </td>
				 <?php } ?>
				<td colspan="2" class="col-total" align="right"><?php echo $this->currencyDisplay->createPriceDiv($rule['virtuemart_calc_id'].'Diff','', $this->cart->pricesUnformatted[$rule['virtuemart_calc_id'].'Diff'],false); ?> </td>
			</tr>
			<?php
			if($i) $i=1; else $i=0;
		}

		foreach($this->cart->cartData['DATaxRulesBill'] as $rule){ ?>
			<tr class="sectiontableentry<?php echo $i ?> tax-per-bill">
				<td class="sub-headings" colspan="4" align="right"><?php echo   $rule['calc_name'] ?> </td>
				<?php if ( VmConfig::get('show_tax')) { ?>
				<td class="col-tax" align="right"><?php echo $this->currencyDisplay->createPriceDiv($rule['virtuemart_calc_id'].'Diff','', $this->cart->pricesUnformatted[$rule['virtuemart_calc_id'].'Diff'],false); ?>  </td>		
				<?php } ?>		
				<td colspan="2" class="col-total" align="right"><?php echo $this->currencyDisplay->createPriceDiv($rule['virtuemart_calc_id'].'Diff','', $this->cart->pricesUnformatted[$rule['virtuemart_calc_id'].'Diff'],false); ?> </td>
			</tr>
			<?php
			if($i) $i=1; else $i=0;
		} ?>

			<tr class="sectiontableentry1 shipping-row">
      	<?php if (!$this->cart->automaticSelectedShipment) { ?>
				<td class="shipping-payment-heading" colspan="4" align="left">
					<?php echo $this->cart->cartData['shipmentName']; ?>
				</td>
				<?php } else { ?>
        <td class="shipping-payment-heading" colspan="4" align="left">
					<?php echo $this->cart->cartData['shipmentName']; ?>
				</td>
        <?php } ?>
				<?php if ( VmConfig::get('show_tax')) { ?>
					<td class="col-tax" align="right">
						<?php echo $this->currencyDisplay->createPriceDiv('shipmentTax','', $this->cart->pricesUnformatted['shipmentTax'],false); ?>
					</td>
					<?php } ?>
					<td class="col-total" colspan="2" align="right">
						<?php echo $this->currencyDisplay->createPriceDiv('salesPriceShipment','', $this->cart->pricesUnformatted['salesPriceShipment'],false); ?> 
					</td>
			</tr>
			
			<tr class="sectiontableentry1 payment-row">
			<?php if (!$this->cart->automaticSelectedPayment) { ?>
				<td class="shipping-payment-heading" colspan="4" align="left">
					<?php echo $this->cart->cartData['paymentName']; ?>
				</td>
				<?php } else { ?>
					<td class="shipping-payment-heading" colspan="4" align="left"><?php echo $this->cart->cartData['paymentName']; ?> </td>
				<?php } ?>
				<?php if ( VmConfig::get('show_tax')) { ?>
						<td class="col-tax" align="right"><?php echo $this->currencyDisplay->createPriceDiv('paymentTax','', $this->cart->pricesUnformatted['paymentTax'],false) ?> </td>
					<?php } ?>
					<td class="col-total" colspan="2" align="right"><?php  echo $this->currencyDisplay->createPriceDiv('salesPricePayment','', $this->cart->pricesUnformatted['salesPricePayment'],false); ?> </td>
			</tr>
			
		  <tr class="blank-row">
				<td colspan="4">&nbsp;</td>
				<td colspan="<?php echo $colspan ?>"></td>
		  </tr>
			
		  <tr class="sectiontableentry2 grand-total">
				<td class="sub-headings" colspan="4" align="right"><?php echo JText::_('COM_VIRTUEMART_CART_TOTAL') ?>: </td>
				<?php if ( VmConfig::get('show_tax')) { ?>
					<td class="col-tax" align="right"> <?php echo $this->currencyDisplay->createPriceDiv('billTaxAmount','', $this->cart->pricesUnformatted['billTaxAmount'],false) ?> </td>
				<?php } ?>
				<td class="col-discount" align="right"> <?php echo $this->currencyDisplay->createPriceDiv('billDiscountAmount','', $this->cart->pricesUnformatted['billDiscountAmount'],false) ?> </td>
				<td class="col-total" align="right"><?php echo $this->currencyDisplay->createPriceDiv('billTotal','', $this->cart->pricesUnformatted['billTotal'],false); ?></td>
			</tr>
			
			<?php if ( $this->totalInPaymentCurrency) { ?>
			<tr class="sectiontableentry2 grand-total-p-currency">
				<td class="sub-headings" colspan="4" align="right"><?php echo JText::_('COM_VIRTUEMART_CART_TOTAL_PAYMENT') ?>: </td>
				<td class="col-total" <?php if ( VmConfig::get('show_tax')) { echo 'colspan="3"'; } else { echo 'colspan="2"'; } ?> align="right"><span class="PricesalesPrice"><?php echo $this->totalInPaymentCurrency; ?></span></td>
			</tr>
			<?php } ?>
		</tbody>
	</table>
</fieldset>
<?php } ?>