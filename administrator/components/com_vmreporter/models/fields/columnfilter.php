<?php
/**
 * @version     1.0.0
 * @package     com_vmreporter
 * @copyright   Copyright (C) 2013 VirtuePlanet Services LLP. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      VirtuePlanet Services LLP <info@virtueplanet.com> - http://www.virtueplanet.com
 */

defined('JPATH_BASE') or die;

jimport('joomla.form.formfield');

/**
 * Supports an HTML select list of categories
 */
class JFormFieldColumnfilter extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since	1.6
	 */
	protected $type = 'columnfilter';

	/**
	 * Method to get the field input markup.
	 *
	 * @return	string	The field input markup.
	 * @since	1.6
	 */
	protected function getInput() {
		$coloums = array(	
						'order_number'=>JText::_('COM_VMREPORT_ORDER_NUMBER'),
            'virtuemart_user_id'=>JText::_('JGLOBAL_USERNAME'),
						'customer_name'=>JText::_('COM_VMREPORT_VIRTUEMART_USER_ID'),
						'virtuemart_vendor_id'=>JText::_('COM_VMREPORT_VIRTUEMART_VENDOR_ID'),
						'order_item_name'=>JText::_('COM_VMREPORT_ORDER_ITEM_NAME'),
						'product_attribute'=>JText::_('COM_VMREPORT_PRODUCT_ATTRIBUTE'),
						'product_quantity'=>JText::_('COM_VMREPORT_PRODUCT_QUANTITY'),
						'product_item_price'=>JText::_('COM_VMREPORT_PRODUCT_ITEM_PRICE'),
						'product_tax'=>JText::_('COM_VMREPORT_PRODUCT_TAX'),
						'product_basePriceWithTax'=>JText::_('COM_VMREPORT_PRODUCT_BASEPRICEWITHTAX'),
						'product_final_price'=>JText::_('COM_VMREPORT_PRODUCT_FINAL_PRICE'),
						'product_subtotal_discount'=>JText::_('COM_VMREPORT_PRODUCT_SUBTOTAL_DISCOUNT'),
						'product_subtotal_with_tax'=>JText::_('COM_VMREPORT_PRODUCT_SUBTOTAL_WITH_TAX'),
						'order_total'=>JText::_('COM_VMREPORT_ORDER_TOTAL'),
						'order_salesPrice'=>JText::_('COM_VMREPORT_ORDER_SALESPRICE'),
						'order_billTaxAmount'=>JText::_('COM_VMREPORT_ORDER_BILLTAXAMOUNT'),
						'order_billDiscountAmount'=>JText::_('COM_VMREPORT_ORDER_BILLDISCOUNTAMOUNT'),
						'order_discountAmount'=>JText::_('COM_VMREPORT_ORDER_DISCOUNTAMOUNT'),
						'order_subtotal'=>JText::_('COM_VMREPORT_ORDER_SUBTOTAL'),
						'order_tax'=>JText::_('COM_VMREPORT_ORDER_TAX'),
						'virtuemart_shipmentmethod_id'=>JText::_('COM_VMREPORT_ORDER_SHIPMENTMETHOD'),
						'order_shipment'=>JText::_('COM_VMREPORT_ORDER_SHIPMENT'),
						'order_shipment_tax'=>JText::_('COM_VMREPORT_ORDER_SHIPMENT_TAX'),
						'virtuemart_paymentmethod_id'=>JText::_('COM_VMREPORT_VIRTUEMART_PAYMENTMETHOD_ID'),
						'order_payment'=>JText::_('COM_VMREPORT_ORDER_PAYMENT'),
						'order_payment_tax'=>JText::_('COM_VMREPORT_ORDER_PAYMENT_TAX'),
						'coupon_discount'=>JText::_('COM_VMREPORT_COUPON_DISCOUNT'),
						'coupon_code'=>JText::_('COM_VMREPORT_COUPON_CODE'),
						'order_discount'=>JText::_('COM_VMREPORT_ORDER_DISCOUNT'),
						'order_currency'=>JText::_('COM_VMREPORT_ORDER_CURRENCY'),
						'order_status_name'=>JText::_('COM_VMREPORT_ORDER_STATUS'),
						'user_currency_id'=>JText::_('COM_VMREPORT_USER_CURRENCY_ID'),
						'user_currency_rate'=>JText::_('COM_VMREPORT_USER_CURRENCY_RATE'),				
						'created_on'=>JText::_('COM_VMREPORT_ORDER_CREATED_ON'),
						'modified_on'=>JText::_('COM_VMREPORT_ORDER_MODIFIED_ON'),
						'created_by'=>JText::_('COM_VMREPORT_ORDER_CREATED_BY'),
						'ip_address'=>JText::_('COM_VMREPORT_IP_ADDRESS')
						);
		return $coloums;
    }
}