<?php
/**
 * IceVMCart Extension for Joomla 2.5 By IceTheme
 * 
 * 
 * @copyright	Copyright (C) 2008 - 2012 IceTheme.com. All rights reserved.
 * @license		GNU General Public License version 2
 * 
 * @Website 	http://www.icetheme.com/Joomla-Extensions/icevmcart.html
 * @Support 	http://www.icetheme.com/Forums/IceVmCart/
 *
 */
// No direct access
defined('_JEXEC') or die;

 ?>
<?php $dropdown = $params->get('dropdown',1); ?>
<span class="ice_store_dropdown" style="display:none"><?php echo $dropdown; ?></span>
<div id="vm_module_cart" class="iceVmCartModule">
	<div class="lof_vm_top">
        
         <?php if( count( $data->products) == 0): ?>
         <p class="vm_cart_empy"><?php echo $vm_cart_empy ?></p>
         
         <?php else:?>
           
        <div class="lof_top_1">
			<span class="vm_products"><?php echo  $data->totalProductTxt ?>&nbsp;</span>
            <span class="vm_sum"><?php if ($data->totalProduct) echo  $data->billTotal; ?></span>
		</div>
        
		<div class="lof_top_2">
	    	<?php if ($data->totalProduct) echo  $data->cart_show; ?>
			<?php if($dropdown){ ?>
				<?php if( count( $data->products) == 0): ?>
					<p class="vm_cart_empy"><?php echo $vm_cart_empy ?></p>
				<?php else:?>
					<a class="vm_readmore showmore" href = "javascript:void(0)"><?php print JText::_('SHOW_MORE')?></a>
				<?php endif; ?>
			<?php } ?>
		</div>
        
       
        <?php endif; ?>
        
	</div>
	<?php
		if($dropdown){
	?>
	<div class="lof_vm_bottom" style="display:none;">
		<?php 
		foreach ($data->products as $product){
		?><div style="clear:both;"></div> 
		<div class="lof_item">
			<a href="<?php echo $product["link"];?>"><img src="<?php echo $product["image"]; ?>" alt="<?php print htmlspecialchars($product['product_name']);?>"/></a>
			<div class="lof_info">
				<a href = "<?php echo $product["link"]; ?>" title = "<?php print htmlspecialchars($product['product_name']);?>">
					<?php print htmlspecialchars($product['product_name']);?>
				</a>
				<span class="lof_quantity"><?php print JText::_('QUANTITY')?> : <?php echo  $product['quantity'] ?></span>
				<span class="lof_price"><?php print JText::_('PRICE')?> : <?php echo  $product['prices'] ?></span>
			</div>
		</div>
		<?php }?>
		<div class="lof_vm_bottom_btn">
			<?php if ($data->totalProduct) echo  $data->cart_show; ?>
			<a class="lofclose" href = "javascript:void(0)"><?php print JText::_('CLOSE')?></a>
		</div>
	</div>
	<?php } ?>
	<script language="javascript">
		jQuery(document).ready(function(){
			<?php if($dropdown){ ?>
			jQuery('.lof_vm_top .showmore').click(function(){
				if(jQuery(this).hasClass('showmore')){
					jQuery('.lof_vm_bottom').slideDown("slow");
					jQuery(this).text('<?php print JText::_('SHOW_LESS')?>');
					$(this).removeClass('showmore').addClass('showless');
				}else{
					jQuery('.lof_vm_bottom').slideUp();
					jQuery(this).text('<?php print JText::_('SHOW_MORE')?>');
					$(this).removeClass('showless').addClass('showmore');
				}
			});
			jQuery('.lof_vm_bottom_btn .lofclose').click(function(){
				jQuery('.lof_vm_bottom').slideUp();
				jQuery('.lof_vm_top .lof_top_2 .showless').text('<?php print JText::_('SHOW_MORE')?>');
				jQuery('.lof_vm_top .lof_top_2 .showless').removeClass('showless').addClass('showmore');
			});
			<?php } ?>
			jQuery('#main').find('.vm table.cart').addClass("cart-full");
			
		});	
	</script>
</div>
