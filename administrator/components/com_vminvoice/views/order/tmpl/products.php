<?php


/**
 * PDF Invoicing Solution for VirtueMart & Joomla!
 * 
 * @package   VM Invoice
 * @version   2.0.31
 * @author    ARTIO http://www.artio.net
 * @copyright Copyright (C) 2011 ARTIO s.r.o. 
 * @license   GNU/GPLv3 http://www.artio.net/license/gnu-general-public-license
 */

defined('_JEXEC') or ('Restrict Access');

/* @var $this VMInvoiceViewOrder */

JFilterOutput::objectHTMLSafe($this->orderData);

$document = JFactory::getDocument();

if ($this->orderajax) {
	array_unshift($this->orderStatus, JHTML::_('select.option', '', JText::_('COM_VMINVOICE_SELECT'), 'id', 'name')); 
	array_unshift($this->vendors, JHTML::_('select.option', '', JText::_('COM_VMINVOICE_SELECT'), 'id', 'name'));
}

if (COM_VMINVOICE_ISVM2)
	array_unshift($this->taxRates,JHTML::_('select.option', -1, JText::_('COM_VMINVOICE_OTHER'), 'value', 'name'));
	
$openedAttrs = JRequest::getVar('opened_attrs', array(), 'post', 'array');
$openedRules = JRequest::getVar('opened_rules', array(), 'post', 'array');

$lang = JFactory::getLanguage();
$langTag = $lang->get('tag');

$col = ($this->showWeight ? 1 : 0);
?>

<script type="text/javascript">

function show_all_attributes(){
	var trs = $$('tr[id^=attr_]');
	var opened = 0;
	
	trs.each(function(el){ //if there are unopened attrs, open them all
		if (el.style.display=='none'){
			show_attributes(el.id.substring(5));
			opened++;}
	});

	if (opened==0){ //else close all
		trs.each(function(el){
			show_attributes(el.id.substring(5));
		});
	}
}

function show_attributes(i) {
	var row = document.getElementById('attr_'+i);
	
	if (row.style.display == 'none') {
		row.style.display = 'table-row';
		document.getElementById('show_attr_'+i).getElementsByTagName('SPAN')[0].innerHTML='<?php echo JText::_('COM_VMINVOICE_HIDE'); ?>';
		row.getElement('input[name^=opened_attrs]').value=1;
	}
	else {
		row.getElement('input[name^=opened_attrs]').value=0; //cannot be called after display:none?
		row.style.display = 'none';
		var textarea = document.getElementById('attrs_'+i); //?
		if (textarea && textarea.value.trim()==''){
			document.getElementById('show_attr_'+i).getElementsByTagName('SPAN')[0].innerHTML='<?php echo JText::_('COM_VMINVOICE_ADD'); ?>';
			document.getElementById('show_attr_'+i).className = 'addProduct';}
		else{
			document.getElementById('show_attr_'+i).getElementsByTagName('SPAN')[0].innerHTML='<?php echo JText::_('COM_VMINVOICE_EDIT'); ?>';
			document.getElementById('show_attr_'+i).className = 'editProduct';}
	}	
}

function show_rules(i) {
	var row = document.getElementById('calc_rules_'+i);
	
	if (row.style.display == 'none'){
		row.style.display = 'table-row';
		row.getElement('input[name^=opened_rules]').value=1;}
	else{
		row.style.display = 'none';
		row.getElement('input[name^=opened_rules]').value=0;}
}

