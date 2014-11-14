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

$openedRules = JRequest::getVar('opened_rules', array(), 'post', 'array');
$basePrices= JRequest::getVar('calc_baseprice', array(), 'post', 'array');
$baseCurrencies = JRequest::getVar('calc_basecurrency', array(), 'default', 'array');
$overrides = JRequest::getVar('calc_override', array(), 'post', 'array');
$overridesPrices= JRequest::getVar('calc_override_price', array(), 'post', 'array');

//get calc kind options
if (!isset($itemKinds) OR !isset($generalKinds)){
	$lang = JFactory::getLanguage();
	$lang->load('com_virtuemart', JPATH_ADMINISTRATOR);
	
	//for some reason, in order general rules they are named differently than in rules table!!
	
	$itemKinds = array(
			'Marge' => JText::_('COM_VIRTUEMART_CALC_EPOINT_PMARGIN'),
			'DBTax' => JText::_('COM_VIRTUEMART_CALC_EPOINT_DBTAX'), 
			'Tax' => JText::_('COM_VIRTUEMART_CALC_EPOINT_TAX'),
			'VatTax' => JText::_('COM_VIRTUEMART_CALC_EPOINT_VATTAX'), 
			'DATax' => JText::_('COM_VIRTUEMART_CALC_EPOINT_DATAX'));
	
	//general rules are named differently than in basic rules table
	$generalKinds = array(
			'DATaxRulesBill' => JText::_('COM_VIRTUEMART_CALC_EPOINT_DBTAXBILL'), 
			'taxRulesBill' => JText::_('COM_VIRTUEMART_CALC_EPOINT_TAXBILL'),
			'DATaxBill' => JText::_('COM_VIRTUEMART_CALC_EPOINT_DATAXBILL'));
}

//one template for calculation rules table. its shown at product line, shipment and paymnet (and possibly whole order?)

//passed vars:
//$calcRules calc rules to show
//$i: first dimension  >=0: products, -1: whole order, -2: shipping, -3: payment
//$basePrice: 	//price used as base for re-calculation of price. all rules are applied on it. it should be base product price.
//$baseCurrency //currency $basePrice is in
//$overridePrice
//$override
?>

<input type="hidden" name="opened_rules[<?php echo $i; ?>]" value="<?php echo !empty($openedRules[$i]) ? 1 : 0?>"> <!-- for passing opened rules between ajax requests -->
<table id="rules_table_<?php echo $i; ?>">
<tbody>
<?php 

//get mathop options
$options = array();
foreach ($calcRules as $rule) //for forward compatibility
	$options[] = $rule->calc_mathop;
$options = array_unique(array_merge(array('-', '+', '-%', '+%'), $options));
$options = array_combine($options, $options);

//get calc kind options

$kinds = $i>=0 ? $itemKinds : $generalKinds; //pick calc kinds based on what we are displaying

foreach ($calcRules as $rule) //for forward compatibility, if some will be added.
	if (!isset($kinds[$rule->calc_kind]))
		$kinds[$rule->calc_kind] = $rule->calc_kind;
