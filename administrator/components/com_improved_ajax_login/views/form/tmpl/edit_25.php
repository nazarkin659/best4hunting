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

JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');
$document = JFactory::getDocument();
// Import CSS
$document->addStyleSheet($this->themeCSS);
$document->addStyleSheet('components/com_improved_ajax_login/assets/css/bootstrap.min.css');
$document->addStyleSheet('components/com_improved_ajax_login/assets/css/jquery-ui.css');
$document->addStyleSheet('components/com_improved_ajax_login/assets/css/improved_ajax_login.css');
// Import JS
$document->addScript('//ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js');
$document->addScript('components/com_improved_ajax_login/assets/js/bootstrap.min.js');
$document->addScript('components/com_improved_ajax_login/assets/js/jss.js');
$document->addScript('components/com_improved_ajax_login/assets/js/jquery-ui-1.10.3.custom.min.js');
$document->addScript('components/com_improved_ajax_login/assets/js/jquery.ui.touch-punch.min.js');
$document->addScript('../modules/mod_improved_ajax_login/script/improved_ajax_login.js');
$document->addScript("../modules/mod_improved_ajax_login/themes/{$this->theme}/theme.js");
$document->addScript('components/com_improved_ajax_login/assets/js/com_improved_ajax_login.js');
?>
<style type="text/css">
.icon-48-form {
  background: url(<?php echo JURI::root()
    ?>administrator/components/com_improved_ajax_login/assets/images/l_forms.png);
  height: 47px;
}
#system-message ul {
  margin-left: 0;
}
.red {
  color: inherit;
}
[class^=icon-32] {
  background-position: 0 0;
}
[class^="icon-"] {
  margin: 0;
  vertical-align: text-bottom;
}
.nav > li > a:hover{
  background-color: #fff;
}
.nav > li > a {
  font-weight: bold;
  color: #000;
}
.disabled .icon-remove {
  opacity: 0.5;
}
.radio label {
  clear: none;
  float: none;
}
.modal.btn {
  position: static;
  margin: 0;
  width: auto;
  -webkit-box-shadow: inherit;
  box-shadow: inherit;
}
div.width-30 fieldset.radio {
  padding: 0 0 0 20px;
  border: 0;
  margin: 0;
  background: transparent;
  font-size: inherit;
}
.tab-content {
  background: #fff;
}
/* Temporary fix for drifting editor fields */
.adminformlist li {
    clear: both;
}
</style>
<script type="text/javascript">
jQuery(document).ready(function(){
    Joomla.submitbutton = function(task)
    {
        if (task == 'form.cancel') {
            Joomla.submitform(task, document.getElementById('form-form'));
        }
        else{
            if (task != 'form.cancel' && document.formvalidator.isValid(document.id('form-form'))) {
                JForm.save();
                Joomla.submitform(task, document.getElementById('form-form'));
            }
            else {
                alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED')); ?>');
            }
        }
    }
});
<?php require_once(dirname(__FILE__).'/predefined.js.php') ?>
</script>

<div class="fltlft" style="width:15%">
  <?php require_once(dirname(__FILE__).'/left.php') ?>
</div>

<div class="width-55 fltlft">
  <div style="margin:0 10px">
    <ul class="nav nav-pills">
      <li class="active">
        <a>Design view</a>
      </li>
      <li class="disabled gi-selectable gi-click" id="delete-btn">
        <a><i class="icon-remove"></i> Delete</a>
      </li>
    </ul>
    <div class="ial-window ial-trans-gpu ial-active">
      <div class="loginWndInside" id="design-layer">
      </div>
    </div>
  </div>
</div>

<div class="width-30 fltlft gi-properties">
  <?php require_once(dirname(__FILE__).'/right.php') ?>
</div>

<div class="clr"></div>