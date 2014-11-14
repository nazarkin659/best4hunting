<?php defined('_JEXEC') or die('Restricted access');
/*------------------------------------------------------------------------------------------------------------
# VP Manufacturer Carousel! Joomla 2.5 Module for VirtueMart 2.0 Ver. 1.0
# ------------------------------------------------------------------------------------------------------------
# Copyright (C) 2012 VirtuePlanet Services LLP. All Rights Reserved.
# License - GNU General Public License version 2. http://www.gnu.org/licenses/gpl-2.0.html
# Author: VirtuePlanet Services LLP
# Email: info@virtueplanet.com
# Websites:  http://www.virtueplanet.com
------------------------------------------------------------------------------------------------------------*/
?>
<?php if($headerText){ ?>
<div id="<?php echo "vp-mf-header-".$ID ?>" class="vp-mf-header-text">
	<?php echo $headerText ?>
</div>
<?php } ?>

<?php if($orientation == 'horizontal') { ?>
<div id="<?php echo "vp-mf-".$ID ?>" class="vp_mf_carousel<?php echo $params->get( 'moduleclass_sfx' ) ?> horizontal">
	<div id="<?php echo "carousel-".$ID ?>">
		<?php foreach ($manufacturers as $manufacturer) {
			if($linkType == 'listing') {
			
				$link = JRoute::_('index.php?option=com_virtuemart&view=category&virtuemart_manufacturer_id=' . $manufacturer->virtuemart_manufacturer_id.'&Itemid=' . $menuitem);				
			} else {			
				$link = JROUTE::_('index.php?option=com_virtuemart&view=manufacturer&virtuemart_manufacturer_id=' . $manufacturer->virtuemart_manufacturer_id);
			}
			if ($manufacturer->images) {
				echo '<a href="'.$link.'" title="'.$manufacturer->mf_name.'" >'; 
				echo $manufacturer->images[0]->displayMediaThumb('',false);
				echo '</a>';
			}
		}?>	
	</div>
	<a href="#" id="ui-carousel-next-<?php echo $ID ?>" class="ui-carousel-next"><span>next</span></a>
	<a href="#" id="ui-carousel-prev-<?php echo $ID ?>" class="ui-carousel-prev"><span>prev</span></a>
</div>
<?php } 

else { ?>
<div id="<?php echo "vp-mf-".$ID ?>" class="vp_mf_carousel<?php echo $params->get( 'moduleclass_sfx' ) ?> vertical">
	<div id="<?php echo "carousel-".$ID ?>">
		<?php foreach ($manufacturers as $manufacturer) {
			if($linkType == 'listing') {
				$link = JRoute::_('index.php?option=com_virtuemart&view=category&virtuemart_manufacturer_id=' . $manufacturer->virtuemart_manufacturer_id.'&Itemid=' . $menuitem);				
			} else {			
				$link = JROUTE::_('index.php?option=com_virtuemart&view=manufacturer&virtuemart_manufacturer_id=' . $manufacturer->virtuemart_manufacturer_id);
			}
			if ($manufacturer->images) {
				echo '<a href="'.$link.'" title="'.$manufacturer->mf_name.'" >'; 
				echo $manufacturer->images[0]->displayMediaThumb('',false);
				echo '</a>';
			}
		}?>	
	</div>
	<a href="#" id="ui-carousel-next-<?php echo $ID ?>" class="ui-carousel-next"><span>next</span></a>
	<a href="#" id="ui-carousel-prev-<?php echo $ID ?>" class="ui-carousel-prev"><span>prev</span></a>
</div>
<?php } ?>

<?php if($footerText){ ?>
<div id="<?php echo "vp-mf-footer-".$ID ?>" class="vp-mf-footer-text">
	<?php echo $footerText ?>
</div>
<?php } ?>