</script>
<?php if (! $this->orderajax) { ?><div id="orderInfo"><?php	} ?>
<h2 class="basket"><?php echo JText::_('COM_VMINVOICE_ORDER_ITEMS'); ?></h2>
<table class="adminlist admintable" cellspacing="1">
	<thead>
		<tr> 
			<th width="1%" nowrap="nowrap"><?php echo JText::_('COM_VMINVOICE_ACTIONS'); ?></th>
			<th width="1%" nowrap="nowrap"><?php echo JText::_('COM_VMINVOICE_PRODUCT_SKU'); ?></th>
			<th><?php echo JText::_('COM_VMINVOICE_NAME'); ?></th>
			<th width="1%" nowrap="nowrap" style="cursor:pointer" onclick="show_all_attributes();"><?php echo JText::_('COM_VMINVOICE_ATTRIBUTES'); ?></th>
			<th width="1%" nowrap="nowrap"><?php echo JText::_('COM_VMINVOICE_ORDER_STATUS'); ?></th>
			<th width="1%" nowrap="nowrap"><?php echo JText::_('COM_VMINVOICE_VENDOR'); ?></th>
			<th width="1%" nowrap="nowrap"><?php echo JText::_('COM_VMINVOICE_QUANTITY'); ?></th>
            <?php if ($this->showWeight) { ?>
                <th width="1%" nowrap="nowrap"><?php echo JText::_('COM_VMINVOICE_WEIGHT'); ?></th>
            <?php } ?>
			<?php if (COM_VMINVOICE_ISVM2) {?>
			<th width="1%" nowrap="nowrap">Calc.</th>
			<?php }?>
			<th width="1%" nowrap="nowrap"><?php echo JText::_('COM_VMINVOICE_PRICE_NET'); ?></th>
			<th width="1%" nowrap="nowrap"><?php echo JText::_('COM_VMINVOICE_TAX'); ?></th>
			<th width="1%" nowrap="nowrap"><?php echo JText::_('COM_VMINVOICE_PRICE_GROSS'); ?></th>
			<?php if (COM_VMINVOICE_ISVM2) {?>
			<th width="1%" nowrap="nowrap"><?php echo JText::_('COM_VMINVOICE_DISCOUNT'); ?></th>
			<?php }?>
			<th width="1%" nowrap="nowrap"><?php echo JText::_('COM_VMINVOICE_SUBTOTAL'); ?></th>
			<th width="1%" nowrap="nowrap"><?php echo JText::_('COM_VMINVOICE_TAX'); ?></th>
			<?php if (COM_VMINVOICE_ISVM2) {?>
			<th width="1%" nowrap="nowrap"><?php echo JText::_('COM_VMINVOICE_DISCOUNT'); ?></th>
			<?php }?>
			<th width="1%" nowrap="nowrap"><?php echo JText::_('COM_VMINVOICE_TOTAL'); ?></th>
		</tr>
	</thead>
	<tbody>
		<?php
		    $orderSubtotal = 0;
			$taxSubtotal = 0;

		 	$count = count($this->productsInfo);			
			for ($i = 0; $i < $count; $i++) {
				$product = $this->productsInfo[$i];

                $productWeight = isset($product->product_weight) ? $product->product_weight : 0;
                $lineWeightUOM = isset($product->product_weight_uom) ? $product->product_weight_uom : '';

				/* @var $product TableVmOrderItem */
				JFilterOutput::objectHTMLSafe($product,ENT_QUOTES,'product_attribute');

				//cleanup parameters
				$params = explode ("\n", preg_replace('/<\s*br\s*\/?\s*>/Uis', "\n", $product->product_attribute));
				foreach ($params as $key => $value)
					if (($value = JString::trim($value)))  //? what if json?
						$params[$key] = $value;
					else 
						unset($params[$key]);
					
				$attributes = trim(implode("\n", $params));

				$notFromVM = empty($product->product_id) ? ' class="hasTip" title="'.JText::_('COM_VMINVOICE_THIS_PRODUCT_IS_NOT_FROM_VIRTUEMART').'"' : '';
				?>
					
				<tr valign="top" class="row<?php echo ($i % 2); ?>"<?php if ($i == $count - 1) { ?> id="lastProduct"<?php } ?>>
              		<td><a href="javascript:void(0)" onclick="deleteProduct(this)" title="" class="deleteProduct"><?php echo JText::_('COM_VMINVOICE_DELETE'); ?></a></td>
        			<td<?php echo $notFromVM?>><input type="text" name="order_item_sku[<?php echo $i?>]" value="<?php echo $product->order_item_sku; ?>" class="fullWidth" /></td>
        			<td>
        				<input type="text" name="order_item_name[<?php echo $i?>]" value="<?php echo $product->order_item_name; ?>" class="fullWidth" /> 
						<input type="hidden" name="product_id[<?php echo $i?>]" value="<?php echo $product->product_id; ?>" />
						<input type="hidden" name="order_item_id[<?php echo $i?>]" value="<?php echo $product->order_item_id; ?>" />
					</td>
					<td align="center" valign="middle">
						<a id="show_attr_<?php echo $i; ?>" href="javascript:void(0)" onclick="show_attributes(<?php echo $i; ?>)" class="<?php if (trim($attributes," \n")=="") echo 'addProduct'; else echo 'editProduct'; ?>" title="<?php if (trim($attributes," \n")=="") echo JText::_('COM_VMINVOICE_ADD'); else echo JText::_('COM_VMINVOICE_EDIT'); ?>">
						<span class="unseen"><?php if (trim($attributes," \n")=="") echo JText::_('COM_VMINVOICE_ADD'); else echo JText::_('COM_VMINVOICE_EDIT'); ?></span></a>
					</td>
  					<td><?php echo JHTML::_('select.genericlist', $this->orderStatus, 'order_status['.$i.']', null, 'id', 'name', $product->order_status, 'status_'.$i); ?></td>
  					<td><?php echo JHTML::_('select.genericlist', $this->vendors, 'vendor_id['.$i.']', null, 'id', 'name', $product->vendor_id, 'vendor_id_'.$i); ?></td>
  					<td><input type="text" class="fullWidth" name="product_quantity[<?php echo $i?>]" value="<?php echo $product->product_quantity; ?>" /></td>					
                    <?php if ($this->showWeight) { ?>
                        <td>
                            <?php echo $productWeight * $product->product_quantity; ?> <?php echo $lineWeightUOM ?>
                            <input type="hidden" name="product_weight[<?php echo $i; ?>]" value="<?php echo $productWeight ?>" />
                            <input type="hidden" name="product_weight_uom[<?php echo $i; ?>]" value="<?php echo $lineWeightUOM ?>" />
                        </td>
                    <?php } ?>
					<?php if (COM_VMINVOICE_ISVM2) {?>
					<td nowrap="nowrap" align="center">
					<a href="javascript:void(0)" class="showRules" onclick="show_rules(<?php echo $i; ?>)" 
						title="<?php echo JText::_('COM_VMINVOICE_SHOW_CALC_RULES'); ?>"></a>
					</td>
					<?php } ?>
					<td nowrap="nowrap">
					<?php if (COM_VMINVOICE_ISVM2) {?>
						
						<?php if (!empty($product->product_priceWithoutTax) AND $product->product_discountedPriceWithoutTax != $product->product_priceWithoutTax) { ?>
							<span style="text-decoration:line-through"><?php echo round($product->product_item_price, $this->nbDecimal); ?></span> 
							<?php echo round($product->product_discountedPriceWithoutTax, $this->nbDecimal); ?>
						<?php } else {?>
							<?php echo round($product->product_item_price, $this->nbDecimal); ?>
						<?php }?>

						<input type="hidden" name="product_priceWithoutTax[<?php echo $i?>]" value="<?php echo $product->product_priceWithoutTax*1; ?>" />
						<input type="hidden" name="product_discountedPriceWithoutTax[<?php echo $i?>]" value="<?php echo $product->product_discountedPriceWithoutTax*1; ?>" />
						<input type="hidden" name="product_item_price[<?php echo $i?>]" value="<?php echo $product->product_item_price*1; ?>" />
					<?php } else {?>
						<input type="text" class="price" name="product_item_price[<?php echo $i?>]" value="<?php echo $product->product_item_price*1; ?>" />
					<?php } ?>
					</td>				
					<td nowrap="nowrap">
					<?php if (COM_VMINVOICE_ISVM2) {?>
						<?php echo $product->tax_rate_guessed ? round($product->tax_rate_guessed*100, 2).'%' : ''?>
						<input type="hidden" name="tax_rate_guessed[<?php echo $i?>]" value="<?php echo $product->tax_rate_guessed*1; ?>" id="tax_rate_guessed_<?php echo $i ?>" />
						<?php echo round($product->product_tax, $this->nbDecimal); ?>
						<input type="hidden" name="product_tax[<?php echo $i?>]" value="<?php echo $product->product_tax*1; ?>" id="product_tax_<?php echo $i ?>" />
					<?php } else {?>
						<?php echo JHTML::_('select.genericlist', $this->taxRates, 'tax_rate['.$i.']', null, 'value', 'name', $product->tax_rate, 'tax_rate_'.$i);  ?>
						<input type="text" class="price" name="product_tax[<?php echo $i?>]" value="<?php echo $product->product_tax*1; ?>" id="product_tax_<?php echo $i ?>"/>
					<?php } ?>
					</td>
					<td>
						<input type="hidden" name="product_price_with_tax[<?php echo $i?>]" value="<?php echo $product->product_price_with_tax; ?>" />
						<?php echo round($product->product_price_with_tax, $this->nbDecimal); ?>
					</td>
					
					<?php if (COM_VMINVOICE_ISVM2) {?>
					<td nowrap="nowrap">
						<?php echo round($product->product_price_discount, $this->nbDecimal) ?>
						<input type="hidden" name="product_price_discount[<?php echo $i?>]" value="<?php echo $product->product_price_discount; ?>" />
					</td>
					<?php }?>
					
					<td nowrap="nowrap" align="left"><?php echo round($product->subtotal, $this->nbDecimal); ?></td>
					<td nowrap="nowrap" align="left"><?php echo round($product->overall_tax, $this->nbDecimal); ?></td>
					<?php if (COM_VMINVOICE_ISVM2) {?>
					<td nowrap="nowrap" align="left">
						<?php echo round($product->product_price_discount * $product->product_quantity, $this->nbDecimal); ?>
					</td>
					<?php }?>
					<td nowrap="nowrap" align="left">
						<?php if (COM_VMINVOICE_ISVM2){?>
						<input type="hidden" name="product_subtotal_with_tax[<?php echo $i?>]" value="<?php echo $product->product_subtotal_with_tax; ?>" />
						<?php } ?>
						<?php echo round($product->total, $this->nbDecimal); ?>
					</td>
				</tr>
				<!-- attributes row -->
				<tr id="attr_<?php echo $i; ?>" style="display:<?php echo !empty($openedAttrs[$i]) ? 'table-row' : 'none'?>">
				<th valign="top"><?php echo JText::_('COM_VMINVOICE_PRODUCT_ATTRIBUTES'); ?>:</th>
					<td colspan="<?php echo (COM_VMINVOICE_ISVM2 ? 15 : 12) + $col; ?>">
					<input type="hidden" name="opened_attrs[<?php echo $i; ?>]" value="<?php echo !empty($openedAttrs[$i]) ? 1 : 0?>"> <!-- for passing opened attrs between ajax requests -->
					<?php 
					require dirname(__FILE__).'/products_attributes.php';
					?>
					</td>
				</tr>
				<?php if (COM_VMINVOICE_ISVM2) {?>
				<!-- calc rules row (VM2) -->
				<tr id="calc_rules_<?php echo $i; ?>" style="display:<?php echo !empty($openedRules[$i]) ? 'table-row' : 'none'?>">
				<th valign="top">
					<span class="hasTip" title="<?php echo JText::_('COM_VMINVOICE_CALC_RULES'); ?>" 
					rel="<?php echo JText::_('COM_VMINVOICE_SHOW_CALC_RULES_DESC'); ?>"><?php echo JText::_('COM_VMINVOICE_CALC_RULES'); ?>:</span></th>
				<td colspan="<?php echo 15 + $col; ?>">
				<?php 
				$calcRules = $product->calcRules;
				$basePrice = $product->product_item_price_init;
				$baseCurrency = $product->product_currency_init;
				$override = isset($product->override) ? $product->override : 0;
				$overridePrice = isset($product->product_override_price) ? $product->product_override_price : 0;
				require dirname(__FILE__).'/calc_rules.php';
				?>
				</td>
				</tr>
				<?php } ?>
		<?php 
			} 
		?>	  
	<!-- Subtotal row -->		
	<tr>
    	<td colspan="<?php echo (COM_VMINVOICE_ISVM2 ? 12 : 10) + $col; ?>" align="right" class="key"><?php echo JText::_('COM_VMINVOICE_SUBTOTAL'); ?>:</td>
    	<?php if (COM_VMINVOICE_ISVM2){?>
	    	<td class="hasTip" title="<?php echo JText::_('COM_VMINVOICE_SUBTOTAL'); ?>">
	    		<?php echo round($this->orderData->order_subtotal, $this->nbDecimal)?>
	    		<input type="hidden" name="order_subtotal" id="order_subtotal" value="<?php echo $this->orderData->order_subtotal*1?>">
	    	</td>
	    	<td class="hasTip" title="<?php echo JText::_('COM_VMINVOICE_TAX_TOTAL'); ?>">
	    		<?php echo round($this->orderData->order_tax, $this->nbDecimal)?>
	    		<input type="hidden" name="order_tax" value="<?php echo $this->orderData->order_tax*1?>">
	     	</td>
    	<?php } else {?>
	    	<td class="hasTip" title="<?php echo JText::_('COM_VMINVOICE_SUBTOTAL'); ?>">
	    		<input class="price" type="text" name="order_subtotal" id="order_subtotal" value="<?php echo $this->orderData->order_subtotal*1; ?>" />
	    	</td>
	    	<td class="hasTip" title="<?php echo JText::_('COM_VMINVOICE_TAX_TOTAL'); ?>">
	    		<input class="price" type="text" name="order_tax" id="order_tax" value="<?php echo $this->orderData->order_tax*1; ?>" />
	    	</td>
    	<?php } ?>
    	<?php if (COM_VMINVOICE_ISVM2){?>
    		<td>
    			<?php echo round($this->orderData->order_discountAmount, $this->nbDecimal)?>
    			<input type="hidden" name="order_discountAmount" value="<?php echo $this->orderData->order_discountAmount*1?>">
    		</td>
    	<?php } ?>
    	<td>
    		<?php echo round($this->orderData->order_salesPrice, $this->nbDecimal)?>
    		<?php if (COM_VMINVOICE_ISVM2){?>
    			<input type="hidden" name="order_salesPrice" id="order_salesPrice" value="<?php echo $this->orderData->order_salesPrice*1?>">
    		<?php } ?>
    	</td>
	</tr>
	<!-- Shipping and handling row -->
	<tr>
		<td colspan="2"></td>
		<td><?php echo JText::_('COM_VMINVOICE_SHIPPING_AND_HANDLING_FEE'); ?>:
		<?php if (COM_VMINVOICE_ISVM2 && isset($this->shippings[$this->orderData->shipment_method_id]))
				echo $this->shippings[$this->orderData->shipment_method_id]->name;
		?></td>
		<td colspan="<?php echo (COM_VMINVOICE_ISVM2 ? 4 : 3) + $col; ?>"></td>
		<td align="center">
		<?php if (COM_VMINVOICE_ISVM2) {?>
		<a href="javascript:void(0)" class="showRules" onclick="show_rules(-2)" title="<?php echo JText::_('COM_VMINVOICE_SHOW_CALC_RULES'); ?>"></a>
		<?php } ?>
		</td>
		<td>
		<?php if (COM_VMINVOICE_ISVM2) {?>
			<?php echo round($this->orderData->order_shipping, $this->nbDecimal) ?>
			<input type="hidden" name="order_shipping" id="order_shipping" value="<?php echo $this->orderData->order_shipping*1; ?>" />
		<?php } else {?>
			<input class="price" type="text" name="order_shipping" id="order_shipping" value="<?php echo $this->orderData->order_shipping*1; ?>" />
		<?php } ?>
		</td>
		<td>
		<?php if (COM_VMINVOICE_ISVM2) {?>
			<?php echo $this->orderData->order_shipping_taxrate ? ($this->orderData->order_shipping_taxrate*100).'%' : ''?>
			<?php echo round($this->orderData->order_shipping_tax, $this->nbDecimal) ?>
			<input type="hidden" name="order_shipping_tax" value="<?php echo $this->orderData->order_shipping_tax*1; ?>" id="order_shipping_tax"/>
		<?php } else {?>
			<?php echo JHTML::_('select.genericlist', $this->taxRates, 'order_shipping_taxrate', null, 'value', 'name', $this->orderData->order_shipping_taxrate);  ?>
			<input class="price" type="text" name="order_shipping_tax" value="<?php echo $this->orderData->order_shipping_tax*1; ?>" id="order_shipping_tax"/>
		<?php } ?>
		</td>
		<td><?php echo round($this->orderData->order_shipping + $this->orderData->order_shipping_tax, $this->nbDecimal); ?></td>
		<?php if (COM_VMINVOICE_ISVM2){?>
		<td></td>
		<?php } ?>
		<td><?php echo round($this->orderData->order_shipping, $this->nbDecimal); ?></td>
		<td><?php echo round($this->orderData->order_shipping_tax, $this->nbDecimal); ?></td>
		<?php if (COM_VMINVOICE_ISVM2){?>
		<td></td>
		<?php } ?>
		<td><?php echo round($this->orderData->order_shipping + $this->orderData->order_shipping_tax, $this->nbDecimal); ?></td>
	</tr>	
	<?php if (COM_VMINVOICE_ISVM2) {
	$i = -2;?>
	<!-- calc rules row (VM2) -->
	<tr id="calc_rules_<?php echo $i; ?>" style="display:<?php echo !empty($openedRules[$i]) ? 'table-row' : 'none'?>">
	<th valign="top"><?php echo JText::_('COM_VMINVOICE_CALC_RULES'); ?>:</th>
	<td colspan="<?php echo 15 + $col; ?>">
	<?php 
	$calcRules = $this->orderData->shippingCalcRules;
	$basePrice = $this->orderData->order_shipping_init;
	$baseCurrency = $this->orderData->order_currency_init;
	require dirname(__FILE__).'/calc_rules.php';
	?>
	</td>
	</tr>
	<?php }?>		
	<?php if (COM_VMINVOICE_ISVM2) {?>
	<!-- Payment fee/discount row (VM2) -->
	<tr>
		<td colspan="2"></td>
		<td><?php echo JText::_('COM_VMINVOICE_PAYMENT_FEE_OR_DISCOUNT'); ?>:
		<?php if (isset($this->payments[$this->orderData->payment_method_id]))
			echo $this->payments[$this->orderData->payment_method_id]->name;
		?></td>
		<td align="center"></td>
		<td colspan="<?php echo 3 + $col; ?>">
		</td>
		<td align="center"><a href="javascript:void(0)" class="showRules" onclick="show_rules(-3)" title="<?php echo JText::_('COM_VMINVOICE_SHOW_CALC_RULES'); ?>"></a></td>
		<td>
			<?php echo round($this->orderData->order_payment, $this->nbDecimal) ?>
			<input type="hidden" name="order_payment" id="order_payment" value="<?php echo $this->orderData->order_payment*1; ?>" />
		</td>
		<td>
			<?php echo round($this->orderData->order_payment_tax, $this->nbDecimal) ?>
			<?php echo $this->orderData->order_payment_taxrate ? ($this->orderData->order_payment_taxrate*100).'%' : ''?>
			<input type="hidden" name="order_payment_tax" value="<?php echo $this->orderData->order_payment_tax*1; ?>" id="order_payment_tax"/>
		</td>
		<td><?php echo round($this->orderData->order_payment + $this->orderData->order_payment_tax, $this->nbDecimal); ?></td>
		<td></td>
		<td><?php echo round($this->orderData->order_payment, $this->nbDecimal); ?></td>
		<td><?php echo round($this->orderData->order_payment_tax, $this->nbDecimal); ?></td>
		<td></td>
		<td><?php echo round($this->orderData->order_payment + $this->orderData->order_payment_tax, $this->nbDecimal); ?></td>
	</tr>
	<?php $i = -3;?>
	<!-- calc rules row (VM2) -->
	<tr id="calc_rules_<?php echo $i; ?>" style="display:<?php echo !empty($openedRules[$i]) ? 'table-row' : 'none'?>">
	<th valign="top"><?php echo JText::_('COM_VMINVOICE_CALC_RULES'); ?>:</th>
	<td colspan="<?php echo 15 + $col; ?>">
	<?php 
	$calcRules = $this->orderData->paymentCalcRules;
	$basePrice = $this->orderData->order_payment_init;
	$baseCurrency = $this->orderData->order_currency_init;
	require dirname(__FILE__).'/calc_rules.php';
	?>
	</td>
	</tr>
	<?php } ?>
	</tbody>
