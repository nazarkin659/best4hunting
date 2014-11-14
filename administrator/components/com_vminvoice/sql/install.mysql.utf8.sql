/*
 * NOTE: this file is run every time the new install or reinstall is performed.
 * Or when is set in upgrade file to run this file.
 * 
 * So it must me made to not rewrite current configuration or component state. 
 * In case component is installed it must just update things that have to be updated.
 * 
 * Database is not deleted on unistall so keep it in mind 
 * 
 * http://stackoverflow.com/questions/972922/add-column-to-mysql-table-if-it-does-not-exist
 */
				CREATE TABLE IF NOT EXISTS `#__vminvoice_config` (
				`id` int(3) NOT NULL auto_increment,
				`params` text,
				`last_appearance_change` int(11) DEFAULT 0,
				`template_body` TEXT,
				`template_items` TEXT,
				`template_header` TEXT,
				`template_footer` TEXT,
				`template_dn_body` TEXT,
				`template_dn_items` TEXT,
				`template_dn_header` TEXT,
				`template_dn_footer` TEXT,
				`template_restore` TEXT,
				`template_dn_restore` TEXT,
        `template_tax_header` TEXT,
        `template_tax_row` TEXT,
				PRIMARY KEY (`id`)
				) ;

				INSERT INTO `#__vminvoice_config` (`id`, `params`) VALUES (1,'') ON DUPLICATE KEY UPDATE `id`=`id`;
				
				
				UPDATE `#__vminvoice_config` SET `template_body` = IF(`template_body` IS NULL, '<table style="width: 100%;" border="0">
<tbody>
<tr>
<td><br />{start_note}<br /> 
<table style="width: 100%;" border="0">
<tbody>
<tr>
<td width="50%" valign="top">{billing_address}<br /></td>
<td width="50%" valign="top">{shipping_address}<br /></td>
</tr>
</tbody>
</table>
<br /> 
<table style="width: 100%;" border="0">
<tbody>
<tr>
<td width="50%" valign="middle">
<h1>{invoice_cpt} {invoice_number}</h1>
</td>
<td width="50%" align="right">{invoice_date_cpt}: {invoice_date}<br />{taxable_payment_date_cpt}: {taxable_payment_date}<br />{maturity_date_cpt}: {maturity_date}<br />{shipping_date_cpt}: {shipping_date}<br />{payment_type_cpt}: {payment_type}<br />{variable_symbol_cpt}: {variable_symbol}<br />{finnish_index_number_cpt}: {finnish_index_number}<br />{customer_number_cpt}: {customer_number}<br />{shopper_group_cpt}: {shopper_group}</td>
</tr>
</tbody>
</table></td>
</tr>
</tbody>
</table>{items}<br />{customer_note_cpt}: {customer_note} <br />{end_note} <br />{extra_fields}', `template_body`) WHERE id=1;

				UPDATE `#__vminvoice_config` SET `template_items` = IF(`template_items` IS NULL, '
<table style="width: 100%;" border="0"><tbody><tr><td style="width: 5%;" align="left">{qty_cpt}</td><td style="width: 10%;" align="center"></td><td style="width: 10%;" align="left">{sku_cpt}</td><td style="width: 35%;" align="left">{name_cpt}</td><td style="width: 10%;" align="right">{price_cpt}</td><td style="width: 10%;" align="right">{base_total_cpt}</td><td style="width: 10%;" align="right">{tax_rate_cpt}</td><td style="width: 10%;" align="right">{tax_cpt}</td><td style="width: 10%;" align="right">{discount_cpt}</td><td style="width: 15%;" align="right">{subtotal_cpt}</td></tr></tbody></table>_TEMPLATE_ITEMS_SEPARATOR_<table style="width: 100%;" border="0"><tbody><tr><td style="width: 5%;" align="left">{qty}{qty_unit}</td><td style="width: 10%;" align="center">{item_image}</td><td style="width: 10%;" align="left">{sku}</td><td style="width: 35%;" align="left">{name}<div style="font-size:70%;text-align:left">{attributes}</div></td><td style="width: 10%;" align="right">{price}</td><td style="width: 10%;" align="right">{price_notax}</td><td style="width: 10%;" align="right">{tax_rate}</td><td style="width: 10%;" align="right">{tax_price}</td><td style="width: 10%;" align="right">{discount}</td><td style="width: 15%;" align="right">{subtotal}</td></tr></tbody></table>
', `template_items`) WHERE id=1;

				UPDATE `#__vminvoice_config` SET `template_header` = IF(`template_header` IS NULL, '
<table style="width: 100%;" border="0">\r\n<tbody>\r\n<tr>\r\n<td style="font-size:75%" width="60%">{contact}</td>\r\n<td width="40%" align="right">{logo}</td>\r\n</tr>\r\n</tbody>\r\n</table>
', `template_header`) WHERE id=1;

				UPDATE `#__vminvoice_config` SET `template_footer` = IF(`template_footer` IS NULL, '
<p>{extra_fields}</p>\r\n<div style="text-align:center">{pagination}</div>\r\n<div style="text-align:center">{signature}</div>
', `template_footer`) WHERE id=1;

				UPDATE `#__vminvoice_config` SET `template_dn_body` = IF(`template_dn_body` IS NULL, '<table style="width: 100%;" border="0">
<tbody>
<tr>
<td><br />{start_note}<br /> 
<table style="width: 100%;" border="0">
<tbody>
<tr>
<td width="50%" valign="top">{shipping_address}<br /></td>
<td width="50%" valign="top">{billing_address}<br /></td>
</tr>
</tbody>
</table>
<br /> 
<table style="width: 100%;" border="0">
<tbody>
<tr>
<td width="50%" valign="middle">
<h1>{dn_cpt} {invoice_number}</h1>
</td>
<td width="50%" align="right">{invoice_date_cpt}: {invoice_date}<br />{taxable_payment_date_cpt}: {taxable_payment_date}<br />{maturity_date_cpt}: {maturity_date}<br />{shipping_date_cpt}: {shipping_date}<br />{payment_type_cpt}: {payment_type}<br />{variable_symbol_cpt}: {variable_symbol}<br />{finnish_index_number_cpt}: {finnish_index_number}<br />{customer_number_cpt}: {customer_number}<br />{shopper_group_cpt}: {shopper_group}</td>
</tr>
</tbody>
</table></td>
</tr>
</tbody>
</table>{items}<br />{customer_note_cpt}: {customer_note} <br />{end_note} <br />{extra_fields}', `template_dn_body`) WHERE id=1;

				UPDATE `#__vminvoice_config` SET `template_dn_items` =  IF(`template_dn_items` IS NULL, '
<table style="width: 100%;" border="0"><tbody><tr><td style="width: 10%;" align="left">{qty_cpt}</td><td style="width: 20%;" align="left">{sku_cpt}</td><td style="width: 70%;" align="left">{name_cpt}</td></tr></tbody></table>_TEMPLATE_ITEMS_SEPARATOR_<table style="width: 100%;" border="0"><tbody><tr><td style="width: 10%;" align="left">{qty}{qty_unit}</td><td style="width: 20%;" align="left">{sku}</td><td style="width: 70%;" align="left">{name}<div style="font-size:70%;text-align:left">{attributes}</div></td></tr></tbody></table>
', `template_dn_items`) WHERE id=1;

				UPDATE `#__vminvoice_config` SET `template_dn_header` =  IF(`template_dn_header` IS NULL, '
<table style="width: 100%;" border="0">\r\n<tbody>\r\n<tr>\r\n<td style="font-size:75%" width="60%">{contact}</td>\r\n<td width="40%" align="right">{logo}</td>\r\n</tr>\r\n</tbody>\r\n</table>
', `template_dn_header`) WHERE id=1;

				UPDATE `#__vminvoice_config` SET `template_dn_footer` =  IF(`template_dn_footer` IS NULL, '
<p>{extra_fields}</p>\r\n<div style="text-align:center">{pagination}</div>\r\n<div style="text-align:center">{signature}</div>
', `template_dn_footer`) WHERE id=1;

				UPDATE `#__vminvoice_config` SET `template_restore` = '<table style="width: 100%;" border="0">\r\n<tbody>\r\n<tr>\r\n<td style="font-size:75%" width="60%">{contact}</td>\r\n<td width="40%" align="right">{logo}</td>\r\n</tr>\r\n</tbody>\r\n</table>
_TEMPLATE_SEPARATOR_<table style="width: 100%;" border="0">
<tbody>
<tr>
<td><br />{start_note}<br /> 
<table style="width: 100%;" border="0">
<tbody>
<tr>
<td width="50%" valign="top">{billing_address}<br /></td>
<td width="50%" valign="top">{shipping_address}<br /></td>
</tr>
</tbody>
</table>
<br /> 
<table style="width: 100%;" border="0">
<tbody>
<tr>
<td width="50%" valign="middle">
<h1>{invoice_cpt} {invoice_number}</h1>
</td>
<td width="50%" align="right">{invoice_date_cpt}: {invoice_date}<br />{taxable_payment_date_cpt}: {taxable_payment_date}<br />{maturity_date_cpt}: {maturity_date}<br />{shipping_date_cpt}: {shipping_date}<br />{payment_type_cpt}: {payment_type}<br />{variable_symbol_cpt}: {variable_symbol}<br />{finnish_index_number_cpt}: {finnish_index_number}<br />{customer_number_cpt}: {customer_number}<br />{shopper_group_cpt}: {shopper_group}</td>
</tr>
</tbody>
</table>
</td>
</tr>
</tbody>
</table>{items}<br />{customer_note_cpt}: {customer_note} <br />{end_note} <br />{extra_fields}_TEMPLATE_SEPARATOR_<table style="width: 100%;" border="0"><tbody><tr><td style="width: 5%;" align="left">{qty_cpt}</td><td style="width: 10%;" align="center"></td><td style="width: 10%;" align="left">{sku_cpt}</td><td style="width: 35%;" align="left">{name_cpt}</td><td style="width: 10%;" align="right">{price_cpt}</td><td style="width: 10%;" align="right">{base_total_cpt}</td><td style="width: 10%;" align="right">{tax_rate_cpt}</td><td style="width: 10%;" align="right">{tax_cpt}</td><td style="width: 10%;" align="right">{discount_cpt}</td><td style="width: 15%;" align="right">{subtotal_cpt}</td></tr></tbody></table>_TEMPLATE_ITEMS_SEPARATOR_<table style="width: 100%;" border="0"><tbody><tr><td style="width: 5%;" align="left">{qty}{qty_unit}</td><td style="width: 10%;" align="center">{item_image}</td><td style="width: 10%;" align="left">{sku}</td><td style="width: 35%;" align="left">{name}<div style="font-size:70%;text-align:left">{attributes}</div></td><td style="width: 10%;" align="right">{price}</td><td style="width: 10%;" align="right">{price_notax}</td><td style="width: 10%;" align="right">{tax_rate}</td><td style="width: 10%;" align="right">{tax_price}</td><td style="width: 10%;" align="right">{discount}</td><td style="width: 15%;" align="right">{subtotal}</td></tr></tbody></table>
_TEMPLATE_SEPARATOR_<p>{extra_fields}</p>\r\n<div style="text-align:center">{pagination}</div>\r\n<div style="text-align:center">{signature}</div>' WHERE id=1;
				
				UPDATE `#__vminvoice_config` SET `template_dn_restore` = '<table style="width: 100%;" border="0">\r\n<tbody>\r\n<tr>\r\n<td style="font-size:75%" width="60%">{contact}</td>\r\n<td width="40%" align="right">{logo}</td>\r\n</tr>\r\n</tbody>\r\n</table>
_TEMPLATE_SEPARATOR_<table style="width: 100%;" border="0">
<tbody>
<tr>
<td><br />{start_note}<br /> 
<table style="width: 100%;" border="0">
<tbody>
<tr>
<td width="50%" valign="top">{shipping_address}<br /></td>
<td width="50%" valign="top">{billing_address}<br /></td>
</tr>
</tbody>
</table>
<br /> 
<table style="width: 100%;" border="0">
<tbody>
<tr>
<td width="50%" valign="middle">
<h1>{dn_cpt} {invoice_number}</h1>
</td>
<td width="50%" align="right">{invoice_date_cpt}: {invoice_date}<br />{taxable_payment_date_cpt}: {taxable_payment_date}<br />{maturity_date_cpt}: {maturity_date}<br />{shipping_date_cpt}: {shipping_date}<br />{payment_type_cpt}: {payment_type}<br />{variable_symbol_cpt}: {variable_symbol}<br />{finnish_index_number_cpt}: {finnish_index_number}<br />{customer_number_cpt}: {customer_number}<br />{shopper_group_cpt}: {shopper_group}</td>
</tr>
</tbody>
</table>
</td>
</tr>
</tbody>
</table>{items}<br />{customer_note_cpt}: {customer_note} <br />{end_note} <br />{extra_fields}_TEMPLATE_SEPARATOR_<table style="width: 100%;" border="0"><tbody><tr><td style="width: 10%;" align="left">{qty_cpt}</td><td style="width: 20%;" align="left">{sku_cpt}</td><td style="width: 70%;" align="left">{name_cpt}</td></tr></tbody></table>_TEMPLATE_ITEMS_SEPARATOR_<table style="width: 100%;" border="0"><tbody><tr><td style="width: 10%;" align="left">{qty}{qty_unit}</td><td style="width: 20%;" align="left">{sku}</td><td style="width: 70%;" align="left">{name}<div style="font-size:70%;text-align:left">{attributes}</div></td></tr></tbody></table>
_TEMPLATE_SEPARATOR_<p>{extra_fields}</p>\r\n<div style="text-align:center">{pagination}</div>\r\n<div style="text-align:center">{signature}</div>' WHERE id=1;
				



				
				CREATE TABLE IF NOT EXISTS `#__vminvoice_mailsended` (
				`id` int(11) NOT NULL auto_increment,
				`order_id` int(11) NOT NULL,
				`invoice_prefix` varchar(20) NULL DEFAULT NULL,
				`invoice_no` int(11),
				`invoice_mailed` int(1) DEFAULT 0,
				`dn_mailed` int(1) DEFAULT 0,
				`invoice_generated` int(11) DEFAULT 0,
				`dn_generated` int(11) DEFAULT 0,
				`invoice_date` int(11) DEFAULT 0,
				`invoice_lastchanged` int(11) DEFAULT 0,
        `params` TEXT NOT NULL,
				PRIMARY KEY (`id`),
				UNIQUE KEY `order_id` (`order_id`)
				);

				CREATE TABLE IF NOT EXISTS `#__vminvoice_additional_field` (
				`id` int(11) NOT NULL AUTO_INCREMENT,
				`bank_name` varchar(110) NOT NULL,
				`account_nr` varchar(110) NOT NULL,
				`bank_code_no` varchar(110) NOT NULL,
				`bic_swift` varchar(110) NOT NULL,
				`iban` varchar(110) NOT NULL,
				`tax_number` varchar(110) NOT NULL,
				`vat_id` varchar(110) NOT NULL,
				`registration_court` varchar(110) NOT NULL,
				`phone` varchar(110) NOT NULL,
				`email` varchar(110) NOT NULL,
				`web_url` varchar(110) NOT NULL,
				`note_start` text NOT NULL,
				`note_end` text NOT NULL,
				`show_bank_name` int(1) NOT NULL,
				`show_account_nr` int(1) NOT NULL,
				`show_bank_code_no` int(1) NOT NULL,
				`show_bic_swift` int(1) NOT NULL,
				`show_iban` int(1) NOT NULL,
				`show_vat_id` int(1) NOT NULL,
				`show_tax_number` int(1) NOT NULL,
				`show_registration_court` int(1) NOT NULL,
				`show_phone` int(1) NOT NULL,
				`show_email` int(1) NOT NULL,
				`show_web_url` int(1) NOT NULL,
				`last_fields_change` int(11) DEFAULT 0,
				PRIMARY KEY (`id`)
				);

				INSERT INTO `#__vminvoice_additional_field`(`id`, `bank_name`,
				`account_nr`, `bank_code_no`, `bic_swift`,
				`iban`, `tax_number`, `vat_id`, `registration_court`, `phone`, `email`,
				`web_url`, `note_start`, `note_end`,
				`show_bank_name`, `show_account_nr`, `show_bank_code_no`,
				`show_bic_swift`,
				`show_iban`, `show_vat_id`, `show_tax_number`, `show_registration_court`,
				`show_phone`,
				`show_email`, `show_web_url`) VALUES
				(1, '', '', '', '', '', '', '', '', '', '', '', '', '', 0, 0, 0, 0, 0,
				0, 0, 0, 0, 0, 0) ON DUPLICATE KEY UPDATE `id`=`id`;
