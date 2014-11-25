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

JHTML::_('behavior.mootools');
JHTML::_('behavior.tooltip');

//check OR create indexes for customer search
if (JRequest::getInt('createindex'))
	InvoiceGetter::checkUserSearchIndexes(true);
elseif ($this->params->get('auto_check_search_indexes', 1)){
	$fields = InvoiceGetter::checkUserSearchIndexes();
	if ($fields){
		foreach ($fields as &$field) if (is_array($field)) $field = implode('+', $field);
		$code = JText::sprintf('COM_VMINVOICE_CREATE_SEARCH_INDEX_PROMPT', implode(', ', $fields));
		$url = JRoute::_('index.php?option=com_vminvoice&controller=invoices&task='.JRequest::getVar('task').'&cid='.$this->orderData->order_id.'&createindex=1');
		$code.='<br><center><input type="button" class="button btn" value="'.JText::_('COM_VMINVOICE_CREATE_SEARCH_INDEXES').'" onclick="document.location.href=\''.$url.'\';"></center>';
		JError::raiseNotice(0, $code);
	}
}

//add files with version query param (to refresh when file changes). 
//because of stupid bug in J!>1.6, when calling JHTML::script with some ? query parameter, script gets discareted. use directly addScript
//but not always(?) based on server settings
$files = array(
	'administrator/components/com_vminvoice/assets/js/ajaxcontent.js', 
	'administrator/components/com_vminvoice/assets/js/autility.js');
if (COM_VMINVOICE_ISVM2)
	$files[] = 'administrator/components/com_vminvoice/assets/js/rows.js';
$document = JFactory::getDocument();
foreach ($files as $file){
	$version = ($mtime = filemtime(JPATH_SITE.'/'.$file)) ? $mtime : time();
	$document->addScript(JURI::root().$file.'?v='.$version);}

JHTML::_('behavior.tooltip');
JHTML::_('behavior.calendar');

JToolBarHelper::title(JText::sprintf('COM_VMINVOICE_ORDER_NUMBER_EDITING', $this->orderData->order_id ? $this->orderData->order_id : ''), 'order');
JToolBarHelper::save();
JToolBarHelper::apply();
JToolBarHelper::cancel();

JFilterOutput::objectHTMLSafe($this->orderData);

$document = JFactory::getDocument();
/* @var $document JDocumentHTML */

