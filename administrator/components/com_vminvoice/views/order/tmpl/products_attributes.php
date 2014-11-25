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

?>
<?php if (is_array($decoded = json_decode($attributes, true))){	?>

	<div class="attributes_info"><?php echo JText::_('COM_VMINVOICE_ATTRIBUTES_EDIT_INFO')?></div>

	<table width="100%" id="attributes_<?php echo $i?>" style="table-layout:fixed">
	<tbody>
	<?php $j=0; foreach ($decoded as $key => $val){ 
		$input = $this->getAttributeInput($key, $val, $i, $j);
	?>
	<tr>
		<td width="40"><input class="fullWidth hasTip" name="product_attribute_key[<?php echo $i?>][<?php echo $j?>]" type="<?php echo $input ? 'hidden' : 'text'?>" size="2" value="<?php echo $key ?>" title="<?php echo JText::_('COM_VMINVOICE_ATTRIBUTE_KEY'); ?>" /></td>
	
	<?php if ($input) {//we can display directly input for this attribute ?>
		<td width="150" colspan="2" title="<?php echo JText::_($input->custom_title) ?>" rel="<?php echo JText::_($input->custom_tip) ?>" class="hasTip">
		<?php echo JText::_($input->custom_title) ?>
		</td>
		<td><?php echo $input->display ?></td>
	<?php } elseif (is_string($val) AND preg_match('#^\s*<span class="c[ou]stumTitle">(.*)</span>\s*<span class="c[ou]stumValue" >(.*)</span>\s*$#is', $val, $matches)){ //base title: value format
	
		$valueName = htmlspecialchars($matches[1]);
		$titleName = JText::_('COM_VMINVOICE_ATTRIBUTE_NAME');
		if (($relName = InvoiceHelper::frontendTranslate($valueName, $langTag, false, 'com_virtuemart'))==$valueName) //if used lang tag, show also translation in tooltip
			$relName = '';	
		$valueValue = htmlspecialchars($matches[2]);
		$titleValue = JText::_('COM_VMINVOICE_ATTRIBUTE_VALUE');
		if (($relValue = InvoiceHelper::frontendTranslate($valueValue, $langTag, false, 'com_virtuemart'))==$valueValue)
			$relValue = '';
		?>
		<td width="150" colspan="2"><input class="fullWidth hasTip" name="product_attribute_name[<?php echo $i?>][<?php echo $j?>]" type="text" size="25" value="<?php echo $valueName ?>" title="<?php echo $titleName ?>" rel="<?php echo $relName ?>"/></td>
		<td><input class="fullWidth hasTip" name="product_attribute_value[<?php echo $i?>][<?php echo $j?>]" type="text" size="60" value="<?php echo $valueValue ?>" title="<?php echo $titleValue ?>" rel="<?php echo $relValue ?>" /></td>
	
	<?php } elseif (is_array($val) AND count($val)==1 AND is_array(reset($val)) AND count(reset($val))==1) { //support for simple plugin format (only one value) 
		
		$key1 = htmlspecialchars(key($val));
		$key2 = htmlspecialchars(key(reset($val)));
		$val2 = htmlspecialchars(reset(reset($val)));
		if (($titlekey1 = InvoiceHelper::frontendTranslate($key1, $langTag, false, 'com_virtuemart'))==$key1) //if used lang tag, show also translation in tooltip
			$titlekey1 = '';
		if (($titlekey2 = InvoiceHelper::frontendTranslate($key2, $langTag, false, 'com_virtuemart'))==$key2) //if used lang tag, show also translation in tooltip
			$titlekey2 = '';
		if (($relval2 = InvoiceHelper::frontendTranslate($val2, $langTag, false, 'com_virtuemart'))==$val2) //if used lang tag, show also translation in tooltip
			$relval2 = '';
		?>
		
		<td width="75"><input class="fullWidth" name="product_attribute_plugin_key[<?php echo $i?>][<?php echo $j?>]" type="text" size="25" value="<?php echo $key1 ?>" title="<?php echo $titlekey1 ?>" /></td>
		<td width="75"><input class="fullWidth" name="product_attribute_plugin_key2[<?php echo $i?>][<?php echo $j?>]" type="text" size="25" value="<?php echo $key2 ?>" title="<?php echo $titlekey2 ?>" /></td>
		<td><input class="fullWidth hasTip" name="product_attribute_plugin_val2[<?php echo $i?>][<?php echo $j?>]" type="text" size="25" value="<?php echo $val2 ?>" title="<?php echo JText::_('COM_VMINVOICE_ATTRIBUTE_VALUE')?>" rel="<?php echo $relval2 ?>"/></td>
	
	<?php } else { //else put everything else as json.. ?>
		<td colspan="3">
		<input class="fullWidth" name="product_attribute_textvalue[<?php echo $i?>][<?php echo $j?>]" type="text" size="25" value="<?php echo htmlspecialchars(is_string($val) ? $val : json_encode($val)) ?>" title="<?php echo JText::_('COM_VMINVOICE_ATTRIBUTE_JSON_VALUE')?>"/>
		</td>
	<?php } ?>
		<td width="20"><a href="javascript:void(0)" onclick="this.getParent('tr').dispose();"><img src="components/com_vminvoice/assets/images/icon-16-delete.png"></a></td>
	</tr>
	<?php $j++; } ?>
	</tbody>
	<tfoot style="display:none">
	<tr>
	<td width="40"><input class="fullWidth" name="product_attribute_key(<?php echo $i?>)_model" type="text" size="2" value="" title="<?php echo JText::_('COM_VMINVOICE_ATTRIBUTE_KEY')?>" /></td>
	<td width="150" colspan="2"><input class="fullWidth" name="product_attribute_name(<?php echo $i?>)_model" type="text" size="25" value="" title="<?php echo JText::_('COM_VMINVOICE_ATTRIBUTE_NAME')?>" /></td>
	<td><input class="fullWidth" name="product_attribute_value(<?php echo $i?>)_model" type="text" size="60" value="" title="<?php echo JText::_('COM_VMINVOICE_ATTRIBUTE_VALUE')?>" /></td>
	<td width="1%"><a href="javascript:void(0)" onclick="this.getParent('tr').dispose();"><img src="components/com_vminvoice/assets/images/icon-16-delete.png"></a></td>
	</tr>
	</tfoot>
	</table>
	<a href="javascript:void(0)" onclick="vmiRows.addNewRow('attributes_<?php echo $i?>')"><?php echo JText::_('COM_VMINVOICE_ADD_ATTRIBUTE')?></a>
<?php } else {?>
	<textarea rows="3" cols="30" class="fullWidth" name="product_attribute[<?php echo $i?>]" id="attrs_<?php echo $i; ?>"><?php echo $attributes; ?></textarea>
<?php } ?>