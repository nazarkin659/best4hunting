<?php
/**
 * @package Sj Vm Extra Slider responsive
 * @version 2.5
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @copyright (c) 2013 YouTech Company. All Rights Reserved.
 * @author YouTech Company http://www.smartaddons.com
 * 
 */
    defined('_JEXEC') or die;
    $vm_currency_display = &CurrencyDisplay::getInstance();
    $image_items_config = array(
    		'output_width'  => $params->get('item_image_width'),
    		'output_height' => $params->get('item_image_height'),
    		'function'		=> $params->get('item_image_function'),
    		'background'	=> $params->get('item_image_background')
    );    
    $options=$params->toObject();
	$count_item = count($items);
	$item_of_page = $options->num_rows * $options->num_cols;
	$suffix = rand().time();
	$tag_id = 'sjextraslider_'.$suffix;	
	   
	if(!empty($items)){?>
    <div id="<?php echo $tag_id;?>" class="sj-extraslider <?php if( $options->effect == 'slide' ){ echo $options->effect;}?> preset02-<?php echo $options->num_cols; ?>" data-pause='hover'>
		<?php if(!empty($options->pretext)) { ?>
			<div class="pre-text"><?php echo $options->pretext; ?></div>
		<?php } ?> 
        <?php if($options->title_slider_display == 1){?>
            <div class="heading-title"><?php echo $options->title_slider;?></div><!--end heading-title-->
        <?php }?>		    
    	<div class="extraslider-control  <?php if( $options->button_page == 'under' ){echo 'button-type2';}?>">
		    <a class="button-prev" href="<?php echo '#'.$tag_id;?>" data-jslide="prev"></a>
		    <?php if( $options->button_page == 'top' ){?>
		    <ul class="nav-page">
		    <?php $j = 0;$page = 0;
		    	foreach ($items as $item){$j ++;
				$active_class = $page == 0 ? " active" : "";
		    		if( $j%$item_of_page == 1 || $item_of_page == 1 ){$page ++;?>
		    		<li class="page">
		    			<a class="button-page <?php if( $page==1 ){echo 'sel';}?>" href="<?php echo '#'.$tag_id;?>" data-jslide="<?php echo $page-1;?>"></a>
		    		</li>
	    		<?php }}?>
		    </ul>
		    <?php }?>
		    <a class="button-next" href="<?php echo '#'.$tag_id;?>" data-jslide="next"></a>
	    </div>
	    <div class="extraslider-inner">
	    <?php $count = 0; $i = 0; 
	    foreach($items as $item){$count ++; $i++;?>
            <?php if($count%$item_of_page == 1 || $item_of_page == 1){?>
            <div class="item <?php if($i==1){echo "active";}?>">
            <?php }?>
                <?php if($count%$options->num_cols == 1 || $options->num_cols == 1 ){?>
                <div class="line">
                <?php }?>  
                
				    <div class="item-wrap <?php echo $options->theme; if($count%$options->num_cols == 0 || $count== $count_item && $options->num_cols !=1){echo " last";}?> ">
				    	<div class="item-image">
                            <a href="<?php echo $item->link;?>" <?php echo YTools::parseTarget($options->item_link_target);?> >
                                <img src="<?php echo YTools::resize($item->images, $image_items_config);?>" alt="<?php echo $item->product_name;?>" title="<?php echo $item->product_name;?>"/>
                            </a>
				    	</div>
			    	<?php if( $options->item_title_display == 1 || $options->item_desc_display == 1  || $options->item_price_display == 1 || $options->item_readmore_display == 1 ){?>
				    	<div class="item-info">
				    	<?php if( $options->item_title_display == 1 ){?>
				    		<div class="item-title">
                                <a href="<?php echo $item->link;?>" <?php echo YTools::parseTarget($options->item_link_target);?>>
                                    <?php echo Ytools::truncate($item->product_name,$options->item_title_max_characs);?>
                            	</a>
				    		</div>
			    		<?php }?>
			    		<?php if( ($options->item_desc_display == 1 && !empty($item->product_s_desc)) || $options->item_price_display == 1 || $options->item_readmore_display == 1 ){?>
                            <div class="item-content">
                            <?php if( $options->item_desc_display == 1 ){?>
                                <div class="item-description">
									<?php
									 	$desc = "";
										if(!empty($item->product_s_desc)){
											YTools::extractImages($item->product_s_desc);
											$desc = $item->product_s_desc;	
										}else{
											YTools::extractImages($item->product_desc);
											$desc = $item->product_desc;	
										}
										if ( (int)$params->get('item_description_striptags', 1) ){
											$keep_tags = $params->get('item_description_keeptags', '');
											$keep_tags = str_replace(array(' '), array(''), $keep_tags);
											$tmp_desc = strip_tags($desc ,$keep_tags );
											echo YTools::truncate($tmp_desc, (int)$params->get('item_desc_max_characs'));
										} else {
											echo YTools::truncate($desc, (int)$params->get('item_desc_max_characs'));
										}?>                                
                                </div>
                            <?php }?>
                            
                                <?php if($options->item_price_display == 1){ ?>
           							<div class="item-price">
           								<div class="sale-price">
           									<?php	$currency = &CurrencyDisplay::getInstance();
												if ( !empty($item->prices['salesPrice']) ){
													echo $currency->createPriceDiv('salesPrice', '', $item->prices, true);
												}
												if ( !empty($item->prices['salesPriceWithDiscount']) ){
													echo $currency->createPriceDiv('salesPriceWithDiscount', '', $item->prices, true);
												}
											?>
           								</div>
           							</div>
           						<?php } ?>                             
                            
                            <?php if( $options->item_readmore_display == 1 ){?>
                                <div class="item-readmore">
			                        <a href="<?php echo $item->link;?>" target = "<?php echo $options->item_link_target;?>">
		                            	<?php echo $options->item_readmore_text;?>
			                        </a>                                
                                </div> 
                            <?php }?>                               
                            </div>
                        <?php }?>
				    	</div>
			    	<?php }?>
				    </div>                
                 
                <?php if($count%$options->num_cols == 0 || $count== $count_item){?>    
                </div><!--line-->
                <?php } ?>		    		
            <?php if(($count%$item_of_page == 0 || $count== $count_item)){?>    
            </div><!--end item--> 
            <?php }?>
	    <?php }?>
	    </div><!--end extraslider-inner -->
	    <?php if( $options->button_page == 'under' ){?>
	    <ul class="nav-page nav-under">
	    <?php $j = 0;$page = 0;
	    	foreach ($items as $item){$j ++;
			$active_class = $page == 0 ? " active" : "";
	    		if( $j%$item_of_page == 1 || $item_of_page == 1 ){$page ++;?>
	    		<li class="page">
	    			<a class="button-page <?php if( $page==1 ){echo 'sel';}?>" href="<?php echo '#'.$tag_id;?>" data-jslide="<?php echo $page-1;?>"></a>
	    		</li>
    		<?php }}?>
	    </ul>
	    <?php }?>	    
		<?php if(!empty($options->posttext)) {  ?>
			<div class="post-text"><?php echo $options->posttext; ?></div>
		<?php }?>
    </div>
<?php }else{ echo JText::_('Has no item to show!');}?>

<script>
//<![CDATA[    					
	jQuery(function($){
		$('#<?php echo $tag_id;?>').each(function(){
			var $this = $(this), options = options = !$this.data('modal') && $.extend({}, $this.data());
			$this.jcarousel(options);
			$this.bind('jslide', function(e){
				var index = $(this).find(e.relatedTarget).index();

				// process for nav
				$('[data-jslide]').each(function(){
					var $nav = $(this), $navData = $nav.data(), href, $target = $($nav.attr('data-target') || (href = $nav.attr('href')) && href.replace(/.*(?=#[^\s]+$)/, ''));
					if ( !$target.is($this) ) return;
					if (typeof $navData.jslide == 'number' && $navData.jslide==index){
						$nav.addClass('sel');
					} else {
						$nav.removeClass('sel');
					}
				});
			});
		});
		return ;
	});
//]]>	
</script>