$js  = '		var AddProduct = \'' . JText::_('COM_VMINVOICE_SELECT_PRODUCT', TRUE) . '\';' . "\n";
$js .= '		var AreYouSure = \'' . JTEXT::_('COM_VMINVOICE_ARE_YOU_SURE', TRUE) . '\';' . "\n";
$js .= '		function submitbutton (pressbutton) {' . "\n";
$js .= '			var form = document.adminForm;' . "\n";
$js .= '			if (pressbutton == \'cancel\') {' . "\n";
$js .= '		  		if ((typeof Joomla != "undefined") && (typeof Joomla.submitform != "undefined")) Joomla.submitform(pressbutton); else submitform(pressbutton);' . "\n";
$js .= '				return;' . "\n";
$js .= '			}' . "\n";
$js .= '			if (form.status.value == \'\')' . "\n";
$js .= '				alert(\'' . JText::_('COM_VMINVOICE_SELECT_STATUS', TRUE) . '\');' . "\n";
$js .= '			else if (form.vendor.value == \'\')' . "\n";
$js .= '				alert(\'' . JText::_('COM_VMINVOICE_SELECT_VENDOR', TRUE) . '\');' . "\n";
$js .= '			else if (form.order_currency.value == \'\')' . "\n";
$js .= '				alert(\'' . JText::_('COM_VMINVOICE_SELECT_CURRENCY', TRUE) . '\');' . "\n";
$js .= '			else if (form.payment_method_id.value == \'\')' . "\n";
$js .= '				alert(\'' . JText::_('COM_VMINVOICE_SELECT_PAYMENT', TRUE) . '\');' . "\n";
$js .= '			else if ($("orderInfo").getElements("select[name^=order_status]").some(function(el){if (!el.options[el.selectedIndex].value){el.focus(); return true;} return false;}))' . "\n";
$js .= '				alert(\'' . JText::_('COM_VMINVOICE_SELECT_ITEM_STATUS', TRUE) . '\');' . "\n";
$js .= '			else {' . "\n";
$js .= '				if ($("user_id").value==""){' . "\n"; //no user id. can be new user create or guest
$js .= '					$("update_userinfo").value=1;' . "\n";
$js .= '					if (form.B_first_name.value.trim() == \'\'){' . "\n";
$js .= '						alert(\'' . JText::_('COM_VMINVOICE_FILL_IN_FIRST_NAME', TRUE) . '\');' . "\n";
$js .= '						form.B_first_name.focus();}' . "\n";
$js .= '					else if (form.B_last_name.value.trim() == \'\'){' . "\n";
$js .= '						alert(\'' . JText::_('COM_VMINVOICE_FILL_IN_LAST_NAME', TRUE) . '\');' . "\n";
$js .= '						form.B_last_name.focus();}' . "\n";
$js .= '					else if (form.B_email.value.trim() == \'\'){' . "\n";
$js .= '						alert(\'' . JText::_('COM_VMINVOICE_FILL_IN_E-MAIL', TRUE) . '\');' . "\n";
$js .= '						form.B_email.focus();}' . "\n";
$js .= '					else {' . "\n";
$js .= '						if (!$("as_guest") || !$("as_guest").checked)' . "\n";
$js .= '							alert("' . JText::_('COM_VMINVOICE_NEW_CUSTOMER_DESC', TRUE) . '");' . "\n";
$js .= '						submitform( pressbutton );}' . "\n";
$js .= '				} else {' . "\n";
$js .= '					if (changed_userinfo==true && $("B_user_info_id").value>0){' . "\n";
$js .= '						if (confirm("' . JText::_('COM_VMINVOICE_UPDATE_ALSO_DEFAULT_VALUES', TRUE) . '"))' . "\n";
$js .= '							$("update_userinfo").value=1;' . "\n";
$js .= '					} ' . "\n";
$js .= '					if ((typeof Joomla != "undefined") && (typeof Joomla.submitform != "undefined")) Joomla.submitform( pressbutton ); else submitform( pressbutton );}' . "\n";
$js .= '				}' . "\n";
$js .= '		}' . "\n";
$js .= '	if ((typeof Joomla != "undefined") && (typeof Joomla.submitbutton != "undefined"))
				Joomla.submitbutton = submitbutton;' . "\n";


//initialize "user info change watcher"
$js .= 'var changed_userinfo=false;

	function addUserInfoCheck()
	{
			//for 1.5 without mootools upgrade
			userInputs = $("billing_address").getElements("input[name!=user]");
			userInputs.concat($("billing_address").getElements("textarea"));
			userInputs.concat($("billing_address").getElements("select"));

			userInputs.concat($("shipping_address").getElements("input"));
			userInputs.concat($("shipping_address").getElements("textarea"));
			userInputs.concat($("shipping_address").getElements("select"));
	
			$each(userInputs,function (input){
			
				if (typeof input.type != "undefined")
				{
					if (input.type=="text")
						input.addEvent("keyup", function(event){changed_userinfo=true;});
						
					if (input.type=="checkbox" || input.type=="radio")
						input.addEvent("click", function(event){changed_userinfo=true;});
				}
				
				input.addEvent("change", function(event){changed_userinfo=true;});
			});
	}

	window.addEvent(\'domready\', function() {
		addUserInfoCheck();
	});'; 

$document->addScriptDeclaration($js);

?>	

