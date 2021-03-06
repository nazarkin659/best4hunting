<?php
/**
 * sh404SEF - SEO extension for Joomla!
 *
 * @author      Yannick Gaultier
 * @copyright   (c) Yannick Gaultier 2014
 * @package     sh404SEF
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version     4.4.0.1725
 * @date		2014-04-09
 */

// Security check to ensure this file is being included by a parent file.
if (!defined('_JEXEC')) die('Direct Access to this location is not allowed.');

jimport('joomla.html.html.bootstrap');
JHtml::_('formbehavior.chosen', 'select');

$sticky = Sh404sefHelperHtml::setFixedTemplate();

if($sticky) :?>
<div class="shl-fixed-top-hidden">&nbsp;</div>
<?php endif; ?>

<div class="shl-main-content">

<form method="post" name="adminForm" id="adminForm" class="shl-no-margin">

<div class="row-fluid">

<?php if($sticky) : ?>
<div class="shl-fixed span12 shl-main-searchbar-wrapper">
	<?php echo ShlMvcLayout_Helper::render('com_sh404sef.filters.bar_search_limit_pag_sticky', $this); ?>
</div>
<?php endif; ?>

<div id="shl-sidebar-container" class="<?php echo $sticky ? 'shl-fixed' : ''; ?> span2 shl-no-margin">
<?php echo $this->sidebar; ?>
</div>

<?php if(!$sticky): ?>
<div class="span10">
<?php endif; ?>

<?php if(!$sticky): ?>
<div class="span12 shl-main-searchbar-wrapper">
	<?php echo ShlMvcLayout_Helper::render('com_sh404sef.filters.bar_search_limit', $this); ?>
</div>
<?php endif; ?>

<div class="shl-main-list-wrapper span12  <?php if($sticky) echo ' shl-main-list-wrapper-padding'; ?>">

	<?php if($sticky):?>
	<div class="span2 shl-hidden-low-width"></div>
	<div class="span10 <?php echo $sticky ? 'shl-no-margin-left' : ''; ?>">
	<?php
		endif;
		echo ShlHtmlBs_Helper::alert($this->helpMessage, $type = 'info', $dismiss = true);
		echo Sh404sefHelperLanguage::getLanguageFilterWarning();
	?>

	<div id="sh-message-box"></div>
    <table class="table table-striped">
      <thead>
        <tr>
          <th class="shl-list-id">&nbsp;
          </th>

          <th class="shl-list-check">
            <input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this);" />
          </th>

          <th class="shl-list-sef">
            <?php echo JHTML::_('grid.sort', JText::_( 'COM_SH404SEF_ALIAS'), 'oldurl', $this->options->filter_order_Dir, $this->options->filter_order); ?>
          </th>

          <th class="shl-list-sef">
            <?php echo JText::_( 'COM_SH404SEF_URL'); ?>
          </th>

        </tr>
      </thead>
      <tfoot>
        <tr>
          <td colspan="4">
            <?php echo '<div id="shl-bottom-pagination-container">' . $this->pagination->getListFooter() . '</div>'; ?>
          </td>
        </tr>
      </tfoot>
      <tbody>
        <?php
          $k = 0;
          if( $this->itemCount > 0 ) {
            for ($i=0; $i < $this->itemCount; $i++) {

              $alias = &$this->items[$i];
              $checked = JHtml::_( 'grid.id', $i, $alias->id);
        ?>

        <tr>

          <td class="shl-list-id">
            <?php echo $this->pagination->getRowOffset( $i ); ?>
          </td>

          <td class="shl-list-check">
            <?php echo $checked; ?>
          </td>

          <td class="shl-list-sef">
            <?php
            $params = array();
            $linkData = array( 'c' => 'editalias', 'task' => 'edit', 'view' => 'editurl', 'startOffset' => '2','cid[]' => $alias->id, 'tmpl' => 'component');
            $targetUrl = Sh404sefHelperUrl::buildUrl($linkData);
            $params['linkTitle'] = JText::_('COM_SH404SEF_MODIFY_ALIAS_TITLE'). ' ' . $this->escape($alias->oldurl);
            $params['linkTitle'] = Sh404sefHelperHtml::abridge($params['linkTitle'], 'editurl');
             $modalTitle = '';
            $name = '-editurl-' . $alias->id;
            $params['linkClass'] = 'shl-list-sef';
            $params['linkType'] = 'a';
            echo ShlHtmlModal_helper::modalLink($name, $alias->alias, $targetUrl, Sh404sefFactory::getPConfig()->windowSizes['editurl']['x'], Sh404sefFactory::getPConfig()->windowSizes['editurl']['y'], $top = 0, $left = 0, $onClose = '', $modalTitle, $params);
            ?>
          </td>

          <td class="shl-list-sef">
            <?php
            echo $this->escape( $alias->newurl);
            $sefConfig = & Sh404sefFactory::getConfig();
            if (!empty( $alias->oldurl)) {
              echo '<br/><span class="muted">(' . $this->escape( $alias->oldurl) . ')</span>';
              $link = JURI::root() . ltrim( $sefConfig->shRewriteStrings[$sefConfig->shRewriteMode], '/') . $alias->oldurl;
            } else {
              echo '<br /><span class="muted">(-)</span>';
              $link = JURI::root() . $alias->newurl;
            }
            // small preview icon
            echo '&nbsp;<a href="' . $this->escape($link) . '" target="_blank" title="' . JText::_('COM_SH404SEF_PREVIEW') . ' ' . $this->escape($alias->oldurl) . '">';
            echo '<img src=\'components/com_sh404sef/assets/images/external-black.png\' border=\'0\' alt=\''.JText::_('COM_SH404SEF_PREVIEW').'\' />';
            echo '</a>';
            ?>

          </td>

        </tr>
        <?php
        $k = 1 - $k;
      }
    } else {
      ?>
        <tr>
          <td class="center shl-middle" colspan="4">
            <?php echo JText::_( 'COM_SH404SEF_NO_ALIASES' ); ?>
          </td>
        </tr>
        <?php
      }
      ?>
      </tbody>
    </table>
    <?php if($sticky):?>
    </div>
    <?php endif;?>
</div>

<?php if(!$sticky): ?>
</div>
<?php endif; ?>

    <input type="hidden" name="c" value="aliases" />
    <input type="hidden" name="view" value="aliases" />
    <input type="hidden" name="option" value="com_sh404sef" />
    <input type="hidden" name="task" value="" />
    <input type="hidden" name="boxchecked" value="0" />
    <input type="hidden" name="hidemainmenu" value="0" />
    <input type="hidden" name="filter_order" value="<?php echo $this->options->filter_order; ?>" />
    <input type="hidden" name="filter_order_Dir" value="<?php echo $this->options->filter_order_Dir; ?>" />
    <?php echo JHTML::_( 'form.token' ); ?>
 </div>
</form>
</div>

<div class="sh404sef-footer-container">
	<?php echo $this->footerText; ?>
</div>
