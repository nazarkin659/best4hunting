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
?>

<div class="ice-main-item page-<?php echo $key+1;?>">
    <?php foreach( $list as $i => $item ): ?>
     <div class="ice-row" style="width:<?php echo $itemWidth;?>%">
		<div class="lof-inner">
			<?php if($params->get("show_photo_title",1)): ?>
        	<h4 class="heading">
			<a <?php echo $target;?>  href="<?php echo $item->link;?>" title="<?php echo $item->title;?>"><?php echo $item->title ?></a></h4>
			<?php endif; ?>
				<div class="carousel_content">
                	<div class="padding jomsocial_padding">
					
					<div class="<?php echo $show_preview?'iceTip':''; ?>" rel="<?php echo $show_preview?$item->thumbnail:'';?>" title="<?php echo  $show_preview?$item->title:'';?>">
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
		</div>
	</div>
    <?php endforeach; ?>
</div>