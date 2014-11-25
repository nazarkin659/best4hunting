<?php
/**
 * @package NST_Order2Mail
 * @version 1.3
 *  @author NST nasieti.com
 * @copyright Copyright (c)2013 Nasieti.com
 * @license GNU General Public License version 3, or later
 **/
defined('JPATH_PLATFORM') or die;

jimport('joomla.html.html');
jimport('joomla.form.formfield');

class JFormFieldVirtuemart extends JFormField {

    protected $type = 'virtuemart';

    protected function getInput() {
	   if(!defined('VMLANG')){
		  if (!class_exists( 'VmConfig' )) require(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_virtuemart'.DS.'helpers'.DS.'config.php');
		  VmConfig::loadConfig();
	   }
        $lng=VMLANG;


        $html = array();
        $attr = '';
        $icon = $this->element['icon'];
        $suffix = $this->element['suffixck'];

        $attr .= $this->element['class'] ? ' class="' . (string) $this->element['class'] . '"' : '';
        if ((string) $this->element['readonly'] == 'true' || (string) $this->element['disabled'] == 'true') {
            $attr .= ' disabled="disabled"';
        }

        $attr .= $this->element['size'] ? ' size="' . (int) $this->element['size'] . '"' : '';
        $attr .= $this->element['multiple'] ? ' multiple="multiple"' : '';
        $attr .= 'style="width:105px;border-radius:3px;-moz-border-radius:3px;padding:1px;"';
        $attr .= $this->element['onchange'] ? ' onchange="' . (string) $this->element['onchange'] . '"' : '';


                $db = & JFactory::getDBO();
                $db->setQuery("select * from #__virtuemart_".$this->element['table']."_".$lng." ".$this->element['order']);

                $key = ($this->element['key_field'] ? $this->element['key_field'] : 'value');
                $val = ($this->element['value_field'] ? $this->element['value_field'] : $name);

                $options = array ();
                $rows = $db->loadAssocList();

                foreach ($rows as $row){
                        $options[]=array("$key"=>$row["$key"],"$val"=>$row["$val"]);
                }

                if($options){
                        return JHTML::_('select.genericlist',$options, $this->name, $attr, "$key", "$val", $this->value, $this->id);
                }
    }

    protected function getPathToImages() {
        $localpath = dirname(__FILE__);
        $rootpath = JPATH_ROOT;
        $httppath = trim(JURI::root(), "/");
        $pathtoimages = str_replace("\\", "/", str_replace($rootpath, $httppath, $localpath));
        return $pathtoimages;
    }

    protected function getOptions() {
        $options = array();

        foreach ($this->element->children() as $option) {
            if ($option->getName() != 'option') {
                continue;
            }
            $tmp = JHtml::_('select.option', (string) $option['value'], JText::alt(trim((string) $option), preg_replace('/[^a-zA-Z0-9_\-]/', '_', $this->fieldname)), 'value', 'text', ((string) $option['disabled'] == 'true'));
            $tmp->class = (string) $option['class'];
            $tmp->onclick = (string) $option['onclick'];

            $options[] = $tmp;
        }

        reset($options);

        return $options;
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
