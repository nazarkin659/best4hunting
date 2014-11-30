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
      <legend><?php echo JText::_('COM_VMINVOICE_SHIPPING') ?></legend>
          <table style="float: left; margin-right: 10px;" cellspacing="0" class="admintable">
    		<tbody>
      <?php if (COM_VMINVOICE_ISVM2) {?>
          			<tr>
    				<td class="key" nowrap="nowrap"><span class="hasTip" title="<?php echo $this->escape(JText::_('COM_VMINVOICE_SHIPPING')); ?>::<?php echo $this->escape(JText::_('COM_VMINVOICE_SHIPPING')); ?>"><?php echo JText::_('COM_VMINVOICE_SHIPPING'); ?></span></td>
    				<td>
      <?php 
      array_unshift($this->shippings, JHTML::_('select.option', '', JText::_('COM_VMINVOICE_SELECT'), 'shipping_rate_id', 'name'));
      echo JHTML::_('select.genericlist', $this->shippings, 'shipment_method_id', "", 'shipping_rate_id', 'name', $this->orderData->shipment_method_id); 						
      ?>
      <input type="button" class="hasTip btn" title="<?php echo $this->escape(JText::_('COM_VMINVOICE_APPLY')); ?>::<?php echo $this->escape(JText::_('COM_VMINVOICE_APPLY_SHIPMENT_DESC')); ?>" value="<?php echo JText::_('COM_VMINVOICE_APPLY'); ?> &raquo;" onclick="showOrderData(null,false,true,false,false);">		
          			</td>
    		</tr>
      
      <?php } else { ?>

    			<tr>
    				<td class="key" nowrap="nowrap"><span class="hasTip" title="<?php echo $this->escape(JText::_('COM_VMINVOICE_SHIPPING_PATTERN')); ?>::<?php echo $this->escape(JText::_('COM_VMINVOICE_SHIPPING_PATTERN_DESC')); ?>"><?php echo JText::_('COM_VMINVOICE_SHIPPING_PATTERN'); ?></span></td>
    				<td>
    					<?php

    						$shippingSelected=$this->orderData->custom_shipping_class.'|'.$this->orderData->custom_shipping_carrier.'|'.$this->orderData->custom_shipping_ratename.'|'.$this->orderData->custom_shipping_costs.'|'.$this->orderData->custom_shipping_id.'|'.$this->orderData->order_shipping_taxrate;
    						
	    					foreach ($this->shippings as $shipping) {
	    						$shipping->shipping_rate_id = htmlspecialchars($shipping->shipping_rate_id).'|'.$shipping->tax_rate;
	    						$shipping->name = htmlspecialchars(JText::sprintf('COM_VMINVOICE_SHIPPING_SHORT_INFO', $shipping->name[0], $shipping->name[1], $shipping->name[2], $shipping->name[3], $shipping->name[4]));
	    					}
    						
    						array_unshift($this->shippings, JHTML::_('select.option', '', JText::_('COM_VMINVOICE_SELECT'), 'shipping_rate_id', 'name'));
    						echo JHTML::_('select.genericlist', $this->shippings, 'ship_method_id', "onchange='processShippingChange();'", 'shipping_rate_id', 'name', $shippingSelected); 						
    						?>		
    						
    						<input type="button" class="hasTip btn" title="<?php echo $this->escape(JText::_('COM_VMINVOICE_APPLY')); ?>::<?php echo $this->escape(JText::_('COM_VMINVOICE_APPLY_SHIPMENT_DESC')); ?>" value="<?php echo JText::_('COM_VMINVOICE_APPLY'); ?> &raquo;" onclick="applyShipping();">			
    				</td>
    			</tr>
    				
    			<tr>
    				<td class="key" nowrap="nowrap"><span class="compulsory hasTip" title="<?php echo $this->escape(JText::_('COM_VMINVOICE_SHIPPING_CLASS')); ?>::<?php echo $this->escape(JText::_('COM_VMINVOICE_SHIPPING_CLASS_DESC')); ?>"><?php echo JText::_('COM_VMINVOICE_SHIPPING_CLASS'); ?></span></td><td>
    					<input type="text" name="custom_shipping_class" id="custom_shipping_class" size="30"  value="<?php echo $this->orderData->custom_shipping_class; ?>" />
    				</td>
    			</tr>
    			
    			<tr>
    				<td class="key" nowrap="nowrap"><span class="compulsory"><?php echo JText::_('COM_VMINVOICE_CARRIER'); ?></span></td><td>
    					<input type="text" name="custom_shipping_carrier" id="custom_shipping_carrier" size="30"  value="<?php echo $this->orderData->custom_shipping_carrier; ?>" />
    				</td>
    			</tr>
    
    			<tr>
    				<td class="key" nowrap="nowrap"><span class="compulsory"><?php echo JText::_('COM_VMINVOICE_RATE_NAME'); ?></span></td><td>
    					<input type="text" name="custom_shipping_ratename" id="custom_shipping_ratename" size="30" value="<?php echo $this->orderData->custom_shipping_ratename; ?>" />
    				</td>
    			</tr>
    
    			<tr>
    				<td class="key" nowrap="nowrap"><span class="compulsory hasTip" title="<?php echo $this->escape(JText::_('COM_VMINVOICE_SHIPPING_COSTS')); ?>::<?php echo $this->escape(JText::_('COM_VMINVOICE_SHIPPING_COSTS_DESC')); ?>"><?php echo JText::_('COM_VMINVOICE_SHIPPING_COSTS'); ?></span></td>
    				<td>
    					<input type="text" name="custom_shipping_costs" id="custom_shipping_costs" size="30"  value="<?php echo $this->orderData->custom_shipping_costs; ?>" />					
    				</td>
    			</tr>
    
    			<tr>
    			<td class="key" nowrap="nowrap"><span class="compulsory hasTip" title="<?php echo $this->escape(JText::_('COM_VMINVOICE_SHIPPING_TAX')); ?>::<?php echo $this->escape(JText::_('COM_VMINVOICE_SHIPPING_TAX_DESC')); ?>"><?php echo JText::_('COM_VMINVOICE_SHIPPING_TAX'); ?></span></td>
    				<td>
    					<!--  <input type="text" name="custom_shipping_taxrate" id="custom_shipping_taxrate" size="30" value="<?php echo $this->orderData->custom_shipping_taxrate; ?>" />-->
    					<?php echo JHTML::_('select.genericlist', $this->taxRates, 'custom_shipping_taxrate', null, 'value', 'name', $this->orderData->custom_shipping_taxrate);  ?>
    				</td>
    			</tr>			
    			<tr>
    				<td class="key" nowrap="nowrap"><span class="hasTip" title="<?php echo $this->escape(JText::_('COM_VMINVOICE_RATE_ID')); ?>::<?php echo $this->escape(JText::_('COM_VMINVOICE_RATE_ID_DESC')); ?>"><?php echo JText::_('COM_VMINVOICE_RATE_ID'); ?></span></td><td>
    					<input type="text" name="custom_shipping_id" id="custom_shipping_id" size="30"  value="<?php echo $this->orderData->custom_shipping_id; ?>" />
    					<!--  <input type="button" class="hasTip btn" title="<?php echo $this->escape(JText::_('COM_VMINVOICE_CALCULATE_TAX')); ?>::<?php echo $this->escape(JText::_('COM_VMINVOICE_COMPUTE_TAX_DESC')); ?>" value="<?php echo JText::_('COM_VMINVOICE_COMPUTE_TAX'); ?> &raquo;" onclick="getShippingTax($('custom_shipping_class').value,$('custom_shipping_id').value);">  -->
    				</td>
    			</tr>								
    	<?php } ?>
    	    </tbody>
    	</table>
    	
    	 <?php
    	 //render details, but only with data from database
    	if (COM_VMINVOICE_ISVM2 AND $this->orderData->order_id AND $this->orderData->shipment_method_id){
	
		    JPluginHelper::importPlugin('vmshipment');
		    $_dispatcher = JDispatcher::getInstance();
		    $_returnValues = $_dispatcher->trigger('plgVmOnShowOrderBEShipment',array($this->orderData->order_id,$this->orderData->shipment_method_id, $this->vmorder));
		    			
		    foreach ($_returnValues as $_returnValue) 
		    	if ($_returnValue !== null) {
		    		$_returnValue = preg_replace('#<thead>\s*<tr>\s*<th[^>]+>[^<]+<\/th>\s*<\/tr>\s*<\/thead>#isU', '', $_returnValue); //remove header
		    		$_returnValue = preg_replace('#(\s*<table [^>]*)width\s*:\s*\d+%?\s*;?([^>]*>)#isU', '$1$2', $_returnValue); //
		    		$_returnValue = preg_replace('#(\s*<table [^>]*)width\s*=\s*"\s*\d+%?\s*"([^>]*>)#isU', '$1$2', $_returnValue); //
		    		echo '<div id="shipment_details">'.preg_replace('#(class\s*=\s*"[^"]*)adminlist([^"]*")#isU', '$1$2', $_returnValue).'</div>';
		    	}
		}
    	?>	
    </fieldset>