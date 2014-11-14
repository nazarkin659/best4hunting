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
    <fieldset class="adminform">
      <legend><?php echo JText::_('COM_VMINVOICE_PAYMENT') ?></legend>
    	<table style="float: left; margin-right: 10px;" cellspacing="0" class="admintable">
    		<tbody>
    			<tr>
    				<td class="key" nowrap="nowrap"><span class="compulsory"><?php echo JText::_('COM_VMINVOICE_PAYMENT'); ?></span></td>
    				<td>
    					<?php 
    						if (COM_VMINVOICE_ISVM1)
	    						foreach ($this->payments as $payment) {
	    							if ($payment->payment_method_discount != 0.00)
	    								$payment->name = JText::sprintf($payment->payment_method_discount_is_percent == 1 ? 'COM_VMINVOICE_PAYMENT_SHORT_INFO_PERCENT' : 'COM_VMINVOICE_PAYMENT_SHORT_INFO', $payment->name, - round($payment->payment_method_discount, 2));
	    							else
	    								$payment->name = $payment->name;
	    						} 
    						array_unshift($this->payments, JHTML::_('select.option', '', JText::_('COM_VMINVOICE_SELECT'), 'id', 'name'));
    						echo JHTML::_('select.genericlist', $this->payments, 'payment_method_id', 'onchange="changedPayment()"', 'id', 'name', $this->orderData->payment_method_id); 
    					?>
    					<?php if (COM_VMINVOICE_ISVM2){ ?>
    					<input type="button" class="hasTip btn" title="<?php echo $this->escape(JText::_('COM_VMINVOICE_APPLY')); ?>::<?php echo $this->escape(JText::_('COM_VMINVOICE_APPLY_PAYMENT_DESC')); ?>" value="<?php echo JText::_('COM_VMINVOICE_APPLY'); ?> &raquo;" onclick="showOrderData(null,false,false,true,false);">
    					<?php } ?>
    				</td>
    			</tr>	
    			<?php if (COM_VMINVOICE_ISVM2){ ?>
    			<tr>
    				<td class="key" nowrap="nowrap"><span class="hasTip" title="<?php echo JText::_('COM_VMINVOICE_PAYMENT_CURRENCY'); ?>" rel="<?php echo JText::_('COM_VMINVOICE_PAYMENT_CURRENCY_DESC'); ?>"><?php echo JText::_('COM_VMINVOICE_PAYMENT_CURRENCY'); ?></span></td>
    				<td>
    					<script type="text/javascript">
    					function changedPayment(){ //on payment chnage, automatically pre-select paymnt currency and load exchange rate
    						var paymentMethod = $('payment_method_id').getSelected()[0].get("value");
    						var paymentCurrency = paymentCurrencies[paymentMethod*1];

    						$('user_currency_id').getElements('option').each(function(option) {
    							option.selected = option.get('value')==paymentCurrency;
    						});
    						changedCurrency();
    						updateExchangeRate();
            			}

    					function changedCurrency(){
							var orderCurrency = $('order_currency').getSelected()[0].get("value");
							var paymentCurrency = $('user_currency_id').getSelected()[0].get("value");
							var showRate = (orderCurrency && paymentCurrency && (orderCurrency*1 != paymentCurrency*1));
							$('payment_curr_exchange_rate').setStyle('display', showRate ? 'table-row' : 'none');							
						}
						function updateExchangeRate()
						{
							var orderCurrency = $('order_currency').getSelected()[0].get("value");
							var paymentCurrency = $('user_currency_id').getSelected()[0].get("value");

							var url = '<?php echo JURI::base() ?>index.php?option=com_vminvoice&controller=order&task=exchangerateajax&from='+encodeURIComponent(orderCurrency)+'&to='+encodeURIComponent(paymentCurrency);
							AUtility.ajaxSend (url, function(text){

								if (!isNaN(parseFloat(text)) && isFinite(text))
									$('user_currency_rate').value = text;
								else
									alert("Error: "+text);
								});
						}

						var paymentCurrencies = <?php echo json_encode($this->paymentCurrencies)?>; //0: same as vendor(=order) currency
						
						</script>
    					<?php
    						reset($this->currencies)->name = ' - '.JText::_('COM_VMINVOICE_SAME_AS_ORDER_CURRENCY').' - ';
    						echo JHTML::_('select.genericlist', $this->currencies, 'user_currency_id', 'onchange="changedCurrency();"', 'id', 'name', $this->orderData->user_currency_id); 
    					?>
    				</td>
    			</tr>
    			<tr id="payment_curr_exchange_rate" style="display:<?php echo ($this->orderData->user_currency_id AND $this->orderData->order_currency AND $this->orderData->user_currency_id!=$this->orderData->order_currency) ? 'table-row' : 'none'?>">
    				<td class="key" nowrap="nowrap"><span class="hasTip" title="<?php echo JText::_('COM_VMINVOICE_EXCHANGE_RATE'); ?>" rel="<?php echo JText::_('COM_VMINVOICE_EXCHANGE_RATE_DESC'); ?>"><?php echo JText::_('COM_VMINVOICE_EXCHANGE_RATE'); ?></span></td>
    				<td>
    					<input type="text" size="5" name="user_currency_rate" id="user_currency_rate" value="<?php echo $this->orderData->user_currency_rate*1 ?>" />				
    					<input type="button" value="<?php echo Jtext::_('COM_VMINVOICE_UPDATE')?>" onclick="updateExchangeRate()" />
    				</td>
    			</tr>
				<?php } ?>
    		</tbody>	
    	</table>
    	
    	 <?php
    	 //co se tyce shipment a paymnet
    	 //kdyz klikneme na appy, melo by se zmenit i toto info
    	 //ale jak je to mozne? neni. ptz to tahame z vm pluginu a ty nacitaji databazi. hej!
    	 //jenom ze bychom to dosasne ulozili a nacetli pluginy a pak zase zmenili zpatky :D
    	 
    	 
    	 //render details, but only with data from database
    	if (COM_VMINVOICE_ISVM2 AND $this->orderData->order_id AND $this->orderData->payment_method_id){

		    JPluginHelper::importPlugin('vmpayment');
		    $_dispatcher = JDispatcher::getInstance();
		    $_returnValues = $_dispatcher->trigger('plgVmOnShowOrderBEPayment',array($this->orderData->order_id,$this->orderData->payment_method_id, $this->vmorder));
		    			
		    foreach ($_returnValues as $_returnValue) 
		    	if ($_returnValue !== null) {
		    		$_returnValue = preg_replace('#<thead>\s*<tr>\s*<th[^>]+>[^<]+<\/th>\s*<\/tr>\s*<\/thead>#isU', '', $_returnValue); //remove header
		    		$_returnValue = preg_replace('#(\s*<table [^>]*)width\s*:\s*\d+%?\s*;?([^>]*>)#isU', '$1$2', $_returnValue); //
		    		$_returnValue = preg_replace('#(\s*<table [^>]*)width\s*=\s*"\s*\d+%?\s*"([^>]*>)#isU', '$1$2', $_returnValue); //
		    		echo '<div id="payment_details">'.preg_replace('#(class\s*=\s*"[^"]*)adminlist([^"]*")#isU', '$1$2', $_returnValue).'</div>';
		    	}
		}
    	?>		
    </fieldset>