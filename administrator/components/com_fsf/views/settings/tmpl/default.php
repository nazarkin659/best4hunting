<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;
?>
<script>
function ResetElement(tabid)
{
	document.getElementById('tab_' + tabid).style.display = 'none';
	document.getElementById('link_' + tabid).style.backgroundColor = '';

	document.getElementById('link_' + tabid).onmouseover = function() {
		this.style.backgroundColor='<?php echo FSF_Settings::get('css_hl'); ?>';
	}
	document.getElementById('link_' + tabid).onmouseout = function() {
		this.style.backgroundColor='';
	}

}
function ShowTab(tabid)
{
	ResetElement('general');
// 
// ##NOT_TEST_END##
	ResetElement('visual');
// ##NOT_TEST_START##
	ResetElement('glossary');
	ResetElement('faq');
// ##NOT_TEST_END##
	
	location.hash = tabid;
	jQuery('#tab').val(tabid);
	document.getElementById('tab_' + tabid).style.display = 'inline';
	document.getElementById('link_' + tabid).style.backgroundColor = '#f0f0ff';
	
	document.getElementById('link_' + tabid).onmouseover = function() {
	}
	document.getElementById('link_' + tabid).onmouseout = function() {
	}
}
</script>

<style>
.fsf_custom_warn
{
	color: red;
}
.fsf_help
{
	border: 1px solid #CCC;
	float: left;
	padding: 3px;
	background-color: #F8F8FF;
}
.admintable td
{
	border-bottom: 1px solid #CCC;
	padding-bottom: 4px;
	padding-top: 2px;
}
</style>
<form action="index.php" method="post" name="adminForm" id="adminForm">
<input type="hidden" name="what" value="save">
<input type="hidden" name="option" value="com_fsf" />
<input type="hidden" name="task" value="save" />
<input type="hidden" name="view" value="settings" />
<input type="hidden" name="tab" id='tab' value="<?php echo $this->tab; ?>" />
<input type="hidden" name="version" value="<?php echo $this->settings['version']; ?>" />
<input type="hidden" name="fsj_username" value="<?php echo $this->settings['fsj_username']; ?>" />
<input type="hidden" name="fsj_apikey" value="<?php echo $this->settings['fsj_apikey']; ?>" />
<input type="hidden" name="content_unpublished_color" value="<?php echo $this->settings['content_unpublished_color']; ?>" />
<div class='ffs_tabs'>

<!--<a id='link_general' class='ffs_tab' href='#' onclick="ShowTab('general');return false;">General</a>-->

<a id='link_general' class='ffs_tab' href='#' onclick="ShowTab('general');return false;"><?php echo JText::_("GENERAL_SETTINGS"); ?></a> 
<?php //  ?>
<a id='link_visual' class='ffs_tab' href='#' onclick="ShowTab('visual');return false;"><?php echo JText::_("VISUAL"); ?></a>
<?php // ##NOT_TEST_START## ?>
<a id='link_glossary' class='ffs_tab' href='#' onclick="ShowTab('glossary');return false;"><?php echo JText::_("GLOSSARY"); ?></a>
<a id='link_faq' class='ffs_tab' href='#' onclick="ShowTab('faq');return false;"><?php echo JText::_("FAQS"); ?></a> 
<?php // ##NOT_TEST_END## ?>

</div>

<div id="tab_general">

	<fieldset class="adminform">
		<legend><?php echo JText::_("GENERAL_SETTINGS"); ?></legend>

		<table class="admintable">
			<tr>
				<td align="left" class="key" style="width:250px;">
					<?php echo JText::_("HIDE_POWERED"); ?>:
				</td>
				<td style="width:250px;">
					<input type='checkbox' name='hide_powered' value='1' <?php if ($this->settings['hide_powered'] == 1) { echo " checked='yes' "; } ?>>
				</td>
				<td>
					<div class='fsf_help'><?php echo JText::_('SETHELP_hide_powered'); ?></div>
				</td>
			</tr>
			<tr>
				<td align="left" class="key">
					<?php echo JText::_("jquery_include"); ?>:
				</td>
				<td style="width:250px;">
					<select name="jquery_include">
						<option value="auto" <?php if ($this->settings['jquery_include'] == "auto") echo " SELECTED"; ?> ><?php echo JText::_('jquery_include_auto'); ?></option>
						<option value="yes" <?php if ($this->settings['jquery_include'] == "yes") echo " SELECTED"; ?> ><?php echo JText::_('jquery_include_yes'); ?></option>
						<option value="yesnonc" <?php if ($this->settings['jquery_include'] == "yesnonc") echo " SELECTED"; ?> ><?php echo JText::_('jquery_include_yesnonc'); ?></option>
						<option value="no" <?php if ($this->settings['jquery_include'] == "no") echo " SELECTED"; ?> ><?php echo JText::_('jquery_include_no'); ?></option>
					</select>
				</td>
				<td>
					<div class='fsf_help'><?php echo JText::_('SETHELP_jquery_include'); ?></div>
				</td>
			</tr>
		</table>
	</fieldset>
