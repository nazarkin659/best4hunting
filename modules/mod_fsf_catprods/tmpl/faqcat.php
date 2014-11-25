<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;
?>
<div class="faq_mod_category_cont">
<?php foreach ($rows as $cat): ?>

<?php $link = FSFRoute::_( 'index.php?option=com_fsf&view=faq&catid=' . $cat['id'] ); ?>
<div class='faq_mod_category' style='cursor: pointer;'>
	<?php if ($params->get('show_images') && $cat['image']) : ?>
	<div class='faq_mod_category_image'>
	    <a href='<?php echo $link ?>'><img src='<?php echo JURI::root( true ); ?>/images/fsf/faqcats/<?php echo $cat['image']; ?>' width='32' height='32'></a>
	</div>
	<?php endif; ?>
	<div class='faq_mod_category_head'>
		<a href='<?php echo $link ?>'><?php echo $cat['title'] ?></a>
	</div>
</div>

<?php endforeach; ?>

<div class="fsf_clear"></div>

</div>
