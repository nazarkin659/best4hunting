<?php
/**
 * IceCarousel Extension for Joomla 1.6 By IceTheme
 * 
 * 
 * @copyright	Copyright (C) 2008 - 2011 IceTheme.com. All rights reserved.
 * @license		GNU General Public License version 2
 * 
 * @Website 	http://www.icetheme.com/Joomla-Extensions/icecarousel.html
 * @Support 	http://www.icetheme.com/Forums/IceCarousel/
 *
 */

/* no direct access*/
defined('_JEXEC') or die;
if( modIceCarousel::checkIceAjax() ):
$list = $pages[ $page - 1];
$key = $page - 1;
endif;

?>

<div class="ice-main-item page-<?php echo $key+1;?>">
    <?php foreach( $list as $i => $item ): 
			$ratingModel = VmModel::getModel('ratings');
			$vm_rating = $ratingModel->getRatingByProduct($item->virtuemart_product_id);
	?>
     <div class="ice-row" style="width:<?php echo $itemWidth;?>%">
		<div class="lof-inner">
        	<h4 class="heading">
			<a <?php echo $target;?>  href="<?php echo $item->link;?>" title="<?php echo $item->title;?>"><?php echo $item->title ?></a></h4>
				<div class="carousel_content">
                	<div class="padding vm_padding">
					
					<div class="<?php echo $vm_show_preview?'iceTip':''; ?>" rel="<?php echo $vm_show_preview?$item->thumbnail:'';?>" title="<?php echo  $vm_show_preview?$item->title:'';?>">
					<?php
						if($vm_show_feature_icon): ?>
							 <?php if ($item->product_special){?>
                                    <div class="product_label">
                                        <span class="lof_featured"><?php echo JText::_("Featured"); ?></span>
                                    </div>
                             <?php }?>
					<?php
						endif;
					?>
					<?php if($vm_show_product_image): ?>
                    <?php if( $params->get('show_readmore','0') ) : ?>
                          <a <?php echo $target;?>  href="<?php echo $item->link;?>" title="<?php echo $item->title;?>">
						  <?php endif; ?>
						
							<?php echo $item->mainImage; ?>
                          
                          <?php if( $params->get('show_readmore','0') ) : ?>
                          </a>
						 <?php endif; ?>
                         
					<?php endif; ?>
                    </div>
					 <?php if( $vm_show_current_stock ): ?>
						<div class="vm_current_stock"><?php echo JText::_("CURRENT_STOCK").$item->product_in_stock; ?></div>
					<?php endif; ?>
                    <?php if( $vm_show_base_price ): ?>
						<div class="vm_old_price"><?php echo JText::_("OLD_PRICE").$item->product_old_price; ?></div>
					<?php endif; ?>
                    
					<?php if( $vm_show_price ): ?>
						<div class="vm_price"><?php echo JText::_("SALE_PRICE").$item->product_price; ?></div>
					<?php endif; ?>
					
                    <?php if($vm_show_description): ?>
						<div class="description"><?php echo $item->description; ?></div>
					<?php endif; ?>
                    
					<?php if( $vm_show_rating ):  
						$maxrating = VmConfig::get('vm_maximum_rating_scale',5);
						if (empty($vm_rating)) { ?>
							<span class="vote"><?php echo JText::_('COM_VIRTUEMART_RATING').' '.JText::_('COM_VIRTUEMART_UNRATED') ?></span>
						<?php } else {
							$ratingwidth = ( $vm_rating->rating * 100 ) / $maxrating;//I don't use round as percetntage with works perfect, as for me
							?>
							<span class="vote">
								<?php echo JText::_('COM_VIRTUEMART_RATING').' '.round($vm_rating->rating, 2) . '/'. $maxrating; ?><br/>
								<span title=" <?php echo (JText::_("COM_VIRTUEMART_RATING_TITLE") . $vm_rating->rating . '/' . $maxrating) ?>" class="vmicon ratingbox" style="display:inline-block;">
									<span class="stars-orange" style="width:<?php echo $ratingwidth;?>%">
									</span>
								</span>
							</span>
							<?php
						} 
					endif; ?>
					<?php if( $params->get('show_readmore','0') ) : ?>
                    	 <p class="readmore">
                          <a <?php echo $target;?>  href="<?php echo $item->link;?>" title="<?php echo $item->title;?>"><?php echo JText::_('READ_MORE');?></a>
                          </p>
					<?php endif; ?>
					<?php
						 if ($vm_show_cart) echo modIceCarousel::addtocart($item, $vm_show_quantity);
					?>
                    </div>
                </div>
		</div>
	</div>
    <?php endforeach; ?>
</div>