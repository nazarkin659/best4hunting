<?php
/**
 * IceAccordion Extension for Joomla 2.5 By IceTheme
 * 
 * 
 * @copyright	Copyright (C) 2008 - 2012 IceTheme.com. All rights reserved.
 * @license		GNU General Public License version 2
 * 
 * @Website 	http://www.icetheme.com/Joomla-Extensions/iceaccordion.html
 * @Support 	http://www.icetheme.com/Forums/IceAccordion/
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
						<?php if( $params->get("show_karma",1)):?>
						<div class="profile-detail">
							<span class="profile-detail-title"><strong><?php echo JText::_("KARMA");?></strong></span>
							<img alt="" src="<?php echo $item->karma;?>"/>
						</div>
						<?php endif; ?>
						<?php
							if($params->get("show_profile_view")):
						?>
						<div class="profile-detail">
							<span class="profile-detail-title"><strong><?php echo JText::_("PROFILE_VIEWS"); ?></strong></span>
							<span><?php echo $item->_view." ".JText::_("VIEWS");?></span>
						</div>
						<?php endif; ?>
						<?php
							if($params->get("show_user_status")):
						?>
						<div class="profile-detail">
							<span class="profile-detail-title"><strong><?php echo JText::_("USER_STATUS"); ?></strong></span>
							<span><?php echo $item->_status;?></span>
						</div>
						<?php endif; ?>
						<?php
							if($params->get("show_last_online")):
						?>
						<div class="profile-detail">
							<span class="profile-detail-title"><strong><?php echo JText::_("LAST_ONLINE"); ?></strong></span>
							<span><?php echo $item->lastLogin; ?></span>
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