?>
<?php $j=0; foreach ($calcRules as $rule) {?>
<tr>
<td <?php if ($i==-1) echo 'style="display:none"'?> title="<?php echo JText::_('COM_VMINVOICE_CALC_RULE_MATHOP')?>">
<input type="hidden" name="virtuemart_order_calc_rule_id[<?php echo $i ?>][<?php echo $j ?>]" value="<?php echo $rule->virtuemart_order_calc_rule_id ?>" />
<?php echo JHTML::_('select.genericlist', $options, "calc_mathop[$i][$j]", null, 'id', 'name', $rule->calc_mathop);  ?>
</td>
<td <?php if ($i==-1) echo 'style="display:none"'?> title="<?php echo JText::_('COM_VMINVOICE_CALC_RULE_VALUE')?>">
<input type="text" size="4" name="calc_value[<?php echo $i ?>][<?php echo $j ?>]" value="<?php echo $rule->calc_value*1 ?>" style="text-align:center" />
</td>
<td>
<?php if ($i!=-1){ //for some reason, general rules does not have currency specified ?> 
<?php echo JHTML::_('select.genericlist', $this->currencies, "calc_currency[$i][$j]", null, 'id', 'name', $rule->calc_currency);  ?>
<?php } else {  ?>
<input type="hidden" name="calc_currency[<?php echo $i ?>][<?php echo $j ?>]" value="<?php echo $rule->calc_currency?>">
<?php } ?>
</td>
<td title="<?php echo JText::_('COM_VMINVOICE_CALC_RULE_NAME')?>">
<input type="text" size="30" name="calc_rule_name[<?php echo $i ?>][<?php echo $j ?>]" value="<?php echo htmlspecialchars($rule->calc_rule_name) ?>"/>
</td>
<td title="<?php echo JText::_('COM_VMINVOICE_CALC_RULE_KIND')?>">
<?php if ($i==-2 OR $i==-3){ //shipping and payment has fixed kind "shipping" and "payment" ?>
<input type="hidden" name="calc_kind[<?php echo $i ?>][<?php echo $j ?>]" value="<?php echo $rule->calc_kind?>">
<?php } else {?>
<?php echo JHTML::_('select.genericlist', $kinds, "calc_kind[$i][$j]", null, 'id', 'name', $rule->calc_kind);  ?>
<?php } ?>
</td>
<td title="<?php echo JText::_('COM_VMINVOICE_CALC_RULE_AMOUNT')?>">
<?php if ($i==-1){ //general rules DOES have amount specified :-/?>
<input type="text" size="7" name="calc_amount[<?php echo $i ?>][<?php echo $j ?>]" value="<?php echo $rule->calc_amount*1 ?>"/>
<?php } ?>
</td>
<?php if ($i==-1){ //general rules no up/down move ?>
<td width="1%"><a href="javascript:void(0)" onclick="this.getParent('tr').dispose();"><img src="components/com_vminvoice/assets/images/icon-16-delete.png"></a></td>
<?php } else {?>
<td width="100">
	<a href="javascript:void(0)" onclick="this.getParent('tr').dispose();"><img src="components/com_vminvoice/assets/images/icon-16-delete.png"></a> 
	<a href="javascript:void(0);" onclick="vmiRows.moveRowUp(this.getParent('tr'))"><?php echo JText::_('COM_VMINVOICE_UP')?></a> 
	<a href="javascript:void(0);" onclick="vmiRows.moveRowDown(this.getParent('tr'))"><?php echo JText::_('COM_VMINVOICE_DOWN')?></a> 
</td>
<?php } ?>
</tr>
<?php $j++; } ?>
</tbody>

<tfoot style="display:none">
<tr>
<td <?php if ($i==-1) echo 'style="display:none"'?> title="<?php echo JText::_('COM_VMINVOICE_CALC_RULE_MATHOP')?>">
<input type="hidden" name="virtuemart_order_calc_rule_id(<?php echo $i ?>)_model" value="" />
<?php echo JHTML::_('select.genericlist', $options, "calc_mathop($i)_model", null, 'id', 'name', '+%');  ?>
</td>
<td <?php if ($i==-1) echo 'style="display:none"'?> title="<?php echo JText::_('COM_VMINVOICE_CALC_RULE_VALUE')?>">
<input type="text" size="4" name="calc_value(<?php echo $i ?>)_model" value="0" style="text-align:center" />
</td>
<td>
<?php if ($i!=-1){ //for some reason, general rules does not have currency specified ?> 
<?php echo JHTML::_('select.genericlist', $this->currencies, "calc_currency($i)_model", null, 'id', 'name', $this->orderData->order_currency);  ?>
<?php } else {  ?>
<input type="hidden" name="calc_currency(<?php echo $i ?>)_model" value="<?php echo $this->orderData->order_currency ?>">
<?php } ?>
</td>
<td title="<?php echo JText::_('COM_VMINVOICE_CALC_RULE_NAME')?>">
<input type="text" size="30" name="calc_rule_name(<?php echo $i ?>)_model" value=""/>
</td>
<td title="<?php echo JText::_('COM_VMINVOICE_CALC_RULE_KIND')?>">
<?php if ($i==-2 OR $i==-3){ //shipping and payment has fixed kind "shipping" and "payment" ?>
<input type="hidden" name="calc_kind(<?php echo $i ?>)_model" value="<?php echo $i==-2 ? 'shipment' : 'payment'?>">
<?php } else {?>
<?php echo JHTML::_('select.genericlist', $kinds, "calc_kind($i)_model", null, 'id', 'name');  ?>
<?php } ?>
</td>
<td title="<?php echo JText::_('COM_VMINVOICE_CALC_RULE_AMOUNT')?>">
<?php if ($i==-1){ //general rules DOES have amount specified :-/?>
<input type="text" size="7" name="calc_amount(<?php echo $i ?>)_model" value="0"/>
<?php } ?>
</td>
<?php if ($i==-1){ //general rules no up/down move ?>
<td width="1%"><a href="javascript:void(0)" onclick="this.getParent('tr').dispose();"><img src="components/com_vminvoice/assets/images/icon-16-delete.png"></a></td>
<?php } else {?>
<td width="100">
	<a href="javascript:void(0)" onclick="this.getParent('tr').dispose();"><img src="components/com_vminvoice/assets/images/icon-16-delete.png"></a>
	<a href="javascript:void(0);" onclick="vmiRows.moveRowUp(this.getParent('tr'))"><?php echo JText::_('COM_VMINVOICE_UP')?></a> 
	<a href="javascript:void(0);" onclick="vmiRows.moveRowDown(this.getParent('tr'))"><?php echo JText::_('COM_VMINVOICE_DOWN')?></a> 
</td>
<?php }?>
</tr>
</tfoot>
</table>

