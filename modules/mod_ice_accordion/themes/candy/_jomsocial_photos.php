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
$show_preview = $params->get("show_preview_image", 1);
 for( $i=$limitstart; $i< $limit; $i++) : ?>
		<?php 
				$item = isset( $list[ $i ] )?$list[ $i ]:null;
				if( $item ):
		?>
	<h4 class="toggler <?php echo $iceaccordion_toggler ?>">
			<span class="<?php echo $show_preview?'iceAccordionTip':''; ?>" rel="<?php echo $show_preview?$item->thumbnail:'';?>" title="<?php echo  $show_preview?$item->title:'';?>"><span><?php echo $item->title ?></span></span></h4>
				<div class="accordion_content <?php echo $iceaccordion_content ?>">
                	<div class="jomsocial_padding padding">
					
					<div class="<?php echo $show_preview?'iceAccordionTip':''; ?>" rel="<?php echo $show_preview?$item->thumbnail:'';?>" title="<?php echo  $show_preview?$item->title:'';?>">
						<a <?php echo $target;?>  href="<?php echo $item->link;?>" title="<?php echo $item->title;?>">
						 	<?php echo $item->mainImage; ?>
                          </a>
					</div>
					<?php
						if($params->get("show_nr_view")):
					?>
					<div class="profile-detail">
						<span class="profile-detail-title"><strong><?php echo JText::_("PHOTO_VIEWS"); ?></strong></span>
						<span><?php echo $item->hits." ".JText::_("VIEWS");?></span>
					</div>
					<?php endif; ?>
					<?php
						if($params->get("show_nr_comments")):
					?>
					<div class="profile-detail">
						<span class="profile-detail-title"><strong><?php echo JText::_("PHOTO_COMMENTS"); ?></strong></span>
						<span><?php echo $item->comments." ".JText::_("COMMENTS");?></span>
					</div>
					<?php endif; ?>
					<?php
						if($params->get("show_photo_date")):
					?>
					<div class="profile-detail">
						<span class="profile-detail-title"><strong><?php echo JText::_("PHOTO_CREATED"); ?></strong></span>
						<span><?php echo $item->created; ?></span>
					</div>
					<?php endif; ?>
					<?php
						if($params->get("show_location")):
					?>
					<div class="profile-detail">
						<span class="profile-detail-title"><strong><?php echo JText::_("PHOTO_LOCATION"); ?></strong></span>
						<span><?php echo $item->location;?></span>
					</div>
					<?php endif; ?>
					<?php
						if($params->get("show_upload_by")):
					?>
					<div class="profile-detail">
						<span class="profile-detail-title"><strong><?php echo JText::_("UPLOADED_BY"); ?></strong></span>
						<span><a href="<?php echo $item->user_link; ?>"><?php echo $item->user_name; ?></a></span>
					</div>
					<?php endif; ?>
					<?php if( $params->get('show_readmore','0') ) : ?>
                    	 <p class="readmore">
                          <a <?php echo $target;?>  href="<?php echo $item->link;?>" title="<?php echo $item->title;?>"><strong><?php echo JText::_('VIEW_DETAIL');?></strong></a>
                          </p>
					<?php endif; ?>
                    </div>
                </div>
	<?php endif; ?>
<?php endfor; ?>