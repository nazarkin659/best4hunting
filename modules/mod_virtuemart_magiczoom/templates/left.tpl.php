<?php

/*------------------------------------------------------------------------
# mod_virtuemart_magiczoom - Magic Zoom for Joomla with VirtueMart
# ------------------------------------------------------------------------
# Magic Toolbox
# Copyright 2011 MagicToolbox.com. All Rights Reserved.
# @license - http://www.opensource.org/licenses/artistic-license-2.0  Artistic License 2.0 (GPL compatible)
# Website: http://www.magictoolbox.com/magiczoom/modules/joomla/
# Technical Support: http://www.magictoolbox.com/contact/
/*-------------------------------------------------------------------------*/

// no direct access
defined('_JEXEC') or die('Restricted access.');

$m = intval(self::$options->getValue('selectors-margin'));
$ws = intval(self::$options->getValue('selector-max-width'));
$wm = intval(self::$options->getValue('thumb-max-width'));
$hm = intval(self::$options->getValue('thumb-max-height'));
?>
<!-- Begin magiczoom -->
<div class="MagicToolboxContainer selectorsLeft" style="width: <?php echo ($wm + $ws + $m)?>px">
    <?php if(count($thumbs) > 1):?>
    <div id="MagicToolboxSelectors<?php echo $pid?>" class="MagicToolboxSelectorsContainer<?php echo $magicscroll;?>" style="width: <?php echo $ws?>px;margin-right: <?php echo $m?>px;">
        <?php echo join("\n\t",$thumbs)?>
    </div>
    <?php if(!empty($magicscroll)): ?>
        <script type="text/javascript">
            MagicScroll.extraOptions.MagicToolboxSelectors<?php echo $pid?> = MagicScroll.extraOptions.MagicToolboxSelectors<?php echo $pid?> || {};
            MagicScroll.extraOptions.MagicToolboxSelectors<?php echo $pid?>.direction = 'bottom';
            <?php if(self::$options->checkValue('height', 0)): ?>
            MagicScroll.extraOptions.MagicToolboxSelectors<?php echo $pid?>.height = <?php echo $hm?>;
            <?php endif?>
        </script>
    <?php endif?>
    <?php endif?>
    <div class="MagicToolboxMainContainer" style="width: <?php echo $wm?>px">
        <?php echo $main?>
        <?php if(isset($message)):?>
            <div class="MagicToolboxMessage"><?php echo $message?></div>
        <?php endif?>
    </div>
    <div style="clear:both"></div>
</div>
<!-- End magiczoom -->
