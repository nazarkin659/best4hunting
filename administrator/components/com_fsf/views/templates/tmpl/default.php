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
	ResetElement('visual');
// 

	location.hash = tabid;
	jQuery('#tab').val(tabid);
	document.getElementById('tab_' + tabid).style.display = 'inline';
	document.getElementById('link_' + tabid).style.backgroundColor = '#f0f0ff';
	
	document.getElementById('link_' + tabid).onmouseover = function() {
	}
	document.getElementById('link_' + tabid).onmouseout = function() {
	}
	
	if (tabid == "visual")
	{
		cminit('display_style', 'css');
		cminit('display_popup_style', 'css');
		cminit('display_head', 'htmlmixed');
		cminit('display_foot', 'htmlmixed');
		
		cminit('display_h1', 'htmlmixed');
		cminit('display_h2', 'htmlmixed');
		cminit('display_h3', 'htmlmixed');
		cminit('display_popup', 'htmlmixed');

	}
}

function cminit(element_name, editmode)
{
	if (typeof(CodeMirror.editors) == "undefined")
		CodeMirror.editors = new Array();
	
	if (CodeMirror.editors[element_name])
		return;
		
	element = jQuery('#' + element_name);
	
	CodeMirror.editors[element_name] = CodeMirror.fromTextArea(element[0], { 
		mode: editmode, 
		lineNumbers: true,
		viewportMargin: Infinity,
		lineWrapping: true,
		tabSize: 2
	});
}

window.addEvent('domready', function(){
	if (location.hash)
	{
		ShowTab(location.hash.replace('#',''));
	}
	else
	{
		ShowTab('visual');
// 
	}

// 
});

function setup_comments(cset)
{
	jQuery('#' + cset + '_reset').click(function (ev) { 
		ev.stopPropagation();
		ev.preventDefault();
		if (confirm("Are you sure you wish to reset this custom template"))
		{
			jQuery('#' + cset).val(jQuery('#' + cset + '_default').val());
			CodeMirror.editors[cset].setValue(jQuery('#' + cset + '_default').val());
		}
	});
	
	jQuery('#' + cset + '_use_custom').change(function (ev) {
		showhide_comments(cset);
	});
	showhide_comments(cset);
}

