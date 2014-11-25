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
			<span class="<?php echo $show_preview?'iceAccordionTip':''; ?>"  rel="<?php echo $show_preview?$item->thumbnail:'';?>" title="<?php echo  $show_preview?$item->title:'';?>"><span><?php echo $item->title ?></span></span></h4>
				<div class="accordion_content <?php echo $iceaccordion_content ?>">
                	<div class="jomsocial_padding padding">
					
					<div class="<?php echo $show_preview?'iceAccordionTip':''; ?>" rel="<?php echo $show_preview?$item->thumbnail:'';?>" title="<?php echo  $show_preview?$item->title:'';?>">
						<a <?php echo $target;?>  href="<?php echo $item->link;?>" title="<?php echo $item->title;?>">
						 	<?php echo $item->mainImage; ?>
                          </a>
					</div>
					<?php
						if($params->get("show_create_date",1)):
					?>
					<div class="profile-detail">
						<span class="profile-detail-title"><strong><?php echo JText::_("CREATE_ON"); ?></strong></span>
						<span><?php echo $item->created;?></span>
					</div>
					<?php endif; ?>
					<?php
						if($params->get("show_js_category",1)):
					?>
					<div class="profile-detail">
						<span class="profile-detail-title"><strong><?php echo JText::_("CATEGORY"); ?></strong></span>
						<span><a href="<?php echo $item->category_link; ?>"><?php echo $item->category_title;?></a></span>
					</div>
					<?php endif; ?>
					<?php
						if($params->get("show_nr_members",1)):
					?>
					<div class="profile-detail">
						<span class="jsIcon1 icon-group" style="margin-right: 5px;">
							<a href="<?php echo $item->viewmember_link; ?>"><?php echo JText::sprintf((CStringHelper::isPlural($item->membercount)) ? 'COM_COMMUNITY_GROUPS_MEMBER_COUNT_MANY':'COM_COMMUNITY_GROUPS_MEMBER_COUNT', $item->membercount);?></a>
						</span>
					</div>
					<?php endif; ?>
					<?php
						if($params->get("show_nr_discussion",1)):
					?>
					<div class="profile-detail">
						<span class="jsIcon1 icon-discuss" style="margin-right: 5px;">
						<?php echo JText::sprintf((CStringHelper::isPlural($item->discusscount)) ? 'COM_COMMUNITY_GROUPS_DISCUSSION_COUNT_MANY' :'COM_COMMUNITY_GROUPS_DISCUSSION_COUNT', $item->discusscount);?>
					</span>
					</div>
					<?php endif; ?>
					<?php
						if($params->get("show_wall_post",1)):
					?>
					<div class="profile-detail">
						<span class="jsIcon1 icon-wall" style="margin-right: 5px;">
						<?php echo JText::sprintf((CStringHelper::isPlural($item->wallcount)) ? 'COM_COMMUNITY_GROUPS_WALL_COUNT_MANY' : 'COM_COMMUNITY_GROUPS_WALL_COUNT', $item->wallcount);?>
						</span>
					</div>
					<?php endif; ?>
					<?php
						if($params->get("show_group_desc",1)):
					?>
					<div class="profile-detail">
						<?php echo $item->description; ?>
						</span>
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