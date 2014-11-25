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
    <?php foreach( $list as $i => $item ): ?>
     <div class="ice-row" style="width:<?php echo $itemWidth;?>%">
		<div class="lof-inner">
        	<h4 class="heading">
			<a <?php echo $target;?>  href="<?php echo $item->link;?>" title="<?php echo $item->title;?>"><?php echo $item->title ?></a></h4>
				<div class="carousel_content">
                	<div class="padding jshopping_padding">
					
					<div class="<?php echo $show_preview?'iceTip':''; ?>" rel="<?php echo $show_preview?$item->thumbnail:'';?>" title="<?php echo  $show_preview?$item->title:'';?>">
					
					<?php
						if($show_image_label): ?>
							 <?php if ($item->label_id && getNameImageLabel($item->label_id)){?>
                                    <div class="product_label">
                                        <img src="<?php echo $jshopConfig->image_labels_live_path."/".getNameImageLabel($item->label_id); ?>" alt="<?php echo getNameImageLabel($item->label_id, 2)?>" />
                                    </div>
                             <?php }?>
					<?php
						endif;
					?>
					<?php if($show_product_image): ?>
                    <?php if( $params->get('show_readmore','0') ) : ?>
                          <a <?php echo $target;?>  href="<?php echo $item->link;?>" title="<?php echo $item->title;?>">
						  <?php endif; ?>
						
							<?php echo $item->mainImage; ?>
                          
                          <?php if( $params->get('show_readmore','0') ) : ?>
                          </a>
						 <?php endif; ?>
                         
					<?php endif; ?>
                    </div>
					                    
                    <?php if( $show_old_price ): ?>
						<div class="jshop_old_price"><?php echo JText::_("OLD_PRICE").$item->product_old_price; ?></div>
					<?php endif; ?>
                    
					<?php if( $show_price ): ?>
						<div class="jshop_price"><?php echo $item->product_price; ?></div>
					<?php endif; ?>
					
                    <?php if($show_description): ?>
						<div class="description"><?php echo $item->description; ?></div>
					<?php endif; ?>
                    
					<?php if( $show_rating ): ?>
                         <table class="review_mark"><tr><td>                            
                            <?php print showMarkStar($item->average_rating);?>
                            </td></tr></table>
                            <div class="count_commentar">
                                <?php print sprintf(_JSHOP_X_COMENTAR, $item->reviews_count);?>
                            </div>
					<?php endif; ?>
					<?php if( $params->get('show_readmore','0') ) : ?>
                    	 <p class="readmore">
                          <a <?php echo $target;?>  href="<?php echo $item->link;?>" title="<?php echo $item->title;?>"><?php echo JText::_('READ_MORE');?></a>
                          </p>
					<?php endif; ?>
                    </div>
                </div>
		</div>
	</div>
    <?php endforeach; ?>
</div>