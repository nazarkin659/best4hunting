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
$show_preview = $params->get("ip_show_preview",1);
$previewWidth = $params->get("ip_preview_width",200);
$previewHeight = $params->get("ip_preview_height",200);
$show_product_image = $params->get("ip_show_image",1);
$show_description = $params->get("ip_show_previewtext",1);
$show_listoffice = $params->get("ip_show_office", 1);
$show_stype = $params->get("ip_show_saletype", 1);
$show_price = $params->get("ip_show_price", 1);
$show_original_price = $params->get("ip_show_originalprice", 1);
$ip_show_category = $params->get("ip_show_category", 1);
$ip_show_agents = $params->get("ip_show_agent", 1);
$show_beds = $params->get("ip_show_beds", 1);
$show_baths = $params->get("ip_show_baths", 1);
$show_surface = $params->get("ip_show_surface", 1);
$show_lottype = $params->get("ip_show_lottype", 1);
$show_street_number = $params->get("ip_show_streetnumber",1);
$show_street = $params->get("ip_show_street",1);
$show_city = $params->get("ip_show_city",1);
$show_postcode = $params->get("ip_show_postcode",1);
$show_state = $params->get("ip_show_state",1);
$show_country = $params->get("ip_show_country",1);
$show_province = $params->get("ip_show_province",1);
?>
<div class="ice-main-item page-<?php echo $key+1;?>">
 <?php foreach( $list as $i => $item ): ?>
		<?php 
				 /* BANNER DISPLAY */
				if($params->get('ip_show_banner', 1) == 1){
					$new = ipropertyHTML::isNew($item->created, $settings->new_days);
					$updated = ipropertyHTML::isNew($item->modified, $settings->updated_days);
					$banner_display = ipropertyHTML::displayBanners($item->stype, $new, JURI::root(true), $settings, $updated);
				}else{
					$banner_display = '';
				}
		?>
		<div class="ice-row" style="width:<?php echo $itemWidth;?>%">
			<div class="lof-inner">
				<h4 class="heading">
					<a <?php echo $target;?>  href="<?php echo $item->link;?>" title="<?php echo $item->title;?>"><?php echo $item->title ?></a></h4>
					<div class="carousel_content">
					
                    <div class="ip_padding padding">
					
                    <div class="<?php echo $show_preview?'iceTip':''; ?>" width="<?php echo $previewWidth; ?>" height="<?php echo $previewHeight;?>" rel="<?php echo $show_preview?$item->thumbnail:'';?>" title="<?php echo  $show_preview?$item->title:'';?>">

						<?php if($show_product_image): ?>
						<div class="ice_ip_thumb">
						<?php if( $params->get('show_readmore','0') ) : ?>
							  <a <?php echo $target;?>  href="<?php echo $item->link;?>" title="<?php echo $item->title;?>">
							  <?php endif; ?>
							  
								<?php echo $item->mainImage.$banner_display; ?>
							  
							  <?php if( $params->get('show_readmore','0') ) : ?>
							  </a>
							 <?php endif; ?>
							 </div>
						<?php endif; ?>
						</div>
                    
                    
					<?php if($show_original_price || $show_price): ?>
						<div class="ice_ip_price">
							 <?php if($show_original_price): ?>
								<span class="ice_ip_slashprice"><?php echo $item->price2; ?></span>
							<?php endif; ?>
							<?php if($show_price): ?>
								<span class="ice_ip_newprice"><?php echo $item->price; ?></span>
							<?php endif; ?>
						</div>
                    <?php endif; ?>
                    
                    
                    <?php if($show_description): ?>
						<div class="ice_ip_description"><?php echo $item->description; ?></div>
					<?php endif; ?>
                    
                    <?php if($show_beds + $show_baths + $show_surface + $show_lottype):?>
					<div class="ice_ip_list">
                    <ul>
						<?php if($show_beds):?>
							<li><strong><?php echo JText::_("IP_BEDS");?></strong> <?php echo $item->beds; ?></li>
						<?php endif; ?>
						<?php if($show_baths):?>
							<li><strong><?php echo JText::_("IP_BATHS");?></strong> <?php echo $item->baths; ?></li>
						<?php endif; ?>
						<?php if($show_surface):?>
							<li><strong><?php echo JText::_("IP_SURFACE");?></strong> <?php echo $item->sqft; ?></li>
						<?php endif; ?>
						<?php if($show_lottype):?>
							<li><strong><?php echo JText::_("IP_LOTTYPE");?></strong> <?php echo $item->lot_type; ?></li>
						<?php endif; ?>
                        </ul>
					</div>
                   <?php endif; ?>
                   
                   
                   <?php if($show_street_number + $show_street + $show_postcode + $show_province + $show_country):?>
                    <div class="ice_ip_list">
                        <ul>
						<?php 
								if($show_street_number){
									echo '<li><strong>'.JText::_("LOF_STREET_NUMBER").'</strong>&nbsp;'.$item->street_num."</li>";
								}
								if($show_street){
									echo '<li><strong>'.JText::_("LOF_STREET").'</strong>&nbsp;'.$item->street."</li>";
								}
								if($show_city){
									echo '<li><strong>'.JText::_("LOF_CITY").'</strong>&nbsp;'.$item->city."</li>";
								}
								if($show_postcode){
									echo '<li><strong>'.JText::_("LOF_POSTALCODE").'</strong>&nbsp;'.$item->postalcode."</li>";
								}
								if($show_state){
									echo '<li><strong>'.JText::_("LOF_STATE").'</strong>&nbsp;'.$item->state."</li>";
								}
								if($show_province ){
									echo '<li><strong>'.JText::_("LOF_PROVINCE").'</strong>&nbsp;'.$item->province."</li>";
								}
								if($show_country){
									echo '<li><strong>'.JText::_("LOF_COUNTRY").'</strong>&nbsp;'.$item->country."</li>";
								}
						?>
                        </ul>
					 </div>
                     <?php endif; ?>
                      
                       
                    
					
                    
                    
					<?php if( $params->get('show_readmore','0') ) : ?>
                    	 <p class="readmore">
                          <a <?php echo $target;?>  href="<?php echo $item->link;?>" title="<?php echo $item->title;?>"><?php echo JText::_('READ_MORE');?></a>
                          </p>
					<?php endif; ?>
					
                    
					  <div class="ip_smallfont">
                      
                      <div class="more_info">
						<?php if($ip_show_category && !empty($item->category) ): ?>
							<b class="ip_category"><?php echo JText::_("IP_CATEGORIES");?></b> &nbsp;<?php echo $item->category; ?><br/>
						<?php endif; ?>
                        
						<?php if($ip_show_agents): ?>
							<b class="ip_agent"><?php echo JText::_("IP_AGENTS");?></b>&nbsp;<?php echo $item->agent_name; ?><br/>
						<?php endif; ?>
					   </div>
						
						<?php if($show_listoffice):
							echo JText::sprintf("LISTED_OFFICE", $item->listing_office,$item->created); 
						 endif;
						 ?>
						<?php if( $show_stype ):
							echo "[".$item->stype_2."]";
						endif;?>
						</div>
					
                    </div>
                    
                    
					</div>
				</div>
			</div>
        <?php endforeach; ?>
</div>