</table>	
<div class="leftCol50">
    <h2 class="addProduct"><?php echo JText::_('COM_VMINVOICE_ADD_PRODUCT'); ?></h2>
	<table class="adminlist" cellspacing="1">
		<tbody>
			<tr>
				<th><?php echo JText::_('COM_VMINVOICE_NAME'); ?>
				<?php if ($this->newproduct_object /*VM2*/){ ?>
				<span style="font-weight:normal" id="newproduct_name" class="newproduct_options"><?php echo rtrim(': '.$this->newproduct_object->product_name,' :') ?></span>
				<a href="javascript:void(0)" style="float:right;font-weight:normal" 
					onclick="$('newproduct_search').style.display='block';$('newproduct_id').value='';$$('.newproduct_options').each(function(el){el.destroy();});this.destroy();"><?php echo JText::_('COM_VMINVOICE_DISCARD');?></a>
				<?php }?>
				</th> 
				<th width="1%"><?php echo JText::_('COM_VMINVOICE_ACTION'); ?></th>
			</tr>
			<tr>
				<td>
					<input type="hidden" id="newproduct_id" name="newproduct_id" value="<?php echo $this->newproduct_id?>" />
					
					<div id="newproduct_search" style="display:<?php echo $this->newproduct_id ? 'none' : 'block' ?>">
					<input type="text" id="newproduct" name="newproduct" class="fullWidth" autocomplete="off" onkeyup="generateWhisper(this, event,'<?php echo JURI::base(); ?>');" onkeydown="moveWhisper(this, event);" />
					<br />
					<div id="newproductwhisper"></div>
					</div>
					
					<?php if (COM_VMINVOICE_ISVM1 AND $this->newproduct_id) { /* VM1: product selected before: now select price */?>
					<div class="newproduct_options"><?php echo $this->newproduct_name ?>
					<a href="javascript:void(0)" 
						onclick="$('newproduct_search').style.display='block';$('newproduct_id').value='';$$('.newproduct_options').each(function(el){el.destroy();});"><?php echo JText::_('COM_VMINVOICE_DISCARD');?></a>
					
					<span style="float:right">
					<?php echo JText::_('COM_VMINVOICE_PRICE'); ?>: 
					<?php echo JHTML::_('select.genericlist', $this->productPrices, 'newproduct_price', null, 'value', 'text', JRequest::getVar('pprice'));  ?>
					</span>
					</div>
					<?php } elseif (COM_VMINVOICE_ISVM2) { //VM2: select new product params ?>
					
					
					<?php if ($this->showQuantitySelect) {?>
					<span style="float:right" class="newproduct_options">
					<?php echo JText::_('COM_VMINVOICE_QUANTITY'); ?>: 
					<input type="text" size="5" name="newproduct_quantity" id="newproduct_quantity" value="<?php echo JRequest::getInt('pquantity', 1) ?>" />
					</span>
					<?php } elseif (!is_null(JRequest::getVar('pquantity'))) { //else we can be selecting variant now. pre-selected price must stay in hdden field.?>
					<input type="hidden" name="newproduct_quantity" id="newproduct_quantity" value="<?php echo JRequest::getInt('pquantity',0 ) //0! ?>" />
					<?php } ?>
					
					<?php if ($this->productPrices) {?>
					<span style="float:right" class="newproduct_options">
					<?php echo JText::_('COM_VMINVOICE_PRICE'); ?>: 
					<?php echo JHTML::_('select.genericlist', $this->productPrices, 'newproduct_price', null, 'value', 'text', $this->productPriceSelected);  ?>
					</span>
					<?php } elseif (!is_null(JRequest::getVar('pprice'))) { //else, if we know it, must stay in hidden field.?>
					<input type="hidden" name="newproduct_price" id="newproduct_price" value="<?php echo JRequest::getVar('pprice') ?>" />
					<?php } ?>
					
					<?php if (!is_null(JRequest::getVar('pquantity'))){ //display pre-selected quantity ?>
					<span style="float:right;padding-right:10px;" class="newproduct_options">
					<?php echo JText::_('COM_VMINVOICE_QUANTITY'); ?>: <?php echo JRequest::getVar('pquantity')?>
					</span>
					<?php } ?>
					
					<?php if ($this->newproduct_object) { //display custom fields for new product?>
					<div class="newproduct_options">
					
					<?php if (!empty($this->newproduct_object->customsChilds)) { //??>
					<table>
					<?php foreach ($this->newproduct_object->customsChilds as $field){?>
					<tr><td>
					<label title="<?php echo JText::_($field->field->custom_value) ?>"><?php echo JText::_($field->field->custom_title) ?>: </label>
					</td><td>
					<?php echo $field->display ?>
					</td></tr>
					<?php } ?>
					</table>
					<?php } ?>
	
					<?php if (JRequest::getVar('customPrice') OR JRequest::getVar('customPlugin')){ //already specified before, pass to next page
					
						echo $this->hiddenFieldsFromVar(JRequest::getVar('customPrice'), 'customPrice');
						echo $this->hiddenFieldsFromVar(JRequest::getVar('customPlugin'), 'customPlugin');
						
					} else {?>
					<table>
					<?php if (!empty($this->newproduct_object->customfieldsCart)) foreach ($this->newproduct_object->customfieldsCart as $field){?>
					<tr><td>
					<label title="<?php echo trim(JText::_($field->custom_tip).' '.JText::_($field->custom_field_desc)) ?>"><?php echo JText::_($field->custom_title) ?>: </label>
					</td><td>
					<?php echo $field->display ?>
					</td></tr>
					<?php } ?>
					</table>
					<?php } ?>
					</div>
					<?php } ?>
					<?php } ?>
	            </td>
	          	<td align="center">
	              	<a href="javascript:showOrderData('<?php echo addslashes(JURI::base()); ?>',true,false,false,false)" title="<?php echo JText::_('COM_VMINVOICE_ADD_PRODUCT'); ?>" class="addProduct" id="addProductIcon"><span class="unseen"><?php echo JText::_('COM_VMINVOICE_ADD'); ?></span></a>
	          	</td>
			</tr>
		</tbody>
	</table>
