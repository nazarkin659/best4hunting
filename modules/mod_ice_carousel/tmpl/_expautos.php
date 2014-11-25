<?php
/**
 * IceCarousel Extension for Joomla 2.5 By IceTheme
 * 
 * 
 * @copyright	Copyright (C) 2008 - 2012 IceTheme.com. All rights reserved.
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

	
$imageHeight   = (int)$params->get( 'main_height', 300 ) ;
$imageWidth    = (int)$params->get( 'main_width', 900 ) ;
?>

<div class="ice-main-item page-<?php echo $key+1;?>">
    <?php foreach( $list as $i => $item ): 
		
	?>
     <div class="ice-row" style="width:<?php echo $itemWidth;?>%">
		<div class="lof-inner">
        	<div class="padding">
        	<h4 class="heading">
			<a <?php echo $target;?>  href="<?php echo $item->link;?>" title="<?php echo $item->title;?>"><?php echo $item->title ?></a></h4>
				<div class="carousel_content">
                	<div class="padding exp_padding">
					 <?php
								$imgclass = "";
								if($exp_display_icons): 
									if ($item->fcommercial)
										$imgclass = "commercial";
									elseif ($item->ftop)
										$imgclass = "top";
									elseif ($item->special)
										$imgclass = "special";
									elseif ($item->solid)
										$imgclass = "solid";
									else
										$imgclass="";
								endif;
							?>
					<div class="<?php echo $exp_show_preview?'iceTip':''; ?>" style=" width:<?php echo  $exppreviewWidth ;?>px ;height: <?php echo $exppreviewHeight;?>px" rel="<?php echo $exp_show_preview?$item->thumbnail:'';?>" title="<?php echo  $exp_show_preview?$item->title:'';?>">
						<div class="photo <?php echo $imgclass; ?>">
							<?php if( $params->get('show_readmore','0') ) : ?>
								  <a <?php echo $target;?>  href="<?php echo $item->link;?>" title="<?php echo $item->title;?>">
								  <?php endif; ?>
								  <?php if($exp_display_icons): ?>
									<span>&nbsp;</span>
									<?php endif; ?>
									<?php echo $item->mainImage; ?>
								  
								  <?php if( $params->get('show_readmore','0') ) : ?>
								  </a>
								 <?php endif; ?>
							</div> 
                    </div>

                     <?php if ($exp_show_reserved && $item->expreserved): ?>
                            <span class="expreserved" title="<?php echo JText::_('EXP_RESERVED_TEXT') ?>"></span>
					<?php endif; ?>	
                    <?php if( $exp_show_year ): ?>
						<div class="exp_year"><span class="label"><?php echo JText::_("EXP_YEAR").'</span>'.$item->year; ?></div>
					<?php endif; ?>
                    
					<?php if( $exp_show_fuel ): ?>
						<div class="exp_fuel"><span class="label"><?php echo JText::_("EXP_FUEL")."</span><span>".$item->fuel_name."</span>"; ?></div>
					<?php endif; ?>
					
                    <?php if($exp_show_mileage): ?>
						<div class="exp_mileage"><span class="label"><?php echo JText::_("EXP_MILEAGE");?></span><span><?php echo $item->mileage; ?></span></div>
					<?php endif; ?>
					 <?php if($exp_show_price): ?>
						<div class="exp_price"><span class="label"><?php echo JText::_("EXP_PRICE");?></span><span><?php echo $item->price; ?></span></div>
					<?php endif; ?>
					
					<?php if( $params->get('show_readmore','0') ) : ?>
                    	 <p class="readmore">
                          <a <?php echo $target;?>  href="<?php echo $item->link;?>" title="<?php echo $item->title;?>"><?php echo JText::_('VM_PRODUCT_DETAIL');?></a>
                          </p>
					<?php endif; ?>
                    </div>
                </div>
                 </div>
		</div>
	</div>
    <?php endforeach; ?>
</div>