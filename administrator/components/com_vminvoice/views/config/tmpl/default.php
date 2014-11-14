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

defined('_JEXEC') or die('Restrict Access');

jimport('joomla.filter.output');
JHTML::_('behavior.tooltip');

if (COM_VMINVOICE_ISJ30)
	JHtml::_('formbehavior.chosen', 'select[size="1"], select:not([size])');

$conf = array(); //tinymce config
$conf['table']=1;
$conf['newlines']=1;
$conf['autosave']=0;
$conf['fonts']=0;
$conf['media']=0;
$conf['insertdate']=0;
$conf['inserttime']=0;
$conf['style']=0;
$conf['layer']=0;
$conf['xhtmlxtras']=0;
$conf['citation']=0;
$conf['paste']=0;
$conf['blockquote']=0;
$conf['invalid_elements']='citation';
$conf['searchreplace']=0;
$conf['smilies']=0;
$conf['directionality']=0;
$conf['colors']=0;
$conf['contextmenu']=0;

$editor =  JFactory::getEditor();
//load editor in externded mode (to display table edit buttons)
//we must somehow display editor on page to have JS insert functions defined because we need them by backgroud image picker
//also it loads editor with defined $conf (which is importatant to display table edits)
//J3?
if (COM_VMINVOICE_ISJ16){
	$conf['mode']=2; //same as extended
	echo '<span style="display:none">'.$editor->display('dummy_editor', '', '10', '10', '1', '1', true, null, null, null, $conf).'</span>';
}
else {
	$conf['mode']='extended';
	echo '<span style="display:none">'.$editor->display('dummy_editor', '', '10', '10', '1', '1', true, $conf).'</span>';
}

//fix for tinyMCE in Joomla3 - remove hard-coded size from init function, else all editors will have same size
$doc = JFactory::getDocument();
if (!empty($doc->_custom))
	foreach ($doc->_custom as $k => $txt)
		if (stripos($txt, 'tinyMCE.init')!==false)
			$doc->_custom[$k] = preg_replace('#([\s,])(height|width)\s*:\s*"[^"]*"\s*,?#is', '$1', $txt);