<?php //  ?>

<?php if (FSF_Helper::Is16()): ?>
	
	<fieldset class="adminform">
		<legend><?php echo JText::_("DATE_SETTINGS"); ?></legend>

		<table class="admintable">
			<tr>
				<td align="left" class="key" style="width:250px;">
					<?php echo JText::_("SHORT_DATETIME"); ?>:
				</td>
				<td style="width:350px;">
					<input name='date_dt_short' id='date_dt_short' size="40" value='<?php echo $this->settings['date_dt_short'] ?>'>
					<div class="fsf_clear"></div>
					<div>Joomla : <b><?php echo JText::_('DATE_FORMAT_LC4') . ', H:i'; ?></b></div>
					<div id="test_date_dt_short"></div>
				</td>
				<td rowspan="4" valign="top">
					<div class='fsf_help'>
					<?php echo JText::_('SETHELP_DATE_FORMATS'); ?>
					</div>
				</td>
			</tr>
			<tr>
				<td align="left" class="key" style="width:250px;">
					<?php echo JText::_("LONG_DATETIME"); ?>:
				</td>
				<td style="width:350px;">
					<input name='date_dt_long' id='date_dt_long' size="40" value='<?php echo $this->settings['date_dt_long'] ?>'>
					<div class="fsf_clear"></div>
					<div>Joomla : <b><?php echo JText::_('DATE_FORMAT_LC3') . ', H:i'; ?></b></div>
					<div id="test_date_dt_long"></div>
				</td>
			</tr>
			<tr>
				<td align="left" class="key" style="width:250px;">
					<?php echo JText::_("SHORT_DATE"); ?>:
				</td>
				<td style="width:350px;">
					<input name='date_d_short' id='date_d_short' size="40" value='<?php echo $this->settings['date_d_short'] ?>'>
					<div class="fsf_clear"></div>
					<div>Joomla : <b><?php echo JText::_('DATE_FORMAT_LC4'); ?></b></div>
					<div id="test_date_d_short"></div>
				</td>
			</tr>
			<tr>
				<td align="left" class="key" style="width:250px;">
					<?php echo JText::_("LONG_DATE"); ?>:
				</td>
				<td style="width:350px;">
					<input name='date_d_long' id='date_d_long' size="40" value='<?php echo $this->settings['date_d_long'] ?>'>
					<div class="fsf_clear"></div>
					<div>Joomla : <b><?php echo JText::_('DATE_FORMAT_LC3'); ?></b></div>
					<div id="test_date_d_long"></div>
				</td>
			</tr>
			<tr>
				<td align="left" class="key" style="width:250px;">
					<?php echo JText::_("TIMEZONE_OFFSET"); ?>:
				</td>
				<td>
					<input name='timezone_offset' size="40" value='<?php echo $this->settings['timezone_offset'] ?>'>
					<div class="fsf_clear"></div>
					<div id="test_timezone_offset"></div>
				</td>
				<td>
					<div class='fsf_help'><?php echo JText::_('SETHELP_timezone_offset'); ?></div>
				</td>
			</tr>
			<tr>
				<td align="left" class="key" style="width:250px;">
					<?php echo JText::_("TEST_DATE_FORMATS"); ?>:
				</td>
				<td style="width:250px;">
					<button id="test_date_formats"><?php echo JText::_('TEST_DATE_FORMATS_BUTTON'); ?></button>
				</td>
				<td valign="top">
					<div class='fsf_help'>
					<?php echo JText::_('SETHELP_DATE_TEST'); ?>
					</div>
				</td>
			</tr>
		</table>
	</fieldset>

<?php endif; ?>

<?php //  ?>
<?php // ##NOT_TEST_END## ?>

</div>

<?php //  ?>
<?php // ##NOT_TEST_END## ?>

