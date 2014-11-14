<?php
/*-------------------------------------------------------------------------
# mod_improved_ajax_login - Improved AJAX Login and Register
# -------------------------------------------------------------------------
# @ author    Balint Polgarfi
# @ copyright Copyright (C) 2013 Offlajn.com  All Rights Reserved.
# @ license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# @ website   http://www.offlajn.com
-------------------------------------------------------------------------*/
?><?php defined('_JEXEC') or die('Restricted access'); ?>

<?php if (count($modules = JModuleHelper::getModules($params->get('reg_top', 'reg-top')))): // REG-TOP MODULEPOS ?>
  <?php foreach ($modules as $m): ?>
    <?php echo JModuleHelper::renderModule($m) ?>
  <?php endforeach ?>
  <div class="loginBrd"></div>
<?php endif ?>

<?php if (isset($_SESSION['oauth']) && $socialpos=='top') require dirname(__FILE__).'/social.php' // TOP SOCIALPOS ?>

<?php
$db = JFactory::getDBo();
$db->setQuery(defined('DEMO')?
  "SELECT fields, props FROM #__offlajn_forms WHERE type='registration' AND id = {$params->get('regform', 1)}":
  "SELECT fields, props FROM #__offlajn_forms WHERE state=1 AND type='registration'");
$res = $db->loadObject();
$fields = json_decode($res->fields);
foreach ($fields->page[0]->elem as $elem) {
  foreach ($elem as $prop) {
    if (!isset($prop->defaultValue) || !isset($prop->value)) continue;
    if ($prop->value == '') $prop->value = @JText::sprintf($prop->defaultValue, '');
    unset($prop->placeholder);
  }
}
?>

<form action="<?php echo JRoute::_('index.php', true, $params->get('usesecure')) ?>" method="post" name="ialRegister" class="ial-form">
  <input type="hidden" value="<?php echo htmlspecialchars(json_encode($fields), ENT_COMPAT, 'UTF-8') ?>" name="fields" />
  <input type="hidden" value="<?php echo htmlspecialchars($res->props, ENT_COMPAT, 'UTF-8') ?>" name="props" />
  <input type="hidden" value="com_users" name="option" />
  <input type="hidden" value="registration.register" name="task" />
  <?php echo JHTML::_('form.token') ?>
</form>

<br style="clear:both" />
<?php if (@$_SESSION['oauth'] && $socialpos=='bottom') require dirname(__FILE__).'/social.php' // BOTTOM SOCIALPOS ?>

<?php if (count($modules = JModuleHelper::getModules($params->get('reg_bottom', 'reg-bottom')))): // REG-BOTTOM MODULEPOS ?>
  <div class="loginBrd"></div>
  <?php foreach ($modules as $m): ?>
    <?php echo JModuleHelper::renderModule($m) ?>
  <?php endforeach ?>
<?php endif ?>