?>

 <script language="javascript" type="text/javascript">

 	<?php if (COM_VMINVOICE_ISJ16) { ?>
	Joomla.submitbutton = function (pressbutton) {
	<?php } else {?>
	function submitbutton (pressbutton) {
	<?php }?>
		var form = document.adminForm;
		if (pressbutton == 'cancel') {
			submitform( pressbutton );
			return;
		}		
		// do field validation (removed)

		<?php if (COM_VMINVOICE_ISJ16) { ?>
		Joomla.submitform( pressbutton );
		<?php } else {?>
		submitform( pressbutton );
		<?php }?>
	}

	function checkcurrency (radio) {
		if (radio.value == 'y') { document.getElementById('currency_char').disabled = false; }
		else { document.getElementById('currency_char').disabled = true; }
	}

	//function override to write img src from com_media to text field
	//btw. on ie 8 error http://joomlacode.org/gf/project/joomla/tracker/?action=TrackerItemEdit&tracker_item_id=22735
	// var jInsertEditorText = jInsertEditorText;  ...? not?
	function jInsertEditorText(img,editor)
	{
		if ($(editor).type=="text"){
			pattern =/src=\"([^\"]+)\"/i;
			matches = img.match(pattern);
			$(editor).value = matches[1];
		}
		else{
			if (typeof tinyMCE != "undefined"){
				if (tinyMCE.execInstanceCommand)
					tinyMCE.execInstanceCommand(editor, 'mceInsertContent',false,img);
				else //j3
					tinyMCE.execCommand('mceInsertContent',false,img);
			}
			else{
				var editor = document.getElementById('xstandard');
				editor.InsertXML(img);
			}
		}
	}
</script>
<?php $x = 0; ?>

<form action="index.php" name="adminForm" id="adminForm" method="post" enctype="multipart/form-data" class="form-horizontal">
<?php

	if (COM_VMINVOICE_ISJ16)
		$params = new JRegistry($this->paramsdata);  //JParameter deprecaed and removed in J3
	else
		$params = new JParameter($this->paramsdata);

	//put all trabslatable params into separarate object. else rendererer would be confused by array inside value
	foreach ($this->translatable as $fieldName)
		$params->set($fieldName, null);

	
	
	InvoiceGetter::getVMTranslation(''); //to load VM files for statuses translation

	//remove vm1 or vm2 specific params
	$onlyVM2Params = array('show_discount','invoice_number::order_number','take_discount_into_summary', 'item_subtotal_with_discount',  //:: - remove option of select
			'attributes_use_vm', 'show_total_currencies', 'show_total_currencies_list', 'default_as_guest', 'show_price_discounts', 'display_in_payment_currency'); 
	$onlyVM1Params = array('product_price_calculation','payment_amount_source','paymenet_taxrate');
	
	$simpleXml = simplexml_load_file($this->paramsdefs);
	$paramsDelete = COM_VMINVOICE_ISVM2 ? $onlyVM1Params : $onlyVM2Params;
		
	foreach ($paramsDelete as $paramDelete){

		if (strpos($paramDelete,'::')!==false)
		{
			$parts = explode('::',$paramDelete);
			if (count($result = $simpleXml->xpath("//param[@name='$parts[0]']/option[@value='$parts[1]']"))){
			    $domRef = dom_import_simplexml($result[0]); 
	   			$domRef->parentNode->removeChild($domRef);
			}
		}
		elseif (count($result = $simpleXml->xpath("//param[@name='$paramDelete']"))){
		    $domRef = dom_import_simplexml($result[0]); 
    		$domRef->parentNode->removeChild($domRef);
		}

	}
	
	//TODO: tidy parameter dependatnt on canTidy (disabled if cannot)
	$xml = InvoiceHelper::getXML($simpleXml->asXML(), false);

	
	
			
	foreach ($xml->xpath('//param') as $param) {

		//change config SQLs based on version of VM
		if (isset($param['query'])){ 
			$param['query']= InvoiceGetter::getConfigSQL($param['name'],$param['query']);}

		//replace fonts by really available fonts in directory
		if ($param['name']=='font' AND $this->availableFonts)
		{
			$oldParam = simplexml_load_string($param->asXML());

			foreach ($param->xpath('option') as $child){
				$domRef = dom_import_simplexml($child);
				$domRef->parentNode->removeChild($domRef);
			}

			foreach ($this->availableFonts as $key => $name){
				
				//search old xml options for better description
				foreach($oldParam->children() as $child)
					if ($child['value']==$key)
						$name = (string)$child;
				
				$newChild = $param->addChild('option', $name);
				$newChild->addAttribute('value', $key);
			}
		}
	}
	
	//create form renderer
	if (COM_VMINVOICE_ISJ16){
		jimport('joomla.form.form');
		$form = JForm::getInstance('com_vminvoice.config2', InvoiceConfig::convertXMLToJ16($xml->asXML()), array('control' => 'params'));
		//cannot use $form->load(), it discarts <option />s ...
		$form->bind($params);
		$params = &$form;
	}
	else {
		$xmlJ15 = & JFactory::getXMLParser('Simple');
		$xmlJ15->loadString($xml->asXML());
		if ($paramsJ15 = & $xmlJ15->document->params)
			foreach ($paramsJ15 as $paramJ15)
				$params->setXML( $paramJ15 );
	}
	
?>
	
	<div class="col100">
        
<?php

echo InvoiceHelper::legacyJPane('start', 'tabs', 'config-pane');

// global configuration groups
if ($this->type=='general')
	$showGroups = array('COM_VMINVOICE_GENERAL_CONFIG', 'COM_VMINVOICE_TECHNICAL_CONFIG', 'COM_VMINVOICE_CUSTOMER_INFO_EXTRA_FIELDS', 'COM_VMINVOICE_ORDER_EDITING', 'COM_VMINVOICE_PAGE_APPEARANCE', 'COM_VMINVOICE_HEADER', 'COM_VMINVOICE_FOOTER', 'COM_VMINVOICE_BACKGROUND', 'COM_VMINVOICE_MAILING_CONFIGURATION', 'COM_VMINVOICE_REGISTRATION');
elseif ($this->type=='invoice')
	$showGroups = array('COM_VMINVOICE_INVOICE_NUMBERING', 
	'COM_VMINVOICE_INVOICE_CONFIGURATION', 
	'COM_VMINVOICE_INVOICE_ITEMS_CONFIGURATION', 
	'COM_VMINVOICE_INVOICE_TOTALS_CONFIGURATION', 
	'COM_VMINVOICE_INVOICE_MAILS');
elseif ($this->type=='dn')
	$showGroups = array('COM_VMINVOICE_DELIVERY_NOTE_CONFIGURATION', 
	'COM_VMINVOICE_DN_MAILS');
	
// not-start groups
$nsGroups = array('COM_VMINVOICE_TECHNICAL_CONFIG', 
'COM_VMINVOICE_CUSTOMER_INFO_EXTRA_FIELDS', 
'COM_VMINVOICE_HEADER', 
'COM_VMINVOICE_FOOTER', 
'COM_VMINVOICE_BACKGROUND', 
'COM_VMINVOICE_INVOICE_TOTALS_CONFIGURATION');

// not-end groups
$neGroups = array('COM_VMINVOICE_GENERAL_CONFIG', 
'COM_VMINVOICE_TECHNICAL_CONFIG', 
'COM_VMINVOICE_PAGE_APPEARANCE', 
'COM_VMINVOICE_HEADER', 
'COM_VMINVOICE_FOOTER',
'COM_VMINVOICE_CUSTOMER_INFO_EXTRA_FIELDS', 
'COM_VMINVOICE_INVOICE_ITEMS_CONFIGURATION');

// render each parameters group

$groups = COM_VMINVOICE_ISJ16 ? $params->getFieldsets() : $params->getGroups();

if (is_array($groups) AND count($groups) > 0) {
    $i = 0;
    foreach ($groups as $group => $countOrInfo) {
        if (!(!COM_VMINVOICE_ISJ16 AND $countOrInfo == 0)) {
			$groupName = COM_VMINVOICE_ISJ16 ? $countOrInfo->group : $group;
            $label = JText::_($groupName);
            if ((isset($showGroups) AND in_array($groupName, $showGroups)) OR !isset($showGroups)) { // configuration was exploded to more views, but only with 1 basic xml
                
				if (!in_array($groupName, $nsGroups))
					echo InvoiceHelper::legacyJPane('startpane', 'tabs', 'page-'.(++$i), $label);
                ?>
                <div class="vminvoice-width-100">
                <fieldset class="adminform invoice_config">
                  <legend><?php echo $label; ?></legend>
                  
                  <?php if (COM_VMINVOICE_ISJ16) { ?>
	                  <ul class="adminformlist">
	                  
	                  <?php  
	                  	//rednder using jform
	                  	//J!1.6 render manually as standard list (little edited in invoice general.css)
                        $this->renderParamsJ25($params->getFieldset($group));
	                  ?>
	                  </ul>
				  <?php } else {
	                  //j! 1.5 - use default render parameters to table
	                  	echo $this->renderParamsJ15($params, 'params', $groupName);;
	               } ?>
                </fieldset>
                </div>
                <?php 
                if (!in_array($groupName, $neGroups))
					echo InvoiceHelper::legacyJPane('endpane');

            }
        }
    }
}

function template_page(&$object,$editor,$dn="",$type)
{
	global $conf;
?>
<fieldset class="adminform">
<legend class="hasTip" title="<?php echo $dn ? JText::_('COM_VMINVOICE_DELIVERY_NOTE_TEMPLATE') : JText::_('COM_VMINVOICE_INVOICE_TEMPLATE')?>::<?php echo JText::_('COM_VMINVOICE_TEMPLATE_INFO')?>"><?php echo $dn ? JText::_('COM_VMINVOICE_DELIVERY_NOTE_TEMPLATE') : JText::_('COM_VMINVOICE_INVOICE_TEMPLATE')?></legend>

<?php 

$template = $object->vminvoice_config->{'template_'.$dn.'header'}.'
<hr class="system-pagebreak" />
'.$object->vminvoice_config->{'template_'.$dn.'body'}.'
<hr class="system-pagebreak" />
'.$object->vminvoice_config->{'template_'.$dn.'footer'};

?>
<?php echo $editor->display('template_'.$dn, $template, '95%', '700', '40', '40'); ?>
</fieldset>

<fieldset class="adminform">
<legend class="hasTip" title="<?php echo JText::_('COM_VMINVOICE_ITEMS_BLOCK')?>::<?php echo JText::_('COM_VMINVOICE_TEMPLATE_ITEMS_INFO')?>"><?php echo JText::_('COM_VMINVOICE_ITEMS_BLOCK')?></legend>


<b><?php echo JText::_('COM_VMINVOICE_TABLE_HEADER')?></b><br>
<?php echo $editor->display('template_'.$dn.'items[0]', $object->vminvoice_config->{'template_'.$dn.'items'}[0], '95%', '50', '40', '5'); ?>

<br><br>

<div class="clr"></div>

<b><?php echo JText::_('COM_VMINVOICE_TABLE_ROW')?></b><br>
<?php echo $editor->display('template_'.$dn.'items[1]', $object->vminvoice_config->{'template_'.$dn.'items'}[1], '95%', '50', '40', '5'); ?>

<div class="clr"></div>



<?php if (!$dn) { //currently not for DN ?>

<b><?php echo JText::_('COM_VMINVOICE_TABLE_FOOTER')?></b><br>
<?php echo JText::_('COM_VMINVOICE_TABLE_FOOTER_HELP')?>
<br><br>
<?php 
//load current ordering
$footers = array();
foreach (invoiceHelper::getItemsFooterOrdering($dn=='dn_') as $key => $rowType){
	$footers[$key] = array($rowType, JText::_('COM_VMINVOICE_'.$rowType));
	if ($rowType=='total')
		$footers[$key][1] = '<b>'.$footers[$key][1].'</b>';
	elseif (in_array($rowType, array('empty','hr')))
		$footers[$key][1] = '['.$footers[$key][1].']';
}

?>
<table width="100%"><tr><td width="500" valign="top">
<div id="template_<?php echo $dn ?>footers" style="border-top: solid gray 1px;">

<?php foreach ($footers as $footer){
	$id = 'orderdiv'.round(rand(0, 10000)); 
	?>
	<div id="<?php echo $id?>" style="border-bottom: solid gray 1px; border-right: solid gray 1px; border-left: solid gray 1px; padding: 10px; cursor:move">
	<?php echo $footer[1]?>
	<input type="hidden" name="params[items_footer_<?php echo $dn ?>ordering][]" value="<?php echo $footer[0]?>" />
	
	<span style="float:right">
	
	<span class="order_move_up" style="display:none">
	<a href="javascript:void(0)" onclick="moveDiv($('<?php echo $id?>'), 'up')" ><?php echo JText::_('COM_VMINVOICE_UP')?></a> 
	</span>
	
	<span class="order_move_down" style="display:none">
	<a href="javascript:void(0)" onclick="moveDiv($('<?php echo $id?>'), 'down')" ><?php echo JText::_('COM_VMINVOICE_DOWN')?></a> 
	</span>
	
	<?php if (in_array($footer[0], array('empty','hr'))){?>
	<a href="javascript:void(0)" onclick="this.getParent('div').destroy()" ><?php echo JText::_('COM_VMINVOICE_REMOVE')?></a>
	<?php }?>
	
	</span>
	
	</div>
<?php } ?>

</div>

<script type="text/javascript">

mySortables<?php echo $dn ?> = null;

window.addEvent('domready', function(){
	
	mySortables<?php echo $dn ?> = typeof Sortables != "undefined" ? new Sortables($('template_<?php echo $dn ?>footers')) : null;
	
	if (!mySortables<?php echo $dn ?>){ //Sortables not possible
	
		//show up/down buttons 
		$('template_<?php echo $dn ?>footers').getElements('span.order_move_up, span.order_move_down').setStyle('display', 'inline');
	
		//remove "move" cursor
		$('template_<?php echo $dn ?>footers').getElements('div').setStyle('cursor', 'default');
	
		initUpDown($('template_<?php echo $dn ?>footers'));
	}

});

function initUpDown(div){

	if (mySortables<?php echo $dn ?>)
		return ;
	
	div.getElements('span.order_move_up').each(function(el, i){
		console.log(i);
		el.style.display = (i==0) ? 'none' : 'inline';	
	});

	els = div.getElements('span.order_move_down');
	els.each(function(el, i){
		el.style.display = (i==(els.length-1)) ? 'none' : 'inline';	
	});
}

function add_<?php echo $dn ?>row(type)
{
	if (type=='empty')
		text = '<?php echo JText::_('COM_VMINVOICE_EMPTY')?>';
	else
		text = '<?php echo JText::_('COM_VMINVOICE_HR')?>';

	id = 'orderdiv'+Math.floor(Math.random()*10001);
	
	myDiv = new Element('div', {id: id, html: '['+text+']', styles: { 'border-bottom': 'solid gray 1px', 'border-left': 'solid gray 1px', 'border-right': 'solid gray 1px', padding: 10, 'cursor': 'move'}});
	if (mySortables<?php echo $dn ?>)
		myDiv.style.cursor = 'move';

	myInput = new Element('input', {type: 'hidden', name: 'params[items_footer_<?php echo $dn ?>ordering][]', value: type});
	myInput.inject(myDiv, 'bottom');

	//wrapping span for float:rights
	mySpan = new Element('span', {styles: {'float': 'right'}});
	
	if (!mySortables<?php echo $dn ?>){ //add move up/down links

		myAUpSpan = new Element('span', {'class': 'order_move_up'});
		myAUp =  new Element('a', {html: '<?php echo JText::_('COM_VMINVOICE_UP')?>',  events: {click: function(e){moveDiv($(id), 'up');}}, href: 'javascript:void(0)'});
		myAUp.inject(myAUpSpan, 'bottom');
		myAUp.appendText(' ', 'after');
		myAUpSpan.inject(mySpan, 'bottom');

		myADownSpan = new Element('span', {'class': 'order_move_down'});
		myADown =  new Element('a', {html: '<?php echo JText::_('COM_VMINVOICE_DOWN')?>',  events: {click: function(e){moveDiv($(id), 'down');}}, href: 'javascript:void(0)'});
		myADown.inject(myADownSpan, 'bottom');
		myADown.appendText('  ', 'after');
		myADownSpan.inject(mySpan, 'bottom');
	}
	
	myARemove = new Element('a', {html: '<?php echo JText::_('COM_VMINVOICE_REMOVE')?>', events: {click: function(e){this.getParent('div').destroy();}}, href: 'javascript:void(0)'})
	myARemove.inject(mySpan, 'bottom');
	
	mySpan.inject(myDiv, 'bottom');
	
	myDiv.inject($('template_<?php echo $dn ?>footers'), 'bottom');
	if (mySortables<?php echo $dn ?>)
		mySortables<?php echo $dn ?>.addItems(myDiv);
}

//move up/down fallback if Sortables not defined (J!1.5 without Mootools upgrade)
function moveDiv(element, direction)
{
	if ((direction=='down') && (next = element.getNext('div')))
		element.inject(next, 'after');
	else if ((direction=='up') && (prev = element.getPrevious('div')))
		element.inject(prev, 'before');	
	
	if (mySortables<?php echo $dn ?>){ //should not exists if calling this.. but for all cases
		mySortables<?php echo $dn ?>.detach();
		mySortables<?php echo $dn ?> = false;
	}
	
	initUpDown(element.getParent('div'));
		
	//TODO:: and show/hide next /prev buttons
}

</script>

</td>
<td width="500" valign="top">
<?php echo JText::_('COM_VMINVOICE_ADD_ROW')?>:<br>
<a href="javascript:void(0)" onclick="add_<?php echo $dn ?>row('empty')"><?php echo JText::_('COM_VMINVOICE_EMPTY')?></a><br>
<a href="javascript:void(0)" onclick="add_<?php echo $dn ?>row('hr')"><?php echo JText::_('COM_VMINVOICE_HR')?></a><br>

</td></tr></table>

<div class="clr"></div>
<br/>
<b><?php echo JText::_('COM_VMINVOICE_CUSTOM_TAX_SUMMARY_TEMPLATE_HEADER')?></b><br/>
<?php echo JText::_('COM_VMINVOICE_CUSTOM_TAX_SUMMARY_TEMPLATE_HELP')?>
<br/><br/>

<?php echo $editor->display('template_'.$dn.'tax_header', $object->vminvoice_config->{'template_'.$dn.'tax_header'}, '95%', '50', '40', '5'); ?>

<div class="clr"></div>
<br/>
<b><?php echo JText::_('COM_VMINVOICE_CUSTOM_TAX_SUMMARY_TEMPLATE_ROW')?></b>
<br/><br/>

<?php echo $editor->display('template_'.$dn.'tax_row', $object->vminvoice_config->{'template_'.$dn.'tax_row'}, '95%', '50', '40', '5'); ?>

<div class="clr"></div>


<?php } ?>



</fieldset>

<fieldset class="adminform">
<legend><?php echo JText::_('COM_VMINVOICE_HELP')?></legend>
<?php echo nl2br(JText::_('COM_VMINVOICE_HELP_TEMPLATE'))?><br>

<br>
<b><a href="javascript:void(0)" onclick="extededTags<?php echo $dn ?>.toggle()">
<?php echo JText::_('COM_VMINVOICE_POSSIBLE_REPLACEMENT_FIELDS')?></a></b>

<div id="extended_tags<?php echo $dn ?>">
<br>
<table style="table-layout:fixed; width: 100%;">
<thead>
<tr style="font-weight:bold">
<th><?php echo JText::_('COM_VMINVOICE_BASIC_FIELDS')?>: </th>
<th colspan="2"><?php echo JText::_('COM_VMINVOICE_CUSTOMER_INFO')?>: </th>

<th><?php echo JText::_('COM_VMINVOICE_VENDOR')?>: </th>

<th><?php echo JText::_('COM_VMINVOICE_ORDER_PRICES')?>: </th>
<th><?php echo JText::_('COM_VMINVOICE_FIELDS_FOR_ITEMS_BLOCK')?>: </th></tr>
</thead>
<tr><td valign="top">

{start_note}<br>
{order_id}<br>
{invoice_number}<br>

{contact}<br>
{logo}<br>

<br>

{invoice_date} + {_cpt}<br>
{taxable_payment_date} + {_cpt}<br>
{maturity_date} + {_cpt}<br>

{shipping_date} + {_cpt}<br>

{payment_type} + {_cpt}

<?php if (COM_VMINVOICE_ISVM2){?> + {payment_type_desc}<?php }?>

<br>
{variable_symbol} + {_cpt}<br>
{finnish_index_number} + {_cpt}<br>
{customer_number} + {_cpt}<br>

{shopper_group} + {_cpt}<br>
{coupon_code} + {_cpt}<br>
{order_status} + {_cpt}<br>
{order_number} + {_cpt}<br>
{shipping_cpt}<br>
{shipping_name}<br>
{shipping_desc}

<?php
$params=InvoiceHelper::getParams();
if ($params->get('allow_order_notes', 0)){?>
<br>{order_note} + {_cpt}
<?php } ?>


<br>
<br>
{items}<br>
{items_count}<br>
{items_sum}<br>
<br>

{total_weight} + {_cpt}<br />
{weight_unit}<br />
<br />
{billing_address}<br>
{shipping_address}<br>


<br>
{customer_note} + {_cpt}<br>

{order_history}<br>

{extra_fields}<br>
{signature}<br>
{pagination}<br>
{end_note}<br>
<br>

<?php ?>	

<?php echo JText::_('COM_VMINVOICE_CAPTIONS_HELP') ?>

</td>

<td valign="top">
<?php foreach (InvoiceGetter::getOrderAddress() as $field)
	echo '{billing_'.$field.'}<br>'."\n";?>
</td><td valign="top">
<?php foreach (InvoiceGetter::getOrderAddress() as $field)
	echo '{shipping_'.$field.'}<br>'."\n";?>
</td>



<td valign="top">
<?php foreach (InvoiceGetter::getVendor() AS $field)
	echo '{vendor_'.$field.'}<br>'."\n";?>
</td>

<td valign="top">

	{shipping_net}<br>
	{shipping_tax}<br>
	{shipping_tax_rate}<br>
	{shipping_gross}<br>
	<?php if (COM_VMINVOICE_ISVM2){?>
	{payment_net}<br>
	{payment_tax}<br>
	{payment_tax_rate}<br>
	{payment_gross}<br>
	<?php }?>
	{subtotal_net} + {-words}<br>
	{subtotal_tax} + {-words}<br>
	{subtotal_gross} + {-words}<br>
	{order_discount} + {-words}<br>
	{coupon_discount} + {-words}<br>
	{total} + {-words}<br>




<br>
<?php echo JText::_('COM_VMINVOICE_WORDS_HELP') ?>
	
</td><td valign="top">
<?php 
//get row tags and tooltips from HTML class
require_once(JPATH_ADMINISTRATOR . '/components/com_vminvoice/helpers/invoicehtmlparent.php');
require_once(JPATH_ADMINISTRATOR . '/components/com_vminvoice/helpers/invoicehtml.php');

$tags = invoiceHTML::getAvailableTags();

foreach ($tags[0] as $tag => $info)
	echo $info ? '<span class="hasTip" style="cursor:help" title="'.$tag.'::'.JText::_($info).'">'.'{'.$tag.'}</span><br>' : '{'.$tag.'}<br>';
	


if (!empty($tags[1])){
	
	echo '<br><b>'.JText::_('COM_VMINVOICE_CAPTIONS').':</b><br>';
	
	
	foreach ($tags[1] as $tag => $info)
		echo $info ? '<span class="hasTip" style="cursor:help" title="'.$tag.'::'.JText::_($info).'">'.'{'.$tag.'}</span><br>' : '{'.$tag.'}<br>';
}
?>
</td>
</tr>
</table>

<br>
<br>
<b><?php echo JText::_('COM_VMINVOICE_CONDITIONAL_BLOCKS') ?>:</b><br>
{lastpage}...{/lastpage} - <?php echo JText::_('COM_VMINVOICE_CONDITIONAL_LASTPAGE') ?><br>
{notlastpage}...{/notlastpage} - <?php echo JText::_('COM_VMINVOICE_CONDITIONAL_NOTLASTPAGE') ?><br>
{onepage}...{/onepage} - <?php echo JText::_('COM_VMINVOICE_CONDITIONAL_ONEPAGE') ?><br>
{notonepage}...{/notonepage}  - <?php echo JText::_('COM_VMINVOICE_CONDITIONAL_NOTONEPAGE') ?><br>
{firstpage}...{/firstpage} - <?php echo JText::_('COM_VMINVOICE_CONDITIONAL_FIRSTPAGE') ?><br>
{notfirstpage}...{/notfirstpage}  - <?php echo JText::_('COM_VMINVOICE_CONDITIONAL_NOTFIRSTPAGE') ?><br>

</div>

<script type="text/javascript">
	var extededTags<?php echo $dn ?> = new Fx.Slide('extended_tags<?php echo $dn ?>'); 
	extededTags<?php echo $dn ?>.hide();
</script>
<br>

<input class="btn" type="button" value="<?php echo JText::_('COM_VMINVOICE_RESTORE_TEMPLATE_BACK_TO_ORIGINAL')?>" onclick="document.location.href='index.php?option=com_vminvoice&controller=config&task=template_<?php echo $dn ?>restore&type=<?php echo $type; ?>'">

	
</fieldset>

<?php 
}

if ($this->type=='invoice' OR empty($this->type)){
	echo InvoiceHelper::legacyJPane('startpane', null, 'template_edit', JText::_('COM_VMINVOICE_INVOICE_TEMPLATE'));
	template_page($this,$editor,"",$this->type);
	echo InvoiceHelper::legacyJPane('endpane');
}
if ($this->type=='dn' OR empty($this->type)){
	echo InvoiceHelper::legacyJPane('startpane', null, 'template_dn_edit', JText::_('COM_VMINVOICE_DELIVERY_NOTE_TEMPLATE'));
	template_page($this,$editor,"dn_",$this->type);
	echo InvoiceHelper::legacyJPane('endpane');
}

echo InvoiceHelper::legacyJPane('end');

?>
        
    </div>
    <div class="clr"></div>

    <input type="hidden" name="type" value="<?php echo $this->type ?>" />
    <input type="hidden" name="option" value="com_vminvoice" />
    <input type="hidden" name="id" value="<?php echo $this->vminvoice_config->id; ?>" />
    <input type="hidden" name="task" value="" />
    <input type="hidden" name="controller" value="config" />
</form>
