<?php
/*-------------------------------------------------------------------------
# com_improved_ajax_login - com_improved_ajax_login
# -------------------------------------------------------------------------
# @ author    Balint Polgarfi
# @ copyright Copyright (C) 2013 Offlajn.com  All Rights Reserved.
# @ license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# @ website   http://www.offlajn.com
-------------------------------------------------------------------------*/
?><?php
// no direct access
defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');
JHtml::_('behavior.formvalidation');
JHtml::_('formbehavior.chosen', 'select');
JHtml::_('behavior.keepalive');

$document = JFactory::getDocument();
// Import CSS
$document->addStyleSheet($this->themeCSS);
$document->addStyleSheet('components/com_improved_ajax_login/assets/css/jquery-ui.css');
$document->addStyleSheet('components/com_improved_ajax_login/assets/css/improved_ajax_login.css');
// Import JS
$document->addScript('components/com_improved_ajax_login/assets/js/jss.js');
$document->addScript('components/com_improved_ajax_login/assets/js/jquery-ui-1.10.3.custom.min.js');
$document->addScript('components/com_improved_ajax_login/assets/js/jquery.ui.touch-punch.min.js');
$document->addScript('../modules/mod_improved_ajax_login/script/improved_ajax_login.js');
$document->addScript("../modules/mod_improved_ajax_login/themes/{$this->theme}/theme.js");
$document->addScript('components/com_improved_ajax_login/assets/js/com_improved_ajax_login.js');
?>

<script type="text/javascript">
Joomla.submitbutton = function(task) {
  if(task == 'form.cancel') Joomla.submitform(task, document.getElementById('form-form'));
  else {
    if (task != 'form.cancel' && document.formvalidator.isValid(document.id('form-form'))) {
      JForm.save();
      Joomla.submitform(task, document.getElementById('form-form'));
    } else alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED')); ?>');
  }
};
<?php require_once(dirname(__FILE__).'/predefined.js.php') ?>

jQuery(function($) {
  $(".hasTip").each(function() {
    $(this).prop("title", $(this).prop("title").split('::')[1]);
  }).tooltip({placement: "left"});
});
</script>

<div class="row-fluid">

  <div class="span2">
    <?php require_once(dirname(__FILE__).'/left.php') ?>
  </div>

  <div class="span7">
    <ul class="nav nav-pills">
      <li class="active">
        <a>Design view</a>
      </li>
      <li class="disabled gi-selectable gi-click" id="delete-btn">
        <a><i class="icon-cancel-2"></i> Delete</a>
      </li>
    </ul>
    <div class="ial-window ial-trans-gpu ial-active">
      <div class="loginWndInside" id="design-layer">
      </div>
    </div>
  </div>

  <div class="span3 gi-properties">
    <?php require_once(dirname(__FILE__).'/right.php') ?>
  </div>

</div>
