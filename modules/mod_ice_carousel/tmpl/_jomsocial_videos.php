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
$show_preview = $params->get("show_preview_image", 1);
if( modIceCarousel::checkIceAjax() ):
$list = $pages[ $page - 1];
$key = $page - 1;
endif;
$imageHeight   = (int)$params->get( 'main_height', 300 ) ;
$imageWidth    = (int)$params->get( 'main_width', 660 ) ;
?>

<div class="ice-main-item page-<?php echo $key+1;?>">
    <?php foreach( $list as $i => $item ): ?>
     <div class="ice-row" style="width:<?php echo $itemWidth;?>%">
		<div class="lof-inner">
        	<h4 class="heading">
			<a <?php echo $target;?>  href="<?php echo $item->link;?>" title="<?php echo $item->title;?>"><?php echo $item->title ?></a></h4>
				<div class="carousel_content">
                	<div class="padding jomsocial_padding" >
					
					<div class="" rel="" title="<?php echo  $show_preview?$item->title:'';?>">
                          <a class="video-thumb-url" <?php echo $target;?>  href="<?php echo $item->link;?>" title="<?php echo $item->title;?>" style="width:<?php echo $imageWidth; ?>px;height:<?php echo $imageHeight;?>px">
							<?php echo $item->mainImage; ?>
							<span class="video-durationHMS"><?php echo $item->duration; ?></span>
                          </a>
					</div>
					<?php if( $params->get("show_video_nr_view",1)):?>
					<div class="profile-detail">
						<span class="profile-detail-title"><strong><?php echo JText::_("HITS");?></strong></span>
						<span><?php echo $item->hits." ".JText::_("VIEWS");?></span>
					</div>
					<?php endif; ?>
					<?php
						if($params->get("show_video_nr_comments")):
					?>
					<div class="profile-detail">
						<span class="profile-detail-title"><strong><?php echo JText::_("COMMENTS"); ?></strong></span>
						<span><?php echo $item->comments." ".JText::_("COMMENTS");?></span>
					</div>
					<?php endif; ?>
					<?php
						if($params->get("show_added_date")):
					?>
					<div class="profile-detail">
						<span class="profile-detail-title"><strong><?php echo JText::_("CREATE_ON"); ?></strong></span>
						<span><?php echo $item->created;?></span>
					</div>
					<?php endif; ?>
					<?php
						if($params->get("show_video_upload_by")):
					?>
					<div class="profile-detail">
						<span class="profile-detail-title"><strong><?php echo JText::_("CREATED_BY"); ?></strong></span>
						<span><a href="<?php echo $item->user_link; ?>"><?php echo $item->username; ?></a></span>
					</div>
					<?php endif; ?>
					<?php
						if($params->get("show_video_category")):
					?>
					<div class="profile-detail">
						<span class="profile-detail-title"><strong><?php echo JText::_("CATEGORY"); ?></strong></span>
						<span><a href="<?php echo $item->category_link;?>"><?php echo $item->category_name; ?></a></span>
					</div>
					<?php endif; ?>
					<?php
						if($params->get("show_video_location")):
					?>
					<div class="profile-detail">
						<span class="profile-detail-title"><strong><?php echo JText::_("LOCATION"); ?></strong></span>
						<span><?php echo $item->location; ?></span>
					</div>
					<?php endif; ?>
					<?php
						if($params->get("show_video_desc")):
					?>
					<div class="profile-detail">
						<?php echo $item->description;?>
					</div>
					<?php endif; ?>
					<?php if( $params->get('show_readmore','0') ) : ?>
                    	 <p class="readmore">
                          <a <?php echo $target;?>  href="<?php echo $item->link;?>" title="<?php echo $item->title;?>"><strong><?php echo JText::_('VIEW_DETAIL');?></strong></a>
                          </p>
					<?php endif; ?>
                    </div>
                </div>
		</div>
	</div>
    <?php endforeach; ?>
</div>