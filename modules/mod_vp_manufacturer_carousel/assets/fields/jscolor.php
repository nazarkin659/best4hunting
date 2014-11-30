<?php

/*
 *  pgn4web javascript chessboard
 *  copyright (C) 2009, 2010 Paolo Casaschi
 *  see README file and http://pgn4web.casaschi.net
 *  for credits, license and more details
 */

defined('JPATH_BASE') or die;

jimport('joomla.form.formfield');
class JFormFieldJSColor extends JFormField {
	protected $type = 'JSColor';
	protected function getInput() {
		global $JElementJSColorJSWritten;
		if (!$JElementJSColorJSWritten) {
      $jsFile = dirname(__FILE__) . DS . "jscolor" . DS . "jscolor.js";
      $jsUrl = str_replace(JPATH_ROOT, JURI::root(true), $jsFile);
      $jsUrl = str_replace(DS, "/", $jsUrl);
			$document	= JFactory::getDocument();
			$document->addScript( $jsUrl );
			$JElementJSColorJSWritten = TRUE;
		}
		$onchange	= $this->element['onchange'] ? ' onchange="'.(string) $this->element['onchange'].'"' : '';
		$class		= ' class="color {required:false}"';
		return '<input type="text" name="'.$this->name.'" id="'.$this->id.'"' . ' value="'.htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8').'"' . $class.$onchange.'/>';
	}
}