<a href="javascript:void(0)" onclick="vmiRows.addNewRow('rules_table_<?php echo $i; ?>');"><?php echo JText::_('COM_VMINVOICE_ADD_NEW_MODIFIER'); ?></a>

<?php if ($i!=-1){?>

<br>
<label style="font-weight:bold" class="hasTip" 
	title="<?php echo JText::_('COM_VMINVOICE_COST_PRICE')?>" rel="<?php echo JText::_('COM_VMINVOICE_COST_PRICE_DESC')?>"><?php echo JText::_('COM_VMINVOICE_COST_PRICE')?>: 
<input type="text" size="10" name="calc_baseprice[<?php echo $i; ?>]" value="<?php echo isset($basePrices[$i]) ? $basePrices[$i] : $basePrice*1; ?>" />
</label>

<?php echo JHTML::_('select.genericlist', $this->currencies, "calc_basecurrency[$i]", null, 'id', 'name', isset($baseCurrencies[$i]) ? $baseCurrencies[$i] : $baseCurrency);  ?>


<?php if ($i>=0){ //override setting only for products ?>

<label class="hasTip" title="<?php echo InvoiceHelper::frontendTranslate('COM_VIRTUEMART_PRODUCT_DISCOUNT_OVERRIDE', null, false, 'com_virtuemart')?>" 
	rel="<?php echo InvoiceHelper::frontendTranslate('COM_VIRTUEMART_PRODUCT_DISCOUNT_OVERRIDE_TIP', null, false, 'com_virtuemart')?>"
 for="calc_override_price_<?php echo $i ?>" style="font-weight:bold"><?php echo InvoiceHelper::frontendTranslate('COM_VIRTUEMART_PRODUCT_DISCOUNT_OVERRIDE', null, false, 'com_virtuemart')?></label>

<?php 
$selectedOverride = isset($overrides[$i]) ? $overrides[$i] : $override;
?>

<input type="text" size="10" name="calc_override_price[<?php echo $i; ?>]" value="<?php echo isset($overridesPrices[$i]) ? $overridesPrices[$i] : $overridePrice ?>" id="calc_override_price_<?php echo $i ?>" 
	style="display:<?php echo $selectedOverride==0 ? 'none' : 'inline'?>!important"/>
	
<label>
<input type="radio" name="calc_override[<?php echo $i; ?>]" value="0" <?php if ($selectedOverride==0) echo 'checked' ?> 
	onclick="$('calc_override_price_<?php echo $i ?>').setAttribute('style', 'display:none!important');">
<?php echo InvoiceHelper::frontendTranslate('COM_VIRTUEMART_DISABLED', null, false, 'com_virtuemart')?>
</label>

<label>
<input type="radio" name="calc_override[<?php echo $i; ?>]" value="1" <?php if ($selectedOverride==1) echo 'checked' ?>
	onclick="$('calc_override_price_<?php echo $i ?>').setAttribute('style', 'display:inline!important');">
<?php echo InvoiceHelper::frontendTranslate('COM_VIRTUEMART_OVERWRITE_FINAL', null, false, 'com_virtuemart')?>
</label>

<label>
<input type="radio" name="calc_override[<?php echo $i; ?>]" value="-1" <?php if ($selectedOverride==-1) echo 'checked' ?>
	onclick="$('calc_override_price_<?php echo $i ?>').setAttribute('style', 'display:inline!important');">
<?php echo InvoiceHelper::frontendTranslate('COM_VIRTUEMART_OVERWRITE_PRICE_TAX', null, false, 'com_virtuemart')?>
</label>

<?php } ?>

<input type="button" style="font-weight:bold" 
	onclick="javascript:showOrderData('<?php echo addslashes(JURI::base()); ?>',false,false,false,<?php echo $i?>)" 
	class="button btn hasTip" value="<?php echo JText::_('COM_VMINVOICE_RECOMPUTE_PRICE_BY_RULES')?> &raquo; " 
	title="<?php echo JText::_('COM_VMINVOICE_RECOMPUTE_PRICE_BY_RULES')?> " rel="<?php echo JText::_('COM_VMINVOICE_RECOMPUTE_PRICE_BY_RULES_DESC')?>"/>

<span id="ajaxLoaderRules<?php echo $i?>" style="display:none;padding:20px;background:url(<?php echo JURI::base()?>components/com_vminvoice/assets/images/ajax-loader-white.gif) no-repeat left center">&nbsp;</span>

<?php } ?>
		