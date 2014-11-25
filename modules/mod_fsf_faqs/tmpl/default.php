<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;
?>


<?php if ($maxheight > 0): ?>
<script>

jQuery(document).ready(function () {
	setTimeout("faqsmod_scrollDown()",3000);
});

function faqsmod_scrollDown()
{
	var settings = { 
		direction: "down", 
		step: 40, 
		scroll: true, 
		onEdge: function (edge) { 
			if (edge.y == "bottom")
			{
				setTimeout("faqsmod_scrollUp()",3000);
			}
		} 
	};
	jQuery(".fsf_mod_faqs_scroll").autoscroll(settings);
}

function faqsmod_scrollUp()
{
	var settings = { 
		direction: "up", 
		step: 40, 
		scroll: true,    
		onEdge: function (edge) { 
			if (edge.y == "top")
			{
				setTimeout("faqsmod_scrollDown()",3000);
			}
		} 
	};
	jQuery(".fsf_mod_faqs_scroll").autoscroll(settings);
}
</script>

<style>
.fsf_mod_faqs_scroll {
	max-height: <?php echo $maxheight; ?>px;
	overflow: hidden;
}
</style>


<?php endif; ?>

<div id="fsf_mod_faqs_scroll" class="fsf_mod_faqs_scroll">

<?php if ($mode == "" || $mode == "newpage"): ?>
	<?php foreach ($data as $row) :?>
		<div class='fsf_mod_faqs_cont'>
			<div class='fsf_mod_faqs_title'>
				<a href='<?php echo FSFRoute::_('index.php?option=com_fsf&view=faq&faqid=' . $row->id); ?>'>
					<?php echo $row->question; ?>
				</a>
			</div>
		</div>
	<?php endforeach;?>
	
<?php elseif ($mode == "popup") : ?>
	<?php foreach ($data as $row) :?>
	<div class='fsf_mod_faqs_cont'>
	<div class='fsf_mod_faqs_title'>
				<a href='#' onclick='TINY.box.show({iframe:"<?php echo FSFRoute::_('index.php?option=com_fsf&view=faq&tmpl=component&faqid=' . $row->id); ?>", width:630,height:440});return false;'>
					<?php echo $row->question; ?>
				</a>
			</div>
		</div>
	<?php endforeach;?>

<?php elseif ($mode == "accordion") : ?>
	<?php foreach ($data as $row) :?>
		<div class='fsf_mod_faqs_cont'>
			<div class='fsf_mod_faqs_title accordion_toggler_1'>
				<a href="#" onclick='return false;'>
					<?php echo $row->question; ?>
				</a>
			</div>
			<div class="fsf_mod_faqs_answer accordion_content_1">
				<?php echo $row->answer; ?>
			</div>
		</div>
	<?php endforeach;?>

<?php endif; ?>

</div>