<div id="tab_visual" style="display:none;">

	<fieldset class="adminform">
		<legend><?php echo JText::_("VISUAL_SETTINGS"); ?></legend>
		<table class="admintable">
			<tr>
				<td align="left" class="key" style="width:250px;">
					
						<?php echo JText::_("USE_SKIN_STYLING_FOR_PAGEINATION_CONTROLS"); ?>:
					
				</td>
				<td style="width:250px;">
					<input type='checkbox' name='skin_style' value='1' <?php if ($this->settings['skin_style'] == 1) { echo " checked='yes' "; } ?>>
					</td>
				<td>
					<div class='fsf_help'><?php echo JText::_('SETHELP_skin_style'); ?></div>
			</td>
			</tr>
			<tr>
				<td align="left" class="key" style="width:250px;">
					
						<?php echo JText::_("title_prefix"); ?>:
					
				</td>
				<td style="width:250px;">
					<input type='checkbox' name='title_prefix' value='1' <?php if ($this->settings['title_prefix'] == 1) { echo " checked='yes' "; } ?>>
					</td>
				<td>
					<div class='fsf_help'><?php echo JText::_('SETHELP_title_prefix'); ?></div>
			</td>
			</tr>
			<tr>
				<td align="left" class="key" style="width:250px;">
					
					<?php echo JText::_("USE_JOOMLA_SETTING_FOR_PAGE_TITLE_VISIBILITY"); ?>:
					
				</td>
				<td style="width:250px;">
					<input type='checkbox' name='use_joomla_page_title_setting' value='1' <?php if ($this->settings['use_joomla_page_title_setting'] == 1) { echo " checked='yes' "; } ?>>
					</td>
				<td>
					<div class='fsf_help'><?php echo JText::_('SETHELP_use_joomla_page_title_setting'); ?></div>
			</td>
			</tr>
		</table>
	</fieldset>
	<fieldset class="adminform">
		<legend><?php echo JText::_("CSS_SETTINGS"); ?></legend>
		<table class="admintable">
			<tr>
				<td align="left" class="key" style="width:250px;">
					
						<?php echo JText::_("HIGHLIGHT_COLOUR"); ?>:
					
				</td>
				<td style="width:250px;">
					<input name='css_hl' value='<?php echo $this->settings['css_hl'] ?>'>
					&nbsp;
					<input type="button" value="Color picker" onclick="showColorPicker(this,document.forms[0].css_hl)">
				</td>
				<td>
					<div class='fsf_help'><?php echo JText::_('SETHELP_css_hl'); ?></div>
				</td>
			</tr>
			<tr>
				<td align="left" class="key">
					
						<?php echo JText::_("BORDER_COLOUR"); ?>:
					
				</td>
				<td>
					<input name='css_bo' value='<?php echo $this->settings['css_bo'] ?>'>
					&nbsp;
					<input type="button" value="Color picker" onclick="showColorPicker(this,document.forms[0].css_bo)">
					</td>
				<td>
					<div class='fsf_help'><?php echo JText::_('SETHELP_css_bo'); ?></div>
			</td>
			</tr>
			<tr>
				<td align="left" class="key">
					
						<?php echo JText::_("TAB_BACKGROUND_COLOUR"); ?>:
					
				</td>
				<td>
					<input name='css_tb' value='<?php echo $this->settings['css_tb'] ?>'>
					&nbsp;
					<input type="button" value="Color picker" onclick="showColorPicker(this,document.forms[0].css_tb)">
				</td>
				<td>
					<div class='fsf_help'><?php echo JText::_('SETHELP_css_tb'); ?></div>
				</td>
			</tr>
		</table>
	</fieldset>
<?php //  ?>	
</div>
<?php // ##NOT_TEST_START## ?>

<div id="tab_glossary">

	<fieldset class="adminform">
		<legend><?php echo JText::_("GLOSSARY_SETTINGS"); ?></legend>

		<table class="admintable">
			<tr>
				<td align="left" class="key" style="width:250px;">
						<?php echo JText::_("USE_GLOSSARY_ON_FAQS"); ?>:
					
				</td>
				<td style="width:250px;">
					<input type='checkbox' name='glossary_faqs' value='1' <?php if ($this->settings['glossary_faqs'] == 1) { echo " checked='yes' "; } ?>>
				</td>
				<td>
					<div class='fsf_help'><?php echo JText::_('SETHELP_glossary_faqs'); ?></div>
				</td>
			</tr>
