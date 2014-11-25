<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;

require_once( JPATH_COMPONENT.DS.'helper'.DS.'glossary.php' );

?>
<?php echo FSF_Helper::PageStyle(); ?>
<?php echo FSF_Helper::PageTitle("GLOSSARY"); ?>
<div class="fsf_spacer"></div>

<?php if ($this->use_letter_bar): ?>
	<div class="fsf_glossary_letters">
	<?php foreach ($this->letters as $letter => $ok): ?>
		<?php if (!$ok): ?>
			<span class="letter-disabled">
				&nbsp;<?php echo $letter; ?>&nbsp;
			</span>
		<?php else: ?>
			<span class="letter-present">
				<?php if ($this->use_letter_bar == 2): ?>
					<a href='<?php echo FSFRoute::_('index.php?option=com_fsf&view=glossary&letter=' . strtolower($letter)); ?>'>&nbsp;<?php echo $letter; ?>&nbsp;</a>
				<?php else : ?>
					<a href='<?php echo FSFRoute::_('index.php?option=com_fsf&view=glossary#letter_' . strtolower($letter)); ?>'>&nbsp;<?php echo $letter; ?>&nbsp;</a>
				<?php endif; ?>
			</span>
		<?php endif; ?>
	<?php endforeach; ?>

	</div>
<?php endif; ?>

<?php $letter = ""; ?>
<?php foreach($this->rows as $glossary) : ?>
<?php $thisletter = strtolower(substr($glossary->word,0,1)); 
	if ($thisletter != $letter)
	{
		$letter = $thisletter;
		echo "<a name='letter_$letter' ></a>";
	}
?>
<div class="fsf_glossary_div">
<div class="fsf_glossary_title"><a name='<?php echo FSF_Glossary::MakeAnchor($glossary->word); ?>'></a><?php echo $glossary->word; ?></div>
<div class="fsf_glossary_text"><?php echo $glossary->description; ?><?php echo $glossary->longdesc; ?></div>
<div class="fsf_clear"></div>
</div>

<?php endforeach; ?>
<?php include JPATH_SITE.DS.'components'.DS.'com_fsf'.DS.'_powered.php'; ?>
<?php echo FSF_Helper::PageStyleEnd(); ?>