</div>
<div class="rightCol30">
	<table class="admintable" cellspacing="1">
	  		<tbody>
	  			<?php if (COM_VMINVOICE_ISVM1) {?>
				<tr> 
					<td class="key autoWidth" nowrap="nowrap"><?php echo JText::_('COM_VMINVOICE_PAYMENT_FEE_OR_DISCOUNT'); ?></td>
					<td width="1%"><?php echo round($this->orderData->order_payment, 4); ?>
					<input type="hidden" name="order_payment" id="order_payment" value="<?php echo ($this->orderData->order_payment*1); ?>" />
					</td>
	  			</tr>
	 			<tr> 
					<td class="key autoWidth" nowrap="nowrap"><?php echo JText::_('COM_VMINVOICE_OTHER_FEE_OR_DISCOUNT'); ?></td>
					<td width="1%"><input type="text" name="order_discount" id="order_discount" value="<?php echo ($this->orderData->order_discount*1); ?>" /></td>
	  			</tr>
	  			<?php } else { ?>
	  			<!-- Calculation rules for order (VM2) -->
	  			<tr> 
	  				<td colspan="2" class="key"><b><?php echo JText::_('COM_VMINVOICE_OTHER_FEE_OR_DISCOUNT'); ?>: </b></td>
	  			<tr> 
	  			<tr>
	  				<td colspan="2">
		  				<?php 
		  				$i=-1;
						$calcRules = $this->orderData->orderCalcRules;
						require dirname(__FILE__).'/calc_rules.php';
						?>
	  				</td>
	  			</tr>
	  			<?php } ?>
	 			<tr> 
					<td class="key autoWidth" nowrap="nowrap"><?php echo JText::_('COM_VMINVOICE_COUPON_DISCOUNT'); ?></td>
					<td width="1%"><input type="text" name="coupon_discount" id="coupon_discount" value="<?php echo round($this->orderData->coupon_discount, $this->nbDecimal); ?>" /></td>
	  			</tr>
				<tr>
					<td class="key autoWidth" nowrap="nowrap"><?php echo JText::_('COM_VMINVOICE_TOTAL'); ?></td>
					<td width="1%">
						<?php if (COM_VMINVOICE_ISVM2) {?>
						<?php echo round($this->orderData->order_total, $this->nbDecimal); ?>
						<input type="hidden" name="order_total" id="order_total" value="<?php echo $this->orderData->order_total*1 ?>" />
						<?php } else {?>
						<input type="text" name="order_total" id="order_total" value="<?php echo round($this->orderData->order_total, $this->nbDecimal); ?>" />
						<?php } ?>
					</td>
				</tr>
                <?php if ($this->showWeight) { ?>
                    <tr>
                        <td class="key autoWidth" nowrap="nowrap"><?php echo JText::_('COM_VMINVOICE_TOTAL_WEIGHT'); ?></td>
                        <td width="1%"><?php echo implode(' ', $this->orderWeight) ?></td>
                    </tr>
                <?php } ?>
			</tbody>
	</table>
	<a class="refreshOrder" id="refreshOrderIcon" href="javascript:showOrderData('<?php echo addslashes(JURI::base()); ?>',false,false,false,false)" title=""><?php echo JText::_('COM_VMINVOICE_REFRESH_AND_RECALCULATE'); ?></a>
</div>
<div class="clr"></div>
<?php if (! $this->orderajax) { ?></div><?php } ?>