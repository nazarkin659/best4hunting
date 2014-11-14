<?php
/**
 * @package NST_Order2Mail
 * @version 1.3
 *  @author NST nasieti.com
 * @copyright Copyright (c)2013 Nasieti.com
 * @license GNU General Public License version 3, or later
 **/

defined('_JEXEC') or 	die( 'Direct Access to ' . basename( __FILE__ ) . ' is not allowed.' ) ;
defined('JPATH_PLATFORM') or die;

jimport('joomla.html.html');
jimport('joomla.form.formfield');

class JFormFieldIntervalfields extends JFormField {

    protected $type = 'intervalfields';

    protected function getInput() {
        $attr = '';
        $icon = $this->element['icon'];
        $suffix = $this->element['suffixck'];

        $attr .= $this->element['class'] ? ' class="' . (string) $this->element['class'] . '"' : '';

        if ((string) $this->element['readonly'] == 'true' || (string) $this->element['disabled'] == 'true') {
            $attr .= ' disabled="disabled"';
        }
        $attr .= 'style="width:105px;border-radius:3px;-moz-border-radius:3px;padding:1px;"';
        $attr .= $this->element['onchange'] ? ' onchange="' . (string) $this->element['onchange'] . '"' : '';



    return "<span style='float: left;margin: 6px 6px 0 0'>start</span><input type='text' id='".$this->id."_start' style='width: 30px' value='".$this->value."' $attr />
    <span style='float: left;margin: 6px 6px 0 0'>stop</span><input type='text' id='".$this->id."_stop' size='2' style='width: 30px' value='".$this->element['default1']."' $attr />";


    }

    protected function getLabel() {
        $label = '';
        $text = $this->element['label'] ? (string) $this->element['label'] : (string) $this->element['name'];
        $text = JText::_($text);


        $class = !empty($this->description) ? 'hasTip' : '';
        $label .= '<label id="' . $this->id . '-lbl" for="' . $this->id . '" class="' . $class . '"';

        if (!empty($this->description)) {
            $label .= ' title="' . htmlspecialchars(trim($text, ':') . '::' .
                            JText::_($this->description), ENT_COMPAT, 'UTF-8') . '"';
        }

        $label .= ' style="min-width:150px;max-width:150px;width:150px;display:block;float:left;padding:1px;">' . $text . '</label>';

        return $label;
    }

}