<?php //  ?>
			<tr>
				<td align="left" class="key">
						<?php echo JText::_("LINK_ITEMS_TO_GLOSSARY_PAGE"); ?>:
					
				</td>
				<td>
					<input type='checkbox' name='glossary_link' value='1' <?php if ($this->settings['glossary_link'] == 1) { echo " checked='yes' "; } ?>>
				</td>
				<td>
					<div class='fsf_help'><?php echo JText::_('SETHELP_glossary_link'); ?></div>
				</td>
			</tr>
			<tr>
				<td align="left" class="key">
						<?php echo JText::_("SHOW_GLOSSARY_WORD_IN_TOOLTIP"); ?>:
					
				</td>
				<td>
					<input type='checkbox' name='glossary_title' value='1' <?php if ($this->settings['glossary_title'] == 1) { echo " checked='yes' "; } ?>>
				</td>
				<td>
					<div class='fsf_help'><?php echo JText::_('SETHELP_glossary_title'); ?></div>
				</td>
			</tr>
			<tr>
				<td align="left" class="key">
					<?php echo JText::_("glossary_all_letters"); ?>:
				</td>
				<td>
					<input type='checkbox' name='glossary_all_letters' value='1' <?php if ($this->settings['glossary_all_letters'] == 1) { echo " checked='yes' "; } ?>>
				</td>
				<td>
					<div class='fss_help'><?php echo JText::_('SETHELP_glossary_all_letters'); ?></div>
				</td>
			</tr>
			<tr>
				<td align="left" class="key">
					<?php echo JText::_("GLOSSARY_USE_CONTENT_PLUGINS"); ?>:
					
				</td>
				<td>
					<input type='checkbox' name='glossary_use_content_plugins' value='1' <?php if ($this->settings['glossary_use_content_plugins'] == 1) { echo " checked='yes' "; } ?>>
				</td>
				<td>
					<div class='fsf_help'><?php echo JText::_('SETHELP_glossary_use_content_plugins'); ?></div>
				</td>
			</tr>
			<tr>
				<td align="left" class="key">
					<?php echo JText::_("GLOSSARY_IGNORE"); ?>:
					
				</td>
				<td>
					<textarea name='glossary_ignore' id="glossary_ignore" rows="12" cols="40" style="float:none;"><?php echo $this->settings['glossary_ignore']; ?></textarea><br>
				</td>
				<td>
					<div class='fsf_help'><?php echo JText::_('SETHELP_glossary_ignore'); ?></div>
				</td>
			</tr>
			<tr>
				<td align="left" class="key">
					<?php echo JText::_("GLOSSARY_EXCLUDE"); ?>:
					
				</td>
				<td>
					<input type='text' name='glossary_exclude' size="60" value='<?php echo $this->settings['glossary_exclude']; ?>'>
				</td>
				<td>
					<div class='fsf_help'><?php echo JText::_('SETHELP_GLOSSARY_EXCLUDE'); ?></div>
				</td>
			</tr>
		</table>
	</fieldset>
</div>

