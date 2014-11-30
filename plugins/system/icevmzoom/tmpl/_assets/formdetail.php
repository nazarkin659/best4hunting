<?php 
/**
 * IceVmZoom Plugin for Joomla 2.5 By IceTheme
 * 
 * 
 * @copyright	Copyright (C) 2008 - 2012 IceTheme.com. All rights reserved.
 * @license		GNU General Public License version 2
 * 
 * @Website 	http://www.icetheme.com/Joomla-Extensions/IceVmZoom.html
 * @Support 	http://www.icetheme.com/Forums/IceVmZoom/
 *
 */
// no direct access
defined('_JEXEC') or die;


echo $pane->startPanel(_JSHOP_INFO_PRODUCT, 'main-page');
?>
    <div class="col100" style="background: #FFFFFF;">
     <table class="admintable" width="90%">
    
     <tr>
       <td class="key" style="width:180px;">
          <?php echo _JSHOP_PUBLISH;?>
       </td>
       <td>
         <input type = "checkbox" name = "product_publish" id = "product_publish" value = "1" <?php if ($row->product_publish) echo 'checked = "checked"'?> />
       </td>
     </tr>
    
     <tr>
       <td class="key">
          <?php echo JText::_('Sale Price')?>
       </td>
       <td>
         <input type = "text" name = "product_price" id = "product_price" value = "<?php echo $row->product_price?>" <?php if (!$this->withouttax){?> onkeyup = "updatePrice2(<?php print $jshopConfig->display_price_admin;?>)" <?php }?> /> <?php echo $jshopConfig->currency_code; ?>
       </td>
     </tr>
     <?php if (!$this->withouttax){?>
     <tr>
       <td class="key">
         <?php if ($jshopConfig->display_price_admin==0) echo JText::_('Price (netto)'); else echo _JSHOP_PRODUCT_BRUTTO_PRICE;?>
       </td>
       <td>
         <input type = "text" id = "product_price2" value = "<?php echo $row->product_price2;?>" onkeyup = "updatePrice(<?php print $jshopConfig->display_price_admin;?>)" /> <?php echo $jshopConfig->currency_code;?>
       </td>
     </tr>
     <?php }?>
     <tr style="display: none !important;">
       <td class="key">
         <?php echo _JSHOP_PRODUCT_ADD_PRICE;?>
       </td>
       <td>
         <input type = "checkbox" name = "product_is_add_price" id = "product_is_add_price" value = "1" <?php if ($row->product_is_add_price) echo 'checked = "checked"';?>  onclick = "showHideAddPrice()" />
       </td>
     </tr>
     <tr id = "tr_add_price" style="display: none;">
        <td class="key"><?php echo JText::_('Price per consignment')?></td>
         <td>
            <table id = "table_add_price" class = "adminlist">
            <thead>
     <tr>
                    <th>
                        <?php echo _JSHOP_PRODUCT_QUANTITY_START;?>    
                    </th>
                    <th>
                        <?php echo _JSHOP_PRODUCT_QUANTITY_FINISH;?>    
                    </th>
                    <th>
                        <?php echo _JSHOP_DISCOUNT;?>
                        (<?php if ($jshopConfig->product_price_qty_discount==1) echo $currency->currency_code; else print "%";?>)    
                    </th>
                    <th>
                        <?php echo _JSHOP_PRODUCT_PRICE;?> (<?php echo $currency->currency_code;?>)
                    </th>                    
                    <th>
                        <?php echo _JSHOP_DELETE;?>    
                    </th>
                </tr>                </thead>                
                <?php 
                $add_prices = $row->product_add_prices;
                $count = count($add_prices);
                for ($i = 0; $i < $count; $i++){
                    if ($jshopConfig->product_price_qty_discount==1){
                        $_add_price = $row->product_price - $add_prices[$i]->discount;
                    }else{
                        $_add_price = $row->product_price - ($row->product_price * $add_prices[$i]->discount / 100);
                    }
                    $_add_price = number_format($_add_price,2,".","");
                    ?>
                    <tr id="add_price_<?php print $i?>">
                        <td>
                            <input type = "text" name = "quantity_start[]" id="quantity_start_<?php print $i?>" value = "<?php echo $add_prices[$i]->product_quantity_start;?>" />    
                        </td>
                        <td>
                            <input type = "text" name = "quantity_finish[]" id="quantity_finish_<?php print $i?>" value = "<?php echo $add_prices[$i]->product_quantity_finish;?>" />    
                        </td>
                        <td>
                            <input type = "text" name = "product_add_discount[]" id="product_add_discount_<?php print $i?>" value = "<?php echo $add_prices[$i]->discount;?>" onkeyup="productAddPriceupdateValue(<?php print $i?>)" />    
                        </td>
                        <td>
                            <input type = "text" id="product_add_price_<?php print $i?>" value = "<?php echo $_add_price;?>" onkeyup="productAddPriceupdateDiscount(<?php print $i?>)" />
                        </td>
                        <td align="center">
                            <a href="#" onclick="delete_add_price(<?php print $i?>);return false;"><img src="images/publish_x.png" border="0"/></a>
                        </td>
                    </tr>
                    <?php
                }
                ?>                
            </table>
            <table  class = "adminlist">
            <tr>
                <td align = "right">
                    <input class = "button" type = "button" name = "add_new_price" onclick = "addNewPrice()" value = " <?php echo JText::_('Add Price')?>" style="float:right;" />
                </td>
            </tr>
            </table>
            <script type="text/javascript">
            <?php 
            print "var add_price_num = $i;";
            print "var config_product_price_qty_discount = ".$jshopConfig->product_price_qty_discount.";";
            ?>             
            </script>
        </td>
     </tr>
     
     <tr>
       <td class="key">
        <?php echo JText::_('Original Price')?>
       </td>
       <td>
         <input type = "text" name = "product_old_price" id = "product_old_price" value = "<?php echo $row->product_old_price?>" /> <?php echo $jshopConfig->currency_code; ?>
       </td>
     </tr>
     <tr>
       <td class="key">
  <?php echo _JSHOP_PRODUCT_WEIGHT;?>       </td>
       <td>
         <input type = "text" name = "product_weight" id = "product_weight" value = "<?php echo $row->product_weight?>" /> <?php print _JSHOP_WEIGHT_UNIT;?>
       </td>
     </tr>     
     <tr>
       <td class="key">
         <?php echo _JSHOP_EAN_PRODUCT;?>
       </td>
       <td>
         <input type = "text" name = "product_ean" id = "product_ean" value = "<?php echo $row->product_ean?>" onkeyup="updateEanForAttrib()"; />
       </td>
     </tr>
     <tr>
       <td class="key">
         <?php echo _JSHOP_QUANTITY_PRODUCT;?>
       </td>
       <td>
         <div id="block_enter_prod_qty" style="padding-bottom:2px;<?php if ($row->unlimited) print "display:none;";?>">
             <input type = "text" name = "product_quantity" id = "product_quantity" value = "<?php echo $row->product_quantity?>" <?php if ($this->product_with_attribute){?>readonly="readonly"<?php }?> />
             <?php if ($this->product_with_attribute){ echo JHTML::tooltip(_JSHOP_INFO_PLEASE_EDIT_AMOUNT_FOR_ATTRIBUTE); } ?>
         </div>
         <div>         
            <input type="checkbox" name="unlimited" value="1" onclick="ShowHideEnterProdQty(this.checked)" <?php if ($row->unlimited) print "checked";?> /> <?php print _JSHOP_UNLIMITED;?>
         </div>         
       </td>
     </tr>

     <tr>
       <td class="key"><?php echo JText::_('URL')?></td>
       <td>
         <input type = "text" name = "product_url" id = "product_url" value = "<?php echo $row->product_url?>" size="80" />
       </td>
     </tr>
     
     <?php if ($jshopConfig->use_different_templates_cat_prod) { ?>
     <tr>
       <td class="key">
         <?php echo _JSHOP_TEMPLATE_PRODUCT;?>
       </td>
       <td>
         <?php echo $lists['templates'];?>
       </td>
     </tr>
     <?php } ?>
     
     <?php if (!$this->withouttax){?>
     <tr>     
       <td class="key">
         <?php echo JText::_('Tax title')?>
       </td>
       <td>
         <?php echo $lists['tax'];?>
       </td>
     </tr>
     <?php }?>
     <tr>
       <td class="key">
         <?php echo JText::_('Manufacturer name')?>
       </td>
       <td>
         <?php echo $lists['manufacturers'];?>
       </td>
     </tr>

     <?php if ($jshopConfig->admin_show_vendors && $this->display_vendor_select) { ?>
     <tr>
       <td class="key">
         <?php echo _JSHOP_VENDOR;?>
       </td>
       <td>
         <?php echo $lists['vendors'];?>
       </td>
     </tr>
     <?php }?>
     
     <?php if ($jshopConfig->admin_show_delivery_time) { ?>
     <tr>
       <td class="key">
         <?php echo JText::_('Delivery time')?>
       </td>
       <td>
         <?php echo $lists['deliverytimes'];?>
       </td>
     </tr>
     <?php }?>
     
     <?php if ($jshopConfig->admin_show_product_labels) { ?>
     <tr>
       <td class="key">
         <?php echo JText::_('Labels')?>
       </td>
       <td>
         <?php echo $lists['labels'];?>
       </td>
     </tr>
     <?php }?>
     
     <?php if ($jshopConfig->admin_show_product_basic_price) { ?>
     <tr>
       <td class="key"><br/><?php echo _JSHOP_BASIC_PRICE;?></td>
     </tr>
     <tr>
       <td class="key">
         <?php echo _JSHOP_WEIGHT_VOLUME_UNITS;?>
       </td>
       <td>
         <input type = "text" name = "weight_volume_units" value = "<?php echo $row->weight_volume_units?>" />
       </td>
     </tr>
     <tr>
       <td class="key">
         <?php echo _JSHOP_UNIT_MEASURE;?>
       </td>
       <td>
         <?php echo $lists['basic_price_units'];?>
       </td>
     </tr>
     <?php }?>
     <tr>
        <td class="key">
            <?php echo JText::_('Start Date?')?>
        </td>
        <td>
              <?php echo JHTML::_( 'calendar',$params->get('start_date'), 'params[start_date]', 'paramsstart_date', '%Y-%m-%d %H:%M:%S'); ?>
        </td>
     </tr>
     <tr>     
        <td class="key">
              <?php echo JText::_('End Date?')?>
        </td>
        <td>
              <?php echo JHTML::_( 'calendar',$params->get('end_date'), 'params[end_date]', 'paramsend_date', '%Y-%m-%d %H:%M:%S'); ?>
        </td>
     </tr>
   </table>
   </div>   
   <div class="clr"></div>

