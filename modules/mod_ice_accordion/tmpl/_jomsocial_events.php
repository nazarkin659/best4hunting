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
						if($params->get("show_event_time")):
					?>
					<div class="profile-detail">
						<div class="eventTime"><span><?php echo JText::_("From"); ?></span> : <?php echo $item->startdate; ?> <br><span><?php echo JText::_("Until"); ?></span> : <?php echo $item->enddate; ?></div>
					</div>
					<?php endif; ?>
					<?php if( $params->get("show_nr_users_attending",1)):?>
					<div class="profile-detail">
						<span class="jsIcon1 icon-group" style="margin-right: 5px;">
						<?php if( CEventHelper::isPast($item) ) { ?>
							<a href="<?php echo $item->getGuestLink( COMMUNITY_EVENT_STATUS_ATTEND );?>"><?php echo JText::sprintf((cIsPlural($item->confirmedcount)) ? 'COM_COMMUNITY_EVENTS_COUNT_MANY_PAST':'COM_COMMUNITY_EVENTS_COUNT_PAST', $item->confirmedcount);?></a>
							<?php } else { ?>
							<a href="<?php echo $item->getGuestLink( COMMUNITY_EVENT_STATUS_ATTEND );?>"><?php echo JText::sprintf((cIsPlural($item->confirmedcount)) ? 'COM_COMMUNITY_EVENTS_MANY_GUEST_COUNT':'COM_COMMUNITY_EVENTS_GUEST_COUNT', $item->confirmedcount);?></a>
							<?php } ?>
						</span>
					</div>
					<?php endif; ?>
					<?php
						if($params->get("show_event_category")):
					?>
					<div class="profile-detail">
						<span class="profile-detail-title"><strong><?php echo JText::_("CATEGORY"); ?></strong></span>
						<span><a href="<?php echo $item->category_link; ?>"><?php echo $item->category_title;?></a></span>
					</div>
					<?php endif; ?>
					<?php
						if($params->get("show_event_summary")):
					?>
					<div class="profile-detail">
						<span class="profile-detail-title"><strong><?php echo JText::_("SUMMARY"); ?></strong></span>
						<span><?php echo $item->description;?></span>
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