<div id="tab_faq">

	<fieldset class="adminform">
		<legend><?php echo JText::_("FAQ_SETTINGS"); ?></legend>

		<table class="admintable">
			<tr>
				<td align="left" class="key" style="width:250px;">
						<?php echo JText::_("FAQS_POPUP_WIDTH"); ?>:
					
				</td>
				<td style="width:250px;">
					<input name='faq_popup_width' value='<?php echo $this->settings['faq_popup_width'] ?>'>
				</td>
				<td>
					<div class='fsf_help'><?php echo JText::_('SETHELP_faq_popup_width'); ?></div>
				</td>
			</tr>
			<tr>
				<td align="left" class="key" style="width:250px;">
						<?php echo JText::_("FAQS_POPUP_HEIGHT"); ?>:
					
				</td>
				<td>
					<input name='faq_popup_height' value='<?php echo $this->settings['faq_popup_height'] ?>'>
				</td>
				<td>
					<div class='fsf_help'><?php echo JText::_('SETHELP_faq_popup_height'); ?></div>
				</td>
			</tr>
			<tr>
				<td align="left" class="key" style="width:250px;">
						<?php echo JText::_("FAQS_POPUP_INNER_WIDTH"); ?>:
					
				</td>
				<td>
					<input name='faq_popup_inner_width' value='<?php echo $this->settings['faq_popup_inner_width'] ?>'>
				</td>
				<td>
					<div class='fsf_help'><?php echo JText::_('SETHELP_faq_popup_inner_width'); ?></div>
				</td>
			</tr>
			<tr>
				<td align="left" class="key">
					<?php echo JText::_("FAQ_USE_CONTENT_PLUGINS"); ?>:
					
				</td>
				<td>
					<input type='checkbox' name='faq_use_content_plugins' value='1' <?php if ($this->settings['faq_use_content_plugins'] == 1) { echo " checked='yes' "; } ?>>
				</td>
				<td>
					<div class='fsf_help'><?php echo JText::_('SETHELP_faq_use_content_plugins'); ?></div>
				</td>
			</tr>
			<tr>
				<td align="left" class="key">
					<?php echo JText::_("FAQ_USE_CONTENT_PLUGINS_LIST"); ?>:
					
				</td>
				<td>
					<input type='checkbox' name='faq_use_content_plugins_list' value='1' <?php if ($this->settings['faq_use_content_plugins_list'] == 1) { echo " checked='yes' "; } ?>>
				</td>
				<td>
					<div class='fsf_help'><?php echo JText::_('SETHELP_faq_use_content_plugins_list'); ?><div>
				</td>
			</tr>
			<tr>
				<td align="left" class="key" style="width:250px;">
					<?php echo JText::_("FAQS_PER_PAGE"); ?>:
				</td>
				<td>
					<?php $this->PerPage('faq_per_page'); ?>
				</td>
				<td>
					<div class='fsf_help'><?php echo JText::_('SETHELP_faq_per_page'); ?></div>
				</td>
			</tr>
			<tr>
				<td align="left" class="key">
					<?php echo JText::_("faq_cat_prefix"); ?>:
					
				</td>
				<td>
					<input type='checkbox' name='faq_cat_prefix' value='1' <?php if ($this->settings['faq_cat_prefix'] == 1) { echo " checked='yes' "; } ?>>
				</td>
				<td>
					<div class='fsf_help'><?php echo JText::_('SETHELP_faq_cat_prefix'); ?><div>
				</td>
			</tr>
		</table>
	</fieldset>
</div>
<?php // ##NOT_TEST_END## ?>

</form>

<script>

function testreference()
{
	$('testref').innerHTML = "<?php echo JText::_('PLEASE_WAIT'); ?>";
	var format = $('support_reference').value
	var url = '<?php echo FSFRoute::x("index.php?option=com_fsf&view=settings&what=testref",false); ?>&ref=' + format;
	
<?php if (FSF_Helper::Is16()): ?>
	$('testref').load(url);
<?php else: ?>
	new Ajax(url, {
	method: 'get',
	update: $('testref')
	}).request();
<?php endif; ?>
}

window.addEvent('domready', function(){

	if (location.hash)
	{
		ShowTab(location.hash.replace('#',''));
	}
	else
	{
		ShowTab('general');
	}
	
<?php if (FSF_Helper::Is16()): ?>
	jQuery('#test_date_formats').click(function (ev) {
		ev.preventDefault();
			
		var url = '<?php echo FSFRoute::x("index.php?option=com_fsf&view=settings&what=testdates",false); ?>';

		url += '&date_dt_short=' + encodeURIComponent(jQuery('#date_dt_short').val());
		url += '&date_dt_long=' + encodeURIComponent(jQuery('#date_dt_long').val());
		url += '&date_d_short=' + encodeURIComponent(jQuery('#date_d_short').val());
		url += '&date_d_long=' + encodeURIComponent(jQuery('#date_d_long').val());

		jQuery.get(url, function (data) {
			var result = jQuery.parseJSON(data);
			jQuery('#test_date_dt_short').html("<?php echo JText::_('DATE_TEST_RESULT'); ?>" + ": " + result.date_dt_short);
			jQuery('#test_date_dt_long').html("<?php echo JText::_('DATE_TEST_RESULT'); ?>" + ": " + result.date_dt_long);
			jQuery('#test_date_d_short').html("<?php echo JText::_('DATE_TEST_RESULT'); ?>" + ": " + result.date_d_short);
			jQuery('#test_date_d_long').html("<?php echo JText::_('DATE_TEST_RESULT'); ?>" + ": " + result.date_d_long);
			jQuery('#test_timezone_offset').html("<?php echo JText::_('DATE_TEST_RESULT'); ?>" + ": " + result.timezone_offset);
		});
	});
<?php endif; ?>
});
 
</script>