<form action="index.php" method="post" name="adminForm" id="adminForm" class="product">
<input type="hidden" id="baseurl" name="baseurl" value="<?php echo addslashes(JURI::base()); ?>" />
  <div class="purchase-order left">
    <fieldset class="adminform">
      <legend><?php echo JText::_('COM_VMINVOICE_GENERAL') ?></legend>
    	<table style="float: left; margin-right: 10px;" cellspacing="0" class="admintable">
    		<tbody>
    			<tr>
    				<td class="key" nowrap="nowrap"><?php echo JText::_('ID'); ?></td>
    				<td>
    					<?php echo $this->orderData->order_id ? $this->orderData->order_id : JText::_('COM_VMINVOICE_NEW'); ?>
    					<input type="hidden" id="cid" name="cid" value="<?php echo $this->orderData->order_id; ?>" />
    					<input type="hidden" id="order_id" name="order_id" value="<?php echo $this->orderData->order_id; ?>" />
    					<input type="hidden" id="order_number" name="order_number" value="<?php echo $this->orderData->order_number; ?>" />
    				</td>
    			</tr>
    			<?php if ($this->orderData->order_number){?>
    			<tr>
    				<td class="key" nowrap="nowrap"><?php echo JText::_('COM_VMINVOICE_ORDER_NUMBER'); ?></td>
    				<td><?php echo $this->orderData->order_number ?></td>
    			</tr>
    			<?php } ?>
    			<tr>
    				<td class="key" nowrap="nowrap"><?php echo JText::_('COM_VMINVOICE_CREATE_DATE'); ?></td>
    				<td><?php echo $this->orderData->cdate ? strftime(JText::_('COM_VMINVOICE_DATETIME_FORMAT'),$this->orderData->cdate) : ''; ?></td>
    			</tr>
    			<tr>
    				<td class="key" nowrap="nowrap"><?php echo JText::_('COM_VMINVOICE_MODIFIED_DATE'); ?></td>
    				<td><?php echo $this->orderData->mdate ? strftime(JText::_('COM_VMINVOICE_DATETIME_FORMAT'),$this->orderData->mdate) : ''; ?></td>
    			</tr>
    			<tr>
    				<td class="key" nowrap="nowrap"><span class="compulsory"><?php echo JText::_('COM_VMINVOICE_STATUS'); ?></span></td>
    				<td>
    					<?php
    						array_unshift($this->orderStatus, JHTML::_('select.option', '', JText::_('COM_VMINVOICE_SELECT'), 'id', 'name')); 
    						echo JHTML::_('select.genericlist', $this->orderStatus, 'status', null, 'id', 'name', $this->orderData->order_status); 
    					?>
    					<label><input type="checkbox" name="notify" value="YF"> <?php echo JText::_('COM_VMINVOICE_NOTIFY_CUSTOMER'); ?></label>
    					
    					<?php if (false /* disabled*/ && COM_VMINVOICE_ISVM2) {?>
    						<label class="hasTip" title="<?php echo $this->escape(JText::_('COM_VMINVOICE_APPLY_TO_ALL_ITEMS')); ?>::<?php echo $this->escape(JText::_('COM_VMINVOICE_STATUS_APPLY_ITEMS_DESC')); ?>">
    						<input type="checkbox" name="apply_status_to_all_items" value="1" checked />
    						<?php echo JText::_('COM_VMINVOICE_APPLY_TO_ALL_ITEMS'); ?></label>
    					<?php }  else {?>
    						<input type="button" class="hasTip btn" title="<?php echo $this->escape(JText::_('COM_VMINVOICE_APPLY_TO_ALL_ITEMS')); ?>::<?php echo $this->escape(JText::_('COM_VMINVOICE_STATUS_APPLY_ITEMS_DESC')); ?>" value="<?php echo JText::_('COM_VMINVOICE_APPLY_TO_ALL_ITEMS'); ?> &raquo;" onclick="applyStatus();">			
    					<?php } ?>
    				</td>
    			</tr>
    
    
    			<tr>
    				<td class="key" nowrap="nowrap"><span class="compulsory"><?php echo JText::_('COM_VMINVOICE_VENDOR'); ?></span></td>
    				<td>
    					<?php
    						array_unshift($this->vendors, JHTML::_('select.option', '', JText::_('COM_VMINVOICE_SELECT'), 'id', 'name')); 
    						echo JHTML::_('select.genericlist', $this->vendors, 'vendor', null, 'id', 'name', $this->orderData->vendor_id); 
    					?>
    				</td>
    			</tr>
    				
    			<tr>
    				<td class="key" nowrap="nowrap"><span class="compulsory hasTip" title="<?php echo $this->escape(JText::_('COM_VMINVOICE_CURRENCY')); ?>::<?php echo $this->escape(JText::_('COM_VMINVOICE_CURRENCY_DESC')); ?>"><?php echo JText::_('COM_VMINVOICE_CURRENCY'); ?></span></td>
    				<td>
    					<?php
    						echo JHTML::_('select.genericlist', $this->currencies, 'order_currency', COM_VMINVOICE_ISVM2 ? 'onchange="changedCurrency()"' : null, 'id', 'name', $this->orderData->order_currency); 
    					?>					
    				</td>
    			</tr>
    			<?php if ($this->orderLanguages) {?>
    			<tr>
    				<td class="key" nowrap="nowrap"><span class="hasTip" title="<?php echo $this->escape(JText::_('COM_VMINVOICE_ORDER_LANGUAGE')); ?>::<?php echo $this->escape(JText::_('COM_VMINVOICE_ORDER_LANGUAGE_DESC')); ?>"><?php echo JText::_('COM_VMINVOICE_ORDER_LANGUAGE'); ?></span></td>
    				<td>
    					<?php
    						echo JHTML::_('select.genericlist', $this->orderLanguages, 'order_language', null, 'lang_code', 'title', $this->orderData->order_language); 
    					?>					
    				</td>
    			</tr>
    			<?php } ?>
    		</tbody>
    	</table>
    </fieldset>
    <fieldset class="adminform">
      <legend><?php echo JText::_('COM_VMINVOICE_ADDITIONAL') ?></legend>
    	<table style="float: left;width:100%" cellspacing="0" class="admintable">
    		<tbody>
    			<tr>
    				<td class="key" nowrap="nowrap" valign="top"><span><?php echo JText::_('COM_VMINVOICE_CUSTOMER_NOTE'); ?></span></td>
    				<td>
    					<textarea name="customer_note" id="customer_note" cols="40" rows="4" style="width:97%"><?php echo $this->orderData->customer_note; ?></textarea>
    				</td>
    			</tr>
    			<tr>
    				<td class="key" nowrap="nowrap" valign="top"><span ><?php echo JText::_('COM_VMINVOICE_COUPON_CODE'); ?></span></td><td>
    					<input type="text" name="coupon_code" id="coupon_code" size="15" onchange="getCouponInfo(this.value,'<?php echo $this->orderData->order_currency?>');" onkeyup="getCouponInfo(this.value,'<?php echo $this->orderData->order_currency?>');"  value="<?php echo $this->orderData->coupon_code; ?>" />
    					<span id="coupon_info"></span>
    					
    					<script type="text/javascript">getCouponInfo($('coupon_code').value, '<?php echo $this->orderData->order_currency?>');</script>
    				</td>
    			</tr>
    			<?php ?>
    		</tbody>
    	</table>
    </fieldset>
  </div>
  <div class="purchase-order right">

	<?php 
	require dirname(__FILE__).'/default_shipment.php';
	require dirname(__FILE__).'/default_payment.php';
	?>
    
    <?php if ($this->params->get('allow_order_notes', 0)){  ?>
    <fieldset class="adminform">
      <legend><?php echo JText::_('COM_VMINVOICE_ORDER_NOTES') ?></legend>
    	<table style="float: left; margin-right: 10px;width:100%" cellspacing="0" class="admintable">
    		<tbody>
    			<tr>
    				<td class="key hasTip" nowrap="nowrap" valign="top"
    				title="<?php echo JText::_('COM_VMINVOICE_ORDER_NOTES'); ?>"
    				rel="<?php echo JText::_('COM_VMINVOICE_ORDER_NOTES_DESC'); ?>"
    				><?php echo JText::_('COM_VMINVOICE_ORDER_NOTES'); ?></td>
    				<td>
						 <textarea name="order_note" id="order_note" cols="40" rows="4" style="width:97%"><?php echo isset($this->orderParams->order_note) ? $this->orderParams->order_note : ''; ?></textarea>
    				</td>
    			</tr>	
    			
    		</tbody>	
    	</table>
    </fieldset>
    <?php } ?>
    
  </div>
	<div class="clr"></div>
	<?php require_once 'products.php'; ?>
	<?php require_once 'userinfo.php'; ?>
	<input type="hidden" value="com_vminvoice" name="option" /> 
	<input type="hidden" name="task" value="" /> 
	<input type="hidden" name="controller" value="order" /> 
</form>