function showhide_comments(cset)
{
	var value = jQuery('#' + cset + '_use_custom').attr('checked');
	if (value == "checked")
	{
		jQuery('#' + cset + '_row').css('display','table-row');
		
		if (jQuery('#' + cset).val() == "")
			jQuery('#' + cset).val(jQuery('#' + cset + '_default').val());

		cminit(cset, 'htmlmixed');
	} else {
		jQuery('#' + cset + '_row').css('display','none');
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
}</style>
<form action="index.php" method="post" name="adminForm" id="adminForm">
<input type="hidden" name="what" value="save">
<input type="hidden" name="option" value="com_fsf" />
<input type="hidden" name="task" value="save" />
<input type="hidden" name="view" value="templates" />
<input type="hidden" name="tab" id='tab' value="<?php echo $this->tab; ?>" />

<div class='ffs_tabs'>

<!--<a id='link_general' class='ffs_tab' href='#' onclick="ShowTab('general');return false;">General</a>-->

<?php //  ?>

<a id='link_visual' class='ffs_tab' href='#' onclick="ShowTab('visual');return false;"><?php echo JText::_("VISUAL"); ?></a>

</div>

<?php //  ?>
<?php // ##NOT_TEST_END## ?>

<div id="tab_visual" style="display:none;">


	<fieldset class="adminform">
		<legend><?php echo JText::_("CSS_SETTINGS"); ?></legend>
		<table class="admintable">
			<tr>
				<td align="right" class="key" style="width:250px;">	
						<?php echo JText::_("MAIN_CSS"); ?>:
				</td>
				<td style="width:450px;">
					<textarea name="display_style" id="display_style" rows="10" cols="60"><?php echo $this->settings['display_style'] ?></textarea>
				</td>
				<td>
					<div class="fsf_help"><?php echo JText::_('TMPLHELP_display_style'); ?>
					</div>
				</td>
			</tr>
			<tr>
				<td align="right" class="key">	
						<?php echo JText::_("POPUP_CSS"); ?>:
				</td>
				<td style="width:450px;">
					<textarea name="display_popup_style" id="display_popup_style" rows="10" cols="60"><?php echo $this->settings['display_popup_style'] ?></textarea>
				</td>
				<td>
					<div class="fsf_help"><?php echo JText::_('TMPLHELP_display_popup_style'); ?>
					
					</div>
				</td>
			</tr>
			<tr>
				<td align="right" class="key">	
						<?php echo JText::_("PAGE_HEADER"); ?>:
				</td>
				<td style="width:450px;">
					<textarea name="display_head" id="display_head" rows="10" cols="60"><?php echo $this->settings['display_head'] ?></textarea>
				</td>
				<td>
					<div class="fsf_help"><?php echo JText::_('TMPLHELP_display_head'); ?>
						
					</div>
				</td>
			</tr>
			<tr>
				<td align="right" class="key">	
						<?php echo JText::_("PAGE_FOOTER"); ?>:
				</td>
				<td style="width:450px;">
					<textarea name="display_foot" id="display_foot" rows="10" cols="60"><?php echo $this->settings['display_foot'] ?></textarea>
				</td>
				<td>
					<div class="fsf_help"><?php echo JText::_('TMPLHELP_display_foot'); ?></div>
				</td>
			</tr>
		</table>
	</fieldset>

	<fieldset class="adminform">
		<legend><?php echo JText::_("PAGE_TITLE_SETTINGS"); ?></legend>
		<table class="admintable">
			<tr>
				<td align="right" class="key" style="width:250px;">
					
						<?php echo JText::_("H1_STYLE"); ?>:
					
				</td>
				<td style="width:450px;">
					<textarea name="display_h1" id="display_h1" rows="5" cols="60"><?php echo $this->settings['display_h1'] ?></textarea>
				</td>
				<td>
					<div class="fsf_help"><?php echo JText::_('TMPLHELP_display_h1'); ?></div>
				</td>
			</tr>
			<tr>
				<td align="right" class="key">
					
						<?php echo JText::_("H2_STYLE"); ?>:
					
				</td>
				<td style="width:450px;">
					<textarea name="display_h2" id="display_h2" rows="5" cols="60"><?php echo $this->settings['display_h2'] ?></textarea>
				</td>
				<td>
					<div class="fsf_help"><?php echo JText::_('TMPLHELP_display_h2'); ?></div>
				</td>
			</tr>
			<tr>
				<td align="right" class="key">
					
						<?php echo JText::_("H3_STYLE"); ?>:
					
				</td>
				<td style="width:450px;">
					<textarea name="display_h3" id="display_h3" rows="5" cols="60"><?php echo $this->settings['display_h3'] ?></textarea>
				</td>
					<td>
					<div class="fsf_help"><?php echo JText::_('TMPLHELP_display_h3'); ?></div>
				</td>
			</tr>
			<tr>
				<td align="right" class="key">	
						<?php echo JText::_("POPUP_STYLE"); ?>:	
				</td>
				<td style="width:450px;">
					<textarea name="display_popup" id="display_popup" rows="5" cols="60"><?php echo $this->settings['display_popup'] ?></textarea>
				</td>
				<td>
					<div class="fsf_help"><?php echo JText::_('TMPLHELP_display_popup'); ?></div>
				</td>
			</tr>
		</table>
	</fieldset>
</div>

</form>

<form action="<?php echo JURI::root(); ?>/index.php?view=admin&layout=support&option=com_fsf&preview=1" method="post" name="adminForm2" id="adminForm2" target="_blank">
<input type="hidden" name="list_template" id="list_template" value="" />
<textarea style='display:none;' name="list_head" id="list_head"></textarea>
<textarea style='display:none;' name="list_row" id="list_row"></textarea>
</form>

<script>

// 

</script>
